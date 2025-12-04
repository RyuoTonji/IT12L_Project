@extends('layouts.app')

@section('content')

{{-- Main Wrapper for Centering and Styling --}}
<div class="d-flex justify-content-center align-items-center py-5" style="min-height: 100vh; background-color: #f8f9fa;">
    <div class="container my-5">

        {{-- Main Content Card Container (Simulating the Login Box Look) --}}
        <div class="card shadow-lg border-0 mx-auto" style="max-width: 1000px;">
            <div class="card-body p-0">

                {{-- 🚀 Hero Section - Adapted to Split Design --}}
                <div class="row g-0">
                    {{-- Left Side: Logo/Branding (Light Background) --}}
                    <div class="col-md-6 p-5 d-flex align-items-center justify-content-center bg-white text-center">
                        <div>
                            {{-- Placeholder for Logo - Replace with an actual image tag if available --}}
                            <div style="max-width: 250px; margin: 0 auto;">
                                                            </div>
                            <h1 class="display-6 fw-bold mt-4">BBQ Lagao & Beef Pares</h1>
                            <p class="lead">Smoky BBQ, Savory Pares. Order Your Filipino Comfort Food.</p>
                            <a href="#branches" class="btn btn-outline-dark btn-lg mt-3">
                                <i class="fas fa-utensils"></i> Order Now
                            </a>
                        </div>
                    </div>

                    {{-- Right Side: Call to Action (Dark/Thematic Background) --}}
                    <div class="col-md-6 p-5 text-white" style="background-color: #A52A2A; background-image: url('your_background_image.jpg'); background-size: cover; background-position: center; border-radius: 0 0.5rem 0.5rem 0;">
                        <div class="text-center">
                            <h2 class="fw-bold mb-4">Taste the 🔥 Difference</h2>
                           <img src="{{ asset('images/pares.png') }}" alt="BBQ-Lagao Logo" class="img-fluid d-block mx-auto"
                                style="max-width: 100%; height: auto;">

                             <p class="mt-4 fs-5">
                                 <strong>Delicious BBQ and Pares, Ready When You Are!</strong><br>
                                Find a branch and start your order.
                            </p>
                        </div>
                    </div>
                </div>

                <hr class="my-0">

                {{-- 🗺️ Branches Section (As a separate card content area) --}}
                <div class="p-5 bg-white" id="branches">
                    <h2 class="text-center mb-5 fw-bold">Select Your Branch</h2>
                    
                    <div class="row">
                        @forelse($branches as $branch)
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100 shadow-sm border-2 border-primary hover-shadow">
                                <div class="card-body">
                                    <h3 class="card-title text-primary mb-3">
                                        <i class="fas fa-store"></i> {{ $branch->name }}
                                    </h3>
                                    <p class="card-text text-muted">
                                        <i class="fas fa-map-marker-alt text-danger me-2"></i> {{ $branch->address }}<br>
                                        <i class="fas fa-phone text-success me-2"></i> {{ $branch->phone }}
                                    </p>
                                    <a href="{{ route('browse', $branch->id) }}" class="btn btn-primary w-100 mt-3">
                                        <i class="fas fa-utensils"></i> Browse Menu
                                    </a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> No branches available at the moment.
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

                <hr class="my-0">

                {{-- ✨ Features Section (As a separate card content area) --}}
                <div class="p-5 bg-light" style="border-radius: 0 0 0.5rem 0.5rem;">
                    <h2 class="text-center mb-5 fw-bold">Why Choose Us?</h2>
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <i class="fas fa-shipping-fast fa-4x text-primary mb-3"></i>
                            <h4 class="fw-bold">Fast Delivery</h4>
                            <p class="text-muted">Quick delivery to your location</p>
                        </div>
                        <div class="col-md-4 text-center mb-4">
                            <i class="fas fa-utensils fa-4x text-primary mb-3"></i>
                            <h4 class="fw-bold">Fresh Food</h4>
                            <p class="text-muted">Made fresh with quality ingredients</p>
                        </div>
                        <div class="col-md-4 text-center mb-4">
                            <i class="fas fa-money-bill-wave fa-4x text-primary mb-3"></i>
                            <h4 class="fw-bold">Affordable Prices</h4>
                            <p class="text-muted">Great value for your money</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Custom Styles to match the login screen aesthetic */

/* Set a consistent primary color (you might need to adjust this for your theme) */
:root {
    --bs-primary: #A52A2A; /* Using a deep red/brown for thematic consistency */
}
.text-primary { color: var(--bs-primary) !important; }
.btn-primary { 
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}
.btn-primary:hover {
    background-color: #8B1A1A; /* Slightly darker hover */
    border-color: #8B1A1A;
}
.border-primary { border-color: var(--bs-primary) !important; }


/* Hover effect for cards */
.hover-shadow {
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}
.hover-shadow:hover {
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1) !important;
    transform: translateY(-5px); /* Subtle lift on hover */
}

/* Ensure the card has rounded corners like the login box */
.card {
    border-radius: 0.5rem;
}

/* Style for the split-card's dark side (Right Hero) */
.col-md-6.p-5.text-white {
    /* If you have a subtle background image like in your login screen */
    /* background-image: url('path/to/your/pattern.jpg'); */
    /* background-blend-mode: multiply; */
}
</style>
@endpush