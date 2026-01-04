/**
 * Main JavaScript file for global functionality
 */

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Confirm delete actions
document.addEventListener('DOMContentLoaded', function() {
    const deleteForms = document.querySelectorAll('form[data-confirm]');
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const message = this.dataset.confirm || 'Are you sure?';
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    });
});

// Number formatting helper
window.formatCurrency = function(amount) {
    return '₱' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
};

// Show loading spinner
window.showLoading = function(element) {
    if (typeof element === 'string') {
        element = document.querySelector(element);
    }
    
    if (element) {
        element.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';
        element.disabled = true;
    }
};

// Hide loading spinner
window.hideLoading = function(element, originalText) {
    if (typeof element === 'string') {
        element = document.querySelector(element);
    }
    
    if (element) {
        element.innerHTML = originalText;
        element.disabled = false;
    }
};

console.log('Main.js loaded');