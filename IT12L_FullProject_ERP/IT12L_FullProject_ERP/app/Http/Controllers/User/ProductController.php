<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = Product::with(['branch', 'category'])->findOrFail($id);

        return view('products.detail', [
            'product' => $product,
            'page_title' => $product->name
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $branchId = $request->input('branch_id');

        $products = Product::with(['branch', 'category'])
            ->search($query)
            ->available();

        if ($branchId) {
            $products->byBranch($branchId);
        }

        $products = $products->get();

        if ($request->expectsJson() || $request->input('json')) {
            return response()->json(['products' => $products]);
        }

        return view('products.search', [
            'products' => $products,
            'query' => $query,
            'page_title' => 'Search Results: ' . $query
        ]);
    }

    public function getDetails($id)
    {
        $product = Product::with(['branch', 'category'])->findOrFail($id);

        return response()->json(['product' => $product]);
    }
}