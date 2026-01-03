

<?php $__env->startSection('content'); ?>
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h2 class="mb-0"><i class="fas fa-comment-dots"></i> Feedback Management</h2>
            <div class="d-flex gap-2">
                <?php if($newCount > 0): ?>
                <span class="badge bg-danger" style="font-size: 1rem; padding: 0.5rem 1rem;">
                    <?php echo e($newCount); ?> New
                </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="filters-card mb-4">
        <h5><i class="fas fa-filter"></i> Filter Feedback</h5>
        <form method="GET" action="<?php echo e(route('admin.feedback.index')); ?>">
            <div class="filter-group">
                <div class="form-group">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, email, or order ID" value="<?php echo e(request('search')); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="all">All Status</option>
                        <option value="new" <?php echo e(request('status') === 'new' ? 'selected' : ''); ?>>New</option>
                        <option value="read" <?php echo e(request('status') === 'read' ? 'selected' : ''); ?>>Read</option>
                        <option value="resolved" <?php echo e(request('status') === 'resolved' ? 'selected' : ''); ?>>Resolved</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="all">All Types</option>
                        <option value="feedback" <?php echo e(request('type') === 'feedback' ? 'selected' : ''); ?>>Feedback</option>
                        <option value="complaint" <?php echo e(request('type') === 'complaint' ? 'selected' : ''); ?>>Complaint</option>
                        <option value="suggestion" <?php echo e(request('type') === 'suggestion' ? 'selected' : ''); ?>>Suggestion</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
                <?php if(request()->hasAny(['search', 'status', 'type'])): ?>
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <a href="<?php echo e(route('admin.feedback.index')); ?>" class="btn btn-secondary w-100">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Feedback Table Card -->
    <div class="feedback-table-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> All Feedback</h5>
            <span class="badge bg-light text-dark"><?php echo e($feedbacks->total()); ?> Total</span>
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
                        <?php $__empty_1 = true; $__currentLoopData = $feedbacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feedback): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="<?php echo e($feedback->status === 'new' ? 'new-feedback' : ''); ?>">
                            <td>
                                <span class="feedback-id">#<?php echo e($feedback->id); ?></span>
                            </td>
                            <td>
                                <?php
                                    $typeColor = match($feedback->feedback_type) {
                                        'complaint' => '#dc3545', // Danger Red
                                        'suggestion' => '#0dcaf0', // Info Blue
                                        default => '#198754' // Success Green
                                    };
                                ?>
                                <span style="color: <?php echo e($typeColor); ?>; font-weight: 700;">
                                    <?php if($feedback->feedback_type === 'complaint'): ?>
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?php elseif($feedback->feedback_type === 'suggestion'): ?>
                                    <i class="fas fa-lightbulb"></i>
                                    <?php else: ?>
                                    <i class="fas fa-comment"></i>
                                    <?php endif; ?>
                                    <?php echo e(ucfirst($feedback->feedback_type)); ?>

                                </span>
                            </td>
                            <td>
                                <div class="customer-info">
                                    <strong><?php echo e($feedback->customer_name); ?></strong><br>
                                    <small class="text-muted"><?php echo e($feedback->customer_email); ?></small>
                                </div>
                            </td>
                            <td>
                                <?php if($feedback->order_id): ?>
                                <a href="<?php echo e(route('admin.orders.show', $feedback->order_id)); ?>" class="order-link">
                                    #<?php echo e($feedback->order_id); ?>

                                </a>
                                <?php else: ?>
                                <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="customer-type-badge">
                                    <?php echo e(ucfirst($feedback->customer_type)); ?>

                                </span>
                            </td>
                            <td>
                                <div class="message-preview">
                                    <?php echo e(Str::limit($feedback->message, 50)); ?>

                                </div>
                            </td>
                            <td>
                                <?php
                                    $statusColor = match($feedback->status) {
                                        'new' => '#ff4d4d', // Red
                                        'read' => '#17a2b8', // Info
                                        'resolved' => '#28a745', // Green
                                        default => '#6c757d'
                                    };
                                ?>
                                <span style="color: <?php echo e($statusColor); ?>; font-weight: 700;">
                                    <?php if($feedback->status === 'new'): ?>
                                    <i class="fas fa-circle small"></i> New
                                    <?php elseif($feedback->status === 'read'): ?>
                                    <i class="fas fa-eye small"></i> Read
                                    <?php else: ?>
                                    <i class="fas fa-check-circle small"></i> Resolved
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <i class="fas fa-calendar text-muted me-1"></i>
                                <?php echo e(\Carbon\Carbon::parse($feedback->created_at)->format('M j, Y')); ?>

                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo e(\Carbon\Carbon::parse($feedback->created_at)->format('g:i A')); ?>

                                </small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?php echo e(route('admin.feedback.show', $feedback->id)); ?>" 
                                       class="btn btn-sm btn-primary"
                                       title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    
                                    <form action="<?php echo e(route('admin.feedback.destroy', $feedback->id)); ?>" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h4>No Feedback Found</h4>
                                    <p><?php echo e(request()->hasAny(['search', 'status', 'type']) ? 'No feedback matches your filters.' : 'There is no feedback yet.'); ?></p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if($feedbacks->hasPages()): ?>
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Showing <?php echo e($feedbacks->firstItem()); ?> to <?php echo e($feedbacks->lastItem()); ?> of <?php echo e($feedbacks->total()); ?> feedback submissions
            </div>
            <?php echo e($feedbacks->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Videos\Project_IT12\sabio\IT12L_Project\IT12L_FullProject_ERP\resources\views/admin/feedback/index.blade.php ENDPATH**/ ?>