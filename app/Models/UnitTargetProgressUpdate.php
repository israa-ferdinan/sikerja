<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitTargetProgressUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_target_id',
        'unit_id',
        'achievement_method',
        'progress_value',
        'status',
        'note',
        'updated_by',
    ];

    protected $casts = [
        'progress_value' => 'integer',
    ];

    public function target()
    {
        return $this->belongsTo(UnitTarget::class, 'unit_target_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'not_started' => 'Belum Mulai',
            'in_progress' => 'Berjalan',
            'completed' => 'Selesai',
            default => '-',
        };
    }

    public function getAchievementMethodLabelAttribute(): string
    {
        return match ($this->achievement_method) {
            'auto_report' => 'Otomatis dari Laporan Harian',
            'manual_progress' => 'Manual Progress',
            'manual_status' => 'Manual Status',
            default => 'Metode Capaian',
        };
    }
}