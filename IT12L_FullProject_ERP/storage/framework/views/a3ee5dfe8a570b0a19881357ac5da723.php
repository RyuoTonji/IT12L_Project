<?php $__env->startSection('content'); ?>
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-store"></i> Manage Branches</h2>
        <a href="<?php echo e(route('admin.branches.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Branch
        </a>
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
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($branch->id); ?></td>
                            <td><?php echo e($branch->name); ?></td>
                            <td><?php echo e($branch->address); ?></td>
                            <td><?php echo e($branch->phone ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($branch->is_active ? 'success' : 'danger'); ?>">
                                    <?php echo e($branch->is_active ? 'Active' : 'Inactive'); ?>

                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo e(route('admin.branches.edit', $branch->id)); ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('admin.branches.destroy', $branch->id)); ?>" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this branch?');"
                                          class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center">No branches found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                <?php echo e($branches->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Vanessa\Desktop\IT12_L\IT12L_FullProject_ERP\resources\views/admin/branches/index.blade.php ENDPATH**/ ?>