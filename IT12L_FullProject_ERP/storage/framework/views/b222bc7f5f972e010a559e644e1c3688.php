

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
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="card-title mb-2">
                                        Order #<?php echo e($order->id); ?>

                                        <?php
                                            $statusClasses = [
                                                'pending' => 'warning',
                                                'confirmed' => 'info',
                                                'delivered' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $badgeClass = $statusClasses[$order->status] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo e($badgeClass); ?>">
                                            <?php echo e(ucfirst($order->status)); ?>

                                        </span>
                                    </h5>
                                    
                                    <p class="card-text text-muted mb-2">
                                        <i class="fas fa-store"></i> <?php echo e($order->branch->name); ?>

                                        <br>
                                        <i class="fas fa-calendar"></i> 
                                        <?php echo e($order->ordered_at->format('F j, Y g:i A')); ?>

                                    </p>

                                    <p class="card-text mb-0">
                                        <strong>Total:</strong> 
                                        <span class="text-primary fs-5">
                                            ₱<?php echo e(number_format($order->total_amount, 2)); ?>

                                        </span>
                                    </p>
                                </div>

                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="btn btn-primary mb-2">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    
                                    <?php if($order->status === 'pending'): ?>
                                        <button class="btn btn-danger cancel-order" 
                                                data-order-id="<?php echo e($order->id); ?>">
                                            <i class="fas fa-times"></i> Cancel Order
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kates\Documents\IT12final\IT12L_FullProject_ERP\resources\views/user/orders/index.blade.php ENDPATH**/ ?>