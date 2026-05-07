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
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the shop that owns this expense
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
