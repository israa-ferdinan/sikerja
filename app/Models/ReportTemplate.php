<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'job_duty_id',
        'title',
        'category',
        'description_template',
        'result_template',
        'is_active',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function duty()
    {
        return $this->belongsTo(Duty::class, 'job_duty_id');
    }
}