<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Food Ordering System')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body class="bg-light">
    <!-- Simple Navigation for Guests -->
   <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #A52A2A;">
    <div class="container">
        {{-- Brand --}}
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('images/logo3.png') }}" alt="BBQ-Lagao Logo"
                 style="max-width: 50px; height: auto !important;" class="me-2">
            <span class="fw-semibold">BBQ Lagao & Beef Pares</span>
        </a>

        {{-- Toggler for mobile --}}
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#guestNavbar"
                aria-controls="guestNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Collapsible links --}}
        <div class="collapse navbar-collapse" id="guestNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                {{-- Home --}}
                <li class="nav-item">
                    <a class="nav-link text-center text-lg-start" href="{{ route('home') }}">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>

                {{-- Cart --}}
                <li class="nav-item">
                    <a class="nav-link cart-link position-relative text-center text-lg-start"
                       href="{{ route('cart.index') }}">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                        <span class="cart-count" id="cart-count">0</span>
                    </a>
                </li>

                {{-- Login --}}
                <li class="nav-item">
                    <a href="{{ route('login') }}" class="nav-link text-center text-lg-start">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </li>

                {{-- Register --}}
                <li class="nav-item">
                    <a href="{{ route('register') }}" class="nav-link text-center text-lg-start">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>


    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Content -->
    <main class="py-5">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 Food Ordering System. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
    <!-- DEBUG: Check cart on every page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== CART DEBUG ===');
    console.log('Cart storage key:', getCartStorageKey ? getCartStorageKey() : 'Function not found');
    console.log('Cart contents:', getCart ? getCart() : 'Function not found');
    console.log('Is admin:', isAdmin ? isAdmin() : 'Function not found');
    console.log('User ID meta:', document.querySelector('meta[name="user-id"]')?.content);
    console.log('Admin meta:', document.querySelector('meta[name="user-is-admin"]')?.content);
    console.log('All localStorage keys:', Object.keys(localStorage));
});
</script>

@stack('scripts')
</body>
</html>
