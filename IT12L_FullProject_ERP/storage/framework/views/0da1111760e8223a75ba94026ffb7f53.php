<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <h2 class="mb-4">
        <i class="fas fa-box"></i> My Orders
    </h2>

    <?php if($orders->isEmpty()): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h4>No orders yet</h4>
                <p class="text-muted">Start ordering your favorite meals!</p>
                <a href="<?php echo e(route('home')); ?>" class="btn btn-primary">
                    <i class="fas fa-utensils"></i> Browse Menu
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-12">
                    <div class="card order-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-9">
                                    <h5 class="card-title mb-3">
                                        Order #<?php echo e($order->id); ?>

                                        <?php
                                            $statusClasses = [
                                                'pending' => 'warning',
                                                'confirmed' => 'info',
                                                'preparing' => 'primary',
                                                'ready' => 'orange',
                                                'picked up' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $textClass = $statusClasses[$order->status] ?? 'secondary';
                                        ?>
                                        <span class="text-<?php echo e($textClass); ?> fw-semibold">
                                            <?php echo e($order->status === 'ready' ? 'Ready for Pickup' : ucfirst($order->status)); ?>

                                        </span>
                                    </h5>
                                    
                                    <p class="card-text text-muted mb-2">
                                        <i class="fas fa-store me-1"></i> <?php echo e($order->branch->name); ?>

                                    </p>
                                    
                                    <p class="card-text text-muted mb-3">
                                        <i class="fas fa-calendar me-1"></i> 
                                        <?php echo e($order->ordered_at->format('F j, Y g:i A')); ?>

                                    </p>

                                    <p class="card-text mb-0">
                                        <strong>Total:</strong> 
                                        <span class="text-dark fs-5 fw-bold">
                                            ₱<?php echo e(number_format($order->total_amount, 2)); ?>

                                        </span>
                                    </p>
                                </div>

                                <div class="col-md-3 d-flex flex-column justify-content-center align-items-stretch gap-2">
                                    <a href="<?php echo e(route('orders.show', $order->id)); ?>" 
                                    class="btn btn-primary d-flex align-items-center justify-content-center gap-2 px-3 py-2 text-nowrap">
                                        <i class="fas fa-eye"></i>
                                        <span>View Details</span>
                                    </a>

                                    <?php if($order->status === 'pending'): ?>
                                        <button class="btn btn-danger cancel-order d-flex align-items-center justify-content-center gap-2 px-3 py-2 text-nowrap"
                                                data-order-id="<?php echo e($order->id); ?>">
                                            <i class="fas fa-times"></i>
                                            <span>Cancel Order</span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('styles'); ?>
<style>
    .order-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #e0e0e0;
    }
    
    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    
    .order-card .btn {
        white-space: nowrap;
    }

    /* Custom orange color for "ready" status */
    .text-orange {
        color: #fd7e14 !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.querySelectorAll('.cancel-order').forEach(button => {
    button.addEventListener('click', function() {
        const orderId = this.dataset.orderId;
        
        if (confirm('Are you sure you want to cancel this order?')) {
            cancelOrder(orderId);
        }
    });
});

function cancelOrder(orderId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    fetch(`/orders/${orderId}/cancel`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            alert(data.message || 'Failed to cancel order');
        }
    })
    .catch(error => {
        alert('An error occurred. Please try again.');
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Videos\projectIT12\IT12L_FullProject_ERP\resources\views/user/orders/index.blade.php ENDPATH**/ ?>