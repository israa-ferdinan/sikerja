<?php

namespace App\Livewire\Reports;

use App\Models\DailyReport;
use Illuminate\Support\Facades\Storage;
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

    public function delete($id)
    {
        $user = auth()->user();

        if (! $user->employee_id) {
            abort(403, 'Akun belum terhubung dengan data pegawai.');
        }

        $report = DailyReport::query()
            ->with('photos')
            ->where('employee_id', $user->employee_id)
            ->findOrFail($id);

        foreach ($report->photos as $photo) {
            if ($photo->file_path && Storage::disk('public')->exists($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }

            $photo->delete();
        }

        $report->delete();

        session()->flash('success', 'Laporan berhasil dihapus.');
    }
    
    public function render()
    {
        $user = auth()->user();

        if (! $user->employee_id) {
            return view('livewire.reports.my-daily-reports', [
                'reports' => DailyReport::query()->whereRaw('1 = 0')->paginate(10),
                'missingEmployee' => true,
            ])->layout('layouts.app');
        }

        $reports = DailyReport::query()
            ->with([
                'duty',
                'server',
                'application',
                'photos',
            ])
            ->where('employee_id', $user->employee_id)
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