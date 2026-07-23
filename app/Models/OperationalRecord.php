<?php

namespace App\Models;

use App\Models\User;
use App\Models\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OperationalRecord extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'record_code',
        'unit_id',
        'category',
        'title',
        'period_month',
        'period_year',
        'record_date',
        'status',
        'notes',
        'created_by_user_id',
        'updated_by_user_id',
        'submitted_at',
        'verified_by_user_id',
        'verified_at',
        'cancelled_at',
        'cancelled_by_user_id',
        'technician_employee_id',
    ];

    protected $casts = [
        'record_date' => 'date',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'period_month' => 'integer',
        'period_year' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (OperationalRecord $record) {
            if (blank($record->record_code)) {
                $record->record_code = self::generateRecordCode();
            }

            if (blank($record->status)) {
                $record->status = self::STATUS_DRAFT;
            }
        });
    }

    public static function generateRecordCode(): string
    {
        do {
            $code = 'OPR-' . now()->format('Ymd') . '-' . Str::upper(Str::random(4));
        } while (self::query()->where('record_code', $code)->exists());

        return $code;
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Diajukan',
            self::STATUS_VERIFIED => 'Terverifikasi',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

    public static function categoryOptions(): array
    {
        return OperationalItem::categoryOptions();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusOptions()[$this->status] ?? $this->status;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryOptions()[$this->category] ?? $this->category;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isLocked(): bool
    {
        return in_array($this->status, [
            self::STATUS_SUBMITTED,
            self::STATUS_VERIFIED,
            self::STATUS_CANCELLED,
        ], true);
    }

    public function isEditable(): bool
    {
        return $this->isDraft();
    }

    public function isDeletable(): bool
    {
        return $this->isDraft() || $this->isCancelled();
    }

    public function canSubmit(): bool
    {
        return $this->isDraft();
    }

    public function canVerify(): bool
    {
        return $this->isSubmitted();
    }

    public function canCancel(): bool
    {
        return $this->isDraft() || $this->isSubmitted();
    }

    public function scopeForCategory($query, ?string $category)
    {
        if (blank($category)) {
            return $query;
        }

        return $query->where('category', $category);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function items()
    {
        return $this->hasMany(OperationalRecordItem::class, 'operational_record_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function verifiedByUser()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function cancelledByUser()
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id');
    }

    public function technician()
    {
        return $this->belongsTo(Employee::class, 'technician_employee_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id');
    }
}