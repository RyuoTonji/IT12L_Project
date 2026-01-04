@extends('layouts.app')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h2 class="mb-0"><i class="fas fa-comment-dots"></i> Feedback Details #{{ $feedback->id }}</h2>
            <a href="{{ route('admin.feedback.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Feedback Details Card -->
    <div class="feedback-details-card">
        <div class="card-header">
            <h5 class="mb-0">Feedback Information</h5>
            <div class="status-actions">
                <form action="{{ route('admin.feedback.updateStatus', $feedback->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="new" {{ $feedback->status === 'new' ? 'selected' : '' }}>New</option>
                        <option value="read" {{ $feedback->status === 'read' ? 'selected' : '' }}>Read</option>
                        <option value="resolved" {{ $feedback->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="info-group">
                        <label><i class="fas fa-hashtag"></i> Feedback ID</label>
                        <p class="value">#{{ $feedback->id }}</p>
                    </div>

                    <div class="info-group">
                        <label><i class="fas fa-tag"></i> Type</label>
                        <p class="value">
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
                        </p>
                    </div>

                    <div class="info-group">
                        <label><i class="fas fa-user"></i> Customer Name</label>
                        <p class="value">{{ $feedback->customer_name }}</p>
                    </div>

                    <div class="info-group">
                        <label><i class="fas fa-envelope"></i> Customer Email</label>
                        <p class="value">
                            <a href="mailto:{{ $feedback->customer_email }}">{{ $feedback->customer_email }}</a>
                        </p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="info-group">
                        <label><i class="fas fa-shopping-bag"></i> Customer Type</label>
                        <p class="value">
                            <span class="customer-type-badge">{{ ucfirst($feedback->customer_type) }}</span>
                        </p>
                    </div>

                    <div class="info-group">
                        <label><i class="fas fa-receipt"></i> Order ID</label>
                        <p class="value">
                            @if($feedback->order_id)
                            <a href="{{ route('admin.orders.show', $feedback->order_id) }}" class="order-link">
                                #{{ $feedback->order_id }}
                            </a>
                            @else
                            <span class="text-muted">N/A</span>
                            @endif
                        </p>
                    </div>

                    <div class="info-group">
                        <label><i class="fas fa-info-circle"></i> Status</label>
                        <p class="value">
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
                        </p>
                    </div>

                    <div class="info-group">
                        <label><i class="fas fa-calendar"></i> Submitted</label>
                        <p class="value">
                            {{ \Carbon\Carbon::parse($feedback->created_at)->format('F j, Y \a\t g:i A') }}
                        </p>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Message Section -->
            <div class="message-section">
                <label><i class="fas fa-comment-alt"></i> Message</label>
                <div class="message-content">
                    {{ $feedback->message }}
                </div>
            </div>

            <hr class="my-4">

            <!-- Actions -->
            <div class="d-flex gap-2 justify-content-end">
                <form action="{{ route('admin.feedback.destroy', $feedback->id) }}" 
                      method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this feedback? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Feedback
                    </button>
                </form>
            </div>
        </div>
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

    /* Feedback Details Card */
    .feedback-details-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .feedback-details-card .card-header {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .feedback-details-card .card-body {
        padding: 2rem;
    }

    /* Info Groups */
    .info-group {
        margin-bottom: 1.5rem;
    }

    .info-group label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        display: block;
    }

    .info-group label i {
        margin-right: 0.5rem;
    }

    .info-group .value {
        font-size: 1.1rem;
        color: #212529;
        margin: 0;
    }

    .info-group .value a {
        color: #0d6efd;
        text-decoration: none;
    }

    .info-group .value a:hover {
        text-decoration: underline;
    }

    /* Type Badges */
    .type-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-block;
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

    /* Status Badges */
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-block;
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

    /* Customer Type Badge */
    .customer-type-badge {
        padding: 0.5rem 1rem;
        background-color: #e7f3ff;
        color: #004085;
        border-radius: 12px;
        font-weight: 500;
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

    /* Message Section */
    .message-section {
        margin-top: 1rem;
    }

    .message-section label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
        display: block;
    }

    .message-section label i {
        margin-right: 0.5rem;
    }

    .message-content {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        border-left: 4px solid #A52A2A;
        line-height: 1.8;
        font-size: 1.05rem;
        color: #212529;
        white-space: pre-wrap;
    }

    /* Status Actions */
    .status-actions .form-select {
        width: auto;
        min-width: 150px;
    }
</style>
@endpush
@endsection
