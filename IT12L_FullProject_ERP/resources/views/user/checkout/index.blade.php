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

                        <div class="mb-3">
                            <label class="form-label">
                                Payment Method <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check border rounded p-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="cash" checked>
                                    <label class="form-check-label w-100" for="payment_cash">
                                        <i class="fas fa-money-bill-wave text-success"></i>
                                        <strong>Cash on Pickup</strong>
                                        <br><small class="text-muted">Pay when you pick up your order</small>
                                    </label>
                                </div>
                                <div class="form-check border rounded p-3 @if(old('payment_method') == 'qr_ph') border-primary bg-light @endif">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_qrph" value="qr_ph" {{ old('payment_method') == 'qr_ph' ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="payment_qrph">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-qrcode text-primary fa-2x me-3"></i>
                                            <div>
                                                <strong>QRPh (Universal QR)</strong>
                                                <br><small class="text-muted">Pay via GCash, Maya, ShopeePay, or any Bank App</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
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
                        <li>Order will be ready for pickup within 15-20 minutes</li>
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
                        <li><strong>Payment:</strong> <span id="summary-payment-method">Cash/Gcash on Pickup</span></li>
                    </ul>
                </div>

                {{-- PayMongo QR Code Section (Hidden by Default) --}}
                <div id="paymongo-qr-section" style="display: none;" class="text-center mb-3">
                    <h5 class="fw-bold mb-3" id="qr-section-title">Scan QRPh to Pay</h5>

                    <div class="mb-3 px-2">
                        <p class="small text-dark mb-0">
                            <i class="fas fa-info-circle text-primary"></i>
                            Please scan this using your <strong>GCash, Maya, or Bank App</strong>.
                        </p>
                        <p class="small text-danger mb-3">
                            <strong>Note:</strong> Do not use your phone's default camera or Google Lens.
                        </p>
                    </div>

                    <div class="qr-container p-3 border rounded bg-white d-inline-block shadow-sm">
                        <div id="paymongo-qrcode" style="width: 200px; height: 200px; margin: 0 auto;">
                            <!-- QR code will be generated here after order creation -->
                        </div>
                        <p class="small text-muted mt-3 mb-0">
                            <strong>Amount: ₱{{ number_format($total, 2) }}</strong><br>
                            Powered by <span class="text-primary fw-bold">PayMongo</span>
                        </p>
                    </div>

                    <div id="qr-status-section" class="mt-4">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <div class="spinner-grow spinner-grow-sm text-warning" role="status"></div>
                            <span class="badge bg-warning text-dark px-3 py-2" id="qr-badge">Waiting for Payment...</span>
                        </div>
                        <p class="small text-muted mt-2">Please do not close this window until payment is confirmed.</p>
                    </div>
                </div>

                <p class="text-muted small mb-0" id="pickup-info-section">
                    <i class="fas fa-clock"></i> Your order will be ready in 15-20 minutes.
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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
                const paymentMethod = form.querySelector('input[name="payment_method"]:checked').value;
                const summaryPaymentMethod = document.getElementById('summary-payment-method');

                // Update payment method display in summary
                if (paymentMethod === 'qr_ph') {
                    summaryPaymentMethod.innerText = 'QRPh (Universal QR)';
                } else {
                    summaryPaymentMethod.innerText = 'Cash on Pickup';
                }

                // Don't show QR section yet - will show after order is created
                const paymongoQrSection = document.getElementById('paymongo-qr-section');
                paymongoQrSection.style.display = 'none';

                modal.show();
            } else {
                // Trigger HTML5 validation
                form.reportValidity();
            }
        });

        // qrcode.js instance
        let qrcodeInstance = null;

        // AJAX Submission handler
        confirmOrderBtn?.addEventListener('click', function() {
            if (confirmOrderBtn.disabled) return;

            const formData = new FormData(form);

            // Show loading state
            confirmOrderBtn.disabled = true;
            confirmOrderBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

            placeOrderBtn.disabled = true;
            placeOrderBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

            fetch("{{ route('checkout.process') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.payment_method === 'qr_ph') {
                            console.log('Generating QRPh display...');

                            const qrTitle = document.getElementById('qr-section-title');
                            qrTitle.innerText = 'Scan QRPh to Pay';

                            const qrContainer = document.getElementById('paymongo-qrcode');
                            qrContainer.innerHTML = ''; // Clear previous

                            // Check if it's a data URL (image) or raw data (string)
                            if (data.checkout_url && data.checkout_url.startsWith('data:image')) {
                                console.log('Displaying direct images from PayMongo');
                                qrContainer.innerHTML = `<img src="${data.checkout_url}" alt="Payment QR" style="width: 200px; height: 200px; object-fit: contain;">`;
                            } else if (data.checkout_url) {
                                try {
                                    if (typeof QRCode !== 'undefined') {
                                        new QRCode(qrContainer, {
                                            text: data.checkout_url,
                                            width: 200,
                                            height: 200,
                                            colorDark: "#000000",
                                            colorLight: "#ffffff",
                                            correctLevel: QRCode.CorrectLevel.H
                                        });
                                        console.log('Client-side QR generated successfully');
                                    } else {
                                        throw new Error('QRCode library not loaded');
                                    }
                                } catch (e) {
                                    console.error('QR Generation Error:', e);
                                    const qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" + encodeURIComponent(data.checkout_url);
                                    qrContainer.innerHTML = `<img src="${qrUrl}" alt="Payment QR" style="width: 200px; height: 200px;">`;
                                }
                            } else {
                                qrContainer.innerHTML = '<div class="alert alert-danger p-2 small">Error: QR data missing</div>';
                            }

                            // NOW show the QR section
                            const paymongoQrSection = document.getElementById('paymongo-qr-section');
                            paymongoQrSection.style.display = 'block';

                            // Update UI to show polling status
                            const statusBadge = document.getElementById('qr-badge');
                            statusBadge.className = 'badge bg-warning text-dark';
                            statusBadge.innerText = 'Waiting for Payment (QRPh)...';

                            // Start Polling
                            startPaymentPolling(data.order_id);
                        } else {
                            // Direct redirect for Cash
                            window.location.href = data.redirect_url;
                        }
                    } else {
                        alert(data.message || 'Error processing order');
                        resetButtons();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An unexpected error occurred. Please try again.');
                    resetButtons();
                });
        });

        function resetButtons() {
            confirmOrderBtn.disabled = false;
            confirmOrderBtn.innerHTML = '<i class="fas fa-check-circle"></i> Yes, Place Order';
            placeOrderBtn.disabled = false;
            placeOrderBtn.innerHTML = '<i class="fas fa-check-circle"></i> Place Pickup Order';
        }

        function startPaymentPolling(orderId) {
            const pollInterval = setInterval(() => {
                fetch("{{ route('checkout.check_status') }}?order_id=" + orderId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.status === 'paid') {
                            clearInterval(pollInterval);
                            const statusBadge = document.getElementById('qr-badge');
                            statusBadge.className = 'badge bg-success';
                            statusBadge.innerText = 'Payment Successful! Redirecting...';

                            setTimeout(() => {
                                window.location.href = "{{ route('checkout.confirm') }}?order_id=" + orderId;
                            }, 2000);
                        }
                    })
                    .catch(error => console.error('Polling error:', error));
            }, 3000); // Poll every 3 seconds
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