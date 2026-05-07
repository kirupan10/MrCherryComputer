<?php

namespace App\Providers;

use Illuminate\Http\Request;
use App\Breadcrumbs\Breadcrumbs;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\Debugbar\Facades\Debugbar;
use App\Services\KpiService;
use App\Models\User;
use Illuminate\View\View as ViewContract;
use App\Models\Order;
use App\Models\CreditSale;
use App\Observers\OrderObserver;
use App\Observers\CreditSaleObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerShopTypeControllerBindings();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Model Observers to sync Order and CreditSale
        Order::observe(OrderObserver::class);
        CreditSale::observe(CreditSaleObserver::class);

        Paginator::useBootstrapFive();

        // Control Debugbar visibility based on user
        $this->configureDebugbar();

        Request::macro('breadcrumbs', fn () => new Breadcrumbs(request()));
        // Load global helpers
        if (file_exists(app_path('helpers.php'))) {
            require_once app_path('helpers.php');
        }

        // Register custom Blade directives for shop features
        $this->registerShopFeatureBladeDirectives();

        // Provide cached finance KPIs to the navbar via view composer
        View::composer('layouts.body.navbar', function (ViewContract $view): void {
            $shopId = null;
            $user = Auth::user();
            if ($user instanceof User) {
                $user->loadMissing('shop');

                if ($user->shop) {
                    $shopId = $user->shop->id;
                } elseif (isset($user->shop_id)) {
                    $shopId = $user->shop_id;
                }
            }

            $ttlSeconds = intval(env('NAV_KPI_CACHE_TTL', 30));
            $cacheKey = 'nav_finance_kpis_shop_' . ($shopId ?? 'global');
            $payload = Cache::remember($cacheKey, now()->addSeconds($ttlSeconds), function () use ($shopId): array {
                try {
                    $svc = app(KpiService::class);
                    $returnKpi = $shopId ? $svc->getReturnKpisByShop($shopId) : (object) ['items_returned' => 0, 'total_returns' => 0, 'last_30_days_total' => 0];
                    $expenseKpi = $shopId ? $svc->getExpenseKpisByShop($shopId) : (object) ['total_expenses' => 0, 'last_30_days_expenses' => 0, 'types_count' => 0];

                    // lightweight credit KPIs: total credit amount and total due for the shop (in cents)
                    $creditKpi = (object) ['total_credit' => 0, 'total_due' => 0, 'sales_count' => 0];
                    try {
                        if ($shopId) {
                            $c = DB::table('credit_sales as cs')
                                ->join('orders as o', 'cs.order_id', '=', 'o.id')
                                ->where('o.shop_id', $shopId)
                                ->selectRaw('COALESCE(SUM(cs.total_amount),0) AS total_credit, COALESCE(SUM(cs.due_amount),0) AS total_due, COALESCE(COUNT(*),0) AS sales_count')
                                ->first();
                            if ($c) {
                                $creditKpi = $c;
                            }
                        }
                    } catch (\Exception $e) {
                        // ignore errors reading the table
                    }

                    return ['returnKpi' => $returnKpi, 'expenseKpi' => $expenseKpi, 'creditKpi' => $creditKpi];
                } catch (\Exception $e) {
                    return ['returnKpi' => (object) ['items_returned' => 0], 'expenseKpi' => (object) ['total_expenses' => 0], 'creditKpi' => (object) ['total_credit' => 0, 'total_due' => 0, 'sales_count' => 0]];
                }
            });

            $view->with('navFinanceKpis', $payload);
        });
    }

    /**
     * Configure debugbar based on authenticated user
     */
    protected function configureDebugbar(): void
    {
        // Only enable debugbar for specific users (development/admin purposes)
        if (class_exists(Debugbar::class)) {
            $allowedEmails = [
                'ikirupan@gmail.com', // Development account
            ];

            // If user is authenticated, check if they should see debugbar
            if (auth()->check()) {
                $user = auth()->user();

                // Only show debugbar to super admins with allowed emails
                if (!$user instanceof User || !$user->isAdmin() || !in_array($user->email, $allowedEmails, true)) {
                    Debugbar::disable();
                }
            } else {
                // Disable for unauthenticated users
                Debugbar::disable();
            }
        }
    }

    /**
     * Register custom Blade directives for shop features
     */
    protected function registerShopFeatureBladeDirectives(): void
    {
        // @shopFeature('feature_name') ... @endshopFeature
        Blade::if('shopFeature', function ($feature) {
            $user = Auth::user();
            if (!$user instanceof User || !$user->shop_id) {
                return false;
            }

            $shop = \App\Models\Shop::find($user->shop_id);
            return $shop && $shop->hasFeature($feature);
        });

        // @shopType('tech_shop') ... @endshopType
        Blade::if('shopType', function ($type) {
            $user = Auth::user();
            if (!$user instanceof User || !$user->shop_id) {
                return false;
            }

            $shop = \App\Models\Shop::find($user->shop_id);
            return $shop && $shop->shop_type->value === $type;
        });

        // @shopHasAnyFeature(['feature1', 'feature2']) ... @endshopHasAnyFeature
        Blade::if('shopHasAnyFeature', function ($features) {
            $user = Auth::user();
            if (!$user instanceof User || !$user->shop_id) {
                return false;
            }

            $shop = \App\Models\Shop::find($user->shop_id);
            if (!$shop) {
                return false;
            }

            foreach ($features as $feature) {
                if ($shop->hasFeature($feature)) {
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * Resolve core controllers from shop-type namespaces when available.
     */
    protected function registerShopTypeControllerBindings(): void
    {
        $shopNamespaces = [
            'tech' => 'Tech',
        ];

        $controllerMap = [
            \App\Http\Controllers\BarcodeController::class => 'BarcodeController',
            \App\Http\Controllers\BusinessTransactionController::class => 'BusinessTransactionController',
            \App\Http\Controllers\CategoryController::class => 'CategoryController',
            \App\Http\Controllers\ChequeController::class => 'ChequeController',
            \App\Http\Controllers\CreditPurchaseController::class => 'CreditPurchaseController',
            \App\Http\Controllers\CreditSaleController::class => 'CreditSaleController',
            \App\Http\Controllers\CustomerController::class => 'CustomerController',
            \App\Http\Controllers\DatabaseBackupController::class => 'DatabaseBackupController',
            \App\Http\Controllers\DeliveryController::class => 'DeliveryController',
            \App\Http\Controllers\ExpenseController::class => 'ExpenseController',
            \App\Http\Controllers\ExternalFundController::class => 'ExternalFundController',
            \App\Http\Controllers\FinanceController::class => 'FinanceController',
            \App\Http\Controllers\FinanceReportController::class => 'FinanceReportController',
            \App\Http\Controllers\JobController::class => 'JobController',
            \App\Http\Controllers\JobTypeController::class => 'JobTypeController',
            \App\Http\Controllers\LetterheadController::class => 'LetterheadController',
            \App\Http\Controllers\LogController::class => 'LogController',
            \App\Http\Controllers\MonthlyBusinessReportController::class => 'MonthlyBusinessReportController',
            \App\Http\Controllers\NotificationController::class => 'NotificationController',
            \App\Http\Controllers\PaymentController::class => 'PaymentController',
            \App\Http\Controllers\ProfileController::class => 'ProfileController',
            \App\Http\Controllers\PurchaseController::class => 'PurchaseController',
            \App\Http\Controllers\ReturnSaleController::class => 'ReturnSaleController',
            \App\Http\Controllers\SalesReportController::class => 'SalesReportController',
            \App\Http\Controllers\ShopSelectionController::class => 'ShopSelectionController',
            \App\Http\Controllers\UnitController::class => 'UnitController',
            \App\Http\Controllers\UserController::class => 'UserController',
            \App\Http\Controllers\VendorController::class => 'VendorController',
            \App\Http\Controllers\WarrantyClaimController::class => 'WarrantyClaimController',
            \App\Http\Controllers\WarrantyController::class => 'WarrantyController',
            \App\Http\Controllers\Product\ProductController::class => 'ProductController',
            \App\Http\Controllers\Order\OrderController::class => 'OrderController',
        ];

        foreach ($controllerMap as $abstract => $shopControllerName) {
            $this->app->bind($abstract, function ($app) use ($abstract, $shopControllerName, $shopNamespaces) {
                $shopType = null;
                $user = auth()->user();

                if ($user instanceof User) {
                    $shop = $user->getActiveShop();
                    $shopType = $shop && $shop->shop_type ? $shop->shop_type->value : null;
                }

                if ($shopType === 'tech_shop') {
                    $shopType = 'tech';
                }

                $shopNamespace = $shopNamespaces[$shopType] ?? null;

                if ($shopNamespace) {
                    $shopClass = "App\\ShopTypes\\{$shopNamespace}\\Controllers\\{$shopControllerName}";

                    if (class_exists($shopClass)) {
                        return $app->build($shopClass);
                    }
                }

                return $app->build($abstract);
            });
        }
    }
}
