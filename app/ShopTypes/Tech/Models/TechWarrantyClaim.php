<?php

namespace App\ShopTypes\Tech\Models;

use App\Models\Shop;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class TechWarrantyClaim extends Model
{
    use HasFactory;

    protected $table = 'tech_warranty_claims';
    protected static ?string $resolvedTable = null;

    protected $fillable = [
        'shop_id',
        'product_id',
        'customer_id',
        'serial_number_id',
        'claim_number',
        'serial_number',
        'issue_description',
        'status',
        'claim_date',
        'resolution_date',
        'vendor',
        'tracking_number',
        'resolution_notes',
        'handled_by',
    ];

    protected $casts = [
        'claim_date' => 'date',
        'resolution_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(TechProduct::class, 'product_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function serialNumber(): BelongsTo
    {
        return $this->belongsTo(TechSerialNumber::class, 'serial_number_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    // Scopes
    public function scopeForShop($query, $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Helpers
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function approve(int $handledBy): void
    {
        $this->update([
            'status' => 'approved',
            'handled_by' => $handledBy,
        ]);
    }

    public function startProcessing(): void
    {
        $this->update(['status' => 'in_progress']);
    }

    public function complete(string $notes = null): void
    {
        $this->update([
            'status' => 'completed',
            'resolution_date' => now(),
            'resolution_notes' => $notes,
        ]);
    }

    public function reject(string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'resolution_date' => now(),
            'resolution_notes' => $reason,
        ]);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending' => 'badge bg-warning',
            'approved' => 'badge bg-info',
            'in_progress' => 'badge bg-primary',
            'completed' => 'badge bg-success',
            'rejected' => 'badge bg-danger',
            default => 'badge bg-secondary',
        };
    }

    public static function generateClaimNumber(int $shopId): string
    {
        $prefix = 'WC';
        $date = now()->format('Ymd');
        $count = self::where('shop_id', $shopId)
            ->whereDate('created_at', today())
            ->count() + 1;

        return sprintf('%s%s%04d', $prefix, $date, $count);
    }

    public function getTable(): string
    {
        if (self::$resolvedTable !== null) {
            return self::$resolvedTable;
        }

        if (Schema::hasTable('warranty_claims')) {
            return self::$resolvedTable = 'warranty_claims';
        }

        return self::$resolvedTable = parent::getTable();
    }
}
