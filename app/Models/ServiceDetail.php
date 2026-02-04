<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'has_work_days',
        'work_days',
    ];

    protected $casts = [
        'has_work_days' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
