<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class ExportService
{
    /**
     * Export laporan penjualan (ringkasan + daftar booking) ke PDF A4.
     *
     * @param  array<string, mixed>  $report  Output ReportService::getSalesReport / getBuyersReport.
     * @param  array<string, mixed>  $filters  Filter aktif untuk dicetak di kop laporan.
     */
    public function exportSalesPdf(array $report, array $filters)
    {
        $filename = 'laporan-penjualan-'.now()->format('Ymd-His').'.pdf';

        return Pdf::loadView('pdf.sales-report', [
            'report' => $report,
            'filters' => $filters,
            'generatedAt' => now(),
        ])->setPaper('a4')->download($filename);
    }

    /**
     * Export rekap data kavling per project ke PDF A4.
     *
     * @param  array<string, mixed>  $report  Output ReportService::getPerformanceReport.
     * @param  array<string, mixed>  $filters  Filter aktif untuk dicetak di kop laporan.
     */
    public function exportKavlingDataPdf(array $report, array $filters)
    {
        $filename = 'rekap-kavling-'.now()->format('Ymd-His').'.pdf';

        return Pdf::loadView('pdf.kavling-data', [
            'report' => $report,
            'filters' => $filters,
            'generatedAt' => now(),
        ])->setPaper('a4')->download($filename);
    }
}
