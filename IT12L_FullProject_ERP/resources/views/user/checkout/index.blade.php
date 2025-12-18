@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">
        <i class="fas fa-shopping-bag"></i> Checkout - Pickup Order
    </h2>

    <div class="row">
        <!-- Order Summary -->
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-bag"></i> Order Items
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Pickup Branch:</strong> {{ $branch->name }}
                        <br>
                        <small class="text-muted">
                            <i class="fas fa-map-marker-alt"></i> {{ $branch->address }}
                        </small>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>₱{{ number_format($item->product->price, 2) }}</td>
                                        <td>₱{{ number_format($item->product->price * $item->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-primary">₱{{ number_format($total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pickup Information Form -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user"></i> Customer Information
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('checkout.process') }}" id="checkout-form">
                        @csrf

                        <div class="mb-3">
                            <label for="customer_name" class="form-label">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('customer_name') is-invalid @enderror" 
                                   id="customer_name" 
                                   name="customer_name" 
                                   value="{{ old('customer_name', $user->name) }}" 
                                   required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">
                                Phone Number <span class="text-danger">*</span>
                            </label>
                            <input type="tel" 
                                   class="form-control @error('customer_phone') is-invalid @enderror" 
                                   id="customer_phone" 
                                   name="customer_phone" 
                                   value="{{ old('customer_phone', $user->phone ?? '') }}" 
                                   required>
                            <small class="form-text text-muted">Format: 09171234567</small>
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">
                                Special Instructions (Optional)
                            </label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="Any special requests or instructions...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success btn-lg" id="place-order-btn">
                                <i class="fas fa-check-circle"></i> Place Pickup Order
                            </button>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Cart
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Pickup Information -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle text-info"></i> Pickup Information
                    </h6>
                    <ul class="mb-0 small text-muted">
                        <li>Order will be ready for pickup within 30-45 minutes</li>
                        <li>Please show order ID when picking up</li>
                        <li>Payment will be collected at pickup (Cash/Gcash)</li>
                        <li>You will receive a confirmation notification</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmOrderModal" tabindex="-1" aria-labelledby="confirmOrderModalLabel" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="confirmOrderModalLabel">
                    <i class="fas fa-question-circle"></i> Confirm Your Order
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="modal-close-btn"></button>
            </div>
            <div class="modal-body" id="modal-body-content">
                <div class="text-center mb-3">
                    <i class="fas fa-store text-success" style="font-size: 3rem;"></i>
                </div>
                
                <h6 class="text-center mb-3">Are you sure you want to place this pickup order?</h6>
                
                <div class="border border-info rounded p-3 mb-3" id="order-summary-section" style="background-color: #d1ecf1; display: block !important; opacity: 1 !important; visibility: visible !important;">
                    <strong><i class="fas fa-info-circle text-info"></i> Order Summary:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>Branch:</strong> {{ $branch->name }}</li>
                        <li><strong>Items:</strong> {{ $items->count() }} item(s)</li>
                        <li><strong>Total:</strong> ₱{{ number_format($total, 2) }}</li>
                        <li><strong>Payment:</strong> Cash/Gcash on Pickup</li>
                    </ul>
                </div>
                
                <p class="text-muted small mb-0" id="pickup-info-section">
                    <i class="fas fa-clock"></i> Your order will be ready in 30-45 minutes. 
                    Please show order ID when picking up your order.
                </p>
            </div>
            <div class="modal-footer" id="modal-footer-section">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancel-btn">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-success" id="confirm-order-btn">
                    <i class="fas fa-check-circle"></i> Yes, Place Order
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    'use strict';
    
    const form = document.getElementById('checkout-form');
    const placeOrderBtn = document.getElementById('place-order-btn');
    const confirmOrderBtn = document.getElementById('confirm-order-btn');
    const confirmModal = document.getElementById('confirmOrderModal');
    const modalCloseBtn = document.getElementById('modal-close-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const modal = new bootstrap.Modal(confirmModal, {
        backdrop: 'static',
        keyboard: false
    });
    
    let isSubmitting = false;
    
    // Prevent Bootstrap from auto-closing alerts
    document.addEventListener('DOMContentLoaded', function() {
        const orderSummary = document.getElementById('order-summary-section');
        if (orderSummary) {
            // Remove any auto-dismiss behavior
            orderSummary.classList.remove('fade');
            orderSummary.style.display = 'block';
            orderSummary.style.opacity = '1';
            orderSummary.style.visibility = 'visible';
        }
    });
    
    // Show confirmation modal when "Place Pickup Order" is clicked
    placeOrderBtn?.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Validate form before showing modal
        if (form.checkValidity()) {
            modal.show();
        } else {
            // Trigger HTML5 validation
            form.reportValidity();
        }
    });
    
    // Submit form when user confirms in modal
    confirmOrderBtn?.addEventListener('click', function() {
        if (isSubmitting) return;
        isSubmitting = true;
        
        // Disable confirm button and show processing state
        confirmOrderBtn.disabled = true;
        confirmOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing Order...';
        
        // Disable place order button
        placeOrderBtn.disabled = true;
        placeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        
        // Disable close button
        if (modalCloseBtn) {
            modalCloseBtn.disabled = true;
            modalCloseBtn.style.pointerEvents = 'none';
            modalCloseBtn.style.opacity = '0.5';
        }
        
        // Disable cancel button
        if (cancelBtn) {
            cancelBtn.disabled = true;
            cancelBtn.classList.add('disabled');
        }
        
        // Submit form via AJAX
        submitOrderViaAjax();
    });
    
    // AJAX form submission
    function submitOrderViaAjax() {
        // Get form data
        const formData = new FormData(form);
        
        // Send AJAX request
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Network response was not ok');
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Order submitted successfully:', data);
            
            // Keep order summary visible, just add success message
            showSuccessState(data);
            
            // Redirect after 2 seconds
            setTimeout(function() {
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    // Fallback redirect
                    window.location.href = '{{ route("orders.index") }}';
                }
            }, 2000);
        })
        .catch(error => {
            console.error('Error submitting order:', error);
            showErrorState(error);
            isSubmitting = false;
        });
    }
    
    // Show success state in modal - KEEP ORDER SUMMARY
    function showSuccessState(data) {
        const modalBody = document.getElementById('modal-body-content');
        const modalFooter = document.getElementById('modal-footer-section');
        const orderSummary = document.getElementById('order-summary-section');
        const pickupInfo = document.getElementById('pickup-info-section');
        
        if (modalBody) {
            // Ensure order summary stays visible and doesn't fade
            if (orderSummary) {
                orderSummary.style.display = 'block';
                orderSummary.style.opacity = '1';
                orderSummary.style.visibility = 'visible';
                orderSummary.classList.remove('fade');
                orderSummary.style.transition = 'none';
            }
            
            // Keep pickup info visible too
            if (pickupInfo) {
                pickupInfo.style.display = 'block';
                pickupInfo.style.opacity = '1';
                pickupInfo.style.visibility = 'visible';
            }
            
            // Prepend success message ABOVE the order summary
            const successMessage = document.createElement('div');
            successMessage.className = 'text-center mb-4';
            successMessage.innerHTML = `
                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                <h5 class="mt-3 mb-2 text-success">Order Placed Successfully!</h5>
                <p class="text-muted">Redirecting to order confirmation...</p>
                <div class="spinner-border text-success mt-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            `;
            
            // Insert at the beginning
            modalBody.insertBefore(successMessage, modalBody.firstChild);
            
            // Keep the order summary visible - just hide the store icon and question
            const storeIcon = modalBody.querySelector('.fa-store');
            if (storeIcon && storeIcon.parentElement) {
                storeIcon.parentElement.style.display = 'none';
            }
            
            const questionText = modalBody.querySelector('h6');
            if (questionText) {
                questionText.style.display = 'none';
            }
        }
        
        // Hide footer buttons
        if (modalFooter) {
            modalFooter.style.display = 'none';
        }
    }
    
    // Show error state in modal - KEEP ORDER SUMMARY
    function showErrorState(error) {
        const modalBody = document.getElementById('modal-body-content');
        const orderSummary = document.getElementById('order-summary-section');
        const pickupInfo = document.getElementById('pickup-info-section');
        
        if (modalBody) {
            // Ensure order summary stays visible and doesn't fade
            if (orderSummary) {
                orderSummary.style.display = 'block';
                orderSummary.style.opacity = '1';
                orderSummary.style.visibility = 'visible';
                orderSummary.classList.remove('fade');
                orderSummary.style.transition = 'none';
            }
            
            // Keep pickup info visible too
            if (pickupInfo) {
                pickupInfo.style.display = 'block';
                pickupInfo.style.opacity = '1';
                pickupInfo.style.visibility = 'visible';
            }
            
            // Prepend error message ABOVE the order summary
            const errorMessage = document.createElement('div');
            errorMessage.className = 'text-center mb-4';
            errorMessage.innerHTML = `
                <i class="fas fa-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                <h5 class="mt-3 mb-2 text-danger">Order Failed</h5>
                <p class="text-danger">${error.message || 'Failed to place order. Please try again.'}</p>
            `;
            
            // Insert at the beginning
            modalBody.insertBefore(errorMessage, modalBody.firstChild);
        }
        
        // Re-enable buttons
        confirmOrderBtn.disabled = false;
        confirmOrderBtn.innerHTML = '<i class="fas fa-check-circle"></i> Yes, Place Order';
        placeOrderBtn.disabled = false;
        placeOrderBtn.innerHTML = '<i class="fas fa-check-circle"></i> Place Pickup Order';
        
        if (modalCloseBtn) {
            modalCloseBtn.disabled = false;
            modalCloseBtn.style.pointerEvents = 'auto';
            modalCloseBtn.style.opacity = '1';
        }
        
        if (cancelBtn) {
            cancelBtn.disabled = false;
            cancelBtn.classList.remove('disabled');
        }
    }
    
    // Prevent modal from closing during submission
    confirmModal?.addEventListener('hide.bs.modal', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }
    });
})();
</script>
@endpush
@endsection