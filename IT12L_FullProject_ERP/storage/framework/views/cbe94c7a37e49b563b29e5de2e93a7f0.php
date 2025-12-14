<?php $__env->startSection('content'); ?>
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0"><i class="fas fa-receipt"></i> Manage Orders</h2>
            <a href="<?php echo e(route('admin.orders.archived')); ?>" class="btn btn-light">
                <i class="fas fa-archive"></i> Archived Orders
            </a>
        </div>
    </div>

    <!-- <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?> -->

    <!-- Filters Card -->
    <div class="filters-card mb-4">
        <h5><i class="fas fa-filter"></i> Filter Orders</h5>
        <form method="GET" action="<?php echo e(route('admin.orders.index')); ?>">
            <div class="filter-group">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="confirmed" <?php echo e(request('status') == 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                        <option value="delivered" <?php echo e(request('status') == 'delivered' ? 'selected' : ''); ?>>Delivered</option>
                        <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch_id') == $branch->id ? 'selected' : ''); ?>>
                                <?php echo e($branch->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Orders Table Card -->
    <div class="orders-table-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> All Orders</h5>
            <span class="badge bg-light text-dark"><?php echo e($orders->total()); ?> Total</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="orders-table table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Branch</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <span class="order-id">#<?php echo e($order->id); ?></span>
                            </td>
                            <td>
                                <div class="customer-info">
                                    <strong><?php echo e($order->user_name); ?></strong>
                                    <small><?php echo e($order->user_email); ?></small>
                                </div>
                            </td>
                            <td>
                                <i class="fas fa-store text-muted me-1"></i>
                                <?php echo e($order->branch_name); ?>

                            </td>
                            <td>
                                <span class="order-total">₱<?php echo e(number_format($order->total_amount, 2)); ?></span>
                            </td>
                            <td>
                                <?php
                                    $statusClass = [
                                        'pending' => 'pending',
                                        'confirmed' => 'confirmed',
                                        'delivered' => 'completed',
                                        'cancelled' => 'cancelled'
                                    ];
                                    $badgeClass = $statusClass[$order->status] ?? 'pending';
                                ?>
                                <span class="status-badge <?php echo e($badgeClass); ?>">
                                    <?php echo e(ucfirst($order->status)); ?>

                                </span>
                            </td>
                            <td>
                                <i class="fas fa-calendar text-muted me-1"></i>
                                <?php echo e(\Carbon\Carbon::parse($order->created_at)->format('M j, Y')); ?>

                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo e(\Carbon\Carbon::parse($order->created_at)->format('g:i A')); ?>

                                </small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <form action="<?php echo e(route('admin.orders.destroy', $order->id)); ?>" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to archive this order?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-archive"></i> Archive
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h4>No Orders Found</h4>
                                    <p>There are no orders matching your filters.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if($orders->hasPages()): ?>
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Showing <?php echo e($orders->firstItem()); ?> to <?php echo e($orders->lastItem()); ?> of <?php echo e($orders->total()); ?> orders
            </div>
            <?php echo e($orders->links()); ?>

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
        min-width: 180px;
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

    /* Orders Table Card */
    .orders-table-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .orders-table-card .card-header {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .orders-table-card .card-body {
        padding: 0;
    }

    /* Table Styles */
    .orders-table {
        margin-bottom: 0;
    }

    .orders-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .orders-table thead th {
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        border: none;
        text-transform: uppercase;
        font-size: 0.875rem;
        text-align: center;
    }

    .orders-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #dee2e6;
        text-align: center;
    }

    .orders-table tbody tr:hover {
        background-color: rgba(165, 42, 42, 0.05);
    }

    .orders-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    /* Order ID */
    .order-id {
        font-weight: 700;
        color: #A52A2A;
        font-size: 1rem;
    }

    /* Customer Info */
    .customer-info strong {
        display: block;
        color: #212529;
        margin-bottom: 0.25rem;
    }

    .customer-info small {
        color: #6c757d;
    }

    /* Order Total */
    .order-total {
        font-weight: 700;
        font-size: 1.125rem;
        color: #000000ff;
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

    .status-badge.pending {
        
        color: #e0a800;
    }

    .status-badge.confirmed {
        
        color: #0dcaf0;
    }

    .status-badge.completed {
        
        color: #198754;
    }

    .status-badge.cancelled {
        
        color: #bb2d3b;
    }

    /* Action Buttons - UNIFORM SIZE MATCHING PRODUCTS PAGE */
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
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    white-space: nowrap;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.action-buttons .btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.action-buttons .btn-primary:hover {
    background: linear-gradient(135deg, #0a58ca 0%, #084298 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.4);
}

.action-buttons .btn-danger {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
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
    }

    /* Responsive */
    @media (max-width: 992px) {
    .action-buttons {
        flex-direction: column;
    }

        .filter-group .form-group {
            width: 100%;
        }

        .page-header {
            padding: 1rem;
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

        @media (max-width: 576px) {
        .category-name strong {
            font-size: 0.75rem;
            padding: 0.3rem 0.6rem;
        }
        }
    }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Videos\Project_IT12\sabio\IT12L_Project\IT12L_FullProject_ERP\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>