@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-receipt"></i> Order #{{ $order->id }}</h2>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag"></i> Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderItems as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product_image)
                                                <img src="{{ asset('storage/' . $item->product_image) }}" 
                                                     alt="{{ $item->product_name }}" 
                                                     style="width: 50px; height: 50px; object-fit: cover;"
                                                     class="me-3">
                                            @endif
                                            <div>{{ $item->product_name }}</div>
                                        </div>
                                    </td>
                                    <td>₱{{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>₱{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-active">
                                    <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                    <td><strong>₱{{ number_format($order->total_amount, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Order Details -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Order Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Order ID:</dt>
                        <dd class="col-sm-7">#{{ $order->id }}</dd>

                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            @php
                                $statusClass = [
                                    'pending' => 'warning',
                                    'confirmed' => 'info',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                $badgeClass = $statusClass[$order->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </dd>

                        <dt class="col-sm-5">Order Date:</dt>
                        <dd class="col-sm-7">{{ \Carbon\Carbon::parse($order->created_at)->format('M j, Y g:i A') }}</dd>

                        <dt class="col-sm-5">Branch:</dt>
                        <dd class="col-sm-7">{{ $order->branch_name }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Customer Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8">{{ $order->user_name }}</dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">{{ $order->user_email }}</dd>

                        @if($order->user_phone)
                        <dt class="col-sm-4">Phone:</dt>
                        <dd class="col-sm-8">{{ $order->user_phone }}</dd>
                        @endif

                        <dt class="col-sm-4">Address:</dt>
                        <dd class="col-sm-8">{{ $order->delivery_address }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Update Status -->
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Update Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="status" class="form-label">Order Status</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection