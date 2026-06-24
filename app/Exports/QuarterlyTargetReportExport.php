<?php

namespace App\Exports;

use App\Services\QuarterlyTargetReportService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class QuarterlyTargetReportExport implements FromArray, ShouldAutoSize, WithColumnWidths, WithEvents, WithTitle
{
    private Collection $rows;

    public function __construct(
        private readonly ?int $unitId,
        private readonly int $year,
        private readonly int $quarter,
        private readonly string $unitName,
        private readonly ?string $kanitName = null,
        private readonly ?string $kanitNip = null,
    ) {
        $this->rows = app(QuarterlyTargetReportService::class)
            ->buildRows($this->unitId, $this->year, $this->quarter);
    }

    public function title(): string
    {
        return 'Laporan Capaian Target';
    }

    public function array(): array
    {
        $service = app(QuarterlyTargetReportService::class);

        $quarterLabel = strtoupper($service->quarterLabel($this->quarter));
        $periodLabel = $service->quarterPeriodLabel($this->year, $this->quarter);

        $data = [];

        $data[] = ['KEMENTERIAN PERHUBUNGAN'];
        $data[] = ['BADAN PENGEMBANGAN SDM PERHUBUNGAN'];
        $data[] = ['SEKOLAH TINGGI ILMU PELAYARAN'];
        $data[] = ['INDONESIA'];
        $data[] = [];
        $data[] = ["LAPORAN 3 BULANAN {$quarterLabel} TAHUN {$this->year}"];
        $data[] = [];
        $data[] = ['SATUAN KERJA', ': ' . $this->unitName];
        $data[] = ['PERIODE', ': ' . $periodLabel];
        $data[] = ['TANGGAL EXPORT', ': ' . now()->format('d/m/Y H:i')];
        $data[] = [];

        $data[] = [
            'NO',
            'SASARAN MUTU / KLASIFIKASI',
            'KEGIATAN UNTUK MENCAPAI SASARAN MUTU',
            'METODE CAPAIAN',
            'TARGET TAHUNAN',
            'PENCAPAIAN PERIODE INI',
            'PENCAPAIAN KUMULATIF',
            'SELISIH',
            'PERSENTASE CAPAIAN',
            'STATUS',
            'ANALISA PENYEBAB / CATATAN',
            'TINDAKAN PERBAIKAN / PENCEGAHAN',
            'BUKTI DOKUMEN',
            'PEGAWAI PELAKSANA',
            'MONITORING',
        ];

        foreach ($this->rows as $index => $row) {
            $data[] = [
                $index + 1,
                $row['sasaran_mutu'] ?? '-',
                $this->cleanMultiline($row['kegiatan'] ?? $row['nama_target'] ?? '-'),
                $row['achievement_method_label'] ?? '-',
                $row['target_tahunan'] ?? '-',
                $row['capaian_periode'] ?? '-',
                $row['capaian_kumulatif'] ?? '-',
                $row['selisih'] ?? '-',
                $row['persentase_kumulatif_label'] ?? '-',
                $row['status'] ?? '-',
                $this->cleanMultiline($row['catatan'] ?? '-'),
                $this->cleanMultiline($row['tindakan_perbaikan'] ?? '-'),
                $this->cleanMultiline($row['bukti_dokumen'] ?? '-'),
                $row['pegawai_pelaksana'] ?? '-',
                $row['monitoring'] ?? '-',
            ];
        }

        $footerStart = count($data) + 3;

        $data[] = [];
        $data[] = ['Catatan:'];
        $data[] = ['1. Target yang ditampilkan adalah target tahunan.'];
        $data[] = ['2. Pencapaian periode ini membaca data pada rentang triwulan terpilih.'];
        $data[] = ['3. Pencapaian kumulatif membaca data dari 1 Januari sampai akhir triwulan terpilih.'];
        $data[] = [];
        $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', 'Jakarta, ' . $this->reportDate()->format('d F Y')];
        $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', 'Kepala Unit'];
        $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', $this->unitName];
        $data[] = [];
        $data[] = [];
        $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', $this->kanitName ?: '-'];
        $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', $this->kanitNip ? 'NIP. ' . $this->kanitNip : 'NIP. -'];

        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 28,
            'C' => 40,
            'D' => 22,
            'E' => 18,
            'F' => 22,
            'G' => 22,
            'H' => 18,
            'I' => 18,
            'J' => 18,
            'K' => 35,
            'L' => 35,
            'M' => 35,
            'N' => 28,
            'O' => 30,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $highestRow = $sheet->getHighestRow();
                $highestColumn = 'O';

                foreach ([1, 2, 3, 4, 6] as $row) {
                    $sheet->mergeCells("A{$row}:O{$row}");
                    $sheet->getStyle("A{$row}:O{$row}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A{$row}:O{$row}")
                        ->getFont()
                        ->setBold(true);
                }

                $sheet->getStyle('A1:O4')->getFont()->setSize(12);
                $sheet->getStyle('A6:O6')->getFont()->setSize(14);

                $sheet->getStyle('A8:B10')->getFont()->setBold(true);

                $headerRow = 12;
                $tableStartRow = 12;
                $tableEndRow = 12 + max($this->rows->count(), 1);

                $sheet->getStyle("A{$headerRow}:O{$headerRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '1E293B'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle("A{$tableStartRow}:O{$tableEndRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CBD5E1'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle("A" . ($headerRow + 1) . ":A{$tableEndRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("D" . ($headerRow + 1) . ":J{$tableEndRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getRowDimension($headerRow)->setRowHeight(42);

                for ($row = $headerRow + 1; $row <= $tableEndRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(70);
                }

                $signatureStart = $highestRow - 6;

                if ($signatureStart > 0) {
                    $sheet->getStyle("M{$signatureStart}:O{$highestRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $sheet->freezePane('A13');

                for ($row = 1; $row <= $highestRow; $row++) {
                    $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                        ->getAlignment()
                        ->setWrapText(true);
                }

                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);

                $sheet->getPageMargins()
                    ->setTop(0.5)
                    ->setRight(0.3)
                    ->setLeft(0.3)
                    ->setBottom(0.5);
            },
        ];
    }

    private function cleanMultiline(?string $value): string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : '-';
    }

    private function reportDate(): \Carbon\Carbon
    {
        [, $endDate] = app(QuarterlyTargetReportService::class)
            ->quarterRange($this->year, $this->quarter);

        return $endDate;
    }
}