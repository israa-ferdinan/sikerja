<?php

namespace App\Exports\Reports;

use App\Models\DailyReport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MonthlyDutySummaryExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithTitle
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
            ->with(['duty'])
            ->whereMonth('report_date', $this->month)
            ->whereYear('report_date', $this->year)
            ->when($this->unitId, function ($query) {
                $query->where('unit_id', $this->unitId);
            })
            ->get();

        return $reports
            ->groupBy('duty_id')
            ->values()
            ->map(function ($dutyReports, $index) {
                $firstReport = $dutyReports->first();

                return [
                    'no' => $index + 1,
                    'tupoksi' => $firstReport?->duty?->name ?? '-',
                    'total_laporan' => $dutyReports->count(),
                    'laporan_normal' => $dutyReports->where('is_delegated', false)->count(),
                    'laporan_delegasi' => $dutyReports->where('is_delegated', true)->count(),
                ];
            })
            ->sortByDesc('total_laporan')
            ->values();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tupoksi',
            'Total Laporan',
            'Laporan Normal',
            'Laporan Delegasi',
        ];
    }

    public function title(): string
    {
        return 'Rekap Tupoksi';
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
                        'fillType' => Fill::FILL_SOLID,
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

                $sheet->getRowDimension(1)->setRowHeight(24);
            },
        ];
    }
}
