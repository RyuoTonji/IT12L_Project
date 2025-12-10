@extends('layouts.app')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0"><i class="fas fa-tags"></i> Manage Categories</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.categories.archived') }}" class="btn btn-light">
                    <i class="fas fa-archive"></i> Archived Categories
                </a>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Category
                </a>
            </div>
        </div>
    </div>

    <!-- Categories Table Card -->
    <div class="categories-table-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-tag"></i> All Categories</h5>
            <span class="badge bg-light text-dark">{{ $categories->total() }} Total</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="categories-table table">
                    <thead>
                        <tr>
                            <th>Category ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>
                                <span class="category-id">#{{ $category->id }}</span>
                            </td>
                            <td>
                                <div class="category-name">
                                    <i class="fas fa-tag"></i>
                                    <strong>{{ $category->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="category-description">
                                    {{ $category->description ? Str::limit($category->description, 60) : 'No description' }}
                                </span>
                            </td>
                            <td>
                                <i class="fas fa-calendar text-muted me-1"></i>
                                {{ \Carbon\Carbon::parse($category->created_at)->format('M j, Y') }}
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($category->created_at)->format('g:i A') }}
                                </small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to archive this category?');">
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
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h4>No Categories Found</h4>
                                    <p>Start by creating your first category.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} categories
            </div>
            {{ $categories->links() }}
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

    /* Categories Table Card */
    .categories-table-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .categories-table-card .card-header {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .categories-table-card .card-body {
        padding: 0;
    }

    /* Table Styles */
    .categories-table {
        margin-bottom: 0;
    }

    .categories-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .categories-table thead th {
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        border: none;
        text-transform: uppercase;
        font-size: 0.875rem;
        text-align: center;
    }

    .categories-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #dee2e6;
    }

    .categories-table tbody tr:hover {
        background-color: rgba(165, 42, 42, 0.05);
    }

    .categories-table tbody td {
        padding: 1rem;
        vertical-align: middle;
        text-align: center;
    }

    /* Category ID */
    .category-id {
        font-weight: 700;
        color: #A52A2A;
        font-size: 1rem;
    }

    /* Category Name */
    .category-name {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        justify-content: center;
    }

    .category-name strong {
        color: #1971c2;
        font-size: 1rem;
    }

    .category-name i {
        color: #1971c2;
        font-size: 0.875rem;
    }

    /* Category Description */
    .category-description {
        color: #6c757d;
        font-size: 0.9rem;
    }

    /* Action Buttons - FIXED TO MATCH PRODUCTS PAGE */
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
        font-weight: 500;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 1rem;
        }

        .page-header .d-flex {
            flex-direction: column;
            gap: 1rem;
        }

        .page-header .btn {
            width: 100%;
        }

        .pagination-wrapper {
            flex-direction: column;
            gap: 1rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons .btn {
            width: 100%;
        }

        .categories-table thead th,
        .categories-table tbody td {
            padding: 0.75rem 0.5rem;
            font-size: 0.875rem;
        }
    }

    @media (max-width: 576px) {
        .page-header h2 {
            font-size: 1.5rem;
        }

        .categories-table-card .card-header h5 {
            font-size: 1rem;
        }
    }
</style>
@endpush
@endsection