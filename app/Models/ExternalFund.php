<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property int $shop_id
 * @property string $source_name
 * @property string $fund_type
 * @property string|float $amount
 * @property string|float|null $interest_rate
 * @property string|null $repayment_terms
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $maturity_date
 * @property string|null $notes
 * @property string $status
 * @property int|null $created_by
 * @property-read float $total_repaid
 * @property-read float $outstanding_balance
 * @property-read float $total_interest_paid
 */
class ExternalFund extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'source_name',
        'fund_type',
        'amount',
        'interest_rate',
        'repayment_terms',
        'start_date',
        'maturity_date',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'start_date' => 'date',
        'maturity_date' => 'date',
    ];

    /**
     * Get the shop that owns the external fund
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the user who created the fund record
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all repayments for this fund
     */
    public function repayments(): HasMany
    {
        return $this->hasMany(FundRepayment::class);
    }

    /**
     * Calculate total repaid amount
     */
    public function getTotalRepaidAttribute(): float
    {
        if ($this->relationLoaded('repayments')) {
            return (float) $this->repayments->sum('total_amount');
        }

        return (float) $this->repayments()->sum('total_amount');
    }

    /**
     * Calculate outstanding balance
     */
    public function getOutstandingBalanceAttribute(): float
    {
        return (float) ($this->amount - $this->total_repaid);
    }

    /**
     * Calculate total interest paid
     */
    public function getTotalInterestPaidAttribute(): float
    {
        if ($this->relationLoaded('repayments')) {
            return (float) $this->repayments->sum('interest_amount');
        }

        return (float) $this->repayments()->sum('interest_amount');
    }

    /**
     * Calculate total principal paid
     */
    public function getTotalPrincipalPaidAttribute(): float
    {
        if ($this->relationLoaded('repayments')) {
            return (float) $this->repayments->sum('principal_amount');
        }

        return (float) $this->repayments()->sum('principal_amount');
    }

    /**
     * Check if fund is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if fund is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Scope a query to only include active funds
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include funds for a specific shop
     */
    public function scopeForShop(Builder $query, int $shopId): Builder
    {
        return $query->where('shop_id', $shopId);
    }
}
