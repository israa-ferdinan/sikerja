<?php

namespace App\Exports\Reports;

use App\Models\DailyReport;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MonthlyReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithTitle
{
    public function __construct(
        public int $month,
        public int $year,
        public ?int $unitId = null
    ) {
    }

    public function collection(): Collection
    {
        $reports = DailyReport::query()
            ->with([
                'employee',
                'employee.jobPosition',
                'unit',
                'duty.classification',
                'server',
                'application',
                'photos',
                'delegation',
                'dutyOwnerEmployee',
                'reportedByEmployee',
            ])
            ->whereMonth('report_date', $this->month)
            ->whereYear('report_date', $this->year)
            ->when($this->unitId, function ($query) {
                $query->where('unit_id', $this->unitId);
            })
            ->orderBy('report_date')
            ->orderBy('employee_id')
            ->get();

        return $reports->map(function ($report, $index) {
            return [
                'no' => $index + 1,
                'tanggal_laporan' => $report->report_date
                    ? Carbon::parse($report->report_date)->format('d/m/Y')
                    : '-',
                'nama_pegawai' => $report->employee?->name ?? '-',
                'jabatan' => $report->employee?->jobPosition?->name ?? '-',
                'unit' => $report->unit?->name ?? '-',
                'tupoksi' => $report->duty?->name ?? '-',
                'klasifikasi_tupoksi' => $report->duty?->classification?->name ?? '-',
                'jenis_objek_tupoksi' => $report->duty?->object_type_label ?? '-',
                'objek_detail_laporan' => $this->detailObjectLabel($report),
                'jenis_laporan' => $report->is_delegated ? 'Delegasi' : 'Normal',
                'pemilik_tupoksi' => $report->dutyOwnerEmployee?->name
                    ?? $report->employee?->name
                    ?? '-',
                'dilaporkan_oleh' => $report->reportedByEmployee?->name
                    ?? $report->employee?->name
                    ?? '-',
                'periode_delegasi' => $report->is_delegated
                    ? (
                        ($report->delegation?->start_date ? Carbon::parse($report->delegation->start_date)->format('d/m/Y') : '-') .
                        ' s.d. ' .
                        ($report->delegation?->end_date ? Carbon::parse($report->delegation->end_date)->format('d/m/Y') : 'Tidak ditentukan')
                    )
                    : '-',
                'server' => $report->server?->name ?? '-',
                'aplikasi' => $report->application?->name ?? '-',
                'judul_laporan' => $report->title ?? '-',
                'deskripsi' => $report->description ?? '-',
                'hasil' => $report->result ?? '-',
                'status' => $report->status ?? '-',
                'jumlah_foto' => $report->photos?->count() ?? 0,
                'tanggal_input' => $report->created_at
                    ? $report->created_at->format('d/m/Y H:i')
                    : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Laporan',
            'Nama Pegawai',
            'Jabatan',
            'Unit',
            'Tupoksi',
            'Klasifikasi Tupoksi',
            'Jenis Objek Tupoksi',
            'Objek Detail Laporan',
            'Jenis Laporan',
            'Pemilik Tupoksi',
            'Dilaporkan Oleh',
            'Periode Delegasi',
            'Server',
            'Aplikasi',
            'Judul Laporan',
            'Deskripsi',
            'Hasil',
            'Status',
            'Jumlah Foto',
            'Tanggal Input',
        ];
    }

    public function title(): string
    {
        return 'Detail Laporan';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $tableRange = 'A1:' . $highestColumn . $highestRow;
                $headerRange = 'A1:' . $highestColumn . '1';

                $sheet->freezePane('A2');

                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'E5E7EB',
                        ],
                    ],
                ]);

                $sheet->getStyle($tableRange)->applyFromArray([
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $sheet->getStyle('P:R')->getAlignment()->setWrapText(true);

                $sheet->getColumnDimension('F')->setWidth(35);
                $sheet->getColumnDimension('G')->setWidth(25);
                $sheet->getColumnDimension('H')->setWidth(25);
                $sheet->getColumnDimension('I')->setWidth(30);
                $sheet->getColumnDimension('P')->setWidth(35);
                $sheet->getColumnDimension('Q')->setWidth(45);
                $sheet->getColumnDimension('R')->setWidth(45);

                $sheet->getRowDimension(1)->setRowHeight(24);
            },
        ];
    }

    private function detailObjectLabel(DailyReport $report): string
    {
        if ($report->server && $report->application) {
            return $report->server->name . ' / ' . $report->application->name;
        }

        if ($report->server) {
            return $report->server->name;
        }

        if ($report->application) {
            return $report->application->name;
        }

        return 'Detail dicatat pada uraian laporan';
    }
}
