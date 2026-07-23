<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ControlFollowUp extends Model
{
    protected $fillable = [
        'evaluation_record_id',
        'unit_id',
        'title',
        'description',
        'recommendation',
        'pic_user_id',
        'due_date',
        'status',
        'progress_note',
        'completed_note',
        'completed_at',
        'cancelled_note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';
    public const STATUS_CANCELLED = 'cancelled';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_IN_PROGRESS => 'Dalam Proses',
            self::STATUS_DONE => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? ucfirst((string) $this->status);
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'bg-slate-100 text-slate-700 border-slate-200',
            self::STATUS_IN_PROGRESS => 'bg-amber-50 text-amber-700 border-amber-200',
            self::STATUS_DONE => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::STATUS_CANCELLED => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    public function evaluationRecord(): BelongsTo
    {
        return $this->belongsTo(EvaluationRecord::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function picUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function letters(): HasMany
    {
        return $this->hasMany(ControlLetter::class);
    }
}