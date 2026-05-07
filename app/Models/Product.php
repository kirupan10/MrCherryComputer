<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, BelongsToShop;

    public $fillable = [
        'shop_id',
        'created_by',
        'name',
        'slug',
        'code',
        'barcode',
        'quantity',
        'quantity_alert',
        'buying_price',
        'selling_price',
        'notes',
        'product_image',
        'category_id',
        'unit_id',
        'warranty_id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'quantity'       => 'float',
        'quantity_alert' => 'float',
        'buying_price'   => 'float',
        'selling_price'  => 'float',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /**
     * Boot method to auto-generate SKU/code and barcode when creating products
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->code)) {
                // Generate a shop-scoped SKU so each shop has its own sequence
                $product->code = static::generateSku($product->shop_id);
            }

            if (empty($product->barcode)) {
                // Generate barcode based on the product code
                $product->barcode = static::generateBarcode($product->shop_id, $product->code);
            }
        });

        static::updating(function ($product) {
            // If the product code or shop_id changes, regenerate barcode to keep them in sync
            if ($product->isDirty(['code', 'shop_id'])) {
                $product->barcode = static::generateBarcode($product->shop_id, $product->code);
            }
        });
    }

    /**
     * Generate unique SKU in format PRD00001, PRD00002, etc.
     */
    public static function generateSku($shopId = null): string
    {
        $prefix = 'PRD';

        // Resolve shop id: prefer explicit value, then active shop from auth
        $shopId = $shopId ?? auth()->user()?->getActiveShop()?->id;

        // Find last code for this shop and increment the numeric tail
        $lastCode = static::where('shop_id', $shopId)
            ->orderByDesc('id')
            ->value('code');

        $nextNumber = 1;
        if ($lastCode && preg_match('/^PRD(\d+)/', $lastCode, $matches)) {
            $nextNumber = ((int)$matches[1]) + 1;
        }

        // Format: PRD00001 (5-digit, per shop)
        return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique numeric barcode for products
     * Format: Shop ID (2 digits) + Product Code Number (10 digits) = 12 digits
     * Example: Shop 1, PRD00189 → 010000000189
     */
    public static function generateBarcode($shopId = null, $productCode = null): string
    {
        // Resolve shop ID
        $shopId = $shopId ?? auth()->user()?->getActiveShop()?->id ?? 1;
        $shopPrefix = str_pad($shopId, 2, '0', STR_PAD_LEFT);

        \Log::info("Generate Barcode Called", [
            'shop_id' => $shopId,
            'shop_prefix' => $shopPrefix,
            'product_code' => $productCode,
        ]);

        if ($productCode) {
            // Extract numeric portion from product code (e.g., "PRD00189" -> "00189")
            $numericPortion = preg_replace('/[^0-9]/', '', $productCode);

            \Log::info("Generate Barcode - Extraction", [
                'product_code' => $productCode,
                'numeric_portion' => $numericPortion,
                'padded' => str_pad($numericPortion, 10, '0', STR_PAD_LEFT),
                'final' => $shopPrefix . str_pad($numericPortion, 10, '0', STR_PAD_LEFT),
            ]);

            if ($numericPortion) {
                // Format: Shop ID (2 digits) + Code Number (10 digits)
                return $shopPrefix . str_pad($numericPortion, 10, '0', STR_PAD_LEFT);
            }
        }

        // Fallback: Generate sequential barcode if no product code provided
        \Log::warning("Generate Barcode - Using Fallback (no product code)", ['shop_id' => $shopId]);

        $lastBarcode = static::where('shop_id', $shopId)
            ->whereNotNull('barcode')
            ->orderByDesc('id')
            ->value('barcode');

        $nextNumber = 1;
        if ($lastBarcode) {
            $numericBarcode = preg_replace('/[^0-9]/', '', $lastBarcode);
            if ($numericBarcode) {
                // Extract last 10 digits as product number
                $nextNumber = ((int)substr($numericBarcode, -10)) + 1;
            }
        }

        \Log::info("Generate Barcode - Fallback Result", [
            'last_barcode' => $lastBarcode,
            'next_number' => $nextNumber,
            'final' => $shopPrefix . str_pad($nextNumber, 10, '0', STR_PAD_LEFT),
        ]);

        return $shopPrefix . str_pad($nextNumber, 10, '0', STR_PAD_LEFT);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function warranty(): BelongsTo
    {
        return $this->belongsTo(Warranty::class);
    }

    /**
     * Enhanced search scope to search by name, code (SKU), and ID
     */
    public function scopeSearch($query, $value): void
    {
        if (empty($value)) {
            return;
        }

        $query->where(function ($q) use ($value) {
            $q->where('name', 'like', "%{$value}%")
              ->orWhere('code', 'like', "%{$value}%")
              ->orWhere('id', 'like', "%{$value}%");
        });
    }
}
