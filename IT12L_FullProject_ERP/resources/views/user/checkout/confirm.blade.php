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

                    <h2 class="mb-3">Pickup Order Placed Successfully!</h2>

                    <p class="text-muted mb-4">
                        Thank you for your order. Your order is being prepared and will be ready for pickup soon.
                    </p>

                    <div class="alert alert-info">
                        <strong>Order #{{ $order->id }}</strong>
                        <br>
                        <small><i class="fas fa-store"></i> Pickup Branch: {{ $order->branch_name }}</small>
                        <br>
                        <small><i class="fas fa-map-marker-alt"></i> {{ $order->branch_address }}</small>
                        <br>
                        <small><i class="fas fa-receipt"></i> Total: ₱{{ number_format($order->total_amount, 2) }}</small>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i>
                        <strong>Important Pickup Instructions:</strong>
                        <ul class="text-start mt-2 mb-0">
                            <li>Order will be ready in 15-20 minutes</li>
                            <li>Please show order ID when picking up</li>
                            <li>Payment will be collected at pickup (Cash/Gcash)</li>
                            <li>Call the branch if you need assistance: {{ $order->branch_phone ?? 'See branch details' }}</li>
                        </ul>
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
@endsection

@push('scripts')
<script>
    /**
     * ============================================================================
     * ORDER CONFIRMATION - CART CLEARING SCRIPT
     * Version: 2.0 (Pickup Only - Compatible with cart.js v7.0 SESSION-BASED)
     * 
     * Purpose: Clear cart after successful pickup order placement
     * Trigger: When session('clear_cart') is true
     * ============================================================================
     */

    (function() {
        'use strict';

        console.log('📄 Pickup order confirmation page script loaded');

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('clear_cart'))
            console.log('🎉 Pickup order confirmed successfully');
            console.log('🛒 Clearing cart after checkout...');

            // Wait for cart.js to fully initialize
            setTimeout(function() {
                performCartClearing();
            }, 250);
            @else
            console.log('ℹ️ No cart clearing needed (no session flag)');
            @endif
        });

        /**
         * Main cart clearing function
         */
        function performCartClearing() {
            console.log('=== Starting Cart Clearing Process ===');

            try {
                // PRIORITY 1: Use cart.js clearCartAfterCheckout() if available
                if (typeof window.clearCartAfterCheckout === 'function') {
                    console.log('✓ Using cart.js clearCartAfterCheckout() function');
                    window.clearCartAfterCheckout();
                    console.log('✅ Cart cleared via cart.js (method 1)');
                    return;
                }

                // PRIORITY 2: Use cart.js clearCart() as fallback
                if (typeof window.clearCart === 'function') {
                    console.log('✓ Using cart.js clearCart() function');
                    window.clearCart();
                    console.log('✅ Cart cleared via cart.js (method 2)');
                    return;
                }

                // PRIORITY 3: Manual clearing (last resort)
                console.warn('⚠️ cart.js functions not available - using manual clearing');
                manualClearCart();

            } catch (error) {
                console.error('❌ Error during cart clearing:', error);
                try {
                    manualClearCart();
                } catch (fallbackError) {
                    console.error('❌ Manual clearing also failed:', fallbackError);
                    alert('Warning: Cart may not have been cleared. Please refresh the page.');
                }
            }
        }

        /**
         * Manual cart clearing (fallback method)
         */
        function manualClearCart() {
            console.log('--- Manual Cart Clearing Started ---');

            try {
                let sessionId = getSessionId();

                if (sessionId) {
                    const cartKey = `cart_session_${sessionId}`;
                    console.log('Clearing cart with key:', cartKey);
                    localStorage.removeItem(cartKey);
                    console.log('✅ Session cart cleared:', cartKey);
                } else {
                    console.warn('⚠️ Could not determine session ID');
                }

                // Clean up legacy cart formats
                cleanupLegacyCarts();

                // Update UI
                manualUpdateCartCount();

                // Trigger storage event for cross-tab sync
                window.dispatchEvent(new Event('storage'));
                window.dispatchEvent(new CustomEvent('cartCleared'));

                console.log('✅ Manual cart clearing complete');

            } catch (error) {
                console.error('❌ Error in manual cart clearing:', error);
                throw error;
            }
        }

        /**
         * Get session ID using multiple fallback methods
         */
        function getSessionId() {
            // Method 1: Check meta tag
            const sessionMeta = document.querySelector('meta[name="session-id"]');
            if (sessionMeta && sessionMeta.content) {
                console.log('✓ Session ID from meta tag');
                return sessionMeta.content;
            }

            // Method 2: Check cookies
            const cookies = document.cookie.split(';');
            for (let cookie of cookies) {
                const [name, value] = cookie.trim().split('=');
                if (name === 'laravel_session' || name.includes('session')) {
                    if (value) {
                        console.log('✓ Session ID from cookie:', name);
                        return decodeURIComponent(value);
                    }
                }
            }

            // Method 3: Check localStorage
            const storedSessionId = localStorage.getItem('_cart_session_id');
            if (storedSessionId) {
                console.log('✓ Session ID from localStorage');
                return storedSessionId;
            }

            console.warn('⚠️ No session ID found via any method');
            return null;
        }

        /**
         * Clean up legacy cart storage formats
         */
        function cleanupLegacyCarts() {
            const legacyKeys = ['cart', 'cart_guest', 'Cart', 'shopping_cart'];
            let cleanedCount = 0;

            legacyKeys.forEach(key => {
                if (localStorage.getItem(key)) {
                    localStorage.removeItem(key);
                    cleanedCount++;
                    console.log('Cleaned legacy cart:', key);
                }
            });

            Object.keys(localStorage).forEach(key => {
                if (key.startsWith('cart_user_')) {
                    localStorage.removeItem(key);
                    cleanedCount++;
                    console.log('Cleaned old user cart:', key);
                }
            });

            if (cleanedCount > 0) {
                console.log(`Cleaned ${cleanedCount} legacy cart entries`);
            }
        }

        /**
         * Manually update cart count badges in the UI
         */
        function manualUpdateCartCount() {
            try {
                if (typeof window.updateCartCount === 'function') {
                    window.updateCartCount();
                    console.log('✓ Cart count updated via cart.js');
                    return;
                }

                const selectors = [
                    '.cart-count',
                    '#cart-count',
                    '[data-cart-count]',
                    '.badge.cart-badge',
                    '.cart-badge'
                ];

                let updatedCount = 0;

                selectors.forEach(selector => {
                    const elements = document.querySelectorAll(selector);
                    elements.forEach(element => {
                        element.textContent = '0';
                        element.setAttribute('data-count', '0');
                        element.style.display = 'none';
                        element.classList.add('d-none');
                        updatedCount++;
                    });
                });

                if (updatedCount > 0) {
                    console.log(`✅ Updated ${updatedCount} cart count badges to 0`);
                }

            } catch (error) {
                console.error('Error updating cart count:', error);
            }
        }

        console.log('🛒 Pickup order confirmation cart clearing script initialized');

    })();
</script>
@endpush