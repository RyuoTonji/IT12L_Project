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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================================================

// Home & Browse
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/browse/{id}', [HomeController::class, 'browse'])->name('browse');
Route::get('/browse/{branchId}/category/{categoryId}', [HomeController::class, 'filterByCategory']);

// Products
Route::get('/products/{id}', [UserProductController::class, 'show'])->name('products.show');
Route::get('/products/search', [UserProductController::class, 'search'])->name('products.search');
Route::get('/api/products/{id}', [UserProductController::class, 'getDetails']);

// ============================================================================
// CART ROUTES (Available to EVERYONE - Guests and Users)
// ============================================================================

// Cart viewing
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/api/cart/count', [CartController::class, 'count']);
Route::post('/api/cart/products', [CartController::class, 'getProducts'])->name('cart.products');

// Cart actions (NO AUTH REQUIRED - handled by JavaScript)
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/sync', [CartController::class, 'sync'])->name('cart.sync');
// ============================================================================
// AUTH ROUTES (Login/Register)
// ============================================================================
Route::middleware('guest')->group(function () {
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
});
// Logout
Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/logout', [LogoutController::class, 'logout'])->middleware('auth');
// ============================================================================
// USER ROUTES (Authenticated Users Only, NOT Admin)
// ============================================================================
Route::middleware(['auth', 'prevent_admin_cart'])->group(function () {
// Checkout (requires login)
Route::match(['GET', 'POST'], '/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
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
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

// Products Management
Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products.index');
Route::get('/products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
Route::post('/products', [AdminProductController::class, 'store'])->name('admin.products.store');
Route::get('/products/{id}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
Route::put('/products/{id}', [AdminProductController::class, 'update'])->name('admin.products.update');
Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');

// Orders Management
Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
Route::patch('/orders/{id}/update-status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

// Branches Management
Route::get('/branches', [BranchController::class, 'index'])->name('admin.branches.index');
Route::get('/branches/create', [BranchController::class, 'create'])->name('admin.branches.create');
Route::post('/branches', [BranchController::class, 'store'])->name('admin.branches.store');
Route::get('/branches/{id}/edit', [BranchController::class, 'edit'])->name('admin.branches.edit');
Route::put('/branches/{id}', [BranchController::class, 'update'])->name('admin.branches.update');
Route::delete('/branches/{id}', [BranchController::class, 'destroy'])->name('admin.branches.destroy');

// Categories Management
Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
});