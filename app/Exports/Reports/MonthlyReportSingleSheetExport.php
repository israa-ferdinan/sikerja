<?php

namespace App\Exports\Reports;

use App\Models\DailyReport;
use App\Models\Unit;
use App\Models\MonthlyReportApproval;

use Carbon\Carbon;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class MonthlyReportSingleSheetExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithTitle, WithCustomStartCell
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

    public function title(): string
    {
        return 'Laporan Bulanan';
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
            ->orderBy('id')
            ->get();

        return $reports->map(function ($report, $index) {
            return [
                'no' => $index + 1,
                'tanggal' => $report->report_date
                    ? Carbon::parse($report->report_date)->format('d/m/Y')
                    : '-',
                'tupoksi' => $report->duty?->name ?? '-',
                'judul_kegiatan' => $report->title ?? '-', 
                'deskripsi_kegiatan' => $report->description ?? '-',
                'link_foto_bukti_kegiatan' => $this->photoUrls($report),
                'nama_pegawai' => $report->employee?->name ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No.',
            'Tanggal',
            'Tupoksi',
            'Judul Kegiatan',
            'Deskripsi Kegiatan',
            'Link Foto Bukti Kegiatan',
            'Nama Pegawai',
            ];
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

                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'REKAP LAPORAN KERJA BULANAN');

                $sheet->setCellValue('A3', 'Periode');
                $sheet->setCellValue('B3', ': ' . $this->monthName() . ' ' . $this->year);

                $sheet->setCellValue('A4', 'Unit');
                $sheet->setCellValue('B4', ': ' . $this->unitName());

                $sheet->setCellValue('A5', 'Total Laporan');
                $sheet->setCellValue('B5', ': ' . $this->totalReports());

                $sheet->setCellValue('A6', 'Total Foto');
                $sheet->setCellValue('B6', ': ' . $this->totalPhotos());

                $sheet->setCellValue('D5', 'Laporan Normal');
                $sheet->setCellValue('E5', ': ' . $this->totalNormalReports());

                $sheet->setCellValue('D6', 'Laporan Delegasi');
                $sheet->setCellValue('E6', ': ' . $this->totalDelegatedReports());

                $sheet->setCellValue('A7', 'Dicetak Oleh');
                $sheet->setCellValue('B7', ': ' . ($this->printedBy ?? '-'));

                $sheet->setCellValue('A8', 'Tanggal Cetak');
                $sheet->setCellValue('B8', ': ' . Carbon::now()->format('d/m/Y H:i'));

                $sheet->getStyle('A1:G1')->applyFromArray([
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

                $sheet->getStyle('C:F')->getAlignment()->setWrapText(true);

                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(16);
                $sheet->getColumnDimension('C')->setWidth(38);
                $sheet->getColumnDimension('D')->setWidth(60);
                $sheet->getColumnDimension('E')->setWidth(70);
                $sheet->getColumnDimension('F')->setWidth(28);
                $sheet->getColumnDimension('G')->setWidth(28);

                $sheet->getRowDimension(1)->setRowHeight(24);
                $sheet->getRowDimension($tableStartRow)->setRowHeight(24);

                $sheet->freezePane('A11');

                $this->appendApprovalSignatureBlock($sheet, $highestRow);
            },
        ];
    }

    private function photoUrls(DailyReport $report): string
    {
        $urls = $report->photos
            ?->map(function ($photo) {
                return route('reports.photos.show', $photo);
            })
            ->filter()
            ->values();

        if (! $urls || $urls->isEmpty()) {
            return '-';
        }

        return $urls->implode("\n");
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

    private function baseReportQuery()
    {
        return DailyReport::query()
            ->whereMonth('report_date', $this->month)
            ->whereYear('report_date', $this->year)
            ->when($this->unitId, function ($query) {
                $query->where('unit_id', $this->unitId);
            });
    }

    private function totalReports(): int
    {
        return (clone $this->baseReportQuery())->count();
    }

    private function totalPhotos(): int
    {
        return (clone $this->baseReportQuery())
            ->withCount('photos')
            ->get()
            ->sum('photos_count');
    }

    private function totalNormalReports(): int
    {
        return (clone $this->baseReportQuery())
            ->where('is_delegated', false)
            ->count();
    }

    private function totalDelegatedReports(): int
    {
        return (clone $this->baseReportQuery())
            ->where('is_delegated', true)
            ->count();
    }

    private function appendApprovalSignatureBlock($sheet, int $highestRow): void
    {
        $approval = $this->approval();

        $startRow = $highestRow + 3;

        $sheet->mergeCells("E{$startRow}:G{$startRow}");
        $sheet->setCellValue("E{$startRow}", 'Status Finalisasi');

        $sheet->mergeCells('E' . ($startRow + 1) . ':G' . ($startRow + 1));

        if (! $approval) {
            $sheet->setCellValue(
                'E' . ($startRow + 1),
                'Belum difinalisasi. Export ini belum memiliki tanda tangan Kanit.'
            );

            $sheet->getStyle("D{$startRow}:F" . ($startRow + 1))->applyFromArray([
                'font' => [
                    'italic' => true,
                    'color' => [
                        'rgb' => '92400E',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'FEF3C7',
                    ],
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => [
                            'rgb' => 'F59E0B',
                        ],
                    ],
                ],
            ]);

            return;
        }

        $sheet->setCellValue('E' . ($startRow + 1), 'Sudah difinalisasi dan disahkan oleh Kanit.');

        $signatureTitleRow = $startRow + 3;
        $signatureImageRow = $startRow + 5;
        $nameRow = $startRow + 10;
        $positionRow = $startRow + 11;
        $dateRow = $startRow + 12;

        $sheet->mergeCells("E{$signatureTitleRow}:G{$signatureTitleRow}");
        $sheet->setCellValue("E{$signatureTitleRow}", 'Mengetahui,');

        $sheet->mergeCells('E' . ($signatureTitleRow + 1) . ':G' . ($signatureTitleRow + 1));
        $sheet->setCellValue(
            'E' . ($signatureTitleRow + 1),
            $approval->approver_unit_name
                ? 'Kepala Unit ' . $approval->approver_unit_name
                : 'Kepala Unit'
        );

        $sheet->mergeCells("E{$nameRow}:G{$nameRow}");
        $sheet->setCellValue("E{$nameRow}", $approval->approver_name ?? '-');

        $sheet->mergeCells("E{$positionRow}:G{$positionRow}");
        $sheet->setCellValue(
            "E{$positionRow}",
            trim(
                ($approval->approver_nip ? 'NIP. ' . $approval->approver_nip . ' | ' : '')
                . ($approval->approver_position ?? '-')
            )
        );

        $sheet->mergeCells("E{$dateRow}:G{$dateRow}");
        $sheet->setCellValue(
            "E{$dateRow}",
            'Tanggal finalisasi: ' . ($approval->approved_at?->format('d/m/Y H:i') ?? '-')
        );

        $sheet->getStyle("E{$startRow}:G{$dateRow}")->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $sheet->getStyle("E{$startRow}:G{$startRow}")->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E5E7EB',
                ],
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $sheet->getStyle('E' . ($startRow + 1) . ':G' . ($startRow + 1))->applyFromArray([
            'font' => [
                'color' => [
                    'rgb' => '047857',
                ],
            ],
        ]);

        $sheet->getStyle("E{$nameRow}:G{$nameRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'underline' => true,
            ],
        ]);

        for ($row = $signatureImageRow; $row <= $signatureImageRow + 4; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(22);
        }

        $this->insertSignatureImageCentered(
            $sheet,
            $approval->approver_signature_path,
            "E{$signatureImageRow}:G" . ($signatureImageRow + 4)
        );
    }

    private function approval(): ?MonthlyReportApproval
    {
        if (! $this->unitId) {
            return null;
        }

        return MonthlyReportApproval::query()
            ->where('unit_id', $this->unitId)
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->where('status', 'approved')
            ->first();
    }

    private function insertSignatureImageCentered($sheet, ?string $signaturePath, string $range): void
    {
        if (! $signaturePath) {
            return;
        }

        if (! Storage::disk('public')->exists($signaturePath)) {
            return;
        }

        $extension = strtolower(pathinfo($signaturePath, PATHINFO_EXTENSION));

        if (! in_array($extension, ['png', 'jpg', 'jpeg'], true)) {
            return;
        }

        $realPath = Storage::disk('public')->path($signaturePath);

        if (! file_exists($realPath)) {
            return;
        }

        [$startCell, $endCell] = explode(':', $range);

        preg_match('/([A-Z]+)(\d+)/', $startCell, $startMatches);
        preg_match('/([A-Z]+)(\d+)/', $endCell, $endMatches);

        $startColumn = $startMatches[1];
        $startRow = (int) $startMatches[2];
        $endColumn = $endMatches[1];
        $endRow = (int) $endMatches[2];

        $targetHeight = 90;

        [$originalWidth, $originalHeight] = getimagesize($realPath);

        if (! $originalWidth || ! $originalHeight) {
            return;
        }

        $displayWidth = (int) round(($originalWidth / $originalHeight) * $targetHeight);
        $displayHeight = $targetHeight;

        $rangeWidth = $this->calculateRangeWidthInPixels($sheet, $startColumn, $endColumn);
        $rangeHeight = $this->calculateRangeHeightInPixels($sheet, $startRow, $endRow);

        $offsetX = max(0, (int) floor(($rangeWidth - $displayWidth) / 2));
        $offsetY = max(0, (int) floor(($rangeHeight - $displayHeight) / 2));

        $drawing = new Drawing();
        $drawing->setName('Tanda Tangan Kanit');
        $drawing->setDescription('Tanda tangan Kanit');
        $drawing->setPath($realPath);
        $drawing->setHeight($displayHeight);
        $drawing->setCoordinates($startCell);
        $drawing->setOffsetX($offsetX);
        $drawing->setOffsetY($offsetY);
        $drawing->setWorksheet($sheet);
    }

    private function calculateRangeWidthInPixels($sheet, string $startColumn, string $endColumn): int
    {
        $startIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($startColumn);
        $endIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($endColumn);

        $total = 0;

        for ($col = $startIndex; $col <= $endIndex; $col++) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $width = $sheet->getColumnDimension($columnLetter)->getWidth();

            if (! $width || $width < 0) {
                $width = 8.43;
            }

            $total += $this->excelColumnWidthToPixels($width);
        }

        return (int) $total;
    }

    private function calculateRangeHeightInPixels($sheet, int $startRow, int $endRow): int
    {
        $total = 0;

        for ($row = $startRow; $row <= $endRow; $row++) {
            $height = $sheet->getRowDimension($row)->getRowHeight();

            if (! $height || $height < 0) {
                $height = 15;
            }

            $total += $this->pointsToPixels($height);
        }

        return (int) $total;
    }

    private function excelColumnWidthToPixels(float $width): int
    {
        return (int) round(($width * 7) + 5);
    }

    private function pointsToPixels(float $points): int
    {
        return (int) round($points * 96 / 72);
    }

}