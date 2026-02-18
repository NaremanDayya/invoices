<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceRevisionStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'revision_status',
        'revision_notes',
        'revised_by',
        'approved_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function revisedBy()
    {
        return $this->belongsTo(User::class, 'revised_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopeRequested($query)
    {
        return $query->where('revision_status', 'requested');
    }

    public function scopeRejected($query)
    {
        return $query->where('revision_status', 'rejected');
    }

    public function scopeCompleted($query)
    {
        return $query->where('revision_status', 'completed');
    }

    public function scopeApproved($query)
    {
        return $query->where('revision_status', 'approved');
    }
}
