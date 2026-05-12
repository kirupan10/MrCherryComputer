<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Dashboards\DashboardController;
use App\ShopTypes\Tech\Controllers\TechProductController;
use App\ShopTypes\Tech\Controllers\TechWarrantyController;
use App\ShopTypes\Tech\Controllers\TechRepairJobController;
use App\ShopTypes\Tech\Controllers\TechSerialNumberController;
use App\ShopTypes\Tech\Controllers\SalesController;
use App\ShopTypes\Tech\Controllers\FinanceReportController;
use App\ShopTypes\Tech\Controllers\ProfileController;
use App\ShopTypes\Tech\Controllers\CategoryController;
use App\ShopTypes\Tech\Controllers\WarrantyController;
use App\ShopTypes\Tech\Controllers\WarrantyClaimController;
use App\ShopTypes\Tech\Controllers\DeliveryController;
use App\ShopTypes\Tech\Controllers\JobController;
use App\ShopTypes\Tech\Controllers\ExpenseController;
use App\ShopTypes\Tech\Controllers\LogController;
use App\ShopTypes\Tech\Controllers\LetterheadController;
use App\ShopTypes\Tech\Controllers\BarcodeController;
use App\ShopTypes\Tech\Controllers\POSController;
use App\ShopTypes\Tech\Controllers\OrderController;
use App\ShopTypes\Tech\Controllers\OrderImportController;
use App\ShopTypes\Tech\Controllers\PurchaseController;
use App\ShopTypes\Tech\Controllers\BusinessTransactionController;
use App\ShopTypes\Tech\Controllers\ChequeController;
use App\ShopTypes\Tech\Controllers\VendorController;
use App\ShopTypes\Tech\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Tech Shop Type Routes
|--------------------------------------------------------------------------
|
| Routes specific to Tech/Computer shop type functionality
| All routes are prefixed with /tech and named tech.*
|
*/

// Tech Dashboard (canonical URL: /tech)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Profile & Settings
Route::get('/profile', [ProfileController::class, 'userProfile'])->name('user.profile');
Route::patch('/profile', [ProfileController::class, 'userProfileUpdate'])->name('user.profile.update');
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
Route::get('/settings', [ProfileController::class, 'settings'])->name('profile.settings');
Route::get('/features', [ProfileController::class, 'features'])->name('features');
Route::post('/features', [ProfileController::class, 'updateShopSettings'])->name('features.update');
Route::get('/setting', function () {
    return redirect()->route('tech.profile.settings');
});

// POS (canonical URL: /tech/pos)
Route::get('/pos', [POSController::class, 'index'])->name('pos.index');

// Orders (canonical URL: /tech/orders)
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

// Purchases (canonical URL: /tech/purchases)
Route::prefix('purchases')->name('purchases.')->group(function () {
    Route::get('/', [PurchaseController::class, 'index'])->name('index');
    Route::get('/create', [PurchaseController::class, 'create'])->name('create');
    Route::post('/', [PurchaseController::class, 'store'])->name('store');
    Route::get('/{creditPurchase}', [PurchaseController::class, 'show'])->name('show');
    Route::get('/{creditPurchase}/edit', [PurchaseController::class, 'edit'])->name('edit');
    Route::put('/{creditPurchase}', [PurchaseController::class, 'update'])->name('update');
    Route::delete('/{creditPurchase}', [PurchaseController::class, 'destroy'])->name('destroy');
    Route::post('/{creditPurchase}/record-payment', [PurchaseController::class, 'recordPayment'])->name('record-payment');
});

// Transactions (canonical URL: /tech/transactions)
Route::resource('transactions', BusinessTransactionController::class)->names('business-transactions');

// Cheques (canonical URL: /tech/cheques)
Route::prefix('cheques')->name('cheques.')->group(function () {
    Route::get('/', [ChequeController::class, 'index'])->name('index');
    Route::get('/create', [ChequeController::class, 'create'])->name('create');
    Route::post('/', [ChequeController::class, 'store'])->name('store');
    Route::get('/{cheque}', [ChequeController::class, 'show'])->name('show');
    Route::get('/{cheque}/edit', [ChequeController::class, 'edit'])->name('edit');
    Route::put('/{cheque}', [ChequeController::class, 'update'])->name('update');
    Route::delete('/{cheque}', [ChequeController::class, 'destroy'])->name('destroy');
    Route::post('/{cheque}/mark-deposited', [ChequeController::class, 'markDeposited'])->name('mark-deposited');
    Route::post('/{cheque}/mark-cleared', [ChequeController::class, 'markCleared'])->name('mark-cleared');
    Route::post('/{cheque}/mark-bounced', [ChequeController::class, 'markBounced'])->name('mark-bounced');
    Route::patch('/{cheque}/status', [ChequeController::class, 'updateStatus'])->name('status');
});

// Vendors (canonical URL: /tech/vendors)
Route::resource('vendors', VendorController::class);
Route::get('/vendors-search', [VendorController::class, 'search'])->name('vendors.search');
Route::post('/vendors/{vendor}/record-payment', [VendorController::class, 'recordPayment'])->name('vendors.record-payment');

// Categories
Route::resource('categories', CategoryController::class);
Route::get('/catagories', function () {
    return redirect()->route('tech.categories.index');
});

// Jobs
Route::get('/jobs-list', [JobController::class, 'list'])->name('jobs.list');
Route::resource('jobs', JobController::class);
Route::get('/jobs/{job}/receipt', [JobController::class, 'showReceipt'])->name('jobs.receipt');
Route::get('/jobs/{job}/pdf-job-sheet', [JobController::class, 'downloadPdfJobSheet'])->name('jobs.pdf-job-sheet');

// Delivery Management
Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
Route::get('/deliveries/create', [DeliveryController::class, 'create'])->name('deliveries.create');
Route::post('/deliveries', [DeliveryController::class, 'store'])->name('deliveries.store');
Route::get('/deliveries/{delivery}/edit', [DeliveryController::class, 'edit'])->name('deliveries.edit');
Route::put('/deliveries/{delivery}', [DeliveryController::class, 'update'])->name('deliveries.update');
Route::delete('/deliveries/{delivery}', [DeliveryController::class, 'destroy'])->name('deliveries.destroy');

// Expense Management
Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
Route::post('/expenses/store', [ExpenseController::class, 'store'])->name('expenses.store');
Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
Route::get('/expense-management', function () {
    return redirect()->route('tech.expenses.index');
});

// Warranty Management
Route::resource('warranties', WarrantyController::class);
Route::resource('warranty-claims', WarrantyClaimController::class);
Route::get('/warranty-claims/search/products', [WarrantyClaimController::class, 'searchProducts'])->name('warranty-claims.search.products');
Route::get('/warranty-claims/search/customers', [WarrantyClaimController::class, 'searchCustomers'])->name('warranty-claims.search.customers');
Route::get('/warranty-claims/products/{productId}/serial-numbers', [WarrantyClaimController::class, 'getProductSerialNumbers'])->name('warranty-claims.product.serial-numbers');

// Order Import Routes
Route::get('/manual', [OrderImportController::class, 'manualForm'])->name('orders.import.manual');
Route::post('/manual', [OrderImportController::class, 'storeManual'])->name('orders.import.store-manual');
Route::get('/orders/import/bulk', [OrderImportController::class, 'bulkForm'])->name('orders.import.bulk');
Route::post('/orders/import/bulk', [OrderImportController::class, 'processBulk'])->name('orders.import.process-bulk');
Route::get('/orders/import/template', [OrderImportController::class, 'downloadTemplate'])->name('orders.import.download-template');
Route::get('/import-single-order', function () {
    return redirect()->route('tech.orders.import.manual');
});
Route::get('/import-bulk-orders', function () {
    return redirect()->route('tech.orders.import.bulk');
});

// Audit Logs
Route::prefix('logs')->name('logs.')->group(function () {
    Route::get('/', [LogController::class, 'index'])->name('index');
    Route::get('/{log}', [LogController::class, 'show'])->name('show');
});
Route::get('/audit-logs', function () {
    return redirect()->route('tech.logs.index');
});

// Letterhead
Route::get('/letterhead', [LetterheadController::class, 'index'])->name('letterhead.index');
Route::post('/letterhead/upload', [LetterheadController::class, 'uploadLetterhead'])->name('letterhead.upload');
Route::post('/letterhead/save-positions', [LetterheadController::class, 'savePositions'])->name('letterhead.save-positions');
Route::get('/letterhead/positions', [LetterheadController::class, 'getPositions'])->name('letterhead.get-positions');
Route::post('/letterhead/save-toggles', [LetterheadController::class, 'saveToggles'])->name('letterhead.save-toggles');
Route::get('/letterhead/toggles', [LetterheadController::class, 'getToggles'])->name('letterhead.get-toggles');
Route::post('/letterhead/save-items-alignment', [LetterheadController::class, 'saveItemsAlignment'])->name('letterhead.save-items-alignment');
Route::post('/letterhead/save-table-width', [LetterheadController::class, 'saveTableWidth'])->name('letterhead.save-table-width');
Route::post('/letterhead/regenerate-preview', [LetterheadController::class, 'regeneratePreview'])->name('letterhead.regenerate-preview');
Route::post('/letterhead/save-sales-config', [LetterheadController::class, 'saveSalesConfig'])->name('letterhead.save-sales-config');
Route::get('/letterhead/position-preview', [\App\ShopTypes\Tech\Controllers\OrderController::class, 'positionPreview'])->name('letterhead.position_preview');
Route::post('/letterhead/save-offset', [\App\ShopTypes\Tech\Controllers\OrderController::class, 'saveLetterheadMergeOffset'])->name('letterhead.save_offset');

// Barcode Settings
Route::get('/barcode', [BarcodeController::class, 'index'])->name('barcode.index');
Route::post('/barcode/settings', [BarcodeController::class, 'updateSettings'])->name('barcode.settings.update');
Route::post('/barcode/preview', [BarcodeController::class, 'preview'])->name('barcode.preview');
Route::get('/barcode/test-print', [BarcodeController::class, 'testPrint'])->name('barcode.test.print');
Route::get('/barcode/print/bulk', [BarcodeController::class, 'printBulk'])->name('barcode.print.bulk');
Route::get('/barcode/print/product/{product}', [BarcodeController::class, 'printProduct'])->name('barcode.print.product');

// Tech Products
Route::resource('products', TechProductController::class);

// Customer Management
Route::resource('customers', \App\ShopTypes\Tech\Controllers\CustomerController::class);
Route::post('/customers/{customer}/update', [\App\ShopTypes\Tech\Controllers\CustomerController::class, 'updateAjax'])->name('customers.update.ajax');

// Tech Serial Numbers
Route::prefix('serial-numbers')->name('serial-numbers.')->group(function () {
    Route::get('/', [TechSerialNumberController::class, 'index'])->name('index');
    Route::get('/create', [TechSerialNumberController::class, 'create'])->name('create');
    Route::post('/', [TechSerialNumberController::class, 'store'])->name('store');
    Route::get('/{serialNumber}', [TechSerialNumberController::class, 'show'])->name('show');
    Route::get('/{serialNumber}/edit', [TechSerialNumberController::class, 'edit'])->name('edit');
    Route::put('/{serialNumber}', [TechSerialNumberController::class, 'update'])->name('update');
    Route::delete('/{serialNumber}', [TechSerialNumberController::class, 'destroy'])->name('destroy');

    // Bulk operations
    Route::post('/bulk-import', [TechSerialNumberController::class, 'bulkImport'])->name('bulk-import');
    Route::get('/product/{product}', [TechSerialNumberController::class, 'byProduct'])->name('by-product');
});

// Tech Warranty Claims
Route::prefix('warranty')->name('warranty.')->group(function () {
    Route::get('/', [TechWarrantyController::class, 'index'])->name('index');
    Route::get('/create', [TechWarrantyController::class, 'create'])->name('create');
    Route::post('/', [TechWarrantyController::class, 'store'])->name('store');
    Route::get('/{warrantyClaim}', [TechWarrantyController::class, 'show'])->name('show');
    Route::get('/{warrantyClaim}/edit', [TechWarrantyController::class, 'edit'])->name('edit');
    Route::put('/{warrantyClaim}', [TechWarrantyController::class, 'update'])->name('update');
    Route::delete('/{warrantyClaim}', [TechWarrantyController::class, 'destroy'])->name('destroy');

    // Status updates
    Route::post('/{warrantyClaim}/approve', [TechWarrantyController::class, 'approve'])->name('approve');
    Route::post('/{warrantyClaim}/reject', [TechWarrantyController::class, 'reject'])->name('reject');
    Route::post('/{warrantyClaim}/complete', [TechWarrantyController::class, 'complete'])->name('complete');
});

// Tech Repair Jobs
Route::prefix('repairs')->name('repairs.')->group(function () {
    Route::get('/', [TechRepairJobController::class, 'index'])->name('index');
    Route::get('/create', [TechRepairJobController::class, 'create'])->name('create');
    Route::post('/', [TechRepairJobController::class, 'store'])->name('store');
    Route::get('/{repairJob}', [TechRepairJobController::class, 'show'])->name('show');
    Route::get('/{repairJob}/edit', [TechRepairJobController::class, 'edit'])->name('edit');
    Route::put('/{repairJob}', [TechRepairJobController::class, 'update'])->name('update');
    Route::delete('/{repairJob}', [TechRepairJobController::class, 'destroy'])->name('destroy');

    // Job management actions
    Route::post('/{repairJob}/assign-technician', [TechRepairJobController::class, 'assignTechnician'])->name('assign-technician');
    Route::post('/{repairJob}/start-diagnosis', [TechRepairJobController::class, 'startDiagnosis'])->name('start-diagnosis');
    Route::post('/{repairJob}/start-repair', [TechRepairJobController::class, 'startRepair'])->name('start-repair');
    Route::post('/{repairJob}/complete', [TechRepairJobController::class, 'complete'])->name('complete');
    Route::post('/{repairJob}/deliver', [TechRepairJobController::class, 'deliver'])->name('deliver');

    // Parts and diagnostics
    Route::post('/{repairJob}/parts', [TechRepairJobController::class, 'addPart'])->name('add-part');
    Route::delete('/{repairJob}/parts/{part}', [TechRepairJobController::class, 'removePart'])->name('remove-part');
    Route::post('/{repairJob}/diagnostics', [TechRepairJobController::class, 'addDiagnostic'])->name('add-diagnostic');

    // Print job sheet
    Route::get('/{repairJob}/print', [TechRepairJobController::class, 'print'])->name('print');
});

// Backward-compatible dashboard URL
Route::get('/dashboard', function () {
    return redirect()->route('tech.dashboard');
});

// Backward-compatible caps/spelling aliases for requested paths
Route::get('/Finance', function () {
    return redirect()->route('tech.finance.index');
});
Route::get('/Warranties', function () {
    return redirect()->route('tech.warranties.index');
});
Route::get('/Warranty-Claims', function () {
    return redirect()->route('tech.warranty-claims.index');
});
Route::get('/Customers', function () {
    return redirect()->route('tech.customers.index');
});
Route::get('/Catagories', function () {
    return redirect()->route('tech.categories.index');
});
Route::get('/Jobs', function () {
    return redirect()->route('tech.jobs.index');
});
Route::get('/Letterhead', function () {
    return redirect()->route('tech.letterhead.index');
});
Route::get('/Barcode', function () {
    return redirect()->route('tech.barcode.index');
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/warranty', [PageController::class, 'reportsWarranty'])->name('warranty');
    Route::get('/repairs', [PageController::class, 'reportsRepairs'])->name('repairs');
    Route::get('/serial-numbers', [PageController::class, 'reportsSerialNumbers'])->name('serial-numbers');
});

// Tech sales canonical routes (/tech/sales...)
Route::middleware(['role:reports_access', 'shop.tenant'])->prefix('sales')->name('sales.')->group(function () {
    Route::get('/', [SalesController::class, 'index'])->name('index');
    Route::get('/daily', [SalesController::class, 'daily'])->name('daily');
    Route::get('/daily/download', [SalesController::class, 'downloadDaily'])->name('daily.download');
    Route::get('/weekly', [SalesController::class, 'weekly'])->name('weekly');
    Route::get('/weekly/download', [SalesController::class, 'downloadWeekly'])->name('weekly.download');
    Route::get('/monthly', [SalesController::class, 'monthly'])->name('monthly');
    Route::get('/monthly/download', [SalesController::class, 'downloadMonthly'])->name('monthly.download');
    Route::get('/yearly', [SalesController::class, 'yearly'])->name('yearly');
    Route::get('/yearly/download', [SalesController::class, 'downloadYearly'])->name('yearly.download');

    // API endpoints for chart data
    Route::get('/api/daily-data', [SalesController::class, 'getDailySalesData'])->name('api.daily');
    Route::get('/api/weekly-data', [SalesController::class, 'getWeeklySalesData'])->name('api.weekly');
    Route::get('/api/monthly-data', [SalesController::class, 'getMonthlySalesData'])->name('api.monthly');
    Route::get('/api/yearly-data', [SalesController::class, 'getYearlySalesData'])->name('api.yearly');

    // Finance report pages and APIs
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/returns', [FinanceReportController::class, 'returnsIndex'])->name('returns');
        Route::get('/returns/api', [FinanceReportController::class, 'returnsApi'])->name('returns.api');

        Route::get('/expenses', [FinanceReportController::class, 'expensesIndex'])->name('expenses');
        Route::get('/expenses/api', [FinanceReportController::class, 'expensesApi'])->name('expenses.api');

        Route::get('/credit-sales', [FinanceReportController::class, 'creditSalesIndex'])->name('credit-sales');
        Route::get('/credit-sales/api', [FinanceReportController::class, 'creditSalesApi'])->name('credit-sales.api');
    });
});

// Backward-compatible sales report URLs: /tech/reports/sales* -> /tech/sales*
Route::get('/reports/sales', function () {
    return redirect()->route('tech.sales.index');
});
Route::get('/reports/sales/daily', function (Request $request) {
    return redirect()->route('tech.sales.daily', $request->query());
});
Route::get('/reports/sales/daily/download', function (Request $request) {
    return redirect()->route('tech.sales.daily.download', $request->query());
});
Route::get('/reports/sales/weekly', function (Request $request) {
    return redirect()->route('tech.sales.weekly', $request->query());
});
Route::get('/reports/sales/weekly/download', function (Request $request) {
    return redirect()->route('tech.sales.weekly.download', $request->query());
});
Route::get('/reports/sales/monthly', function (Request $request) {
    return redirect()->route('tech.sales.monthly', $request->query());
});
Route::get('/reports/sales/monthly/download', function (Request $request) {
    return redirect()->route('tech.sales.monthly.download', $request->query());
});
Route::get('/reports/sales/yearly', function (Request $request) {
    return redirect()->route('tech.sales.yearly', $request->query());
});
Route::get('/reports/sales/yearly/download', function (Request $request) {
    return redirect()->route('tech.sales.yearly.download', $request->query());
});
Route::get('/reports/sales/api/daily-data', function (Request $request) {
    return redirect()->route('tech.sales.api.daily', $request->query());
});
Route::get('/reports/sales/api/weekly-data', function (Request $request) {
    return redirect()->route('tech.sales.api.weekly', $request->query());
});
Route::get('/reports/sales/api/monthly-data', function (Request $request) {
    return redirect()->route('tech.sales.api.monthly', $request->query());
});
Route::get('/reports/sales/api/yearly-data', function (Request $request) {
    return redirect()->route('tech.sales.api.yearly', $request->query());
});
Route::get('/reports/sales/finance/returns', function (Request $request) {
    return redirect()->route('tech.sales.finance.returns', $request->query());
});
Route::get('/reports/sales/finance/returns/api', function (Request $request) {
    return redirect()->route('tech.sales.finance.returns.api', $request->query());
});
Route::get('/reports/sales/finance/expenses', function (Request $request) {
    return redirect()->route('tech.sales.finance.expenses', $request->query());
});
Route::get('/reports/sales/finance/expenses/api', function (Request $request) {
    return redirect()->route('tech.sales.finance.expenses.api', $request->query());
});
Route::get('/reports/sales/finance/credit-sales', function (Request $request) {
    return redirect()->route('tech.sales.finance.credit-sales', $request->query());
});
Route::get('/reports/sales/finance/credit-sales/api', function (Request $request) {
    return redirect()->route('tech.sales.finance.credit-sales.api', $request->query());
});

// Shared reports routes file removed in tech-only build.
