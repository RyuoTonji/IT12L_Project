@extends('layouts.app')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h2 class="mb-0"><i class="fas fa-users"></i> Customer Management</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.customers.create') }}" class="btn btn-light">
                    <i class="fas fa-user-plus"></i> Add New Customer
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="filters-card mb-4">
        <h5><i class="fas fa-filter"></i> Filter Customers</h5>
        <form method="GET" action="{{ route('admin.customers.index') }}">
            <div class="filter-group">
                <div class="form-group">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, email, or phone" value="{{ request('search') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
                @if(request()->hasAny(['search', 'status']))
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Customers Table Card -->
    <div class="customers-table-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> All Customers</h5>
            <span class="badge bg-light text-dark">{{ $customers->total() }} Total</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="customers-table table">
                    <thead>
                        <tr>
                            <th>Customer ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td>
                                <span class="customer-id">#{{ $customer->id }}</span>
                            </td>
                            <td>
                                <div class="customer-info">
                                    <i class="fas fa-user text-muted me-2"></i>
                                    <strong>{{ $customer->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <i class="fas fa-envelope text-muted me-1"></i>
                                <span class="text-muted">{{ $customer->email }}</span>
                            </td>
                            <td>
                                <i class="fas fa-phone text-muted me-1"></i>
                                {{ $customer->phone ?? 'N/A' }}
                            </td>
                            <td>
                                <span class="status-badge {{ $customer->is_active ? 'active' : 'inactive' }}">
                                    {{ $customer->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td>
                                <i class="fas fa-calendar text-muted me-1"></i>
                                {{ \Carbon\Carbon::parse($customer->created_at)->format('M j, Y') }}
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($customer->created_at)->format('g:i A') }}
                                </small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" 
                                       class="btn btn-sm btn-primary"
                                       title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    
                                    <a href="{{ route('admin.customers.edit', $customer->id) }}" 
                                       class="btn btn-sm btn-warning"
                                       title="Edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    
                                    <form action="{{ route('admin.customers.toggleStatus', $customer->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to {{ $customer->is_active ? 'disable' : 'enable' }} this customer?');">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm btn-{{ $customer->is_active ? 'secondary' : 'success' }}" 
                                                title="{{ $customer->is_active ? 'Disable' : 'Enable' }}">
                                            <i class="fas fa-{{ $customer->is_active ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-users-slash"></i>
                                    <h4>No Customers Found</h4>
                                    <p>{{ request()->hasAny(['search', 'status']) ? 'No customers match your filters.' : 'There are no customers yet.' }}</p>
                                    @if(!request()->hasAny(['search', 'status']))
                                    <a href="{{ route('admin.customers.create') }}" class="btn btn-success mt-2">
                                        <i class="fas fa-user-plus"></i> Add New Customer
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($customers->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} customers
            </div>
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        color: white;
        padding: 1.5rem 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .page-header h2 {
        color: white;
    }

    .page-header .btn-success {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%);
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .page-header .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Filters Card */
    .filters-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .filters-card h5 {
        color: #A52A2A;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .filter-group {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: end;
    }

    .filter-group .form-group {
        flex: 1;
        min-width: 160px;
    }

    .filter-group .form-group:has(.btn) {
        flex: 0 0 160px;
        min-width: 160px;
        max-width: 160px;
    }

    .filter-group .form-group .btn {
        width: 100%;
        margin: 0;
        padding: 0.625rem 1rem;
    }

    .filter-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        display: block;
    }

    .filter-group .form-control,
    .filter-group .form-select {
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 0.625rem 1rem;
        transition: all 0.3s ease;
    }

    .filter-group .form-control:focus,
    .filter-group .form-select:focus {
        border-color: #A52A2A;
        box-shadow: 0 0 0 0.25rem rgba(165, 42, 42, 0.15);
    }

    /* Customers Table Card */
    .customers-table-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .customers-table-card .card-header {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .customers-table-card .card-body {
        padding: 0;
    }

    /* Table Styles */
    .customers-table {
        margin-bottom: 0;
    }

    .customers-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .customers-table thead th {
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        border: none;
        text-transform: uppercase;
        font-size: 0.875rem;
        white-space: nowrap;
        text-align: center;
    }

    .customers-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #dee2e6;
        text-align: center;
    }

    .customers-table tbody tr:hover {
        background-color: rgba(165, 42, 42, 0.05);
    }

    .customers-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    /* Customer ID */
    .customer-id {
        font-weight: 700;
        color: #A52A2A;
        font-size: 0.95rem;
    }

    /* Customer Info */
    .customer-info {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .customer-info strong {
        color: #212529;
    }

    /* Status Badges */
    .status-badge {
        padding: 0.5rem 1rem;
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
        color: #A52A2A;
    }

    /* Action Buttons - WITH IMPORTANT FLAGS */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: center;
        align-items: stretch;
    }

    .action-buttons form {
        display: flex;
        margin: 0;
    }

    .action-buttons .btn {
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
    }

    /* Override any existing btn-primary styles */
  
    .action-buttons .btn-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
        border: none !important;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white !important;
    }

    
    .action-buttons .btn-primary:hover {
        background: linear-gradient(135deg, #0a58ca 0%, #084298 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
        color: white !important;
    }

    /* Override any existing btn-warning styles */
    .customers-table .btn-warning,
    .action-buttons .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
        border: none !important;
        color: #000 !important;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .customers-table .btn-warning:hover,
    .action-buttons .btn-warning:hover {
        background: linear-gradient(135deg, #e0a800 0%, #cc9a06 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
        color: #000 !important;
    }

    /* Override any existing btn-secondary styles */
    .customers-table .btn-secondary,
    .action-buttons .btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #545b62 100%) !important;
        border: none !important;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white !important;
    }

    .customers-table .btn-secondary:hover,
    .action-buttons .btn-secondary:hover {
        background: linear-gradient(135deg, #545b62 0%, #3d4349 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
        color: white !important;
    }

    /* Override any existing btn-success styles */
    .customers-table .btn-success,
    .action-buttons .btn-success {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
        border: none !important;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white !important;
    }

    .customers-table .btn-success:hover,
    .action-buttons .btn-success:hover {
        background: linear-gradient(135deg, #146c43 0%, #0f5132 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
        color: white !important;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    .empty-state h4 {
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #6c757d;
        margin-bottom: 1rem;
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
        font-weight: 500;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .customers-table {
            font-size: 0.875rem;
        }
    }

    @media (max-width: 992px) {
        .page-header {
            padding: 1.5rem;
        }

        .page-header h2 {
            font-size: 1.5rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons .btn {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 1rem;
        }

        .filter-group {
            flex-direction: column;
        }

        .filter-group .form-group {
            width: 100%;
        }

        .pagination-wrapper {
            flex-direction: column;
            gap: 1rem;
        }

        /* Make table scrollable on mobile */
        .table-responsive {
            overflow-x: auto;
        }

        .customers-table {
            min-width: 900px;
        }
    }

    @media (max-width: 576px) {
        .page-header .d-flex {
            flex-direction: column;
            gap: 1rem;
        }

        .page-header .btn {
            width: 100%;
        }
    }
</style>
@endpush
@endsection