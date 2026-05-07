<?php

namespace App\ShopTypes\Tech\Models;

use App\Models\Shop;
use App\Models\Category;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class TechProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tech_products';
    protected static ?string $resolvedTable = null;

    protected $fillable = [
        'shop_id',
        'category_id',
        'unit_id',
        'name',
        'code',
        'quantity',
        'quantity_alert',
        'buying_price',
        'selling_price',
        'notes',
        'product_image',
        'brand',
        'model_number',
        'track_serial_numbers',
        'warranty_months',
        'warranty_type',
        'specifications',
        'is_repairable',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'quantity_alert' => 'integer',
        'buying_price' => 'integer',
        'selling_price' => 'integer',
        'warranty_months' => 'integer',
        'track_serial_numbers' => 'boolean',
        'is_repairable' => 'boolean',
        'specifications' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function serialNumbers(): HasMany
    {
        return $this->hasMany(TechSerialNumber::class, 'product_id');
    }

    public function warrantyClaims(): HasMany
    {
        return $this->hasMany(TechWarrantyClaim::class, 'product_id');
    }

    public function repairJobs(): HasMany
    {
        return $this->hasMany(TechRepairJob::class, 'product_id');
    }

    // Scopes
    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'quantity_alert');
    }

    public function scopeForShop($query, $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    public function scopeWithSerialTracking($query)
    {
        return $query->where('track_serial_numbers', true);
    }

    // Helpers
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->quantity_alert;
    }

    public function hasWarranty(): bool
    {
        return $this->warranty_months > 0;
    }

    public function getAvailableSerialNumbers()
    {
        return $this->serialNumbers()->where('status', 'in_stock')->get();
    }

    public function getFormattedSellingPrice(): string
    {
        return 'LKR ' . number_format($this->selling_price / 100, 2);
    }

    public function getFormattedBuyingPrice(): string
    {
        return 'LKR ' . number_format($this->buying_price / 100, 2);
    }

    public function getTable(): string
    {
        if (self::$resolvedTable !== null) {
            return self::$resolvedTable;
        }

        if (Schema::hasTable('products')) {
            return self::$resolvedTable = 'products';
        }

        return self::$resolvedTable = parent::getTable();
    }
}
