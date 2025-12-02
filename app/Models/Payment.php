<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'client_id',
        'invoice_id',
        'payment_date',
        'amount',
        'payment_method',
        'status',
        'description',
        'reference_number'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ﷼';
    }

    public function getStatusBadgeAttribute()
    {
        $statuses = [
            'completed' => ['bg-success', 'مكتمل'],
            'pending' => ['bg-warning', 'قيد الانتظار'],
            'cancelled' => ['bg-danger', 'ملغى']
        ];

        $status = $statuses[$this->status] ?? ['bg-secondary', 'غير محدد'];
        return '<span class="badge ' . $status[0] . '">' . $status[1] . '</span>';
    }

    public function getMethodBadgeAttribute()
    {
        $methods = [
            'cash' => ['bg-info', 'نقدي'],
            'bank_transfer' => ['bg-primary', 'تحويل بنكي'],
            'check' => ['bg-secondary', 'شيك']
        ];

        $method = $methods[$this->payment_method] ?? ['bg-secondary', 'غير محدد'];
        return '<span class="badge ' . $method[0] . '">' . $method[1] . '</span>';
    }
}
