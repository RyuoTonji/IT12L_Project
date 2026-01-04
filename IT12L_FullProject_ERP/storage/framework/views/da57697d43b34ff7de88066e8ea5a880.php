<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <h2 class="mb-4">
        <i class="fas fa-shopping-cart"></i> Food Cart
    </h2>

    <!-- Loading State (show by default if migration is needed) -->
    <div id="loading-cart" class="<?php echo e(session('_cart_migration_needed') ? '' : 'd-none'); ?>">
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5>Loading your cart...</h5>
                <p class="text-muted">Please wait while we retrieve your items</p>
            </div>
        </div>
    </div>

    <!-- Empty Cart (hidden by default if migration is needed) -->
    <div id="empty-cart" class="d-none">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                <h4>Your cart is empty</h4>
                <p class="text-muted">Start adding some delicious items!</p>
                <a href="<?php echo e(route('home')); ?>" class="btn btn-primary">
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
                            <?php if(auth()->guard()->check()): ?>
                                <?php if(!auth()->user()->is_admin): ?>
                                    <form action="<?php echo e(route('checkout.index')); ?>" method="POST" id="checkout-form">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="cart_items" id="cart_items_hidden" value="">
                                        <button type="submit" class="btn btn-primary w-100 btn-lg" id="checkout-btn">
                                            <i class="fas fa-credit-card"></i> Proceed to Checkout
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle"></i> Admins cannot checkout
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle"></i> Please login to checkout
                                </div>
                                <a href="<?php echo e(route('login')); ?>?redirect=checkout" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-sign-in-alt"></i> Login to Checkout
                                </a>
                                <a href="<?php echo e(route('register')); ?>" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="fas fa-user-plus"></i> Register New Account
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <a href="<?php echo e(route('home')); ?>" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// =============================================================================
// CART PAGE - FIXED: No race condition with migration
// =============================================================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('[Cart Page] Initializing...');
    
    // Check if migration is in progress
    const migrationNeeded = document.querySelector('meta[name="cart-migration-needed"]');
    const isMigrating = migrationNeeded && migrationNeeded.content === 'true';
    
    if (isMigrating) {
        console.log('[Cart Page] Migration detected - waiting for completion...');
        
        // Listen for migration completion
        window.addEventListener('cartMigrated', function(e) {
            console.log('[Cart Page] Migration completed, loading cart...', e.detail);
            setTimeout(() => loadCartPageSafely(), 500);
        });
        
        // Fallback timeout in case migration event doesn't fire
        setTimeout(() => {
            console.log('[Cart Page] Fallback timeout - loading cart anyway');
            loadCartPageSafely();
        }, 2000);
        
    } else {
        // No migration needed - load immediately
        console.log('[Cart Page] No migration needed, loading cart immediately');
        loadCartPageSafely();
    }
    
    // Setup checkout form
    setupCheckoutForm();
});

/**
 * Safely load cart page (checks if cart.js is loaded)
 */
function loadCartPageSafely() {
    if (typeof loadCartPage === 'function') {
        console.log('[Cart Page] cart.js detected, loading cart...');
        loadCartPage();
    } else {
        console.error('[Cart Page] ERROR: cart.js not loaded! Retrying...');
        setTimeout(loadCartPageSafely, 200);
    }
}

/**
 * Setup checkout form submission
 */
function setupCheckoutForm() {
    const checkoutForm = document.getElementById('checkout-form');
    if (!checkoutForm) return;
    
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
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Videos\projectIT12\IT12L_FullProject_ERP\resources\views/user/cart/index.blade.php ENDPATH**/ ?>