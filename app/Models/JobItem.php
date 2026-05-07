<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'description',
        'quantity',
        'remarks',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
