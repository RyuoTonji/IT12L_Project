

<?php $__env->startSection('content'); ?>
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-box"></i> Manage Orders</h2>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <form method="GET" action="<?php echo e(route('admin.orders.index')); ?>" class="row g-3">
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="confirmed" <?php echo e(request('status') == 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                        <option value="delivered" <?php echo e(request('status') == 'delivered' ? 'selected' : ''); ?>>Delivered</option>
                        <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
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
                            <td>#<?php echo e($order->id); ?></td>
                            <td>
                                <div><?php echo e($order->user_name); ?></div>
                                <small class="text-muted"><?php echo e($order->user_email); ?></small>
                            </td>
                            <td><?php echo e($order->branch_name); ?></td>
                            <td>₱<?php echo e(number_format($order->total_amount, 2)); ?></td>
                            <td>
                                <?php
                                    $statusClass = [
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $badgeClass = $statusClass[$order->status] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo e($badgeClass); ?>">
                                    <?php echo e(ucfirst($order->status)); ?>

                                </span>
                            </td>
                            <td><?php echo e(\Carbon\Carbon::parse($order->created_at)->format('M j, Y g:i A')); ?></td>
                            <td>
                                <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center">No orders found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                <?php echo e($orders->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kates\Documents\IT12final\IT12L_FullProject_ERP\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>