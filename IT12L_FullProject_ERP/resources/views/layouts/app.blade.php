<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    
    <!-- User Meta Tags for Cart System - REQUIRED -->
    @auth
        <meta name="user-id" content="{{ auth()->user()->id }}">
        <meta name="user-is-admin" content="{{ auth()->user()->is_admin ? 'true' : 'false' }}">
    @else
        <meta name="user-id" content="">
        <meta name="user-is-admin" content="false">
    @endauth
    
    <title>@yield('title', 'Food Ordering System')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
    
    <style>
        /* Cart Count Badge Styles */
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545 !important;
            color: white !important;
            border-radius: 50%;
            padding: 3px 7px;
            font-size: 11px;
            font-weight: bold;
            min-width: 20px;
            text-align: center;
            line-height: 1;
            display: inline-block;
        }
        
        /* Position relative for cart link */
        .cart-link {
            position: relative;
            display: inline-block;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark"
     style="{{ auth()->check() && auth()->user()->is_admin ? 'background-color: #000;' : 'background-color: #A52A2A;' }}">
    <div class="container{{ auth()->check() && auth()->user()->is_admin ? '-fluid' : '' }}">
        <a class="navbar-brand" href="{{ auth()->check() && auth()->user()->is_admin ? route('admin.dashboard') : route('home') }}">
            <img src="{{ asset('images/logo3.png') }}" alt="BBQ-Lagao Logo" 
         style="max-width: 50px; height: auto !important;"> 
            {{ auth()->check() && auth()->user()->is_admin ? 'Admin Panel' : 'BBQ Lagao & Beef Pares' }}
        </a>

            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                @auth
                    @if(auth()->user()->is_admin)
                        <!-- Admin Menu -->
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.products.index') }}">
                                    <i class="fas fa-box"></i> Products
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.orders.index') }}">
                                    <i class="fas fa-receipt"></i> Orders
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.branches.index') }}">
                                    <i class="fas fa-store"></i> Branches
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.categories.index') }}">
                                    <i class="fas fa-tags"></i> Categories
                                </a>
                            </li>
                        </ul>
                    @else
                        <!-- Regular User Menu -->
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">
                                    <i class="fas fa-home"></i> Home
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('orders.index') }}">
                                    <i class="fas fa-box"></i> My Orders
                                </a>
                            </li>
                        </ul>
                    @endif
                    
                    <ul class="navbar-nav">
                        @if(!auth()->user()->is_admin)
                            <!-- Cart Icon (only for regular users) -->
                            <li class="nav-item">
                                <a class="nav-link cart-link" href="{{ route('cart.index') }}">
                                    <i class="fas fa-shopping-cart fa-lg"></i>
                                    <span class="cart-count" id="cart-count">0</span>
                                </a>
                            </li>
                        @endif
                        
                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(auth()->user()->is_admin)
                                    <li><a class="dropdown-item" href="{{ route('home') }}">
                                        <i class="fas fa-globe"></i> View Site
                                    </a></li>
                                @else
                                    <li><a class="dropdown-item" href="{{ route('orders.index') }}">
                                        <i class="fas fa-box"></i> My Orders
                                    </a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @else
                    <!-- Guest Menu -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">
                                <i class="fas fa-home"></i> Home
                            </a>
                        </li>
                        <!-- Cart Icon for Guests -->
                        <li class="nav-item">
                            <a class="nav-link cart-link" href="{{ route('cart.index') }}">
                                <i class="fas fa-shopping-cart fa-lg"></i>
                                <span class="cart-count" id="cart-count">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus"></i> Register
                            </a>
                        </li>
                    </ul>
                @endauth
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
    <main>
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
    
    <!-- CRITICAL: Load cart.js BEFORE other scripts -->
    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    
    <!-- Page-specific scripts -->
    @stack('scripts')
    <!-- QUICK FIX FOR CART COUNT -->
<!-- Add this script in your layout AFTER cart.js loads -->
<!-- Place it just before closing </body> tag -->

<script>
// Force cart count update with aggressive element finding
function forceUpdateCartCount() {
    console.log('🔧 Force updating cart count...');
    
    // Don't update for admins
    const isAdminMeta = document.querySelector('meta[name="user-is-admin"]');
    if (isAdminMeta && isAdminMeta.content === 'true') {
        console.log('Admin user, skipping cart count');
        return;
    }
    
    // Get cart count
    const cartKey = (function() {
        const userIdMeta = document.querySelector('meta[name="user-id"]');
        const userId = userIdMeta ? userIdMeta.content : null;
        return (userId && userId !== '') ? `cart_user_${userId}` : 'cart_guest';
    })();
    
    const cartData = localStorage.getItem(cartKey);
    const cart = JSON.parse(cartData || '[]');
    const count = cart.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0);
    
    console.log('Cart key:', cartKey);
    console.log('Cart items:', cart.length);
    console.log('Total count:', count);
    
    // Find ALL possible cart count elements
    const selectors = [
        '#cart-count',
        '.cart-count',
        '[id*="cart-count"]',
        '[class*="cart-count"]',
        'span.cart-count',
        'span#cart-count'
    ];
    
    let updated = 0;
    
    selectors.forEach(selector => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(el => {
            console.log('Found element:', el.tagName, el.id, el.className);
            
            // Update text
            el.textContent = count;
            el.innerText = count;
            
            // Force styles
            if (count > 0) {
                el.style.cssText = `
                    display: inline-block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    position: absolute !important;
                    top: -8px !important;
                    right: -8px !important;
                    background-color: #dc3545 !important;
                    color: white !important;
                    border-radius: 50% !important;
                    padding: 3px 7px !important;
                    font-size: 11px !important;
                    font-weight: bold !important;
                    min-width: 20px !important;
                    text-align: center !important;
                    line-height: 1 !important;
                    z-index: 999 !important;
                `;
                updated++;
            } else {
                el.style.display = 'none';
            }
        });
    });
    
    console.log(`✅ Updated ${updated} cart count elements to: ${count}`);
}

// Run immediately
forceUpdateCartCount();

// Run after short delay (in case elements load slowly)
setTimeout(forceUpdateCartCount, 500);
setTimeout(forceUpdateCartCount, 1000);
setTimeout(forceUpdateCartCount, 2000);

// Also run whenever cart.js updates (if the function exists)
if (typeof updateCartCount !== 'undefined') {
    const originalUpdate = updateCartCount;
    window.updateCartCount = function() {
        originalUpdate();
        setTimeout(forceUpdateCartCount, 100);
    };
}

// Run on page visibility change (when user switches tabs)
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        forceUpdateCartCount();
    }
});

console.log('🔧 Force cart count updater installed');
</script>
<script>
// Check if we need to clear cart (from meta tag)
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('meta[name="clear-cart"]')) {
        console.log('Clear cart meta tag detected - clearing cart...');
        
        // Clear all possible cart keys
        ['cart', 'Cart', 'shopping_cart', 'shoppingCart', 'cartItems'].forEach(key => {
            localStorage.removeItem(key);
        });
        
        // Update cart count
        document.querySelectorAll('.cart-count, [data-cart-count]').forEach(el => {
            el.textContent = '0';
            if (el.classList.contains('badge')) {
                el.style.display = 'none';
            }
        });
        
        console.log('Cart cleared from meta tag trigger');
    }
});
</script>
</body>
</html>