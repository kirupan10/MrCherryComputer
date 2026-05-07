<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundRepayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'external_fund_id',
        'payment_date',
        'principal_amount',
        'interest_amount',
        'total_amount',
        'payment_method',
        'reference_number',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the external fund that owns this repayment
     */
    public function externalFund(): BelongsTo
    {
        return $this->belongsTo(ExternalFund::class);
    }

    /**
     * Get the user who recorded this repayment
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Scope a query to only include repayments for a specific fund
     */
    public function scopeForFund($query, $fundId)
    {
        return $query->where('external_fund_id', $fundId);
    }

    /**
     * Scope a query to order by payment date
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('payment_date', 'desc');
    }
}
