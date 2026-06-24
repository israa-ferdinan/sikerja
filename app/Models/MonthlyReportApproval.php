<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyReportApproval extends Model
{
    protected $fillable = [
        'unit_id',
        'month',
        'year',
        'status',
        'approved_by_user_id',
        'approved_by_employee_id',
        'approved_at',
        'approver_name',
        'approver_nip',
        'approver_position',
        'approver_unit_name',
        'approver_signature_path',
        'cancelled_by_user_id',
        'cancelled_at',
        'cancel_reason',
        'notes',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function approvedByEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by_employee_id');
    }

    public function cancelledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}