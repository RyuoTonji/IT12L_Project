@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <!-- Page Header -->
        <div class="order-detail-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-receipt"></i> Order #{{ $order->id }}</h2>
                    <p class="mb-0 text-white-50">
                        <i class="fas fa-calendar me-2"></i>
                        {{ \Carbon\Carbon::parse($order->created_at)->format('F j, Y \a\t g:i A') }}
                    </p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Order Items -->
            <div class="col-lg-8">
                <div class="card detail-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shopping-bag"></i> Order Items</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table detail-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $imagePath = null;
                                                        if ($item->product && $item->product->image) {
                                                            // Check if image is in storage
                                                            if (file_exists(public_path('storage/' . $item->product->image))) {
                                                                $imagePath = asset('storage/' . $item->product->image);
                                                            } 
                                                            // Check if image is in public/images
                                                            elseif (file_exists(public_path('images/' . $item->product->image))) {
                                                                $imagePath = asset('images/' . $item->product->image);
                                                            }
                                                        }
                                                    @endphp

                                                    @if($imagePath)
                                                        <img src="{{ $imagePath }}"
                                                            alt="{{ $item->product_name }}" class="product-image me-3">
                                                    @else
                                                        <div class="product-image-placeholder me-3">
                                                            <i class="fas fa-utensils"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $item->product_name }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="price-cell">₱{{ number_format($item->price, 2) }}</td>
                                            <td>
                                                <span class="quantity-badge">{{ $item->quantity }}</span>
                                            </td>
                                            <td class="subtotal-cell">₱{{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="total-row">
                                        <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                        <td class="total-amount">
                                            <strong>₱{{ number_format((float) $order->total_amount, 2) }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Details & Actions -->
            <div class="col-lg-4">
                <!-- Update Status -->
                <div class="card detail-card">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0 text-light">
                            <i class="fas fa-edit"></i> Update Status
                        </h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            @php
                                $flow = [
                                    'pending' => ['label' => 'Pending', 'class' => 'secondary'],
                                    'confirmed' => ['label' => 'Confirmed', 'class' => 'info'],
                                    'preparing' => ['label' => 'Preparing', 'class' => 'primary'],
                                    'ready' => ['label' => 'Ready for Pickup', 'class' => 'warning'],
                                    'picked up' => ['label' => 'Picked Up', 'class' => 'success'],
                                ];
                                $currentIndex = array_search($order->status, array_keys($flow));
                            @endphp


                            <label class="form-label fw-bold mb-2">Order Status</label>

                            <div class="d-grid gap-2">
                                @foreach($flow as $key => $data)
                                    @php
                                        $index = array_search($key, array_keys($flow));
                                        $locked = $index <= $currentIndex;
                                    @endphp

                                    <button type="submit" name="status" value="{{ $key }}" class="btn btn-{{ $data['class'] }}"
                                        @if($locked) disabled @endif>
                                        {{ $data['label'] }}
                                    </button>
                                @endforeach

                                {{-- Cancel button ONLY if NOT picked up --}}
                                @if(!in_array($order->status, ['picked up', 'cancelled']))
                                    <button type="submit" name="status" value="cancelled" class="btn btn-danger">
                                        Cancel Order
                                    </button>
                                @endif
                            </div>

                    </div>
                    </form>
                </div>
            </div>

            <!-- Order Details -->
            <div class="card detail-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="detail-item">
                        <span class="detail-label">Order ID:</span>
                        <span class="detail-value order-id">#{{ $order->id }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">
                            @php
                                $statusClass = [
                                    'pending' => 'pending',
                                    'confirmed' => 'confirmed',
                                    'preparing' => 'preparing',
                                    'ready' => 'ready',
                                    'picked up' => 'completed',
                                    'cancelled' => 'cancelled'
                                ];
                                $badgeClass = $statusClass[$order->status] ?? 'pending';
                            @endphp
                            <span class="status-badge {{ $badgeClass }}">
                                {{ $order->status === 'ready' ? 'Ready for Pickup' : ucfirst($order->status) }}
                            </span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Order Date:</span>
                        <span
                            class="detail-value">{{ \Carbon\Carbon::parse($order->created_at)->format('M j, Y g:i A') }}</span>
                    </div>
                    @if($order->approved_by)
                        <div class="detail-item">
                            <span class="detail-label">Approved By:</span>
                            <span class="detail-value text-success">
                                <i class="fas fa-check-circle me-1"></i>
                                {{ $order->approvedUser->name ?? 'Admin' }}
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Approved At:</span>
                            <span class="detail-value">
                                {{ \Carbon\Carbon::parse($order->approved_at)->format('M j, Y g:i A') }}
                            </span>
                        </div>
                    @endif
                    <div class="detail-item">
                        <span class="detail-label">Payment Method:</span>
                        <span class="detail-value">
                            @if($order->payment_method === 'qr_ph')
                                <i class="fas fa-qrcode text-primary me-1"></i> QRPh (Universal QR)
                            @elseif($order->payment_method === 'cash')
                                <i class="fas fa-money-bill-wave text-success me-1"></i> Cash on Pickup
                            @else
                                {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                            @endif
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Branch:</span>
                        <span class="detail-value">
                            <i class="fas fa-store text-muted me-1"></i>
                            {{ $order->branch->name ?? 'Unknown' }}
                        </span>
                    </div>
                    <div class="detail-item border-top pt-3 mt-3">
                        <span class="detail-label">Total Amount:</span>
                        <span
                            class="detail-value total-highlight">₱{{ number_format((float) $order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card detail-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="detail-item">
                        <span class="detail-label">Name:</span>
                        <span class="detail-value">{{ $order->user->name ?? $order->customer_name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value">
                            <i class="fas fa-envelope text-muted me-1"></i>
                            {{ $order->user->email ?? 'Guest Order' }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Phone:</span>
                        <span class="detail-value">
                            <i class="fas fa-phone text-muted me-1"></i>
                            {{ $order->user->phone ?? $order->customer_phone }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value">
                            <i class="fas fa-map-marker-alt text-muted me-1"></i>
                            {{ $order->address ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>



            @push('styles')
                <style>
                    /* Order Detail Header */
                    .order-detail-header {
                        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
                        color: white;
                        padding: 2rem;
                        border-radius: 12px;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    }

                    .order-detail-header h2 {
                        color: white;
                        font-weight: 700;
                    }

                    /* Detail Cards */
                    .detail-card {
                        border: none;
                        border-radius: 12px;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                        overflow: hidden;
                    }

                    .detail-card .card-header {
                        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
                        color: white;
                        padding: 1rem 1.5rem;
                        border-bottom: none;
                    }

                    .detail-card .card-header h5 {
                        margin: 0;
                        font-weight: 600;
                        color: white;
                    }

                    .detail-card .card-body {
                        padding: 1.5rem;
                    }

                    /* Detail Table */
                    .detail-table {
                        border-collapse: separate;
                        border-spacing: 0;
                    }

                    .detail-table thead {
                        background: #f8f9fa;
                    }

                    .detail-table thead th {
                        font-weight: 600;
                        color: #495057;
                        padding: 1rem;
                        border-bottom: 2px solid #dee2e6;
                        text-transform: uppercase;
                        font-size: 0.875rem;
                    }

                    .detail-table tbody tr {
                        border-bottom: 1px solid #f8f9fa;
                        transition: background-color 0.2s ease;
                    }

                    .detail-table tbody tr:hover {
                        background-color: rgba(165, 42, 42, 0.05);
                    }

                    .detail-table tbody td {
                        padding: 1rem;
                        vertical-align: middle;
                    }

                    .detail-table tfoot {
                        background: #f8f9fa;
                        border-top: 2px solid #dee2e6;
                    }

                    .detail-table tfoot td {
                        padding: 1rem;
                        font-size: 1.125rem;
                    }

                    /* Product Image */
                    .product-image {
                        width: 60px;
                        height: 60px;
                        object-fit: cover;
                        border-radius: 8px;
                        border: 2px solid #dee2e6;
                    }

                    .product-image-placeholder {
                        width: 60px;
                        height: 60px;
                        background: #f8f9fa;
                        border-radius: 8px;
                        border: 2px solid #dee2e6;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #6c757d;
                    }

                    /* Price Cells */
                    .price-cell,
                    .subtotal-cell {
                        font-weight: 600;
                        color: #495057;
                    }

                    .total-amount {
                        font-size: 1.25rem;
                        color: #A52A2A;
                    }

                    /* Quantity Badge */
                    .quantity-badge {
                        color: #000000ff;
                        padding: 0.25rem 0.75rem;
                        border-radius: 12px;
                        font-weight: 600;
                        display: inline-block;
                    }

                    /* Detail Items */
                    .detail-item {
                        display: flex;
                        justify-content: space-between;
                        padding: 0.75rem 0;
                        border-bottom: 1px solid #f8f9fa;
                    }

                    .detail-item:last-child {
                        border-bottom: none;
                        padding-bottom: 0;
                    }

                    .detail-label {
                        font-weight: 600;
                        color: #495057;
                    }

                    .detail-value {
                        color: #6c757d;
                        text-align: right;
                    }

                    .order-id {
                        color: #A52A2A !important;
                        font-weight: 700;
                    }

                    .total-highlight {
                        font-size: 1.25rem;
                        font-weight: 700;
                        color: #000000ff !important;
                    }

                    /* Status Badges */
                    .status-badge {
                        padding: 0.4rem 0.9rem;
                        border-radius: 20px;
                        font-weight: 600;
                        font-size: 0.875rem;
                        display: inline-block;
                        text-transform: capitalize;
                    }

                    .status-badge.pending {
                        color: #ffc107;
                    }

                    .status-badge.confirmed {
                        color: #0aa2c0;
                    }

                    .status-badge.preparing {
                        color: #6610f2;
                    }

                    .status-badge.ready {
                        color: #fd7e14;
                    }

                    .status-badge.completed {
                        color: #198754;
                    }

                    .status-badge.cancelled {
                        color: #bb2d3b;
                    }

                    /* Status Select */
                    .status-select {
                        border: 2px solid #dee2e6;
                        border-radius: 8px;
                        padding: 0.625rem 1rem;
                        font-weight: 600;
                        transition: all 0.3s ease;
                    }

                    .status-select:focus {
                        border-color: #A52A2A;
                        box-shadow: 0 0 0 0.25rem rgba(165, 42, 42, 0.15);
                    }

                    /* Total Row */
                    .total-row {
                        background: #f8f9fa;
                        font-size: 1.125rem;
                    }

                    .total-row td {
                        padding: 1.25rem 1rem !important;
                    }

                    /* Responsive */
                    @media (max-width: 992px) {
                        .order-detail-header {
                            padding: 1.5rem;
                        }

                        .order-detail-header h2 {
                            font-size: 1.5rem;
                        }

                        .detail-card .card-body {
                            padding: 1rem;
                        }
                    }

                    @media (max-width: 576px) {
                        .status-badge {
                            font-size: 0.75rem;
                            padding: 0.3rem 0.6rem;
                        }
                    }
                </style>
            @endpush
@endsection