<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .meta { color: #555; margin-bottom: 12px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #999; padding: 5px 6px; text-align: left; }
        table.data th { background: #eee; }
        .right { text-align: right; }
        .footer { margin-top: 12px; color: #555; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Rekap Data Kavling — Lombok Horizon</h1>
    <div class="meta">
        Periode booking: {{ $filters['date_from'] ?? 'awal' }} s/d {{ $filters['date_to'] ?? 'sekarang' }} |
        Total kavling: {{ $report['totals']['kavling'] ?? 0 }} |
        Terjual (verified): {{ $report['totals']['terjual'] ?? 0 }} |
        Revenue: Rp {{ number_format($report['totals']['revenue'] ?? 0, 0, ',', '.') }}
    </div>

    <table class="data">
        <thead>
            <tr><th>Project</th><th class="right">Total</th><th class="right">Available</th><th class="right">Booked</th><th class="right">Sold</th><th class="right">Disabled</th><th class="right">Terjual</th><th class="right">Revenue</th></tr>
        </thead>
        <tbody>
            @forelse($report['projects'] as $project)
            <tr>
                <td>{{ $project['nama'] }}</td>
                <td class="right">{{ $project['total'] }}</td>
                <td class="right">{{ $project['available'] }}</td>
                <td class="right">{{ $project['booked'] }}</td>
                <td class="right">{{ $project['sold'] }}</td>
                <td class="right">{{ $project['disabled'] }}</td>
                <td class="right">{{ $project['terjual'] }}</td>
                <td class="right">Rp {{ number_format($project['revenue'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="8">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak {{ $generatedAt->format('d M Y H:i') }} — Lombok Horizon</div>
</body>
</html>
