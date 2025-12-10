<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('users')
            ->where('role', 'customer')
            ->whereNull('deleted_at');

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $customers = $query->orderBy('id', 'desc')->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'password' => 'required|min:8|confirmed',
            'is_active' => 'nullable|boolean'
        ]);

        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'is_active' => $request->has('is_active') ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer account created successfully!');
    }

    public function show($id)
    {
        $customer = DB::table('users')
            ->where('id', $id)
            ->where('role', 'customer')
            ->whereNull('deleted_at')
            ->first();

        if (!$customer) {
            return redirect()->route('admin.customers.index')->with('error', 'Customer not found!');
        }

        // Get customer's orders
        $orders = DB::table('orders')
            ->join('branches', 'orders.branch_id', '=', 'branches.id')
            ->whereNull('orders.deleted_at')
            ->where('orders.user_id', $id)
            ->select(
                'orders.*',
                'branches.name as branch_name'
            )
            ->orderBy('orders.created_at', 'desc')
            ->paginate(10);

        // Get order statistics
        $totalOrders = DB::table('orders')
            ->whereNull('deleted_at')
            ->where('user_id', $id)
            ->count();

        $totalSpent = DB::table('orders')
            ->whereNull('deleted_at')
            ->where('user_id', $id)
            ->whereIn('status', ['confirmed', 'delivered'])
            ->sum('total_amount');

        return view('admin.customers.show', compact('customer', 'orders', 'totalOrders', 'totalSpent'));
    }

    public function edit($id)
    {
        $customer = DB::table('users')
            ->where('id', $id)
            ->where('role', 'customer')
            ->whereNull('deleted_at')
            ->first();

        if (!$customer) {
            return redirect()->route('admin.customers.index')->with('error', 'Customer not found!');
        }

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'password' => 'nullable|min:8|confirmed',
            'is_active' => 'boolean'
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'updated_at' => now()
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $id)->update($updateData);

        return redirect()->route('admin.customers.show', $id)->with('success', 'Customer updated successfully!');
    }

    public function toggleStatus($id)
    {
        $customer = DB::table('users')
            ->where('id', $id)
            ->where('role', 'customer')
            ->whereNull('deleted_at')
            ->first();

        if (!$customer) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Customer not found');
        }

        // Toggle the status
        $newStatus = $customer->is_active ? 0 : 1;
        
        DB::table('users')->where('id', $id)->update([
            'is_active' => $newStatus,
            'updated_at' => now()
        ]);

        $statusText = $newStatus ? 'enabled' : 'disabled';
        
        return redirect()->route('admin.customers.index')
            ->with('success', "Customer status updated successfully! Account is now {$statusText}.");
    }

    public function destroy($id)
    {
        $customer = DB::table('users')
            ->where('id', $id)
            ->where('role', 'customer')
            ->whereNull('deleted_at')
            ->first();

        if (!$customer) {
            return redirect()->route('admin.customers.index')->with('error', 'Customer not found!');
        }

        // Soft delete
        DB::table('users')->where('id', $id)->update([
            'deleted_at' => now()
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Customer account archived successfully!');
    }

    public function archived()
    {
        $customers = DB::table('users')
            ->where('role', 'customer')
            ->whereNotNull('deleted_at')
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('admin.customers.archived', compact('customers'));
    }

    public function restore($id)
    {
        DB::table('users')->where('id', $id)->update([
            'deleted_at' => null,
            'is_active' => 1
        ]);

        return redirect()->route('admin.customers.archived')->with('success', 'Customer account restored successfully!');
    }
}