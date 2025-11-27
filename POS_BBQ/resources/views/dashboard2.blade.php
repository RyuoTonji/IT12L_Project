@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- Header / Navigation Bar -->
<header class="header">
    <div class="logo-section">
        <img src="{{ asset('images/logo1.png') }}" alt="BBQ-Lagao Logo" class="logo">
        <h1 class="brand-name">BBQ-Lagao</h1>
    </div>
    <div class="user-section">
        <span class="user-greeting">Hello, {{ Auth::user()->name ?? 'Guest' }}!</span>
        <button class="icon-btn">
            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm1-13h-2v6h2V7zm0 8h-2v2h2v-2z"></path>
            </svg>
        </button>
        <button class="icon-btn">
            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </button>
        <button onclick="showLogoutModal()" class="logout-btn">
            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            Logout
        </button>
    </div>
</header>

<!-- Main Content Area -->
<main class="container">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-card">
            <div class="hero-content">
                <h2 class="hero-title">Feast Day Specials!</h2>
                <p class="hero-description">
                    Get the Grand BBQ Platter for ₱999! Includes 2 Skewers of everything and 4 rice bowls.
                </p>
                <button class="order-btn">Order Now</button>
            </div>
            <div class="hero-icon">
                <svg class="flame-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                </svg>
            </div>
        </div>
    </section>

    <!-- Menu Categories Section -->
    <section class="menu-section">
        <h3 class="section-title">Explore Menu</h3>
        <div class="menu-grid">
            <div class="menu-card">
                <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                </svg>
                <span class="menu-text">Chicken BBQ</span>
            </div>
            <div class="menu-card">
                <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span class="menu-text">Beef Skewers</span>
            </div>
            <div class="menu-card">
                <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span class="menu-text">Rice Meals</span>
            </div>
            <div class="menu-card">
                <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                </svg>
                <span class="menu-text">Soups & Sides</span>
            </div>
        </div>
    </section>

    <!-- Featured Items Grid -->
    <section>
        <h3 class="section-title">Popular Picks</h3>
        <div class="items-grid">
            <!-- Item Card 1 -->
            <div class="item-card">
                <img src="https://placehold.co/200x160/d95353/ffffff?text=Pares+BBQ" alt="Pares BBQ" class="item-image">
                <h4 class="item-title">Grand Pares BBQ</h4>
                <p class="item-description">Tender beef pares slow-grilled to perfection.</p>
                <div class="item-footer">
                    <span class="item-price">₱180</span>
                    <button class="add-btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add
                    </button>
                </div>
            </div>

            <!-- Item Card 2 -->
            <div class="item-card">
                <img src="https://placehold.co/200x160/a04040/ffffff?text=Chicken+Skewer" alt="Chicken Skewer" class="item-image">
                <h4 class="item-title">Chicken Skewer Supreme</h4>
                <p class="item-description">Marinated chicken breast with sweet BBQ glaze.</p>
                <div class="item-footer">
                    <span class="item-price">₱120</span>
                    <button class="add-btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add
                    </button>
                </div>
            </div>

            <!-- Item Card 3 -->
            <div class="item-card">
                <img src="https://placehold.co/200x160/d95353/ffffff?text=Pork+Belly" alt="Pork Belly Adobo" class="item-image">
                <h4 class="item-title">Pork Belly Adobo</h4>
                <p class="item-description">Crispy pork belly seasoned with classic adobo.</p>
                <div class="item-footer">
                    <span class="item-price">₱195</span>
                    <button class="add-btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add
                    </button>
                </div>
            </div>

            <!-- Item Card 4 -->
            <div class="item-card">
                <img src="https://placehold.co/200x160/a04040/ffffff?text=Garlic+Rice" alt="Garlic Rice Tower" class="item-image">
                <h4 class="item-title">Garlic Rice Tower</h4>
                <p class="item-description">Good for 3-4 pax, perfectly toasted garlic rice.</p>
                <div class="item-footer">
                    <span class="item-price">₱90</span>
                    <button class="add-btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add
                    </button>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Logout Modal -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <svg class="modal-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
        </svg>
        <h3 class="modal-title">Confirm Logout</h3>
        <p class="modal-text">Are you sure you want to log out of your account?</p>
        <div class="modal-buttons">
            <button onclick="hideLogoutModal()" class="btn-secondary">Cancel</button>
            <button onclick="confirmLogout()" class="btn-primary">Yes, Log Out</button>
        </div>
    </div>
</div>

<script>
    const logoutModal = document.getElementById('logoutModal');

    function showLogoutModal() {
        logoutModal.style.display = 'flex';
    }

    function hideLogoutModal() {
        logoutModal.style.display = 'none';
    }

    function confirmLogout() {
        // Create a form to POST to logout route
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("logout") }}';
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }

    // Close modal when clicking outside
    logoutModal.addEventListener('click', function(e) {
        if (e.target === logoutModal) {
            hideLogoutModal();
        }
    });
</script>
@endsection
