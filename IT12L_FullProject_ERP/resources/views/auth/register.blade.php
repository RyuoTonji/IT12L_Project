@extends('layouts.app')

@section('content')

    {{-- Container to center the entire registration card on the screen --}}
    <div class="d-flex justify-content-center align-items-center py-5"
        style="min-height: 100vh; background-color: #f8f9fa;">
        <div class="container">
            <div class="row justify-content-center">
                {{-- Setting a slightly wider container for the registration form fields --}}
                <div class="col-lg-10 col-xl-9">

                    {{-- The Split Registration Card (Matching the Login Theme) --}}
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
                                    <h3 class="fw-bold mt-3">BBQ Lagao & Pares</h3>
                                    <p class="text-muted">Create your account to start ordering delicious food!</p>
                                </div>
                            </div>

                            {{-- Right Side: Registration Form (Enhanced Dark Thematic Background) --}}
                            <div class="col-md-7 p-4 p-md-5 text-white position-relative" style="background: linear-gradient(135deg, #8B1A1A 0%, #A52A2A 50%, #C84B31 100%); 
                                        background-size: cover; 
                                        background-position: center; 
                                        border-radius: 0 0.75rem 0.75rem 0;
                                        overflow: hidden;">

                                {{-- Decorative Background Elements --}}
                                <div class="position-absolute top-0 start-0 w-100 h-100"
                                    style="opacity: 0.1; pointer-events: none;">
                                    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; 
                                                background: white; border-radius: 50%; filter: blur(60px);"></div>
                                    <div style="position: absolute; bottom: -80px; left: -80px; width: 250px; height: 250px; 
                                                background: white; border-radius: 50%; filter: blur(80px);"></div>
                                </div>

                                {{-- Content Container --}}
                                <div class="position-relative" style="z-index: 1;">
                                    <h2 class="fw-bold mb-4 text-center text-uppercase pb-3" style="border-bottom: 2px solid rgba(255,255,255,0.3); 
                                               letter-spacing: 1px;
                                               text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                                        Register Account
                                    </h2>

                                    {{-- Laravel Error Messages --}}
                                    @if ($errors->any())
                                        <div class="alert alert-light text-dark py-2 small mb-4" role="alert">
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
                                            <label for="name" class="form-label text-white-50 small fw-semibold mb-2">
                                                <i class="fas fa-user me-1"></i> Full Name
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="text"
                                                    class="form-control form-control-enhanced @error('name') is-invalid @enderror"
                                                    id="name" name="name" value="{{ old('name') }}" required autofocus
                                                    placeholder="Enter your full name">
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Email Input --}}
                                        <div class="mb-3">
                                            <label for="email" class="form-label text-white-50 small fw-semibold mb-2">
                                                <i class="fas fa-envelope me-1"></i> Email Address
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="email"
                                                    class="form-control form-control-enhanced @error('email') is-invalid @enderror"
                                                    id="email" name="email" value="{{ old('email') }}" required
                                                    placeholder="Enter your email">
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Phone Input --}}
                                        <div class="mb-3">
                                            <label for="phone" class="form-label text-white-50 small fw-semibold mb-2">
                                                <i class="fas fa-phone me-1"></i> Phone Number <span
                                                    class="text-white-50">(Optional)</span>
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="tel"
                                                    class="form-control form-control-enhanced @error('phone') is-invalid @enderror"
                                                    id="phone" name="phone" value="{{ old('phone') }}"
                                                    placeholder="Enter your phone number">
                                                @error('phone')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Address Input --}}
                                        <div class="mb-3">
                                            <label for="address" class="form-label text-white-50 small fw-semibold mb-2">
                                                <i class="fas fa-map-marker-alt me-1"></i> Delivery Address
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="text"
                                                    class="form-control form-control-enhanced @error('address') is-invalid @enderror"
                                                    id="address" name="address" value="{{ old('address') }}" required
                                                    placeholder="Enter your complete delivery address">
                                                @error('address')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Password Input --}}
                                        <div class="mb-3">
                                            <label for="password" class="form-label text-white-50 small fw-semibold mb-2">
                                                <i class="fas fa-lock me-1"></i> Password
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="password"
                                                    class="form-control form-control-enhanced @error('password') is-invalid @enderror"
                                                    id="password" name="password" required
                                                    placeholder="Enter your password">
                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <small class="form-text text-white-50 d-block mt-1 ms-1">Minimum 6
                                                characters</small>
                                        </div>

                                        {{-- Confirm Password Input --}}
                                        <div class="mb-4">
                                            <label for="confirm_password"
                                                class="form-label text-white-50 small fw-semibold mb-2">
                                                <i class="fas fa-lock me-1"></i> Confirm Password
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <input type="password" class="form-control form-control-enhanced"
                                                    id="confirm_password" name="password_confirmation" required
                                                    placeholder="Confirm your password">
                                            </div>
                                        </div>

                                        {{-- Register Button --}}
                                        <div class="d-grid gap-2 mt-4">
                                            <button type="submit"
                                                class="btn btn-light btn-lg fw-bold py-3 register-submit-btn" style="color: #A52A2A; 
                                                           border-radius: 0.5rem;
                                                           box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                                                           transition: all 0.3s ease;">
                                                <i class="fas fa-user-plus me-2"></i> Register Account
                                            </button>
                                        </div>
                                    </form>

                                    {{-- Login Link --}}
                                    <div class="text-center pt-4 mt-3" style="border-top: 1px solid rgba(255,255,255,0.2);">
                                        <p class="mb-3 text-white-50">Already have an account?</p>
                                        <a href="{{ route('login') }}"
                                            class="btn btn-outline-light w-100 fw-bold py-2 login-link-btn" style="border-radius: 0.5rem; 
                                                  border-width: 2px;
                                                  transition: all 0.3s ease;">
                                            <i class="fas fa-sign-in-alt me-2"></i> Login Here
                                        </a>
                                    </div>
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
        document.getElementById('register-form')?.addEventListener('submit', function (e) {
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

                alert('Passwords do not match!');
            }
        });
    </script>
@endsection

@push('styles')
    <style>
        /* Enhanced Form Control Styles (Matching Login) */
        .form-control-enhanced {
            background-color: rgba(255, 255, 255, 0.9) !important;
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: #333 !important;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-control-enhanced::placeholder {
            color: rgba(0, 0, 0, 0.4);
        }

        .form-control-enhanced:focus {
            background-color: rgba(255, 255, 255, 1) !important;
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.2);
            border-color: white;
            color: #333 !important;
            transform: translateY(-2px);
        }

        /* Register Submit Button Hover Effect */
        .register-submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            background-color: white !important;
        }

        .register-submit-btn:active {
            transform: translateY(-1px);
        }

        /* Login Link Button Hover Effect */
        .login-link-btn {
            border-color: rgba(255, 255, 255, 0.6);
            background-color: transparent;
        }

        .login-link-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Label Animation */
        .form-label {
            transition: all 0.3s ease;
        }

        .form-control-enhanced:focus+.form-label,
        .form-control-enhanced:not(:placeholder-shown)+.form-label {
            color: white !important;
        }

        /* Remove input group text styling if not needed */
        .input-group-text {
            display: none;
        }

        /* Alert styling for better visibility on gradient background */
        .alert-light {
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush