/**
 * FOOD ORDERING SYSTEM - ADMIN PANEL JAVASCRIPT
 * Handles admin-specific functionality
 */

// ============================================================================
// DELETE CONFIRMATION
// ============================================================================
function confirmDelete(itemName, deleteUrl, itemType = 'item') {
    if (confirm(`Are you sure you want to delete this ${itemType}: ${itemName}?`)) {
        deleteItem(deleteUrl);
    }
}

function deleteItem(url) {
    const csrfToken = getCSRFToken();

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message || 'Item deleted successfully');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.error || 'Failed to delete item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred. Please try again.');
    });
}

// ============================================================================
// UPDATE ORDER STATUS
// ============================================================================
function updateOrderStatus(orderId, newStatus) {
    const csrfToken = getCSRFToken();

    fetch(`/admin/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': csrfToken
        },
        body: new URLSearchParams({
            status: newStatus,
            csrf_token: csrfToken
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.error || 'Failed to update order status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred. Please try again.');
    });
}

// ============================================================================
// TOGGLE PRODUCT AVAILABILITY
// ============================================================================
function toggleProductAvailability(productId, isAvailable) {
    const csrfToken = getCSRFToken();

    fetch(`/admin/products/${productId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': csrfToken
        },
        body: new URLSearchParams({
            is_available: isAvailable ? 1 : 0,
            csrf_token: csrfToken
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
        } else {
            showAlert('danger', data.error || 'Failed to update availability');
            // Revert checkbox state
            const checkbox = document.querySelector(`input[data-product-id="${productId}"]`);
            if (checkbox) {
                checkbox.checked = !isAvailable;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred. Please try again.');
    });
}

// ============================================================================
// IMAGE PREVIEW
// ============================================================================
function previewImage(input, previewId = 'image-preview') {
    const preview = document.getElementById(previewId);
    const file = input.files[0];

    if (file) {
        // Validate file type
        if (!file.type.match('image.*')) {
            showAlert('danger', 'Please select a valid image file');
            input.value = '';
            return;
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            showAlert('danger', 'Image size must be less than 5MB');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

function removeImagePreview(previewId = 'image-preview', inputId = 'image') {
    const preview = document.getElementById(previewId);
    const input = document.getElementById(inputId);
    
    if (preview) {
        preview.src = '';
        preview.style.display = 'none';
    }
    
    if (input) {
        input.value = '';
    }
}

// ============================================================================
// TABLE SEARCH/FILTER
// ============================================================================
function filterTable(inputId, tableId, columnIndex = 0) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    const filter = input.value.toUpperCase();
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const cell = rows[i].getElementsByTagName('td')[columnIndex];
        if (cell) {
            const textValue = cell.textContent || cell.innerText;
            if (textValue.toUpperCase().indexOf(filter) > -1) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
}

// ============================================================================
// STATISTICS LOADER
// ============================================================================
function loadStatistics() {
    fetch('/api/admin/orders/statistics')
        .then(response => response.json())
        .then(data => {
            // Update statistics cards
            updateStatCard('total-orders', data.total_orders);
            updateStatCard('total-revenue', '₱' + formatNumber(data.total_revenue));
            updateStatCard('pending-orders', data.pending_orders);
            updateStatCard('confirmed-orders', data.confirmed_orders);
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
        });
}

function updateStatCard(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = value;
    }
}

// ============================================================================
// FORM VALIDATION
// ============================================================================
function validateAdminForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    // Check all required fields
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
    });

    if (!isValid) {
        showAlert('danger', 'Please fill in all required fields');
    }

    return isValid;
}

// ============================================================================
// BULK ACTIONS
// ============================================================================
function selectAllCheckboxes(selectAllCheckbox, checkboxClass) {
    const checkboxes = document.querySelectorAll(`.${checkboxClass}`);
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    updateBulkActionButtons();
}

function updateBulkActionButtons() {
    const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
    const bulkActionButtons = document.querySelectorAll('.bulk-action-btn');
    
    if (checkedBoxes.length > 0) {
        bulkActionButtons.forEach(btn => btn.disabled = false);
    } else {
        bulkActionButtons.forEach(btn => btn.disabled = true);
    }
}

function getSelectedItems() {
    const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
    return Array.from(checkedBoxes).map(checkbox => checkbox.value);
}

// ============================================================================
// EXPORT FUNCTIONS
// ============================================================================
function exportTableToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tr');
    const csv = [];

    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        
        cols.forEach(col => {
            // Remove action buttons column
            if (!col.classList.contains('no-export')) {
                rowData.push('"' + col.textContent.trim().replace(/"/g, '""') + '"');
            }
        });
        
        if (rowData.length > 0) {
            csv.push(rowData.join(','));
        }
    });

    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}

// ============================================================================
// EVENT LISTENERS
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    
    // Order status change
    document.querySelectorAll('.order-status-select').forEach(select => {
        select.addEventListener('change', function() {
            const orderId = this.dataset.orderId;
            const newStatus = this.value;
            
            if (confirm('Change order status to ' + newStatus + '?')) {
                updateOrderStatus(orderId, newStatus);
            } else {
                // Revert selection
                this.value = this.dataset.originalStatus;
            }
        });
    });

    // Product availability toggle
    document.querySelectorAll('.availability-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const isAvailable = this.checked;
            toggleProductAvailability(productId, isAvailable);
        });
    });

    // Image upload preview
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            previewImage(this);
        });
    });

    // Delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const itemName = this.dataset.itemName;
            const deleteUrl = this.dataset.deleteUrl;
            const itemType = this.dataset.itemType || 'item';
            confirmDelete(itemName, deleteUrl, itemType);
        });
    });

    // Table search
    const searchInputs = document.querySelectorAll('.table-search');
    searchInputs.forEach(input => {
        input.addEventListener('keyup', debounce(function() {
            const tableId = this.dataset.tableId;
            const columnIndex = this.dataset.columnIndex || 0;
            filterTable(this.id, tableId, columnIndex);
        }, 300));
    });

    // Select all checkbox
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            selectAllCheckboxes(this, 'item-checkbox');
        });
    }

    // Item checkboxes
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionButtons);
    });

    // Form validation
    document.querySelectorAll('form.admin-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateAdminForm(this.id)) {
                e.preventDefault();
            }
        });
    });

    // Auto-refresh statistics (every 30 seconds)
    if (document.getElementById('total-orders')) {
        setInterval(loadStatistics, 30000);
    }
});

// ============================================================================
// EXPORT FUNCTIONS
// ============================================================================
window.confirmDelete = confirmDelete;
window.updateOrderStatus = updateOrderStatus;
window.toggleProductAvailability = toggleProductAvailability;
window.previewImage = previewImage;
window.removeImagePreview = removeImagePreview;
window.filterTable = filterTable;
window.exportTableToCSV = exportTableToCSV;