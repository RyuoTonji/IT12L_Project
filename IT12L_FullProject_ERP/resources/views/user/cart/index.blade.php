@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">
        <i class="fas fa-shopping-cart"></i> Shopping Cart
    </h2>

    <!-- Loading State -->
    <div id="loading-cart" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3">Loading your cart...</p>
    </div>

    <!-- Empty Cart -->
    <div id="empty-cart" class="d-none">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                <h4>Your cart is empty</h4>
                <p class="text-muted">Start adding some delicious items!</p>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="fas fa-utensils"></i> Browse Menu
                </a>
            </div>
        </div>
    </div>

    <!-- Cart with Items -->
    <div id="cart-content" class="d-none">
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="cart-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items-body">
                                    <!-- Items will be inserted here by cart.js -->
                                </tbody>
                            </table>
                        </div>

                        <div class="text-end">
                            <button class="btn btn-outline-danger" id="clear-cart">
                                <i class="fas fa-trash"></i> Clear Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <strong id="cart-total">₱0.00</strong>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-primary fs-4" id="cart-total-final">₱0.00</strong>
                        </div>

                        <div id="checkout-section">
                            @auth
                                @if(!auth()->user()->is_admin)
                                    <form action="{{ route('checkout.index') }}" method="POST" id="checkout-form">
                                        @csrf
                                        <input type="hidden" name="cart_items" id="cart_items_hidden" value="">
                                        <button type="submit" class="btn btn-primary w-100 btn-lg" id="checkout-btn">
                                            <i class="fas fa-credit-card"></i> Proceed to Checkout
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle"></i> Admins cannot checkout
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle"></i> Please login to checkout
                                </div>
                                <a href="{{ route('login') }}?redirect=checkout" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-sign-in-alt"></i> Login to Checkout
                                </a>
                                <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="fas fa-user-plus"></i> Register New Account
                                </a>
                            @endauth
                        </div>
                        
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// =============================================================================
// CART PAGE - NO API VERSION
// This script relies entirely on cart.js which stores full product data in localStorage
// =============================================================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('[Cart Page] Initializing...');
    
    // Wait for cart.js to be loaded
    if (typeof loadCartPage === 'function') {
        console.log('[Cart Page] cart.js detected, loading cart...');
        loadCartPage();
    } else {
        console.error('[Cart Page] ERROR: cart.js not loaded! loadCartPage() function not found');
        showEmptyCart();
    }
    
    // Setup checkout form
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            console.log('[Cart Page] Checkout form submitted');
            
            // Get cart from localStorage (cart.js provides this function)
            if (typeof getCart !== 'function') {
                console.error('[Cart Page] ERROR: getCart() function not found');
                alert('Cart system error. Please refresh the page.');
                return false;
            }
            
            const cart = getCart();
            console.log('[Cart Page] Cart data for checkout:', cart);
            
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return false;
            }
            
            // Set cart data in hidden input
            const cartJson = JSON.stringify(cart);
            document.getElementById('cart_items_hidden').value = cartJson;
            
            console.log('[Cart Page] Submitting checkout with cart data');
            
            // Submit the form
            this.submit();
        });
    }
});

// Helper function to show empty cart (fallback)
function showEmptyCart() {
    const loadingEl = document.getElementById('loading-cart');
    const emptyEl = document.getElementById('empty-cart');
    const contentEl = document.getElementById('cart-content');
    
    if (loadingEl) loadingEl.classList.add('d-none');
    if (emptyEl) emptyEl.classList.remove('d-none');
    if (contentEl) contentEl.classList.add('d-none');
    
    console.log('[Cart Page] Empty cart state displayed');
}
</script>
@endpush