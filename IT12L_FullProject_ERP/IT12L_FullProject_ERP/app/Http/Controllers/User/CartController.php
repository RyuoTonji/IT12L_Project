<?php

/**
 * File: app/Http/Controllers/User/CartController.php
 * COMPLETE WORKING VERSION - Copy this entire file
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display the cart page
     * Works for both guests and authenticated users
     */
    public function index()
    {
        return view('user.cart.index');
    }

    /**
     * Get cart count (not used since localStorage handles it)
     */
    public function count()
    {
        return response()->json(['count' => 0]);
    }

    /**
     * Get product details for items in cart
     * This is called by the cart page to display full product info
     */
    public function getProducts(Request $request)
    {
        $productIds = $request->input('product_ids', []);
        
        if (empty($productIds)) {
            return response()->json([]);
        }
        
        $products = DB::table('products')
            ->join('branches', 'products.branch_id', '=', 'branches.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereIn('products.id', $productIds)
            ->select(
                'products.id',
                'products.name',
                'products.price',
                'products.image',
                'products.is_available',
                'products.branch_id',
                'branches.name as branch_name',
                'categories.name as category_name'
            )
            ->get();
            
        return response()->json($products);
    }

    /**
     * Add item to cart
     * Just returns success - actual cart is handled by JavaScript/localStorage
     */
    public function add(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Item added to cart'
        ]);
    }

    /**
     * Update cart item quantity
     * Just returns success - actual cart is handled by JavaScript/localStorage
     */
    public function update(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Cart updated'
        ]);
    }

    /**
     * Remove item from cart
     * Just returns success - actual cart is handled by JavaScript/localStorage
     */
    public function remove(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart'
        ]);
    }

    /**
     * Clear entire cart
     * Just returns success - actual cart is handled by JavaScript/localStorage
     */
    public function clear(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Cart cleared'
        ]);
    }

    /**
     * Sync cart (for when guest logs in)
     * Just returns success - actual cart is handled by JavaScript/localStorage
     */
    public function sync(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Cart synced'
        ]);
    }
}