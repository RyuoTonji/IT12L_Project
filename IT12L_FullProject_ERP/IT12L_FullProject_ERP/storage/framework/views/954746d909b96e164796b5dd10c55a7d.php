

<?php $__env->startSection('content'); ?>
<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">BBQ Lagao & Beef Pares</h1>
                <p class="lead">Delicious BBQ and Pares delivered to your doorstep!</p>
                <a href="#branches" class="btn btn-light btn-lg">
                    <i class="fas fa-utensils"></i> Order Now
                </a>
            </div>
            <div class="col-md-6 text-center">
                <i class="fas fa-hamburger fa-10x opacity-75"></i>
            </div>
        </div>
    </div>
</div>

<!-- Branches Section -->
<div class="container my-5" id="branches">
    <h2 class="text-center mb-4">Select Your Branch</h2>
    
    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <h3 class="card-title">
                        <i class="fas fa-store text-primary"></i> <?php echo e($branch->name); ?>

                    </h3>
                    <p class="card-text">
                        <i class="fas fa-map-marker-alt text-danger"></i> <?php echo e($branch->address); ?><br>
                        <i class="fas fa-phone text-success"></i> <?php echo e($branch->phone); ?>

                    </p>
                    <a href="<?php echo e(route('browse', $branch->id)); ?>" class="btn btn-primary w-100">
                        <i class="fas fa-utensils"></i> Browse Menu
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No branches available at the moment.
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Features Section -->
<div class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-4">Why Choose Us?</h2>
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                <h4>Fast Delivery</h4>
                <p class="text-muted">Quick delivery to your location</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-utensils fa-3x text-primary mb-3"></i>
                <h4>Fresh Food</h4>
                <p class="text-muted">Made fresh with quality ingredients</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-money-bill-wave fa-3x text-primary mb-3"></i>
                <h4>Affordable Prices</h4>
                <p class="text-muted">Great value for your money</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.hover-shadow {
    transition: box-shadow 0.3s ease;
}
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kates\Documents\IT12final\IT12L_FullProject_ERP\resources\views/user/home/index.blade.php ENDPATH**/ ?>