<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Employee;

class JobDuty extends Model
{
    protected $fillable = [
        'unit_id',
        'name',
        'description',
        'is_active',
    ];

    protected $table = 'duties';
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function dailyReports(): BelongsToMany
    {
        return $this->belongsToMany(
            DailyReport::class,
            'daily_report_tupoksi',
            'job_duty_id',
            'daily_report_id'
        )->withTimestamps();
    }

    public function reportTemplates()
    {
        return $this->hasMany(ReportTemplate::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_duty', 'duty_id', 'employee_id')
            ->withPivot(['is_primary', 'notes'])
            ->withTimestamps();
    }
}