<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

class ActivityLogger
{
    protected static array $hiddenKeys = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'new_password_confirmation',
        'remember_token',
        'token',
        'api_token',
        'access_token',
        'refresh_token',
        'secret',
        'secret_key',
        'private_key',
    ];

    public static function log(
        string $module,
        string $action,
        ?string $description = null,
        ?Model $subject = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): void {
        try {
            $user = Auth::user();

            ActivityLog::create([
                'user_id' => $user?->id,
                'role_id' => $user?->role_id,
                'role_name' => $user?->role?->name,

                'module' => $module,
                'action' => $action,
                'description' => $description,

                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $subject?->getKey(),

                'old_values' => self::sanitizeValues($oldValues),
                'new_values' => self::sanitizeValues($newValues),

                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    protected static function sanitizeValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        foreach ($values as $key => $value) {
            if (in_array(strtolower((string) $key), self::$hiddenKeys, true)) {
                $values[$key] = '[FILTERED]';
                continue;
            }

            if (is_array($value)) {
                $values[$key] = self::sanitizeValues($value);
            }
        }

        return $values;
    }
}