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
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="card-title mb-2">
                                        Order #{{ $order->id }}
                                        @php
                                            $statusClasses = [
                                                'pending' => 'warning',
                                                'confirmed' => 'info',
                                                'delivered' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $badgeClass = $statusClasses[$order->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </h5>
                                    
                                    <p class="card-text text-muted mb-2">
                                        <i class="fas fa-store"></i> {{ $order->branch->name }}
                                        <br>
                                        <i class="fas fa-calendar"></i> 
                                        {{ $order->ordered_at->format('F j, Y g:i A') }}
                                    </p>

                                    <p class="card-text mb-0">
                                        <strong>Total:</strong> 
                                        <span class="text-primary fs-5">
                                            ₱{{ number_format($order->total_amount, 2) }}
                                        </span>
                                    </p>
                                </div>

                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary mb-2">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    
                                    @if($order->status === 'pending')
                                        <button class="btn btn-danger cancel-order" 
                                                data-order-id="{{ $order->id }}">
                                            <i class="fas fa-times"></i> Cancel Order
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