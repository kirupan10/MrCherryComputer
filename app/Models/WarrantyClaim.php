<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarrantyClaim extends Model
{
    use HasFactory, BelongsToShop;

    protected $fillable = [
        'product_id',
        'customer_id',
        'order_id',
        'serial_number',
        'vendor',
        'sending_date',
        'sending_method',
        'tracking_number',
        'claim_receipt_file',
        'issue_description',
        'status',
        'expected_return_date',
        'actual_return_date',
        'resolution_notes',
        'shop_id',
        'created_by',
    ];

    protected $casts = [
        'sending_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-secondary',
            'sent' => 'bg-info',
            'in_progress' => 'bg-primary',
            'repaired' => 'bg-success',
            'replaced' => 'bg-success',
            'rejected' => 'bg-danger',
            'completed' => 'bg-teal',
            default => 'bg-secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'sent' => 'Sent',
            'in_progress' => 'In Progress',
            'repaired' => 'Repaired',
            'replaced' => 'Replaced',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
            default => 'Unknown',
        };
    }
}
