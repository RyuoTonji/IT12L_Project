<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="/browse/<?= $product['branch_id'] ?>"><?= htmlspecialchars($product['branch_name']) ?></a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-6">
            <?php if ($product['image']): ?>
                <img src="<?= htmlspecialchars($product['image']) ?>" 
                     class="img-fluid rounded shadow" 
                     alt="<?= htmlspecialchars($product['name']) ?>">
            <?php else: ?>
                <div class="bg-light rounded shadow d-flex align-items-center justify-content-center" 
                     style="height: 400px;">
                    <i class="fas fa-utensils fa-5x text-muted"></i>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <h1 class="mb-3"><?= htmlspecialchars($product['name']) ?></h1>
            
            <div class="mb-3">
                <span class="badge bg-primary"><?= htmlspecialchars($product['category_name']) ?></span>
                <span class="badge bg-info"><?= htmlspecialchars($product['branch_name']) ?></span>
                <?php if ($product['is_available']): ?>
                    <span class="badge bg-success">Available</span>
                <?php else: ?>
                    <span class="badge bg-danger">Out of Stock</span>
                <?php endif; ?>
            </div>

            <h2 class="text-primary mb-4">₱<?= number_format($product['price'], 2) ?></h2>

            <?php if ($product['is_available']): ?>
                <div class="mb-4">
                    <label for="quantity" class="form-label fw-bold">Quantity:</label>
                    <div class="input-group" style="max-width: 200px;">
                        <button class="btn btn-outline-secondary" type="button" id="decrease-qty">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="99">
                        <button class="btn btn-outline-secondary" type="button" id="increase-qty">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg" id="add-to-cart-detail"
                            data-product-id="<?= $product['id'] ?>"
                            data-product-name="<?= htmlspecialchars($product['name']) ?>"
                            data-product-price="<?= $product['price'] ?>">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    <a href="/browse/<?= $product['branch_id'] ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> This item is currently out of stock.
                </div>
                <a href="/browse/<?= $product['branch_id'] ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Menu
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const decreaseBtn = document.getElementById('decrease-qty');
    const increaseBtn = document.getElementById('increase-qty');
    const addToCartBtn = document.getElementById('add-to-cart-detail');

    // Quantity controls
    decreaseBtn?.addEventListener('click', () => {
        let value = parseInt(quantityInput.value);
        if (value > 1) {
            quantityInput.value = value - 1;
        }
    });

    increaseBtn?.addEventListener('click', () => {
        let value = parseInt(quantityInput.value);
        if (value < 99) {
            quantityInput.value = value + 1;
        }
    });

    // Add to cart with custom quantity
    addToCartBtn?.addEventListener('click', function() {
        const quantity = parseInt(quantityInput.value);
        const productId = this.dataset.productId;
        const productName = this.dataset.productName;
        
        addToCart(productId, quantity, productName);
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>