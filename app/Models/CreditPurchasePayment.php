<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditPurchasePayment extends Model
{
    use HasFactory, SoftDeletes, BelongsToShop;

    protected $table = 'credit_purchase_payments';

    protected $fillable = [
        'credit_purchase_id',
        'created_by',
        'shop_id',
        'payment_amount',
        'payment_date',
        'payment_method',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'payment_amount' => 'decimal:2',
    ];

    /**
     * Get the credit purchase this payment belongs to
     */
    public function creditPurchase()
    {
        return $this->belongsTo(CreditPurchase::class);
    }

    /**
     * Get the user who recorded this payment
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to filter by shop
     */
    public function scopeByShop($query, $shopId)
    {
        return $query->where('shop_id', $shopId);
    }
}
