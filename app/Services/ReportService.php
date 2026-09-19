<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Kavling;
use App\Models\Project;
use Carbon\Carbon;
use Flowframe\Trend\Trend;
use Illuminate\Database\Eloquent\Builder;

class ReportService
{
    /**
     * Normalisasi filter laporan: date_from, date_to, project_id, status, search.
     *
     * @param  array<string, mixed>  $filters
     * @return array{date_from: ?string, date_to: ?string, project_id: ?int, status: ?string, search: ?string}
     */
    public function normalizeFilters(array $filters): array
    {
        return [
            'date_from' => $filters['date_from'] ?? null,
            'date_to' => $filters['date_to'] ?? null,
            'project_id' => isset($filters['project_id']) && $filters['project_id'] !== '' ? (int) $filters['project_id'] : null,
            'status' => $filters['status'] ?? null,
            'search' => $filters['search'] ?? null,
        ];
    }

    /**
     * Range tanggal untuk query. Default 12 bulan terakhir bila filter kosong.
     *
     * @param  array<string, mixed>  $filters
     * @return array{0: Carbon, 1: Carbon}
     */
    public function dateRange(array $filters): array
    {
        $from = ! empty($filters['date_from']) ? Carbon::parse($filters['date_from'])->startOfDay() : now()->subYear()->startOfDay();
        $to = ! empty($filters['date_to']) ? Carbon::parse($filters['date_to'])->endOfDay() : now()->endOfDay();

        return [$from, $to];
    }

    /**
     * Base query booking dengan filter project + tanggal (tanpa filter status).
     *
     * @param  array<string, mixed>  $filters
     */
    protected function bookingQuery(array $filters): Builder
    {
        [$from, $to] = $this->dateRange($filters);

        $query = Booking::query()->with(['kavling.project']);

        if (! empty($filters['project_id'])) {
            $query->whereHas('kavling', fn (Builder $q) => $q->where('project_id', $filters['project_id']));
        }

        return $query->whereBetween('booked_at', [$from, $to]);
    }

    /**
     * Laporan penjualan. Revenue = sum deal_price (booking_fee tidak masuk revenue).
     *
     * @param  array<string, mixed>  $filters
     */
    public function getSalesReport(array $filters): array
    {
        $filters = $this->normalizeFilters($filters);

        $query = $this->bookingQuery($filters);
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Distribusi status dihitung tanpa filter status agar terlihat perbandingannya.
        $statusCounts = (clone $this->bookingQuery($filters))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // ponytail: Trend untuk series bulanan (konsisten dengan DashboardMetricsWidget).
        $monthly = Trend::query($this->bookingQuery($filters))
            ->dateColumn('booked_at')
            ->between(...$this->dateRange($filters))
            ->perMonth()
            ->sum('deal_price');

        return [
            'filters' => $filters,
            'summary' => [
                'revenue' => (float) (clone $query)->sum('deal_price'),
                'count' => (clone $query)->count(),
                'average' => (float) (clone $query)->avg('deal_price'),
                'pending' => (int) ($statusCounts['pending'] ?? 0),
                'verified' => (int) ($statusCounts['verified'] ?? 0),
                'cancelled' => (int) ($statusCounts['cancelled'] ?? 0),
            ],
            'monthly' => [
                'labels' => $monthly->map(fn ($v) => Carbon::parse($v->date)->format('M Y'))->values()->all(),
                'data' => $monthly->map(fn ($v) => (float) $v->aggregate)->values()->all(),
            ],
            'rows' => (clone $query)->latest('booked_at')->limit(100)->get(),
        ];
    }

    /**
     * Laporan performa: agregat stok + revenue per project, plus tren booking bulanan.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getPerformanceReport(array $filters): array
    {
        $filters = $this->normalizeFilters($filters);
        [$from, $to] = $this->dateRange($filters);

        $projects = Project::query()
            ->when(! empty($filters['project_id']), fn (Builder $q) => $q->where('id', $filters['project_id']))
            ->orderBy('nama')
            ->get();

        $rows = $projects->map(function (Project $project) use ($from, $to) {
            $kavlings = Kavling::where('project_id', $project->id);
            $verified = Booking::where('status', 'verified')
                ->whereHas('kavling', fn (Builder $q) => $q->where('project_id', $project->id))
                ->whereBetween('booked_at', [$from, $to]);

            return [
                'id' => $project->id,
                'nama' => $project->nama,
                'total' => (clone $kavlings)->count(),
                'available' => (clone $kavlings)->where('status', 'available')->count(),
                'booked' => (clone $kavlings)->where('status', 'booked')->count(),
                'sold' => (clone $kavlings)->where('status', 'sold')->count(),
                'disabled' => (clone $kavlings)->where('status', 'disabled')->count(),
                'terjual' => (clone $verified)->count(),
                'revenue' => (float) (clone $verified)->sum('deal_price'),
            ];
        })->all();

        $monthly = Trend::query($this->bookingQuery($filters)->where('status', 'verified'))
            ->dateColumn('booked_at')
            ->between($from, $to)
            ->perMonth()
            ->count();

        return [
            'filters' => $filters,
            'projects' => $rows,
            'totals' => [
                'kavling' => array_sum(array_column($rows, 'total')),
                'terjual' => array_sum(array_column($rows, 'terjual')),
                'revenue' => array_sum(array_column($rows, 'revenue')),
            ],
            'monthly' => [
                'labels' => $monthly->map(fn ($v) => Carbon::parse($v->date)->format('M Y'))->values()->all(),
                'data' => $monthly->map(fn ($v) => (int) $v->aggregate)->values()->all(),
            ],
        ];
    }

    /**
     * Laporan buyer: daftar booking + pencarian nama/telepon.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getBuyersReport(array $filters): array
    {
        $filters = $this->normalizeFilters($filters);

        $query = $this->bookingQuery($filters);
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn (Builder $q) => $q->where('buyer_name', 'like', "%{$search}%")->orWhere('buyer_phone', 'like', "%{$search}%"));
        }

        $statusCounts = (clone $this->bookingQuery($filters))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'filters' => $filters,
            'summary' => [
                'total' => (clone $query)->count(),
                'revenue' => (float) (clone $query)->sum('deal_price'),
                'verified' => (int) ($statusCounts['verified'] ?? 0),
                'pending' => (int) ($statusCounts['pending'] ?? 0),
                'cancelled' => (int) ($statusCounts['cancelled'] ?? 0),
            ],
            'rows' => (clone $query)->latest('booked_at')->limit(100)->get(),
        ];
    }
}
