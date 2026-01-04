<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">

    
    <meta name="session-id" content="<?php echo e(session()->getId()); ?>">

    
    <?php if(session('_cart_migration_needed')): ?>
        <meta name="cart-migration-needed" content="true">
        <meta name="cart-old-session" content="<?php echo e(session('_cart_old_session_id')); ?>">
        <meta name="cart-new-session" content="<?php echo e(session('_cart_new_session_id')); ?>">
    <?php endif; ?>

    <?php if(auth()->guard()->check()): ?>
        <meta name="user-id" content="<?php echo e(auth()->id() ?? ''); ?>">
        <meta name="user-is-admin" content="<?php echo e(auth()->check() && auth()->user()->is_admin ? 'true' : 'false'); ?>">
    <?php else: ?>
        <meta name="user-id" content="">
        <meta name="user-is-admin" content="false">
    <?php endif; ?>

    <title><?php echo $__env->yieldContent('title', 'BBQ Lagao & Beef Pares'); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <?php echo $__env->yieldPushContent('styles'); ?>

    <style>
        /* ========================================================================
           GLOBAL STYLES
           ======================================================================== */
        :root {
            --primary-red: #A52A2A;
            --dark-red: #8B0000;
            --darker-red: #6B0000;
            --light-red: #C54A4A;
            --red-hover: rgba(165, 42, 42, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        /* ========================================================================
   UNIFIED NAVBAR STYLES - FIXED VERSION
   ======================================================================== */
        .navbar {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            border-bottom: 3px solid var(--dark-red);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 0.75rem 0;
        }

        .navbar-brand {
            color: #ffffff !important;
            font-weight: 700;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            color: #ffffff !important;
            transform: scale(1.02);
        }

        .navbar-brand img {
            border-radius: 8px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .navbar-brand:hover img {
            border-color: rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Navigation Links - FIXED */
        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.625rem 1.25rem !important;
            margin: 0 0.25rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff !important;
            transform: translateY(-2px);
        }

        /* Active state - FIXED to be more visible */
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
            font-weight: 600;
        }

        /* Prevent active state from losing background on hover */
        .nav-link.active:hover {
            background-color: rgba(255, 255, 255, 0.3) !important;
        }

        .nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        /* Cart Link - FIXED */
        .cart-link {
            position: relative;
        }

        .cart-link:hover {
            background-color: rgba(255, 255, 255, 0.15) !important;
        }

        /* Active cart link should also have background */
        .cart-link.active {
            background-color: rgba(255, 255, 255, 0.25) !important;
        }

        .cart-link.active:hover {
            background-color: rgba(255, 255, 255, 0.3) !important;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, #FF4444 0%, #CC0000 100%);
            color: white;
            border-radius: 12px;
            padding: 2px 7px;
            font-size: 0.7rem;
            font-weight: 700;
            min-width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            border: 2px solid var(--dark-red);
        }

        .cart-count:empty,
        .cart-count[data-count="0"] {
            display: none !important;
        }

        /* User Dropdown - FIXED */
        .dropdown-toggle {
            background-color: transparent !important;
            border: none !important;
            border-radius: 8px;
            color: #ffffff !important;
            font-weight: 600;
            padding: 0.625rem 1rem !important;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dropdown-toggle:hover {
            background-color: rgba(255, 255, 255, 0.15) !important;
            transform: translateY(-2px);
        }

        /* Keep hover state when dropdown is open */
        .dropdown-toggle.show,
        .nav-item.dropdown.show .dropdown-toggle {
            background-color: rgba(255, 255, 255, 0.25) !important;
            transform: translateY(0);
        }

        .dropdown-toggle::after {
            margin-left: 0.5rem;
            border-top-color: #ffffff;
        }

        .dropdown-toggle i {
            font-size: 1.25rem;
        }

        /* Dropdown Menu */
        .dropdown-menu {
            background-color: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            padding: 0.75rem;
            margin-top: 0.5rem;
            min-width: 200px;
            animation: fadeIn 0.3s ease;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            color: #333333;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
            margin-bottom: 0.25rem;
        }

        .dropdown-item:last-child {
            margin-bottom: 0;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            color: #ffffff;
            transform: translateX(5px);
        }

        .dropdown-item:active {
            background: linear-gradient(135deg, var(--dark-red) 0%, var(--darker-red) 100%);
            color: #ffffff;
        }

        .dropdown-item i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .dropdown-item button {
            background: none;
            border: none;
            padding: 0;
            color: inherit;
            font: inherit;
            cursor: pointer;
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .dropdown-divider {
            border-color: rgba(0, 0, 0, 0.1);
            margin: 0.5rem 0;
        }

        /* Navbar Toggler */
        .navbar-toggler {
            border: 2px solid rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            padding: 0.5rem;
            transition: all 0.3s ease;
        }

        .navbar-toggler:hover {
            border-color: rgba(255, 255, 255, 0.8);
            background-color: rgba(255, 255, 255, 0.1);
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* ========================================================================
           BUTTON STYLES
           ======================================================================== */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            border: none;
            color: white !important;
            font-weight: 600;
            padding: 0.625rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--dark-red) 0%, var(--darker-red) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white !important;
        }

        .btn-primary:focus,
        .btn-primary:active {
            background: linear-gradient(135deg, var(--dark-red) 0%, var(--darker-red) 100%);
            color: white !important;
            box-shadow: 0 0 0 0.25rem rgba(165, 42, 42, 0.25);
        }

        .btn-outline-primary {
            color: var(--primary-red) !important;
            border: 2px solid var(--primary-red);
            background-color: transparent;
            font-weight: 600;
            padding: 0.625rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            border-color: var(--dark-red);
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-outline-primary:focus,
        .btn-outline-primary:active {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            border-color: var(--dark-red);
            color: white !important;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #bb2d3b 100%);
            border: none;
            color: white !important;
            font-weight: 600;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #bb2d3b 0%, #a02331 100%);
            transform: translateY(-2px);
            color: white !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #198754 0%, #146c43 100%);
            border: none;
            color: white !important;
            font-weight: 600;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #146c43 0%, #0f5132 100%);
            transform: translateY(-2px);
            color: white !important;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            border: none;
            color: #000 !important;
            font-weight: 600;
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #e0a800 0%, #c79100 100%);
            transform: translateY(-2px);
            color: #000 !important;
        }

        .btn-info {
            background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);
            border: none;
            color: #000 !important;
            font-weight: 600;
        }

        .btn-info:hover {
            background: linear-gradient(135deg, #0aa2c0 0%, #088ca0 100%);
            transform: translateY(-2px);
            color: #000 !important;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            border: none;
            color: white !important;
            font-weight: 600;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268 0%, #4e555b 100%);
            transform: translateY(-2px);
            color: white !important;
        }

        .btn-dark {
            background: linear-gradient(135deg, #212529 0%, #1a1d20 100%);
            border: none;
            color: white !important;
            font-weight: 600;
        }

        .btn-dark:hover {
            background: linear-gradient(135deg, #1a1d20 0%, #121416 100%);
            transform: translateY(-2px);
            color: white !important;
        }

        .btn-light {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            color: #212529 !important;
            font-weight: 600;
        }

        .btn-light:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            transform: translateY(-2px);
            color: #212529 !important;
        }

        /* Small button sizes */
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        /* Large button sizes */
        .btn-lg {
            padding: 0.75rem 2rem;
            font-size: 1.125rem;
        }

        /* Disabled state */
        .btn:disabled,
        .btn.disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none !important;
        }

        /* ========================================================================
           ALERT STYLES
           ======================================================================== */
        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: slideInDown 0.3s ease-out;
            padding: 1rem 1.25rem;
            font-weight: 500;
        }

        .alert i {
            margin-right: 0.5rem;
        }

        .alert-success {
            background-color: #d1e7dd;
            color: #0a3622;
            border-left: 4px solid #198754;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #58151c;
            border-left: 4px solid #dc3545;
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* ========================================================================
           CARD STYLES
           ======================================================================== */
        .card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            color: white;
            font-weight: 600;
            border-bottom: none;
            padding: 1rem 1.25rem;
        }

        /* ========================================================================
           TABLE STYLES
           ======================================================================== */
        .table {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .table thead {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            color: white;
        }

        .table thead th {
            border: none;
            font-weight: 600;
            padding: 1rem;
        }

        .table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #dee2e6;
        }

        .table tbody tr:last-child {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background-color: var(--red-hover);
            transform: scale(1.01);
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* ========================================================================
           BADGE STYLES
           ======================================================================== */
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 600;
            border-radius: 6px;
        }

        .bg-primary {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%) !important;
        }

        .text-primary {
            color: var(--primary-red) !important;
        }

        /* ========================================================================
           PAGINATION STYLES
           ======================================================================== */
        .pagination {
            margin-bottom: 0;
        }

        .page-link {
            color: var(--primary-red);
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin: 0 0.25rem;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s ease;
        }

        .page-link:hover {
            color: white;
            background-color: var(--primary-red);
            border-color: var(--primary-red);
            transform: translateY(-2px);
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            border-color: var(--dark-red);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* ========================================================================
           FOOTER STYLES
           ======================================================================== */
        footer {
            background: linear-gradient(135deg, #343a40 0%, #212529 100%);
            color: white;
            padding: 2rem 0;
            margin-top: auto;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
        }

        /* ========================================================================
           ANIMATIONS
           ======================================================================== */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ========================================================================
           ACCESSIBILITY
           ======================================================================== */
        .nav-link:focus,
        .dropdown-toggle:focus,
        .btn:focus {
            outline: 3px solid rgba(255, 255, 255, 0.5);
            outline-offset: 2px;
        }

        /* ========================================================================
           MOBILE RESPONSIVE
           ======================================================================== */
        @media (max-width: 991px) {
            .nav-link {
                margin: 0.25rem 0;
                padding: 0.75rem 1rem !important;
            }

            .dropdown-toggle {
                width: 100%;
                justify-content: center;
                margin-top: 0.5rem;
            }

            .cart-link {
                justify-content: center;
            }

            .navbar-brand {
                font-size: 1.1rem;
            }

            .navbar-brand img {
                width: 35px;
                height: 35px;
            }
        }

        @media (max-width: 576px) {
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .card {
                margin-bottom: 1rem;
            }
        }
    </style>

</head>

<body>

    <?php
        $isAdmin = auth()->check() && auth()->user()->is_admin;
        $containerClass = $isAdmin ? 'container-fluid' : 'container';
        $brandText = $isAdmin ? 'Admin Panel' : 'BBQ Lagao & Beef Pares';
        $brandRoute = $isAdmin ? route('admin.dashboard') : route('home');
    ?>

    <nav class="navbar navbar-expand-lg">
        <div class="<?php echo e($containerClass); ?>">
            <a class="navbar-brand" href="<?php echo e($brandRoute); ?>">
                <img src="<?php echo e(asset('images/logo3.png')); ?>" alt="BBQ-Lagao Logo"
                    style="width: 40px; height: 40px; object-fit: cover;">
                <?php echo e($brandText); ?>

            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if(auth()->guard()->check()): ?>
                    <?php if($isAdmin): ?>
                        <!-- ADMIN NAVBAR -->
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('admin.dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('admin.products.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('admin.products.index')); ?>">
                                    <i class="fas fa-utensils"></i>
                                    <span>Products</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('admin.orders.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('admin.orders.index')); ?>">
                                    <i class="fas fa-receipt"></i>
                                    <span>Orders</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('admin.branches.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('admin.branches.index')); ?>">
                                    <i class="fas fa-store"></i>
                                    <span>Branches</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('admin.categories.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('admin.categories.index')); ?>">
                                    <i class="fas fa-tags"></i>
                                    <span>Categories</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('admin.customers.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('admin.customers.index')); ?>">
                                    <i class="fas fa-users"></i>
                                    <span>Customers</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('admin.feedback.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('admin.feedback.index')); ?>">
                                    <i class="fas fa-comment-dots"></i>
                                    <span>Feedback</span>
                                </a>
                            </li>
                        </ul>
                    <?php else: ?>
                        <!-- USER NAVBAR (LOGGED IN) -->
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>" href="<?php echo e(route('home')); ?>">
                                    <i class="fas fa-home"></i>
                                    <span>Home</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e(request()->routeIs('orders.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('orders.index')); ?>">
                                    <i class="fas fa-receipt"></i>
                                    <span>My Orders</span>
                                </a>
                            </li>
                        </ul>
                    <?php endif; ?>

                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <?php if(!$isAdmin): ?>
                            <li class="nav-item">
                                <a class="nav-link cart-link <?php echo e(request()->routeIs('cart.*') ? 'active' : ''); ?>"
                                    href="<?php echo e(route('cart.index')); ?>" style="position: relative;">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Cart</span>
                                    <span class="cart-count" id="cart-count" data-count="0">0</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-user-circle"></i>
                                <span><?php echo e(auth()->user()->name); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if($isAdmin): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('home')); ?>">
                                            <i class="fas fa-globe"></i>
                                            <span>View Site</span>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('feedback.index')); ?>">
                                            <i class="fas fa-comment-dots"></i>
                                            <span>Send Feedback</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('feedback.history')); ?>">
                                            <i class="fas fa-history"></i>
                                            <span>Feedback History</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('orders.index')); ?>">
                                            <i class="fas fa-receipt"></i>
                                            <span>My Orders</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('profile.edit')); ?>">
                                            <i class="fas fa-user-edit"></i>
                                            <span>My Profile</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt"></i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                <?php else: ?>
                    <!-- GUEST NAVBAR (NOT LOGGED IN) -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>" href="<?php echo e(route('home')); ?>">
                                <i class="fas fa-home"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link cart-link <?php echo e(request()->routeIs('cart.*') ? 'active' : ''); ?>"
                                href="<?php echo e(route('cart.index')); ?>" style="position: relative;">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Cart</span>
                                <span class="cart-count" id="cart-count" data-count="0">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('login') ? 'active' : ''); ?>"
                                href="<?php echo e(route('login')); ?>">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('register') ? 'active' : ''); ?>"
                                href="<?php echo e(route('register')); ?>">
                                <i class="fas fa-user-plus"></i>
                                <span>Register</span>
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-3">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        
        <?php if(session('error')): ?>
            <?php
                $isCartPage = request()->is('cart') || request()->is('cart/*');
                $isMigrating = session('_cart_migration_needed');
                $shouldHideError = $isCartPage && $isMigrating;
            ?>

            <?php if (! ($shouldHideError)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <footer>
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 BBQ Lagao & Beef Pares. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    
    <script src="<?php echo e(asset('js/cart.js')); ?>"></script>

    <script src="<?php echo e(asset('js/main.js')); ?>"></script>

    
    
    <?php if(session('_cart_migration_needed')): ?>
        <script>
            /**
             * localStorage Cart Migration After Login
             * Migrates cart from old session to new session automatically
             */
            (function () {
                'use strict';

                // Get session IDs from meta tags (set by LoginController)
                const oldSessionId = document.querySelector('meta[name="cart-old-session"]')?.content;
                const newSessionId = document.querySelector('meta[name="cart-new-session"]')?.content;

                if (!oldSessionId || !newSessionId || oldSessionId === newSessionId) {
                    return; // Nothing to migrate
                }

                // Wait for cart.js to fully load
                function waitForCartJs() {
                    return new Promise((resolve) => {
                        if (typeof window.migrateCartAfterLogin === 'function') {
                            resolve();
                        } else {
                            setTimeout(() => waitForCartJs().then(resolve), 50);
                        }
                    });
                }

                // Execute migration when DOM and cart.js are ready
                document.addEventListener('DOMContentLoaded', async function () {
                    try {
                        await waitForCartJs();

                        // Build storage keys
                        const oldKey = `cart_session_${oldSessionId}`;
                        const newKey = `cart_session_${newSessionId}`;

                        // Get old cart data
                        const oldCartData = localStorage.getItem(oldKey);

                        if (!oldCartData) {
                            return; // Nothing to migrate
                        }

                        let oldCart;
                        try {
                            oldCart = JSON.parse(oldCartData);
                        } catch (e) {
                            console.error('Failed to parse cart data:', e);
                            return;
                        }

                        if (!Array.isArray(oldCart) || oldCart.length === 0) {
                            localStorage.removeItem(oldKey);
                            return;
                        }

                        // Check if new session already has cart data
                        const newCartData = localStorage.getItem(newKey);
                        let newCart = [];

                        if (newCartData) {
                            try {
                                newCart = JSON.parse(newCartData);
                            } catch (e) {
                                newCart = [];
                            }
                        }

                        // Merge carts - old cart items take priority
                        const merged = {};

                        oldCart.forEach(item => {
                            if (item && item.id && item.quantity) {
                                merged[item.id] = item;
                            }
                        });

                        newCart.forEach(item => {
                            if (item && item.id && item.quantity && !merged[item.id]) {
                                merged[item.id] = item;
                            }
                        });

                        const mergedCart = Object.values(merged);

                        // Save merged cart to new session
                        localStorage.setItem(newKey, JSON.stringify(mergedCart));

                        // Remove old cart
                        localStorage.removeItem(oldKey);

                        console.log(`Cart migrated successfully: ${mergedCart.length} items`);

                        // Clear session flags via AJAX
                        try {
                            await fetch('/cart/cleanup-migration-flags', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                                }
                            });
                        } catch (err) {
                            console.warn('Failed to cleanup session flags:', err);
                        }

                        // Update cart count display
                        if (typeof window.updateCartCount === 'function') {
                            window.updateCartCount();
                        }

                        // Show success notification
                        if (mergedCart.length > 0 && typeof window.showNotification === 'function') {
                            window.showNotification(
                                `Welcome back! Your cart has ${mergedCart.length} item(s)`,
                                'success'
                            );
                        }

                        // Dispatch custom event for other scripts
                        window.dispatchEvent(new CustomEvent('cartMigrated', {
                            detail: {
                                itemCount: mergedCart.length,
                                oldSession: oldSessionId,
                                newSession: newSessionId
                            }
                        }));

                    } catch (error) {
                        console.error('Cart migration failed:', error);
                    }
                });

            })();
        </script>
    <?php endif; ?>

    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const phoneInputs = document.querySelectorAll('input[type="tel"], input[name="phone"]');

            phoneInputs.forEach(function (phoneInput) {
                phoneInput.addEventListener('input', function (e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });

                phoneInput.addEventListener('paste', function (e) {
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    this.value = pastedText.replace(/[^0-9]/g, '');
                });

                phoneInput.addEventListener('keypress', function (e) {
                    if (e.charCode < 48 || e.charCode > 57) {
                        e.preventDefault();
                    }
                });

                phoneInput.setAttribute('inputmode', 'numeric');
                phoneInput.setAttribute('pattern', '[0-9]*');
            });
        });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH C:\Users\User\Videos\projectIT12\IT12L_FullProject_ERP\resources\views/layouts/app.blade.php ENDPATH**/ ?>