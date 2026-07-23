<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlLetter extends Model
{
    protected $fillable = [
        'control_follow_up_id',
        'unit_id',
        'letter_type',
        'letter_number',
        'letter_date',
        'subject',
        'sender',
        'recipient',
        'summary',
        'visibility',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    protected $casts = [
        'letter_date' => 'date',
    ];

    public const TYPE_INCOMING = 'incoming';
    public const TYPE_OUTGOING = 'outgoing';

    public const VISIBILITY_UNIT = 'unit';
    public const VISIBILITY_RESTRICTED = 'restricted';

    public static function typeOptions(): array
    {
        return [
            self::TYPE_INCOMING => 'Surat Masuk',
            self::TYPE_OUTGOING => 'Surat Keluar',
        ];
    }

    public static function visibilityOptions(): array
    {
        return [
            self::VISIBILITY_UNIT => 'Unit',
            self::VISIBILITY_RESTRICTED => 'Terbatas',
        ];
    }

    public function typeLabel(): string
    {
        return self::typeOptions()[$this->letter_type] ?? ucfirst((string) $this->letter_type);
    }

    public function visibilityLabel(): string
    {
        return self::visibilityOptions()[$this->visibility] ?? ucfirst((string) $this->visibility);
    }

    public function typeBadgeClass(): string
    {
        return match ($this->letter_type) {
            self::TYPE_INCOMING => 'bg-sky-50 text-sky-700 border-sky-200',
            self::TYPE_OUTGOING => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    public function visibilityBadgeClass(): string
    {
        return match ($this->visibility) {
            self::VISIBILITY_UNIT => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::VISIBILITY_RESTRICTED => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    public function followUp(): BelongsTo
    {
        return $this->belongsTo(ControlFollowUp::class, 'control_follow_up_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}