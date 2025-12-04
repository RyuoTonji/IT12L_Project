<?php $__env->startSection('content'); ?>
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
        <div>
            <span class="text-muted">Welcome, <?php echo e(auth()->user()->name); ?></span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Total Orders</h6>
                            <h2 class="card-title mb-0"><?php echo e(number_format($totalOrders)); ?></h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Total Revenue</h6>
                            <h2 class="card-title mb-0">₱<?php echo e(number_format($totalRevenue, 2)); ?></h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Pending Orders</h6>
                            <h2 class="card-title mb-0"><?php echo e(number_format($pendingOrders)); ?></h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Total Products</h6>
                            <h2 class="card-title mb-0"><?php echo e(number_format($totalProducts)); ?></h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-utensils fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="/admin/orders" class="btn btn-outline-primary w-100">
                                <i class="fas fa-box"></i> Manage Orders
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-outline-success w-100">
                                <i class="fas fa-utensils"></i> Manage Products
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/admin/branches" class="btn btn-outline-info w-100">
                                <i class="fas fa-store"></i> Manage Branches
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="/admin/categories" class="btn btn-outline-warning w-100">
                                <i class="fas fa-tags"></i> Manage Categories
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Recent Orders</h5>
                    <a href="/admin/orders" class="btn btn-sm btn-light">View All</a>
                </div>
                <div class="card-body">
                    <?php if($recentOrders->isEmpty()): ?>
                        <p class="text-muted text-center py-4">No orders yet</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Branch</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>#<?php echo e($order->id); ?></td>
                                            <td><?php echo e($order->user_name); ?></td>
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
                                            <td><?php echo e(\Carbon\Carbon::parse($order->ordered_at)->format('M j, Y')); ?></td>
                                            <td>
                                                <a href="/admin/orders/<?php echo e($order->id); ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Vanessa\Desktop\IT12_L\IT12L_FullProject_ERP\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>