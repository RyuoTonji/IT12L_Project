<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('products')
            ->join('branches', 'products.branch_id', '=', 'branches.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereNull('products.deleted_at')
            ->select(
                'products.*',
                'branches.name as branch_name',
                'categories.name as category_name'
            );

        // Search filter
        if ($request->filled('search')) {
            $query->where('products.name', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('products.category_id', $request->category_id);
        }

        // Branch filter
        if ($request->filled('branch_id')) {
            $query->where('products.branch_id', $request->branch_id);
        }

        // Availability filter
        if ($request->filled('is_available')) {
            $query->where('products.is_available', $request->is_available);
        }

        // Price range filter
        if ($request->filled('price_range')) {
            switch ($request->price_range) {
                case '0-100':
                    $query->whereBetween('products.price', [0, 100]);
                    break;
                case '100-200':
                    $query->whereBetween('products.price', [100, 200]);
                    break;
                case '200-500':
                    $query->whereBetween('products.price', [200, 500]);
                    break;
                case '500+':
                    $query->where('products.price', '>=', 500);
                    break;
            }
        }

        $products = $query->orderBy('products.id', 'desc')
            ->paginate(20)
            ->appends($request->all());

        // Get categories and branches for filter dropdowns
        $categories = DB::table('categories')->whereNull('deleted_at')->get();
        $branches = DB::table('branches')->whereNull('deleted_at')->get();

        return view('admin.products.index', compact('products', 'categories', 'branches'));
    }

    public function create()
    {
        $branches = DB::table('branches')->whereNull('deleted_at')->get();
        $categories = DB::table('categories')->whereNull('deleted_at')->get();
        
        return view('admin.products.create', compact('branches', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
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
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'is_available' => $request->has('is_available') ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function edit($id)
    {
        $product = DB::table('products')
            ->whereNull('deleted_at')
            ->where('id', $id)
            ->first();
        
        if (!$product) {
            return redirect()->route('admin.products.index')->with('error', 'Product not found!');
        }

        $branches = DB::table('branches')->whereNull('deleted_at')->get();
        $categories = DB::table('categories')->whereNull('deleted_at')->get();
        
        return view('admin.products.edit', compact('product', 'branches', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean',
        ]);

        $product = DB::table('products')
            ->whereNull('deleted_at')
            ->where('id', $id)
            ->first();
        
        if (!$product) {
            return redirect()->route('admin.products.index')->with('error', 'Product not found!');
        }

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        DB::table('products')->where('id', $id)->update([
            'branch_id' => $request->branch_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'is_available' => $request->has('is_available') ? 1 : 0,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $product = DB::table('products')
            ->whereNull('deleted_at')
            ->where('id', $id)
            ->first();
        
        if (!$product) {
            return redirect()->route('admin.products.index')->with('error', 'Product not found!');
        }

        // Soft delete
        DB::table('products')->where('id', $id)->update([
            'deleted_at' => now()
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product archived successfully!');
    }

    public function archived()
    {
        $products = DB::table('products')
            ->join('branches', 'products.branch_id', '=', 'branches.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereNotNull('products.deleted_at')
            ->select(
                'products.*',
                'branches.name as branch_name',
                'categories.name as category_name'
            )
            ->orderBy('products.deleted_at', 'desc')
            ->paginate(20);

        return view('admin.products.archived', compact('products'));
    }

    public function restore($id)
    {
        DB::table('products')->where('id', $id)->update([
            'deleted_at' => null
        ]);

        return redirect()->route('admin.products.archived')->with('success', 'Product restored successfully!');
    }

    public function toggleAvailability($id)
    {
        try {
            $product = DB::table('products')
                ->whereNull('deleted_at')
                ->where('id', $id)
                ->first();
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'error' => 'Product not found'
                ], 404);
            }

            // Convert to boolean and toggle
            $newStatus = $product->is_available ? 0 : 1;
            
            DB::table('products')->where('id', $id)->update([
                'is_available' => $newStatus,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'is_available' => (bool)$newStatus,
                'message' => 'Product availability updated successfully!'
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Toggle availability error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while updating availability'
            ], 500);
        }
    }
}