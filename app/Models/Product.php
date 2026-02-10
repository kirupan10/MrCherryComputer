<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'category_id',
        'unit_id',
        'description',
        'image',
        'purchase_price',
        'selling_price',
        'mrp',
        'tax_percentage',
        'low_stock_alert',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = ['current_stock'];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function returnItems()
    {
        return $this->hasMany(ReturnItem::class);
    }

    // Accessors
    public function getCurrentStockAttribute()
    {
        return $this->stock ? $this->stock->quantity : 0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereHas('stock', function ($q) {
            $q->whereRaw('quantity <= products.low_stock_alert');
        });
    }
}
