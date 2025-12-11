@extends('layouts.app')

@section('content')

{{-- Main Wrapper for Centering and Styling --}}
<div class="d-flex justify-content-center align-items-center " style="min-height: 100vh; background-color: #f8f9fa;">
    <div class="container my-5">

        {{-- Main Content Card Container --}}
        <div class="card shadow-lg border-0 mx-auto" style="max-width: 1000px;">
            <div class="card-body p-0">

                {{-- Hero Section - Split Design --}}
                <div class="row g-0 mb-6">
                    {{-- Left Side: Logo/Branding --}}
                    <div class="col-md-6 p-4 d-flex align-items-center justify-content-center bg-white text-center">
                        <div>
                            <div style="max-width: 250px; margin: 0 auto;">
                                <img src="{{ asset('images/logo3.png') }}" alt="BBQ-Lagao Logo" class="img-fluid" style="max-width: 100%; height: auto;">
                            </div>
                            <h1 class="display-6 fw-bold mt-4">BBQ Lagao & Beef Pares</h1>
                            <p class="lead">Smoky BBQ, Savory Pares. Order Your Filipino Comfort Food.</p>
                            <a href="#branches" class="btn btn-outline-dark btn-lg mt-3">
                                <i class="fas fa-utensils"></i> Order Now
                            </a>
                        </div>
                    </div>

                    {{-- Right Side: Call to Action --}}
                    <div class="col-md-6 p-4 text-white" style="background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%); border-radius: 0 0.5rem 0.5rem 0;">
                        <div class="text-center">
                            <h2 class="fw-bold mb-3">Taste the 🔥 Difference</h2>
                            <img src="{{ asset('images/pares.png') }}" alt="Delicious Pares" class="img-fluid d-block mx-auto" style="max-width: 100%; height: auto; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                            <p class="mt-4 fs-5">
                                <strong>Delicious BBQ and Pares, Ready When You Are!</strong><br>
                                Find a branch and start your order.
                            </p>
                        </div>
                    </div>
                </div>

                <hr class="my-0">

                {{-- Branches Section --}}
                <div class="p-4 bg-white" id="branches">
                    <h2 class="text-center mb-5 fw-bold" style="color: #A52A2A;">Select Your Branch</h2>
                    
                    <div class="row">
                        @forelse($branches as $branch)
                        <div class="col-lg-6 mb-4">
                            <div class="branch-card">
                                <div class="branch-card-icon">
                                    <i class="fas fa-store"></i>
                                </div>
                                <h3 class="branch-card-title">
                                    {{ $branch->name }}
                                </h3>
                                <div class="branch-info">
                                    <div class="branch-info-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $branch->address }}</span>
                                    </div>
                                    <div class="branch-info-item">
                                        <i class="fas fa-phone"></i>
                                        <span>{{ $branch->phone }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('browse', $branch->id) }}" class="btn btn-primary w-100">
                                    <i class="fas fa-utensils"></i> Browse Menu
                                </a>
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

                {{-- Features Section --}}
                <div class="p-4 bg-light" style="border-radius: 0 0 0.5rem 0.5rem;">
                    <h2 class="text-center mb-5 fw-bold" style="color: #A52A2A;">Why Choose Us?</h2>
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <i class="fas fa-shipping-fast fa-4x mb-3" style="color: #A52A2A;"></i>
                            <h4 class="fw-bold">Fast Delivery</h4>
                            <p class="text-muted">Quick delivery to your location</p>
                        </div>
                        <div class="col-md-4 text-center mb-4">
                            <i class="fas fa-utensils fa-4x mb-3" style="color: #A52A2A;"></i>
                            <h4 class="fw-bold">Fresh Food</h4>
                            <p class="text-muted">Made fresh with quality ingredients</p>
                        </div>
                        <div class="col-md-4 text-center mb-4">
                            <i class="fas fa-money-bill-wave fa-4x mb-3" style="color: #A52A2A;"></i>
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
/* ========================================================================
   BRANCH CARD STYLES - HIGHLY VISIBLE BORDERS WITH HOVER GLOW
   ======================================================================== */

.branch-card {
    background: white;
    border: 4px solid #A52A2A;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 12px rgba(165, 42, 42, 0.15);
    transition: all 0.3s ease;
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.branch-card:hover {
    transform: translateY(-8px) !important;
    box-shadow: 0 12px 28px rgba(165, 42, 42, 0.6),
                /* 0 0 0 6px rgba(165, 42, 42, 0.25),
                0 0 40px rgba(165, 42, 42, 0.7), */
                0 0 60px rgba(165, 42, 42, 0.4) !important;
    border-color: #8B0000 !important;
}

/* Branch Card Icon */
.branch-card-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.branch-card-icon i {
    font-size: 1.75rem;
    color: #A52A2A;
}

/* Branch Title */
.branch-card-title {
    color: #A52A2A;
    font-weight: 700;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    margin-top: 0;
    padding-right: 80px;
}

/* Branch Info Container */
.branch-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-grow: 1;
}

.branch-info-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    color: #495057;
    font-size: 1rem;
    line-height: 1.5;
}

.branch-info-item i {
    color: #A52A2A;
    font-size: 1.25rem;
    width: 24px;
    flex-shrink: 0;
    margin-top: 2px;
}

/* Browse Menu Button */
.branch-card .btn-primary {
    padding: 0.875rem 1.5rem;
    font-size: 1.125rem;
    font-weight: 600;
    border-radius: 10px;
    margin-top: auto;
}

/* Responsive Design */
@media (max-width: 768px) {
    .branch-card {
        padding: 1.5rem;
    }
    
    .branch-card-title {
        font-size: 1.25rem;
        padding-right: 70px;
    }
    
    .branch-card-icon {
        width: 50px;
        height: 50px;
        top: 15px;
        right: 15px;
    }
    
    .branch-card-icon i {
        font-size: 1.5rem;
    }
}
</style>
@endpush