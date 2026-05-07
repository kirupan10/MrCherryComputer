<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $shop_id
 * @property int|null $created_by
 * @property int|null $vendor_id
 * @property string|null $vendor_name
 * @property string|null $vendor_phone
 * @property string|null $vendor_email
 * @property string|null $vendor_address
 * @property string|float|int $total_amount
 * @property string|float|int $paid_amount
 * @property string|float|int $due_amount
 * @property \Illuminate\Support\Carbon|null $purchase_date
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property int|null $credit_days
 * @property string|null $status
 * @property string|null $purchase_type
 * @property string|null $reference_number
 * @property string|null $notes
 * @property \Illuminate\Support\Collection|array|null $items
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read Vendor|null $vendor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CreditPurchasePayment> $payments
 * @property-read string|float|int $balance
 * @property-read float $payment_percentage
 * @property-read bool $is_overdue
 * @property-read int $days_until_due
 */
class CreditPurchase extends Model
{
    use HasFactory, SoftDeletes, BelongsToShop;

    protected $table = 'credit_purchases';

    protected $fillable = [
        'shop_id',
        'created_by',
        'vendor_id',
        'vendor_name',
        'vendor_phone',
        'vendor_email',
        'vendor_address',
        'total_amount',
        'paid_amount',
        'due_amount',
        'purchase_date',
        'due_date',
        'credit_days',
        'status',
        'purchase_type',
        'reference_number',
        'notes',
        'items',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'items' => AsCollection::class,
    ];

    /**
     * Get the user who created this purchase
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the vendor for this purchase
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get all payments for this credit purchase
     */
    public function payments()
    {
        return $this->hasMany(CreditPurchasePayment::class);
    }

    /**
     * Scope to filter by shop
     */
    public function scopeByShop(Builder $query, int $shopId): Builder
    {
        return $query->where('shop_id', $shopId);
    }

    /**
     * Scope to filter pending purchases
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter partial purchases
     */
    public function scopePartial(Builder $query): Builder
    {
        return $query->where('status', 'partial');
    }

    /**
     * Scope to filter paid purchases
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope to filter cash purchases
     */
    public function scopeCash(Builder $query): Builder
    {
        return $query->where('purchase_type', 'cash');
    }

    /**
     * Scope to filter cheque purchases
     */
    public function scopeCheque(Builder $query): Builder
    {
        return $query->where('purchase_type', 'cheque');
    }

    /**
     * Scope to filter credit purchases
     */
    public function scopeCredit(Builder $query): Builder
    {
        return $query->where('purchase_type', 'credit');
    }

    /**
     * Get remaining balance
     */
    public function getBalanceAttribute()
    {
        return $this->due_amount;
    }

    /**
     * Get payment percentage
     */
    public function getPaymentPercentageAttribute()
    {
        if ($this->total_amount === 0) {
            return 0;
        }
        return round(($this->paid_amount / $this->total_amount) * 100, 2);
    }

    /**
     * Check if overdue
     */
    public function getIsOverdueAttribute()
    {
        return now()->isAfter($this->due_date) && $this->status !== 'paid';
    }

    /**
     * Gets days until due
     */
    public function getDaysUntilDueAttribute()
    {
        return $this->due_date->diffInDays(now());
    }
}
