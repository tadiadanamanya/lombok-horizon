<?php

namespace App\Filament\Pages\Reports;

use App\Models\Project;
use App\Services\ExportService;
use App\Services\ReportService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Number;
use UnitEnum;

class SalesReportPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Laporan Penjualan';

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Laporan Penjualan';

    protected string $view = 'filament.pages.reports.sales-report';

    public ?array $data = [];

    protected ?array $cachedReport = null;

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Filter')
                ->schema([
                    DatePicker::make('date_from')->label('Dari Tanggal')->live(),
                    DatePicker::make('date_to')->label('Sampai Tanggal')->live(),
                    Select::make('project_id')
                        ->label('Proyek')
                        ->options(fn () => Project::orderBy('nama')->pluck('nama', 'id'))
                        ->placeholder('Semua proyek')
                        ->live(),
                    Select::make('status')
                        ->label('Status')
                        ->options(['pending' => 'Menunggu', 'verified' => 'Diverifikasi', 'cancelled' => 'Dibatalkan'])
                        ->placeholder('Semua status')
                        ->default('verified')
                        ->live(),
                ])
                ->columns(4),
        ])->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn () => app(ExportService::class)->exportSalesPdf($this->getReport(), $this->getReport()['filters'])),
        ];
    }

    public function getReport(): array
    {
        return $this->cachedReport ??= app(ReportService::class)->getSalesReport($this->data ?? []);
    }

    /** @return array<int, array{label: string, value: string}> */
    public function summaryCards(): array
    {
        $summary = $this->getReport()['summary'];

        return [
            ['label' => 'Total Pendapatan', 'value' => Number::currency($summary['revenue'], 'IDR')],
            ['label' => 'Jumlah Booking', 'value' => (string) $summary['count']],
            ['label' => 'Rata-rata Deal', 'value' => Number::currency($summary['average'], 'IDR')],
            ['label' => 'Diverifikasi', 'value' => (string) $summary['verified']],
        ];
    }
}
