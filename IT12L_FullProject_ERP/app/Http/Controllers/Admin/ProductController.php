<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
<<<<<<< Updated upstream
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
=======
        $query = Product::with(['branch', 'category']);

        // Search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Branch filter
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Availability filter
        if ($request->filled('is_available')) {
            $query->where('is_available', $request->is_available);
        }

        // Price range filter
        if ($request->filled('price_range')) {
            switch ($request->price_range) {
                case '0-100':
                    $query->whereBetween('price', [0, 100]);
                    break;
                case '100-200':
                    $query->whereBetween('price', [100, 200]);
                    break;
                case '200-500':
                    $query->whereBetween('price', [200, 500]);
                    break;
                case '500+':
                    $query->where('price', '>=', 500);
                    break;
            }
        }

        $products = $query->orderBy('id', 'desc')
            ->paginate(20)
            ->appends($request->all());

        // Get categories and branches for filter dropdowns
        $categories = Category::all();
        $branches = Branch::all();

        return view('admin.products.index', compact('products', 'categories', 'branches'));
>>>>>>> Stashed changes
    }

    public function create()
    {
<<<<<<< Updated upstream
        $branches = DB::table('branches')->get();
        $categories = DB::table('categories')->get();
        
=======
        $branches = Branch::all();
        $categories = Category::all();

>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
            $imagePath = $request->file('image')->store('products', 'public');
=======
            try {
                $image = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Store in public/products directory
                $imagePath = $image->storeAs('products', $filename, 'public');

                Log::info('Product image uploaded successfully', ['filename' => $filename]);
            } catch (\Exception $e) {
                Log::error('Image upload failed', ['error' => $e->getMessage()]);
                return back()->withInput()->with('error', 'Failed to upload image. Please try again.');
            }
>>>>>>> Stashed changes
        }

        Product::create([
            'branch_id' => $request->branch_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'image' => $imagePath,
            'is_available' => $request->has('is_available') ? 1 : 0,
        ]);

        return redirect('/admin/products')->with('success', 'Product created successfully!');
    }

    public function edit($id)
    {
<<<<<<< Updated upstream
        $product = DB::table('products')->where('id', $id)->first();
        
=======
        $product = Product::find($id);

>>>>>>> Stashed changes
        if (!$product) {
            return redirect('/admin/products')->with('error', 'Product not found!');
        }

<<<<<<< Updated upstream
        $branches = DB::table('branches')->get();
        $categories = DB::table('categories')->get();
        
=======
        $branches = Branch::all();
        $categories = Category::all();

>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
        $product = DB::table('products')->where('id', $id)->first();
        
=======
        $product = Product::find($id);

>>>>>>> Stashed changes
        if (!$product) {
            return redirect('/admin/products')->with('error', 'Product not found!');
        }

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
<<<<<<< Updated upstream
            // Delete old image
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
=======
            try {
                // Delete old image if exists
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }

                $image = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Store new image
                $imagePath = $image->storeAs('products', $filename, 'public');

                Log::info('Product image updated successfully', ['filename' => $filename]);
            } catch (\Exception $e) {
                Log::error('Image update failed', ['error' => $e->getMessage()]);
                return back()->withInput()->with('error', 'Failed to update image. Please try again.');
>>>>>>> Stashed changes
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'branch_id' => $request->branch_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'image' => $imagePath,
            'is_available' => $request->has('is_available') ? 1 : 0,
        ]);

        return redirect('/admin/products')->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
<<<<<<< Updated upstream
        $product = DB::table('products')->where('id', $id)->first();
        
=======
        $product = Product::find($id);

>>>>>>> Stashed changes
        if (!$product) {
            return redirect('/admin/products')->with('error', 'Product not found!');
        }

<<<<<<< Updated upstream
        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
=======
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product archived successfully!');
    }

    public function archived()
    {
        $products = Product::onlyTrashed()
            ->with(['branch', 'category'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('admin.products.archived', compact('products'));
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->find($id);

        if (!$product) {
            return redirect()->route('admin.products.archived')->with('error', 'Product not found!');
        }

        $product->restore();

        return redirect()->route('admin.products.archived')->with('success', 'Product restored successfully!');
    }

    public function toggleAvailability($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'error' => 'Product not found'
                ], 404);
            }

            // Toggle the status
            $product->update([
                'is_available' => !$product->is_available
            ]);

            return response()->json([
                'success' => true,
                'is_available' => (bool) $product->is_available,
                'message' => 'Product availability updated successfully!'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Toggle availability error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while updating availability'
            ], 500);
>>>>>>> Stashed changes
        }

        DB::table('products')->where('id', $id)->delete();

        return redirect('/admin/products')->with('success', 'Product deleted successfully!');
    }
}