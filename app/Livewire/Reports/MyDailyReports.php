<?php

namespace App\Livewire\Reports;

use App\Models\DailyReport;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyDailyReports extends Component
{
    use WithPagination;

    public $month;
    public $year;
    public $search = '';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->month = now()->format('m');
        $this->year = now()->format('Y');
    }

    public function updatedMonth()
    {
        $this->resetPage();
    }

    public function updatedYear()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->month = now()->format('m');
        $this->year = now()->format('Y');
        $this->search = '';

        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        $reports = DailyReport::query()
            ->with([
                'duty',
                'server',
                'application',
                'photos',
            ])
            ->where('user_id', $user->id)
            ->whereMonth('report_date', $this->month)
            ->whereYear('report_date', $this->year)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('notes', 'like', '%' . $this->search . '%');
                });
            })
            ->latest('report_date')
            ->latest('id')
            ->paginate(10);

        return view('livewire.reports.my-daily-reports', [
            'reports' => $reports,
        ])->layout('layouts.app');
    }
}