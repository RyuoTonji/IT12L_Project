@extends('layouts.app')

@section('title', 'Login')

@section('body-class', 'bg-gray-100 flex items-center justify-center min-h-screen')

@section('content')
    <div class="flex w-full max-w-4xl shadow-2xl rounded-lg overflow-hidden h-[600px]">
        <div class="logo-left-side w-1/2 p-10">
            <img src="{{ asset('images/logo1.png') }}" alt="BBQ-Lagao Logo" class="max-w-full h-auto">
        </div>
        <div class="login-right-side w-1/2 flex items-center justify-center">
            <div class="login-content w-full max-w-xs">
                <h2 class="text-4xl font-extrabold text-center mb-10 tracking-widest uppercase">Login</h2>
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-lg font-medium">Email:</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email..."
                            class="mt-1 block w-full px-4 py-3 bg-white/20 border-white/50 border rounded-lg placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white text-white">
                    </div>

                    <div>
                        <label for="password" class="block text-lg font-medium">Password:</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password..."
                            class="mt-1 block w-full px-4 py-3 bg-white/20 border-white/50 border rounded-lg placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white text-white">
                    </div>

                    <div class="login-number-link">
                        <a href="{{ route('login.phone') }}" class="text-sm text-white hover:text-gray-300 underline">Login using number.</a>
                    </div>

                    <button type="submit"
                        class="w-full bg-white text-red-700 py-3 rounded-full font-bold text-xl hover:bg-gray-200 transition duration-150">
                        Login
                    </button>

                    <a href="{{ route('register') }}"
                        class="block text-center w-full bg-transparent border-2 border-white text-white py-3 rounded-full font-bold text-xl hover:bg-white/10 transition duration-150 mt-4">
                        Register
                    </a>

                </form>
            </div>
        </div>
    </div>

    <div id="warningModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50">
        <div class="error-box">
            <h3 class="text-4xl font-extrabold error-text mb-4">Error</h3>
            <p class="text-2xl font-semibold error-text mb-2">Email and password are required.</p>
            <button id="closeModal" class="ok-button">OK</button>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('warningModal');
    const closeModal = document.getElementById('closeModal');

    document.querySelector('form').addEventListener('submit', function(e) {
        // NOTE: Removed the 'required' attributes from the input fields in the HTML
        // so that this JavaScript validation logic controls the submission entirely.
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();

        if (!email || !password) {
            e.preventDefault();
            modal.classList.remove('hidden');
        }
    });

    closeModal.addEventListener('click', function() {
        modal.classList.add('hidden');
    });

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
</script>
@endpush