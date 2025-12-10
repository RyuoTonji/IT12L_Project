

<?php $__env->startSection('content'); ?>
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h2 class="mb-0"><i class="fas fa-store"></i> Manage Branches</h2>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('admin.branches.archived')); ?>" class="btn btn-light">
                    <i class="fas fa-archive"></i> View Archived
                </a>
                <a href="<?php echo e(route('admin.branches.create')); ?>" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Branch
                </a>
            </div>
        </div>
    </div>

    <!-- Branches Grid -->
    <div class="row">
        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-6 mb-4">
                <div class="branch-card">
                    <!-- Branch Header -->
                    <div class="branch-card-header">
                        <div class="branch-info-header">
                            <div>
                                <h4 class="branch-name"><?php echo e($branch->name); ?></h4>
                                <?php if(isset($branch->code)): ?>
                                    <p class="branch-code"><?php echo e($branch->code); ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="status-badge <?php echo e($branch->is_active ? 'active' : 'inactive'); ?>">
                                <?php if($branch->is_active): ?>
                                    <i class="fas fa-check-circle"></i> Active
                                <?php else: ?>
                                    <i class="fas fa-times-circle"></i> Inactive
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Branch Details -->
                    <div class="branch-card-body">
                        <div class="branch-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo e($branch->address ?? 'N/A'); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span><?php echo e($branch->phone ?? 'N/A'); ?></span>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="branch-stats">
                            <div class="stat-item">
                                <div class="stat-value orders"><?php echo e($branch->orders_count ?? 0); ?></div>
                                <div class="stat-label">Total Orders</div>
                            </div>
                            <div class="stat-divider"></div>
                            <div class="stat-item">
                                <div class="stat-value menu"><?php echo e($branch->available_menu_items_count ?? 0); ?></div>
                                <div class="stat-label">Available Menu</div>
                            </div>
                            <div class="stat-divider"></div>
                            <div class="stat-item">
                                <div class="stat-value inventory"><?php echo e($branch->inventories_count ?? 0); ?></div>
                                <div class="stat-label">Inventory Items</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="branch-actions">
                            <a href="<?php echo e(route('admin.branches.show', $branch->id)); ?>" class="btn btn-primary flex-fill">
                                <i class="fas fa-eye"></i> View Operations
                            </a>
                            <a href="<?php echo e(route('admin.branches.edit', $branch->id)); ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="<?php echo e(route('admin.branches.destroy', $branch->id)); ?>" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to archive this branch?');"
                                  class="d-inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Archive
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Pagination -->
    <?php if($branches->hasPages()): ?>
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Showing <?php echo e($branches->firstItem()); ?> to <?php echo e($branches->lastItem()); ?> of <?php echo e($branches->total()); ?> branches
        </div>
        <?php echo e($branches->links()); ?>

    </div>
    <?php endif; ?>
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

    /* Branch Card */
    .branch-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .branch-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    /* Branch Card Header */
    .branch-card-header {
        background-color: #A52A2A;
        padding: 1.5rem;
        border-bottom: 3px solid #A52A2A;
    }

    .branch-info-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
    }

    .branch-name {
        color: #ffffffff;
        font-weight: 700;
        font-size: 1.5rem;
        margin: 0;
    }

    .branch-code {
        color: #6c757d;
        font-size: 0.875rem;
        margin: 0.25rem 0 0 0;
    }

    /* Status Badge - Updated: No background for active */
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    .status-badge.active {
        background: transparent;
        color: #198754;
    }

    .status-badge.inactive {
        background: transparent;
        color: #dc3545;
    }

    /* Branch Card Body */
    .branch-card-body {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    /* Branch Details */
    .branch-details {
        margin-bottom: 1.5rem;
    }

    .detail-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.5rem 0;
        color: #495057;
    }

    .detail-item i {
        color: #A52A2A;
        font-size: 1.125rem;
        width: 20px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .detail-item span {
        line-height: 1.5;
        font-size: 1.225rem;
        font-weight: 500;
    }

    /* Branch Statistics */
    .branch-stats {
        display: flex;
        justify-content: space-around;
        align-items: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 1.5rem 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-item {
        text-align: center;
        flex: 1;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-value.orders {
        color: #A52A2A;
    }

    .stat-value.menu {
        color: #198754;
    }

    .stat-value.inventory {
        color: #0dcaf0;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-divider {
        width: 1px;
        height: 40px;
        background: #dee2e6;
    }

    /* Branch Actions */
    .branch-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: auto;
        align-items: stretch;
    }

    .branch-actions .btn {
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        white-space: nowrap;
        padding: 0.5rem 1rem;
    }

    /* Pagination */
    .pagination-wrapper {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
    }

    .pagination-info {
        color: #6c757d;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .page-header {
            padding: 1.5rem;
        }

        .page-header h2 {
            font-size: 1.5rem;
        }

        .branch-actions {
            flex-wrap: wrap;
        }

        .branch-actions .btn {
            flex: 1 1 calc(50% - 0.25rem);
        }

        .branch-actions .flex-fill {
            flex: 1 1 100%;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 1rem;
        }

        .branch-card-header,
        .branch-card-body {
            padding: 1rem;
        }

        .branch-stats {
            flex-direction: column;
            gap: 1rem;
            padding: 1rem;
        }

        .stat-divider {
            width: 100%;
            height: 1px;
        }

        .branch-actions {
            flex-direction: column;
        }

        .branch-actions .btn {
            width: 100%;
        }

        .pagination-wrapper {
            flex-direction: column;
            gap: 1rem;
        }
    }

    @media (max-width: 576px) {
        .branch-name {
            font-size: 1.25rem;
        }

        .stat-value {
            font-size: 1.5rem;
        }
    }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kates\Documents\IT12final\IT12L_FullProject_ERP\resources\views/admin/branches/index.blade.php ENDPATH**/ ?>