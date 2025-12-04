<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = DB::table('products')
            ->join('branches', 'products.branch_id', '=', 'branches.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.*',
                'branches.name as branch_name',
                'categories.name as category_name'
            )
            ->orderBy('products.id', 'desc')
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $branches = DB::table('branches')->get();
        $categories = DB::table('categories')->get();
        
        return view('admin.products.create', compact('branches', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:200',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        DB::table('products')->insert([
            'branch_id' => $request->branch_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'image' => $imagePath,
            'is_available' => $request->has('is_available') ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/products')->with('success', 'Product created successfully!');
    }

    public function edit($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        
        if (!$product) {
            return redirect('/admin/products')->with('error', 'Product not found!');
        }

        $branches = DB::table('branches')->get();
        $categories = DB::table('categories')->get();
        
        return view('admin.products.edit', compact('product', 'branches', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:200',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean',
        ]);

        $product = DB::table('products')->where('id', $id)->first();
        
        if (!$product) {
            return redirect('/admin/products')->with('error', 'Product not found!');
        }

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        DB::table('products')->where('id', $id)->update([
            'branch_id' => $request->branch_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'image' => $imagePath,
            'is_available' => $request->has('is_available') ? 1 : 0,
            'updated_at' => now(),
        ]);

        return redirect('/admin/products')->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        
        if (!$product) {
            return redirect('/admin/products')->with('error', 'Product not found!');
        }

        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        DB::table('products')->where('id', $id)->delete();

        return redirect('/admin/products')->with('success', 'Product deleted successfully!');
    }
}
