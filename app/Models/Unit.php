<?php

namespace App\Models;

use App\Models\Employee;
use App\Models\jobDuty;
use App\Models\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Unit::class, 'parent_id');
    }
    
    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class);
    }

    public function reportTemplates(): HasMany
    {
        return $this->hasMany(ReportTemplate::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function tupoksis()
    {
        return $this->hasMany(JobDuty::class);
    }
}