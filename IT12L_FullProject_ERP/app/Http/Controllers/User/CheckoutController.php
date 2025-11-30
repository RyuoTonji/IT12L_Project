<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Debug: Log what we receive
        Log::info('Checkout Index - Request Method: ' . $request->method());
        Log::info('Checkout Index - Cart Items Input: ' . $request->input('cart_items'));
        
        // Get the authenticated user
        $user = Auth::user();
        
        // Get cart items from request (sent via POST from the cart page)
        $cartData = $request->input('cart_items');
        
        if ($cartData) {
            // Decode if it's JSON string
            $cart = is_string($cartData) ? json_decode($cartData, true) : $cartData;
            // Store in session for later use
            session(['checkout_cart' => $cart]);
            Log::info('Checkout Index - Cart stored in session', ['cart' => $cart]);
        } else {
            // Try to get from session
            $cart = session('checkout_cart', []);
            Log::info('Checkout Index - Cart from session', ['cart' => $cart]);
        }
        
        // If cart is empty, redirect back to cart
        if (empty($cart)) {
            Log::warning('Checkout Index - Cart is empty, redirecting to cart page');
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }
        
        // Extract product IDs from cart
        $productIds = array_column($cart, 'id');
        
        // Fetch products from database
        $products = DB::table('products')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');
        
        // If no products found, redirect
        if ($products->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Invalid cart items!');
        }
        
        // Get branch from first product
        $firstProduct = $products->first();
        $branch = DB::table('branches')->where('id', $firstProduct->branch_id)->first();
        
        if (!$branch) {
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
        
        return view('user.checkout.index', compact('branch', 'items', 'total', 'user'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
        ]);

        // Get cart from session (stored during index method)
        $cart = session('checkout_cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Extract product IDs
        $productIds = array_column($cart, 'id');
        
        // Fetch products
        $products = DB::table('products')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        // Calculate total and prepare order items
        $total = 0;
        $orderItems = [];
        $branchId = null;
        
        foreach ($cart as $cartItem) {
            $productId = $cartItem['id'];
            $quantity = $cartItem['quantity'];
            
            if (!isset($products[$productId])) {
                continue;
            }
            
            $product = $products[$productId];
            
            // Set branch_id from first product
            if ($branchId === null) {
                $branchId = $product->branch_id;
            }
            
            $itemTotal = $product->price * $quantity;
            $total += $itemTotal;
            
            $orderItems[] = [
                'product_name' => $product->name,
                'quantity' => $quantity,
                'price' => $product->price,
            ];
        }

        // Create order
        $orderId = DB::table('orders')->insertGetId([
            'user_id' => auth()->id(),
            'branch_id' => $branchId,
            'total_amount' => $total,
            'status' => 'pending',
            'address' => $request->address,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'notes' => $request->notes ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create order items
        foreach ($orderItems as $item) {
            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Clear the checkout cart from session
        session()->forget('checkout_cart');
        
        // Add a flag to indicate cart should be cleared
        session()->flash('clear_cart', true);

        // Redirect to order confirmation page (this will clear the localStorage cart)
        return redirect()->route('checkout.confirm', ['order_id' => $orderId]);
    }
    
    public function confirm(Request $request)
    {
        $orderId = $request->query('order_id');
        
        if (!$orderId) {
            return redirect()->route('home');
        }
        
        // Fetch the order to display details
        $order = DB::table('orders')
            ->join('branches', 'orders.branch_id', '=', 'branches.id')
            ->where('orders.id', $orderId)
            ->where('orders.user_id', auth()->id())
            ->select('orders.*', 'branches.name as branch_name')
            ->first();
        
        if (!$order) {
            return redirect()->route('home');
        }
        
        return view('user.checkout.confirm', compact('order'));
    }
}