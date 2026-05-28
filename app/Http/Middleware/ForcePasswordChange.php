<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        if (! $user->must_change_password) {
            return $next($request);
        }

        if (
            $request->routeIs('profile.show')
            || $request->routeIs('logout')
        ) {
            return $next($request);
        }

        return redirect()
            ->route('profile.show')
            ->with('warning', 'Silakan ganti password terlebih dahulu sebelum menggunakan aplikasi.');
    }
}