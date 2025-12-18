<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * ✅ Session-based cart (same for guest and authenticated)
     *  FIXED: Session-based cart (same for guest and authenticated)
     */
    private function getCart()
    {
        $sessionId = session()->getId();
        return Cart::getOrCreate(auth()->id(), $sessionId);
    }

    public function index(Request $request)
    {
        Log::info('Checkout Index accessed', [
            'method' => $request->method(),
            'cart_items_input' => $request->input('cart_items'),
            'session_cart' => session('checkout_cart'),
            'user_id' => auth()->id(),
            'session_id' => session()->getId()
        ]);
        
        $user = Auth::user();
        
        // Get cart items from request (sent via POST from the cart page)
        $cartData = $request->input('cart_items');
        
        if ($cartData) {
            // Decode if it's JSON string
            $cart = is_string($cartData) ? json_decode($cartData, true) : $cartData;
            
            // Validate cart data
            if (!is_array($cart) || empty($cart)) {
                Log::warning('Invalid cart data received', ['cart_data' => $cartData]);
                return redirect()->route('cart.index')->with('error', 'Invalid cart data!');
            }
            
            // Store in session for later use
            session(['checkout_cart' => $cart]);
            Log::info('Cart stored in session', ['cart' => $cart]);
        } else {
            // Try to get from session
            $cart = session('checkout_cart', []);
            Log::info('Cart retrieved from session', ['cart' => $cart]);
        }
        
        // If cart is empty, redirect back to cart
        if (empty($cart)) {
            Log::warning('Checkout accessed with empty cart');
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }
        
        // Extract product IDs from cart
        $productIds = array_column($cart, 'id');
        
        // Fetch products from database
        $products = DB::table('products')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');
        
        if ($products->isEmpty()) {
            Log::error('No products found in database', ['product_ids' => $productIds]);
            return redirect()->route('cart.index')->with('error', 'Invalid cart items!');
        }
        
        // Get branch from first product
        $firstProduct = $products->first();
        $branch = DB::table('branches')->where('id', $firstProduct->branch_id)->first();
        
        if (!$branch) {
            Log::error('Branch not found', ['branch_id' => $firstProduct->branch_id]);
            return redirect()->route('cart.index')->with('error', 'Branch not found!');
        }
        
        // Build cart items with product details
        $items = collect($cart)->map(function($cartItem) use ($products) {
            $productId = $cartItem['id'];
            
            if (!isset($products[$productId])) {
                return null;
            }
            
            return (object) [
                'product' => $products[$productId],
                'quantity' => $cartItem['quantity']
            ];
        })->filter();
        
        // Calculate total
        $total = $items->sum(function($item) {
            return $item->product->price * $item->quantity;
        });
        
        Log::info('Checkout page rendered', [
            'items_count' => $items->count(),
            'total' => $total
        ]);
        
        return view('user.checkout.index', compact('branch', 'items', 'total', 'user'));
    }

    public function process(Request $request)
    {
        Log::info('Checkout process started', [
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'is_ajax' => $request->ajax()
        ]);

        // ✅ REMOVED: address validation (pickup only)
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Get cart from session
        $cart = session('checkout_cart', []);
        
        if (empty($cart)) {
            Log::warning('Process checkout with empty cart');
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty!'
                ], 400);
            }
            
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Extract product IDs
        $productIds = array_column($cart, 'id');
        
        // Fetch products
        $products = DB::table('products')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        if ($products->isEmpty()) {
            Log::error('No products found during checkout process', ['product_ids' => $productIds]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid cart items!'
                ], 400);
            }
            
            return redirect()->route('cart.index')->with('error', 'Invalid cart items!');
        }

        // Calculate total and prepare order items
        $total = 0;
        $orderItems = [];
        $branchId = null;
        
        foreach ($cart as $cartItem) {
            $productId = $cartItem['id'];
            $quantity = $cartItem['quantity'];
            
            if (!isset($products[$productId])) {
                Log::warning('Product not found in checkout', ['product_id' => $productId]);
                continue;
            }
            
            $product = $products[$productId];
            
            // Check if product is available
            if (!$product->is_available) {
                Log::warning('Unavailable product in cart', ['product_id' => $productId]);
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Product '{$product->name}' is no longer available!"
                    ], 400);
                }
                
                return redirect()->route('cart.index')
                    ->with('error', "Product '{$product->name}' is no longer available!");
            }
            
            // Set branch_id from first product
            if ($branchId === null) {
                $branchId = $product->branch_id;
            }
            
            $subtotal = $product->price * $quantity;
            $total += $subtotal;
            
            $orderItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_image' => $product->image,
                'quantity' => $quantity,
                'price' => $product->price,
                'subtotal' => $subtotal,
            ];
        }

        if (empty($orderItems)) {
            Log::error('No valid order items after processing cart');
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid items in cart!'
                ], 400);
            }
            
            return redirect()->route('cart.index')->with('error', 'No valid items in cart!');
        }

        try {
            DB::beginTransaction();

            // Create order (NO ADDRESS FIELD)
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => auth()->id(),
                'branch_id' => $branchId,
                'total_amount' => $total,
                'status' => 'pending',
                // ✅ REMOVED: address field
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Order created', ['order_id' => $orderId]);

            // Create order items
            foreach ($orderItems as $item) {
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_image' => $item['product_image'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Log::info('Order items created', ['order_id' => $orderId, 'items_count' => count($orderItems)]);

            //  CLEAR CART AFTER SUCCESSFUL ORDER
            $this->clearCartAfterCheckout($orderId);

            DB::commit();

            Log::info('Order completed successfully', ['order_id' => $orderId]);
            
            // ✅ AJAX Response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pickup order placed successfully!',
                    'order_id' => $orderId,
                    'redirect_url' => route('checkout.confirm', ['order_id' => $orderId])
                ]);
            }
            
            // ✅ Regular redirect for non-AJAX
            //  Redirect with clear_cart flag for frontend
            return redirect()->route('checkout.confirm', ['order_id' => $orderId])
                ->with('success', 'Pickup order placed successfully!')
                ->with('clear_cart', true);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to place order. Please try again.',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('cart.index')
                ->with('error', 'Failed to place order. Please try again.');
        }
    }
    
    /**
     *  CLEAR CART AFTER SUCCESSFUL CHECKOUT
     */
    private function clearCartAfterCheckout($orderId)
    {
        try {
            $sessionId = session()->getId();
            
            // 1. Clear database cart
            $cartModel = $this->getCart();
            if ($cartModel) {
                $itemsCount = $cartModel->items()->count();
                $cartModel->items()->delete();
                $cartModel->delete();
                
                Log::info('Database cart cleared after checkout', [
                    'session_id' => $sessionId,
                    'user_id' => auth()->id(),
                    'order_id' => $orderId,
                    'items_cleared' => $itemsCount
                ]);
            }
            
            // 2. Clear session cart
            session()->forget('checkout_cart');
            
            // 3. Set flash message to signal frontend
            session()->flash('clear_cart', true);
            
            Log::info('Cart clearing completed', [
                'order_id' => $orderId,
                'user_id' => auth()->id()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error clearing cart after checkout', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
                'user_id' => auth()->id()
            ]);
        }
    }
    
    public function confirm(Request $request)
    {
        $orderId = $request->query('order_id');
        
        if (!$orderId) {
            Log::warning('Confirm page accessed without order_id');
            return redirect()->route('home');
        }
        
        // Fetch the order to display details
        $order = DB::table('orders')
            ->join('branches', 'orders.branch_id', '=', 'branches.id')
            ->where('orders.id', $orderId)
            ->where('orders.user_id', auth()->id())
            ->select('orders.*', 'branches.name as branch_name', 'branches.address as branch_address')
            ->first();
        
        if (!$order) {
            Log::warning('Order not found or unauthorized', [
                'order_id' => $orderId,
                'user_id' => auth()->id()
            ]);
            return redirect()->route('home')->with('error', 'Order not found!');
        }
        
        Log::info('Order confirmation page displayed', [
            'order_id' => $orderId,
            'clear_cart_flag' => session('clear_cart')
        ]);
        
        return view('user.checkout.confirm', compact('order'));
    }
}