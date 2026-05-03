<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class JobDuty extends Model
{
    protected $fillable = [
        'unit_id',
        'name',
        'description',
        'is_active',
    ];

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
}