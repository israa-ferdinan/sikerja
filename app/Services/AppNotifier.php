<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Employee;
use App\Models\User;
use Throwable;

class AppNotifier
{
    /**
     * Kirim notifikasi langsung kepada user.
     *
     * Kegagalan pembuatan notifikasi tidak boleh menggagalkan
     * proses utama aplikasi.
     */
    public static function notifyUser(
        int|User|null $user,
        string $module,
        string $title,
        ?string $message = null,
        ?string $url = null,
        ?array $data = null,
    ): ?AppNotification {
        try {
            $userId = $user instanceof User
                ? $user->id
                : $user;

            if (blank($userId)) {
                return null;
            }

            $targetUser = $user instanceof User
                ? $user
                : User::query()->find($userId);

            if (! $targetUser || ! $targetUser->is_active) {
                return null;
            }

            return AppNotification::create([
                'user_id' => $targetUser->id,
                'module' => $module,
                'title' => $title,
                'message' => $message,
                'url' => $url,
                'data' => $data,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }

    /**
     * Kirim notifikasi berdasarkan employee.
     *
     * Employee harus mempunyai akun user aktif yang terhubung.
     */
    public static function notifyEmployee(
        int|Employee|null $employee,
        string $module,
        string $title,
        ?string $message = null,
        ?string $url = null,
        ?array $data = null,
    ): ?AppNotification {
        try {
            $employeeId = $employee instanceof Employee
                ? $employee->id
                : $employee;

            if (blank($employeeId)) {
                return null;
            }

            $user = User::query()
                ->where('employee_id', $employeeId)
                ->where('is_active', true)
                ->first();

            if (! $user) {
                return null;
            }

            return self::notifyUser(
                user: $user,
                module: $module,
                title: $title,
                message: $message,
                url: $url,
                data: $data,
            );
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }
}