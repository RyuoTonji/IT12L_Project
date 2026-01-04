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

        $order = Order::find($id);

        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found!');
        }

        $order->update([
            'status' => $request->status
        ]);

        return redirect()->route('admin.orders.show', $id)->with('success', 'Order status updated successfully!');
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