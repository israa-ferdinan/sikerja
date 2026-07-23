<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class AppNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = AppNotification::query()
            ->forUser($user)
            ->latest();

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            }

            if ($request->status === 'read') {
                $query->read();
            }
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        $notifications = $query
            ->paginate(15)
            ->withQueryString();

        $modules = AppNotification::query()
            ->forUser($user)
            ->whereNotNull('module')
            ->select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        $summary = [
            'total' => AppNotification::query()
                ->forUser($user)
                ->count(),

            'unread' => AppNotification::query()
                ->forUser($user)
                ->unread()
                ->count(),

            'read' => AppNotification::query()
                ->forUser($user)
                ->read()
                ->count(),
        ];

        return view('notifications.index', [
            'notifications' => $notifications,
            'modules' => $modules,
            'summary' => $summary,
        ]);
    }

    public function markAsRead(Request $request, AppNotification $notification)
    {
        $this->authorizeNotification($request, $notification);

        $notification->markAsRead();

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllAsRead(Request $request)
    {
        AppNotification::query()
            ->forUser($request->user())
            ->unread()
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function open(Request $request, AppNotification $notification)
    {
        $this->authorizeNotification($request, $notification);

        $notification->markAsRead();

        $safeUrl = $this->safeNotificationUrl($notification->url);

        if ($safeUrl) {
            return redirect($safeUrl);
        }

        return redirect()
            ->route('notifications.index')
            ->with('success', 'Notifikasi sudah dibaca.');
    }

    private function authorizeNotification(Request $request, AppNotification $notification): void
    {
        abort_unless(
            (int) $notification->user_id === (int) $request->user()->id,
            403,
            'Anda tidak memiliki akses ke notifikasi ini.'
        );
    }

    private function safeNotificationUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $url = trim($url);

        // Izinkan relative path internal seperti /operations/tickets/1
        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return $url;
        }

        // Izinkan absolute URL yang masih satu host dengan aplikasi.
        $appUrl = rtrim(url('/'), '/');

        if (str_starts_with($url, $appUrl . '/')) {
            return $url;
        }

        return null;
    }
}