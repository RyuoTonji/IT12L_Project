<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalOrders = DB::table('orders')->count();
        
        $totalRevenue = DB::table('orders')
            ->whereIn('status', ['confirmed', 'delivered'])
            ->sum('total_amount');
        
        $pendingOrders = DB::table('orders')
            ->where('status', 'pending')
            ->count();
        
        $totalProducts = DB::table('products')->count();

        // Get recent orders
        $recentOrders = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->join('branches', 'orders.branch_id', '=', 'branches.id')
            ->select(
                'orders.id',
                'orders.total_amount',
                'orders.status',
                'orders.created_at as ordered_at',
                'users.name as user_name',
                'branches.name as branch_name'
            )
            ->orderBy('orders.created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'totalProducts',
            'recentOrders'
        ));
    }
}