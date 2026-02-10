<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Add stock (stock in)
     */
    public function addStock(int $productId, float $quantity, string $notes = null): Stock
    {
        return DB::transaction(function () use ($productId, $quantity, $notes) {
            $product = Product::findOrFail($productId);

            $stock = $product->stock ?? Stock::create([
                'product_id' => $productId,
                'quantity' => 0,
                'last_updated_by' => Auth::id(),
            ]);

            $previousQuantity = $stock->quantity;
            $newQuantity = $previousQuantity + $quantity;

            $stock->update([
                'quantity' => $newQuantity,
                'last_updated_by' => Auth::id(),
            ]);

            $this->logStockMovement(
                $productId,
                'in',
                $quantity,
                $previousQuantity,
                $newQuantity,
                'manual',
                null,
                $notes ?? 'Manual stock addition'
            );

            return $stock;
        });
    }

    /**
     * Remove stock (stock out)
     */
    public function removeStock(int $productId, float $quantity, string $notes = null): Stock
    {
        return DB::transaction(function () use ($productId, $quantity, $notes) {
            $product = Product::findOrFail($productId);

            $stock = $product->stock;

            if (!$stock || $stock->quantity < $quantity) {
                throw new \Exception('Insufficient stock available');
            }

            $previousQuantity = $stock->quantity;
            $newQuantity = max(0, $previousQuantity - $quantity);

            $stock->update([
                'quantity' => $newQuantity,
                'last_updated_by' => Auth::id(),
            ]);

            $this->logStockMovement(
                $productId,
                'out',
                $quantity,
                $previousQuantity,
                $newQuantity,
                'manual',
                null,
                $notes ?? 'Manual stock removal'
            );

            return $stock;
        });
    }

    /**
     * Adjust stock (set to specific quantity)
     */
    public function adjustStock(int $productId, float $newQuantity, string $notes = null): Stock
    {
        return DB::transaction(function () use ($productId, $newQuantity, $notes) {
            $product = Product::findOrFail($productId);

            $stock = $product->stock ?? Stock::create([
                'product_id' => $productId,
                'quantity' => 0,
                'last_updated_by' => Auth::id(),
            ]);

            $previousQuantity = $stock->quantity;
            $difference = abs($newQuantity - $previousQuantity);

            $stock->update([
                'quantity' => $newQuantity,
                'last_updated_by' => Auth::id(),
            ]);

            $this->logStockMovement(
                $productId,
                'adjustment',
                $difference,
                $previousQuantity,
                $newQuantity,
                'adjustment',
                null,
                $notes ?? 'Stock adjustment'
            );

            return $stock;
        });
    }

    /**
     * Transfer stock between products (if needed in future)
     */
    public function transferStock(int $fromProductId, int $toProductId, float $quantity, string $notes = null): array
    {
        return DB::transaction(function () use ($fromProductId, $toProductId, $quantity, $notes) {
            // Remove from source
            $this->removeStock($fromProductId, $quantity, $notes ?? "Transfer to product {$toProductId}");

            // Add to destination
            $this->addStock($toProductId, $quantity, $notes ?? "Transfer from product {$fromProductId}");

            return [
                'from' => Product::with('stock')->find($fromProductId),
                'to' => Product::with('stock')->find($toProductId),
            ];
        });
    }

    /**
     * Get low stock alerts
     */
    public function getLowStockAlerts(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::with(['category', 'unit', 'stock'])
            ->lowStock()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                return [
                    'product' => $product,
                    'current_stock' => $product->stock?->quantity ?? 0,
                    'alert_level' => $product->low_stock_alert,
                    'difference' => ($product->stock?->quantity ?? 0) - $product->low_stock_alert,
                ];
            });
    }

    /**
     * Get stock movement history for a product
     */
    public function getStockHistory(int $productId, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return StockLog::with(['product', 'createdBy'])
            ->where('product_id', $productId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get stock value for a product
     */
    public function getStockValue(int $productId): float
    {
        $product = Product::with('stock')->findOrFail($productId);
        $quantity = $product->stock?->quantity ?? 0;

        return round($quantity * $product->purchase_price, 2);
    }

    /**
     * Get total inventory value
     */
    public function getTotalInventoryValue(): float
    {
        return Product::with('stock')->get()->sum(function ($product) {
            $quantity = $product->stock?->quantity ?? 0;
            return $quantity * $product->purchase_price;
        });
    }

    /**
     * Check stock availability
     */
    public function checkAvailability(int $productId, float $requiredQuantity): bool
    {
        $product = Product::with('stock')->find($productId);

        if (!$product) {
            return false;
        }

        $availableQuantity = $product->stock?->quantity ?? 0;

        return $availableQuantity >= $requiredQuantity;
    }

    /**
     * Log stock movement
     */
    private function logStockMovement(
        int $productId,
        string $type,
        float $quantity,
        float $previousQuantity,
        float $currentQuantity,
        string $referenceType,
        ?int $referenceId = null,
        string $notes = null
    ): StockLog {
        return StockLog::create([
            'product_id' => $productId,
            'type' => $type,
            'quantity' => $quantity,
            'previous_quantity' => $previousQuantity,
            'current_quantity' => $currentQuantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Bulk update stock (for imports or mass updates)
     */
    public function bulkUpdateStock(array $updates): array
    {
        $results = [
            'success' => [],
            'failed' => [],
        ];

        DB::transaction(function () use ($updates, &$results) {
            foreach ($updates as $update) {
                try {
                    $stock = $this->adjustStock(
                        $update['product_id'],
                        $update['quantity'],
                        $update['notes'] ?? 'Bulk stock update'
                    );

                    $results['success'][] = [
                        'product_id' => $update['product_id'],
                        'quantity' => $stock->quantity,
                    ];
                } catch (\Exception $e) {
                    $results['failed'][] = [
                        'product_id' => $update['product_id'],
                        'error' => $e->getMessage(),
                    ];
                }
            }
        });

        return $results;
    }
}
