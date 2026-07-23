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
        public ?string $printedBy = null,
        public ?int $employeeId = null,
        public string $exportMode = 'unit'
    ) {
    }

    public function startCell(): string
    {
        return 'A10';
    }

    public function title(): string
    {
        return $this->exportMode === 'employee'
            ? 'Laporan Pegawai'
            : 'Laporan Bulanan';
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
            ->when($this->exportMode === 'employee' && $this->employeeId, function ($query) {
                $query->where('employee_id', $this->employeeId);
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
                $sheet->setCellValue(
                    'A1',
                    $this->exportMode === 'employee'
                        ? 'LAPORAN KERJA BULANAN PEGAWAI'
                        : 'REKAP LAPORAN KERJA BULANAN'
                );

                $sheet->setCellValue('A3', 'Periode');
                $sheet->setCellValue('B3', ': ' . $this->monthName() . ' ' . $this->year);

                $sheet->setCellValue('A4', 'Unit');
                $sheet->setCellValue('B4', ': ' . $this->unitName());
                if ($this->exportMode === 'employee') {
                    $sheet->setCellValue('D3', 'Pegawai');
                    $sheet->setCellValue('E3', ': ' . ($this->employeeName() ?? '-'));

                    $sheet->setCellValue('D4', 'NIP');
                    $sheet->setCellValue('E4', ': ' . ($this->employeeNip() ?? '-'));
                }

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

                if ($this->exportMode === 'employee') {
                    $this->appendEmployeeReportSignatureBlock($sheet, $highestRow);
                } else {
                    $this->appendApprovalSignatureBlock($sheet, $highestRow);
                }
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
            })
            ->when($this->exportMode === 'employee' && $this->employeeId, function ($query) {
                $query->where('employee_id', $this->employeeId);
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

    private function appendEmployeeSignatureBlock($sheet, int $highestRow): int
    {
        $employees = $this->employeeSignatureRows();

        $startRow = $highestRow + 3;

        $sheet->mergeCells("A{$startRow}:D{$startRow}");
        $sheet->setCellValue("A{$startRow}", 'Tanda Tangan Pegawai Pelapor');

        $sheet->getStyle("A{$startRow}:D{$startRow}")->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
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

        if ($employees->isEmpty()) {
            $emptyRow = $startRow + 1;

            $sheet->mergeCells("A{$emptyRow}:D{$emptyRow}");
            $sheet->setCellValue("A{$emptyRow}", 'Tidak ada pegawai pelapor pada periode ini.');

            $sheet->getStyle("A{$emptyRow}:D{$emptyRow}")->applyFromArray([
                'font' => [
                    'italic' => true,
                    'color' => [
                        'rgb' => '6B7280',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);

            return $emptyRow;
        }

        $headerRow = $startRow + 1;

        $sheet->setCellValue("A{$headerRow}", 'No.');
        $sheet->setCellValue("B{$headerRow}", 'Nama Pegawai');
        $sheet->setCellValue("C{$headerRow}", 'Jumlah Laporan');
        $sheet->setCellValue("D{$headerRow}", 'Tanda Tangan');

        $sheet->getStyle("A{$headerRow}:D{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'F3F4F6',
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $row = $headerRow + 1;

        foreach ($employees as $index => $employeeRow) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $employeeRow['name']);
            $sheet->setCellValue("C{$row}", $employeeRow['total_reports']);

            $sheet->getRowDimension($row)->setRowHeight(64);

            $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);

            $sheet->getStyle("A{$row}:A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}:D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($employeeRow['signature_path']) {
                $this->insertSignatureImageCentered(
                    $sheet,
                    $employeeRow['signature_path'],
                    "D{$row}:D{$row}",
                    'Tanda Tangan Pegawai'
                );
            } else {
                $sheet->setCellValue("D{$row}", 'Belum ada tanda tangan');

                $sheet->getStyle("D{$row}:D{$row}")->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'color' => [
                            'rgb' => '92400E',
                        ],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'FEF3C7',
                        ],
                    ],
                ]);
            }

            $row++;
        }

        $lastRow = $row - 1;

        $sheet->getStyle("A{$headerRow}:D{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        return $lastRow;
    }

    private function employeeSignatureRows(): Collection
    {
        return DailyReport::query()
            ->with('employee')
            ->whereMonth('report_date', $this->month)
            ->whereYear('report_date', $this->year)
            ->when($this->unitId, function ($query) {
                $query->where('unit_id', $this->unitId);
            })
            ->whereNotNull('employee_id')
            ->get()
            ->groupBy('employee_id')
            ->map(function ($reports) {
                $employee = $reports->first()->employee;

                return [
                    'name' => $employee?->name ?? '-',
                    'signature_path' => $employee?->signature_path,
                    'total_reports' => $reports->count(),
                ];
            })
            ->sortBy('name')
            ->values();
    }

    private function employee()
    {
        if (! $this->employeeId) {
            return null;
        }

        return \App\Models\Employee::query()
            ->with('unit')
            ->where('id', $this->employeeId)
            ->first();
    }

    private function employeeName(): ?string
    {
        return $this->employee()?->name;
    }

    private function employeeNip(): ?string
    {
        return $this->employee()?->nip;
    }

    private function employeePosition(): ?string
    {
        $employee = $this->employee();

        return $employee?->jobPosition?->name
            ?? $employee?->position
            ?? null;
    }

    private function employeeSignaturePath(): ?string
    {
        return $this->employee()?->signature_path;
    }


    private function appendEmployeeReportSignatureBlock($sheet, int $highestRow): void
    {
        $approval = $this->approval();
        $employee = $this->employee();

        $startRow = $highestRow + 3;

        if (! $approval) {
            $sheet->mergeCells("A{$startRow}:G" . ($startRow + 1));
            $sheet->setCellValue(
                "A{$startRow}",
                'Belum difinalisasi. Laporan per pegawai ini belum memiliki tanda tangan Kanit.'
            );

            $sheet->getRowDimension($startRow)->setRowHeight(34);

            $sheet->getStyle("A{$startRow}:G" . ($startRow + 1))->applyFromArray([
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

        $titleRow = $startRow;
        $roleRow = $startRow + 1;
        $imageStartRow = $startRow + 3;
        $imageEndRow = $startRow + 7;
        $nameRow = $startRow + 8;
        $nipRow = $startRow + 9;
        $dateRow = $startRow + 10;

        $sheet->mergeCells("A{$titleRow}:C{$titleRow}");
        $sheet->mergeCells("E{$titleRow}:G{$titleRow}");
        $sheet->setCellValue("A{$titleRow}", 'Mengetahui,');
        $sheet->setCellValue("E{$titleRow}", 'Pelapor,');

        $sheet->mergeCells("A{$roleRow}:C{$roleRow}");
        $sheet->mergeCells("E{$roleRow}:G{$roleRow}");
        $sheet->setCellValue(
            "A{$roleRow}",
            $approval->approver_unit_name
                ? 'Kepala Unit ' . $approval->approver_unit_name
                : 'Kepala Unit'
        );
        $sheet->setCellValue("E{$roleRow}", $this->employeePosition() ?? 'Pegawai');

        $sheet->mergeCells("A{$nameRow}:C{$nameRow}");
        $sheet->mergeCells("E{$nameRow}:G{$nameRow}");
        $sheet->setCellValue("A{$nameRow}", $approval->approver_name ?? '-');
        $sheet->setCellValue("E{$nameRow}", $employee?->name ?? '-');

        $sheet->mergeCells("A{$nipRow}:C{$nipRow}");
        $sheet->mergeCells("E{$nipRow}:G{$nipRow}");
        $sheet->setCellValue(
            "A{$nipRow}",
            $approval->approver_nip
                ? 'NIP. ' . $approval->approver_nip
                : 'NIP. -'
        );
        $sheet->setCellValue(
            "E{$nipRow}",
            $employee?->nip
                ? 'NIP. ' . $employee->nip
                : 'NIP. -'
        );

        $sheet->mergeCells("A{$dateRow}:G{$dateRow}");
        $sheet->setCellValue(
            "A{$dateRow}",
            'Tanggal finalisasi: ' . ($approval->approved_at?->format('d/m/Y H:i') ?? '-')
        );

        for ($row = $imageStartRow; $row <= $imageEndRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(22);
        }

        $sheet->getStyle("A{$titleRow}:G{$dateRow}")->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $sheet->getStyle("A{$nameRow}:G{$nameRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'underline' => true,
            ],
        ]);

        $this->insertSignatureImageCentered(
            $sheet,
            $approval->approver_signature_path,
            "A{$imageStartRow}:C{$imageEndRow}",
            'Tanda Tangan Kanit'
        );

        if ($employee?->signature_path) {
            $this->insertSignatureImageCentered(
                $sheet,
                $employee->signature_path,
                "E{$imageStartRow}:G{$imageEndRow}",
                'Tanda Tangan Pegawai'
            );
        } else {
            $sheet->mergeCells("E{$imageStartRow}:G{$imageEndRow}");
            $sheet->setCellValue("E{$imageStartRow}", 'Belum ada tanda tangan pegawai');

            $sheet->getStyle("E{$imageStartRow}:G{$imageEndRow}")->applyFromArray([
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
            ]);
        }
    }

    private function appendApprovalSignatureBlock($sheet, int $highestRow): void
    {
        $approval = $this->approval();

        $startRow = $highestRow + 3;

        $sheet->mergeCells("A{$startRow}:G{$startRow}");
        $sheet->setCellValue("A{$startRow}", 'Status Finalisasi');

        $sheet->mergeCells('A' . ($startRow + 1) . ':G' . ($startRow + 1));

        if (! $approval) {
            $sheet->setCellValue(
                'A' . ($startRow + 1),
                'Belum difinalisasi. Export ini belum memiliki tanda tangan Kanit.'
            );

            $sheet->getRowDimension($startRow)->setRowHeight(22);
            $sheet->getRowDimension($startRow + 1)->setRowHeight(34);

            $sheet->getStyle("A{$startRow}:G" . ($startRow + 1))->applyFromArray([
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

        $sheet->setCellValue('A' . ($startRow + 1), 'Sudah difinalisasi dan disahkan oleh Kanit.');

        $signatureTitleRow = $startRow + 3;
        $signatureImageRow = $startRow + 5;
        $nameRow = $startRow + 10;
        $positionRow = $startRow + 11;
        $dateRow = $startRow + 12;

        $sheet->mergeCells("A{$signatureTitleRow}:G{$signatureTitleRow}");
        $sheet->setCellValue("A{$signatureTitleRow}", 'Mengetahui,');

        $sheet->mergeCells('A' . ($signatureTitleRow + 1) . ':G' . ($signatureTitleRow + 1));
        $sheet->setCellValue(
            'A' . ($signatureTitleRow + 1),
            $approval->approver_unit_name
                ? 'Kepala Unit ' . $approval->approver_unit_name
                : 'Kepala Unit'
        );

        $sheet->mergeCells("A{$nameRow}:G{$nameRow}");
        $sheet->setCellValue("A{$nameRow}", $approval->approver_name ?? '-');

        $sheet->mergeCells("A{$positionRow}:G{$positionRow}");
        $sheet->setCellValue(
            "A{$positionRow}",
            $approval->approver_nip
                ? 'NIP. ' . $approval->approver_nip
                : 'NIP. -'
        );

        $sheet->mergeCells("A{$dateRow}:G{$dateRow}");
        $sheet->setCellValue(
            "A{$dateRow}",
            'Tanggal finalisasi: ' . ($approval->approved_at?->format('d/m/Y H:i') ?? '-')
        );

        $sheet->getStyle("A{$startRow}:G{$dateRow}")->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $sheet->getStyle("A{$startRow}:G{$startRow}")->applyFromArray([
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

        $sheet->getStyle('A' . ($startRow + 1) . ':G' . ($startRow + 1))->applyFromArray([
            'font' => [
                'color' => [
                    'rgb' => '047857',
                ],
            ],
        ]);

        $sheet->getStyle("A{$nameRow}:G{$nameRow}")->applyFromArray([
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
            "A{$signatureImageRow}:G" . ($signatureImageRow + 4)
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

    private function insertSignatureImageCentered($sheet, ?string $signaturePath, string $range, string $label = 'Tanda Tangan Kanit'): void
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
        $drawing->setName($label);
        $drawing->setDescription($label);
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