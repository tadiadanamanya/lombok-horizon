<div class="space-y-4">
<div class="grid grid-cols-2 gap-4 md:grid-cols-5">
    @foreach ([
        ['Total Kavling', $totalKavling],
        ['Tersedia', $availableKavling],
        ['Dibooking', $bookedKavling],
        ['Terjual', $soldKavling],
        ['Pendapatan', $revenueFormatted],
    ] as [$label, $value])
    <div class="rounded-lg border border-gray-200 bg-white p-4">
        <div class="text-sm text-gray-500">{{ $label }}</div>
        <div class="text-xl font-semibold">{{ $value }}</div>
    </div>
    @endforeach
</div>

@include('filament.pages.reports.chart', [
    'id' => 'dashboardRevenue',
    'type' => 'line',
    'chartLabel' => 'Pendapatan per bulan',
    'labels' => $monthlyLabels,
    'chartData' => $monthlyData,
])
</div>
