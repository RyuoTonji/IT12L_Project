<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\ShiftReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function exportInventory()
    {
        $inventoryItems = Inventory::all();

        // Get metadata
        $exporter = auth()->user();
        $branch = $exporter->branch;
        $exportDate = now()->format('F d, Y');
        $exportTime = now()->format('h:i A');

        $pdf = Pdf::loadView('exports.inventory_pdf', compact(
            'inventoryItems',
            'exporter',
            'branch',
            'exportDate',
            'exportTime'
        ));
        return $pdf->download('inventory_report.pdf');
    }

    public function exportSales()
    {
        $orders = Order::with('user')->latest()->get();

        // Get metadata
        $exporter = auth()->user();
        $branch = $exporter->branch;
        $exportDate = now()->format('F d, Y');
        $exportTime = now()->format('h:i A');

        $pdf = Pdf::loadView('exports.sales_pdf', compact(
            'orders',
            'exporter',
            'branch',
            'exportDate',
            'exportTime'
        ));
        return $pdf->download('sales_report.pdf');
    }

    public function exportShiftReport(ShiftReport $report)
    {
        // Get metadata
        $exporter = auth()->user();
        $branch = $exporter->branch;
        $exportDate = now()->format('F d, Y');
        $exportTime = now()->format('h:i A');

        $pdf = Pdf::loadView('exports.report_pdf', compact(
            'report',
            'exporter',
            'branch',
            'exportDate',
            'exportTime'
        ));
        return $pdf->download('shift_report_' . $report->shift_date->format('Y-m-d') . '.pdf');
    }

    public function exportItems(Request $request)
    {
        $startDate = $request->input('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', \Carbon\Carbon::now()->format('Y-m-d'));

        $startDateCarbon = \Carbon\Carbon::parse($startDate)->startOfDay();
        $endDateCarbon = \Carbon\Carbon::parse($endDate)->endOfDay();

        // Get top selling items
        $topItems = \App\Models\MenuItem::select(
            'menu_items.id',
            'menu_items.name',
            'categories.name as category_name',
            \DB::raw('SUM(order_items.quantity) as total_quantity'),
            \DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
        )
            ->join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('categories', 'menu_items.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDateCarbon, $endDateCarbon])
            ->where('orders.payment_status', 'paid')
            ->groupBy('menu_items.id', 'menu_items.name', 'categories.name')
            ->orderByDesc('total_quantity')
            ->limit(20)
            ->get();

        // Get category breakdown
        $categories = \DB::table('categories')
            ->select(
                'categories.name',
                \DB::raw('SUM(order_items.quantity) as total_quantity'),
                \DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales')
            )
            ->join('menu_items', 'categories.id', '=', 'menu_items.category_id')
            ->join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDateCarbon, $endDateCarbon])
            ->where('orders.payment_status', 'paid')
            ->groupBy('categories.name')
            ->orderByDesc('total_sales')
            ->get();

        // Get metadata
        $exporter = auth()->user();
        $branch = $exporter->branch;
        $exportDate = now()->format('F d, Y');
        $exportTime = now()->format('h:i A');

        $pdf = Pdf::loadView('exports.items_pdf', compact(
            'topItems',
            'categories',
            'startDate',
            'endDate',
            'exporter',
            'branch',
            'exportDate',
            'exportTime'
        ));
        return $pdf->download('items_report_' . $startDate . '_to_' . $endDate . '.pdf');
    }

    public function exportStaff(Request $request)
    {
        $startDate = $request->input('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', \Carbon\Carbon::now()->format('Y-m-d'));

        $startDateCarbon = \Carbon\Carbon::parse($startDate)->startOfDay();
        $endDateCarbon = \Carbon\Carbon::parse($endDate)->endOfDay();

        // Get staff performance
        $staffPerformance = \App\Models\User::select(
            'users.id',
            'users.name',
            \DB::raw('COUNT(orders.id) as total_orders'),
            \DB::raw('SUM(orders.total_amount) as total_sales')
        )
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$startDateCarbon, $endDateCarbon])
            ->where('orders.payment_status', 'paid')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_sales')
            ->get();

        // Get metadata
        $exporter = auth()->user();
        $branch = $exporter->branch;
        $exportDate = now()->format('F d, Y');
        $exportTime = now()->format('h:i A');

        $pdf = Pdf::loadView('exports.staff_pdf', compact(
            'staffPerformance',
            'startDate',
            'endDate',
            'exporter',
            'branch',
            'exportDate',
            'exportTime'
        ));
        return $pdf->download('staff_report_' . $startDate . '_to_' . $endDate . '.pdf');
    }

    public function exportDaily(Request $request)
    {
        $date = $request->input('date', \Carbon\Carbon::today()->format('Y-m-d'));
        $carbonDate = \Carbon\Carbon::parse($date);

        // 1. Shift Reports for the day
        $shiftReports = \App\Models\ShiftReport::with('user')
            ->whereDate('shift_date', $carbonDate)
            ->get();

        // 2. Aggregated Sales
        $totalSales = Order::whereDate('created_at', $carbonDate)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // 3. Aggregated Refunds
        $totalRefunds = \App\Models\Payment::whereDate('created_at', $carbonDate)
            ->where('amount', '<', 0)
            ->sum('amount');

        // 4. Inventory Activities
        $inventoryActivities = \App\Models\Activity::with('user')
            ->whereDate('created_at', $carbonDate)
            ->where('related_model', \App\Models\Inventory::class)
            ->get();

        // 5. Void Requests
        $voidRequests = \App\Models\VoidRequest::with(['order', 'requester', 'approver'])
            ->whereDate('created_at', $carbonDate)
            ->get();

        // Get metadata
        $exporter = auth()->user();
        $branch = $exporter->branch;
        $exportDate = now()->format('F d, Y');
        $exportTime = now()->format('h:i A');

        $pdf = Pdf::loadView('exports.daily_pdf', compact(
            'date',
            'shiftReports',
            'totalSales',
            'totalRefunds',
            'inventoryActivities',
            'voidRequests',
            'exporter',
            'branch',
            'exportDate',
            'exportTime'
        ));
        return $pdf->download('daily_report_' . $date . '.pdf');
    }
}
