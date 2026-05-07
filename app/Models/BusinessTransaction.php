<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property int $shop_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $transaction_date
 * @property string|null $transaction_type
 * @property string|null $vendor_name
 * @property string|null $receipt_number
 * @property string|null $reference_number
 * @property string|null $paid_by
 * @property int|null $paid_by_user_id
 * @property string|float|int|null $total_amount
 * @property string|float|int|null $discount_amount
 * @property string|float|int|null $net_amount
 * @property string|null $description
 * @property array|null $items
 * @property string|null $category
 * @property string|null $status
 * @property string|null $attachment_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Shop|null $shop
 * @property-read User|null $creator
 * @property-read User|null $paidByUser
 */
class BusinessTransaction extends Model
{
    use HasFactory, SoftDeletes, BelongsToShop;

    protected $fillable = [
        'shop_id',
        'created_by',
        'transaction_date',
        'transaction_type',
        'vendor_name',
        'receipt_number',        // Receipt from vendor
        'reference_number',      // Our bank transfer reference
        'paid_by',
        'paid_by_user_id',
        'total_amount',
        'discount_amount',
        'net_amount',
        'description',
        'items',
        'category',
        'status',
        'attachment_path',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'items' => 'array',
    ];

    /**
     * Get the shop that owns the transaction
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the user who created the transaction
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Backward-compatible alias for creator relation.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who paid for the transaction
     */
    public function paidByUser()
    {
        return $this->belongsTo(User::class, 'paid_by_user_id');
    }

    /**
     * Scope for filtering by transaction type
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange(Builder $query, mixed $startDate, mixed $endDate): Builder
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Get formatted transaction type
     */
    public function getFormattedTypeAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->transaction_type));
    }

    /**
     * Get formatted paid by method
     */
    public function getFormattedPaidByAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->paid_by ?? 'N/A'));
    }
}
