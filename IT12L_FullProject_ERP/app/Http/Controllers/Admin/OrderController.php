<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->join('branches', 'orders.branch_id', '=', 'branches.id')
            ->whereNull('orders.deleted_at')
            ->select(
                'orders.*',
                'users.name as user_name',
                'users.email as user_email',
                'branches.name as branch_name'
            )
            ->orderBy('orders.created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('orders.status', $request->status);
        }

        // Filter by branch
        if ($request->has('branch_id') && $request->branch_id != '') {
            $query->where('orders.branch_id', $request->branch_id);
        }

        // Date range filter
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('orders.created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('orders.created_at', '<=', $request->end_date);
        }

        $orders = $query->paginate(20);
        $branches = DB::table('branches')->whereNull('deleted_at')->get();

        return view('admin.orders.index', compact('orders', 'branches'));
    }

    public function show($id)
    {
        $order = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->join('branches', 'orders.branch_id', '=', 'branches.id')
            ->whereNull('orders.deleted_at')
            ->select(
                'orders.*',
                'users.name as user_name',
                'users.email as user_email',
                'users.phone as user_phone',
                'branches.name as branch_name',
                'branches.address as branch_address'
            )
            ->where('orders.id', $id)
            ->first();

        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found!');
        }

        // Get order items - no JOIN needed since we store product details as snapshot
        $orderItems = DB::table('order_items')
            ->select('order_items.*')
            ->where('order_items.order_id', $id)
            ->get();

        return view('admin.orders.show', compact('order', 'orderItems'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled'
        ]);

        $order = DB::table('orders')
            ->whereNull('deleted_at')
            ->where('id', $id)
            ->first();

        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found!');
        }

        DB::table('orders')->where('id', $id)->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return redirect()->route('admin.orders.show', $id)->with('success', 'Order status updated successfully!');
    }

    public function destroy($id)
    {
        $order = DB::table('orders')
            ->whereNull('deleted_at')
            ->where('id', $id)
            ->first();

        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found!');
        }

        // Soft delete
        DB::table('orders')->where('id', $id)->update([
            'deleted_at' => now()
        ]);

        return redirect()->route('admin.orders.index')->with('success', 'Order archived successfully!');
    }

    public function archived(Request $request)
    {
        $query = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->join('branches', 'orders.branch_id', '=', 'branches.id')
            ->whereNotNull('orders.deleted_at')
            ->select(
                'orders.*',
                'users.name as user_name',
                'users.email as user_email',
                'branches.name as branch_name'
            )
            ->orderBy('orders.deleted_at', 'desc');

        // Filter by branch
        if ($request->has('branch_id') && $request->branch_id != '') {
            $query->where('orders.branch_id', $request->branch_id);
        }

        $orders = $query->paginate(20);
        $branches = DB::table('branches')->whereNull('deleted_at')->get();

        return view('admin.orders.archived', compact('orders', 'branches'));
    }

    public function restore($id)
    {
        DB::table('orders')->where('id', $id)->update([
            'deleted_at' => null
        ]);

        return redirect()->route('admin.orders.archived')->with('success', 'Order restored successfully!');
    }
}