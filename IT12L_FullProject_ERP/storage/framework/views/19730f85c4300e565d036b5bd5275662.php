<?php $__env->startSection('content'); ?>
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
                        <strong>Order #<?php echo e($order->id); ?></strong>
                        <br>
                        <small>Branch: <?php echo e($order->branch_name); ?></small>
                        <br>
                        <small>Total: ₱<?php echo e(number_format($order->total_amount, 2)); ?></small>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                        <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="btn btn-primary">
                            <i class="fas fa-eye"></i> View Order Details
                        </a>
                        <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> View All Orders
                        </a>
                        <a href="<?php echo e(route('home')); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-home"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Clear cart immediately when page loads
document.addEventListener('DOMContentLoaded', function() {
    clearCartAfterCheckout();
});

function clearCartAfterCheckout() {
    console.log('=== Clearing Cart After Successful Checkout ===');
    
    // Get the correct cart storage key
    const cartKey = getCartStorageKey();
    console.log('Cart key:', cartKey);
    
    // Clear the cart from localStorage
    if (localStorage.getItem(cartKey)) {
        console.log('Cart found, clearing...');
        localStorage.removeItem(cartKey);
    }
    
    // Clear all possible cart keys (safety measure)
    const possibleKeys = ['cart', 'cart_guest', 'Cart', 'shopping_cart'];
    possibleKeys.forEach(key => {
        if (localStorage.getItem(key)) {
            localStorage.removeItem(key);
        }
    });
    
    // Clear user-specific carts
    Object.keys(localStorage).forEach(key => {
        if (key.startsWith('cart_user_')) {
            localStorage.removeItem(key);
        }
    });
    
    console.log('✅ Cart cleared from localStorage');
    
    // Update cart count in navbar
    updateCartCount();
    
    // Trigger storage event for other tabs/windows
    window.dispatchEvent(new Event('storage'));
    
    console.log('=== Cart Clearing Complete ===');
}

// Get the cart storage key (must match your cart.js logic)
function getCartStorageKey() {
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    const userId = userIdMeta ? userIdMeta.content : null;
    
    if (userId && userId !== '' && userId !== 'null') {
        return `cart_user_${userId}`;
    }
    return 'cart_guest';
}

// Update cart count in navbar
function updateCartCount() {
    // Update badge to 0
    const badges = document.querySelectorAll('.cart-count, #cart-count, .badge.cart-count, [data-cart-count]');
    badges.forEach(badge => {
        badge.textContent = '0';
        badge.style.display = 'none';
    });
    
    // If there's a global updateCartCount function, call it
    if (typeof window.updateCartCount === 'function') {
        window.updateCartCount();
    }
    
    console.log('Cart count updated to 0');
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Videos\Project_IT12\sabio\IT12L_Project\IT12L_FullProject_ERP\resources\views/user/checkout/confirm.blade.php ENDPATH**/ ?>