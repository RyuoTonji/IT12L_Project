<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ============================================================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================================================

// Home & Browse
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/browse/{id}', [HomeController::class, 'browse'])->name('browse');
Route::get('/browse/{branchId}/category/{categoryId}', [HomeController::class, 'filterByCategory'])->name('browse.category');

// Products
Route::get('/products/{id}', [UserProductController::class, 'show'])->name('products.show');
Route::get('/products/search', [UserProductController::class, 'search'])->name('products.search');
Route::get('/api/products/{id}', [UserProductController::class, 'getDetails'])->name('api.products.details');

// ============================================================================
// CART ROUTES (Available to EVERYONE - Guests and Users)
// ============================================================================

// Cart viewing
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// Cart API endpoints (public for guests)
Route::get('/api/cart/count', [CartController::class, 'count'])->name('api.cart.count');
Route::post('/api/cart/products', [CartController::class, 'getProducts'])->name('cart.products');

// Cart actions (NO AUTH REQUIRED - handled by JavaScript for guests)
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/sync', [CartController::class, 'sync'])->name('cart.sync');

// ============================================================================
// AUTH ROUTES (Login/Register/Logout)
// ============================================================================

// Guest only routes (login/register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

// Logout (authenticated users only)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    Route::get('/logout', [LogoutController::class, 'logout'])->name('logout.get');
    
    // *** NEW: Cart sync after login (authenticated users only) ***
    Route::get('/api/cart/sync-after-login', [CartController::class, 'syncAfterLogin'])->name('cart.syncAfterLogin');
});

// ============================================================================
// USER ROUTES (Authenticated Users Only, NOT Admin)
// ============================================================================

Route::middleware(['auth', 'prevent_admin_cart'])->group(function () {
    // Checkout (requires login and non-admin)
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'index'])->name('checkout.index.post');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/confirm', [CheckoutController::class, 'confirm'])->name('checkout.confirm');
    
    // Orders (User's orders)
    Route::get('/orders', [UserOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [UserOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [UserOrderController::class, 'cancel'])->name('orders.cancel');
});

// ============================================================================
// ADMIN ROUTES (Admin Users Only)
// ============================================================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('index');
        Route::get('/create', [AdminProductController::class, 'create'])->name('create');
        Route::post('/', [AdminProductController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminProductController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminProductController::class, 'destroy'])->name('destroy');
        Route::get('/archived', [AdminProductController::class, 'archived'])->name('archived');
        Route::post('/{id}/restore', [AdminProductController::class, 'restore'])->name('restore');
        Route::post('/{id}/toggle-availability', [AdminProductController::class, 'toggleAvailability'])->name('toggleAvailability');
    });

    // Orders Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminOrderController::class, 'show'])->name('show');
        Route::patch('/{id}/update-status', [AdminOrderController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{id}', [AdminOrderController::class, 'destroy'])->name('destroy');
        Route::get('/archived/list', [AdminOrderController::class, 'archived'])->name('archived');
        Route::post('/{id}/restore', [AdminOrderController::class, 'restore'])->name('restore');
    });

    // Customer Management
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/archived/list', [CustomerController::class, 'archived'])->name('archived');
        Route::post('/{id}/restore', [CustomerController::class, 'restore'])->name('restore');
        Route::get('/{id}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CustomerController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('toggleStatus');
        Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('destroy');
    });

    // Branches Management
    Route::prefix('branches')->name('branches.')->group(function () {
        Route::get('/', [BranchController::class, 'index'])->name('index');
        Route::get('/create', [BranchController::class, 'create'])->name('create');
        Route::post('/', [BranchController::class, 'store'])->name('store');
        Route::get('/archived/list', [BranchController::class, 'archived'])->name('archived');
        Route::post('/{id}/restore', [BranchController::class, 'restore'])->name('restore');
        Route::get('/{id}', [BranchController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BranchController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BranchController::class, 'update'])->name('update');
        Route::delete('/{id}', [BranchController::class, 'destroy'])->name('destroy');
    });

    // Categories Management
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::get('/archived', [CategoryController::class, 'archived'])->name('archived');
        Route::post('/{id}/restore', [CategoryController::class, 'restore'])->name('restore');
    });
});