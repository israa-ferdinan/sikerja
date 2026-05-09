<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyReportPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'file_path',
        'original_name',
        'compressed_size',
        'mime_type',
        'sort_order',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
}