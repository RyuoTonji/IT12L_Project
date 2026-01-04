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
     * ✅ SIMPLIFIED: localStorage-based cart (no database needed for cart storage)
     * Database Cart model is kept only for backward compatibility
     */
    private function getCart()
    {
        $sessionId = session()->getId();
        
        // ALWAYS use session_id as primary identifier
        $cart = Cart::getOrCreate(auth()->id(), $sessionId);
        
        Log::info('Cart accessed', [
            'cart_id' => $cart ? $cart->id : null,
            'session_id' => $sessionId,
            'user_id' => auth()->id() ?? 'guest'
        ]);
        
        return $cart;
    }

    /**
     * Display cart page
     */
    public function index()
    {
        // Cart page - frontend (cart.js) handles everything
        return view('user.cart.index');
    }

    /**
     * Get cart count (for navbar badge)
     */
    public function count()
    {
        $cart = $this->getCart();
        return response()->json([
            'count' => $cart ? $cart->getItemCount() : 0
        ]);
    }

    /**
     * Get product details (API endpoint for cart.js)
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
     * ✅ SIMPLIFIED: These methods are kept for API compatibility
     * But the actual cart logic is handled by localStorage (cart.js)
     */
    
    public function add(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'integer|min:1|max:99'
            ]);

            $cart = $this->getCart();
            
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to create cart'
                ], 500);
            }
            
            $quantity = $request->input('quantity', 1);
            
            $cart->addItem($request->product_id, $quantity);
            
            Log::info('Item added to cart', [
                'cart_id' => $cart->id,
                'session_id' => $cart->session_id,
                'product_id' => $request->product_id,
                'quantity' => $quantity,
                'user_id' => auth()->id() ?? 'guest'
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

    public function update(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|integer',
                'quantity' => 'required|integer|min:0|max:99'
            ]);

            $cart = $this->getCart();
            
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found'
                ], 404);
            }
            
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

    public function remove(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|integer'
            ]);

            $cart = $this->getCart();
            
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found'
                ], 404);
            }
            
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

    public function clear(Request $request)
    {
        try {
            $cart = $this->getCart();
            
            if (!$cart) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cart already empty'
                ]);
            }
            
            $cart->clear();
            
            Log::info('Cart cleared', [
                'cart_id' => $cart->id,
                'session_id' => $cart->session_id,
                'user_id' => auth()->id() ?? 'guest'
            ]);
            
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
     * ✅ REMOVED: syncAfterLogin() - Not needed with localStorage approach
     * The migration happens entirely in the frontend (cart.js + app.blade.php)
     */

    /**
     * ✅ OPTIONAL: Sync localStorage cart to database (if you want database backup)
     * This is useful if you want to store cart in DB for analytics or recovery
     */
    public function sync(Request $request)
    {
        try {
            $localCart = $request->input('items', []);
            
            Log::info('Cart sync request received', [
                'items_count' => count($localCart),
                'current_session_id' => session()->getId(),
                'user_id' => auth()->id(),
                'items' => $localCart
            ]);
            
            if (empty($localCart)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No items to sync',
                    'cart_count' => 0,
                    'cart' => []
                ]);
            }

            // Get current cart (will create if doesn't exist)
            $cart = $this->getCart();
            
            if (!$cart) {
                Log::error('Unable to create cart during sync');
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to create cart'
                ], 500);
            }
            
            // Clear existing items (fresh sync)
            $cart->clear();
            
            // Add items from localStorage
            $syncedCount = 0;
            foreach ($localCart as $item) {
                $productId = $item['id'] ?? $item['product_id'] ?? null;
                $quantity = $item['quantity'] ?? 1;
                
                if (!$productId) {
                    Log::warning('Invalid item in sync request', ['item' => $item]);
                    continue;
                }
                
                // Verify product exists and is available
                $product = Product::find($productId);
                if (!$product || !$product->is_available) {
                    Log::warning('Product not found or unavailable during sync', [
                        'product_id' => $productId
                    ]);
                    continue;
                }
                
                // Add item to database cart
                $cart->addItem($productId, $quantity);
                $syncedCount++;
            }
            
            Log::info('Cart synced successfully', [
                'cart_id' => $cart->id,
                'session_id' => $cart->session_id,
                'items_synced' => $syncedCount,
                'total_items' => $cart->getItemCount(),
                'user_id' => auth()->id()
            ]);
            
            // Return updated cart data
            $cartItems = $cart->items()->with('product')->get()->map(function($item) {
                return [
                    'id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'image' => $item->product->image
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => "Cart synced successfully ({$syncedCount} items)",
                'cart_count' => $cart->getItemCount(),
                'cart' => $cartItems
            ]);

        } catch (\Exception $e) {
            Log::error('Error syncing cart', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync cart: ' . $e->getMessage()
            ], 500);
        }
    }
}