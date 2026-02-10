<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnModel extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'return_number',
        'sale_id',
        'customer_id',
        'return_date',
        'subtotal',
        'tax_amount',
        'total_amount',
        'refund_amount',
        'refund_method',
        'reason',
        'status',
        'created_by',
    ];

    protected $casts = [
        'return_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($return) {
            if (empty($return->return_number)) {
                $return->return_number = self::generateReturnNumber();
            }
        });
    }

    public static function generateReturnNumber()
    {
        $latest = self::latest('id')->first();
        $number = $latest ? $latest->id + 1 : 1;
        return 'RET-' . date('Ymd') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
