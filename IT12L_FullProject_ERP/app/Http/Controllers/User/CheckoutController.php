<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\PayMongoService;
use App\Mail\OrderAlert;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    /**
     *  Session-based cart (same for guest and authenticated)
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
            'user_id' => Auth::id(),
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
        $items = collect($cart)->map(function ($cartItem) use ($products) {
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
        $total = $items->sum(function ($item) {
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
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson()
        ]);

        //  REMOVED: address validation (pickup only)
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|string|in:cash,gcash,grab_pay,qr_ph',
        ]);

        // Get cart from session
        $cart = session('checkout_cart', []);

        // Fallback: If session cart is empty, try to get from database Cart model
        if (empty($cart)) {
            Log::info('Session cart empty, trying database cart fallback');
            $cartModel = $this->getCart();
            if ($cartModel && $cartModel->items()->count() > 0) {
                $cart = $cartModel->items()->with('product')->get()->map(function ($item) {
                    return [
                        'id' => $item->product_id,
                        'quantity' => $item->quantity,
                    ];
                })->toArray();
                Log::info('Cart retrieved from database', ['items_count' => count($cart)]);
            }
        }

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

            // Create order using Eloquent (NO ADDRESS FIELD)
            $order = Order::create([
                'user_id' => Auth::id(),
                'branch_id' => $branchId,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'notes' => $request->notes,
            ]);

            Log::info('Order created', ['order_id' => $order->id]);

            // Handle PayMongo Source Creation if needed (GCash or GrabPay)
            $checkoutUrl = null;
            $sourceId = null;
            if (in_array($request->payment_method, ['gcash', 'grab_pay', 'qr_ph'])) {
                $paymongoService = new PayMongoService();
                $description = "Payment for Order #{$order->id} at BBQ Lagao";
                $paymentType = $request->payment_method; // 'gcash', 'grab_pay', or 'qr_ph'

                $billingData = [
                    'name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'email' => Auth::user()->email ?? '' // Fallback to user email if logged in
                ];

                $sourceResult = $paymongoService->createSource($total, $description, $order->id, $paymentType, $billingData);

                if ($sourceResult && isset($sourceResult['data'])) {
                    $sourceId = $sourceResult['data']['id'];

                    // Handle difference between Source (GCash/Maya) and Payment Intent (QRPh)
                    if (isset($sourceResult['is_payment_intent'])) {
                        $checkoutUrl = $sourceResult['data']['attributes']['redirect']['checkout_url']; // This is the QR string
                    } else {
                        $checkoutUrl = $sourceResult['data']['attributes']['redirect']['checkout_url'];
                    }

                    // Update order with source ID using Eloquent
                    $order->update([
                        'paymongo_source_id' => $sourceId,
                        'payment_status' => 'pending_payment'
                    ]);

                    Log::info('PayMongo payment initiated', [
                        'order_id' => $order->id,
                        'id' => $sourceId,
                        'type' => $paymentType,
                        'is_pi' => $sourceResult['is_payment_intent'] ?? false
                    ]);
                } else {
                    throw new \Exception('Failed to create PayMongo payment request.');
                }
            }

            // Create order items using Eloquent
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_image' => $item['product_image'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            Log::info('Order items created', ['order_id' => $order->id, 'items_count' => count($orderItems)]);

            // ✅ CLEAR CART AFTER SUCCESSFUL ORDER
            $this->clearCartAfterCheckout($order->id);

            DB::commit();

            Log::info('Order completed successfully', ['order_id' => $order->id]);

            //  AJAX Response
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully!',
                    'order_id' => $order->id,
                    'payment_method' => $request->payment_method,
                    'checkout_url' => $checkoutUrl,
                    'source_id' => $sourceId,
                    'redirect_url' => route('checkout.confirm', ['order_id' => $order->id])
                ]);
            }

            //  Regular redirect for non-AJAX
            return redirect()->route('checkout.confirm', ['order_id' => $order->id])
                ->with('success', 'Pickup order placed successfully!')
                ->with('clear_cart', true);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
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
                    'user_id' => Auth::id(),
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

        // Fetch the order using Eloquent with relationships
        $query = Order::with('branch')->where('id', $orderId);

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            // For guests, check if the order_id is in session or has no user_id (less secure but works for now)
            // Ideally we'd store the created order IDs in session
            $query->whereNull('user_id');
        }

        $order = $query->first();

        if (!$order) {
            Log::warning('Order not found or unauthorized', [
                'order_id' => $orderId,
                'user_id' => Auth::id()
            ]);
            return redirect()->route('home')->with('error', 'Order not found!');
        }

        // Add branch details for view compatibility
        $order->branch_name = $order->branch->name;
        $order->branch_address = $order->branch->address;

        return view('user.checkout.confirm', compact('order'));
    }

    /**
     * ✅ Poll payment status
     */
    public function checkPaymentStatus(Request $request)
    {
        $orderId = $request->order_id;
        $order = Order::find($orderId);

        if (!$order || !$order->paymongo_source_id) {
            return response()->json(['success' => false, 'message' => 'Order not found or no payment source.'], 404);
        }

        // If already paid, return success
        if ($order->payment_status === 'paid') {
            return response()->json(['success' => true, 'status' => 'paid']);
        }

        $paymongoService = new PayMongoService();
        $paymongoResponse = $paymongoService->getSourceStatus($order->paymongo_source_id);

        if ($paymongoResponse && isset($paymongoResponse['data'])) {
            $status = $paymongoResponse['data']['attributes']['status'];
            $id = $paymongoResponse['data']['id'];

            // Handle Payment Intent (QRPh)
            if (str_starts_with($id, 'pi_')) {
                if ($status === 'succeeded') {
                    $this->markOrderAsPaid($order);
                    return response()->json(['success' => true, 'status' => 'paid']);
                }
                return response()->json(['success' => true, 'status' => $status]);
            }

            // Handle Source (GCash/PayMaya)
            if ($status === 'chargeable') {
                $payment = $paymongoService->createPayment(
                    $order->total_amount,
                    $order->paymongo_source_id,
                    "Payment for Order #{$orderId}"
                );

                if ($payment && isset($payment['data'])) {
                    $this->markOrderAsPaid($order);
                    return response()->json(['success' => true, 'status' => 'paid']);
                }
            }

            return response()->json(['success' => true, 'status' => $status]);
        }

        return response()->json(['success' => false, 'message' => 'Could not verify payment status.']);
    }

    /**
     * Helper to mark order as paid and notify admin
     */
    private function markOrderAsPaid($order)
    {
        $order->update([
            'payment_status' => 'paid',
            'status' => 'confirmed'
        ]);

        try {
            $adminEmail = config('mail.from.address');
            Mail::to($adminEmail)->send(new OrderAlert($order));
        } catch (\Exception $e) {
            Log::error('Failed to send order alert email', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }
    }
}