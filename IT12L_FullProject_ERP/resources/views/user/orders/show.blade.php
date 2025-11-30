@extends('layouts.app')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">My Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Order #{{ $order->id }}</li>
        </ol>
    </nav>

    <!-- Header: Order ID + Status Badge -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-receipt"></i> Order #{{ $order->id }}
        </h2>

        @php
            $statusBadges = [
                'pending'    => 'bg-warning',
                'confirmed'  => 'bg-info',
                'delivered'  => 'bg-success',
                'cancelled'  => 'bg-danger',
            ];
            $badgeClass = $statusBadges[$order->status] ?? 'bg-secondary';
        @endphp

        <span class="badge {{ $badgeClass }} fs-5 text-white">
            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
        </span>
    </div>

    <div class="row">
        <!-- Left Column: Items & Timeline -->
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-box"></i> Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">₱{{ number_format($item->price, 2) }}</td>
                                        <td class="text-end">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No items found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-primary fs-5 text-end">
                                        ₱{{ number_format($order->total_amount, 2) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Order Status Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Order Placed -->
                        <div class="timeline-item {{ in_array($order->status, ['pending', 'confirmed', 'delivered']) ? 'active' : '' }}">
                            <div class="timeline-icon bg-warning text-white">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Order Placed</h6>
                                <p class="text-muted small mb-0">
                                    {{ $order->ordered_at->format('F j, Y g:i A') }}
                                </p>
                            </div>
                        </div>

                        <!-- Confirmed -->
                        <div class="timeline-item {{ in_array($order->status, ['confirmed', 'delivered']) ? 'active' : '' }}">
                            <div class="timeline-icon bg-info text-white">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Order Confirmed</h6>
                                <p class="text-muted small mb-0">
                                    {{ in_array($order->status, ['confirmed', 'delivered']) ? 'Your order has been confirmed' : 'Waiting for confirmation' }}
                                </p>
                            </div>
                        </div>

                        <!-- Delivered -->
                        <div class="timeline-item {{ $order->status === 'delivered' ? 'active' : '' }}">
                            <div class="timeline-icon bg-success text-white">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Delivered</h6>
                                <p class="text-muted small mb-0">
                                    {{ $order->status === 'delivered' ? 'Order has been delivered' : 'Pending delivery' }}
                                </p>
                            </div>
                        </div>

                        <!-- Cancelled -->
                        @if($order->status === 'cancelled')
                        <div class="timeline-item active">
                            <div class="timeline-icon bg-danger text-white">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Order Cancelled</h6>
                                <p class="text-muted small mb-0">This order has been cancelled</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Delivery Info & Actions -->
        <div class="col-lg-4">
            <!-- Delivery Info -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-shipping-fast"></i> Delivery Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Customer Name:</strong><br>{{ $order->customer_name }}</p>
                    <p class="mb-2"><strong>Phone Number:</strong><br>{{ $order->customer_phone }}</p>
                    <p class="mb-0"><strong>Delivery Address:</strong><br>{{ nl2br(e($order->address)) }}</p>
                </div>
            </div>

            <!-- Branch Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-store"></i> Branch</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $order->branch->name }}</strong></p>
                    <small class="text-muted">{{ $order->branch->address }}</small>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-grid gap-2">
                @if($order->status === 'pending')
                    <button class="btn btn-danger btn-lg" id="cancel-order-btn" data-order-id="{{ $order->id }}">
                        <i class="fas fa-times"></i> Cancel Order
                    </button>
                @endif

                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 50px;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 30px;
        opacity: 0.5;
        transition: opacity 0.3s;
    }
    .timeline-item.active {
        opacity: 1;
    }
    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: -33px;
        top: 30px;
        bottom: -10px;
        width: 2px;
        background: #dee2e6;
    }
    .timeline-icon {
        position: absolute;
        left: -50px;
        top: 0;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('cancel-order-btn')?.addEventListener('click', function () {
        const orderId = this.dataset.orderId;

        if (!confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
            return;
        }

        fetch(`/orders/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Order cancelled successfully!');
                setTimeout(() => location.reload(), 1000);
            } else {
                alert(data.message || 'Failed to cancel order.');
            }
        })
        .catch(() => {
            alert('An error occurred. Please try again.');
        });
    });
</script>
@endpush