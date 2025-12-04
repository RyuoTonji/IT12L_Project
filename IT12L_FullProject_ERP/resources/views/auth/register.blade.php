@extends('layouts.app') 

@section('content')

{{-- Container to center the entire registration card on the screen --}}
<div class="d-flex justify-content-center align-items-center py-5" style="min-height: 100vh; background-color: #f8f9fa;">
    <div class="container">
        <div class="row justify-content-center">
            {{-- Setting a slightly wider container for the registration form fields --}}
            <div class="col-lg-10 col-xl-9"> 

                {{-- The Split Registration Card (Matching the Image Theme) --}}
                <div class="card shadow-xl border-0" style="border-radius: 0.75rem; overflow: hidden;">
                    <div class="row g-0">

                        {{-- Left Side: Branding and Logo (White Background) --}}
                        <div class="col-md-5 p-5 d-flex align-items-center justify-content-center bg-white text-center">
                            <div>
                                {{-- Placeholder for Logo/Icon --}}
                                <div style="max-width: 250px; margin: 0 auto;">
                                    <img src="{{ asset('images/logo1.png') }}" alt="BBQ-Lagao Logo" 
         style="max-width: 160px; height: auto !important;">
                                </div>
                                <h3 class="fw-bold mt-3">Join BBQ Lagao & Pares</h3>
                                <p class="text-muted">Create your account to start ordering delicious food!</p>
                            </div>
                        </div>

                        {{-- Right Side: Registration Form (Dark Thematic Background) --}}
                        <div class="col-md-7 p-4 p-md-5 text-white" style="background-color: #A52A2A; background-size: cover; background-position: center; border-radius: 0 0.75rem 0.75rem 0;">
                            
                            <h2 class="fw-bold mb-4 text-center text-uppercase border-bottom pb-3">Register Account</h2>

                            {{-- Laravel Error Messages --}}
                            @if ($errors->any())
                                <div class="alert alert-light text-dark py-2 small" role="alert">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('register') }}" id="register-form">
                                @csrf

                                {{-- Name Input --}}
                                <div class="mb-3">
                                    <label for="name" class="form-label visually-hidden">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-0 text-white-50"><i class="fas fa-user"></i></span>
                                        <input type="text" 
                                            class="form-control @error('name') is-invalid @enderror" 
                                            id="name" 
                                            name="name" 
                                            value="{{ old('name') }}" 
                                            required 
                                            autofocus
                                            placeholder="Full Name *">
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback d-block ms-4">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email Input --}}
                                <div class="mb-3">
                                    <label for="email" class="form-label visually-hidden">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-0 text-white-50"><i class="fas fa-envelope"></i></span>
                                        <input type="email" 
                                            class="form-control @error('email') is-invalid @enderror" 
                                            id="email" 
                                            name="email" 
                                            value="{{ old('email') }}" 
                                            required
                                            placeholder="Email Address *">
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block ms-4">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Phone Input --}}
                                <div class="mb-3">
                                    <label for="phone" class="form-label visually-hidden">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-0 text-white-50"><i class="fas fa-phone"></i></span>
                                        <input type="tel" 
                                            class="form-control @error('phone') is-invalid @enderror" 
                                            id="phone" 
                                            name="phone" 
                                            value="{{ old('phone') }}" 
                                            placeholder="Phone Number (Optional)">
                                    </div>
                                    @error('phone')
                                        <div class="invalid-feedback d-block ms-4">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Password Input --}}
                                <div class="mb-3">
                                    <label for="password" class="form-label visually-hidden">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-0 text-white-50"><i class="fas fa-lock"></i></span>
                                        <input type="password" 
                                            class="form-control @error('password') is-invalid @enderror" 
                                            id="password" 
                                            name="password" 
                                            required
                                            placeholder="Password *">
                                    </div>
                                    <small class="form-text text-white-50 ms-4">Minimum 6 characters</small>
                                    @error('password')
                                        <div class="invalid-feedback d-block ms-4">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Confirm Password Input --}}
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label visually-hidden">Confirm Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-0 text-white-50"><i class="fas fa-lock"></i></span>
                                        <input type="password" 
                                            class="form-control" 
                                            id="confirm_password" 
                                            name="password_confirmation" 
                                            required
                                            placeholder="Confirm Password *">
                                    </div>
                                </div>

                                {{-- Register Button --}}
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-light btn-lg fw-bold mb-3" style="color: #A52A2A;">
                                        <i class="fas fa-user-plus me-2"></i> Register Account
                                    </button>
                                </div>
                            </form>
                            
                            {{-- Login Link --}}
                            <div class="text-center pt-3 border-top border-white border-opacity-25">
                                <p class="mb-2">Already have an account?</p>
                                <a href="{{ route('login') }}" class="btn btn-outline-light w-100 fw-bold">
                                    Login Here
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Client-side password confirmation check
    document.getElementById('register-form')?.addEventListener('submit', function(e) {
        const passwordField = document.getElementById('password');
        const password = passwordField.value;
        const confirmPasswordField = document.getElementById('confirm_password');
        const confirmPassword = confirmPasswordField.value;
        
        // Remove existing error classes first
        confirmPasswordField.classList.remove('is-invalid');
        
        if (password !== confirmPassword) {
            e.preventDefault();
            // Add error visual to the confirm field
            confirmPasswordField.classList.add('is-invalid');
            
            // You might want to update the alert to show the error near the field
            // For now, sticking to the alert as per your original code:
            alert('Passwords do not match!');
        }
    });
</script>
@endsection

@push('styles')
<style>
/* Define Primary Color to match the dark side of the design */
:root {
    --bs-primary: #A52A2A; 
}

/* Custom styles for inputs on the dark background */
.col-md-7 input.form-control {
    background-color: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    border-radius: 0.3rem;
    padding-left: 0.75rem; /* Adjust padding since icon is in input-group-text */
}
.col-md-7 input.form-control::placeholder {
    color: rgba(255, 255, 255, 0.7);
}
.col-md-7 input.form-control:focus {
    background-color: rgba(255, 255, 255, 0.25);
    box-shadow: none;
    border-color: white;
}

/* Customizing input group text for the dark side */
.input-group-text {
    background-color: transparent !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    border-right: none !important;
    color: rgba(255, 255, 255, 0.7) !important;
    border-radius: 0.3rem 0 0 0.3rem;
}

/* Styling the secondary login button */
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