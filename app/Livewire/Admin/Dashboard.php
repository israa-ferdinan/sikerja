<?php

namespace App\Livewire\Admin;

use App\Models\Application;
use App\Models\DailyReport;
use App\Models\Employee;
use App\Models\JobDuty;
use App\Models\ReportTemplate;
use App\Models\Server;
use App\Models\Unit;
use Livewire\Component;

class Dashboard extends Component
{
    public int $totalEmployees = 0;

    public int $totalUnits = 0;

    public int $totalDuties = 0;

    public int $monthlyReports = 0;

    public int $totalServers = 0;

    public int $totalApplications = 0;

    public int $totalTemplates = 0;

    public function mount(): void
    {
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $this->totalEmployees = Employee::query()->count();

        $this->totalUnits = Unit::query()->count();

        $this->totalDuties = JobDuty::query()->count();

        $this->monthlyReports = DailyReport::query()
            ->whereBetween('report_date', [$startOfMonth, $endOfMonth])
            ->count();

        $this->totalServers = Server::query()->count();

        $this->totalApplications = Application::query()->count();

        $this->totalTemplates = ReportTemplate::query()->count();
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}