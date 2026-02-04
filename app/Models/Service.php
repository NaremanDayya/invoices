<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'service_type',
        'service_details',
    ];

    protected $casts = [
        'service_details' => 'array',
    ];

    /**
     * Relationships
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function serviceDetails()
    {
        return $this->hasMany(ServiceDetail::class);
    }

    /**
     * Accessors
     */
    public function getActiveInvoicesCountAttribute()
    {
        return $this->invoices()->where('is_cancelled', false)->count();
    }

    public function getTotalRevenueAttribute()
    {
        return $this->invoices()->where('is_cancelled', false)->sum('total_price');
    }

    public function getIsHumanResourceAttribute()
    {
        return $this->service_type === 'human_resource';
    }

    /**
     * Scopes
     */

    public function scopeHumanResource($query)
    {
        return $query->where('service_type', 'human_resource');
    }

}
