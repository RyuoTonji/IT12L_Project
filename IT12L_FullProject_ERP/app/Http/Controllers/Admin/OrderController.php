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
            ->select(
                'orders.*',
                'users.name as user_name',
                'users.email as user_email',
                'branches.name as branch_name'
            )
            ->orderBy('orders.created_at', 'desc');

        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('orders.status', $request->status);
        }

        $orders = $query->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->join('branches', 'orders.branch_id', '=', 'branches.id')
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

        // Get order items
        $orderItems = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'order_items.*',
                'products.name as product_name',
                'products.image as product_image'
            )
            ->where('order_items.order_id', $id)
            ->get();

        return view('admin.orders.show', compact('order', 'orderItems'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,delivered,cancelled'
        ]);

        $order = DB::table('orders')->where('id', $id)->first();

        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found!');
        }

        DB::table('orders')->where('id', $id)->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return redirect()->route('admin.orders.show', $id)->with('success', 'Order status updated successfully!');
    }
}