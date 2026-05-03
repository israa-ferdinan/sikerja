<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'unit_id',
        'report_date',
        'title',
        'description',
        'result',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(DailyReportPhoto::class)
            ->orderBy('sort_order');
    }

    public function jobDuties(): BelongsToMany
    {
        return $this->belongsToMany(
            JobDuty::class,
            'daily_report_tupoksi',
            'daily_report_id',
            'job_duty_id'
        )->withTimestamps();
    }

    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(
            Server::class,
            'daily_report_servers',
            'daily_report_id',
            'server_id'
        )->withTimestamps();
    }

    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(
            Application::class,
            'daily_report_applications',
            'daily_report_id',
            'application_id'
        )->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('report_date', now()->month)
            ->whereYear('report_date', now()->year);
    }

    public function scopeForUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }
}