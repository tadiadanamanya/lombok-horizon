<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .meta { color: #555; margin-bottom: 12px; }
        .summary { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .summary td { border: 1px solid #ccc; padding: 6px 8px; }
        .summary td.label { background: #f2f2f2; width: 220px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #999; padding: 5px 6px; text-align: left; }
        table.data th { background: #eee; }
        .right { text-align: right; }
        .footer { margin-top: 12px; color: #555; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Laporan Penjualan — Lombok Horizon</h1>
    <div class="meta">
        Periode: {{ $filters['date_from'] ?? 'awal' }} s/d {{ $filters['date_to'] ?? 'sekarang' }}
        @if(! empty($filters['status'])) | Status: {{ $filters['status'] }} @endif
    </div>

    <table class="summary">
        <tr><td class="label">Total Revenue (deal_price)</td><td>Rp {{ number_format($report['summary']['revenue'] ?? 0, 0, ',', '.') }}</td></tr>
        <tr><td class="label">Jumlah Booking</td><td>{{ $report['summary']['count'] ?? $report['summary']['total'] ?? 0 }}</td></tr>
        @if(isset($report['summary']['average']))
        <tr><td class="label">Rata-rata Deal</td><td>Rp {{ number_format($report['summary']['average'], 0, ',', '.') }}</td></tr>
        @endif
        <tr><td class="label">Verified / Pending / Cancelled</td><td>{{ $report['summary']['verified'] ?? 0 }} / {{ $report['summary']['pending'] ?? 0 }} / {{ $report['summary']['cancelled'] ?? 0 }}</td></tr>
    </table>

    <table class="data">
        <thead>
            <tr><th>Tanggal</th><th>Pembeli</th><th>Telepon</th><th>Kavling</th><th>Project</th><th>Status</th><th class="right">Deal Price</th></tr>
        </thead>
        <tbody>
            @forelse($report['rows'] as $booking)
            <tr>
                <td>{{ optional($booking->booked_at)->format('d M Y') }}</td>
                <td>{{ $booking->buyer_name }}</td>
                <td>{{ $booking->buyer_phone }}</td>
                <td>{{ $booking->kavling->nomor ?? '-' }}</td>
                <td>{{ $booking->kavling->project->nama ?? '-' }}</td>
                <td>{{ $booking->status }}</td>
                <td class="right">Rp {{ number_format($booking->deal_price, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="7">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak {{ $generatedAt->format('d M Y H:i') }} — Lombok Horizon</div>
</body>
</html>
