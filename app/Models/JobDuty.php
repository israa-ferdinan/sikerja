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
        'duty_classification_id',
        'object_type',
        'server_id',
        'application_id',
        'object_name',
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

    public function delegations()
    {
        return $this->hasMany(DutyDelegation::class, 'duty_id');
    }

    public function classification(): BelongsTo
    {
        return $this->belongsTo(DutyClassification::class, 'duty_classification_id');
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function getWorkObjectLabelAttribute(): string
    {
        return match ($this->object_type) {
            'server' => $this->server?->name ?? '-',
            'application' => $this->application?->name ?? '-',
            'facility' => $this->object_name ?: '-',
            'document' => $this->object_name ?: '-',
            'user_service' => $this->object_name ?: '-',
            'other' => $this->object_name ?: '-',
            default => '-',
        };
    }

    public function getObjectTypeLabelAttribute(): string
    {
        return match ($this->object_type) {
            'server' => 'Server',
            'application' => 'Aplikasi',
            'facility' => 'Perangkat / Fasilitas',
            'document' => 'Dokumen / Administrasi',
            'user_service' => 'Layanan Pengguna',
            'other' => 'Lainnya',
            default => 'Tidak Ada Objek Khusus',
        };
    }
}