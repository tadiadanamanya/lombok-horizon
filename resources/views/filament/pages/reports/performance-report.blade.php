@php($report = $this->getReport())

<x-filament-panels::page>
    {{ $this->content }}

    <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
        @foreach ($this->summaryCards() as $card)
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <div class="text-sm text-gray-500">{{ $card['label'] }}</div>
            <div class="text-xl font-semibold">{{ $card['value'] }}</div>
        </div>
        @endforeach
    </div>

    @include('filament.pages.reports.chart', [
        'id' => 'performanceChart',
        'type' => 'bar',
        'chartLabel' => 'Booking verified per bulan',
        'labels' => $report['monthly']['labels'],
        'chartData' => $report['monthly']['data'],
    ])

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-gray-500">
                    <th class="px-4 py-2">Project</th>
                    <th class="px-4 py-2 text-right">Total</th>
                    <th class="px-4 py-2 text-right">Available</th>
                    <th class="px-4 py-2 text-right">Booked</th>
                    <th class="px-4 py-2 text-right">Sold</th>
                    <th class="px-4 py-2 text-right">Terjual</th>
                    <th class="px-4 py-2 text-right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($report['projects'] as $project)
                <tr class="border-b last:border-0">
                    <td class="px-4 py-2">{{ $project['nama'] }}</td>
                    <td class="px-4 py-2 text-right">{{ $project['total'] }}</td>
                    <td class="px-4 py-2 text-right">{{ $project['available'] }}</td>
                    <td class="px-4 py-2 text-right">{{ $project['booked'] }}</td>
                    <td class="px-4 py-2 text-right">{{ $project['sold'] }}</td>
                    <td class="px-4 py-2 text-right">{{ $project['terjual'] }}</td>
                    <td class="px-4 py-2 text-right">Rp {{ number_format($project['revenue'], 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
