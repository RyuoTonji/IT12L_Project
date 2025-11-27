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
                    <input type="text" id="given_name" name="given_name" placeholder="Ex: Juan."
                        class="custom-input w-full required-field">
                </div>

                <div>
                    <label for="surname" class="block text-white font-semibold mb-1">*Surname:</label>
                    <input type="text" id="surname" name="surname" placeholder="Ex: Dela Cruz"
                        class="custom-input w-full required-field">
                </div>

                <div>
                    <label for="middle_initial" class="block text-white font-semibold mb-1">Middle Initial:</label>
                    <input type="text" id="middle_initial" name="middle_initial" placeholder="Ex: A."
                        class="custom-input w-full">
                </div>

                <div>
                    <label for="suffix" class="block text-white font-semibold mb-1">Suffix:</label>
                    <input type="text" id="suffix" name="suffix" placeholder="Ex. III, Jr., etc."
                        class="custom-input w-full">
                </div>

                <div class="sm:col-span-2">
                    <label for="address" class="block text-white font-semibold mb-1">*Address:</label>
                    <input type="text" id="address" name="address" placeholder="(House/Unit No., Street, Barangay, City/Municipality, Province, ZIP Code)"
                        class="custom-input w-full required-field">
                </div>

                <div>
                    <label for="email" class="block text-white font-semibold mb-1">*Email:</label>
                    <input type="email" id="email" name="email" placeholder="Ex: name@gmail.com"
                        class="custom-input w-full required-field">
                </div>

                <div>
                    <label for="contact_number" class="block text-white font-semibold mb-1">*Contact Number:</label>
                    <input type="tel" id="contact_number" name="contact_number" placeholder="Ex: 09xx xxx xxxx"
                        class="custom-input w-full required-field">
                </div>

                <div>
                    <label for="password" class="block text-white font-semibold mb-1">*Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password..."
                        class="custom-input w-full required-field">
                </div>

                <div>
                    <label for="confirm_password" class="block text-white font-semibold mb-1">*Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-type the password..."
                        class="custom-input w-full required-field">
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

    <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50">
        <div class="error-box">
            <h3 class="text-4xl font-extrabold error-text mb-4">Error</h3>
            <p id="errorModalMessage" class="text-2xl font-semibold error-text mb-2">All required fields must be filled out.</p>
            <button id="closeErrorModal" class="ok-button">OK</button>
        </div>
    </div>

    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50">
        <div class="success-box">
            <h3 class="text-4xl font-extrabold error-text mb-4">Success</h3>
            <p class="text-2xl font-semibold error-text mb-2">Registration Success! You may now login.</p>
            <button id="closeSuccessModal" class="ok-button">OK</button>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('registrationForm');

    // Error Modal elements
    const errorModal = document.getElementById('errorModal');
    const closeErrorModal = document.getElementById('closeErrorModal');
    const errorModalMessage = document.getElementById('errorModalMessage');

    // Success Modal elements
    const successModal = document.getElementById('successModal');
    const closeSuccessModal = document.getElementById('closeSuccessModal');


    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const requiredFields = document.querySelectorAll('.required-field');
        let allFieldsFilled = true;
        let passwordMatch = true;
        let errorMessage = '';

        // 1. Check if all required fields are filled
        requiredFields.forEach(field => {
            if (field.value.trim() === '') {
                allFieldsFilled = false;
            }
        });
        if (!allFieldsFilled) {
            errorMessage = 'All required fields must be filled out.';
        }

        // 2. Check if passwords match (only if all fields are filled to avoid confusing messages)
        const password = document.getElementById('password').value.trim();
        const confirmPassword = document.getElementById('confirm_password').value.trim();

        if (allFieldsFilled && password !== confirmPassword) {
            passwordMatch = false;
            errorMessage = 'Password and Confirm Password must match.';
        }

        if (errorMessage) {
            // Handle Error
            errorModalMessage.textContent = errorMessage;
            errorModal.classList.remove('hidden');
        } else {
            // Handle Success
            successModal.classList.remove('hidden');
        }
    });

    // Event listeners to close the modals
    closeErrorModal.addEventListener('click', function() {
        errorModal.classList.add('hidden');
    });
    errorModal.addEventListener('click', function(e) {
        if (e.target === errorModal) {
            errorModal.classList.add('hidden');
        }
    });

    closeSuccessModal.addEventListener('click', function() {
        successModal.classList.add('hidden');
        // Redirect the user to the login page after success and clicking OK
        window.location.href = '{{ route("login") }}';
    });
    successModal.addEventListener('click', function(e) {
        if (e.target === successModal) {
            successModal.classList.add('hidden');
            // Redirect the user to the login page after success and clicking outside
            window.location.href = '{{ route("login") }}';
        }
    });
</script>
@endpush