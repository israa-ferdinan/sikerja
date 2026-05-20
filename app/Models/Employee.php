<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\Position;
use App\Models\User;
use App\Models\JobDuty;
use App\Models\DutyDelegation;

class Employee extends Model
{
    protected $fillable = [
        'unit_id',
        'position_id',
        'name',
        'nip',
        'position',
        'phone',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $table = 'employees';
    
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'employee_id');
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class, 'employee_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function jobPosition()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function duties()
    {
        return $this->belongsToMany(JobDuty::class, 'employee_duty', 'employee_id', 'duty_id')
            ->withPivot(['is_primary', 'notes'])
            ->withTimestamps();
    }

    public function ownedDutyDelegations()
    {
        return $this->hasMany(DutyDelegation::class, 'owner_employee_id');
    }

    public function receivedDutyDelegations()
    {
        return $this->hasMany(DutyDelegation::class, 'delegate_employee_id');
    }
}