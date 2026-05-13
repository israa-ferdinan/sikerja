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

    public int $totalPhotos = 0;

    public ?DailyReport $latestReport = null;

    public function mount(): void
    {
        $user = Auth::user();

        $today = today()->toDateString();

        $startOfMonth = now()->startOfMonth()->toDateString();

        $endOfMonth = now()->endOfMonth()->toDateString();

        $this->todayReports = DailyReport::query()
            ->where('user_id', $user->id)
            ->whereDate('report_date', $today)
            ->count();

        $this->monthlyReports = DailyReport::query()
            ->where('user_id', $user->id)
            ->whereBetween('report_date', [$startOfMonth, $endOfMonth])
            ->count();

        $this->totalPhotos = DailyReportPhoto::query()
            ->whereHas('dailyReport', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();

        $this->latestReport = DailyReport::query()
            ->with([
                'duty:id,name',
                'server:id,name',
                'application:id,name',
            ])
            ->where('user_id', $user->id)
            ->latest('report_date')
            ->latest('id')
            ->first();
    }

    public function render()
    {
        return view('livewire.pegawai.dashboard');
    }
}