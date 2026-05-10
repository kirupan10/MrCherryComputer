<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Expense extends Model
{
    use BelongsToShop;

    protected $guarded = ['id'];

    protected $casts = [
        'expense_date' => 'date',
        'details' => 'array',
        'amount' => 'decimal:2',
    ];

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($expense) {
            // Auto Expense Number
            if (empty($expense->expense_number)) {
                $expense->expense_number = self::generateExpenseNumber();
            }

            // Default Status
            if (empty($expense->status)) {
                $expense->status = self::STATUS_PENDING;
            }
        });
    }

    /**
     * Generate a unique expense number
     */
    public static function generateExpenseNumber()
    {
        $latest = self::latest('id')->first();
        $number = $latest ? $latest->id + 1 : 1;

        return 'EXP-' . date('Ymd') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // =========================
    // Relationships
    // =========================

    /**
     * Get the delivery record that generated this expense (if any)
     */
    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class, 'expense_id');
    }

    /**
     * Get the user who approved this expense
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Note: shop() and creator() are provided by BelongsToShop trait

    // =========================
    // Scopes
    // =========================

    public function scopeToday($query)
    {
        return $query->whereDate('expense_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('expense_date', now()->month)
                     ->whereYear('expense_date', now()->year);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // =========================
    // Accessors
    // =========================

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getReceiptUrlAttribute()
    {
        if ($this->receipt_image) {
            return asset('storage/' . $this->receipt_image);
        }

        return null;
    }

    // =========================
    // Helper Methods
    // =========================

    public function approve($userId)
    {
        return $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
        ]);
    }

    public function reject()
    {
        return $this->update([
            'status' => self::STATUS_REJECTED,
        ]);
    }
}
