@extends('layouts.app')

@section('content')
<div class="container my-4">
    <!-- Branch Header -->
    <div class="card mb-4">
        <div class="card-body">
            <h2>
                <i class="fas fa-store text-primary"></i> {{ $branch->name }}
            </h2>
            <p class="mb-0">
                <i class="fas fa-map-marker-alt text-danger"></i> {{ $branch->address }} | 
                <i class="fas fa-phone text-success"></i> {{ $branch->phone }}
            </p>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <h5><i class="fas fa-filter"></i> Filter by Category</h5>
            <div class="btn-group flex-wrap" role="group">
                <a href="{{ route('browse', $branch->id) }}" 
                   class="btn btn-outline-primary {{ !request('category') ? 'active' : '' }}">
                    All Items
                </a>
                @foreach($categories as $category)
                <a href="{{ url('/browse/' . $branch->id . '/category/' . $category->id) }}" 
                   class="btn btn-outline-primary {{ request('category') == $category->id ? 'active' : '' }}">
                    {{ $category->name }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row">
        @forelse($products as $product)
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card h-100 product-card">
                @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" 
                     class="card-img-top" 
                     alt="{{ $product->name }}"
                     style="height: 200px; object-fit: cover;">
                @else
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" 
                     style="height: 200px;">
                    <i class="fas fa-utensils fa-3x text-white"></i>
                </div>
                @endif
                
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="text-muted small mb-2">
                        <i class="fas fa-tag"></i> {{ $product->category_name }}
                    </p>
                    <div class="mt-auto">
                        <h4 class="text-primary mb-3">₱{{ number_format($product->price, 2) }}</h4>
                        
                        @if($product->is_available)
                        <button class="btn btn-primary w-100 add-to-cart-btn" 
                                data-product="{{ json_encode([
                                    'id' => $product->id,
                                    'name' => $product->name,
                                    'price' => $product->price,
                                    'image' => $product->image,
                                    'branch_id' => $product->branch_id,
                                    'branch_name' => $branch->name
                                ]) }}">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                        @else
                        <button class="btn btn-secondary w-100" disabled>
                            <i class="fas fa-ban"></i> Unavailable
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No products available in this category.
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
    @endif
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
@endsection

@push('styles')
<style>
.product-card {
    transition: transform 0.2s;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>
@endpush

@push('scripts')
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
@endpush