<?php $__env->startSection('content'); ?>
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-archive"></i> Archived Orders</h2>
        <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Active Orders
        </a>
    </div>

    <!-- <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?> -->

    <div class="card">
        <div class="card-header">
            <form method="GET" action="<?php echo e(route('admin.orders.archived')); ?>" class="row g-3">
                <div class="col-md-3">
                    <select name="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch_id') == $branch->id ? 'selected' : ''); ?>>
                                <?php echo e($branch->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-light">
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
                            <th>Archived Date</th>
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
                                    $textClass = $statusClass[$order->status] ?? 'secondary';
                                ?>
                                <span class="text-<?php echo e($textClass); ?> fw-semibold">
                                    <?php echo e(ucfirst($order->status)); ?>

                                </span>
                            </td>
                            <td><?php echo e(\Carbon\Carbon::parse($order->created_at)->format('M j, Y g:i A')); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($order->deleted_at)->format('M j, Y g:i A')); ?></td>
                            <td>
                                <form action="<?php echo e(route('admin.orders.restore', $order->id)); ?>" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to restore this order?');"
                                      class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center">No archived orders found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <?php echo e($orders->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kates\Documents\IT12final\IT12L_FullProject_ERP\resources\views/admin/orders/archived.blade.php ENDPATH**/ ?>