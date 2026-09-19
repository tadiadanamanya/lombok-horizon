<?php

use App\Models\Booking;
use App\Models\Kavling;
use App\Models\Project;
use App\Models\User;
use App\Services\ExportService;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;

function seedSales(): void
{
    $user = User::factory()->create();
    $project = Project::create([
        'nama' => 'Sky Lancing', 'slug' => 'sky-lancing',
        'lokasi' => 'Lombok', 'deskripsi' => 'Test',
    ]);
    $kavling = Kavling::create(['project_id' => $project->id, 'nomor' => 'A-001']);
    Booking::create([
        'kavling_id' => $kavling->id, 'user_id' => $user->id,
        'buyer_name' => 'Budi Santoso', 'buyer_phone' => '081234567890',
        'deal_price' => 500000000, 'status' => 'verified', 'booked_at' => now(),
    ]);
}

it('exports a valid sales PDF', function () {
    seedSales();
    $report = app(ReportService::class)->getSalesReport(['status' => 'verified']);

    $response = app(ExportService::class)->exportSalesPdf($report, $report['filters']);

    expect($response->getContent())->toStartWith('%PDF-');
});

it('exports the PDF in A4 portrait', function () {
    seedSales();
    $report = app(ReportService::class)->getPerformanceReport([]);

    $pdf = Pdf::loadView('pdf.kavling-data', [
        'report' => $report, 'filters' => $report['filters'], 'generatedAt' => now(),
    ])->setPaper('a4');

    // A4 portrait dalam poin dompdf: 595.28 x 841.89.
    [$x, $y, $width, $height] = $pdf->getDomPDF()->getPaperSize();

    expect((int) round($width))->toBe(595)
        ->and((int) round($height))->toBe(842)
        ->and($pdf->output())->toStartWith('%PDF-');
});
