<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index()
    {
        $branches = DB::table('branches')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean'
        ]);

        DB::table('branches')->insert([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'Branch created successfully!');
    }

    public function edit($id)
    {
        $branch = DB::table('branches')->where('id', $id)->first();

        if (!$branch) {
            return redirect()->route('admin.branches.index')->with('error', 'Branch not found!');
        }

        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean'
        ]);

        $branch = DB::table('branches')->where('id', $id)->first();

        if (!$branch) {
            return redirect()->route('admin.branches.index')->with('error', 'Branch not found!');
        }

        DB::table('branches')->where('id', $id)->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'updated_at' => now()
        ]);

        return redirect()->route('admin.branches.index')->with('success', 'Branch updated successfully!');
    }

    public function destroy($id)
    {
        $branch = DB::table('branches')->where('id', $id)->first();

        if (!$branch) {
            return redirect()->route('admin.branches.index')->with('error', 'Branch not found!');
        }

        // Check if branch has products
        $productCount = DB::table('products')->where('branch_id', $id)->count();
        if ($productCount > 0) {
            return redirect()->route('admin.branches.index')->with('error', 'Cannot delete branch with existing products!');
        }

        DB::table('branches')->where('id', $id)->delete();

        return redirect()->route('admin.branches.index')->with('success', 'Branch deleted successfully!');
    }
}