<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    

    protected $fillable = [
        'unit_id',
        'name',
        'nip',
        'position',
        'phone',
        'is_active',
    ];

    protected $table = 'employees';

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }
}