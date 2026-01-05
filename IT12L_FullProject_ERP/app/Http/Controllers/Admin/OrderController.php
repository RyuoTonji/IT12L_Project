<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'branch']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by branch
        if ($request->has('branch_id') && $request->branch_id != '') {
            $query->where('branch_id', $request->branch_id);
        }

        // Date range filter
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        $branches = Branch::all();

        return view('admin.orders.index', compact('orders', 'branches'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'branch', 'items'])->find($id);

        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found!');
        }

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,confirmed,preparing,ready,picked up,cancelled'
    ]);

    $order = Order::findOrFail($id);

    // FINAL states
    if (in_array($order->status, ['picked up', 'cancelled'])) {
        return back()->with('error', 'This order is already final.');
    }

    // Allowed forward flow
    $flow = ['pending', 'confirmed', 'preparing', 'ready', 'picked up'];

    $currentIndex = array_search($order->status, $flow);
    $newIndex = array_search($request->status, $flow);

    // Cancel allowed ONLY before picked up
    if ($request->status === 'cancelled') {
        $order->status = 'cancelled';
        $order->save();
        return back()->with('success', 'Order cancelled.');
    }

    // Block backward or invalid transitions
    if ($newIndex === false || $newIndex <= $currentIndex) {
        return back()->with('error', 'Invalid status change.');
    }

    $order->status = $request->status;
    $order->save();

    return back()->with('success', 'Order status updated.');
}


    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found!');
        }

        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order archived successfully!');
    }

    public function archived(Request $request)
    {
        $query = Order::onlyTrashed()->with(['user', 'branch']);

        // Filter by branch
        if ($request->has('branch_id') && $request->branch_id != '') {
            $query->where('branch_id', $request->branch_id);
        }

        $orders = $query->orderBy('deleted_at', 'desc')->paginate(20);
        $branches = Branch::all();

        return view('admin.orders.archived', compact('orders', 'branches'));
    }

    public function restore($id)
    {
        $order = Order::onlyTrashed()->find($id);

        if ($order) {
            $order->restore();
            return redirect()->route('admin.orders.archived')->with('success', 'Order restored successfully!');
        }

        return redirect()->route('admin.orders.archived')->with('error', 'Order not found!');
    }
}