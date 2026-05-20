<?php

namespace App\Livewire\Pegawai;

use App\Models\DailyReport;
use App\Models\DailyReportPhoto;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public int $todayReports = 0;

    public int $monthlyReports = 0;

    public int $normalReports = 0;

    public int $delegatedReports = 0;

    public int $totalPhotos = 0;

    public ?DailyReport $latestReport = null;

    public function mount(): void
    {
        $user = Auth::user();

        $employeeId = $user->employee_id;

        $employeeReportScope = function ($query) use ($user, $employeeId) {
            if ($employeeId) {
                $query->where(function ($q) use ($employeeId, $user) {
                    $q->where('reported_by_employee_id', $employeeId)
                        ->orWhere(function ($fallback) use ($employeeId, $user) {
                            $fallback->whereNull('reported_by_employee_id')
                                ->where(function ($old) use ($employeeId, $user) {
                                    $old->where('employee_id', $employeeId)
                                        ->orWhere('user_id', $user->id);
                                });
                        });
                });
            } else {
                $query->where('user_id', $user->id);
            }
        };

        $today = today()->toDateString();

        $startOfMonth = now()->startOfMonth()->toDateString();

        $endOfMonth = now()->endOfMonth()->toDateString();

        $this->todayReports = DailyReport::query()
            ->where($employeeReportScope)
            ->whereDate('report_date', $today)
            ->count();

        $this->monthlyReports = DailyReport::query()
            ->where($employeeReportScope)
            ->whereBetween('report_date', [$startOfMonth, $endOfMonth])
            ->count();

        $this->normalReports = DailyReport::query()
            ->where($employeeReportScope)
            ->whereBetween('report_date', [$startOfMonth, $endOfMonth])
            ->where('is_delegated', false)
            ->count();

        $this->delegatedReports = DailyReport::query()
            ->where($employeeReportScope)
            ->whereBetween('report_date', [$startOfMonth, $endOfMonth])
            ->where('is_delegated', true)
            ->count();

        $this->totalPhotos = DailyReportPhoto::query()
            ->whereHas('dailyReport', function ($query) use ($employeeReportScope) {
                $query->where($employeeReportScope);
            })
            ->count();

        $this->latestReport = DailyReport::query()
            ->with([
                'duty:id,name',
                'server:id,name',
                'application:id,name',
                'dutyOwnerEmployee:id,name',
                'reportedByEmployee:id,name',
            ])
            ->where($employeeReportScope)
            ->latest('report_date')
            ->latest('id')
            ->first();
    }

    public function render()
    {
        return view('livewire.pegawai.dashboard');
    }
}