@extends('layouts.app')

@section('title', 'Feedback History - BBQ Lagao & Beef Pares')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-dark"><i class="fas fa-history me-2 text-primary"></i>Feedback History</h2>
        <a href="{{ route('feedback.index') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Send New Feedback
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted text-uppercase small">
                        <tr>
                            <th class="px-4 py-3 border-0">Date</th>
                            <th class="py-3 border-0">Type</th>
                            <th class="py-3 border-0">Order ID</th>
                            <th class="py-3 border-0">Message Preview</th>
                            <th class="py-3 border-0">Status</th>
                            <th class="px-4 py-3 border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbacks as $feedback)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="d-block fw-medium">{{ $feedback->created_at->format('M d, Y') }}</span>
                                <small class="text-muted">{{ $feedback->created_at->format('h:i A') }}</small>
                            </td>
                            <td class="py-3">
                                <span class="fw-semibold text-{{ $feedback->feedback_type === 'complaint' ? 'danger' : ($feedback->feedback_type === 'suggestion' ? 'info' : 'success') }}">
                                    {{ ucfirst($feedback->feedback_type) }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-light text-dark fw-normal border">#{{ $feedback->order_id }}</span>
                            </td>
                            <td class="py-3">
                                <div class="text-truncate text-muted" style="max-width: 250px;">
                                    {{ $feedback->message }}
                                </div>
                            </td>
                            <td class="py-3">
                                @php
                                    $statusColor = match($feedback->status) {
                                        'new' => '#ff4d4d', // Bright Red
                                        'read' => '#17a2b8', // Cyan/Info
                                        'resolved' => '#28a745', // Green
                                        default => '#6c757d'
                                    };
                                @endphp
                                <span style="color: {{ $statusColor }}; font-weight: 700;">
                                    {{ ucfirst($feedback->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#feedbackModal{{ $feedback->id }}">
                                    View Details
                                </button>
                            </td>
                        </tr>

                        <!-- Modal for Details -->
                        <div class="modal fade" id="feedbackModal{{ $feedback->id }}" tabindex="-1" aria-labelledby="feedbackModalLabel{{ $feedback->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                                        <h5 class="modal-title fw-bold" id="feedbackModalLabel{{ $feedback->id }}">Feedback Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <div class="small text-muted text-uppercase fw-bold mb-1">Status</div>
                                            <div style="color: {{ $statusColor }}; font-weight: 700; font-size: 1.1rem;">
                                                {{ ucfirst($feedback->status) }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="small text-muted text-uppercase fw-bold mb-1">Type</div>
                                                <div class="fw-semibold">{{ ucfirst($feedback->feedback_type) }}</div>
                                            </div>
                                            <div class="col-6 text-end">
                                                <div class="small text-muted text-uppercase fw-bold mb-1">Order #</div>
                                                <div class="fw-semibold">#{{ $feedback->order_id }}</div>
                                            </div>
                                        </div>
                                        <hr class="my-3 opacity-10">
                                        <div class="mb-0">
                                            <div class="small text-muted text-uppercase fw-bold mb-2">Message</div>
                                            <div class="bg-light p-3 rounded-3 text-darker" style="white-space: pre-wrap;">{{ $feedback->message }}</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 p-4 pt-0">
                                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center">
                                <div class="mb-3 text-muted opacity-50">
                                    <i class="fas fa-comment-slash fa-3x"></i>
                                </div>
                                <h5 class="text-muted">No feedback history found.</h5>
                                <p class="text-muted small mb-0">You haven't submitted any feedback yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $feedbacks->links() }}
    </div>
</div>

<style>
    .text-darker {
        color: #333;
        line-height: 1.6;
    }
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    .rounded-4 {
        border-radius: 1rem !important;
    }
</style>
@endsection
