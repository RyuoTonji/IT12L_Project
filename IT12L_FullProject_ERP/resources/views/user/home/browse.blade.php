@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">

@section('content')
<div class="container my-4" style="border-color: #A52A2A;">
    <!-- Branch Header -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 style="color: #A52A2A;">
                <i class="fas fa-store" style="color: #A52A2A;"></i> {{ $branch->name }}
            </h2>
            <p class="mb-0">
                <i class="fas fa-map-marker-alt text-danger"></i> {{ $branch->address }} |
                <i class="fas fa-phone text-success"></i> {{ $branch->phone }}
            </p>
        </div>
    </div>

    <!-- Category Filter -->
    @php
    // Grab the category from the URL segment
    $currentCategory = request()->segment(4);
    @endphp

    <!-- Category Filter -->
    <div class="card mb-4" style="border-color:#A52A2A;">
        <div class="card-body">
            <h5 style="color:#A52A2A;">
                <i class="fas fa-filter" style="color:#A52A2A;"></i> Filter by Category
            </h5>

            <div class="btn-group flex-wrap" role="group">

                <!-- All Items -->
                <a href="{{ route('browse', $branch->id) }}"
                    class="btn {{ !$currentCategory ? 'btn-maroon-active' : 'btn-maroon-outline' }}">
                    All Items
                </a>

                <!-- Dynamic Categories -->
                @foreach($categories as $category)
                <a href="{{ url('/browse/' . $branch->id . '/category/' . $category->id) }}"
                    class="btn {{ $currentCategory == $category->id ? 'btn-maroon-active' : 'btn-maroon-outline' }}">
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
                <img src="{{ asset('images/' . $product->image) }}"
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
                        <h4 class="text-maroon mb-3">₱{{ number_format($product->price, 2) }}</h4>

                        @if($product->is_available)
                        {{-- FIXED: Use individual data-* attributes instead of JSON --}}
                        <button
                            class="btn btn-maroon w-100 add-to-cart-btn"
                            data-product-id="{{ $product->id }}"
                            data-product-name="{{ $product->name }}"
                            data-product-price="{{ $product->price }}"
                            data-product-image="{{ $product->image }}"
                            data-branch-id="{{ $product->branch_id }}"
                            data-branch-name="{{ $branch->name }}"
                            data-is-available="true"
                            data-quantity="1">
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
    // SIMPLIFIED: Let cart.js handle everything automatically
    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ Browse page loaded - cart.js will auto-attach event listeners');

        // cart.js setupAddToCartButtons() will automatically find and attach to .add-to-cart-btn
        // No manual event listeners needed!

        // Just update cart count on load
        if (typeof updateCartCount !== 'undefined') {
            updateCartCount();
        }
    });

    // Keep the toast function for success notifications
    function showToast() {
        const toastEl = document.getElementById('cartToast');
        if (toastEl) {
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    }
</script>
@endpush