{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@section('title', 'Sign Up')

@section('body-class', 'register-background flex items-center justify-center min-h-screen p-4')

@section('content')
<div class="w-full max-w-2xl bg-white/20 p-8 sm:p-10 rounded-xl shadow-2xl backdrop-blur-sm">

    <div class="flex flex-col items-center mb-8">
        <img src="{{ asset('images/logo1.png') }}" alt="BBQ-Lagao Logo" class="w-20 h-auto mb-2">
        <h1 class="text-4xl font-extrabold text-white text-shadow-lg" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Sign Up</h1>
    </div>

    <form id="registrationForm" action="{{ route('register') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">

            <div>
                <label for="given_name" class="block text-white font-semibold mb-1">*Given Name:</label>
                <input type="text" id="given_name" name="given_name" value="{{ old('given_name') }}" placeholder="Ex: Juan."
                    class="custom-input w-full required-field @error('given_name') border-red-500 @enderror">
                @error('given_name')
                    <span class="text-yellow-300 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="surname" class="block text-white font-semibold mb-1">*Surname:</label>
                <input type="text" id="surname" name="surname" value="{{ old('surname') }}" placeholder="Ex: Dela Cruz"
                    class="custom-input w-full required-field @error('surname') border-red-500 @enderror">
                @error('surname')
                    <span class="text-yellow-300 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="middle_initial" class="block text-white font-semibold mb-1">Middle Initial:</label>
                <input type="text" id="middle_initial" name="middle_initial" value="{{ old('middle_initial') }}" placeholder="Ex: A."
                    class="custom-input w-full">
            </div>

            <div>
                <label for="suffix" class="block text-white font-semibold mb-1">Suffix:</label>
                <input type="text" id="suffix" name="suffix" value="{{ old('suffix') }}" placeholder="Ex. III, Jr., etc."
                    class="custom-input w-full">
            </div>

            <div class="sm:col-span-2">
                <label for="address" class="block text-white font-semibold mb-1">*Address:</label>
                <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="(House/Unit No., Street, Barangay, City/Municipality, Province, ZIP Code)"
                    class="custom-input w-full required-field @error('address') border-red-500 @enderror">
                @error('address')
                    <span class="text-yellow-300 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-white font-semibold mb-1">*Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Ex: name@gmail.com"
                    class="custom-input w-full required-field @error('email') border-red-500 @enderror">
                @error('email')
                    <span class="text-yellow-300 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="contact_number" class="block text-white font-semibold mb-1">*Contact Number:</label>
                <input type="tel" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" placeholder="Ex: 09xx xxx xxxx"
                    class="custom-input w-full required-field @error('contact_number') border-red-500 @enderror">
                @error('contact_number')
                    <span class="text-yellow-300 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-white font-semibold mb-1">*Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password..."
                    class="custom-input w-full required-field @error('password') border-red-500 @enderror">
                @error('password')
                    <span class="text-yellow-300 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="confirm_password" class="block text-white font-semibold mb-1">*Confirm Password</label>
                <!-- THIS IS THE ONLY CHANGE: name="password_confirmation" -->
                <input type="password" id="confirm_password" name="password_confirmation" placeholder="Re-type the password..."
                    class="custom-input w-full required-field">
                @error('password_confirmation')
                    <span class="text-yellow-300 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <p class="text-white text-center text-lg mt-4">Fields marked with asterisk (<span class="text-yellow-300">*</span>) are required.</p>

        <div class="flex justify-center space-x-6 pt-4">
            <a href="{{ route('login') }}" class="custom-btn hover:bg-gray-300">
                Back to Login
            </a>
            <button type="submit" class="custom-btn hover:bg-gray-300">
                Confirm Registration
            </button>
        </div>
    </form>

</div>

<!-- Error Modal (kept exactly the same) -->
<div id="errorModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50">
    <div class="error-box bg-white p-8 rounded-xl text-center">
        <h3 class="text-4xl font-extrabold text-red-600 mb-4">Error</h3>
        <p id="errorModalMessage" class="text-2xl font-semibold text-gray-800 mb-6">All required fields must be filled out.</p>
        <button id="closeErrorModal" class="px-8 py-3 bg-red-600 text-white rounded-full font-bold hover:bg-red-700">OK</button>
    </div>
</div>

<!-- Success Modal (kept exactly the same) -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50">
    <div class="success-box bg-white p-8 rounded-xl text-center">
        <h3 class="text-4xl font-extrabold text-green-600 mb-4">Success</h3>
        <p class="text-2xl font-semibold text-gray-800 mb-6">Registration successful! Redirecting to login...</p>
        <button id="closeSuccessModal" class="px-8 py-3 bg-green-600 text-white rounded-full font-bold hover:bg-green-700">OK</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('registrationForm');
    const errorModal = document.getElementById('errorModal');
    const successModal = document.getElementById('successModal');
    const errorModalMessage = document.getElementById('errorModalMessage');
    const closeErrorModal = document.getElementById('closeErrorModal');
    const closeSuccessModal = document.getElementById('closeSuccessModal');

    form.addEventListener('submit', function(e) {
        const required = document.querySelectorAll('.required-field');
        let missing = false;
        let errorMsg = '';

        required.forEach(field => {
            if (!field.value.trim()) missing = true;
        });

        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirm_password').value;

        if (missing) {
            errorMsg = 'All required fields must be filled out.';
        } else if (password !== confirm) {
            errorMsg = 'Password and Confirm Password must match.';
        }

        if (errorMsg) {
            e.preventDefault();
            errorModalMessage.textContent = errorMsg;
            errorModal.classList.remove('hidden');
        }
    });

    closeErrorModal.onclick = () => errorModal.classList.add('hidden');
    closeSuccessModal.onclick = () => {
        successModal.classList.add('hidden');
        window.location.href = '{{ route("login") }}';
    };

    errorModal.onclick = e => e.target === errorModal && errorModal.classList.add('hidden');
    successModal.onclick = e => e.target === successModal && closeSuccessModal.click();
</script>
@endpush