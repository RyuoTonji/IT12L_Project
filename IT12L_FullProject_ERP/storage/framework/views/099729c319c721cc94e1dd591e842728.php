<?php $__env->startSection('content'); ?>
<div class="container-fluid my-4">
    <!-- Header Section -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h1 class="h2 mb-0 text-white"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
            <div>
                <span class="text-white opacity-75">Welcome, <?php echo e(auth()->user()->name); ?></span>
            </div>
        </div>
    </div>

    <!-- Date Filter Card -->
    <div class="filters-card mb-4">
        <h5><i class="fas fa-calendar-alt"></i> Date Filter</h5>
        <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>">
            <div class="filter-group">
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo e($startDate); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo e($endDate); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
                <?php if($startDate || $endDate): ?>
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-secondary w-100">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Combined Analytics Section -->
    <div class="mb-4">
        <div class="section-header mb-4">
            <h2 class="h5 mb-0">
                <i class="fas fa-chart-bar"></i> Overall Performance
            </h2>
        </div>
        
        <div class="row g-4 mb-4">
            <!-- Total Sales (All Time) -->
            <div class="col-md-6">
                <div class="stat-card stat-card-large">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="w-100">
                                <h6 class="stat-subtitle">Total Sales (All Time)</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="stat-icon-large">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <h2 class="stat-value">₱<?php echo e(number_format($totalRevenueAllTime ?? 0, 2)); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Orders (All Time) -->
            <div class="col-md-6">
                <div class="stat-card stat-card-large">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="w-100">
                                <h6 class="stat-subtitle">Total Orders (All Time)</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="stat-icon-large">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <h2 class="stat-value"><?php echo e(number_format($totalOrdersAllTime ?? 0, )); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Statistics -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card stat-card-small stat-card-today-sales">
                    <div class="card-body">
                        <h6 class="stat-subtitle-small">Today's Sales</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="stat-icon-small">
                                <i class="fas fa-peso-sign"></i>
                            </div>
                            <h3 class="stat-value-small">₱<?php echo e(number_format($todayRevenue ?? 0, 2)); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-card-small stat-card-today-orders">
                    <div class="card-body">
                        <h6 class="stat-subtitle-small">Today's Orders</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="stat-icon-small">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <h3 class="stat-value-small"><?php echo e(number_format($todayOrders ?? 0)); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-card-small stat-card-pending">
                    <div class="card-body">
                        <h6 class="stat-subtitle-small">Pending Orders</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="stat-icon-small">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3 class="stat-value-small"><?php echo e(number_format($pendingOrders ?? 0)); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-card-small stat-card-products">
                    <div class="card-body">
                        <h6 class="stat-subtitle-small">Total Products</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="stat-icon-small">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <h3 class="stat-value-small"><?php echo e(number_format($totalProducts ?? 0)); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales & Products Section -->
    <div class="row g-4 mb-4 mt-4">
        <!-- Sales by Branch -->
        <div class="col-lg-6">
            <div class="data-card">
                <div class="data-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-store"></i> Sales by Branch
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table table">
                            <thead>
                                <tr>
                                    <th>Branch</th>
                                    <th class="text">Orders</th>
                                    <th class="text">Total Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $salesByBranch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="fw-medium"><?php echo e($branch->branch_name); ?></td>
                                    <td class="text"><?php echo e(number_format($branch->total_orders)); ?></td>
                                    <td class="text fw-semibold text-dark">₱<?php echo e(number_format($branch->total_sales, 2)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3">
                                        <div class="empty-state-small">
                                            <i class="fas fa-inbox"></i>
                                            <p class="mb-0">No sales data available</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="col-lg-6">
            <div class="data-card">
                <div class="data-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy"></i> Top Selling Products
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text">Quantity</th>
                                    <th class="text">Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="fw-medium"><?php echo e($product->name); ?></td>
                                    <td class="text"><?php echo e(number_format($product->total_quantity)); ?></td>
                                    <td class="text fw-semibold text-dark">₱<?php echo e(number_format($product->total_sales, 2)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3">
                                        <div class="empty-state-small">
                                            <i class="fas fa-inbox"></i>
                                            <p class="mb-0">No product data available</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="data-card">
                <div class="data-card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-sync"></i> Real-Time Transactions
                        </h5>
                        <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-sm btn-light">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($recentOrders->isEmpty()): ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h4>No Recent Orders</h4>
                            <p>There are no recent orders to display</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="data-table table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Branch</th>
                                        <th class="text-end">Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <span class="order-id">#<?php echo e($order->id); ?></span>
                                            </td>
                                            <td><?php echo e($order->user_name); ?></td>
                                            <td>
                                                <span class="branch-badge">
                                                    <i class="fas fa-store"></i> <?php echo e($order->branch_name); ?>

                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <span class="amount-text">₱<?php echo e(number_format($order->total_amount, 2)); ?></span>
                                            </td>
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
                                                <span class="status-badge status-<?php echo e($badgeClass); ?>">
                                                    <?php echo e(ucfirst($order->status)); ?>

                                                </span>
                                            </td>
                                            <td class="text-muted"><?php echo e(\Carbon\Carbon::parse($order->ordered_at)->diffForHumans()); ?></td>
                                            <td class="text-center">
                                                <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
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

    /* Section Header */
    .section-header {
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #A52A2A;
    }

    .section-header h2 {
        color: #A52A2A;
        font-weight: 600;
    }

    /* Filters Card - Balanced Size */
    .filters-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .filters-card h5 {
        color: #A52A2A;
        font-weight: 600;
        margin-bottom: 0.875rem;
        font-size: 1rem;
    }

    .filter-group {
        display: flex;
        gap: 0.875rem;
        flex-wrap: wrap;
        align-items: end;
    }

    .filter-group .form-group {
        flex: 1;
        min-width: 150px;
    }

    .filter-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.4rem;
        display: block;
        font-size: 0.875rem;
    }

    .filter-group .form-control {
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 0.5rem 0.875rem;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .filter-group .form-control:focus {
        border-color: #A52A2A;
        box-shadow: 0 0 0 0.2rem rgba(165, 42, 42, 0.15);
    }

    .filter-group .btn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border-left: 4px solid #A52A2A;
    }

    .stat-card:hover {
/*      
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); */
    }

    .stat-card-large {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        border-left: none;
    }

    .stat-card-large .card-body {
        padding: 2rem;
    }

    .stat-card-large .stat-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .stat-card-large .stat-value {
        color: white;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .stat-card-large .stat-icon-large {
        font-size: 3.5rem;
        color: rgba(255, 255, 255, 0.3);
    }

    /* Small Stat Cards - Individual colors */
    .stat-card-small {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .stat-card-small:hover {
 
        /* box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); */
    }

    .stat-card-small .card-body {
        padding: 1.5rem;
    }

    .stat-card-small .stat-subtitle-small {
        color: #6c757d;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .stat-card-small .stat-value-small {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
    }

    .stat-card-small .stat-icon-small {
        font-size: 2.5rem;
    }

    /* Today's Sales - Green */
    .stat-card-today-sales {
        border-left: 4px solid #28A745;
    }

    .stat-card-today-sales .stat-value-small {
        color: #28A745;
    }

    .stat-card-today-sales .stat-icon-small {
        color: rgba(40, 167, 69, 0.2);
    }

    /* Today's Orders - Blue */
    .stat-card-today-orders {
        border-left: 4px solid #007BFF;
    }

    .stat-card-today-orders .stat-value-small {
        color: #007BFF;
    }

    .stat-card-today-orders .stat-icon-small {
        color: rgba(0, 123, 255, 0.2);
    }

    /* Pending Orders - Orange */
    .stat-card-pending {
        border-left: 4px solid #FD7E14;
    }

    .stat-card-pending .stat-value-small {
        color: #FD7E14;
    }

    .stat-card-pending .stat-icon-small {
        color: rgba(253, 126, 20, 0.2);
    }

    /* Total Products - Purple */
    .stat-card-products {
        border-left: 4px solid #6610F2;
    }

    .stat-card-products .stat-value-small {
        color: #6610F2;
    }

    .stat-card-products .stat-icon-small {
        color: rgba(102, 16, 242, 0.2);
    }

    /* Data Cards */
    .data-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        height: 100%;
    }

    .data-card-header {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        color: white;
        padding: 1.25rem 1.5rem;
    }

    .data-card-header h5 {
        color: white;
        font-weight: 600;
    }

    .data-card-header .btn-light {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .data-card-header .btn-light:hover {
        background: white;
        transform: translateY(-2px);
    }

    .data-card .card-body {
        padding: 0;
    }

    /* Data Table */
    .data-table {
        margin-bottom: 0;
    }

    .data-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .data-table thead th {
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        border: none;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        text-align: center;
    }

    .data-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #dee2e6;
        text-align: center;
    }

    .data-table tbody tr:hover {
        background-color: rgba(165, 42, 42, 0.05);
    }

    .data-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    /* Order ID */
    .order-id {
        font-weight: 700;
        color: #A52A2A;
        font-size: 0.95rem;
    }

    /* Branch Badge */
    .branch-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #d9480f;
    }

    /* Amount Text */
    .amount-text {
        font-weight: 700;
        color: #000000ff;
        font-size: 1rem;
    }

    /* Status Badges */
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-block;
        text-transform: capitalize;
    }

    .status-warning {
        color: #ffc107;
    }

    .status-info {
        color: #0dcaf0;
    }

    .status-success {
        color: #198754;
    }

    .status-danger {
        color: #dc3545;
    }

    .status-secondary {
        color: #6c757d;
    }

    /* Action Buttons - Matching Orders Page */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: center;
        align-items: stretch;
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

    /* Override any existing btn-primary styles */
    .data-table .btn-primary,
    .btn-primary.btn-sm {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
        border: none !important;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white !important;
    }

    .data-table .btn-primary:hover,
    .btn-primary.btn-sm:hover {
        background: linear-gradient(135deg, #0a58ca 0%, #084298 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.4);
        color: white !important;
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #545b62 100%);
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
    }

    /* Empty States */
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
        margin-bottom: 0;
    }

    .empty-state-small {
        text-align: center;
        padding: 2rem 1rem;
    }

    .empty-state-small i {
        font-size: 2rem;
        color: #dee2e6;
        margin-bottom: 0.5rem;
        display: block;
    }

    .empty-state-small p {
        color: #6c757d;
        font-size: 0.875rem;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .page-header {
            padding: 1.5rem;
        }

        .stat-card-large .stat-value {
            font-size: 2rem;
        }

        .stat-icon-large {
            font-size: 2.5rem !important;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 1rem;
        }

        .filter-group {
            flex-direction: column;
        }

        .filter-group .form-group {
            width: 100%;
        }

        .data-card-header .d-flex {
            flex-direction: column;
            gap: 1rem;
        }

        .data-card-header .btn {
            width: 100%;
        }

        /* Make table scrollable on mobile */
        .table-responsive {
            overflow-x: auto;
        }

        .data-table {
            min-width: 800px;
        }
    }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Videos\Project_IT12\sabio\IT12L_Project\IT12L_FullProject_ERP\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>