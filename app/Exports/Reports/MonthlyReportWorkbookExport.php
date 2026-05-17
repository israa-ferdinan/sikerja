<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MonthlyReportWorkbookExport implements WithMultipleSheets
{
    public function __construct(
        public int $month,
        public int $year,
        public ?int $unitId = null,
        public ?string $printedBy = null
    ) {
    }

    public function sheets(): array
    {
        return [
            new MonthlyEmployeeSummaryExport(
                month: $this->month,
                year: $this->year,
                unitId: $this->unitId,
                printedBy: $this->printedBy
            ),
            new MonthlyReportExport(
                month: $this->month,
                year: $this->year,
                unitId: $this->unitId
            ),
            new MonthlyDutySummaryExport(
                month: $this->month,
                year: $this->year,
                unitId: $this->unitId
            ),
            new MonthlyApplicationSummaryExport(
                month: $this->month,
                year: $this->year,
                unitId: $this->unitId
            ),
            new MonthlyPhotoDataExport(
                month: $this->month,
                year: $this->year,
                unitId: $this->unitId
            ),
        ];
    }
}