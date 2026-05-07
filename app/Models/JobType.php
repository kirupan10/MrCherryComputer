<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToShop;

class JobType extends Model
{
    use HasFactory, BelongsToShop;

    protected $fillable = [
        'shop_id',
        'created_by',
        'name',
        'description',
        'default_days',
    ];

    public function jobs()
    {
        return $this->hasMany(Job::class, 'job_type_id');
    }
}
