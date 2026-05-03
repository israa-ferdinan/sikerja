<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Server extends Model
{
    protected $fillable = [
        'unit_id',
        'name',
        'hostname',
        'ip_address',
        'server_type',
        'location',
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
            'daily_report_servers',
            'server_id',
            'daily_report_id'
        )->withTimestamps();
    }
}