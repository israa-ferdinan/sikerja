<?php

namespace App\Exports\Reports;

use App\Models\DailyReport;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MonthlyEmployeeSummaryExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithTitle, WithCustomStartCell
{
    public function __construct(
        public int $month,
        public int $year,
        public ?int $unitId = null,
        public ?string $printedBy = null
    ) {
    }

    public function startCell(): string
    {
        return 'A10';
    }

    public function collection(): Collection
    {
        $reports = DailyReport::query()
            ->with([
                'employee',
                'employee.position',
                'unit',
                'photos',
            ])
            ->whereMonth('report_date', $this->month)
            ->whereYear('report_date', $this->year)
            ->when($this->unitId, function ($query) {
                $query->where('unit_id', $this->unitId);
            })
            ->get();

        return $reports
            ->groupBy('employee_id')
            ->values()
            ->map(function ($employeeReports, $index) {
                $firstReport = $employeeReports->first();
                $employee = $firstReport?->employee;

                return [
                    'no' => $index + 1,
                    'nama_pegawai' => $employee?->name ?? '-',
                    'jabatan' => $employee?->position?->name ?? '-',
                    'unit' => $firstReport?->unit?->name ?? '-',
                    'total_laporan' => $employeeReports->count(),
                    'total_foto' => $employeeReports->sum(function ($report) {
                        return $report->photos?->count() ?? 0;
                    }),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pegawai',
            'Jabatan',
            'Unit',
            'Total Laporan',
            'Total Foto',
        ];
    }

    public function title(): string
    {
        return 'Ringkasan Pegawai';
    }

    private function monthName(): string
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ][$this->month] ?? '-';
    }

    private function unitName(): string
    {
        if (! $this->unitId) {
            return 'Semua Unit';
        }

        return Unit::where('id', $this->unitId)->value('name') ?? '-';
    }

    private function totalReports(): int
    {
        return DailyReport::query()
            ->whereMonth('report_date', $this->month)
            ->whereYear('report_date', $this->year)
            ->when($this->unitId, function ($query) {
                $query->where('unit_id', $this->unitId);
            })
            ->count();
    }

    private function totalPhotos(): int
    {
        return DailyReport::query()
            ->withCount('photos')
            ->whereMonth('report_date', $this->month)
            ->whereYear('report_date', $this->year)
            ->when($this->unitId, function ($query) {
                $query->where('unit_id', $this->unitId);
            })
            ->get()
            ->sum('photos_count');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $tableStartRow = 10;
                $tableRange = 'A' . $tableStartRow . ':' . $highestColumn . $highestRow;
                $headerRange = 'A' . $tableStartRow . ':' . $highestColumn . $tableStartRow;

                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', 'REKAP LAPORAN KERJA BULANAN');

                $sheet->setCellValue('A3', 'Periode');
                $sheet->setCellValue('B3', ': ' . $this->monthName() . ' ' . $this->year);

                $sheet->setCellValue('A4', 'Unit');
                $sheet->setCellValue('B4', ': ' . $this->unitName());

                $sheet->setCellValue('A5', 'Total Laporan');
                $sheet->setCellValue('B5', ': ' . $this->totalReports());

                $sheet->setCellValue('A6', 'Total Foto');
                $sheet->setCellValue('B6', ': ' . $this->totalPhotos());

                $sheet->setCellValue('A7', 'Dicetak Oleh');
                $sheet->setCellValue('B7', ': ' . ($this->printedBy ?? '-'));

                $sheet->setCellValue('A8', 'Tanggal Cetak');
                $sheet->setCellValue('B8', ': ' . Carbon::now()->format('d/m/Y H:i'));

                $sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A3:A8')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);

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
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'E5E7EB',
                        ],
                    ],
                ]);

                if ($highestRow >= $tableStartRow) {
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
                }

                $sheet->freezePane('A11');

                $sheet->getRowDimension(1)->setRowHeight(24);
                $sheet->getRowDimension(10)->setRowHeight(24);
            },
        ];
    }
}