<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module',
        'title',
        'message',
        'url',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, ?User $user)
    {
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('user_id', $user->id);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function getShortMessageAttribute(): ?string
    {
        if (blank($this->message)) {
            return null;
        }

        return \Illuminate\Support\Str::limit($this->message, 110);
    }

    public function isRead(): bool
    {
        return filled($this->read_at);
    }

    public function isUnread(): bool
    {
        return blank($this->read_at);
    }

    public function markAsRead(): void
    {
        if ($this->isUnread()) {
            $this->forceFill([
                'read_at' => now(),
            ])->save();
        }
    }

    public static function unreadCountFor(?User $user): int
    {
        if (! $user) {
            return 0;
        }

        return self::query()
            ->forUser($user)
            ->unread()
            ->count();
    }
}