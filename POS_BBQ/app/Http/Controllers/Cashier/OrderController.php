<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Table;
use App\Models\Category;
use App\Models\Activity;
use App\Models\VoidRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');

        $orders = Order::with(['table', 'user'])
            ->when($user->branch_id, function ($query) use ($user) {
                // Filter by branch for branch-specific users
                return $query->where('branch_id', $user->branch_id);
            })
            ->when($search, function ($query) use ($search) {
                // Search by order ID, customer name, or date
                return $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhereDate('created_at', $search)
                        ->orWhere('created_at', 'like', "%{$search}%");
                })
                    // Prioritize exact matches
                    ->orderByRaw("CASE 
                    WHEN id = ? THEN 1
                    WHEN customer_name = ? THEN 2
                    WHEN DATE(created_at) = ? THEN 3
                    ELSE 4
                END", [$search, $search, $search]);
            })
            ->latest()
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('cashier.orders.index', compact('orders', 'search'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        // Filter tables by branch
        $tables = Table::where('status', 'available')
            ->when($user->branch_id, function ($query) use ($user) {
                return $query->where('branch_id', $user->branch_id);
            })
            ->get();
        $categories = Category::orderBy('sort_order', 'asc')->with([
            'menuItems' => function ($query) {
                $query->where('is_available', true)->orderBy('id');
            }
        ])->get();

        $selectedTableId = $request->input('table_id');
        $selectedTable = null;

        if ($selectedTableId) {
            $selectedTable = Table::find($selectedTableId);
        }

        return view('cashier.orders.create', compact('tables', 'categories', 'selectedTable'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'nullable|exists:tables,id',
            'customer_name' => 'nullable|string|max:255',
            'order_type' => 'required|in:dine-in,takeout',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create the order
            $order = new Order();
            $order->table_id = $request->table_id;
            $order->user_id = Auth::id();
            $order->branch_id = Auth::user()->branch_id; // Set branch from user
            $order->customer_name = $request->customer_name;
            $order->order_type = $request->order_type;
            $order->status = 'new';
            $order->payment_status = 'pending';
            $order->total_amount = 0;
            $order->save();

            // Add order items and calculate total
            $total = 0;

            foreach ($request->items as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->menu_item_id = $menuItem->id;
                $orderItem->quantity = $item['quantity'];
                $orderItem->unit_price = $menuItem->price;
                $orderItem->notes = $item['notes'] ?? null;
                $orderItem->save();

                $total += $menuItem->price * $item['quantity'];
            }

            // Update order total
            $order->total_amount = $total;
            $order->save();

            // Update table status if this is a dine-in order
            if ($request->order_type == 'dine-in' && $request->table_id) {
                $table = Table::find($request->table_id);
                $table->status = 'occupied';
                $table->save();
            }

            // Automated Activity Logging
            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'create_order',
                'details' => "Created Order #{$order->id} ({$order->order_type}) - Total: {$order->total_amount}",
                'status' => 'info',
                'related_id' => $order->id,
                'related_model' => Order::class,
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'Order created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating order: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Order $order)
    {
        // Check if user can access this order
        $user = Auth::user();
        if ($user->branch_id && $order->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['orderItems.menuItem', 'table', 'user']);
        return view('cashier.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        // Check if user can access this order
        $user = Auth::user();
        if ($user->branch_id && $order->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized access to this order.');
        }

        // Only allow editing of orders that are not completed or cancelled
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Cannot edit completed or cancelled orders');
        }

        $order->load(['orderItems.menuItem', 'table']);

        $categories = Category::orderBy('sort_order', 'asc')->with([
            'menuItems' => function ($query) {
                $query->where('is_available', true)->orderBy('id');
            }
        ])->get();

        return view('cashier.orders.edit', compact('order', 'categories'));
    }

    public function update(Request $request, Order $order)
    {
        // Only allow updating of orders that are not completed or cancelled
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Cannot update completed or cancelled orders');
        }

        $allowedStatuses = 'new,preparing,ready,served,completed';
        if (Auth::user()->role === 'admin' || Auth::user()->role === 'manager') {
            $allowedStatuses .= ',cancelled';
        }

        $request->validate([
            'status' => 'required|in:' . $allowedStatuses,
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:order_items,id',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Update order status
            $oldStatus = $order->status;
            $newStatus = $request->status;

            $order->status = $newStatus;

            // Handle table status changes based on order status
            if ($order->table_id) {
                $table = $order->table;

                // If order is cancelled or completed, free up the table
                if (in_array($newStatus, ['completed', 'cancelled']) && !in_array($oldStatus, ['completed', 'cancelled'])) {
                    $table->status = 'available';
                    $table->save();
                }

                // If order was cancelled/completed and is now active, occupy the table
                if (!in_array($newStatus, ['completed', 'cancelled']) && in_array($oldStatus, ['completed', 'cancelled'])) {
                    $table->status = 'occupied';
                    $table->save();
                }
            }

            // Get existing order items
            $existingItems = $order->orderItems->keyBy('id');
            $keepItemIds = [];

            // Update or create order items
            $total = 0;

            foreach ($request->items as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);

                if (isset($item['id']) && $existingItems->has($item['id'])) {
                    // Update existing item
                    $orderItem = $existingItems->get($item['id']);
                    $orderItem->menu_item_id = $menuItem->id;
                    $orderItem->quantity = $item['quantity'];
                    $orderItem->unit_price = $menuItem->price;
                    $orderItem->notes = $item['notes'] ?? null;
                    $orderItem->save();

                    $keepItemIds[] = $orderItem->id;
                } else {
                    // Create new item
                    $orderItem = new OrderItem();
                    $orderItem->order_id = $order->id;
                    $orderItem->menu_item_id = $menuItem->id;
                    $orderItem->quantity = $item['quantity'];
                    $orderItem->unit_price = $menuItem->price;
                    $orderItem->notes = $item['notes'] ?? null;
                    $orderItem->save();

                    $keepItemIds[] = $orderItem->id;
                }

                $total += $menuItem->price * $item['quantity'];
            }

            // Delete removed items
            foreach ($existingItems as $existingItem) {
                if (!in_array($existingItem->id, $keepItemIds)) {
                    $existingItem->delete();
                }
            }

            // Update order total
            $order->total_amount = $total;
            $order->save();

            // Automated Activity Logging
            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'update_order',
                'details' => "Updated Order #{$order->id} - Status: {$newStatus}, Total: {$total}",
                'status' => 'info',
                'related_id' => $order->id,
                'related_model' => Order::class,
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'Order updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating order: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update the order status from the kitchen display.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, Order $order)
    {
        $allowedStatuses = 'preparing,ready,served,completed';
        if (Auth::user()->role === 'admin' || Auth::user()->role === 'manager') {
            $allowedStatuses .= ',cancelled';
        }

        $request->validate([
            'status' => 'required|in:' . $allowedStatuses,
        ]);

        try {
            DB::beginTransaction();

            // Update order status
            $oldStatus = $order->status;
            $newStatus = $request->status;
            $order->status = $newStatus;
            $order->save();

            // Handle table status changes if needed
            if ($order->table_id) {
                $table = $order->table;

                // If order is cancelled or completed, free up the table
                if (in_array($newStatus, ['completed', 'cancelled']) && !in_array($oldStatus, ['completed', 'cancelled'])) {
                    $table->status = 'available';
                    $table->save();
                }
            }

            // Automated Activity Logging
            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'update_order_status',
                'details' => "Updated Order #{$order->id} status to {$newStatus}",
                'status' => 'info',
                'related_id' => $order->id,
                'related_model' => Order::class,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'updated_at' => $order->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating order status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating order status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Order $order)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'manager') {
            return redirect()->route('orders.index')
                ->with('error', 'Unauthorized action.');
        }

        // Only allow deletion of orders that are new
        if ($order->status != 'new') {
            return redirect()->route('orders.index')
                ->with('error', 'Only new orders can be deleted');
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Free up table if this was a dine-in order
            if ($order->table_id) {
                $table = $order->table;
                $table->status = 'available';
                $table->save();
            }

            // Delete order items
            $order->orderItems()->delete();

            // Delete the order
            $order->delete();

            // Automated Activity Logging
            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'void_order',
                'details' => "Voided Order #{$order->id}",
                'status' => 'warning', // Warning for void actions
                'related_id' => $order->id,
                'related_model' => Order::class,
            ]);

            DB::commit();

            return redirect()->route('orders.index')->with('success', 'Order deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting order: ' . $e->getMessage());
        }
    }
    public function requestVoid(Request $request, Order $order)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255',
            'reason_tags' => 'nullable|array',
        ]);

        // Check if a pending request already exists
        if ($order->voidRequests()->where('status', 'pending')->exists()) {
            return back()->with('error', 'A void request is already pending for this order.');
        }

        VoidRequest::create([
            'order_id' => $order->id,
            'requester_id' => Auth::id(),
            'reason' => $request->reason,
            'reason_tags' => $request->reason_tags,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Void request submitted successfully. Waiting for manager approval.');
    }
}
