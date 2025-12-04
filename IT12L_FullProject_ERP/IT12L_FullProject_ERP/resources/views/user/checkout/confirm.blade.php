@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    
                    <h2 class="mb-3">Order Placed Successfully!</h2>
                    
                    <p class="text-muted mb-4">
                        Thank you for your order. Your order has been received and is being processed.
                    </p>
                    
                    <div class="alert alert-info">
                        <strong>Order #{{ $order->id }}</strong>
                        <br>
                        <small>Branch: {{ $order->branch_name }}</small>
                        <br>
                        <small>Total: ₱{{ number_format($order->total_amount, 2) }}</small>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> View Order Details
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> View All Orders
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Clear the cart when order is confirmed
(function() {
    console.log('=== Order Confirmation - Clearing Cart ===');
    
    // Get the cart storage key using the same function from cart.js
    function getCartStorageKey() {
        const userIdMeta = document.querySelector('meta[name="user-id"]');
        const userId = userIdMeta ? userIdMeta.content : null;
        
        if (userId && userId !== '') {
            return `cart_user_${userId}`;
        } else {
            return 'cart_guest';
        }
    }
    
    // Log current localStorage before clearing
    console.log('localStorage before clearing:', { ...localStorage });
    
    // Get the correct cart key
    const cartKey = getCartStorageKey();
    console.log('Cart storage key to clear:', cartKey);
    
    // Clear the cart
    if (localStorage.getItem(cartKey)) {
        console.log('Found cart data:', localStorage.getItem(cartKey));
        localStorage.removeItem(cartKey);
        console.log('✅ Cart cleared successfully!');
    } else {
        console.log('⚠️ No cart found in localStorage');
    }
    
    // Also clear any other possible cart keys (safety measure)
    const allPossibleKeys = [
        'cart',
        'cart_guest',
        'Cart',
        'shopping_cart'
    ];
    
    // Clear user-specific carts (in case of multiple users)
    Object.keys(localStorage).forEach(key => {
        if (key.startsWith('cart_user_')) {
            console.log('Clearing additional cart key:', key);
            localStorage.removeItem(key);
        }
    });
    
    allPossibleKeys.forEach(key => {
        if (localStorage.getItem(key)) {
            console.log('Clearing additional cart key:', key);
            localStorage.removeItem(key);
        }
    });
    
    // Log after clearing
    console.log('localStorage after clearing:', { ...localStorage });
    
    // Update cart count using the function from cart.js
    if (typeof updateCartCount === 'function') {
        console.log('Calling updateCartCount()...');
        updateCartCount();
    } else {
        console.log('updateCartCount() not available, manually updating...');
        manuallyUpdateCartCount();
    }
    
    // Dispatch custom event to notify cart.js
    window.dispatchEvent(new Event('cartCleared'));
    window.dispatchEvent(new Event('storage'));
    
    console.log('=== Cart clearing complete ===');
})();

// Fallback function to manually update cart count
function manuallyUpdateCartCount() {
    const selectors = [
        '.cart-count',
        '#cart-count',
        '.badge.cart-count',
        '[data-cart-count]',
        '[class*="cart-count"]'
    ];
    
    selectors.forEach(selector => {
        document.querySelectorAll(selector).forEach(element => {
            console.log('Updating cart count element:', element);
            element.textContent = '0';
            element.innerText = '0';
            
            if (element.classList.contains('badge')) {
                element.style.display = 'none';
            }
        });
    });
}
</script>
@endpush
@endsection