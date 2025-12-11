<?php $__env->startSection('content'); ?>
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-archive"></i> Archived Categories</h2>
        <a href="<?php echo e(route('admin.categories.index')); ?>" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Active Categories
        </a>
    </div>

    <!-- <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?> -->

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Created Date</th>
                            <th>Archived Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>#<?php echo e($category->id); ?></td>
                            <td><strong><?php echo e($category->name); ?></strong></td>
                            <td>
                                <small class="text-muted">
                                    <?php echo e($category->description ? Str::limit($category->description, 60) : 'N/A'); ?>

                                </small>
                            </td>
                            <td><?php echo e(\Carbon\Carbon::parse($category->created_at)->format('M j, Y g:i A')); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($category->deleted_at)->format('M j, Y g:i A')); ?></td>
                            <td>
                                <form action="<?php echo e(route('admin.categories.restore', $category->id)); ?>" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to restore this category?');"
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
                            <td colspan="6" class="text-center">No archived categories found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <?php echo e($categories->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kates\Documents\IT12final\IT12L_FullProject_ERP\resources\views/admin/categories/archived.blade.php ENDPATH**/ ?>