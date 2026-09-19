<?php

namespace Tests\Feature;

use App\Filament\Pages\Reports\BuyersReportPage;
use App\Filament\Pages\Reports\PerformanceReportPage;
use App\Filament\Pages\Reports\SalesReportPage;
use App\Filament\Widgets\BookingConfirmationWidget;
use App\Filament\Widgets\DashboardMetricsWidget;
use App\Models\Booking;
use App\Models\Kavling;
use App\Models\Project;
use App\Models\User;
use App\Services\ExportService;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    protected function seedReportData(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'nama' => 'Lombok Horizon', 'slug' => 'lombok-horizon',
            'lokasi' => 'Lombok', 'deskripsi' => 'Test',
        ]);
        $a1 = Kavling::create(['project_id' => $project->id, 'nomor' => 'A-001', 'status' => 'available']);
        $a2 = Kavling::create(['project_id' => $project->id, 'nomor' => 'A-002', 'status' => 'available']);

        Booking::create([
            'kavling_id' => $a1->id, 'user_id' => $user->id,
            'buyer_name' => 'Budi Santoso', 'buyer_phone' => '081234567890',
            'deal_price' => 500000000, 'status' => 'verified',
            'booked_at' => '2026-06-15 10:00:00', 'verified_at' => '2026-06-16 10:00:00',
        ]);
        Booking::create([
            'kavling_id' => $a2->id, 'user_id' => $user->id,
            'buyer_name' => 'Siti Aminah', 'buyer_phone' => '081987654321',
            'deal_price' => 300000000, 'status' => 'pending',
            'booked_at' => '2026-08-10 10:00:00',
        ]);
    }

    public function test_sales_report_numbers(): void
    {
        $this->seedReportData();

        $report = app(ReportService::class)->getSalesReport(['status' => 'verified']);

        $this->assertEquals(500000000, $report['summary']['revenue']);
        $this->assertEquals(1, $report['summary']['count']);
        $this->assertEquals(1, $report['summary']['pending']);
        $this->assertNotEmpty($report['monthly']['labels']);
    }

    public function test_performance_report_numbers(): void
    {
        $this->seedReportData();

        $report = app(ReportService::class)->getPerformanceReport([]);

        $this->assertEquals(2, $report['totals']['kavling']);
        $this->assertEquals(1, $report['totals']['terjual']);
        $this->assertEquals(500000000, $report['totals']['revenue']);
    }

    public function test_buyers_report_search(): void
    {
        $this->seedReportData();

        $report = app(ReportService::class)->getBuyersReport(['search' => 'Budi']);

        $this->assertEquals(1, $report['summary']['total']);
        $this->assertEquals(500000000, $report['summary']['revenue']);
    }

    public function test_report_pages_render(): void
    {
        $this->seedReportData();
        $this->actingAs(User::first());

        $this->get('/admin')->assertOk();

        Livewire::test(DashboardMetricsWidget::class)
            ->assertSee('Total Kavling');
        Livewire::test(BookingConfirmationWidget::class)
            ->assertSee('Konfirmasi Booking')
            ->assertSee('Siti Aminah');

        $this->get(SalesReportPage::getUrl())->assertOk()->assertSee('500');
        $this->get(PerformanceReportPage::getUrl())->assertOk()->assertSee('Lombok Horizon');
        $this->get(BuyersReportPage::getUrl())->assertOk()->assertSee('Budi Santoso');
    }

    public function test_dashboard_revenue_counts_only_verified_bookings(): void
    {
        $this->seedReportData();
        $this->actingAs(User::first());

        // seedReportData: verified 500jt + pending 300jt → widget harus 500jt.
        Livewire::test(DashboardMetricsWidget::class)
            ->assertViewHas('revenue', 500000000)
            ->assertSee('Pendapatan')
            ->assertSee('500,000,000')
            ->assertDontSee('800,000,000');
    }

    public function test_export_pdfs(): void
    {
        $this->seedReportData();

        $service = app(ReportService::class);
        $export = app(ExportService::class);

        $sales = $service->getSalesReport(['status' => 'verified']);
        $response = $export->exportSalesPdf($sales, $sales['filters']);
        $this->assertStringStartsWith('%PDF-', $response->getContent());

        $perf = $service->getPerformanceReport([]);
        $response = $export->exportKavlingDataPdf($perf, $perf['filters']);
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }
}
