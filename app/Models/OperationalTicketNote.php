<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalTicketNote extends Model
{
    use HasFactory;

    public const VISIBILITY_INTERNAL = 'internal';
    public const VISIBILITY_PUBLIC = 'public';

    protected $fillable = [
        'operational_ticket_id',
        'created_by_user_id',
        'visibility',
        'note',
    ];

    public static function visibilityOptions(): array
    {
        return [
            self::VISIBILITY_INTERNAL => 'Internal',
            self::VISIBILITY_PUBLIC => 'Public / Tampil ke Pemohon',
        ];
    }

    public function ticket()
    {
        return $this->belongsTo(OperationalTicket::class, 'operational_ticket_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function getVisibilityLabelAttribute(): string
    {
        return self::visibilityOptions()[$this->visibility] ?? ucfirst((string) $this->visibility);
    }

    public function isPublic(): bool
    {
        return $this->visibility === self::VISIBILITY_PUBLIC;
    }
}