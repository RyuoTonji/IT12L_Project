@extends('layouts.app')

@section('content')
<div class="container my-4">
    <!-- Page Header -->
    <div class="customer-detail-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1"><i class="fas fa-user"></i> Customer Details</h2>
                <p class="mb-0 text-white-50">
                    <i class="fas fa-id-card me-2"></i>
                    Customer ID: #{{ $customer->id }}
                </p>
            </div>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Back to Customers
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Customer Info & Statistics -->
        <div class="col-lg-4">
            <!-- Customer Information -->
            <div class="card detail-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="detail-item">
                        <span class="detail-label">Customer ID:</span>
                        <span class="detail-value customer-id">#{{ $customer->id }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Name:</span>
                        <span class="detail-value">
                            <i class="fas fa-user text-muted me-1"></i>
                            {{ $customer->name }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value">
                            <i class="fas fa-envelope text-muted me-1"></i>
                            {{ $customer->email }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Phone:</span>
                        <span class="detail-value">
                            <i class="fas fa-phone text-muted me-1"></i>
                            {{ $customer->phone ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value">
                            <i class="fas fa-map-marker-alt text-muted me-1"></i>
                            {{ $customer->address ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">
                            <span class="status-badge {{ $customer->is_active ? 'active' : 'inactive' }}">
                                {{ $customer->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Registered:</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($customer->created_at)->format('M j, Y g:i A') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Last Updated:</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($customer->updated_at)->format('M j, Y g:i A') }}</span>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card detail-card mb-4">
                <div class="card-header bg-success">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Orders</div>
                            <div class="stat-value">{{ number_format($totalOrders) }}</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-peso-sign"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Spent</div>
                            <div class="stat-value">₱{{ number_format($totalSpent, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card detail-card">
                <div class="card-header w-100">
                    <h5 class="mb-0 text-light"><i class="fas fa-tools"></i> Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-warning w-100">
                            <i class="fas fa-edit"></i> Edit Customer
                        </a>
                        
                        <form action="{{ route('admin.customers.toggleStatus', $customer->id) }}" 
                              method="POST"
                              onsubmit="return confirm('Are you sure you want to {{ $customer->is_active ? 'disable' : 'enable' }} this account?');">
                            @csrf
                            <button type="submit" class="btn btn-{{ $customer->is_active ? 'secondary' : 'success' }} w-100">
                                <i class="fas fa-{{ $customer->is_active ? 'ban' : 'check' }}"></i> 
                                {{ $customer->is_active ? 'Disable Account' : 'Enable Account' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Order History -->
        <div class="col-lg-8">
            <div class="card detail-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Order History</h5>
                    @if($orders->isNotEmpty())
                        <span class="badge bg-light text-dark">{{ $orders->total() }} Orders</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($orders->isEmpty())
                        <div class="empty-state">
                            <i class="fas fa-shopping-bag"></i>
                            <h4>No Orders Yet</h4>
                            <p>This customer hasn't placed any orders.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table detail-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Branch</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            <span class="order-id">#{{ $order->id }}</span>
                                        </td>
                                        <td>
                                            <i class="fas fa-store text-muted me-1"></i>
                                            {{ $order->branch_name }}
                                        </td>
                                        <td class="price-cell">₱{{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'pending' => 'pending',
                                                    'confirmed' => 'confirmed',
                                                    'delivered' => 'completed',
                                                    'cancelled' => 'cancelled'
                                                ];
                                                $badgeClass = $statusClass[$order->status] ?? 'pending';
                                            @endphp
                                            <span class="status-badge {{ $badgeClass }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar text-muted me-1"></i>
                                            {{ \Carbon\Carbon::parse($order->created_at)->format('M j, Y') }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($orders->hasPages())
                        <div class="pagination-wrapper">
                            <div class="pagination-info">
                                Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders
                            </div>
                            {{ $orders->links() }}
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Customer Detail Header */
    .customer-detail-header {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .customer-detail-header h2 {
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
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .detail-card .card-header.bg-success {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%) !important;
    }

    .detail-card .card-header.bg-warning {
        background: linear-gradient(135deg, #A52A2A 0%, #A52A2A 100%) !important;
    }

    .detail-card .card-header h5 {
        margin: 0;
        font-weight: 600;
        color: white;
    }

    .detail-card .card-body {
        padding: 1.5rem;
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

    .customer-id {
        color: #A52A2A !important;
        font-weight: 700;
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

    .status-badge.active {
        
        color: #146c43;
    }

    .status-badge.inactive {
        
        color: #bb2d3b;
    }

    .status-badge.pending {
        
        color: #ffc107;
    }

    .status-badge.confirmed {
        
        color: #0aa2c0;
    }

    .status-badge.completed {
        
        color: #198754;
    }

    .status-badge.cancelled {
        
        color: #dc3545;
    }

    /* Statistics */
    .stat-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .stat-item:last-child {
        margin-bottom: 0;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .stat-content {
        flex: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #A52A2A;
    }

    /* Detail Table */
    .detail-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .detail-table thead {
        background: #f8f9fa;
        text-align: center;
    }

    .detail-table thead th {
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        border-bottom: 2px solid #dee2e6;
        text-transform: uppercase;
        font-size: 0.875rem;
        text-align: center;
    }

    .detail-table tbody tr {
        border-bottom: 1px solid #f8f9fa;
        transition: background-color 0.2s ease;
        text-align: center;
    }

    .detail-table tbody tr:hover {
        background-color: rgba(165, 42, 42, 0.05);
    }

    .detail-table tbody td {
        padding: 1rem;
        vertical-align: middle;
        text-align: center;
    }

    /* Order ID */
    .order-id {
        font-weight: 700;
        color: #A52A2A;
        font-size: 1rem;
    }

    /* Price Cells */
    .price-cell {
        font-weight: 600;
        color: #495057;
    }

    /* Action Buttons */
    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        border: none;
        color: #000;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #545b62 100%);
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
    }

    .btn-success {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%);
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
    }

    .btn-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
    }

    .empty-state i {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    .empty-state h4 {
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #6c757d;
    }

    /* Pagination */
    .pagination-wrapper {
        padding: 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pagination-info {
        color: #6c757d;
        font-size: 0.875rem;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .customer-detail-header {
            padding: 1.5rem;
        }

        .customer-detail-header h2 {
            font-size: 1.5rem;
        }

        .detail-card .card-body {
            padding: 1rem;
        }

        .stat-value {
            font-size: 1.25rem;
        }
    }

    @media (max-width: 576px) {
        .detail-table thead th,
        .detail-table tbody td {
            padding: 0.75rem 0.5rem;
            font-size: 0.875rem;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 1.25rem;
        }
    }
</style>
@endpush
@endsection