<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\CreditStatus;
use App\Traits\BelongsToShop;
use Carbon\Carbon;

class CreditSale extends Model
{
    use HasFactory, BelongsToShop;

    protected $fillable = [
        'user_id',
        'order_id',
        'customer_id',
        'shop_id',
        'created_by',
        'total_amount',
        'paid_amount',
        'due_amount',
        'due_date',
        'sale_date',
        'status',
        'credit_days',
        'notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'sale_date' => 'date',
        'status' => CreditStatus::class,
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(CreditPayment::class);
    }

    // Accessors & Mutators
    public function getTotalAmountFormattedAttribute()
    {
        return number_format($this->total_amount, 2);
    }

    public function getPaidAmountFormattedAttribute()
    {
        return number_format($this->paid_amount, 2);
    }

    public function getDueAmountFormattedAttribute()
    {
        return number_format($this->due_amount, 2);
    }

    public function getDaysOverdueAttribute()
    {
        if ($this->status === CreditStatus::PAID) {
            return 0;
        }

        return max(0, now()->diffInDays($this->due_date, false));
    }

    public function getIsOverdueAttribute()
    {
        return $this->days_overdue > 0;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', CreditStatus::PENDING);
    }

    public function scopePartial($query)
    {
        return $query->where('status', CreditStatus::PARTIAL);
    }

    public function scopePaid($query)
    {
        return $query->where('status', CreditStatus::PAID);
    }

    public function scopeOverdue($query)
    {
        return $query->whereDate('due_date', '<', Carbon::today())
                    ->where(function($q) {
                        $q->where('status', 'pending')
                          ->orWhere('status', 'partial');
                    });
    }

    // Methods
    public function makePayment($amount, $method = 'cash', $notes = null)
    {
        // Create payment record
        $payment = $this->payments()->create([
            'user_id' => $this->user_id,
            'payment_amount' => $amount,
            'payment_date' => now()->toDateString(),
            'payment_method' => $method,
            'notes' => $notes
        ]);

        // Update credit sale amounts
        $this->paid_amount += $amount;
        $this->due_amount -= $amount;

        // Update status - ensure PAID when due_amount is 0 or less
        if ($this->due_amount <= 0) {
            $this->status = CreditStatus::PAID;
            $this->due_amount = 0; // Ensure it's exactly 0, not negative
        } elseif ($this->paid_amount > 0) {
            $this->status = CreditStatus::PARTIAL;
        }

        $this->save();

        // Record in business transactions
        \App\Models\BusinessTransaction::create([
            'shop_id' => auth()->user()->shop_id ?? $this->shop_id,
            'created_by' => auth()->id() ?? $this->user_id,
            'transaction_date' => now(),
            'transaction_type' => 'credit_sale_payment',
            'vendor_name' => $this->customer->name ?? 'Customer',
            'reference_number' => 'CS-' . $this->id,
            'paid_by' => $method,
            'paid_by_user_id' => auth()->id() ?? $this->user_id,
            'total_amount' => $amount,
            'net_amount' => $amount,
            'description' => 'Payment received from customer for credit sale #' . $this->id . ($notes ? ' - ' . $notes : ''),
            'category' => 'credit_sale_payment',
            'status' => 'completed',
        ]);

        return $payment;
    }

    public function calculateInterestIfAny()
    {
        // Can be extended for interest calculation based on overdue days
        return 0;
    }
}
