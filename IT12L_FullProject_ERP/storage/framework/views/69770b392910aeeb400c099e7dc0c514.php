<?php $__env->startSection('content'); ?>
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
                                    <!-- Items will be inserted here -->
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
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart page loaded');
    loadCartPage();
    
    // Attach checkout form submit handler
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            console.log('Checkout form submit triggered');
            
            // Get cart from localStorage
            const cart = getCart();
            console.log('Cart from localStorage:', cart);
            
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return false;
            }
            
            // Set cart data in hidden input
            const cartJson = JSON.stringify(cart);
            console.log('Cart JSON to send:', cartJson);
            document.getElementById('cart_items_hidden').value = cartJson;
            
            console.log('Hidden input value:', document.getElementById('cart_items_hidden').value);
            console.log('Form action:', this.action);
            console.log('Form method:', this.method);
            
            // Submit the form
            this.submit();
        });
    } else {
        console.error('Checkout form not found!');
    }
});

function loadCartPage() {
    // Check if getCart function exists
    if (typeof getCart === 'undefined') {
        console.error('getCart function not found!');
        showEmptyCart();
        return;
    }
    
    const cart = getCart();
    console.log('Cart contents:', cart);
    
    if (cart.length === 0) {
        showEmptyCart();
        return;
    }
    
    // Fetch product details
    const productIds = cart.map(item => item.id);
    
    fetch('/api/cart/products', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ product_ids: productIds })
    })
    .then(res => res.json())
    .then(products => {
        console.log('Products fetched:', products);
        if (products.length === 0) {
            showEmptyCart();
            return;
        }
        renderCartItems(cart, products);
    })
    .catch(error => {
        console.error('Error loading cart:', error);
        showEmptyCart();
    });
}

function showEmptyCart() {
    document.getElementById('loading-cart').classList.add('d-none');
    document.getElementById('empty-cart').classList.remove('d-none');
    document.getElementById('cart-content').classList.add('d-none');
}

function renderCartItems(cart, products) {
    document.getElementById('loading-cart').classList.add('d-none');
    document.getElementById('empty-cart').classList.add('d-none');
    document.getElementById('cart-content').classList.remove('d-none');
    
    const tbody = document.getElementById('cart-items-body');
    tbody.innerHTML = '';
    
    let total = 0;
    
    cart.forEach((cartItem, index) => {
        const product = products.find(p => p.id == cartItem.id);
        
        if (!product) {
            console.warn('Product not found for cart item:', cartItem);
            return;
        }
        
        const subtotal = product.price * cartItem.quantity;
        total += subtotal;
        
        const row = document.createElement('tr');
        row.dataset.productId = product.id;
        
        row.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    ${product.image 
                        ? `<img src="/storage/${product.image}" 
                               alt="${product.name}"
                               class="rounded me-3"
                               style="width: 60px; height: 60px; object-fit: cover;">`
                        : `<div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                               <i class="fas fa-utensils text-white"></i>
                           </div>`
                    }
                    <div>
                        <strong>${product.name}</strong>
                        <br>
                        <small class="text-muted">
                            <i class="fas fa-store"></i> ${product.branch_name}
                        </small>
                        ${product.is_available ? '' : '<br><span class="badge bg-danger">Out of Stock</span>'}
                    </div>
                </div>
            </td>
            <td class="align-middle">₱${parseFloat(product.price).toFixed(2)}</td>
            <td class="align-middle">
                <div class="input-group" style="width: 130px;">
                    <button class="btn btn-sm btn-outline-secondary decrease-qty" data-product-id="${product.id}">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" 
                           class="form-control form-control-sm text-center quantity-input" 
                           value="${cartItem.quantity}" 
                           min="1" max="99"
                           data-product-id="${product.id}">
                    <button class="btn btn-sm btn-outline-secondary increase-qty" data-product-id="${product.id}">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </td>
            <td class="align-middle item-subtotal">₱${subtotal.toFixed(2)}</td>
            <td class="align-middle">
                <button class="btn btn-sm btn-danger remove-item" 
                        data-product-id="${product.id}"
                        data-product-name="${product.name}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
    
    updateTotalDisplay(total);
    attachEventListeners();
}

function updateTotalDisplay(total) {
    document.getElementById('cart-total').textContent = formatCurrency(total);
    document.getElementById('cart-total-final').textContent = formatCurrency(total);
}

function attachEventListeners() {
    // Attach decrease quantity
    document.querySelectorAll('.decrease-qty').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = parseInt(this.dataset.productId);
            const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
            let qty = parseInt(input.value);
            if (qty > 1) {
                qty--;
                input.value = qty;
                if (typeof updateCartItem !== 'undefined') {
                    updateCartItem(productId, qty);
                }
                loadCartPage();
            }
        });
    });
    
    // Attach increase quantity
    document.querySelectorAll('.increase-qty').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = parseInt(this.dataset.productId);
            const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
            let qty = parseInt(input.value);
            if (qty < 99) {
                qty++;
                input.value = qty;
                if (typeof updateCartItem !== 'undefined') {
                    updateCartItem(productId, qty);
                }
                loadCartPage();
            }
        });
    });
    
    // Attach quantity input change
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const productId = parseInt(this.dataset.productId);
            let qty = parseInt(this.value);
            
            if (isNaN(qty) || qty < 1) {
                qty = 1;
            } else if (qty > 99) {
                qty = 99;
            }
            
            this.value = qty;
            if (typeof updateCartItem !== 'undefined') {
                updateCartItem(productId, qty);
            }
            loadCartPage();
        });
    });
    
    // Attach remove buttons
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = parseInt(this.dataset.productId);
            const productName = this.dataset.productName;
            
            if (typeof removeFromCart !== 'undefined') {
                removeFromCart(productId, productName);
                setTimeout(() => loadCartPage(), 500);
            }
        });
    });
    
    // Attach clear cart button
    const clearCartBtn = document.getElementById('clear-cart');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', function() {
            if (typeof clearCart !== 'undefined') {
                clearCart();
            }
        });
    }
}

function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Videos\Project_IT12\sabio\IT12L_Project\IT12L_FullProject_ERP\resources\views/user/cart/index.blade.php ENDPATH**/ ?>