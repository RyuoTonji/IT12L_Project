<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Activity;
use App\Models\InventoryAdjustment;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::with(['menuItems.category'])->orderBy('name')->get();

        // Group inventory items by menu item categories
        $inventoriesByCategory = collect();

        foreach ($inventories as $inventory) {
            // Get all menu items linked to this inventory item
            $menuItems = $inventory->menuItems;

            if ($menuItems->isNotEmpty()) {
                // Get unique categories from linked menu items
                foreach ($menuItems as $menuItem) {
                    $categoryName = $menuItem->category ? $menuItem->category->name : 'Uncategorized';

                    if (!$inventoriesByCategory->has($categoryName)) {
                        $inventoriesByCategory->put($categoryName, collect());
                    }

                    // Add inventory to this category if not already there
                    if (!$inventoriesByCategory[$categoryName]->contains('id', $inventory->id)) {
                        $inventoriesByCategory[$categoryName]->push($inventory);
                    }
                }
            } else {
                // No menu items linked - put in "Uncategorized"
                if (!$inventoriesByCategory->has('Uncategorized')) {
                    $inventoriesByCategory->put('Uncategorized', collect());
                }
                $inventoriesByCategory['Uncategorized']->push($inventory);
            }
        }

        return view('inventory.dashboard', compact('inventories', 'inventoriesByCategory'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        $branches = \App\Models\Branch::all();
        return view('inventory.create', compact('categories', 'branches'));
    }

    public function store(Request $request)
    {
        // Wrapper for addStock logic to match Resource Controller
        return $this->addStock($request);
    }

    public function show(Inventory $inventory)
    {
       // Load necessary relationships for the show view
       $inventory->load(['menuItems', 'adjustments.recorder', 'adjustments' => function($q) {
           $q->latest()->limit(50);
       }]);
       
       return view('inventory.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        return view('inventory.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        // Wrapper for updateStock logic to match Resource Controller
        // Note: The Admin update form might send more data than just quantity, so we may need a specific update logic here 
        // if we want to allow editing name/unit/etc. For now, let's assume it primarily handles stock updates or simple edits.
        
        // Check if this is a full update or just stock
        if ($request->has('name') || $request->has('unit')) {
             $request->validate([
                'name' => 'required|string|max:255',
                'unit' => 'required|string|max:50',
                'quantity' => 'nullable|numeric|min:0', // Optional if just editing details
            ]);

            $inventory->update([
                'name' => $request->name,
                'unit' => $request->unit,
                'category' => $request->category ?? $inventory->category,
            ]);

            // If quantity is also provided (e.g. correction), handle that
             if ($request->filled('quantity') && $request->quantity != $inventory->quantity) {
                 // Calculate difference for adjustment
                 $diff = $request->quantity - $inventory->quantity;
                 if ($diff != 0) {
                     return $this->updateStock(new Request(['quantity' => $diff]), $inventory);
                 }
             }

             return redirect()->route('inventory.dashboard')->with('success', 'Inventory item details updated successfully.');
        }

        return $this->updateStock($request, $inventory);
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        DB::transaction(function () use ($request) {
            $inventory = Inventory::create([
                'name' => $request->name,
                'quantity' => $request->quantity,
                'unit' => $request->unit,
                'stock_in' => $request->quantity, // Initialize stock_in
                'stock_out' => 0,
                'category' => $request->category ?? null,
                'branch_id' => Auth::user()->branch_id ?? null,
            ]);

            // Create Adjustment Record with before/after tracking
            InventoryAdjustment::create([
                'inventory_id' => $inventory->id,
                'adjustment_type' => 'stock_in',
                'quantity' => $request->quantity,
                'quantity_before' => 0, // New inventory item starts at 0
                'quantity_after' => $request->quantity,
                'reason' => 'Initial Stock',
                'recorded_by' => Auth::id(),
            ]);

            // Automated Activity Logging
            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'add_stock',
                'details' => "Added stock: {$inventory->name} ({$inventory->quantity} {$inventory->unit})",
                'status' => 'info',
                'related_id' => $inventory->id,
                'related_model' => Inventory::class,
            ]);
        });

        return redirect()->route('inventory.dashboard')->with('success', 'Stock added successfully.');
    }

    public function updateStock(Request $request, Inventory $inventory)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $inventory) {
            $quantityBefore = $inventory->quantity;

            $inventory->increment('quantity', $request->quantity);
            $inventory->increment('stock_in', $request->quantity); // Track stock in

            $quantityAfter = $inventory->fresh()->quantity;

            // Create Adjustment Record with before/after tracking
            InventoryAdjustment::create([
                'inventory_id' => $inventory->id,
                'adjustment_type' => 'stock_in',
                'quantity' => $request->quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'reason' => 'Stock Update',
                'recorded_by' => Auth::id(),
            ]);

            // Automated Activity Logging
            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'update_stock',
                'details' => "Updated stock for {$inventory->name}: Added {$request->quantity} {$inventory->unit}. New Total: {$quantityAfter}",
                'status' => 'info',
                'related_id' => $inventory->id,
                'related_model' => Inventory::class,
            ]);
        });

        return redirect()->route('inventory.dashboard')->with('success', 'Stock updated successfully.');
    }

    public function destroy(Inventory $inventory)
    {
        $name = $inventory->name;
        $inventory->delete(); // Soft delete

        // Automated Activity Logging
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'archive_stock',
            'details' => "Archived stock: {$name}",
            'status' => 'info',
            'related_id' => $inventory->id,
            'related_model' => Inventory::class,
        ]);

        return redirect()->route('inventory.dashboard')->with('success', 'Stock archived successfully.');
    }

    public function report(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $branch = $request->input('branch', 'all'); // Default to 'all'

        $stats = $this->getInventoryStats($date, $branch);

        $stockIns = $stats['stockIns'];
        $preparedDishes = $stats['preparedDishes'];
        $inventoryItems = $stats['inventoryItems'];
        $reportData = $stats['reportData'];

        return view('inventory.report', compact('stockIns', 'preparedDishes', 'date', 'inventoryItems', 'reportData', 'branch'));
    }

    private function getInventoryStats($date, $branch = 'all')
    {
        $dateCarbon = \Carbon\Carbon::parse($date);
        $endOfDay = $dateCarbon->copy()->endOfDay();
        $startOfDay = $dateCarbon->copy()->startOfDay();

        // 1. Get all Inventory Items with optional branch filtering
        $inventoryQuery = Inventory::with(['menuItems']);
        
        if ($branch !== 'all') {
            $inventoryQuery->where('branch_id', $branch);
        }
        
        $inventoryItems = $inventoryQuery->orderBy('name')->get();

        // 2. Fetch Future Adjustments (After the selected date)
        $futureAdjustmentsQuery = InventoryAdjustment::where('created_at', '>', $endOfDay);
        if ($branch !== 'all') {
            $futureAdjustmentsQuery->whereHas('inventory', function($q) use ($branch) {
                $q->where('branch_id', $branch);
            });
        }
        $futureAdjustments = $futureAdjustmentsQuery->get()->groupBy('inventory_id');

        // 3. Fetch Future Sales (After the selected date)
        $futureOrderItems = OrderItem::whereHas('order', function ($query) use ($endOfDay) {
            $query->where('created_at', '>', $endOfDay)
                ->whereNotIn('status', ['cancelled']);
        })->with('menuItem.inventoryItems')->get();

        // Calculate Future Stock Out per Inventory Item
        $futureStockOuts = [];
        foreach ($futureOrderItems as $item) {
            if ($item->menuItem) {
                foreach ($item->menuItem->inventoryItems as $ingredient) {
                    // Apply branch filter
                    if ($branch !== 'all' && $ingredient->branch_id != $branch) {
                        continue;
                    }
                    
                    $qty = $item->quantity * $ingredient->pivot->quantity_needed;
                    if (!isset($futureStockOuts[$ingredient->id])) {
                        $futureStockOuts[$ingredient->id] = 0;
                    }
                    $futureStockOuts[$ingredient->id] += $qty;
                }
            }
        }

        // 4. Fetch Target Date Adjustments
        $todaysAdjustmentsQuery = InventoryAdjustment::with(['inventory', 'recorder'])
            ->whereBetween('created_at', [$startOfDay, $endOfDay]);
            
        if ($branch !== 'all') {
            $todaysAdjustmentsQuery->whereHas('inventory', function($q) use ($branch) {
                $q->where('branch_id', $branch);
            });
        }
        
        $todaysAdjustments = $todaysAdjustmentsQuery->get();

        $todaysAdjustmentsByItem = $todaysAdjustments->groupBy('inventory_id');

        // Separate Stock Ins for the list view
        $stockIns = $todaysAdjustments->where('adjustment_type', 'stock_in');

        // 5. Fetch Target Date Sales
        $todaysOrderItems = OrderItem::whereHas('order', function ($query) use ($startOfDay, $endOfDay) {
            $query->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->whereNotIn('status', ['cancelled']);
        })->with('menuItem.inventoryItems')->get();

        // Calculate Today's Stock Out per Inventory Item  
        $todaysStockOuts = [];
        foreach ($todaysOrderItems as $item) {
            if ($item->menuItem) {
                foreach ($item->menuItem->inventoryItems as $ingredient) {
                    // Apply branch filter
                    if ($branch !== 'all' && $ingredient->branch_id != $branch) {
                        continue;
                    }
                    
                    $qty = $item->quantity * $ingredient->pivot->quantity_needed;
                    if (!isset($todaysStockOuts[$ingredient->id])) {
                        $todaysStockOuts[$ingredient->id] = 0;
                    }
                    $todaysStockOuts[$ingredient->id] += $qty;
                }
            }
        }

        // 6. Build the Report Data
        $reportData = [];

        foreach ($inventoryItems as $item) {
            // Current Live Quantity
            $currentQty = $item->quantity;

            // Reverse Engineering logic
            $futureIn = 0;
            $futureSpoilage = 0;
            if (isset($futureAdjustments[$item->id])) {
                foreach ($futureAdjustments[$item->id] as $adj) {
                    if ($adj->adjustment_type == 'stock_in') {
                        $futureIn += $adj->quantity;
                    } elseif ($adj->adjustment_type == 'spoilage') {
                        $futureSpoilage += $adj->quantity;
                    }
                }
            }

            $futureOut = $futureStockOuts[$item->id] ?? 0;

            // Calculated End Count for the selected Date
            $endCount = $currentQty - $futureIn + $futureOut + $futureSpoilage;

            // Determine Today's stats
            $todayIn = 0;
            $todaySpoilage = 0;
            if (isset($todaysAdjustmentsByItem[$item->id])) {
                foreach ($todaysAdjustmentsByItem[$item->id] as $adj) {
                    if ($adj->adjustment_type == 'stock_in') {
                        $todayIn += $adj->quantity;
                    } elseif ($adj->adjustment_type == 'spoilage') {
                        $todaySpoilage += $adj->quantity;
                    }
                }
            }

            $todayOut = $todaysStockOuts[$item->id] ?? 0;

            // Start Count = End Count - In + Out + Spoilage
            $startCount = $endCount - $todayIn + $todayOut + $todaySpoilage;

            $reportData[] = (object) [
                'id' => $item->id,
                'name' => $item->name,
                'unit' => $item->unit,
                'category' => $item->category ? $item->category : 'Uncategorized',
                'start_count' => $startCount,
                'stock_in' => $todayIn,
                'stock_out' => $todayOut,
                'spoilage' => $todaySpoilage,
                'end_count' => $endCount,
            ];
        }

        // Prepared Dishes (Sales)
        $preparedDishes = OrderItem::with('menuItem')
            ->whereHas('order', function ($query) use ($startOfDay, $endOfDay) {
                $query->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->whereNotIn('status', ['cancelled']);
            })
            ->select('menu_item_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(unit_price * quantity) as total_amount'))
            ->groupBy('menu_item_id')
            ->get();

        return [
            'stockIns' => $stockIns,
            'preparedDishes' => $preparedDishes,
            'inventoryItems' => $inventoryItems,
            'reportData' => $reportData
        ];
    }

    public function stockIn(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $menuItem = \App\Models\MenuItem::with('inventoryItems')->find($request->menu_item_id);

            if (!$menuItem || $menuItem->inventoryItems->isEmpty()) {
                throw new \Exception('This menu item is not linked to any inventory ingredient.');
            }

            // Stock in for ALL linked inventory items proportional to quantity_needed
            foreach ($menuItem->inventoryItems as $ingredient) {
                $stockToAdd = $request->quantity * $ingredient->pivot->quantity_needed;
                $quantityBefore = $ingredient->quantity;

                // Update Inventory
                $ingredient->increment('quantity', $stockToAdd);
                $ingredient->increment('stock_in', $stockToAdd);

                $quantityAfter = $ingredient->fresh()->quantity;

                // Create Adjustment Record with before/after tracking
                InventoryAdjustment::create([
                    'inventory_id' => $ingredient->id,
                    'adjustment_type' => 'stock_in',
                    'quantity' => $stockToAdd,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $quantityAfter,
                    'reason' => $request->reason ?? "Stock in for {$menuItem->name}",
                    'recorded_by' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('inventory.dashboard')->with('success', 'Stock added successfully.');
    }

    /**
     * Display stock-in history with before/after quantities
     */
    public function stockInHistory(Request $request)
    {
        $date = $request->input('date');

        $query = InventoryAdjustment::with(['inventory', 'recorder'])
            ->where('adjustment_type', 'stock_in')
            ->orderBy('created_at', 'desc');

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $stockInHistory = $query->paginate(20);

        return view('inventory.stock_in_history', compact('stockInHistory', 'date'));
    }

    /**
     * Show form for creating daily inventory report
     */
    public function createDailyReport()
    {
        $user = Auth::user();
        $today = \Carbon\Carbon::today();

        // Fetch automated inventory stats for today
        $stats = $this->getInventoryStats($today->format('Y-m-d'));
        $reportData = $stats['reportData'];

        // Check if reports already exist for today
        $startOfDayReport = \App\Models\ShiftReport::where('user_id', $user->id)
            ->where('report_type', 'inventory_start')
            ->whereDate('shift_date', $today)
            ->first();

        $endOfDayReport = \App\Models\ShiftReport::where('user_id', $user->id)
            ->where('report_type', 'inventory_end')
            ->whereDate('shift_date', $today)
            ->first();

        // Fetch user's report history
        $reportHistory = \App\Models\ShiftReport::where('user_id', $user->id)
            ->whereIn('report_type', ['inventory_start', 'inventory_end'])
            ->latest()
            ->paginate(10);

        return view('inventory.daily_report', compact('today', 'reportHistory', 'startOfDayReport', 'endOfDayReport', 'reportData'));
    }

    /**
     * Store daily inventory report (start or end of day)
     */
    public function storeDailyReport(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'report_type' => 'required|in:inventory_start,inventory_end',
            'shift_date' => 'required|date',
            'report_json' => 'nullable|json',
            'content' => 'nullable|string',
        ]);

        // Decode the JSON data
        $reportItems = json_decode($request->report_json, true);

        // Calculate Totals from the automated data if available
        $totalStockIn = 0;
        $totalStockOut = 0;
        $totalRemaining = 0;
        $totalSpoilage = 0;

        if ($reportItems && is_array($reportItems)) {
            foreach ($reportItems as $item) {
                // Ensure values are numeric
                $in = floatval($item['stock_in'] ?? 0);
                $out = floatval($item['stock_out'] ?? 0);
                $end = floatval($item['end_count'] ?? 0);
                $spoil = floatval($item['spoilage'] ?? 0);

                $totalStockIn += $in;
                $totalStockOut += $out;
                $totalRemaining += $end;
                $totalSpoilage += $spoil;
            }
        } else {
            // Fallback
            $totalStockIn = $request->stock_in ?? 0;
            $totalStockOut = $request->stock_out ?? 0;
            $totalRemaining = $request->remaining_stock ?? 0;
            $totalSpoilage = $request->spoilage ?? 0;
        }

        // Generate Human Readable Content Table for the admin view
        $formattedContent = $request->input('content') . "\n\n";
        $formattedContent .= "DETAILED INVENTORY REPORT\n";
        $formattedContent .= str_repeat("-", 80) . "\n";

        if ($request->report_type === 'inventory_start') {
            $formattedContent .= sprintf("%-30s | %-15s | %-15s\n", "Item Name", "Start Qty", "Adjustments");
            $formattedContent .= str_repeat("-", 80) . "\n";
            if ($reportItems) {
                foreach ($reportItems as $item) {
                    if ($item['start_count'] > 0 || $item['stock_in'] > 0) {
                        $startStr = $item['start_count'] . ' ' . substr($item['unit'], 0, 4);
                        $adjStr = $item['stock_in'] > 0 ? '+' . $item['stock_in'] : '-';
                        $formattedContent .= sprintf(
                            "%-30s | %-15s | %-15s\n",
                            substr($item['name'], 0, 30),
                            $startStr,
                            $adjStr
                        );
                    }
                }
            }
        } else {
            $formattedContent .= sprintf("%-25s | %-10s | %-10s | %-10s\n", "Item Name", "Added", "Sold", "End");
            $formattedContent .= str_repeat("-", 80) . "\n";
            if ($reportItems) {
                foreach ($reportItems as $item) {
                    if ($item['stock_in'] > 0 || $item['stock_out'] > 0 || $item['end_count'] > 0) {
                        $addedStr = $item['stock_in'] > 0 ? '+' . $item['stock_in'] : '-';
                        $soldStr = $item['stock_out'] > 0 ? '-' . $item['stock_out'] : '-';
                        $endStr = $item['end_count'] . ' ' . substr($item['unit'], 0, 4);

                        $formattedContent .= sprintf(
                            "%-25s | %-10s | %-10s | %-10s\n",
                            substr($item['name'], 0, 25),
                            $addedStr,
                            $soldStr,
                            $endStr
                        );
                    }
                }
            }
        }
        $formattedContent .= str_repeat("-", 80) . "\n";

        $shiftDate = \Carbon\Carbon::parse($request->shift_date);

        // Check if report exists
        $exists = \App\Models\ShiftReport::where('user_id', $user->id)
            ->where('report_type', $request->report_type)
            ->whereDate('shift_date', $shiftDate)
            ->exists();

        if ($exists) {
            return back()->withErrors(['report_type' => 'Report already submitted for this date.']);
        }

        \App\Models\ShiftReport::create([
            'user_id' => $user->id,
            'report_type' => $request->report_type,
            'shift_date' => $shiftDate,
            'stock_in' => $totalStockIn,
            'stock_out' => $totalStockOut,
            'remaining_stock' => $totalRemaining,
            'spoilage' => $totalSpoilage,
            'returns' => $request->returns ?? 0,
            'return_reason' => $request->return_reason,
            'content' => $formattedContent,
            'status' => 'submitted',
            'branch_id' => $user->branch_id,
        ]);

        $reportName = $request->report_type === 'inventory_start' ? 'Start of Day' : 'End of Day';

        return redirect()->route('inventory.daily-report')->with('success', $reportName . ' report submitted successfully.');
    }
}
