/**
 * ============================================================================
 * SESSION-BASED CART MANAGEMENT SYSTEM
 * File: public/js/cart.js
 * Version: 6.0 - SESSION BASED
 * 
 * Logic: One session = One cart (regardless of login status)
 * - Guest adds items → stored in session cart
 * - Same user logs in → SAME session → SAME cart persists
 * - No cart transfer needed - it's already the same cart!
 * ============================================================================
 */

'use strict';

// ============================================================================
// CONFIGURATION
// ============================================================================

const CART_CONFIG = {
    MAX_QUANTITY: 99,
    MIN_QUANTITY: 1,
    NOTIFICATION_DURATION: 3000,
    DEBUG_MODE: true
};

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

function log(...args) {
    if (CART_CONFIG.DEBUG_MODE) {
        console.log('[Cart]', ...args);
    }
}

function logError(...args) {
    console.error('[Cart Error]', ...args);
}

function logWarn(...args) {
    console.warn('[Cart Warning]', ...args);
}

function isAdmin() {
    const meta = document.querySelector('meta[name="user-is-admin"]');
    return meta && meta.content === 'true';
}

function getUserId() {
    const meta = document.querySelector('meta[name="user-id"]');
    return meta && meta.content && meta.content !== '' && meta.content !== 'null' 
        ? meta.content 
        : null;
}

/**
 * SESSION-BASED: Use PHP session ID as cart key
 * This ensures the SAME cart persists across guest → login transition
 */
function getCartStorageKey() {
    if (isAdmin()) {
        log('Admin user detected - no cart');
        return null;
    }
    
    // Use session ID from meta tag (set by Laravel)
    const sessionMeta = document.querySelector('meta[name="session-id"]');
    let sessionId = sessionMeta ? sessionMeta.content : null;
    
    // Fallback: generate a stable session identifier
    if (!sessionId) {
        // Try to get from cookie
        const cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === 'laravel_session' || name.includes('session')) {
                sessionId = value;
                break;
            }
        }
    }
    
    // Final fallback: use a persistent identifier
    if (!sessionId) {
        sessionId = localStorage.getItem('_cart_session_id');
        if (!sessionId) {
            sessionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('_cart_session_id', sessionId);
        }
    }
    
    const key = `cart_session_${sessionId}`;
    log('Using SESSION-BASED cart key:', key);
    log('User status:', getUserId() ? `Logged in (ID: ${getUserId()})` : 'Guest');
    
    return key;
}

function formatPrice(price) {
    const num = parseFloat(price);
    if (isNaN(num)) return '0.00';
    return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (!meta) {
        logError('CSRF token meta tag not found!');
        return '';
    }
    return meta.content;
}

function isValidCartItem(item) {
    return item && 
           typeof item === 'object' && 
           item.id && 
           item.quantity &&
           !isNaN(parseInt(item.id)) &&
           !isNaN(parseInt(item.quantity)) &&
           parseInt(item.id) > 0 &&
           parseInt(item.quantity) > 0;
}

// ============================================================================
// CORE CART OPERATIONS (SESSION-BASED)
// ============================================================================

function getCart() {
    const cartKey = getCartStorageKey();
    
    if (!cartKey) {
        log('No cart key available (admin user)');
        return [];
    }
    
    try {
        const cartData = localStorage.getItem(cartKey);
        
        if (!cartData) {
            log('No cart data found in localStorage');
            return [];
        }
        
        const cart = JSON.parse(cartData);
        
        if (!Array.isArray(cart)) {
            logError('Cart data is not an array:', cart);
            localStorage.removeItem(cartKey);
            return [];
        }
        
        const validCart = cart.filter(item => {
            const isValid = isValidCartItem(item);
            if (!isValid) {
                logWarn('Invalid cart item filtered out:', item);
            }
            return isValid;
        });
        
        if (validCart.length !== cart.length) {
            logWarn(`Filtered ${cart.length - validCart.length} invalid items from cart`);
            saveCart(validCart);
        }
        
        log('Cart retrieved from localStorage:', validCart);
        return validCart;
        
    } catch (error) {
        logError('Error reading cart from localStorage:', error);
        try {
            localStorage.removeItem(cartKey);
        } catch (e) {
            logError('Failed to clear corrupted cart:', e);
        }
        return [];
    }
}

function saveCart(cart) {
    const cartKey = getCartStorageKey();
    
    if (!cartKey) {
        logWarn('Cannot save cart (admin user or no key)');
        return false;
    }
    
    try {
        if (!Array.isArray(cart)) {
            logError('Attempted to save non-array as cart:', cart);
            cart = [];
        }
        
        const validCart = cart.filter(isValidCartItem);
        
        if (validCart.length !== cart.length) {
            logWarn('Some invalid items were filtered during save');
        }
        
        localStorage.setItem(cartKey, JSON.stringify(validCart));
        
        log('Cart saved to localStorage:', validCart);
        log('Cart persists across login/logout - same session!');
        
        updateCartCount();
        
        window.dispatchEvent(new Event('storage'));
        window.dispatchEvent(new CustomEvent('cartUpdated', { 
            detail: { cart: validCart } 
        }));
        
        return true;
        
    } catch (error) {
        logError('Error saving cart to localStorage:', error);
        
        if (error.name === 'QuotaExceededError') {
            showNotification('Cart storage is full. Please clear some items.', 'error');
        }
        
        return false;
    }
}

/**
 * Add item to cart - SESSION-BASED (no user check needed)
 */
function addToCart(productData, quantity = 1, productName = 'Product') {
    if (isAdmin()) {
        showNotification('Admins cannot add items to cart', 'warning');
        return false;
    }
    
    // Handle different input formats
    let product = {};
    
    if (typeof productData === 'object' && productData !== null) {
        product = productData;
    } else {
        product = {
            id: productData,
            name: productName
        };
    }
    
    const productId = parseInt(product.id);
    quantity = parseInt(quantity);
    
    if (isNaN(productId) || productId <= 0) {
        logError('Invalid product ID:', productData);
        showNotification('Invalid product', 'error');
        return false;
    }
    
    if (isNaN(quantity) || quantity <= 0) {
        logWarn('Invalid quantity, defaulting to 1:', quantity);
        quantity = 1;
    }
    
    if (quantity > CART_CONFIG.MAX_QUANTITY) {
        quantity = CART_CONFIG.MAX_QUANTITY;
        showNotification(`Maximum quantity is ${CART_CONFIG.MAX_QUANTITY}`, 'warning');
    }
    
    const cart = getCart();
    const existingItemIndex = cart.findIndex(item => item.id === productId);
    
    if (existingItemIndex !== -1) {
        const existingItem = cart[existingItemIndex];
        const newQuantity = parseInt(existingItem.quantity) + quantity;
        
        if (newQuantity > CART_CONFIG.MAX_QUANTITY) {
            cart[existingItemIndex].quantity = CART_CONFIG.MAX_QUANTITY;
            showNotification(`${product.name || 'Product'} quantity updated to maximum (${CART_CONFIG.MAX_QUANTITY})`, 'warning');
        } else {
            cart[existingItemIndex].quantity = newQuantity;
            showNotification(`${product.name || 'Product'} quantity updated!`, 'success');
        }
    } else {
        const cartItem = {
            id: productId,
            name: product.name || 'Product',
            price: product.price || 0,
            image: product.image || null,
            branch_id: product.branch_id || null,
            branch_name: product.branch_name || 'Unknown Branch',
            is_available: product.is_available !== false,
            quantity: quantity
        };
        
        cart.push(cartItem);
        showNotification(`${product.name || 'Product'} added to cart!`, 'success');
    }
    
    const saved = saveCart(cart);
    
    if (saved) {
        log('Item added to cart:', { productId, quantity, product });
    }
    
    return saved;
}

function updateCartItem(productId, quantity) {
    productId = parseInt(productId);
    quantity = parseInt(quantity);
    
    if (isNaN(productId) || isNaN(quantity)) {
        logError('Invalid parameters for updateCartItem:', { productId, quantity });
        return false;
    }
    
    if (quantity <= 0) {
        return removeFromCart(productId);
    }
    
    if (quantity > CART_CONFIG.MAX_QUANTITY) {
        quantity = CART_CONFIG.MAX_QUANTITY;
        showNotification(`Maximum quantity is ${CART_CONFIG.MAX_QUANTITY}`, 'warning');
    }
    
    const cart = getCart();
    const itemIndex = cart.findIndex(item => item.id === productId);
    
    if (itemIndex !== -1) {
        cart[itemIndex].quantity = quantity;
        const saved = saveCart(cart);
        
        if (saved) {
            log('Cart item updated:', { productId, quantity });
        }
        
        return saved;
    }
    
    logWarn('Item not found in cart for update:', productId);
    return false;
}

function removeFromCart(productId, productName = 'Item') {
    productId = parseInt(productId);
    
    if (isNaN(productId) || productId <= 0) {
        logError('Invalid product ID for removal:', productId);
        return false;
    }
    
    const cart = getCart();
    const originalLength = cart.length;
    const filteredCart = cart.filter(item => item.id !== productId);
    
    if (filteredCart.length < originalLength) {
        const saved = saveCart(filteredCart);
        
        if (saved) {
            showNotification(`${productName} removed from cart`, 'info');
            log('Item removed from cart:', productId);
        }
        
        return saved;
    }
    
    logWarn('Item not found in cart for removal:', productId);
    return false;
}

function clearCart() {
    const cartKey = getCartStorageKey();
    
    if (!cartKey) {
        logWarn('Cannot clear cart (no key)');
        return false;
    }
    
    try {
        localStorage.removeItem(cartKey);
        updateCartCount();
        showNotification('Cart cleared', 'info');
        
        log('Cart cleared');
        
        window.dispatchEvent(new Event('storage'));
        window.dispatchEvent(new CustomEvent('cartCleared'));
        
        if (window.location.pathname.includes('/cart')) {
            setTimeout(() => location.reload(), 500);
        }
        
        return true;
        
    } catch (error) {
        logError('Error clearing cart:', error);
        return false;
    }
}

function getCartCount() {
    const cart = getCart();
    const count = cart.reduce((total, item) => {
        return total + parseInt(item.quantity || 0);
    }, 0);
    
    log('Cart count:', count);
    return count;
}

function getCartTotal() {
    const cart = getCart();
    let total = 0;
    
    cart.forEach(item => {
        if (item.price) {
            total += parseFloat(item.price) * parseInt(item.quantity);
        }
    });
    
    return total;
}

// ============================================================================
// UI UPDATE FUNCTIONS
// ============================================================================

function updateCartCount() {
    if (isAdmin()) {
        log('Skipping cart count update (admin user)');
        return;
    }
    
    const count = getCartCount();
    const selectors = ['#cart-count', '.cart-count'];
    
    selectors.forEach(selector => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(el => {
            el.textContent = count;
            el.setAttribute('data-count', count);
            
            if (count > 0) {
                el.style.display = 'flex';
                el.classList.remove('d-none');
            } else {
                el.style.display = 'none';
                el.classList.add('d-none');
            }
        });
    });
    
    log(`Updated cart count display to: ${count}`);
}

function showNotification(message, type = 'success') {
    document.querySelectorAll('.cart-notification-alert').forEach(el => el.remove());
    
    if (type === 'error') type = 'danger';
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed cart-notification-alert`;
    alert.style.cssText = 'top: 80px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
    alert.setAttribute('role', 'alert');
    
    const icons = {
        success: 'check-circle',
        danger: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    
    const icon = icons[type] || 'info-circle';
    
    alert.innerHTML = `
        <i class="fas fa-${icon} me-2"></i>${escapeHtml(message)}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 150);
    }, CART_CONFIG.NOTIFICATION_DURATION);
    
    log('Notification shown:', { message, type });
}

// ============================================================================
// CART PAGE FUNCTIONS
// ============================================================================

function loadCartPage() {
    const cart = getCart();
    
    log('=== Loading Cart Page (SESSION-BASED) ===');
    log('Cart items:', cart);
    
    if (cart.length === 0) {
        showEmptyCart();
        return;
    }
    
    renderCartItems(cart);
}

function showEmptyCart() {
    const loadingEl = document.getElementById('loading-cart');
    const emptyEl = document.getElementById('empty-cart');
    const contentEl = document.getElementById('cart-content');
    
    if (loadingEl) loadingEl.classList.add('d-none');
    if (emptyEl) emptyEl.classList.remove('d-none');
    if (contentEl) contentEl.classList.add('d-none');
    
    log('Empty cart state displayed');
}

function renderCartItems(cart) {
    log('Rendering cart items...');
    
    const loadingEl = document.getElementById('loading-cart');
    const emptyEl = document.getElementById('empty-cart');
    const contentEl = document.getElementById('cart-content');
    
    if (loadingEl) loadingEl.classList.add('d-none');
    if (emptyEl) emptyEl.classList.add('d-none');
    if (contentEl) contentEl.classList.remove('d-none');
    
    const tbody = document.getElementById('cart-items-body');
    
    if (!tbody) {
        logError('Cart items tbody element not found!');
        return;
    }
    
    tbody.innerHTML = '';
    
    let total = 0;
    let hasUnavailableItems = false;
    
    cart.forEach(item => {
        const subtotal = parseFloat(item.price || 0) * parseInt(item.quantity);
        total += subtotal;
        
        if (item.is_available === false) {
            hasUnavailableItems = true;
        }
        
        const row = createCartItemRow(item, subtotal);
        tbody.appendChild(row);
    });
    
    if (hasUnavailableItems) {
        showNotification('Some items in your cart may no longer be available', 'warning');
    }
    
    updateTotalDisplay(total);
    attachCartEventListeners();
    
    log('Cart rendered successfully. Total:', total);
}

function createCartItemRow(item, subtotal) {
    const row = document.createElement('tr');
    row.dataset.productId = item.id;
    
    if (item.is_available === false) {
        row.classList.add('table-warning');
    }
    
    const imageHtml = item.image 
        ? `<img src="/storage/${escapeHtml(item.image)}" 
               alt="${escapeHtml(item.name)}"
               class="rounded me-3"
               style="width: 60px; height: 60px; object-fit: cover;"
               onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'60\\' height=\\'60\\'%3E%3Crect fill=\\'%23ddd\\' width=\\'60\\' height=\\'60\\'/%3E%3Ctext x=\\'50%25\\' y=\\'50%25\\' dominant-baseline=\\'middle\\' text-anchor=\\'middle\\' fill=\\'%23999\\' font-size=\\'12\\'%3ENo Image%3C/text%3E%3C/svg%3E';">`
        : `<div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center" 
                style="width: 60px; height: 60px;">
               <i class="fas fa-utensils text-white"></i>
           </div>`;
    
    const availabilityBadge = item.is_available === false
        ? '<br><span class="badge bg-danger">Out of Stock</span>' 
        : '';
    
    const isDisabled = item.is_available === false ? 'disabled' : '';
    
    row.innerHTML = `
        <td>
            <div class="d-flex align-items-center">
                ${imageHtml}
                <div>
                    <strong>${escapeHtml(item.name)}</strong>
                    <br>
                    <small class="text-muted">
                        <i class="fas fa-store"></i> ${escapeHtml(item.branch_name || 'Unknown')}
                    </small>
                    ${availabilityBadge}
                </div>
            </div>
        </td>
        <td class="align-middle">₱${formatPrice(item.price || 0)}</td>
        <td class="align-middle">
            <div class="input-group" style="width: 130px;">
                <button class="btn btn-sm btn-outline-secondary decrease-qty" 
                        data-product-id="${item.id}"
                        ${isDisabled}>
                    <i class="fas fa-minus"></i>
                </button>
                <input type="number" 
                       class="form-control form-control-sm text-center quantity-input" 
                       value="${item.quantity}" 
                       min="${CART_CONFIG.MIN_QUANTITY}" 
                       max="${CART_CONFIG.MAX_QUANTITY}"
                       data-product-id="${item.id}"
                       ${isDisabled}>
                <button class="btn btn-sm btn-outline-secondary increase-qty" 
                        data-product-id="${item.id}"
                        ${isDisabled}>
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </td>
        <td class="align-middle item-subtotal">₱${formatPrice(subtotal)}</td>
        <td class="align-middle">
            <button class="btn btn-sm btn-danger remove-item" 
                    data-product-id="${item.id}"
                    data-product-name="${escapeHtml(item.name)}">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    return row;
}

function updateTotalDisplay(total) {
    const totalEl = document.getElementById('cart-total');
    const totalFinalEl = document.getElementById('cart-total-final');
    
    const formattedTotal = '₱' + formatPrice(total);
    
    if (totalEl) totalEl.textContent = formattedTotal;
    if (totalFinalEl) totalFinalEl.textContent = formattedTotal;
}

function attachCartEventListeners() {
    log('Attaching cart event listeners...');
    
    document.querySelectorAll('.decrease-qty').forEach(btn => {
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
    });
    
    document.querySelectorAll('.increase-qty').forEach(btn => {
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
    });
    
    document.querySelectorAll('.quantity-input').forEach(input => {
        const newInput = input.cloneNode(true);
        newInput.value = input.value;
        input.parentNode.replaceChild(newInput, input);
    });
    
    document.querySelectorAll('.remove-item').forEach(btn => {
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
    });
    
    document.querySelectorAll('.decrease-qty').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const productId = parseInt(this.dataset.productId);
            const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
            let qty = parseInt(input.value);
            
            if (qty > CART_CONFIG.MIN_QUANTITY) {
                qty--;
                input.value = qty;
                updateCartItem(productId, qty);
                loadCartPage();
            }
        });
    });

    document.querySelectorAll('.increase-qty').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const productId = parseInt(this.dataset.productId);
            const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
            let qty = parseInt(input.value);
            
            if (qty < CART_CONFIG.MAX_QUANTITY) {
                qty++;
                input.value = qty;
                updateCartItem(productId, qty);
                loadCartPage();
            }
        });
    });
    
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function(e) {
            e.stopPropagation();
            const productId = parseInt(this.dataset.productId);
            let qty = parseInt(this.value);
            
            if (isNaN(qty) || qty < CART_CONFIG.MIN_QUANTITY) {
                qty = CART_CONFIG.MIN_QUANTITY;
            } else if (qty > CART_CONFIG.MAX_QUANTITY) {
                qty = CART_CONFIG.MAX_QUANTITY;
            }
            
            this.value = qty;
            updateCartItem(productId, qty);
            loadCartPage();
        });
        
        input.addEventListener('keypress', function(e) {
            if (e.key && !/[0-9]/.test(e.key) && e.key !== 'Enter') {
                e.preventDefault();
            }
        });
    });
    
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const productId = parseInt(this.dataset.productId);
            const productName = this.dataset.productName;
            
            removeFromCart(productId, productName);
            setTimeout(() => loadCartPage(), 500);
        });
    });
    
    const clearCartBtn = document.getElementById('clear-cart');
    if (clearCartBtn) {
        const newClearBtn = clearCartBtn.cloneNode(true);
        clearCartBtn.parentNode.replaceChild(newClearBtn, clearCartBtn);
        
        newClearBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (confirm('Are you sure you want to clear your entire cart?')) {
                clearCart();
            }
        });
    }

    log('Event listeners attached successfully');
}

// ============================================================================
// INITIALIZATION
// ============================================================================

function initializeCart() {
    log('=== Initializing Cart System (SESSION-BASED) ===');
    log('Current page:', window.location.pathname);
    log('User is admin:', isAdmin());
    log('User ID:', getUserId() || 'Guest');
    log('Cart storage key:', getCartStorageKey());
    log('SESSION LOGIC: Same session = Same cart (before AND after login)');
    
    updateCartCount();
    
    if (window.location.pathname.includes('/cart')) {
        log('Cart page detected, loading cart items...');
        loadCartPage();
        setupCheckoutForm();
    }
    
    setupAddToCartButtons();
    
    log('=== Cart System Initialized ===');
}

function setupCheckoutForm() {
    const checkoutForm = document.getElementById('checkout-form');
    
    if (!checkoutForm) {
        log('Checkout form not found');
        return;
    }
    
    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        log('Checkout form submitted');
        
        const cart = getCart();
        
        if (cart.length === 0) {
            alert('Your cart is empty!');
            return false;
        }
        
        const cartJson = JSON.stringify(cart);
        log('Sending cart to checkout:', cartJson);
        
        const hiddenInput = document.getElementById('cart_items_hidden');
        if (hiddenInput) {
            hiddenInput.value = cartJson;
        } else {
            logError('Hidden input cart_items_hidden not found!');
        }
        
        this.submit();
    });
    
    log('Checkout form setup complete');
}

function setupAddToCartButtons() {
    const buttons = document.querySelectorAll('[data-add-to-cart], .add-to-cart-btn, #add-to-cart-detail');
    
    log(`Found ${buttons.length} "Add to Cart" button(s)`);
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productData = {
                id: this.dataset.productId || this.dataset.id,
                name: this.dataset.productName || this.dataset.name || 'Product',
                price: this.dataset.productPrice || this.dataset.price || 0,
                image: this.dataset.productImage || this.dataset.image || null,
                branch_id: this.dataset.branchId || null,
                branch_name: this.dataset.branchName || 'Unknown Branch',
                is_available: this.dataset.isAvailable !== 'false'
            };
            
            const quantityInput = document.getElementById('quantity');
            const quantity = quantityInput ? parseInt(quantityInput.value) : parseInt(this.dataset.quantity || 1);
            
            log('Add to cart button clicked:', productData, 'Quantity:', quantity);
            
            addToCart(productData, quantity);
        });
    });
}

// ============================================================================
// EVENT LISTENERS
// ============================================================================

document.addEventListener('DOMContentLoaded', initializeCart);

window.addEventListener('storage', function(e) {
    if (e.key && e.key.startsWith('cart_session_')) {
        log('Cart updated in another tab/window');
        updateCartCount();
        
        if (window.location.pathname.includes('/cart')) {
            loadCartPage();
        }
    }
});

document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        log('Page became visible, updating cart count');
        updateCartCount();
    }
});

// ============================================================================
// DEBUG UTILITIES
// ============================================================================

window.debugCart = function() {
    console.log('=== CART DEBUG INFO (SESSION-BASED) ===');
    console.log('Configuration:', CART_CONFIG);
    console.log('Cart Key:', getCartStorageKey());
    console.log('Cart Contents:', getCart());
    console.log('Cart Count:', getCartCount());
    console.log('Cart Total:', getCartTotal());
    console.log('Is Admin:', isAdmin());
    console.log('User ID:', getUserId() || 'Guest');
    console.log('Session Logic: ONE SESSION = ONE CART');
    console.log('All localStorage keys:', Object.keys(localStorage));
    console.log('======================');
};

window.clearAllCarts = function() {}
    console.log('Clearing all cart data from localStorage...');

// ============================================================================
// DEBUG UTILITIES
// ============================================================================

/**
 * Logs comprehensive cart debug information to console
 */
window.debugCart = function() {
    console.log('=== CART DEBUG INFO ===');
    console.log('Configuration:', CART_CONFIG);
    console.log('Cart Key:', getCartStorageKey());
    console.log('Cart Contents:', getCart());
    console.log('Cart Count:', getCartCount());
    console.log('Cart Total:', getCartTotal());
    console.log('Is Admin:', isAdmin());
    console.log('User ID:', getUserId());
    console.log('All localStorage keys:', Object.keys(localStorage));
    console.log('======================');
};

/**
 * Clears all cart data from localStorage
 */
window.clearAllCarts = function() {
    console.log('Clearing all cart data from localStorage...');
    Object.keys(localStorage).forEach(key => {
        if (key.startsWith('cart_')) {
            console.log(`Removing: ${key}`);
            localStorage.removeItem(key);
        }
    });
    updateCartCount();
    console.log('All carts cleared!');
};

/**
 * Resets the current user's cart
 */
window.resetCart = function() {
    console.log('Resetting current cart...');
    const cartKey = getCartStorageKey();
    localStorage.removeItem(cartKey);
    updateCartCount();
    console.log('Cart reset complete!');
};

/**
 * Lists all carts in localStorage
 */
window.listAllCarts = function() {
    console.log('=== ALL CARTS ===');
    Object.keys(localStorage).forEach(key => {
        if (key.startsWith('cart_')) {
            const cart = JSON.parse(localStorage.getItem(key));
            console.log(`${key}:`, cart);
        }
    });
    console.log('=================');
};

/**
 * Adds a test item to the cart for debugging
 */
window.addTestItem = function() {
    const testItem = {
        id: 'test_' + Date.now(),
        name: 'Test Product',
        price: 99.99,
        quantity: 1
    };
    addToCart(testItem);
    console.log('Test item added:', testItem);
};

// Make debug functions available globally
console.log('Cart debug utilities loaded. Available commands:');
console.log('- debugCart(): View cart debug info');
console.log('- clearAllCarts(): Clear all carts from localStorage');
console.log('- resetCart(): Reset current user cart');
console.log('- listAllCarts(): List all carts');
console.log('- addTestItem(): Add test item to cart');

// ============================================================================
// EXPORT FUNCTIONS TO WINDOW
// ============================================================================

window.getCart = getCart;
window.addToCart = addToCart;
window.updateCartItem = updateCartItem;
window.removeFromCart = removeFromCart;
window.clearCart = clearCart;
window.getCartCount = getCartCount;
window.getCartTotal = getCartTotal;
window.updateCartCount = updateCartCount;
window.showNotification = showNotification;
window.loadCartPage = loadCartPage;
window.getCartStorageKey = getCartStorageKey;
window.isAdmin = isAdmin;
window.getUserId = getUserId;
window.formatPrice = formatPrice;

log('✅ Cart.js loaded successfully (VERSION 5.0 WITH LOGIN SYNC)');