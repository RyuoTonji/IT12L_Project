@extends('layouts.app')

@section('title', 'Login')

@section('body-class', 'bg-gray-100 flex items-center justify-center min-h-screen')

@section('content')
<div class="flex w-full max-w-4xl shadow-2xl rounded-lg overflow-hidden h-[600px]">
    <!-- Left Side - Logo -->
    <div class="logo-left-side w-1/2 p-10 bg-red-700 flex items-center justify-center">
        <img src="{{ asset('images/logo1.png') }}" alt="BBQ-Lagao Logo" class="max-w-full h-auto">
    </div>

    <!-- Right Side - Login Form -->
    <div class="login-right-side w-1/2 flex items-center justify-center">
        <div class="login-content w-full max-w-xs">
            <h2 class="text-4xl font-extrabold text-center mb-10 tracking-widest uppercase text-white">
                Login
            </h2>

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-lg font-medium text-white">Email:</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        placeholder="Enter your email..."
                        class="mt-1 block w-full px-4 py-3 bg-white/20 border-white/50 border rounded-lg placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white text-white"
                        required autofocus>

                    <!-- Wrong password → red text only -->
                    @error('wrong_password')
                        <span class="text-red-400 text-sm mt-2 block">{{ $message }}</span>
                    @enderror

                    <!-- User not found → red text + popup -->
                    @error('email_not_found')
                        <span class="text-red-400 text-sm mt-2 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-lg font-medium text-white">Password:</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        placeholder="Enter your password..."
                        class="mt-1 block w-full px-4 py-3 bg-white/20 border-white/50 border rounded-lg placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white text-white"
                        required>

                    <!-- Optional: red border on password field when wrong -->
                    @error('wrong_password')
                        <style>#password { border-color: #f87171 !important; }</style>
                    @enderror
                </div>

                <div class="text-right mt-2">
                    <a href="{{ route('login.phone') }}" class="text-sm text-white hover:text-gray-300 underline transition">
                        Login using phone number
                    </a>
                </div>

                <button type="submit"
                    class="w-full bg-white text-red-700 py-3 rounded-full font-bold text-xl hover:bg-gray-200 transition">
                    Login
                </button>

                <a href="{{ route('register') }}"
                    class="block text-center w-full bg-transparent border-2 border-white text-white py-3 rounded-full font-bold text-xl hover:bg-white/10 transition mt-4">
                    Register
                </a>
            </form>
        </div>
    </div>
</div>

<!-- Empty Fields Modal -->
<div id="warningModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden z-50">
    <div class="bg-white p-10 rounded-2xl text-center shadow-2xl max-w-sm">
        <h3 class="text-4xl font-extrabold text-red-600 mb-4">Error</h3>
        <p class="text-2xl font-semibold text-gray-800 mb-8">
            Email and password are required.
        </p>
        <button id="closeModal" class="px-10 py-3 bg-red-600 text-white rounded-full font-bold hover:bg-red-700 transition">
            OK
        </button>
    </div>
</div>

<!-- ONLY shows when email/phone NOT FOUND (popup) -->
@error('email_not_found')
<div id="loginErrorModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
    <div class="bg-white p-10 rounded-2xl text-center shadow-2xl max-w-md">
        <svg class="w-20 h-20 text-red-600 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/>
        </svg>
        <h3 class="text-4xl font-extrabold text-red-600 mb-4">Login Failed</h3>
        <p class="text-2xl font-semibold text-gray-800 mb-6">
            These credentials do not match our records.
        </p>
        <p class="text-lg text-gray-600 mb-8">
            No account yet? 
            <a href="{{ route('register') }}" class="text-red-600 font-bold underline hover:text-red-800">
                Create one now
            </a>
        </p>
        <button id="closeErrorModal" class="px-12 py-3 bg-red-600 text-white rounded-full font-bold hover:bg-red-700 transition">
            OK
        </button>
    </div>
</div>
@enderror
@endsection

@push('styles')
<style>
    .login-right-side {
        position: relative;
        background-color: #7f1d1d;
    }
    .login-right-side::before {
        content: "";
        position: absolute;
        inset: 0;
        background: url("{{ asset('images/bg2.png') }}") center/cover no-repeat;
        opacity: 0.9;
        z-index: -1;
    }
    .login-content { position: relative; z-index: 1; }
</style>
@endpush

@push('scripts')
<script>
    // Empty fields check
    document.querySelector('form').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();
        if (!email || !password) {
            e.preventDefault();
            document.getElementById('warningModal').classList.remove('hidden');
        }
    });

    // Close modals
    document.getElementById('closeModal')?.addEventListener('click', () => {
        document.getElementById('warningModal').classList.add('hidden');
    });

    const errorModal = document.getElementById('loginErrorModal');
    const closeBtn = document.getElementById('closeErrorModal');
    if (closeBtn) closeBtn.addEventListener('click', () => errorModal?.remove());
    if (errorModal) errorModal.addEventListener('click', e => e.target === errorModal && errorModal.remove());
</script>
@endpush