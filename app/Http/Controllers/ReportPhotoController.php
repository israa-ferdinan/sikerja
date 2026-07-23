<?php

namespace App\Http\Controllers;

use App\Models\DailyReportPhoto;
use Illuminate\Support\Facades\Storage;

class ReportPhotoController extends Controller
{
    public function show(DailyReportPhoto $photo)
    {
        $user = auth()->user();

        abort_if(!$user, 401);

        $photo->loadMissing([
            'dailyReport',
        ]);

        $report = $photo->dailyReport;

        abort_if(!$report, 404, 'Data laporan tidak ditemukan.');

        $user->loadMissing([
            'role',
            'employee',
        ]);

        $roleName = $user->role?->name;
        $employee = $user->employee;

        $canAccess = match ($roleName) {
            'admin' => true,

            'kanit', 'gkm' => $employee
                && $employee->unit_id
                && (int) $report->unit_id === (int) $employee->unit_id,

            'pegawai' => $employee
                && (int) $report->employee_id === (int) $employee->id,

            default => false,
        };

        abort_if(!$canAccess, 403, 'Anda tidak memiliki akses ke foto laporan ini.');

        $disk = Storage::disk('public');

        abort_if(
            !$disk->exists($photo->file_path),
            404,
            'File foto laporan tidak ditemukan.'
        );

        return $disk->response(
            $photo->file_path,
            basename($photo->file_path),
            [
                'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
            ]
        );
    }
}