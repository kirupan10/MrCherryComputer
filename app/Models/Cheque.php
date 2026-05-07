<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property int $shop_id
 * @property int|null $created_by
 * @property string $cheque_number
 * @property string $bank_name
 * @property string|null $branch_name
 * @property \Carbon\Carbon|string $cheque_date
 * @property string|float $amount
 * @property string|null $related_to
 * @property int|null $related_id
 * @property string|null $drawer_name
 * @property string|null $payee_name
 * @property string|null $payee_address
 * @property string $status
 * @property \Carbon\Carbon|string|null $deposit_date
 * @property \Carbon\Carbon|string|null $clearance_date
 * @property string|null $bounce_reason
 * @property string|null $notes
 * @property string|null $reference_number
 */
class Cheque extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shop_id',
        'created_by',
        'cheque_number',
        'bank_name',
        'branch_name',
        'cheque_date',
        'amount',
        'related_to',
        'related_id',
        'drawer_name',
        'payee_name',
        'payee_address',
        'status',
        'deposit_date',
        'clearance_date',
        'bounce_reason',
        'notes',
        'reference_number',
    ];

    /** @var array<int, string> */
    protected array $dates = [
        'cheque_date',
        'deposit_date',
        'clearance_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cheque_date' => 'date',
        'deposit_date' => 'date',
        'clearance_date' => 'date',
    ];

    /**
     * Get the user who created this cheque
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the shop this cheque belongs to
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the related vendor purchase (if applicable)
     */
    public function vendorPurchase()
    {
        return $this->belongsTo(CreditPurchase::class, 'related_id');
    }

    /**
     * Get the related customer payment (if applicable)
     */
    public function customerPayment()
    {
        return $this->belongsTo(CreditSale::class, 'related_id');
    }

    /**
     * Scope to filter by shop
     */
    public function scopeByShop(Builder $query, int $shopId): Builder
    {
        return $query->where('shop_id', $shopId);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus(Builder $query, ?string $status): Builder
    {
        if ($status && in_array($status, ['pending', 'deposited', 'cleared', 'bounced', 'cancelled'])) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope to filter pending cheques
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter cleared cheques
     */
    public function scopeCleared(Builder $query): Builder
    {
        return $query->where('status', 'cleared');
    }

    /**
     * Scope to filter bounced cheques
     */
    public function scopeBounced(Builder $query): Builder
    {
        return $query->where('status', 'bounced');
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange(Builder $query, mixed $startDate, mixed $endDate): Builder
    {
        return $query->whereBetween('cheque_date', [$startDate, $endDate]);
    }

    /**
     * Get the status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'deposited' => 'info',
            'cleared' => 'success',
            'bounced' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get days until cheque clears (if applicable)
     */
    public function getDaysPendingAttribute()
    {
        if ($this->status === 'pending' && $this->cheque_date) {
            return $this->cheque_date->diffInDays(now());
        }
        return null;
    }

    /**
     * Check if cheque is overdue for clearing
     */
    public function getIsOverdueAttribute()
    {
        if ($this->status === 'deposited' && $this->deposit_date) {
            return $this->deposit_date->addDays(3)->lessThan(now()); // Assuming 3 days clearing time
        }
        return false;
    }
};
