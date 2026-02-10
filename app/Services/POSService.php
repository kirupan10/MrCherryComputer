<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\StockLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class POSService
{
    /**
     * Calculate cart totals
     */
    public function calculateCartTotals(array $items): array
    {
        $subtotal = 0;
        $taxAmount = 0;
        
        foreach ($items as $item) {
            $itemSubtotal = $item['price'] * $item['quantity'];
            $itemTax = $itemSubtotal * ($item['tax_percentage'] ?? 0) / 100;
            
            $subtotal += $itemSubtotal;
            $taxAmount += $itemTax;
        }
        
        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'total_amount' => round($subtotal + $taxAmount, 2),
        ];
    }

    /**
     * Validate stock availability
     */
    public function validateStockAvailability(array $items): array
    {
        $errors = [];
        
        foreach ($items as $item) {
            $product = Product::with('stock')->find($item['product_id']);
            
            if (!$product) {
                $errors[] = "Product not found: ID {$item['product_id']}";
                continue;
            }
            
            $stock = $product->stock;
            
            if (!$stock || $stock->quantity < $item['quantity']) {
                $available = $stock ? $stock->quantity : 0;
                $errors[] = "Insufficient stock for {$product->name}. Available: {$available}, Requested: {$item['quantity']}";
            }
        }
        
        return $errors;
    }

    /**
     * Process a sale transaction
     */
    public function processSale(array $saleData): Sale
    {
        return DB::transaction(function () use ($saleData) {
            // Validate stock
            $stockErrors = $this->validateStockAvailability($saleData['items']);
            
            if (!empty($stockErrors)) {
                throw new \Exception(implode(', ', $stockErrors));
            }

            // Create sale
            $sale = Sale::create([
                'customer_id' => $saleData['customer_id'] ?? null,
                'subtotal' => $saleData['subtotal'],
                'tax_amount' => $saleData['tax_amount'],
                'discount_amount' => $saleData['discount_amount'] ?? 0,
                'total_amount' => $saleData['total_amount'],
                'status' => 'completed',
                'notes' => $saleData['notes'] ?? null,
                'sold_by' => Auth::id(),
            ]);

            // Create sale items and update stock
            foreach ($saleData['items'] as $item) {
                $this->addSaleItem($sale, $item);
                $this->updateStock($item['product_id'], $item['quantity'], 'out', $sale);
            }

            // Create payment
            $this->recordPayment($sale, $saleData);

            return $sale->load(['customer', 'items.product', 'payments']);
        });
    }

    /**
     * Add a sale item
     */
    private function addSaleItem(Sale $sale, array $itemData): SaleItem
    {
        return SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $itemData['product_id'],
            'quantity' => $itemData['quantity'],
            'price' => $itemData['price'],
            'tax_amount' => $itemData['tax_amount'] ?? 0,
            'total' => ($itemData['price'] * $itemData['quantity']) + ($itemData['tax_amount'] ?? 0),
        ]);
    }

    /**
     * Update product stock
     */
    private function updateStock(int $productId, float $quantity, string $type, Sale $sale): void
    {
        $product = Product::findOrFail($productId);
        
        $stock = $product->stock ?? Stock::create([
            'product_id' => $productId,
            'quantity' => 0,
            'last_updated_by' => Auth::id(),
        ]);

        $previousQuantity = $stock->quantity;
        
        if ($type === 'out') {
            $newQuantity = $previousQuantity - $quantity;
        } else {
            $newQuantity = $previousQuantity + $quantity;
        }

        $stock->update([
            'quantity' => max(0, $newQuantity),
            'last_updated_by' => Auth::id(),
        ]);

        // Log stock movement
        StockLog::create([
            'product_id' => $productId,
            'type' => $type,
            'quantity' => $quantity,
            'previous_quantity' => $previousQuantity,
            'current_quantity' => $newQuantity,
            'reference_type' => 'sale',
            'reference_id' => $sale->id,
            'notes' => "Sale invoice: {$sale->invoice_number}",
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Record payment
     */
    private function recordPayment(Sale $sale, array $paymentData): Payment
    {
        return Payment::create([
            'sale_id' => $sale->id,
            'payment_method' => $paymentData['payment_method'],
            'amount' => $paymentData['paid_amount'],
            'status' => 'completed',
            'received_by' => Auth::id(),
        ]);
    }

    /**
     * Calculate change amount
     */
    public function calculateChange(float $totalAmount, float $paidAmount): float
    {
        return max(0, round($paidAmount - $totalAmount, 2));
    }

    /**
     * Get low stock products
     */
    public function getLowStockProducts(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::with(['category', 'unit', 'stock'])
            ->lowStock()
            ->where('is_active', true)
            ->get();
    }

    /**
     * Apply discount to cart
     */
    public function applyDiscount(float $subtotal, float $discountAmount = 0, float $discountPercentage = 0): float
    {
        if ($discountAmount > 0) {
            return round($discountAmount, 2);
        }
        
        if ($discountPercentage > 0) {
            return round(($subtotal * $discountPercentage) / 100, 2);
        }
        
        return 0;
    }
}
