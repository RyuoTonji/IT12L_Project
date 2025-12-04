@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">
        <i class="fas fa-credit-card"></i> Checkout
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
                        <strong>Branch:</strong> {{ $branch->name }}
                        <br>
                        <small class="text-muted">{{ $branch->address }}</small>
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

        <!-- Delivery Information Form -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shipping-fast"></i> Delivery Information
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
                            <label for="address" class="form-label">
                                Delivery Address <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="3" 
                                      required>{{ old('address') }}</textarea>
                            <small class="form-text text-muted">
                                Please provide complete address including street, barangay, city
                            </small>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="place-order-btn">
                                <i class="fas fa-check-circle"></i> Place Order
                            </button>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Cart
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle text-info"></i> Payment Information
                    </h6>
                    <p class="card-text small text-muted mb-0">
                        Payment will be collected upon delivery. Cash only.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('checkout-form')?.addEventListener('submit', function(e) {
    const btn = document.getElementById('place-order-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
});
</script>
@endpush
@endsection