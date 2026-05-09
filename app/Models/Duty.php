<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Duty extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'name',
        'description',
        'is_active',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }
}