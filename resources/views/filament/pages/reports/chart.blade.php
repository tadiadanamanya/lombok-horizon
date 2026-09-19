{{-- Partial chart: $id, $type (line|bar), $chartLabel, $labels, $chartData --}}
<div class="rounded-lg border border-gray-200 bg-white p-4">
    <div wire:key="chart-{{ $id }}-{{ md5(json_encode($labels).json_encode($chartData)) }}">
        <canvas id="{{ $id }}" height="110"></canvas>
        <script>
            (function initChart(attempt) {
                var el = document.getElementById(@js($id));
                if (! el) return;
                if (! window.Chart) {
                    if (attempt < 50) setTimeout(function () { initChart(attempt + 1); }, 100);
                    return;
                }
                if (window['chart_' + @js($id)]) window['chart_' + @js($id)].destroy();
                window['chart_' + @js($id)] = new Chart(el, {
                    type: @js($type),
                    data: {
                        labels: @js($labels),
                        datasets: [{
                            label: @js($chartLabel),
                            data: @js($chartData),
                            borderColor: 'rgb(217, 119, 6)',
                            backgroundColor: 'rgba(217, 119, 6, 0.4)',
                        }],
                    },
                    options: { responsive: true, maintainAspectRatio: true },
                });
            })(0);
        </script>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
@endpush
