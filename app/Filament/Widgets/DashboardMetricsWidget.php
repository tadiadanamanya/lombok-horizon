<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Kavling;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Flowframe\Trend\Trend;
use Illuminate\Support\Number;

class DashboardMetricsWidget extends Widget
{
    protected static ?string $heading = 'Ringkasan Dashboard';

    protected string $view = 'filament.widgets.dashboard-metrics';

    protected function getViewData(): array
    {
        return $this->getData();
    }

    protected function getData(): array
    {
        $totalKavling = Kavling::count();
        $availableKavling = Kavling::where('status', 'available')->count();
        $bookedKavling = Kavling::where('status', 'booked')->count();
        $soldKavling = Kavling::where('status', 'sold')->count();

        // Revenue hanya dari booking verified (aturan bisnis PRD).
        $verifiedBookings = Booking::where('status', 'verified');
        $revenue = (clone $verifiedBookings)->sum('deal_price');

        // Monthly revenue trend for chart (TrendValue::$date adalah string Y-m).
        $monthly = Trend::query($verifiedBookings)
            ->between(
                start: now()->subYear(),
                end: now()
            )
            ->perMonth()
            ->sum('deal_price');

        return [
            'totalKavling' => $totalKavling,
            'availableKavling' => $availableKavling,
            'bookedKavling' => $bookedKavling,
            'soldKavling' => $soldKavling,
            'revenue' => $revenue,
            'revenueFormatted' => Number::currency($revenue, 'IDR'),
            'monthlyLabels' => $monthly->map(fn ($value) => Carbon::parse($value->date)->format('M'))->values()->all(),
            'monthlyData' => $monthly->map(fn ($value) => (float) $value->aggregate)->values()->all(),
        ];
    }
}
