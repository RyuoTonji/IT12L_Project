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


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    $redirect = match ($user->role ?? '') {
        'admin' => redirect()->route('admin.dashboard'),
        'manager' => redirect()->route('manager.dashboard'),
        'inventory' => redirect()->route('inventory.dashboard'),
        'cashier' => redirect()->route('cashier.dashboard'),
        default => function () use ($user) {
                \Log::error('Login failed: Invalid or missing role', [
                'user_id' => $user->id,
                'role' => $user->role ?? 'null'
                ]);
                Auth::logout();
                abort(403, 'Access denied: No role assigned to your account or the account is not registered. Please contact administrator.');
            }
    };

    return is_callable($redirect) ? $redirect() : $redirect;
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/export', [ProfileController::class, 'exportData'])->name('profile.export');
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/branches', [App\Http\Controllers\Admin\BranchController::class, 'index'])->name('admin.branches.index');
    Route::get('/branches/{branch}', [App\Http\Controllers\Admin\BranchController::class, 'show'])->name('admin.branches.show');
    Route::post('/branches/{branch}/switch', [App\Http\Controllers\Admin\BranchController::class, 'switchBranch'])->name('admin.branches.switch');

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('menu', MenuController::class);

    // AJAX endpoints
    Route::post('/menu/{menu}/update-availability', [MenuController::class, 'updateAvailability'])->name('menu.update-availability');
    Route::post('/menu/{menu}/update-branch-availability', [MenuController::class, 'updateBranchAvailability'])->name('menu.update-branch-availability');

    Route::resource('inventory', InventoryController::class);
    Route::resource('staff', StaffController::class);

    Route::post('/staff/{staff}/update-status', [StaffController::class, 'updateStatus'])->name('staff.update-status');


    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');
    Route::get('/reports/activities', [ReportController::class, 'activities'])->name('admin.reports.activities');
    Route::get('/reports/daily', [ReportController::class, 'daily'])->name('admin.reports.daily');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('admin.reports.sales');
    Route::get('/reports/items', [ReportController::class, 'items'])->name('admin.reports.items');
    Route::get('/reports/staff', [ReportController::class, 'staff'])->name('admin.reports.staff');
    Route::get('/void-requests', [\App\Http\Controllers\Manager\VoidRequestController::class, 'index'])->name('admin.void-requests.index');
    Route::post('/void-requests/{voidRequest}/approve', [\App\Http\Controllers\Manager\VoidRequestController::class, 'approve'])->name('admin.void-requests.approve');
    Route::post('/void-requests/{voidRequest}/reject', [\App\Http\Controllers\Manager\VoidRequestController::class, 'reject'])->name('admin.void-requests.reject');
    Route::get('/void-requests/export-pdf', [\App\Http\Controllers\Manager\VoidRequestController::class, 'exportPdf'])->name('admin.void-requests.export-pdf');

    // Order details route for modal
    Route::get('/orders/{order}/details', [AdminDashboardController::class, 'getOrderDetails'])->name('admin.orders.details');
});

Route::prefix('cashier')->middleware(['auth', 'role:cashier'])->group(function () {
    Route::get('/dashboard', [CashierDashboardController::class, 'index'])->name('cashier.dashboard');
    Route::resource('tables', TableController::class);
    Route::resource('orders', OrderController::class);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/{order}/request-void', [OrderController::class, 'requestVoid'])->name('orders.request-void');
    Route::resource('payments', PaymentController::class);
    Route::get('/kitchen-display', [CashierDashboardController::class, 'kitchenDisplay'])->name('cashier.kitchen');

    // Order details route for modal
    Route::get('/orders/{order}/details', [CashierDashboardController::class, 'getOrderDetails'])->name('cashier.orders.details');
});


Route::prefix('inventory')->middleware(['auth', 'role:inventory'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.dashboard');
    Route::post('/add', [\App\Http\Controllers\InventoryController::class, 'addStock'])->name('inventory.add');
    Route::patch('/{inventory}', [\App\Http\Controllers\InventoryController::class, 'updateStock'])->name('inventory.update');
    Route::delete('/{inventory}', [\App\Http\Controllers\InventoryController::class, 'destroy'])->name('inventory.destroy');
});

Route::prefix('manager')->middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\ManagerController::class, 'index'])->name('manager.dashboard');
    Route::get('/reports', [\App\Http\Controllers\ManagerController::class, 'reports'])->name('manager.reports');
    Route::get('/reports/daily', [\App\Http\Controllers\ManagerController::class, 'daily'])->name('manager.reports.daily');
    Route::get('/reports/staff', [\App\Http\Controllers\ManagerController::class, 'staff'])->name('manager.reports.staff');
    Route::get('/reports/sales', [\App\Http\Controllers\ManagerController::class, 'sales'])->name('manager.reports.sales');
    Route::get('/void-requests', [\App\Http\Controllers\Manager\VoidRequestController::class, 'index'])->name('manager.void-requests.index');
    Route::post('/void-requests/{voidRequest}/approve', [\App\Http\Controllers\Manager\VoidRequestController::class, 'approve'])->name('manager.void-requests.approve');
    Route::post('/void-requests/{voidRequest}/reject', [\App\Http\Controllers\Manager\VoidRequestController::class, 'reject'])->name('manager.void-requests.reject');
    Route::get('/void-requests/export-pdf', [\App\Http\Controllers\Manager\VoidRequestController::class, 'exportPdf'])->name('manager.void-requests.export-pdf');
});

// General Report Routes (accessible by authorized roles)
Route::middleware(['auth'])->group(function () {
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');


    Route::get('/shift-reports/create', [\App\Http\Controllers\ShiftReportController::class, 'create'])->name('shift-reports.create');
    Route::post('/shift-reports', [\App\Http\Controllers\ShiftReportController::class, 'store'])->name('shift-reports.store');
});


Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/shift-reports', [\App\Http\Controllers\ShiftReportController::class, 'index'])->name('admin.shift-reports.index');
    Route::get('/shift-reports/{shiftReport}', [\App\Http\Controllers\ShiftReportController::class, 'show'])->name('admin.shift-reports.show');
    Route::post('/shift-reports/{shiftReport}/reply', [\App\Http\Controllers\ShiftReportController::class, 'reply'])->name('admin.shift-reports.reply');
});


Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::get('/export/inventory', [\App\Http\Controllers\ExportController::class, 'exportInventory'])->name('export.inventory');
    Route::get('/export/sales', [\App\Http\Controllers\ExportController::class, 'exportSales'])->name('export.sales');
    Route::get('/export/items', [\App\Http\Controllers\ExportController::class, 'exportItems'])->name('export.items');
    Route::get('/export/staff', [\App\Http\Controllers\ExportController::class, 'exportStaff'])->name('export.staff');
    Route::get('/export/daily', [\App\Http\Controllers\ExportController::class, 'exportDaily'])->name('export.daily');
    Route::get('/export/report/{report}', [\App\Http\Controllers\ExportController::class, 'exportShiftReport'])->name('export.report');
});

require __DIR__ . '/auth.php';
