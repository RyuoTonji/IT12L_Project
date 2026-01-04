@extends('layouts.app')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h2 class="mb-0"><i class="fas fa-comment-dots"></i> Feedback Management</h2>
            <div class="d-flex gap-2">
                @if($newCount > 0)
                <span class="badge bg-danger" style="font-size: 1rem; padding: 0.5rem 1rem;">
                    {{ $newCount }} New
                </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="filters-card mb-4">
        <h5><i class="fas fa-filter"></i> Filter Feedback</h5>
        <form method="GET" action="{{ route('admin.feedback.index') }}">
            <div class="filter-group">
                <div class="form-group">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, email, or order ID" value="{{ request('search') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="all">All Status</option>
                        <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="all">All Types</option>
                        <option value="feedback" {{ request('type') === 'feedback' ? 'selected' : '' }}>Feedback</option>
                        <option value="complaint" {{ request('type') === 'complaint' ? 'selected' : '' }}>Complaint</option>
                        <option value="suggestion" {{ request('type') === 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
                @if(request()->hasAny(['search', 'status', 'type']))
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('admin.feedback.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Feedback Table Card -->
    <div class="feedback-table-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> All Feedback</h5>
            <span class="badge bg-light text-dark">{{ $feedbacks->total() }} Total</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="feedback-table table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Customer</th>
                            <th>Order ID</th>
                            <th>Customer Type</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbacks as $feedback)
                        <tr class="{{ $feedback->status === 'new' ? 'new-feedback' : '' }}">
                            <td>
                                <span class="feedback-id">#{{ $feedback->id }}</span>
                            </td>
                            <td>
                                @php
                                    $typeColor = match($feedback->feedback_type) {
                                        'complaint' => '#dc3545', // Danger Red
                                        'suggestion' => '#0dcaf0', // Info Blue
                                        default => '#198754' // Success Green
                                    };
                                @endphp
                                <span style="color: {{ $typeColor }}; font-weight: 700;">
                                    @if($feedback->feedback_type === 'complaint')
                                    <i class="fas fa-exclamation-triangle"></i>
                                    @elseif($feedback->feedback_type === 'suggestion')
                                    <i class="fas fa-lightbulb"></i>
                                    @else
                                    <i class="fas fa-comment"></i>
                                    @endif
                                    {{ ucfirst($feedback->feedback_type) }}
                                </span>
                            </td>
                            <td>
                                <div class="customer-info">
                                    <strong>{{ $feedback->customer_name }}</strong><br>
                                    <small class="text-muted">{{ $feedback->customer_email }}</small>
                                </div>
                            </td>
                            <td>
                                @if($feedback->order_id)
                                <a href="{{ route('admin.orders.show', $feedback->order_id) }}" class="order-link">
                                    #{{ $feedback->order_id }}
                                </a>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="customer-type-badge">
                                    {{ ucfirst($feedback->customer_type) }}
                                </span>
                            </td>
                            <td>
                                <div class="message-preview">
                                    {{ Str::limit($feedback->message, 50) }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusColor = match($feedback->status) {
                                        'new' => '#ff4d4d', // Red
                                        'read' => '#17a2b8', // Info
                                        'resolved' => '#28a745', // Green
                                        default => '#6c757d'
                                    };
                                @endphp
                                <span style="color: {{ $statusColor }}; font-weight: 700;">
                                    @if($feedback->status === 'new')
                                    <i class="fas fa-circle small"></i> New
                                    @elseif($feedback->status === 'read')
                                    <i class="fas fa-eye small"></i> Read
                                    @else
                                    <i class="fas fa-check-circle small"></i> Resolved
                                    @endif
                                </span>
                            </td>
                            <td>
                                <i class="fas fa-calendar text-muted me-1"></i>
                                {{ \Carbon\Carbon::parse($feedback->created_at)->format('M j, Y') }}
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($feedback->created_at)->format('g:i A') }}
                                </small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.feedback.show', $feedback->id) }}" 
                                       class="btn btn-sm btn-primary"
                                       title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    
                                    <form action="{{ route('admin.feedback.destroy', $feedback->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h4>No Feedback Found</h4>
                                    <p>{{ request()->hasAny(['search', 'status', 'type']) ? 'No feedback matches your filters.' : 'There is no feedback yet.' }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($feedbacks->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Showing {{ $feedbacks->firstItem() }} to {{ $feedbacks->lastItem() }} of {{ $feedbacks->total() }} feedback submissions
            </div>
            {{ $feedbacks->links() }}
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

    /* Feedback Table Card */
    .feedback-table-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .feedback-table-card .card-header {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .feedback-table-card .card-body {
        padding: 0;
    }

    /* Table Styles */
    .feedback-table {
        margin-bottom: 0;
    }

    .feedback-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .feedback-table thead th {
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        border: none;
        text-transform: uppercase;
        font-size: 0.875rem;
        white-space: nowrap;
        text-align: center;
    }

    .feedback-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #dee2e6;
        text-align: center;
    }

    .feedback-table tbody tr.new-feedback {
        background-color: rgba(255, 193, 7, 0.1);
    }

    .feedback-table tbody tr:hover {
        background-color: rgba(165, 42, 42, 0.05);
    }

    .feedback-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    /* Feedback ID */
    .feedback-id {
        font-weight: 700;
        color: #A52A2A;
        font-size: 0.95rem;
    }

    /* Type Badges */
    .type-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-block;
        text-transform: capitalize;
    }

    .type-badge.complaint {
        background-color: #fff3cd;
        color: #856404;
    }

    .type-badge.suggestion {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .type-badge.feedback {
        background-color: #d4edda;
        color: #155724;
    }

    /* Customer Info */
    .customer-info strong {
        color: #212529;
    }

    /* Order Link */
    .order-link {
        color: #0d6efd;
        font-weight: 600;
        text-decoration: none;
    }

    .order-link:hover {
        text-decoration: underline;
    }

    /* Customer Type Badge */
    .customer-type-badge {
        padding: 0.25rem 0.75rem;
        background-color: #e7f3ff;
        color: #004085;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Message Preview */
    .message-preview {
        text-align: left;
        max-width: 200px;
        color: #495057;
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

    .status-badge.new {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-badge.read {
        background-color: #cfe2ff;
        color: #084298;
    }

    .status-badge.resolved {
        background-color: #d1e7dd;
        color: #0f5132;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: center;
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
</style>
@endpush
@endsection
