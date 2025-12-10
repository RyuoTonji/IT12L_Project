<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Get or create cart for current user/guest
     */
    private function getCart()
    {
        if (auth()->check()) {
            // Logged-in user
            return Cart::getOrCreate(auth()->id(), null);
        } else {
            // Guest user - use session ID
            $sessionId = session()->getId();
            return Cart::getOrCreate(null, $sessionId);
        }
    }

    /**
     * Display cart page
     */
    public function index()
    {
        $cart = $this->getCart();
        $items = $cart->items()->with('product.branch')->get();
        
        return view('user.cart.index', compact('cart', 'items'));
    }

    /**
     * Get cart count (for navbar badge)
     */
    public function count()
    {
        $cart = $this->getCart();
        return response()->json([
            'count' => $cart->getItemCount()
        ]);
    }

    /**
     * Get product details (your existing endpoint - keep it)
     */
    public function getProducts(Request $request)
    {
        try {
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
                    'branches.address as branch_address',
                    'categories.name as category_name'
                )
                ->get();

            return response()->json($products);
            
        } catch (\Exception $e) {
            Log::error('Error fetching cart products', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to fetch products'
            ], 500);
        }
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'integer|min:1|max:99'
            ]);

            $cart = $this->getCart();
            $quantity = $request->input('quantity', 1);
            
            $cart->addItem($request->product_id, $quantity);
            
            Log::info('Item added to cart', [
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $quantity
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart',
                'cart_count' => $cart->getItemCount()
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding to cart', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart'
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|integer',
                'quantity' => 'required|integer|min:0|max:99'
            ]);

            $cart = $this->getCart();
            $cart->updateItem($request->product_id, $request->quantity);
            
            return response()->json([
                'success' => true,
                'message' => 'Cart updated',
                'cart_count' => $cart->getItemCount()
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating cart', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart'
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|integer'
            ]);

            $cart = $this->getCart();
            $cart->removeItem($request->product_id);
            
            return response()->json([
                'success' => true,
                'message' => 'Item removed',
                'cart_count' => $cart->getItemCount()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item'
            ], 500);
        }
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request)
    {
        try {
            $cart = $this->getCart();
            $cart->clear();
            
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart'
            ], 500);
        }
    }

    /**
     * Sync localStorage cart to database (called on page load)
     */
    public function sync(Request $request)
    {
        try {
            $localCart = $request->input('items', []);
            
            if (empty($localCart)) {
                return response()->json([
                    'success' => true,
                    'cart_count' => 0
                ]);
            }

            $cart = $this->getCart();
            
            // Clear existing cart first
            $cart->clear();
            
            // Add items from localStorage
            foreach ($localCart as $item) {
                if (isset($item['id']) && isset($item['quantity'])) {
                    $cart->addItem($item['id'], $item['quantity']);
                }
            }
            
            Log::info('Cart synced from localStorage', [
                'cart_id' => $cart->id,
                'items_count' => count($localCart)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Cart synced',
                'cart_count' => $cart->getItemCount()
            ]);

        } catch (\Exception $e) {
            Log::error('Error syncing cart', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync cart'
            ], 500);
        }
    }

    /**
     * NEW: Get database cart after login (for syncing localStorage)
     * Called by frontend after successful login to merge guest cart with user cart
     */
    public function syncAfterLogin(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ], 401);
            }

            // Get the authenticated user's cart from database
            $cart = Cart::getOrCreate(auth()->id(), null);
            
            // Load cart items with full product details
            $items = $cart->items()
                ->with(['product' => function($query) {
                    $query->select('id', 'name', 'price', 'image', 'branch_id', 'is_available')
                        ->with('branch:id,name');
                }])
                ->get();

            // Format cart items for frontend (matching localStorage structure)
            $formattedItems = $items->map(function($item) {
                if (!$item->product) {
                    return null; // Skip if product was deleted
                }

                return [
                    'id' => $item->product_id,
                    'name' => $item->product->name,
                    'price' => floatval($item->product->price),
                    'image' => $item->product->image,
                    'branch_id' => $item->product->branch_id,
                    'branch_name' => $item->product->branch->name ?? 'Unknown',
                    'is_available' => (bool)$item->product->is_available,
                    'quantity' => $item->quantity
                ];
            })->filter()->values(); // Remove nulls and reindex

            Log::info('Cart synced after login', [
                'user_id' => auth()->id(),
                'items_count' => $formattedItems->count()
            ]);

            return response()->json([
                'success' => true,
                'cart' => $formattedItems,
                'cart_count' => $cart->getItemCount()
            ]);

        } catch (\Exception $e) {
            Log::error('Error syncing cart after login', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync cart'
            ], 500);
        }
    }
}