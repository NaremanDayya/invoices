<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Deduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'amount',
        'reason',
        'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
