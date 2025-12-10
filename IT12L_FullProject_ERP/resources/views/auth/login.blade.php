@extends('layouts.app')

@section('content')

{{-- Container to center the entire login card on the screen --}}
<div class="d-flex justify-content-center align-items-center py-5" style="min-height: 100vh; background-color: #f8f9fa;">
    <div class="container">
        <div class="row justify-content-center">
            {{-- Increased width slightly to accommodate the split card --}}
            <div class="col-lg-10 col-xl-9"> 

                {{-- The Split Login Card (Matching the Image Theme) --}}
                <div class="card shadow-xl border-0" style="border-radius: 0.75rem; overflow: hidden;">
                    <div class="row g-0">

                        {{-- Left Side: Branding and Logo (White Background) --}}
                        <div class="col-md-5 p-5 d-flex align-items-center justify-content-center bg-white text-center">
                            <div>
                                {{-- Placeholder for Logo --}}
                                <div style="max-width: 250px; margin: 0 auto;">
                                    <img src="{{ asset('images/logo1.png') }}" alt="BBQ-Lagao Logo" 
         style="max-width: 160px; height: auto !important;">
                                </div>
                                <h3 class="fw-bold mt-3">BBQ Lagao & Pares</h3>
                                <p class="text-muted">Welcome back! Please login to continue.</p>
                            </div>
                        </div>

                        {{-- Right Side: Login Form (Dark Thematic Background) --}}
                        <div class="col-md-7 p-4 p-md-5 text-white" style="background-color: #A52A2A; background-size: cover; background-position: center; border-radius: 0 0.75rem 0.75rem 0;">
                            
                            <h2 class="fw-bold mb-4 text-center text-uppercase border-bottom pb-3">Account Login</h2>

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                {{-- Email Input --}}
                                <div class="mb-3">
                                    <label for="email" class="form-label visually-hidden">Email</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-transparent border-0 text-white-50"><i class="fas fa-envelope"></i></span>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email Address">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Password Input --}}
                                <div class="mb-4">
                                    <label for="password" class="form-label visually-hidden">Password</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-transparent border-0 text-white-50"><i class="fas fa-lock"></i></span>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Login Button --}}
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-light btn-lg fw-bold mb-3" style="color: #A52A2A;">
                                        <i class="fas fa-sign-in-alt me-2"></i> Login
                                    </button>
                                </div>
                            </form>
                            
                            {{-- Register Link --}}
                            <div class="text-center pt-3 border-top border-white border-opacity-25">
                                <p class="mb-2">Don't have an account?</p>
                                <a href="{{ route('register') }}" class="btn btn-outline-light w-100 fw-bold">
                                    Register Here
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Demo Credentials Block (Styled separately, below the main card) --}}
                <div class="card mt-3 shadow-sm border-0" style="border-radius: 0.5rem;">
                    <div class="card-body p-3">
                        <h6 class="card-title fw-bold mb-2">
                            <i class="fas fa-info-circle text-info me-1"></i> Demo Credentials
                        </h6>
                        <p class="card-text small mb-1 text-muted">
                            <span class="fw-bold">Admin:</span>
                            Email: admin@foodorder.com | Password: admin12345
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Define Primary Color to match the dark side of the design */
:root {
    --bs-primary: #A52A2A; 
}

/* Custom styles for inputs on the dark background */
.col-md-7 input.form-control {
    /* background-color: rgba(255, 255, 255, 0.15); */
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    border-radius: 0.3rem;
}
.col-md-7 input.form-control::placeholder {
    color: rgba(255, 255, 255, 0.7);
}
.col-md-7 input.form-control:focus {
    /* background-color: rgba(255, 255, 255, 0.25); */
    box-shadow: none; /* Removed default Bootstrap shadow for cleaner look */
    border-color: white;
}

/* Customizing input group text for the dark side */
.input-group-text {
    border-right: none !important;
}

/* Styling the secondary register button */
.btn-outline-light {
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    transition: all 0.3s ease;
}
.btn-outline-light:hover {
    background-color: rgba(255, 255, 255, 0.15);
    color: white;
    border-color: white;
}
</style>
@endpush


@if(session('cart_merged'))
<script>
    // Signal that login just happened
    sessionStorage.setItem('just_logged_in', 'true');
    
    // Trigger cart sync
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof checkLoginAndSyncCart === 'function') {
            checkLoginAndSyncCart();
        }
    });
</script>
@endif