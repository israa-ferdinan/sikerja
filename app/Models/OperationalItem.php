<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalItem extends Model
{
    use HasFactory;

    public const CATEGORY_NETWORK = 'jaringan';
    public const CATEGORY_LAB_INVENTORY = 'inventaris_lab';
    public const CATEGORY_LAB_CHECK = 'pemeriksaan_lab';
    public const CATEGORY_OTHER = 'lainnya';

    protected $fillable = [
        'unit_id',
        'category',
        'name',
        'location',
        'brand',
        'model',
        'year',
        'quantity',
        'identifier',
        'description',
        'is_active',
        'created_by_user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'quantity' => 'integer',
    ];

    public static function categoryOptions(): array
    {
        return [
            self::CATEGORY_NETWORK => 'Rekap Jaringan',
            self::CATEGORY_LAB_INVENTORY => 'Inventaris Lab',
            self::CATEGORY_LAB_CHECK => 'Pemeriksaan Lab',
            self::CATEGORY_OTHER => 'Lainnya',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryOptions()[$this->category] ?? $this->category;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function recordItems()
    {
        return $this->hasMany(OperationalRecordItem::class, 'operational_item_id');
    }
}