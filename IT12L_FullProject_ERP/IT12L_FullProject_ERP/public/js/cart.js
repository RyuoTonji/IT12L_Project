/**
 * FOOD ORDERING SYSTEM - CART FUNCTIONALITY
 * File: public/js/cart.js
 * FIXED: No more blinking cart count
 */

// ============================================================================
// CHECK IF USER IS ADMIN
// ============================================================================
function isAdmin() {
    const adminMeta = document.querySelector('meta[name="user-is-admin"]');
    return adminMeta && adminMeta.content === 'true';
}

// ============================================================================
// GET CART STORAGE KEY
// ============================================================================
function getCartStorageKey() {
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    const userId = userIdMeta ? userIdMeta.content : null;
    
    if (userId && userId !== '') {
        return `cart_user_${userId}`;
    } else {
        return 'cart_guest';
    }
}

// ============================================================================
// GET CART FROM LOCALSTORAGE
// ============================================================================
function getCart() {
    if (isAdmin()) {
        return [];
    }
    
    const cartKey = getCartStorageKey();
    const cartData = localStorage.getItem(cartKey);
    return JSON.parse(cartData || '[]');
}

// ============================================================================
// SAVE CART TO LOCALSTORAGE
// ============================================================================
function saveCart(cart) {
    if (isAdmin()) {
        console.warn('Admin users cannot use cart functionality');
        return;
    }
    
    const cartKey = getCartStorageKey();
    localStorage.setItem(cartKey, JSON.stringify(cart));
    
    // Update cart count ONCE after saving
    updateCartCount();
}

// ============================================================================
// CLEAR GUEST CART
// ============================================================================
function clearGuestCart() {
    localStorage.removeItem('cart_guest');
}

// ============================================================================
// MIGRATE GUEST CART TO USER CART
// ============================================================================
function migrateGuestCartToUser() {
    const guestCart = JSON.parse(localStorage.getItem('cart_guest') || '[]');
    
    if (guestCart.length > 0) {
        const userIdMeta = document.querySelector('meta[name="user-id"]');
        const userId = userIdMeta ? userIdMeta.content : null;
        
        if (userId && userId !== '' && !isAdmin()) {
            const userCartKey = `cart_user_${userId}`;
            let userCart = JSON.parse(localStorage.getItem(userCartKey) || '[]');
            
            guestCart.forEach(guestItem => {
                const existingIndex = userCart.findIndex(item => item.id == guestItem.id);
                if (existingIndex > -1) {
                    userCart[existingIndex].quantity += guestItem.quantity;
                } else {
                    userCart.push(guestItem);
                }
            });
            
            localStorage.setItem(userCartKey, JSON.stringify(userCart));
            clearGuestCart();
            updateCartCount();
            
            console.log('Guest cart migrated to user cart');
        }
    }
}

// ============================================================================
// UPDATE CART COUNT - OPTIMIZED VERSION (NO BLINKING)
// ============================================================================
let updateCartCountTimeout = null;

function updateCartCount() {
    // Clear any pending updates to prevent multiple rapid calls
    if (updateCartCountTimeout) {
        clearTimeout(updateCartCountTimeout);
    }
    
    // Debounce: Wait 50ms before actually updating
    updateCartCountTimeout = setTimeout(() => {
        performCartCountUpdate();
    }, 50);
}

function performCartCountUpdate() {
    try {
        // Hide cart for admins
        if (isAdmin()) {
            const elements = document.querySelectorAll('.cart-count, #cart-count');
            elements.forEach(el => {
                el.style.display = 'none';
            });
            return;
        }
        
        // Get cart and calculate total
        const cart = getCart();
        const count = cart.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0);
        
        // Find all cart count elements
        const elements = document.querySelectorAll('.cart-count, #cart-count, [class*="cart-count"]');
        
        if (elements.length === 0) {
            console.warn('No cart count elements found');
            return;
        }
        
        // Update each element ONCE
        elements.forEach(el => {
            // Only update if value changed (prevents unnecessary redraws)
            if (el.textContent != count) {
                el.textContent = count;
                el.innerText = count;
            }
            
            // Update visibility
            if (count > 0) {
                if (el.style.display !== 'inline-block') {
                    el.style.display = 'inline-block';
                    el.style.visibility = 'visible';
                    el.style.opacity = '1';
                }
            } else {
                if (el.style.display !== 'none') {
                    el.style.display = 'none';
                }
            }
        });
        
    } catch (error) {
        console.error('Error updating cart count:', error);
    }
}

// ============================================================================
// ADD TO CART
// ============================================================================
function addToCart(productData, quantity = 1) {
    if (isAdmin()) {
        showAlert('warning', 'Administrators cannot add items to cart.', 3000);
        return false;
    }
    
    console.log('Adding to cart:', productData);
    
    if (!productData.id) {
        console.error('Product data missing id:', productData);
        showAlert('error', 'Error adding item to cart', 3000);
        return false;
    }
    
    let cart = getCart();
    const existingIndex = cart.findIndex(item => item.id == productData.id);
    
    if (existingIndex > -1) {
        cart[existingIndex].quantity = parseInt(cart[existingIndex].quantity) + parseInt(quantity);
    } else {
        cart.push({
            id: productData.id,
            name: productData.name,
            price: productData.price,
            image: productData.image,
            branch_id: productData.branch_id,
            branch_name: productData.branch_name,
            quantity: parseInt(quantity)
        });
    }
    
    saveCart(cart);
    showAlert('success', `${productData.name} added to cart!`, 3000);
    
    // Cart count will be updated by saveCart() - no need to call again
    
    return true;
}

// ============================================================================
// UPDATE CART ITEM QUANTITY
// ============================================================================
function updateCartItem(productId, quantity) {
    if (isAdmin()) return;
    
    console.log('Updating cart item:', productId, 'Quantity:', quantity);
    
    let cart = getCart();
    const index = cart.findIndex(item => item.id == productId);
    
    if (index > -1) {
        cart[index].quantity = parseInt(quantity);
        saveCart(cart);
    }
}

// ============================================================================
// REMOVE FROM CART
// ============================================================================
function removeFromCart(productId, productName) {
    if (isAdmin()) return;
    
    if (!confirm(`Remove ${productName} from cart?`)) {
        return;
    }
    
    let cart = getCart();
    cart = cart.filter(item => item.id != productId);
    saveCart(cart);
    
    showAlert('success', 'Item removed from cart', 2000);
    
    if (cart.length === 0) {
        setTimeout(() => location.reload(), 1000);
    }
}

// ============================================================================
// CLEAR CART
// ============================================================================
function clearCart() {
    if (isAdmin()) return;
    
    if (!confirm('Are you sure you want to clear your cart?')) {
        return;
    }
    
    const cartKey = getCartStorageKey();
    localStorage.removeItem(cartKey);
    updateCartCount();
    showAlert('success', 'Cart cleared', 2000);
    setTimeout(() => location.reload(), 1000);
}

// ============================================================================
// FORMAT CURRENCY
// ============================================================================
function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// ============================================================================
// SHOW ALERT
// ============================================================================
function showAlert(type, message, duration = 3000) {
    document.querySelectorAll('.custom-cart-alert').forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed custom-cart-alert`;
    alertDiv.style.cssText = 'top: 80px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, duration);
}

// ============================================================================
// INITIALIZE ON PAGE LOAD
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== Cart.js initialized ===');
    console.log('Is Admin:', isAdmin());
    console.log('Cart Storage Key:', getCartStorageKey());
    
    // Migrate guest cart if needed
    if (!isAdmin()) {
        migrateGuestCartToUser();
    }
    
    // Update cart count ONCE on page load
    updateCartCount();
});

// Also update on page show (when navigating back)
window.addEventListener('pageshow', function() {
    updateCartCount();
});

// Export functions globally
window.addToCart = addToCart;
window.updateCartItem = updateCartItem;
window.removeFromCart = removeFromCart;
window.clearCart = clearCart;
window.updateCartCount = updateCartCount;
window.getCart = getCart;
window.isAdmin = isAdmin;
window.migrateGuestCartToUser = migrateGuestCartToUser;
window.formatCurrency = formatCurrency;
window.showAlert = showAlert;
window.getCartStorageKey = getCartStorageKey;

console.log('✅ Cart functions exported globally');