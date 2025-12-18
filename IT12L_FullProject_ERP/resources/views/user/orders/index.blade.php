@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">
        <i class="fas fa-box"></i> My Orders
    </h2>

    @if($orders->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h4>No orders yet</h4>
                <p class="text-muted">Start ordering your favorite meals!</p>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="fas fa-utensils"></i> Browse Menu
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($orders as $order)
                <div class="col-12">
                    <div class="card order-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-9">
                                    <h5 class="card-title mb-3">
                                        Order #{{ $order->id }}
                                        @php
                                            $statusClasses = [
                                                'pending' => 'warning',
                                                'confirmed' => 'info',
                                                'preparing' => 'primary',
                                                'ready' => 'orange',
                                                'picked up' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $textClass = $statusClasses[$order->status] ?? 'secondary';
                                        @endphp
                                        <span class="text-{{ $textClass }} fw-semibold">
                                            {{ $order->status === 'ready' ? 'Ready for Pickup' : ucfirst($order->status) }}
                                        </span>
                                    </h5>
                                    
                                    <p class="card-text text-muted mb-2">
                                        <i class="fas fa-store me-1"></i> {{ $order->branch->name }}
                                    </p>
                                    
                                    <p class="card-text text-muted mb-3">
                                        <i class="fas fa-calendar me-1"></i> 
                                        {{ $order->ordered_at->format('F j, Y g:i A') }}
                                    </p>

                                    <p class="card-text mb-0">
                                        <strong>Total:</strong> 
                                        <span class="text-dark fs-5 fw-bold">
                                            ₱{{ number_format($order->total_amount, 2) }}
                                        </span>
                                    </p>
                                </div>

                                <div class="col-md-3 d-flex flex-column justify-content-center align-items-stretch gap-2">
                                    <a href="{{ route('orders.show', $order->id) }}" 
                                    class="btn btn-primary d-flex align-items-center justify-content-center gap-2 px-3 py-2 text-nowrap">
                                        <i class="fas fa-eye"></i>
                                        <span>View Details</span>
                                    </a>

                                    @if($order->status === 'pending')
                                        <button class="btn btn-danger cancel-order d-flex align-items-center justify-content-center gap-2 px-3 py-2 text-nowrap"
                                                data-order-id="{{ $order->id }}">
                                            <i class="fas fa-times"></i>
                                            <span>Cancel Order</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('styles')
<style>
    .order-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #e0e0e0;
    }
    
    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    
    .order-card .btn {
        white-space: nowrap;
    }

    /* Custom orange color for "ready" status */
    .text-orange {
        color: #fd7e14 !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.cancel-order').forEach(button => {
    button.addEventListener('click', function() {
        const orderId = this.dataset.orderId;
        
        if (confirm('Are you sure you want to cancel this order?')) {
            cancelOrder(orderId);
        }
    });
});

function cancelOrder(orderId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    fetch(`/orders/${orderId}/cancel`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            alert(data.message || 'Failed to cancel order');
        }
    })
    .catch(error => {
        alert('An error occurred. Please try again.');
    });
}
</script>
@endpush
@endsection