@extends('layouts.app')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h2 class="mb-0"><i class="fas fa-utensils"></i> Manage Products</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.products.archived') }}" class="btn btn-light">
                    <i class="fas fa-archive"></i> Archived Products
                </a>
                <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Dish
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters Card -->
    <div class="filters-card mb-4">
        <h5><i class="fas fa-filter"></i> Filter Products</h5>
        <form method="GET" action="{{ route('admin.products.index') }}">
            <div class="filter-group">
                <div class="form-group">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Product name..." value="{{ request('search') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Availability</label>
                    <select name="is_available" class="form-select">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_available') === '1' ? 'selected' : '' }}>Available</option>
                        <option value="0" {{ request('is_available') === '0' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Price Range</label>
                    <select name="price_range" class="form-select">
                        <option value="">All Prices</option>
                        <option value="0-100" {{ request('price_range') == '0-100' ? 'selected' : '' }}>₱0 - ₱100</option>
                        <option value="100-200" {{ request('price_range') == '100-200' ? 'selected' : '' }}>₱100 - ₱200</option>
                        <option value="200-500" {{ request('price_range') == '200-500' ? 'selected' : '' }}>₱200 - ₱500</option>
                        <option value="500+" {{ request('price_range') == '500+' ? 'selected' : '' }}>₱500+</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
                @if(request()->hasAny(['search', 'category_id', 'branch_id', 'is_available', 'price_range']))
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Products Table Card -->
    <div class="products-table-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> All Products</h5>
            <span class="badge bg-light text-dark">{{ $products->total() }} Total</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="products-table table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Branch</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>
                                <span class="product-id">#{{ $product->id }}</span>
                            </td>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                         alt="{{ $product->name }}" 
                                         class="product-image">
                                @else
                                    <div class="product-image-placeholder">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong class="product-name">{{ $product->name }}</strong>
                            </td>
                            <td>
                                <small class="product-description">
                                    {{ Str::limit($product->description ?? 'No description', 60) }}
                                </small>
                            </td>
                            <td>
                                <span class="category-badge">
                                    <i class="fas fa-tag"></i>
                                    {{ $product->category_name }}
                                </span>
                            </td>
                            <td>
                                <span class="branch-badge">
                                    <i class="fas fa-store"></i>
                                    {{ $product->branch_name }}
                                </span>
                            </td>
                            <td>
                                <span class="product-price">₱{{ number_format($product->price, 2) }}</span>
                            </td>
                            <td>
                                <span class="status-badge {{ $product->is_available ? 'available' : 'unavailable' }}">
                                    {{ $product->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to archive this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-archive"></i> Archive
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-utensils"></i>
                                    <h4>No Products Found</h4>
                                    <p>{{ request()->hasAny(['search', 'category_id', 'branch_id', 'is_available', 'price_range']) ? 'No products match your filters.' : 'Start by adding your first product.' }}</p>
                                    @if(!request()->hasAny(['search', 'category_id', 'branch_id', 'is_available', 'price_range']))
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-success mt-2">
                                        <i class="fas fa-plus"></i> Add New Dish
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
        @if($products->hasPages())
        <div class="pagination-wrapper">
            <!-- <div class="pagination-info">
                Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
            </div> -->
            {{ $products->links() }}
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

    .page-header .btn-light {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .page-header .btn-light:hover {
        background: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
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

    /* Products Table Card */
    .products-table-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .products-table-card .card-header {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .products-table-card .card-body {
        padding: 0;
    }

    /* Table Styles */
    .products-table {
        margin-bottom: 0;
    }

    .products-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .products-table thead th {
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        border: none;
        text-transform: uppercase;
        font-size: 0.875rem;
        white-space: nowrap;
        text-align: center;
    }

    .products-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #dee2e6;
        text-align: center;
    }

    .products-table tbody tr:hover {
        background-color: rgba(165, 42, 42, 0.05);
    }

    .products-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    /* Product ID */
    .product-id {
        font-weight: 700;
        color: #A52A2A;
        font-size: 0.95rem;
    }

    /* Product Image */
    .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #dee2e6;
        transition: transform 0.2s ease;
    }

    .product-image:hover {
        transform: scale(1.1);
    }

    .product-image-placeholder {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        border: 2px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        font-size: 1.5rem;
        margin: 0 auto;
    }

    /* Product Name */
    .product-name {
        color: #212529;
        font-size: 1rem;
    }

    /* Product Description */
    .product-description {
        color: #6c757d;
        display: block;
        line-height: 1.4;
    }

    /* Category & Branch Badges */
    .category-badge,
    .branch-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .category-badge {
        color: #1971c2;
    }

    .branch-badge {
        color: #d9480f;
    }

    .category-badge i,
    .branch-badge i {
        font-size: 0.75rem;
    }

    /* Product Price */
    .product-price {
        font-weight: 700;
        font-size: 1.125rem;
        color: #A52A2A;
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

    .status-badge.available {
        
        color: #146c43;
    }

    .status-badge.unavailable {
        color: #A52A2A;
    }

    /* Action Buttons - FIXED */
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

    .action-buttons .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        border: none;
        color: #000;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .action-buttons .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
    }

    .action-buttons .btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #bb2d3b 100%);
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .action-buttons .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
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

    /* Alerts */
    .alert {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .alert-success {
        background: linear-gradient(135deg, #d1e7dd 0%, #badbcc 100%);
        color: #0f5132;
    }

    .alert-danger {
        background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
        color: #842029;
    }

    /* Pagination */
    .pagination-wrapper {
        padding: 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: center;
        align-items: center;

    }

    .pagination-info {
        color: #6c757d;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .products-table {
            font-size: 0.875rem;
        }
        
        .product-image,
        .product-image-placeholder {
            width: 50px;
            height: 50px;
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

        .products-table {
            min-width: 1000px;
        }
    }

    @media (max-width: 576px) {
        .category-badge,
        .branch-badge {
            font-size: 0.75rem;
            padding: 0.3rem 0.6rem;
        }
    }
</style>
@endpush
@endsection