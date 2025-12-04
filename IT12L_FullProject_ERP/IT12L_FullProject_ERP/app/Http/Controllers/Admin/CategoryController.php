<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('categories')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string'
        ]);

        DB::table('categories')->insert([
            'name' => $request->name,
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    public function edit($id)
    {
        $category = DB::table('categories')->where('id', $id)->first();

        if (!$category) {
            return redirect()->route('admin.categories.index')->with('error', 'Category not found!');
        }

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $id,
            'description' => 'nullable|string'
        ]);

        $category = DB::table('categories')->where('id', $id)->first();

        if (!$category) {
            return redirect()->route('admin.categories.index')->with('error', 'Category not found!');
        }

        DB::table('categories')->where('id', $id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'updated_at' => now()
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy($id)
    {
        $category = DB::table('categories')->where('id', $id)->first();

        if (!$category) {
            return redirect()->route('admin.categories.index')->with('error', 'Category not found!');
        }

        // Check if category has products
        $productCount = DB::table('products')->where('category_id', $id)->count();
        if ($productCount > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Cannot delete category with existing products!');
        }

        DB::table('categories')->where('id', $id)->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }
}