<link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">


<?php $__env->startSection('content'); ?>
<div class="container my-4" style="border-color: #A52A2A;">
    <!-- Branch Header -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 style="color: #A52A2A;">
                <i class="fas fa-store" style="color: #A52A2A;"></i> <?php echo e($branch->name); ?>

            </h2>
            <p class="mb-0">
                <i class="fas fa-map-marker-alt text-danger"></i> <?php echo e($branch->address); ?> | 
                <i class="fas fa-phone text-success"></i> <?php echo e($branch->phone); ?>

            </p>
        </div>
    </div>

   <!-- Category Filter -->
<?php
    // Grab the category from the URL segment
    $currentCategory = request()->segment(4);
?>

<!-- Category Filter -->
<div class="card mb-4" style="border-color:#A52A2A;">
    <div class="card-body">
        <h5 style="color:#A52A2A;">
            <i class="fas fa-filter" style="color:#A52A2A;"></i> Filter by Category
        </h5>

        <div class="btn-group flex-wrap" role="group">

            <!-- All Items -->
            <a href="<?php echo e(route('browse', $branch->id)); ?>"
               class="btn <?php echo e(!$currentCategory ? 'btn-maroon-active' : 'btn-maroon-outline'); ?>">
                All Items
            </a>

            <!-- Dynamic Categories -->
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(url('/browse/' . $branch->id . '/category/' . $category->id)); ?>"
               class="btn <?php echo e($currentCategory == $category->id ? 'btn-maroon-active' : 'btn-maroon-outline'); ?>">
                <?php echo e($category->name); ?>

            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </div>
    </div>
</div>



    <!-- Products Grid -->
    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card h-100 product-card">
                <?php if($product->image): ?>
                <img src="<?php echo e(asset('storage/' . $product->image)); ?>" 
                     class="card-img-top" 
                     alt="<?php echo e($product->name); ?>"
                     style="height: 200px; object-fit: cover;">
                <?php else: ?>
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" 
                     style="height: 200px;">
                    <i class="fas fa-utensils fa-3x text-white"></i>
                </div>
                <?php endif; ?>
                
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo e($product->name); ?></h5>
                    <p class="text-muted small mb-2">
                        <i class="fas fa-tag"></i> <?php echo e($product->category_name); ?>

                    </p>
                    <div class="mt-auto">
                        <h4 class="text-maroon mb-3">₱<?php echo e(number_format($product->price, 2)); ?></h4>
                        
                        <?php if($product->is_available): ?>
                        <button class="btn btn-maroon w-100 add-to-cart-btn" 
                                data-product="<?php echo e(json_encode([
                                    'id' => $product->id,
                                    'name' => $product->name,
                                    'price' => $product->price,
                                    'image' => $product->image,
                                    'branch_id' => $product->branch_id,
                                    'branch_name' => $branch->name
                                ])); ?>">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                        <?php else: ?>
                        <button class="btn btn-secondary w-100" disabled>
                            <i class="fas fa-ban"></i> Unavailable
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No products available in this category.
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if($products->hasPages()): ?>
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($products->links()); ?>

    </div>
    <?php endif; ?>
</div>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="cartToast" class="toast" role="alert">
        <div class="toast-header bg-success text-white">
            <i class="fas fa-check-circle me-2"></i>
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            Item added to cart!
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.product-card {
    transition: transform 0.2s;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Add to Cart Functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Browse page loaded');
    
    // Check if cart functions are available
    if (typeof addToCart === 'undefined') {
        console.error('addToCart function not found! Make sure cart.js is loaded.');
    }
    
    // Update cart count on page load
    if (typeof updateCartCount !== 'undefined') {
        updateCartCount();
    }
    
    // Add event listeners to all "Add to Cart" buttons
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Add to cart button clicked');
            
            try {
                const productData = JSON.parse(this.dataset.product);
                console.log('Product data:', productData);
                
                // Check if admin (using global function from cart.js)
                if (typeof isAdmin !== 'undefined' && isAdmin()) {
                    if (typeof showAlert !== 'undefined') {
                        showAlert('warning', 'Administrators cannot add items to cart.', 3000);
                    } else {
                        alert('Administrators cannot add items to cart.');
                    }
                    return;
                }
                
                // Add to cart using cart.js function
                if (typeof addToCart !== 'undefined') {
                    addToCart(productData, 1);
                    showToast();
                } else {
                    console.error('addToCart function is not defined');
                    alert('Error: Cart system not loaded properly');
                }
                
            } catch (error) {
                console.error('Error adding to cart:', error);
                alert('Error adding item to cart');
            }
        });
    });
});

function showToast() {
    const toastEl = document.getElementById('cartToast');
    if (toastEl) {
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Videos\Project_IT12\llena\IT12L_FullProject_ERP\resources\views/user/home/browse.blade.php ENDPATH**/ ?>