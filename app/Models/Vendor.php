<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $shop_id
 * @property string $name
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property string|null $company_name
 * @property string|null $tax_number
 * @property string $total_purchases
 * @property string $total_paid
 * @property string $outstanding_balance
 * @property string $status
 * @property string|null $notes
 * @property int|null $created_by
 */
class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'phone',
        'email',
        'address',
        'company_name',
        'tax_number',
        'total_purchases',
        'total_paid',
        'outstanding_balance',
        'status',
        'notes',
        'created_by',
    ];

    /**
     * Get all payment transactions for this vendor
     */
    public function payments()
    {
        return $this->hasManyThrough(
            \App\Models\CreditPurchasePayment::class,
            \App\Models\CreditPurchase::class,
            'vendor_id', // Foreign key on credit_purchases table
            'credit_purchase_id', // Foreign key on credit_purchase_payments table
            'id', // Local key on vendors table
            'id' // Local key on credit_purchases table
        );
    }
    // ...existing code...

    protected $casts = [
        'total_purchases' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
    ];

    /**
     * Get the shop that owns the vendor
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the user who created this vendor
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all credit purchases for this vendor
     */
    public function creditPurchases()
    {
        return $this->hasMany(CreditPurchase::class);
    }

    /**
     * Get pending credit purchases
     */
    public function pendingPurchases()
    {
        return $this->creditPurchases()->whereIn('status', ['pending', 'partial']);
    }

    /**
     * Update vendor balance totals
     */
    public function updateBalances()
    {
        $this->total_purchases = $this->creditPurchases()->sum('total_amount');
        $this->total_paid = $this->creditPurchases()->sum('paid_amount');
        $this->outstanding_balance = $this->creditPurchases()->sum('due_amount');
        $this->save();
    }

    /**
     * Scope to get active vendors
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Get formatted balance with currency
     */
    public function getFormattedBalanceAttribute()
    {
        return 'LKR ' . number_format((float)$this->outstanding_balance ?? 0, 2);
    }
}

