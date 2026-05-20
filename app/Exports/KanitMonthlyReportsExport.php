<?php

namespace App\Exports;

use App\Models\DailyReport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KanitMonthlyReportsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected int $unitId;
    protected int $month;
    protected int $year;

    public function __construct(int $unitId, int $month, int $year)
    {
        $this->unitId = $unitId;
        $this->month = $month;
        $this->year = $year;
    }

    public function collection(): Collection
    {
        return DailyReport::query()
            ->with([
                'employee.unit',
                'duty',
                'server',
                'application',
                'photos',
                'delegation',
                'dutyOwnerEmployee',
                'reportedByEmployee',
            ])
            ->whereHas('employee', function ($query) {
                $query->where('unit_id', $this->unitId);
            })
            ->whereMonth('report_date', $this->month)
            ->whereYear('report_date', $this->year)
            ->latest('report_date')
            ->latest('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama Pegawai',
            'Jabatan',
            'Unit',
            'Tupoksi',
            'Jenis Laporan',
            'Pemilik Tupoksi',
            'Dilaporkan Oleh',
            'Periode Delegasi',
            'Server',
            'Aplikasi',
            'Judul Laporan',
            'Deskripsi',
            'Hasil / Keterangan',
            'Jumlah Foto',
            'Status',
        ];
    }

    public function map($report): array
    {
        return [
            optional($report->report_date)->format('d/m/Y'),
            $report->employee?->name ?? '-',
            $report->unit?->name ?? '-',
            $report->duty?->name ?? '-',

            $report->is_delegated ? 'Delegasi' : 'Normal',
            $report->dutyOwnerEmployee?->name ?? $report->employee?->name ?? '-',
            $report->reportedByEmployee?->name ?? $report->employee?->name ?? '-',
            $report->is_delegated
                ? (
                    ($report->delegation?->start_date?->format('d/m/Y') ?? '-') .
                    ' s.d. ' .
                    ($report->delegation?->end_date?->format('d/m/Y') ?? 'Tidak ditentukan')
                )
                : '-',

            $report->server?->name ?? '-',
            $report->application?->name ?? '-',
            $report->title,
            $report->description,
            $report->notes,
            $report->status,
        ];
    }
}