<?php

namespace App\ShopTypes\Tech\Models;

use App\Models\Shop;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class TechSerialNumber extends Model
{
    use HasFactory;

    protected $table = 'tech_serial_numbers';
    protected static ?string $resolvedTable = null;

    protected $fillable = [
        'shop_id',
        'product_id',
        'serial_number',
        'status',
        'order_id',
        'order_detail_id',
        'purchase_date',
        'warranty_expires_at',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expires_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(TechProduct::class, 'product_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopeInStock($query)
    {
        return $query->where('status', 'in_stock');
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }

    public function scopeForShop($query, $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeWarrantyActive($query)
    {
        return $query->where('warranty_expires_at', '>', now());
    }

    public function scopeWarrantyExpired($query)
    {
        return $query->where('warranty_expires_at', '<=', now());
    }

    // Helpers
    public function isInStock(): bool
    {
        return $this->status === 'in_stock';
    }

    public function isSold(): bool
    {
        return $this->status === 'sold';
    }

    public function isUnderRepair(): bool
    {
        return $this->status === 'under_repair';
    }

    public function hasActiveWarranty(): bool
    {
        return $this->warranty_expires_at && $this->warranty_expires_at->isFuture();
    }

    public function getWarrantyStatus(): string
    {
        if (!$this->warranty_expires_at) {
            return 'No Warranty';
        }

        if ($this->hasActiveWarranty()) {
            $daysLeft = now()->diffInDays($this->warranty_expires_at);
            return "Active ({$daysLeft} days left)";
        }

        return 'Expired';
    }

    public function markAsSold(int $orderId, ?int $orderDetailId = null): void
    {
        $this->update([
            'status' => 'sold',
            'order_id' => $orderId,
            'order_detail_id' => $orderDetailId,
            'purchase_date' => now(),
        ]);

        // Calculate warranty expiry if product has warranty
        if ($this->product && $this->product->warranty_months > 0) {
            $this->update([
                'warranty_expires_at' => now()->addMonths($this->product->warranty_months),
            ]);
        }
    }

    public function markAsReturned(): void
    {
        $this->update([
            'status' => 'returned',
            'order_id' => null,
            'order_detail_id' => null,
        ]);
    }

    public function getTable(): string
    {
        if (self::$resolvedTable !== null) {
            return self::$resolvedTable;
        }

        if (Schema::hasTable('serial_numbers')) {
            return self::$resolvedTable = 'serial_numbers';
        }

        return self::$resolvedTable = parent::getTable();
    }
}
