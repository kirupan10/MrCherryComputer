<?php

namespace App\ShopTypes\Tech\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class TechDiagnostic extends Model
{
    use HasFactory;

    protected $table = 'tech_diagnostics';
    protected static ?string $resolvedTable = null;

    protected $fillable = [
        'repair_job_id',
        'test_name',
        'result',
        'details',
        'tested_by',
        'tested_at',
    ];

    protected $casts = [
        'tested_at' => 'datetime',
    ];

    // Relationships
    public function repairJob(): BelongsTo
    {
        return $this->belongsTo(TechRepairJob::class, 'repair_job_id');
    }

    public function tester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tested_by');
    }

    // Helpers
    public function getResultBadgeClass(): string
    {
        return match($this->result) {
            'pass' => 'badge bg-success',
            'fail' => 'badge bg-danger',
            'warning' => 'badge bg-warning',
            default => 'badge bg-secondary',
        };
    }

    public function getTable(): string
    {
        if (self::$resolvedTable !== null) {
            return self::$resolvedTable;
        }

        if (Schema::hasTable('diagnostics')) {
            return self::$resolvedTable = 'diagnostics';
        }

        if (Schema::hasTable('job_status_histories')) {
            return self::$resolvedTable = 'job_status_histories';
        }

        return self::$resolvedTable = parent::getTable();
    }
}
