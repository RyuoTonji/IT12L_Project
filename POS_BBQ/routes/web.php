<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Cashier\DashboardController as CashierDashboardController;
use App\Http\Controllers\Cashier\TableController;
use App\Http\Controllers\Cashier\OrderController;
use App\Http\Controllers\Cashier\PaymentController;

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Default route - redirect to login
Route::get('/', function () {
    // return view('welcome');
    return redirect('/login');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'manager' => redirect()->route('manager.dashboard'),
        'inventory' => redirect()->route('inventory.dashboard'),
        'cashier' => redirect()->route('cashier.dashboard'),
        default => redirect()->route('cashier.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('menu', MenuController::class);
    Route::resource('inventory', InventoryController::class);
    Route::resource('staff', StaffController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');
    Route::get('/reports/daily', [ReportController::class, 'daily'])->name('admin.reports.daily');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('admin.reports.sales');
    Route::get('/reports/items', [ReportController::class, 'items'])->name('admin.reports.items');
    Route::get('/reports/staff', [ReportController::class, 'staff'])->name('admin.reports.staff');

    // Void Requests
    Route::get('/void-requests', [\App\Http\Controllers\Manager\VoidRequestController::class, 'index'])->name('admin.void-requests.index');
    Route::post('/void-requests/{voidRequest}/approve', [\App\Http\Controllers\Manager\VoidRequestController::class, 'approve'])->name('admin.void-requests.approve');
    Route::post('/void-requests/{voidRequest}/reject', [\App\Http\Controllers\Manager\VoidRequestController::class, 'reject'])->name('admin.void-requests.reject');
});

// Cashier routes
Route::prefix('cashier')->middleware(['auth', 'role:cashier'])->group(function () {
    Route::get('/dashboard', [CashierDashboardController::class, 'index'])->name('cashier.dashboard');
    Route::resource('tables', TableController::class);
    // Restrict OrderController actions if needed, or handle in Controller
    Route::resource('orders', OrderController::class);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/{order}/request-void', [OrderController::class, 'requestVoid'])->name('orders.request-void');
    Route::resource('payments', PaymentController::class);
    Route::get('/kitchen-display', [CashierDashboardController::class, 'kitchenDisplay'])->name('cashier.kitchen');
});

// Inventory routes
Route::prefix('inventory')->middleware(['auth', 'role:inventory'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.dashboard');
    Route::post('/add', [\App\Http\Controllers\InventoryController::class, 'addStock'])->name('inventory.add');
    Route::patch('/{inventory}', [\App\Http\Controllers\InventoryController::class, 'updateStock'])->name('inventory.update');
    Route::delete('/{inventory}', [\App\Http\Controllers\InventoryController::class, 'destroy'])->name('inventory.destroy');
});

// Manager routes
Route::prefix('manager')->middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\ManagerController::class, 'index'])->name('manager.dashboard');
    Route::get('/reports', [\App\Http\Controllers\ManagerController::class, 'reports'])->name('manager.reports');

    // Void Requests
    Route::get('/void-requests', [\App\Http\Controllers\Manager\VoidRequestController::class, 'index'])->name('manager.void-requests.index');
    Route::post('/void-requests/{voidRequest}/approve', [\App\Http\Controllers\Manager\VoidRequestController::class, 'approve'])->name('manager.void-requests.approve');
    Route::post('/void-requests/{voidRequest}/reject', [\App\Http\Controllers\Manager\VoidRequestController::class, 'reject'])->name('manager.void-requests.reject');
});

// General Report Routes (accessible by authorized roles)
Route::middleware(['auth'])->group(function () {
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');

    // Shift Reports (for cashier, manager, inventory)
    Route::get('/shift-reports/create', [\App\Http\Controllers\ShiftReportController::class, 'create'])->name('shift-reports.create');
    Route::post('/shift-reports', [\App\Http\Controllers\ShiftReportController::class, 'store'])->name('shift-reports.store');
});

// Admin Shift Report Routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/shift-reports', [\App\Http\Controllers\ShiftReportController::class, 'index'])->name('admin.shift-reports.index');
    Route::get('/shift-reports/{shiftReport}', [\App\Http\Controllers\ShiftReportController::class, 'show'])->name('admin.shift-reports.show');
    Route::post('/shift-reports/{shiftReport}/reply', [\App\Http\Controllers\ShiftReportController::class, 'reply'])->name('admin.shift-reports.reply');
});

// Export Routes
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::get('/export/inventory', [\App\Http\Controllers\ExportController::class, 'exportInventory'])->name('export.inventory');
    Route::get('/export/sales', [\App\Http\Controllers\ExportController::class, 'exportSales'])->name('export.sales');
    Route::get('/export/report/{report}', [\App\Http\Controllers\ExportController::class, 'exportShiftReport'])->name('export.report');
});

require __DIR__ . '/auth.php';
