<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForecastingController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('active_branch_id') ?? auth()->user()->branch_id;
        $isGlobal = (auth()->user()->role === 'admin' && !session('active_branch_id'));

        // 1. Forecasting for Tomorrow
        $nextDayForecast = $this->calculateForecast(Carbon::now()->subDays(7), Carbon::now(), 'day', $isGlobal);

        // 2. Forecasting for Next Month
        $startOfPreviousMonth = Carbon::now()->subMonth()->startOfMonth();
        $nextMonthForecast = $this->calculateForecast($startOfPreviousMonth, Carbon::now(), 'month', $isGlobal);

        $trendsQuery = Order::where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays(14));

        if (!$isGlobal) {
            $trendsQuery->where('branch_id', $branchId);
        }

        $dailyTrends = $trendsQuery->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.forecasting.index', compact('nextDayForecast', 'nextMonthForecast', 'dailyTrends'));
    }

    private function calculateForecast($startDate, $endDate, $type = 'day', $isGlobal = false)
    {
        $branchId = session('active_branch_id') ?? auth()->user()->branch_id;
        $days = $startDate->diffInDays($endDate) ?: 1;

        // Get average sales per menu item from POS orders
        $posQuery = OrderItem::whereHas('order', function ($query) use ($branchId, $startDate, $endDate, $isGlobal) {
            $query->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate]);

            if (!$isGlobal) {
                $query->where('branch_id', $branchId);
            }
        });

        $menuItemSales = $posQuery->select('menu_item_id', DB::raw("SUM(quantity) as total_qty"))
            ->groupBy('menu_item_id')
            ->get()
            ->keyBy('menu_item_id');

        // Get sales from ERP orders (using menu_item_id mapping)
        // We use Supabase since it has both pos_ and crm_ tables
        $erpOrderItems = DB::connection('supabase')
            ->table('crm_order_items as oi')
            ->join('crm_orders as o', 'oi.order_id', '=', 'o.id')
            ->where('o.status', 'completed')
            ->whereBetween('o.created_at', [$startDate, $endDate])
            ->whereNotNull('oi.menu_item_id')
            ->select('oi.menu_item_id', DB::raw('SUM(oi.quantity) as total_qty'))
            ->groupBy('oi.menu_item_id')
            ->get();

        // Merge ERP sales with POS sales
        foreach ($erpOrderItems as $erpItem) {
            if (isset($menuItemSales[$erpItem->menu_item_id])) {
                $menuItemSales[$erpItem->menu_item_id]->total_qty += $erpItem->total_qty;
            } else {
                $menuItemSales[$erpItem->menu_item_id] = $erpItem;
            }
        }

        // Calculate average daily quantity
        foreach ($menuItemSales as $item) {
            $item->avg_daily_qty = $item->total_qty / $days;
        }

        // Load menu items with their inventory relationships
        $menuItems = MenuItem::with('inventoryItems')->whereIn('id', $menuItemSales->keys())->get()->keyBy('id');

        $forecast = [
            'items' => [],
            'ingredients' => [],
            'total_predicted_sales' => 0,
            'total_predicted_loss' => 0,
        ];

        $multiplier = ($type === 'month') ? 30 : 1;
        $safetyBuffer = 1.15; // 15% buffer

        foreach ($menuItemSales as $menuItemId => $sale) {
            if (!isset($menuItems[$menuItemId]))
                continue;

            $menuItem = $menuItems[$menuItemId];
            $predictedQty = round($sale->avg_daily_qty * $multiplier * $safetyBuffer, 2);
            $forecast['items'][] = [
                'name' => $menuItem->name,
                'predicted_qty' => $predictedQty,
                'price' => $menuItem->price,
                'subtotal' => $predictedQty * $menuItem->price
            ];
            $forecast['total_predicted_sales'] += ($predictedQty * $menuItem->price);

            // Calculate ingredient needs
            foreach ($menuItem->inventoryItems as $ingredient) {
                $neededPerUnit = $ingredient->pivot->quantity_needed;

                // Calculate spoilage rate for this ingredient
                $spoilageRate = $ingredient->stock_in > 0
                    ? ($ingredient->spoilage / $ingredient->stock_in)
                    : 0;

                // Adjust total needed by spoilage rate
                $totalNeeded = $predictedQty * $neededPerUnit * (1 + $spoilageRate);

                if (!isset($forecast['ingredients'][$ingredient->id])) {
                    $forecast['ingredients'][$ingredient->id] = [
                        'id' => $ingredient->id,
                        'name' => $ingredient->name,
                        'unit' => $ingredient->unit,
                        'current_stock' => $ingredient->quantity,
                        'total_needed' => 0,
                        'to_buy' => 0,
                        'spoilage_rate' => round($spoilageRate * 100, 1) . '%',
                    ];
                }
                $forecast['ingredients'][$ingredient->id]['total_needed'] += $totalNeeded;
            }
        }

        // Finalize ingredient to_buy amounts
        foreach ($forecast['ingredients'] as &$ing) {
            $ing['total_needed'] = round($ing['total_needed'], 2);
            $ing['to_buy'] = max(0, round($ing['total_needed'] - $ing['current_stock'], 2));
        }

        return $forecast;
    }
}
