<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DutyDelegation extends Model
{
    use HasFactory;

    protected $fillable = [
        'duty_id',
        'owner_employee_id',
        'delegate_employee_id',
        'start_date',
        'end_date',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function duty()
    {
        return $this->belongsTo(JobDuty::class, 'duty_id');
    }

    public function ownerEmployee()
    {
        return $this->belongsTo(Employee::class, 'owner_employee_id');
    }

    public function delegateEmployee()
    {
        return $this->belongsTo(Employee::class, 'delegate_employee_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActiveForDate($query, $date)
    {
        return $query->where('is_active', true)
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            });
    }
}