@php($report = $this->getReport())

<x-filament-panels::page>
    {{ $this->content }}

    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        @foreach ($this->summaryCards() as $card)
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <div class="text-sm text-gray-500">{{ $card['label'] }}</div>
            <div class="text-xl font-semibold">{{ $card['value'] }}</div>
        </div>
        @endforeach
    </div>

    @include('filament.pages.reports.chart', [
        'id' => 'salesChart',
        'type' => 'line',
        'chartLabel' => 'Revenue per bulan',
        'labels' => $report['monthly']['labels'],
        'chartData' => $report['monthly']['data'],
    ])

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-gray-500">
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Pembeli</th>
                    <th class="px-4 py-2">Kavling</th>
                    <th class="px-4 py-2">Project</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2 text-right">Deal Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($report['rows'] as $booking)
                <tr class="border-b last:border-0">
                    <td class="px-4 py-2">{{ $booking->booked_at?->format('d M Y') ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $booking->buyer_name }}</td>
                    <td class="px-4 py-2">{{ $booking->kavling->nomor ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $booking->kavling->project->nama ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $booking->status }}</td>
                    <td class="px-4 py-2 text-right">Rp {{ number_format($booking->deal_price, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
