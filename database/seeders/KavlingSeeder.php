<?php

namespace Database\Seeders;

use App\Models\Kavling;
use App\Models\Project;
use Illuminate\Database\Seeder;

class KavlingSeeder extends Seeder
{
    /**
     * 12 kavling per proyek (grid 4 kolom x 3 baris) di dalam boundary.
     * Semua nilai deterministik (tanpa rand) agar hasil seed stabil.
     * Kotak harus sama dengan ProjectSeeder::$projects['box'].
     */
    public function run(): void
    {
        $boxes = [
            'sky-lancing' => [116.040, -8.660, 116.060, -8.645],
            'torok-hill-residence' => [116.090, -8.690, 116.110, -8.675],
            'kuta-central-flat' => [116.270, -8.895, 116.290, -8.880],
            'pujut-flat' => [116.320, -8.860, 116.340, -8.845],
            'senggigi-bay-view' => [116.030, -8.490, 116.050, -8.475],
        ];

        // Pola status per 12 kavling: 7 tersedia, 2 terjual, 2 dibooking, 1 nonaktif.
        $statuses = [
            'available', 'available', 'sold', 'available',
            'booked', 'available', 'available', 'sold',
            'available', 'disabled', 'available', 'booked',
        ];

        $cols = 4;
        $rows = 3;
        $gutter = 0.12; // jeda antar kavling

        foreach ($boxes as $slug => $box) {
            $project = Project::where('slug', $slug)->firstOrFail();
            [$minLng, $minLat, $maxLng, $maxLat] = $box;

            $cellW = ($maxLng - $minLng) / $cols;
            $cellH = ($maxLat - $minLat) / $rows;

            for ($i = 0; $i < 12; $i++) {
                $col = $i % $cols;
                $row = intdiv($i, $cols);

                $x0 = $minLng + $col * $cellW + $cellW * $gutter / 2;
                $x1 = $minLng + ($col + 1) * $cellW - $cellW * $gutter / 2;
                // Baris 0 di utara (maxLat) agar A di depan.
                $y1 = $maxLat - $row * $cellH - $cellH * $gutter / 2;
                $y0 = $maxLat - ($row + 1) * $cellH + $cellH * $gutter / 2;

                $blok = chr(65 + intdiv($i, 6)); // A untuk 1-6, B untuk 7-12
                $nomor = sprintf('%s-%03d', $blok, ($i % 6) + 1);

                Kavling::updateOrCreate(
                    ['project_id' => $project->id, 'nomor' => $nomor],
                    [
                        'luas_m2' => 300 + (($i * 37) % 5) * 50,
                        'harga' => 250000000 + (($i * 53) % 5) * 50000000,
                        'status' => $statuses[$i],
                        'koordinat_bidang' => [
                            'type' => 'Polygon',
                            'coordinates' => [[
                                [round($x0, 6), round($y0, 6)],
                                [round($x1, 6), round($y0, 6)],
                                [round($x1, 6), round($y1, 6)],
                                [round($x0, 6), round($y1, 6)],
                                [round($x0, 6), round($y0, 6)],
                            ]],
                        ],
                    ]
                );
            }
        }
    }
}
