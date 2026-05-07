<?php

namespace App\Traits;

use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

trait HasShopFeatures
{
    /**
     * Check if the current shop has a specific feature
     */
    public function shopHasFeature(string $feature): bool
    {
        $shop = $this->getCurrentShop();

        if (!$shop) {
            return false;
        }

        return $shop->hasFeature($feature);
    }

    /**
     * Get the current active shop
     */
    protected function getCurrentShop(): ?Shop
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        // Try to get active shop first
        if (method_exists($user, 'getActiveShop')) {
            return $user->getActiveShop();
        }

        // Fallback to user's shop_id
        if ($user->shop_id) {
            return Shop::find($user->shop_id);
        }

        return null;
    }

    /**
     * Get all enabled features for current shop
     */
    public function getCurrentShopFeatures(): array
    {
        $shop = $this->getCurrentShop();

        if (!$shop) {
            return [];
        }

        return $shop->getEnabledFeatures();
    }

    /**
     * Check if current shop has any of the given features
     */
    public function shopHasAnyFeature(array $features): bool
    {
        foreach ($features as $feature) {
            if ($this->shopHasFeature($feature)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if current shop has all of the given features
     */
    public function shopHasAllFeatures(array $features): bool
    {
        foreach ($features as $feature) {
            if (!$this->shopHasFeature($feature)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Abort if shop doesn't have the required feature
     */
    public function requireShopFeature(string $feature, string $message = null): void
    {
        if (!$this->shopHasFeature($feature)) {
            abort(403, $message ?? "This feature is not available for your shop type.");
        }
    }

    /**
     * Abort if shop doesn't have any of the required features
     */
    public function requireAnyShopFeature(array $features, string $message = null): void
    {
        if (!$this->shopHasAnyFeature($features)) {
            abort(403, $message ?? "None of the required features are available for your shop type.");
        }
    }

    /**
     * Get shop-type-aware view name with fallback to tech
     *
     * @param string $viewPath The base view path (e.g., 'products.index', 'orders.create')
     * @param string|null $fallback Optional fallback view if shop-type view doesn't exist
     * @return string The resolved view name
     */
    public function getShopTypeView(string $viewPath, ?string $fallback = null): string
    {
        $shop = $this->getCurrentShop();
        $shopType = $shop && $shop->shop_type ? shop_type_route_key($shop->shop_type->value) : 'tech';

        // Try shop-type-specific view first
        $shopTypeView = "shop-types.{$shopType}.{$viewPath}";
        if (view()->exists($shopTypeView)) {
            return $shopTypeView;
        }

        // Try tech fallback view
        $techView = "shop-types.tech.{$viewPath}";
        if (view()->exists($techView)) {
            return $techView;
        }

        // Return fallback if provided, otherwise return the original path
        return $fallback ?? $viewPath;
    }

    /**
     * Return a shop-type-aware view with data
     *
     * @param string $viewPath The base view path (e.g., 'products.index', 'orders.create')
     * @param array $data Data to pass to the view
     * @param string|null $fallback Optional fallback view if shop-type view doesn't exist
     * @return \Illuminate\View\View
     */
    public function viewForShopType(string $viewPath, array $data = [], ?string $fallback = null)
    {
        $viewName = $this->getShopTypeView($viewPath, $fallback);

        // Add shopType to data for view usage
        $shop = $this->getCurrentShop();
        $data['shopType'] = $shop && $shop->shop_type ? shop_type_route_key($shop->shop_type->value) : 'tech';

        return view($viewName, $data);
    }
}
