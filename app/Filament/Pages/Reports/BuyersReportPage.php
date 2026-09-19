<?php

namespace App\Filament\Pages\Reports;

use App\Models\Project;
use App\Services\ExportService;
use App\Services\ReportService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Number;
use UnitEnum;

class BuyersReportPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Laporan Buyer';

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Laporan Buyer';

    protected string $view = 'filament.pages.reports.buyers-report';

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
                        ->live(),
                    TextInput::make('search')
                        ->label('Cari Buyer')
                        ->placeholder('Nama / telepon')
                        ->live(),
                ])
                ->columns(5),
        ])->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $report = $this->getReport();

                    // Bentuk buyers report kompatibel dengan view PDF penjualan (daftar booking).
                    return app(ExportService::class)->exportSalesPdf([
                        'summary' => [
                            'revenue' => $report['summary']['revenue'],
                            'count' => $report['summary']['total'],
                            'pending' => $report['summary']['pending'],
                            'verified' => $report['summary']['verified'],
                            'cancelled' => $report['summary']['cancelled'],
                        ],
                        'rows' => $report['rows'],
                    ], $report['filters']);
                }),
        ];
    }

    public function getReport(): array
    {
        return $this->cachedReport ??= app(ReportService::class)->getBuyersReport($this->data ?? []);
    }

    /** @return array<int, array{label: string, value: string}> */
    public function summaryCards(): array
    {
        $summary = $this->getReport()['summary'];

        return [
            ['label' => 'Total Pembeli', 'value' => (string) $summary['total']],
            ['label' => 'Diverifikasi', 'value' => (string) $summary['verified']],
            ['label' => 'Menunggu', 'value' => (string) $summary['pending']],
            ['label' => 'Pendapatan (Filter Aktif)', 'value' => Number::currency($summary['revenue'], 'IDR')],
        ];
    }
}
