<?php $__env->startSection('content'); ?>
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="branch-operations-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h2 class="mb-2"><i class="fas fa-store"></i> <?php echo e($branch->name); ?> Operations</h2>
                <div class="branch-meta">
                    <span><i class="fas fa-map-marker-alt"></i> <?php echo e($branch->address); ?></span>
                    <span class="mx-2">|</span>
                    <span><i class="fas fa-phone"></i> <?php echo e($branch->phone); ?></span>
                </div>
            </div>
            <a href="<?php echo e(route('admin.branches.index')); ?>" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card sales">
                <div class="stat-card-content">
                    <div class="stat-label">Today's Sales</div>
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-value">₱<?php echo e(number_format($todaySales, 2)); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card orders">
                <div class="stat-card-content">
                    <div class="stat-label">Today's Orders</div>
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-value"><?php echo e($todayOrders); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card active">
                <div class="stat-card-content">
                    <div class="stat-label">Active Orders</div>
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-value"><?php echo e($activeOrders->count()); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card menu">
                <div class="stat-card-content">
                    <div class="stat-label">Available Menu</div>
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="stat-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="stat-value"><?php echo e($menuItems->flatten()->count()); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Card -->
    <div class="operations-tabs-card">
        <div class="card-header">
            <ul class="nav nav-tabs" id="branchTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" 
                            type="button" role="tab">
                        <i class="fas fa-list"></i> Active Orders
                        <span class="badge bg-warning ms-2"><?php echo e($activeOrders->count()); ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="recent-tab" data-bs-toggle="tab" data-bs-target="#recent" 
                            type="button" role="tab">
                        <i class="fas fa-history"></i> Recent Orders
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="menu-tab" data-bs-toggle="tab" data-bs-target="#menu" 
                            type="button" role="tab">
                        <i class="fas fa-utensils"></i> Menu Availability
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="branchTabsContent">
                <!-- Active Orders Tab -->
                <div class="tab-pane fade show active" id="orders" role="tabpanel">
                    <?php if($activeOrders->count() > 0): ?>
                        <div class="row">
                            <?php $__currentLoopData = $activeOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="order-card <?php echo e($order->status); ?>">
                                        <div class="order-card-header">
                                            <div class="order-number">#<?php echo e($order->id); ?></div>
                                            <span class="status-badge <?php echo e($order->status); ?>">
                                                <?php echo e(ucfirst($order->status)); ?>

                                            </span>
                                        </div>
                                        <div class="order-card-body">
                                            <div class="order-info-item">
                                                <i class="fas fa-user"></i>
                                                <span><?php echo e($order->user_name ?? 'N/A'); ?></span>
                                            </div>
                                            <div class="order-info-item">
                                                <i class="fas fa-clock"></i>
                                                <span><?php echo e(\Carbon\Carbon::parse($order->created_at)->format('M d, Y h:i A')); ?></span>
                                            </div>
                                            <div class="order-total">
                                                <span>Total:</span>
                                                <span>₱<?php echo e(number_format($order->total_amount, 2)); ?></span>
                                            </div>
                                            <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>" class="btn btn-primary btn-sm w-100 mt-2">
                                                <i class="fas fa-eye"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h4>No Active Orders</h4>
                            <p>There are no active orders at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Recent Orders Tab -->
                <div class="tab-pane fade" id="recent" role="tabpanel">
                    <?php if($recentOrders->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table recent-orders-table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><span class="order-id">#<?php echo e($order->id); ?></span></td>
                                            <td><?php echo e($order->user_name); ?></td>
                                            <td><strong class="text-dark">₱<?php echo e(number_format($order->total_amount, 2)); ?></strong></td>
                                            <td>
                                                <?php
                                                    $statusMap = [
                                                        'pending' => 'pending',
                                                        'confirmed' => 'confirmed',
                                                        'delivered' => 'completed',
                                                        'cancelled' => 'cancelled'
                                                    ];
                                                    $statusClass = $statusMap[$order->status] ?? 'pending';
                                                ?>
                                                <span class="status-badge <?php echo e($statusClass); ?>">
                                                    <?php echo e(ucfirst($order->status)); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e(\Carbon\Carbon::parse($order->created_at)->format('M d, Y')); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h4>No Recent Orders</h4>
                            <p>No recent orders found for this branch.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Menu Availability Tab -->
                <div class="tab-pane fade" id="menu" role="tabpanel">
                    <?php if($menuItems->count() > 0): ?>
                        <div class="row">
                            <?php $__currentLoopData = $menuItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-lg-6 mb-4">
                                    <div class="menu-category-card">
                                        <div class="menu-category-header">
                                            <h6><i class="fas fa-utensils"></i> <?php echo e($category); ?></h6>
                                            <span class="badge bg-light text-dark"><?php echo e($items->count()); ?> items</span>
                                        </div>
                                        <div class="menu-category-body">
                                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="menu-item">
                                                    <div class="menu-item-info">
                                                        <strong><?php echo e($item->name); ?></strong>
                                                        <span class="menu-item-price">₱<?php echo e(number_format($item->price, 2)); ?></span>
                                                    </div>
                                                    <span class="availability-badge">
                                                        <i class="fas fa-check-circle"></i> Available
                                                    </span>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-utensils"></i>
                            <h4>No Menu Items</h4>
                            <p>No menu items available for this branch.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
    /* Branch Operations Header */
    .branch-operations-header {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .branch-operations-header h2 {
        color: white;
        font-weight: 700;
    }

    .branch-meta {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.95rem;
    }

    /* Statistics Cards */
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        height: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }

    .stat-card.sales::before {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
    }

    .stat-card.orders::before {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%);
    }

    .stat-card.active::before {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    }

    .stat-card.menu::before {
        background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .stat-card-content {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .stat-label {
        color: #6c757d;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        color: #212529;
        font-size: 2rem;
        font-weight: 700;
    }

    .stat-icon {
        font-size: 3rem;
        opacity: 0.15;
    }

    .stat-card.sales .stat-icon {
        color: #A52A2A;
    }

    .stat-card.orders .stat-icon {
        color: #198754;
    }

    .stat-card.active .stat-icon {
        color: #ffc107;
    }

    .stat-card.menu .stat-icon {
        color: #0dcaf0;
    }

    /* Operations Tabs Card */
    .operations-tabs-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .operations-tabs-card .card-header {
        background: #A52A2A;
        border-bottom: 2px solid #A52A2A;
        padding: 0;
    }

    .operations-tabs-card .nav-tabs {
        border: none;
        padding: 0.5rem 1rem 0;
    }

    .operations-tabs-card .nav-link {
        border: none;
        color: #000000ff;
        font-weight: 600;
        padding: 1rem 1.5rem;
        margin-right: 0.5rem;
        border-radius: 8px 8px 0 0;
        transition: all 0.3s ease;
    }

    .operations-tabs-card .nav-link:hover {
        background: rgba(165, 42, 42, 0.1);
        color: #A52A2A;
    }

    .operations-tabs-card .nav-link.active {
        background: #8B0000;
        color: #A52A2A;
        border-bottom: 3px solid #A52A2A;
    }

    .operations-tabs-card .card-body {
        padding: 2rem;
    }

    /* Order Cards */
    .order-card {
        background: white;
        border: 2px solid #A52A2A;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .order-card.pending {
        border-color: #A52A2A;
    }

    .order-card.confirmed {
        border-color: #0dcaf0;
    }

    .order-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .order-card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #dee2e6;
    }

    .order-number {
        font-weight: 700;
        font-size: 1.125rem;
        color: #A52A2A;
    }

    .order-card-body {
        padding: 1.25rem;
    }

    .order-info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        color: #495057;
    }

    .order-info-item i {
        color: #A52A2A;
        width: 18px;
    }

    .order-total {
        font-weight: 700;
        font-size: 1.125rem;
        color: #000000ff;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px solid #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Action Buttons - MATCHING ORDER PAGE STYLE WITH !IMPORTANT */
    /* .order-card-body .btn-primary, */
    .recent-orders-table .btn-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
        border: none !important;
        font-weight: 600 !important;
        transition: all 0.3s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.375rem !important;
    }

    /* .order-card-body .btn-primary:hover, */
    .recent-orders-table .btn-primary:hover {
        background: linear-gradient(135deg, #0a58ca 0%, #084298 100%) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.4) !important;
    }

    /* Ensure consistent button sizing */
    /* .order-card-body .btn-sm, */
    .recent-orders-table .btn-sm {
        padding: 0.375rem 0.75rem !important;
        font-size: 0.875rem !important;
    }

    /* Status Badges */
    .status-badge {
        padding: 0.4rem 0.9rem;
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
        color: #0aa2c0;
    }

    .status-badge.completed {
        color: #146c43;
    }

    .status-badge.cancelled {
        color: #bb2d3b;
    }

    /* Recent Orders Table */
    .recent-orders-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .recent-orders-table thead th {
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        text-transform: uppercase;
        font-size: 0.875rem;
    }

    .recent-orders-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #dee2e6;
    }

    .recent-orders-table tbody tr:hover {
        background-color: rgba(165, 42, 42, 0.05);
    }

    .recent-orders-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    .order-id {
        font-weight: 700;
        color: #A52A2A;
    }

    /* Menu Category Card */
    .menu-category-card {
        background: white;
        border: 2px solid #dee2e6;
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
    }

    .menu-category-header {
        background: #A52A2A;
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #A52A2A;
    }

    .menu-category-header h6 {
        margin: 0;
        color: #ffffffff;
        font-weight: 600;
    }

    .menu-category-body {
        padding: 1rem;
    }

    .menu-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        border-bottom: 1.5px solid #A52A2A;
        transition: all 0.2s ease;
        outline: #212529;
    }

    .menu-item:last-child {
        border-bottom: none;
    }

    .menu-item:hover {
        background: rgba(165, 42, 42, 0.05);
    }

    .menu-item-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .menu-item-price {
        color: #000000ff;
        font-weight: 600;
    }

    .availability-badge {
        color: #146c43;
        padding: 0.35rem 0.75rem;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
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
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .branch-operations-header {
            padding: 1.5rem;
        }

        .operations-tabs-card .card-body {
            padding: 1.5rem;
        }

        .stat-value {
            font-size: 1.75rem;
        }

        .stat-icon {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 768px) {
        .branch-operations-header {
            padding: 1rem;
        }

        .operations-tabs-card .nav-link {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }

        .operations-tabs-card .card-body {
            padding: 1rem;
        }
    }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Videos\Project_IT12\sabio\IT12L_Project\IT12L_FullProject_ERP\resources\views/admin/branches/show.blade.php ENDPATH**/ ?>