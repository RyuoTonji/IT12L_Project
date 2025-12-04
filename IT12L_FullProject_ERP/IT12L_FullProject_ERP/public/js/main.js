/**
 * FOOD ORDERING SYSTEM - MAIN JAVASCRIPT
 * Handles global functionality and utilities
 */

// ============================================================================
// CSRF TOKEN SETUP
// ============================================================================
function getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

// Add CSRF token to all AJAX requests
document.addEventListener('DOMContentLoaded', function() {
    // Setup CSRF token for fetch requests
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        if (args[1] && args[1].method && args[1].method !== 'GET') {
            args[1].headers = args[1].headers || {};
            if (!args[1].headers['X-CSRF-TOKEN']) {
                args[1].headers['X-CSRF-TOKEN'] = getCSRFToken();
            }
        }
        return originalFetch.apply(this, args);
    };

    // Setup CSRF token for jQuery AJAX (if jQuery is loaded)
    if (typeof $ !== 'undefined') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': getCSRFToken()
            }
        });
    }
});

// ============================================================================
// ALERT/NOTIFICATION SYSTEM
// ============================================================================
function showAlert(type, message, duration = 5000) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create alert element
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show custom-alert`;
    alert.style.cssText = 'position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px;';
    
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alert);

    // Auto dismiss after duration
    if (duration > 0) {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, duration);
    }
}

// ============================================================================
// LOADING INDICATOR
// ============================================================================
function showLoading(element, text = 'Loading...') {
    const originalContent = element.innerHTML;
    element.setAttribute('data-original-content', originalContent);
    element.disabled = true;
    element.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${text}`;
    return originalContent;
}

function hideLoading(element) {
    const originalContent = element.getAttribute('data-original-content');
    if (originalContent) {
        element.innerHTML = originalContent;
        element.disabled = false;
        element.removeAttribute('data-original-content');
    }
}

// ============================================================================
// CART COUNT UPDATE
// ============================================================================
function updateCartCount() {
    fetch('/api/cart/count', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const cartCountElements = document.querySelectorAll('.cart-count, #cart-count');
        cartCountElements.forEach(element => {
            element.textContent = data.count || 0;
            if (data.count > 0) {
                element.style.display = 'flex';
            } else {
                element.style.display = 'none';
            }
        });
    })
    .catch(error => {
        console.error('Error updating cart count:', error);
    });
}

// ============================================================================
// FORM VALIDATION
// ============================================================================
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    // Add Bootstrap validation classes
    form.classList.add('was-validated');

    // Check validity
    return form.checkValidity();
}

// ============================================================================
// NUMBER FORMATTING
// ============================================================================
function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function formatNumber(number) {
    return parseFloat(number).toFixed(2);
}

// ============================================================================
// CONFIRMATION DIALOG
// ============================================================================
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// ============================================================================
// DEBOUNCE FUNCTION
// ============================================================================
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ============================================================================
// LOCAL STORAGE HELPERS
// ============================================================================
function setLocalStorage(key, value) {
    try {
        localStorage.setItem(key, JSON.stringify(value));
        return true;
    } catch (e) {
        console.error('Error saving to localStorage:', e);
        return false;
    }
}

function getLocalStorage(key) {
    try {
        const item = localStorage.getItem(key);
        return item ? JSON.parse(item) : null;
    } catch (e) {
        console.error('Error reading from localStorage:', e);
        return null;
    }
}

function removeLocalStorage(key) {
    try {
        localStorage.removeItem(key);
        return true;
    } catch (e) {
        console.error('Error removing from localStorage:', e);
        return false;
    }
}

// ============================================================================
// SEARCH FUNCTIONALITY
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');

    if (searchInput && searchBtn) {
        // Handle search button click
        searchBtn.addEventListener('click', function() {
            performSearch();
        });

        // Handle Enter key in search input
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });

        function performSearch() {
            const query = searchInput.value.trim();
            if (query.length > 0) {
                const branchId = typeof BRANCH_ID !== 'undefined' ? BRANCH_ID : '';
                window.location.href = `/products/search?q=${encodeURIComponent(query)}&branch_id=${branchId}`;
            }
        }
    }

    // Category filter buttons
    const categoryButtons = document.querySelectorAll('[data-category]');
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.dataset.category;
            
            // Update active state
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            // Filter products
            const categorySections = document.querySelectorAll('.category-section');
            if (category === 'all') {
                categorySections.forEach(section => {
                    section.style.display = 'block';
                });
            } else {
                categorySections.forEach(section => {
                    section.style.display = 'none';
                });
                
                // Show only matching category
                const targetSection = Array.from(categorySections).find(section => {
                    return section.dataset.category && 
                           section.dataset.category.toLowerCase().includes(category.toLowerCase());
                });
                if (targetSection) {
                    targetSection.style.display = 'block';
                }
            }
        });
    });
});

// ============================================================================
// SMOOTH SCROLL
// ============================================================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href !== '#!') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// ============================================================================
// FORM AUTO-DISMISS ALERTS
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 5000);
    });
});

// ============================================================================
// PREVENT DOUBLE FORM SUBMISSION
// ============================================================================
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn && !submitBtn.disabled) {
            showLoading(submitBtn, 'Processing...');
        }
    });
});

// ============================================================================
// TOOLTIP INITIALIZATION
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// ============================================================================
// EXPORT FUNCTIONS FOR GLOBAL USE
// ============================================================================
window.showAlert = showAlert;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.updateCartCount = updateCartCount;
window.validateForm = validateForm;
window.formatCurrency = formatCurrency;
window.formatNumber = formatNumber;
window.confirmAction = confirmAction;
window.debounce = debounce;