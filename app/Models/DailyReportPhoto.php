<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DailyReportPhoto extends Model
{
    protected $fillable = [
        'daily_report_id',
        'file_path',
        'original_name',
        'compressed_size',
        'mime_type',
        'sort_order',
    ];

    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}