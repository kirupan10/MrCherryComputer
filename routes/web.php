<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;

use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Dashboards\DashboardController;
use App\Http\Controllers\Product\ProductExportController;
use App\Http\Controllers\Product\ProductImportController;
use App\Models\Order;
use App\Http\Controllers\PermissionController;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

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

// Redirect generic /reports and /finance to shop-type-specific URLs (must be before other route groups)
Route::get('/tech/{path?}', function (?string $path = null) {
    $targetPath = $path ? '/' . ltrim($path, '/') : '/';

    if (!request()->query()) {
        return redirect($targetPath);
    }

    return redirect($targetPath . '?' . http_build_query(request()->query()));
})->where('path', '.*');

Route::middleware(['auth'])->group(function () {
    Route::get('/reports', function (Request $request) {
        /** @var User|null $user */
        $user = auth()->user();
        $shop = $user->getActiveShop();

        if (!$shop || !$shop->shop_type) {
            return redirect()->route('dashboard');
        }

        $shopType = shop_type_route_key($shop->shop_type->value);
        return redirect()->route("{$shopType}.reports.index");
    })->name('reports.redirect');

    Route::get('/finance', function (Request $request) {
        /** @var User|null $user */
        $user = auth()->user();
        $shop = $user->getActiveShop();

        if (!$shop || !$shop->shop_type) {
            return redirect()->route('dashboard');
        }

        $shopType = shop_type_route_key($shop->shop_type->value);
        return redirect()->route("{$shopType}.finance.index");
    })->name('finance.redirect');
});

Route::middleware(['auth', 'check.suspended', 'ensure.shop.selected', 'shop.route.prefix'])->group(function () {

    // Shop Selection Routes (for multi-shop owners)
    Route::get('/shop/select', [App\Http\Controllers\ShopSelectionController::class, 'show'])->name('shop.select')->withoutMiddleware('ensure.shop.selected');
    Route::post('/shop/select', [App\Http\Controllers\ShopSelectionController::class, 'select'])->name('shop.select.post')->withoutMiddleware('ensure.shop.selected');
    Route::post('/shop/switch', [App\Http\Controllers\ShopSelectionController::class, 'switch'])->name('shop.switch')->withoutMiddleware('ensure.shop.selected');

    // Root route - redirect to shop-specific dashboard
    Route::get('/', function () {
        /** @var User|null $user */
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin users see admin dashboard view
        if ($user->isAdmin()) {
            return app(DashboardController::class)->index();
        }

        $shop = $user->getActiveShop();

        if (!$shop || !$shop->shop_type) {
            return redirect()->route('shop.select');
        }

        $shopType = shop_type_route_key($shop->shop_type->value);
        $shopDashboardRoute = "{$shopType}.dashboard";

        if (Route::has($shopDashboardRoute)) {
            return redirect()->route($shopDashboardRoute);
        }

        // Fallback to generic dashboard
        return app(DashboardController::class)->index();
    })->name('dashboard');

    // User Management (Shop Owner Only)
    Route::middleware(['role:user_management'])->group(function () {
        Route::resource('/users', UserController::class)->except(['show']);
        Route::put('/user/change-password/{username}', [UserController::class, 'updatePassword'])->name('users.updatePassword');
    });

    // User Profile Routes (for regular users)
    Route::get('/profile', [ProfileController::class, 'userProfile'])->name('user.profile');
    Route::patch('/profile', [ProfileController::class, 'userProfileUpdate'])->name('user.profile.update');

    // Admin Profile Routes
    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit')
        ->withoutMiddleware('ensure.shop.selected');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Inventory Management (Manager and Shop Owner)
    Route::middleware(['role:inventory_access', 'shop.tenant'])->group(function () {
        // Customer Management routes (shared names used by POS, Livewire, and shop-type views)
        Route::resource('/customers', CustomerController::class);

        Route::resource('/categories', CategoryController::class);
        Route::resource('/units', UnitController::class);

        // Audit Logs Management (Only for Shop Owners and Managers)
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [App\Http\Controllers\LogController::class, 'index'])->name('index');
            Route::get('/{log}', [App\Http\Controllers\LogController::class, 'show'])->name('show');
        });

        // Route Products
        Route::get('/products/import', [ProductImportController::class, 'create'])->name('products.import.view');
        Route::post('/products/import', [ProductImportController::class, 'store'])->name('products.import.store');
        Route::get('/products/export', [ProductExportController::class, 'create'])->name('products.export.store');
        Route::post('/products/{productSlug}/add-stock', [ProductController::class, 'addStock'])->name('products.add-stock');
        Route::post('/products/{productSlug}/update-price', [ProductController::class, 'updatePrice'])->name('products.update-price');
        Route::resource('/products', ProductController::class);
    });

    // Permission Management (Shop Owner and Manager only, not employee)
    Route::middleware(['role:inventory_access', 'shop.tenant'])->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::put('/permissions', [PermissionController::class, 'update'])->name('permissions.update');
    });

    // Finance Management (Excludes Employee role)
    Route::middleware(['role:finance_access', 'shop.tenant'])->group(function () {
        // Transactions
        Route::get('/transactions', function () {
            /** @var User|null $user */
            $user = auth()->user();
            $activeShop = $user ? $user->getActiveShop() : null;
            $shopType = $activeShop && $activeShop->shop_type
                ? shop_type_route_key($activeShop->shop_type->value)
                : null;

            if ($shopType) {
                $shopTransactionsRoute = "{$shopType}.business-transactions.index";
                if (Route::has($shopTransactionsRoute)) {
                    return redirect()->route($shopTransactionsRoute);
                }
            }

            return app(App\Http\Controllers\BusinessTransactionController::class)->index(request());
        })->name('business-transactions.index');
        Route::resource('/transactions', App\Http\Controllers\BusinessTransactionController::class)
            ->names('business-transactions')
            ->except(['index']);

        // Credit Purchases Management
        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/', function () {
                /** @var User|null $user */
                $user = auth()->user();
                $activeShop = $user ? $user->getActiveShop() : null;
                $shopType = $activeShop && $activeShop->shop_type
                    ? shop_type_route_key($activeShop->shop_type->value)
                    : null;

                if ($shopType) {
                    $shopPurchasesRoute = "{$shopType}.purchases.index";
                    if (Route::has($shopPurchasesRoute)) {
                        return redirect()->route($shopPurchasesRoute);
                    }
                }

                return app(App\Http\Controllers\PurchaseController::class)->index(request());
            })->name('index');
            Route::get('/create', [App\Http\Controllers\PurchaseController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\PurchaseController::class, 'store'])->name('store');
            Route::get('/{creditPurchase}', [App\Http\Controllers\PurchaseController::class, 'show'])->name('show');
            Route::get('/{creditPurchase}/edit', [App\Http\Controllers\PurchaseController::class, 'edit'])->name('edit');
            Route::put('/{creditPurchase}', [App\Http\Controllers\PurchaseController::class, 'update'])->name('update');
            Route::delete('/{creditPurchase}', [App\Http\Controllers\PurchaseController::class, 'destroy'])->name('destroy');
            Route::post('/{creditPurchase}/record-payment', [App\Http\Controllers\PurchaseController::class, 'recordPayment'])->name('record-payment');
        });

        // Cheque Management
        Route::prefix('cheques')->name('cheques.')->group(function () {
            Route::get('/', function () {
                /** @var User|null $user */
                $user = auth()->user();
                $activeShop = $user ? $user->getActiveShop() : null;
                $shopType = $activeShop && $activeShop->shop_type
                    ? shop_type_route_key($activeShop->shop_type->value)
                    : null;

                if ($shopType) {
                    $shopChequesRoute = "{$shopType}.cheques.index";
                    if (Route::has($shopChequesRoute)) {
                        return redirect()->route($shopChequesRoute);
                    }
                }

                return app(App\Http\Controllers\ChequeController::class)->index(request());
            })->name('index');
            Route::get('/create', [App\Http\Controllers\ChequeController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\ChequeController::class, 'store'])->name('store');
            Route::get('/{cheque}', [App\Http\Controllers\ChequeController::class, 'show'])->name('show');
            Route::get('/{cheque}/edit', [App\Http\Controllers\ChequeController::class, 'edit'])->name('edit');
            Route::put('/{cheque}', [App\Http\Controllers\ChequeController::class, 'update'])->name('update');
            Route::delete('/{cheque}', [App\Http\Controllers\ChequeController::class, 'destroy'])->name('destroy');
            Route::post('/{cheque}/mark-deposited', [App\Http\Controllers\ChequeController::class, 'markDeposited'])->name('mark-deposited');
            Route::post('/{cheque}/mark-cleared', [App\Http\Controllers\ChequeController::class, 'markCleared'])->name('mark-cleared');
            Route::post('/{cheque}/mark-bounced', [App\Http\Controllers\ChequeController::class, 'markBounced'])->name('mark-bounced');
            Route::patch('/{cheque}/status', [App\Http\Controllers\ChequeController::class, 'updateStatus'])->name('status');
        });

        // Supplier/Vendor Management
        Route::get('/vendors', function () {
            /** @var User|null $user */
            $user = auth()->user();
            $activeShop = $user ? $user->getActiveShop() : null;
            $shopType = $activeShop && $activeShop->shop_type
                ? shop_type_route_key($activeShop->shop_type->value)
                : null;

            if ($shopType) {
                $shopVendorsRoute = "{$shopType}.vendors.index";
                if (Route::has($shopVendorsRoute)) {
                    return redirect()->route($shopVendorsRoute);
                }
            }

            return app(App\Http\Controllers\VendorController::class)->index(request());
        })->name('vendors.index');
        Route::resource('/vendors', App\Http\Controllers\VendorController::class)->except(['index']);
        Route::get('/vendors-search', [App\Http\Controllers\VendorController::class, 'search'])->name('vendors.search');
        Route::post('/vendors/{vendor}/record-payment', [App\Http\Controllers\VendorController::class, 'recordPayment'])->name('vendors.record-payment');

        // Finance Management - Comprehensive Finance Dashboard & Reports
        Route::prefix('finance')->name('finance.')->group(function () {
            // Finance dashboard index - DISABLED: Use shop-type specific routes instead.
            // Route::get('/', [App\Http\Controllers\FinanceController::class, 'dashboard'])->name('index');
            Route::get('/pnl-statement', [App\Http\Controllers\FinanceController::class, 'profitLoss'])->name('pnl-statement');
            Route::get('/profit-loss', [App\Http\Controllers\FinanceController::class, 'profitLoss'])->name('profit-loss');
            Route::get('/monthly-report', [App\Http\Controllers\FinanceController::class, 'monthlyReport'])->name('monthly-report');
            Route::post('/verify-profit', [App\Http\Controllers\FinanceController::class, 'verifyProfit'])->name('verify-profit');
            Route::post('/update-kpi-calculations', [App\Http\Controllers\FinanceController::class, 'updateKpiCalculations'])->name('update-kpi-calculations');
            Route::post('/update-stored-profit', [App\Http\Controllers\FinanceController::class, 'updateStoredProfit'])->name('update-stored-profit');
            Route::post('/bulk-update-profit', [App\Http\Controllers\FinanceController::class, 'bulkUpdateProfit'])->name('bulk-update-profit');
            Route::post('/bulk-preview-profit', [App\Http\Controllers\FinanceController::class, 'bulkPreviewProfit'])->name('bulk-preview-profit');
            Route::post('/update-selected-profit', [App\Http\Controllers\FinanceController::class, 'updateSelectedProfit'])->name('update-selected-profit');
        });
    });

    // Jobs - device repair jobs (shop tenant)
    Route::middleware(['shop.tenant'])->group(function () {
        Route::get('/jobs-list', [\App\Http\Controllers\JobController::class, 'list'])->name('jobs.list');
        Route::resource('/jobs', \App\Http\Controllers\JobController::class);
        // Job receipt JSON endpoint (used by client-side modal/print)
        Route::get('/jobs/{job}/receipt', [\App\Http\Controllers\JobController::class, 'showReceipt'])->name('jobs.receipt');
        // Job sheet PDF download
        Route::get('/jobs/{job}/pdf-job-sheet', [\App\Http\Controllers\JobController::class, 'downloadPdfJobSheet'])->name('jobs.pdf-job-sheet');
        // Job Types management (user-manageable dropdown for Jobs)
        Route::resource('/job-types', \App\Http\Controllers\JobTypeController::class);
    });

    // Payment routes
    Route::post('/payment/modal', [\App\Http\Controllers\PaymentController::class, 'modal'])->name('payment.modal');



    // Route Orders - Simplified
    Route::middleware(['shop.tenant'])->group(function () {
        Route::get('/sales', function () {
            return redirect()->to(shop_route('sales.index'));
        });
        Route::get('/orders', function () {
            /** @var User|null $user */
            $user = auth()->user();
            $activeShop = $user ? $user->getActiveShop() : null;
            $shopType = $activeShop && $activeShop->shop_type
                ? shop_type_route_key($activeShop->shop_type->value)
                : null;

            if ($shopType) {
                $shopOrdersRoute = "{$shopType}.orders.index";
                if (Route::has($shopOrdersRoute)) {
                    return redirect()->route($shopOrdersRoute);
                }
            }

            return app(OrderController::class)->index(request());
        })->name('orders.index');
        Route::get('/pos', function () {
            /** @var User|null $user */
            $user = auth()->user();
            $activeShop = $user ? $user->getActiveShop() : null;
            $shopType = $activeShop && $activeShop->shop_type
                ? shop_type_route_key($activeShop->shop_type->value)
                : null;

            if ($shopType) {
                $shopPosRoute = "{$shopType}.pos.index";
                if (Route::has($shopPosRoute)) {
                    return redirect()->route($shopPosRoute);
                }
            }

            $shopPosControllers = [
                'tech' => 'App\\ShopTypes\\Tech\\Controllers\\POSController',
            ];

            if ($shopType && isset($shopPosControllers[$shopType])) {
                $controller = $shopPosControllers[$shopType];
                if (class_exists($controller)) {
                    return app($controller)->index();
                }
            }

            return app(OrderController::class)->create();
        })->name('orders.create');
        Route::get('/orders/create', function () {
            return redirect()->route('orders.create');
        });
        Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{orderId}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/download-pdf-bill', [OrderController::class, 'downloadPdfBill'])->name('orders.download-pdf-bill');
        Route::get('/orders/{orderId}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/update/{orderId}', [OrderController::class, 'update'])->name('orders.update');
        Route::post('/orders/items/{item}/update', [OrderController::class, 'updateOrderItem'])->name('orders.items.update');

        // Warranty Claims Routes
        Route::resource('warranty-claims', \App\Http\Controllers\WarrantyClaimController::class);
        Route::get('/warranty-claims/search/products', [\App\Http\Controllers\WarrantyClaimController::class, 'searchProducts'])->name('warranty-claims.search.products');
        Route::get('/warranty-claims/search/customers', [\App\Http\Controllers\WarrantyClaimController::class, 'searchCustomers'])->name('warranty-claims.search.customers');
        Route::get('/warranty-claims/products/{productId}/serial-numbers', [\App\Http\Controllers\WarrantyClaimController::class, 'getProductSerialNumbers'])->name('warranty-claims.product.serial-numbers');

        // Warranty Management Routes (Shop Owners & Managers only)
        Route::resource('warranties', \App\Http\Controllers\WarrantyController::class);

        // Order Import Routes - for migrating data from other systems
        Route::get('/manual', [\App\Http\Controllers\Order\OrderImportController::class, 'manualForm'])->name('orders.import.manual');
        Route::post('/manual', [\App\Http\Controllers\Order\OrderImportController::class, 'storeManual'])->name('orders.import.store-manual');
        Route::get('/orders/import/bulk', [\App\Http\Controllers\Order\OrderImportController::class, 'bulkForm'])->name('orders.import.bulk');
        Route::post('/orders/import/bulk', [\App\Http\Controllers\Order\OrderImportController::class, 'processBulk'])->name('orders.import.process-bulk');
        Route::get('/orders/import/template', [\App\Http\Controllers\Order\OrderImportController::class, 'downloadTemplate'])->name('orders.import.download-template');

        // API routes for real-time sync
        Route::get('/orders/api/products', [OrderController::class, 'getProducts'])->name('orders.products');
        Route::get('/orders/api/customers', [OrderController::class, 'getCustomers'])->name('orders.customers');

        Route::get('/orders/{order}/receipt', [OrderController::class, 'showReceipt'])->name('orders.receipt');
    });

    // Returns and Expenses
    Route::middleware(['role:finance_access', 'shop.tenant'])->group(function () {
        // Return sale routes
        Route::get('/returns', [\App\Http\Controllers\ReturnSaleController::class, 'index'])->name('returns.index');
        Route::get('/returns/create', [\App\Http\Controllers\ReturnSaleController::class, 'create'])->name('returns.create');
        Route::post('/returns/store', [\App\Http\Controllers\ReturnSaleController::class, 'store'])->name('returns.store');
        Route::get('/returns/{returnSale}', [\App\Http\Controllers\ReturnSaleController::class, 'show'])->name('returns.show');
        Route::get('/returns/{returnSale}/edit', [\App\Http\Controllers\ReturnSaleController::class, 'edit'])->name('returns.edit');
        Route::put('/returns/{returnSale}', [\App\Http\Controllers\ReturnSaleController::class, 'update'])->name('returns.update');
        Route::delete('/returns/{returnSale}', [\App\Http\Controllers\ReturnSaleController::class, 'destroy'])->name('returns.destroy');

        // Record expenses
        Route::get('/expenses', [\App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('/expenses/store', [\App\Http\Controllers\ExpenseController::class, 'store'])->name('expenses.store');
        Route::get('/expenses/create', [\App\Http\Controllers\ExpenseController::class, 'create'])->name('expenses.create');
        Route::get('/expenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'show'])->name('expenses.show');
        Route::get('/expenses/{expense}/edit', [\App\Http\Controllers\ExpenseController::class, 'edit'])->name('expenses.edit');
        Route::put('/expenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('/expenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'destroy'])->name('expenses.destroy');
    });

    // Delivery Management
    Route::middleware(['shop.tenant'])->group(function () {
        Route::get('/deliveries', [\App\Http\Controllers\DeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/deliveries/create', [\App\Http\Controllers\DeliveryController::class, 'create'])->name('deliveries.create');
        Route::post('/deliveries', [\App\Http\Controllers\DeliveryController::class, 'store'])->name('deliveries.store');
        Route::get('/deliveries/{delivery}/edit', [\App\Http\Controllers\DeliveryController::class, 'edit'])->name('deliveries.edit');
        Route::put('/deliveries/{delivery}', [\App\Http\Controllers\DeliveryController::class, 'update'])->name('deliveries.update');
        Route::delete('/deliveries/{delivery}', [\App\Http\Controllers\DeliveryController::class, 'destroy'])->name('deliveries.destroy');
    });

    // Credit Sales Routes
    Route::get('/credit-sales', [\App\Http\Controllers\CreditSaleController::class, 'index'])->name('credit-sales.index');
    Route::get('/credit-sales/{creditSale}', [\App\Http\Controllers\CreditSaleController::class, 'show'])->name('credit-sales.show');
    Route::get('/credit-sales/{creditSale}/download-pdf', [\App\Http\Controllers\CreditSaleController::class, 'downloadPdf'])->name('credit-sales.download-pdf');
    Route::post('/credit-sales/{creditSale}/payment', [\App\Http\Controllers\CreditSaleController::class, 'makePayment'])->name('credit-sales.payment');
    Route::get('/credit-sales/overdue/report', [\App\Http\Controllers\CreditSaleController::class, 'overdueReport'])->name('credit-sales.overdue');
    Route::get('/customers/{customer}/credit-history', [\App\Http\Controllers\CreditSaleController::class, 'customerCreditHistory'])->name('customers.credit-history');

    // Sales Reports Routes (Manager and Shop Owner only)
    Route::middleware(['role:reports_access', 'shop.tenant'])->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            // Main reports index - DISABLED: Use shop-type specific routes instead.
            // Route::get('/', [App\Http\Controllers\SalesReportController::class, 'reportsIndex'])->name('index');

            // Monthly Business Report (Comprehensive)
            Route::prefix('business')->name('business.')->group(function () {
                Route::get('/monthly', [App\Http\Controllers\MonthlyBusinessReportController::class, 'index'])->name('monthly');
                Route::get('/compare', [App\Http\Controllers\MonthlyBusinessReportController::class, 'compareShops'])->name('compare');
            });

            // Business Transactions Report
            Route::get('/transactions', [App\Http\Controllers\SalesReportController::class, 'transactions'])->name('transactions');
            Route::get('/transactions/download', [App\Http\Controllers\SalesReportController::class, 'downloadTransactions'])->name('transactions.download');

            // Inventory Report
            Route::get('/inventory', [App\Http\Controllers\SalesReportController::class, 'inventory'])->name('inventory');
            Route::get('/inventory/download', [App\Http\Controllers\SalesReportController::class, 'downloadInventory'])->name('inventory.download');

            // External Funds Management
            Route::prefix('external-funds')->name('external-funds.')->group(function () {
                Route::get('/', [App\Http\Controllers\ExternalFundController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\ExternalFundController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\ExternalFundController::class, 'store'])->name('store');
                Route::get('/report', [App\Http\Controllers\ExternalFundController::class, 'report'])->name('report');
                Route::get('/{externalFund}', [App\Http\Controllers\ExternalFundController::class, 'show'])->name('show');
                Route::get('/{externalFund}/edit', [App\Http\Controllers\ExternalFundController::class, 'edit'])->name('edit');
                Route::put('/{externalFund}', [App\Http\Controllers\ExternalFundController::class, 'update'])->name('update');
                Route::delete('/{externalFund}', [App\Http\Controllers\ExternalFundController::class, 'destroy'])->name('destroy');

                // Repayment routes
                Route::post('/{externalFund}/repayments', [App\Http\Controllers\ExternalFundController::class, 'addRepayment'])->name('repayments.add');
                Route::delete('/repayments/{repayment}', [App\Http\Controllers\ExternalFundController::class, 'deleteRepayment'])->name('repayments.delete');
            });

            Route::prefix('sales')->name('sales.')->group(function () {
                Route::get('/', [App\Http\Controllers\SalesReportController::class, 'index'])->name('index');
                Route::get('/daily', [App\Http\Controllers\SalesReportController::class, 'daily'])->name('daily');
                Route::get('/daily/download', [App\Http\Controllers\SalesReportController::class, 'downloadDaily'])->name('daily.download');
                Route::get('/weekly', [App\Http\Controllers\SalesReportController::class, 'weekly'])->name('weekly');
                Route::get('/weekly/download', [App\Http\Controllers\SalesReportController::class, 'downloadWeekly'])->name('weekly.download');
                Route::get('/monthly', [App\Http\Controllers\SalesReportController::class, 'monthly'])->name('monthly');
                Route::get('/monthly/download', [App\Http\Controllers\SalesReportController::class, 'downloadMonthly'])->name('monthly.download');
                Route::get('/yearly', [App\Http\Controllers\SalesReportController::class, 'yearly'])->name('yearly');

                // API endpoints for chart data
                Route::get('/api/daily-data', [App\Http\Controllers\SalesReportController::class, 'getDailySalesData'])->name('api.daily');
                Route::get('/api/weekly-data', [App\Http\Controllers\SalesReportController::class, 'getWeeklySalesData'])->name('api.weekly');
                Route::get('/api/monthly-data', [App\Http\Controllers\SalesReportController::class, 'getMonthlySalesData'])->name('api.monthly');
                Route::get('/api/yearly-data', [App\Http\Controllers\SalesReportController::class, 'getYearlySalesData'])->name('api.yearly');

                // Finance reports: returns and expenses (read-only views & procs)
                Route::prefix('finance')->name('finance.')->group(function () {
                    Route::get('/returns', [\App\Http\Controllers\FinanceReportController::class, 'returnsIndex'])->name('returns');
                    Route::get('/returns/api', [\App\Http\Controllers\FinanceReportController::class, 'returnsApi'])->name('returns.api');

                    Route::get('/expenses', [\App\Http\Controllers\FinanceReportController::class, 'expensesIndex'])->name('expenses');
                    Route::get('/expenses/api', [\App\Http\Controllers\FinanceReportController::class, 'expensesApi'])->name('expenses.api');

                    // Credit sales and related reports
                    Route::get('/credit-sales', [\App\Http\Controllers\FinanceReportController::class, 'creditSalesIndex'])->name('credit-sales');
                    Route::get('/credit-sales/api', [\App\Http\Controllers\FinanceReportController::class, 'creditSalesApi'])->name('credit-sales.api');
                });
            });
        });
    });

    // Shop Management Routes - all moved to admin namespace

    // Letterhead Configuration Routes
    Route::get('/letterhead', [App\Http\Controllers\LetterheadController::class, 'index'])->name('letterhead.index');
    Route::post('/letterhead/upload', [App\Http\Controllers\LetterheadController::class, 'uploadLetterhead'])->name('letterhead.upload');
    Route::post('/letterhead/save-positions', [App\Http\Controllers\LetterheadController::class, 'savePositions'])->name('letterhead.save-positions');
    Route::get('/letterhead/positions', [App\Http\Controllers\LetterheadController::class, 'getPositions'])->name('letterhead.get-positions');
    Route::post('/letterhead/save-toggles', [App\Http\Controllers\LetterheadController::class, 'saveToggles'])->name('letterhead.save-toggles');
    Route::get('/letterhead/toggles', [App\Http\Controllers\LetterheadController::class, 'getToggles'])->name('letterhead.get-toggles');
    Route::post('/letterhead/save-items-alignment', [App\Http\Controllers\LetterheadController::class, 'saveItemsAlignment'])->name('letterhead.save-items-alignment');
    Route::post('/letterhead/save-table-width', [App\Http\Controllers\LetterheadController::class, 'saveTableWidth'])->name('letterhead.save-table-width');
    Route::post('/letterhead/regenerate-preview', [App\Http\Controllers\LetterheadController::class, 'regeneratePreview'])->name('letterhead.regenerate-preview');
    Route::post('/letterhead/save-sales-config', [App\Http\Controllers\LetterheadController::class, 'saveSalesConfig'])->name('letterhead.save-sales-config');

    // Lightweight position preview & offset saver (uses OrderController helpers)
    Route::get('/letterhead/position-preview', [OrderController::class, 'positionPreview'])->name('letterhead.position_preview');
    Route::post('/letterhead/save-offset', [OrderController::class, 'saveLetterheadMergeOffset'])->name('letterhead.save_offset');

    // Barcode Configuration Routes
    Route::get('/barcode', [App\Http\Controllers\BarcodeController::class, 'index'])->name('barcode.index');
    Route::post('/barcode/settings', [App\Http\Controllers\BarcodeController::class, 'updateSettings'])->name('barcode.settings.update');
    Route::post('/barcode/preview', [App\Http\Controllers\BarcodeController::class, 'preview'])->name('barcode.preview');
    Route::get('/barcode/test-print', [App\Http\Controllers\BarcodeController::class, 'testPrint'])->name('barcode.test.print');
    Route::get('/barcode/print/bulk', [App\Http\Controllers\BarcodeController::class, 'printBulk'])->name('barcode.print.bulk');
    Route::get('/barcode/print/product/{product}', [App\Http\Controllers\BarcodeController::class, 'printProduct'])->name('barcode.print.product');

    // Admin Routes (Administrator only)
    Route::middleware(['admin'])->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');

        // Database Backup Routes
        Route::get('/backups', [App\Http\Controllers\DatabaseBackupController::class, 'index'])->name('backups.index');
        Route::post('/backups/create', [App\Http\Controllers\DatabaseBackupController::class, 'create'])->name('backups.create');
        Route::get('/backups/download', [App\Http\Controllers\DatabaseBackupController::class, 'download'])->name('backups.download');
        Route::get('/backups/{filename}/download', [App\Http\Controllers\DatabaseBackupController::class, 'downloadExisting'])->name('backups.download-existing');
        Route::delete('/backups/{filename}', [App\Http\Controllers\DatabaseBackupController::class, 'delete'])->name('backups.delete');

        // Admin Shop Management
        Route::get('/shops/create', [App\Http\Controllers\AdminController::class, 'createShop'])->name('shops.create');
        Route::post('/shops', [App\Http\Controllers\AdminController::class, 'storeShop'])->name('shops.store');
        Route::get('/shops', [App\Http\Controllers\AdminController::class, 'shops'])->name('shops.index');
        Route::get('/shops/subscriptions', [App\Http\Controllers\AdminController::class, 'subscriptions'])->name('shops.subscriptions');
        Route::post('/shops/{shop}/extend-subscription', [App\Http\Controllers\AdminController::class, 'extendSubscription'])->name('shops.extend-subscription');
        Route::post('/shops/{shop}/change-status', [App\Http\Controllers\AdminController::class, 'changeSubscriptionStatus'])->name('shops.change-status');
        Route::get('/shops/suspended', [App\Http\Controllers\AdminController::class, 'suspendedShops'])->name('shops.suspended');
        Route::post('/shops/{shop}/unsuspend', [App\Http\Controllers\AdminController::class, 'unsuspendShop'])->name('shops.unsuspend');

        // Shop deletion routes (must come BEFORE the generic {shop} routes to avoid conflicts)
        Route::get('/shops/{shop}/delete', [App\Http\Controllers\AdminController::class, 'deleteShopPage'])->name('shops.delete.page');
        Route::delete('/shops/{shop}', [App\Http\Controllers\AdminController::class, 'deleteShop'])->name('shops.delete');

        // Shop viewing and editing routes
        Route::get('/shops/{shop}', [App\Http\Controllers\AdminController::class, 'showShop'])->name('shops.show');
        Route::get('/shops/{shop}/edit', [App\Http\Controllers\AdminController::class, 'editShop'])->name('shops.edit');
        Route::put('/shops/{shop}', [App\Http\Controllers\AdminController::class, 'updateShop'])->name('shops.update');
        Route::get('/shops/{shop}/suspend', [App\Http\Controllers\AdminController::class, 'suspendShopPage'])->name('shops.suspend');
        Route::post('/shops/{shop}/suspend', [App\Http\Controllers\AdminController::class, 'suspendShop'])->name('shops.suspend.store');
        Route::post('/shops/{shop}/reactivate', [App\Http\Controllers\AdminController::class, 'reactivateShop'])->name('shops.reactivate');
        Route::post('/shops/{shop}/toggle-status', [App\Http\Controllers\AdminController::class, 'toggleShopStatus'])->name('shops.toggle-status');
        Route::get('/shops/{shop}/users', [App\Http\Controllers\AdminController::class, 'getShopUsers'])->name('shops.users');
        Route::post('/shops/{shop}/assign-user', [App\Http\Controllers\AdminController::class, 'assignUserToShop'])->name('shops.assign-user');
        Route::delete('/shops/{shop}/remove-user/{user}', [App\Http\Controllers\AdminController::class, 'removeUserFromShop'])->name('shops.remove-user');
        Route::get('/available-users', [App\Http\Controllers\AdminController::class, 'getAvailableUsers'])->name('users.available');        // Admin User Management
        Route::get('/users/create', [App\Http\Controllers\AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [App\Http\Controllers\AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users.index');
        Route::get('/users/suspended', [App\Http\Controllers\AdminController::class, 'suspendedUsers'])->name('users.suspended');
        Route::get('/users/{user}', [App\Http\Controllers\AdminController::class, 'showUser'])->name('users.show');
        Route::get('/users/{user}/edit', [App\Http\Controllers\AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [App\Http\Controllers\AdminController::class, 'updateUser'])->name('users.update');
        Route::get('/users/{user}/assign-shop', [App\Http\Controllers\AdminController::class, 'assignShopPage'])->name('users.assign-shop');
        Route::post('/users/{user}/assign-shop', [App\Http\Controllers\AdminController::class, 'updateUserShopAssignment'])->name('users.update-shop-assignment');
        Route::post('/users/{user}/toggle-access', [App\Http\Controllers\AdminController::class, 'toggleUserAccess'])->name('users.toggle-access');
        Route::post('/users/{user}/verify-email', [App\Http\Controllers\AdminController::class, 'verifyUserEmail'])->name('users.verify-email');
        Route::post('/users/{user}/unverify-email', [App\Http\Controllers\AdminController::class, 'unverifyUserEmail'])->name('users.unverify-email');
        Route::post('/users/{user}/send-password-reset', [App\Http\Controllers\AdminController::class, 'sendPasswordResetToUser'])->name('users.send-password-reset');
        Route::get('/users/{user}/suspend', [App\Http\Controllers\AdminController::class, 'suspendUserPage'])->name('users.suspend');
        Route::post('/users/{user}/suspend', [App\Http\Controllers\AdminController::class, 'suspendUser'])->name('users.suspend.store');
        Route::post('/users/{user}/unsuspend', [App\Http\Controllers\AdminController::class, 'unsuspendUser'])->name('users.unsuspend');
        // User deletion disabled - users should be suspended instead
        // Route::get('/users/{user}/delete', [App\Http\Controllers\AdminController::class, 'deleteUserPage'])->name('users.delete.page');
        // Route::delete('/users/{user}', [App\Http\Controllers\AdminController::class, 'deleteUser'])->name('users.delete');

        // Admin Reports
        Route::get('/reports/users', [App\Http\Controllers\AdminController::class, 'userReports'])->name('reports.users');
        Route::get('/reports/shops', [App\Http\Controllers\AdminController::class, 'shopReports'])->name('reports.shops');
        Route::get('/reports/logs', [App\Http\Controllers\AdminController::class, 'systemLogs'])->name('reports.logs');
        Route::get('/reports/logs/{log}', [App\Http\Controllers\AdminController::class, 'showSystemLog'])->name('reports.logs.show');
    });

});

    // Notifications
    Route::post('/notifications/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-read');
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/audit-logs', [\App\Http\Controllers\NotificationController::class, 'auditLogs'])->name('notifications.audit-logs');

require __DIR__.'/auth.php';
