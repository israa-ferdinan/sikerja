<?php

namespace App\Exports\Reports;

use App\Models\DailyReportPhoto;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MonthlyPhotoDataExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithTitle
{
    public function __construct(
        public int $month,
        public int $year,
        public ?int $unitId = null
    ) {
    }

    public function collection(): Collection
    {
        $photos = DailyReportPhoto::query()
            ->with([
                'dailyReport',
                'dailyReport.employee',
                'dailyReport.unit',
                'dailyReport.duty',
            ])
            ->whereHas('dailyReport', function ($query) {
                $query
                    ->whereMonth('report_date', $this->month)
                    ->whereYear('report_date', $this->year)
                    ->when($this->unitId, function ($q) {
                        $q->where('unit_id', $this->unitId);
                    });
            })
            ->orderBy('daily_report_id')
            ->orderBy('sort_order')
            ->get();

        return $photos->map(function ($photo, $index) {
            $report = $photo->dailyReport;

            return [
                'no' => $index + 1,
                'tanggal_laporan' => $report?->report_date
                    ? Carbon::parse($report->report_date)->format('d/m/Y')
                    : '-',
                'nama_pegawai' => $report?->employee?->name ?? '-',
                'unit' => $report?->unit?->name ?? '-',
                'tupoksi' => $report?->duty?->name ?? '-',
                'judul_laporan' => $report?->title ?? '-',
                'nama_file' => basename($photo->file_path),
                'path_storage' => $photo->file_path ?? '-',
                'url_file' => $photo->file_path
                    ? Storage::url($photo->file_path)
                    : '-',
                'urutan_foto' => $photo->sort_order ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Laporan',
            'Nama Pegawai',
            'Unit',
            'Tupoksi',
            'Judul Laporan',
            'Nama File',
            'Path Storage',
            'URL File',
            'Urutan Foto',
        ];
    }

    public function title(): string
    {
        return 'Data Foto';
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

                $sheet->getStyle('H:I')->getAlignment()->setWrapText(true);

                $sheet->getColumnDimension('F')->setWidth(35);
                $sheet->getColumnDimension('H')->setWidth(45);
                $sheet->getColumnDimension('I')->setWidth(55);

                $sheet->getRowDimension(1)->setRowHeight(24);
            },
        ];
    }
}