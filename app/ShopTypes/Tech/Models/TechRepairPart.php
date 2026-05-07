<?php

namespace App\ShopTypes\Tech\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class TechRepairPart extends Model
{
    use HasFactory;

    protected $table = 'tech_repair_parts';
    protected static ?string $resolvedTable = null;

    protected $fillable = [
        'repair_job_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'total_price' => 'integer',
    ];

    // Relationships
    public function repairJob(): BelongsTo
    {
        return $this->belongsTo(TechRepairJob::class, 'repair_job_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(TechProduct::class, 'product_id');
    }

    // Helpers
    public function calculateTotal(): void
    {
        $this->total_price = $this->quantity * $this->unit_price;
        $this->save();
    }

    public function getTable(): string
    {
        if (self::$resolvedTable !== null) {
            return self::$resolvedTable;
        }

        if (Schema::hasTable('repair_parts')) {
            return self::$resolvedTable = 'repair_parts';
        }

        if (Schema::hasTable('job_items')) {
            return self::$resolvedTable = 'job_items';
        }

        return self::$resolvedTable = parent::getTable();
    }
}
