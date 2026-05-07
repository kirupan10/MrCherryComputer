<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarcodeSettings extends Model
{
    use HasFactory, BelongsToShop;

    protected $fillable = [
        'shop_id',
        'created_by',
        'show_barcode',
        'show_title',
        'show_price',
        'barcode_type',
        'barcode_width',
        'barcode_height',
        'paper_size',
        'labels_per_row',
        'font_size',
    ];

    protected $casts = [
        'show_barcode' => 'boolean',
        'show_title' => 'boolean',
        'show_price' => 'boolean',
        'barcode_width' => 'integer',
        'barcode_height' => 'integer',
        'labels_per_row' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the shop that owns the barcode settings
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get or create barcode settings for the given shop
     */
    public static function getForShop($shopId)
    {
        return static::firstOrCreate(
            ['shop_id' => $shopId],
            [
                'created_by' => auth()->id(),
                'show_barcode' => true,
                'show_title' => true,
                'show_price' => true,
                'barcode_type' => 'EAN13',
                'barcode_width' => 3,  // Increased for better scanning
                'barcode_height' => 80, // Increased for EAN-13 standard (was 50)
                'paper_size' => '40x30',
                'labels_per_row' => 3,
                'font_size' => '10',
            ]
        );
    }
}
