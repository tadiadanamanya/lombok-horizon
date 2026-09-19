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

class PerformanceReportPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Laporan Performa';

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Laporan Performa';

    protected string $view = 'filament.pages.reports.performance-report';

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
                ])
                ->columns(3),
        ])->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn () => app(ExportService::class)->exportKavlingDataPdf($this->getReport(), $this->getReport()['filters'])),
        ];
    }

    public function getReport(): array
    {
        return $this->cachedReport ??= app(ReportService::class)->getPerformanceReport($this->data ?? []);
    }

    /** @return array<int, array{label: string, value: string}> */
    public function summaryCards(): array
    {
        $totals = $this->getReport()['totals'];

        return [
            ['label' => 'Total Kavling', 'value' => (string) $totals['kavling']],
            ['label' => 'Terjual', 'value' => (string) $totals['terjual']],
            ['label' => 'Total Pendapatan', 'value' => Number::currency($totals['revenue'], 'IDR')],
        ];
    }
}
