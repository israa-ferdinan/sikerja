<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsKanit
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user()?->load('role', 'employee.unit');

        abort_if(!$user, 403, 'Anda harus login terlebih dahulu.');

        abort_if(!$user->isKanit(), 403, 'Anda tidak memiliki akses sebagai Kanit.');

        abort_if(!$user->employee, 403, 'User Kanit belum terhubung ke data pegawai.');

        abort_if(!$user->employee->unit_id, 403, 'Data pegawai Kanit belum terhubung ke unit.');

        return $next($request);
    }
}