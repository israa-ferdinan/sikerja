<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UnitTargetSupport extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_target_id',
        'unit_id',
        'uploaded_by',
        'support_type',
        'title',
        'description',
        'file_path',
        'file_original_name',
        'file_mime_type',
        'file_size',
        'url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
    ];

    public function target()
    {
        return $this->belongsTo(UnitTarget::class, 'unit_target_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getSupportTypeLabelAttribute(): string
    {
        return match ($this->support_type) {
            'file' => 'File Dokumen',
            'link' => 'Link',
            'note' => 'Catatan',
            'other' => 'Bukti Lainnya',
            default => 'Data Dukung',
        };
    }

    public function getFileUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        return asset('storage/' . ltrim($this->file_path, '/'));
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (! $this->file_size) {
            return '-';
        }

        $size = (int) $this->file_size;

        if ($size >= 1048576) {
            return round($size / 1048576, 2) . ' MB';
        }

        return round($size / 1024, 2) . ' KB';
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->support_type) {
            'file' => 'bg-blue-100 text-blue-700',
            'link' => 'bg-purple-100 text-purple-700',
            'note' => 'bg-amber-100 text-amber-700',
            'other' => 'bg-gray-100 text-gray-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}