<?php

namespace App\ShopTypes\Tech\Models;

use App\Models\Shop;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class TechRepairJob extends Model
{
    use HasFactory;

    protected $table = 'tech_repair_jobs';
    protected static ?string $resolvedTable = null;

    protected $fillable = [
        'shop_id',
        'customer_id',
        'product_id',
        'job_number',
        'device_type',
        'brand',
        'model',
        'serial_number',
        'problem_description',
        'diagnosis',
        'status',
        'priority',
        'estimated_cost',
        'final_cost',
        'received_date',
        'promised_date',
        'completed_date',
        'technician_id',
        'technician_notes',
        'customer_notes',
    ];

    protected $casts = [
        'estimated_cost' => 'integer',
        'final_cost' => 'integer',
        'received_date' => 'date',
        'promised_date' => 'date',
        'completed_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(TechProduct::class, 'product_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function parts(): HasMany
    {
        return $this->hasMany(TechRepairPart::class, 'repair_job_id');
    }

    public function diagnostics(): HasMany
    {
        return $this->hasMany(TechDiagnostic::class, 'repair_job_id');
    }

    // Scopes
    public function scopeForShop($query, $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    public function scopeDiagnosing($query)
    {
        return $query->where('status', 'diagnosing');
    }

    public function scopeRepairing($query)
    {
        return $query->where('status', 'repairing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    public function scopeForTechnician($query, $technicianId)
    {
        return $query->where('technician_id', $technicianId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('promised_date', '<', now())
            ->whereNotIn('status', ['completed', 'delivered', 'cancelled']);
    }

    // Helpers
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isOverdue(): bool
    {
        return $this->promised_date &&
            $this->promised_date->isPast() &&
            !in_array($this->status, ['completed', 'delivered', 'cancelled']);
    }

    public function assignTechnician(int $technicianId): void
    {
        $this->update(['technician_id' => $technicianId]);
    }

    public function startDiagnosis(): void
    {
        $this->update(['status' => 'diagnosing']);
    }

    public function startRepair(): void
    {
        $this->update(['status' => 'repairing']);
    }

    public function complete(int $finalCost = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_date' => now(),
            'final_cost' => $finalCost ?? $this->estimated_cost,
        ]);
    }

    public function deliver(): void
    {
        $this->update(['status' => 'delivered']);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'received' => 'badge bg-info',
            'diagnosing' => 'badge bg-warning',
            'waiting_parts' => 'badge bg-secondary',
            'repairing' => 'badge bg-primary',
            'completed' => 'badge bg-success',
            'delivered' => 'badge bg-success',
            'cancelled' => 'badge bg-danger',
            default => 'badge bg-secondary',
        };
    }

    public function getPriorityBadgeClass(): string
    {
        return match($this->priority) {
            'urgent' => 'badge bg-danger',
            'high' => 'badge bg-warning',
            'normal' => 'badge bg-info',
            'low' => 'badge bg-secondary',
            default => 'badge bg-secondary',
        };
    }

    public function getFormattedEstimatedCost(): string
    {
        return 'LKR ' . number_format($this->estimated_cost / 100, 2);
    }

    public function getFormattedFinalCost(): string
    {
        return 'LKR ' . number_format($this->final_cost / 100, 2);
    }

    public static function generateJobNumber(int $shopId): string
    {
        $prefix = 'JOB';
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

        if (Schema::hasTable('tech_repair_jobs')) {
            return self::$resolvedTable = 'tech_repair_jobs';
        }

        if (Schema::hasTable('repair_jobs')) {
            return self::$resolvedTable = 'repair_jobs';
        }

        return self::$resolvedTable = parent::getTable();
    }
}
