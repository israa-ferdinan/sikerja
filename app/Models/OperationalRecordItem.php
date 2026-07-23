<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalRecordItem extends Model
{
    use HasFactory;

    public const CONDITION_NORMAL = 'normal';
    public const CONDITION_ATTENTION = 'perlu_perhatian';
    public const CONDITION_PROBLEM = 'bermasalah';
    public const CONDITION_BROKEN = 'rusak';

    public const COMPONENT_GOOD = 'baik';
    public const COMPONENT_DAMAGED = 'rusak';
    public const COMPONENT_NOT_AVAILABLE = 'tidak_ada';

    protected $fillable = [
        'operational_record_id',
        'operational_item_id',
        'item_name',
        'item_location',
        'item_identifier',
        'condition_status',
        'component_status',
        'description',
        'action_taken',
    ];

    protected $casts = [
        'component_status' => 'array',
    ];

    public static function conditionOptions(): array
    {
        return [
            self::CONDITION_NORMAL => 'Normal',
            self::CONDITION_ATTENTION => 'Perlu Perhatian',
            self::CONDITION_PROBLEM => 'Bermasalah',
            self::CONDITION_BROKEN => 'Rusak',
        ];
    }

    public static function componentOptions(): array
    {
        return [
            self::COMPONENT_GOOD => 'Baik',
            self::COMPONENT_DAMAGED => 'Rusak',
            self::COMPONENT_NOT_AVAILABLE => 'Tidak Ada',
        ];
    }

    public static function labComponentKeys(): array
    {
        return [
            'os' => 'OS',
            'aplikasi' => 'Aplikasi',
            'mouse' => 'Mouse',
            'keyboard' => 'Keyboard',
            'monitor' => 'Monitor',
            'cpu' => 'CPU',
        ];
    }

    public function getConditionLabelAttribute(): string
    {
        return self::conditionOptions()[$this->condition_status] ?? $this->condition_status;
    }

    public function record()
    {
        return $this->belongsTo(OperationalRecord::class, 'operational_record_id');
    }

    public function item()
    {
        return $this->belongsTo(OperationalItem::class, 'operational_item_id');
    }
}