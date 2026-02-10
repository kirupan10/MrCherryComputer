<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard - All authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // POS - Accessible by all roles (admin, manager, cashier)
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [POSController::class, 'index'])->name('index');
        Route::get('/search-products', [POSController::class, 'searchProducts'])->name('search-products');
        Route::post('/process-sale', [POSController::class, 'processSale'])->name('process-sale');
        Route::get('/invoice/{id}', [POSController::class, 'printInvoice'])->name('invoice');
        Route::get('/thermal-invoice/{id}', [POSController::class, 'thermalInvoice'])->name('thermal-invoice');
    });

    // Sales - All roles can view sales (with role-based filtering in controller)
    Route::resource('sales', SaleController::class)->only(['index', 'show']);
    Route::get('/sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');
    Route::get('/sales/{sale}/download', [SaleController::class, 'downloadInvoice'])->name('sales.download');

    // Admin and Manager routes
    Route::middleware(['role:admin,manager'])->group(function () {

        // Categories
        Route::resource('categories', CategoryController::class);

        // Units
        Route::resource('units', UnitController::class);

        // Products
        Route::resource('products', ProductController::class);
        Route::post('/products/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.update-stock');

        // Customers
        Route::resource('customers', CustomerController::class);
        Route::get('/customers-search', [CustomerController::class, 'search'])->name('customers.search');

        // Sales management (edit, update status, delete)
        Route::get('/sales/{sale}/edit', [SaleController::class, 'edit'])->name('sales.edit');
        Route::patch('/sales/{sale}/status', [SaleController::class, 'updateStatus'])->name('sales.update-status');

        // Returns
        Route::resource('returns', ReturnController::class);
        Route::get('/returns-search-sale', [ReturnController::class, 'searchSale'])->name('returns.search-sale');
        Route::post('/returns/{return}/complete', [ReturnController::class, 'complete'])->name('returns.complete');
        Route::post('/returns/{return}/cancel', [ReturnController::class, 'cancel'])->name('returns.cancel');

        // Expenses
        Route::resource('expenses', ExpenseController::class);

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/product-sales', [ReportController::class, 'productSales'])->name('product-sales');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            Route::get('/stock-movement', [ReportController::class, 'stockMovement'])->name('stock-movement');
            Route::get('/expenses', [ReportController::class, 'expenses'])->name('expenses');
            Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
            Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        });
    });

    // Admin only routes
    Route::middleware(['role:admin'])->group(function () {

        // User Management
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Expense Categories
        Route::resource('expense-categories', ExpenseCategoryController::class);

        // Expense Approval
        Route::post('/expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
        Route::post('/expenses/{expense}/reject', [ExpenseController::class, 'reject'])->name('expenses.reject');

        // Sales deletion (admin only)
        Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
    });
});

require __DIR__.'/auth.php';
