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
    ];

<<<<<<< HEAD
    /**
     * Get the delivery record that generated this expense (if any)
     */
    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class, 'expense_id');
    }

    /**
     * Get the user who created this expense
     */
    public function creator(): BelongsTo
=======
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

    // Generate Expense Number
    public static function generateExpenseNumber()
    {
        $latest = self::latest('id')->first();

        $number = $latest ? $latest->id + 1 : 1;

        return 'EXP-' . date('Ymd') . '-' .
            str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // =========================
    // Relationships
    // =========================

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function creator()
>>>>>>> 0e37cabe230003180f72b2b20d262a05fa72129c
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the shop that owns this expense
     */
    public function shop(): BelongsTo
    {
<<<<<<< HEAD
        return $this->belongsTo(Shop::class);
=======
        return $this->belongsTo(User::class, 'approved_by');
    }

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
>>>>>>> 0e37cabe230003180f72b2b20d262a05fa72129c
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
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
        ]);
    }

    public function reject()
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
        ]);
    }
}
