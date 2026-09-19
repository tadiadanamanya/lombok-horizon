<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * 5 proyek demo. Koordinat boundary ([lng, lat]) realistis Lombok.
     * Kotak boundary dipakai ulang oleh KavlingSeeder untuk grid kavling,
     * jadi ubah di kedua tempat bila menggeser lokasi.
     */
    public function run(): void
    {
        $projects = [
            [
                'nama' => 'Sky Lancing',
                'slug' => 'sky-lancing',
                'lokasi' => 'Sekotong, Lombok Barat',
                'deskripsi' => 'Kavling eksklusif di perbukitan Sekotong dengan pemandangan laut lepas. Cocok untuk vila pribadi maupun investasi jangka panjang.',
                'thumbnail_path' => null,
                'box' => [116.040, -8.660, 116.060, -8.645],
            ],
            [
                'nama' => 'Torok Hill Residence',
                'slug' => 'torok-hill-residence',
                'lokasi' => 'Sekotong, Lombok Barat',
                'deskripsi' => 'Hunian di kawasan perbukitan yang tenang dengan udara sejuk, tidak jauh dari pantai Torok.',
                'thumbnail_path' => null,
                'box' => [116.090, -8.690, 116.110, -8.675],
            ],
            [
                'nama' => 'Kuta Central Flat',
                'slug' => 'kuta-central-flat',
                'lokasi' => 'Pujut, Lombok Tengah',
                'deskripsi' => 'Kavling datar di pusat kawasan Kuta Mandalika, dekat sirkuit dan pantai. Akses jalan aspal sampai lokasi.',
                'thumbnail_path' => null,
                'box' => [116.270, -8.895, 116.290, -8.880],
            ],
            [
                'nama' => 'Pujut Flat',
                'slug' => 'pujut-flat',
                'lokasi' => 'Pujut, Lombok Tengah',
                'deskripsi' => 'Kavling siap bangun di area Pujut dengan kontur datar dan surat lengkap. Pilihan ekonomis untuk rumah tinggal.',
                'thumbnail_path' => null,
                'box' => [116.320, -8.860, 116.340, -8.845],
            ],
            [
                'nama' => 'Senggigi Bay View',
                'slug' => 'senggigi-bay-view',
                'lokasi' => 'Batu Layar, Lombok Barat',
                'deskripsi' => 'Kavling premium menghadap Teluk Senggigi. View matahari terbenam langsung dari lokasi.',
                'thumbnail_path' => null,
                'box' => [116.030, -8.490, 116.050, -8.475],
            ],
        ];

        foreach ($projects as $project) {
            [$minLng, $minLat, $maxLng, $maxLat] = $project['box'];

            Project::updateOrCreate(
                ['slug' => $project['slug']],
                [
                    'nama' => $project['nama'],
                    'lokasi' => $project['lokasi'],
                    'deskripsi' => $project['deskripsi'],
                    'thumbnail_path' => $project['thumbnail_path'],
                    'batas_proyek' => [
                        'type' => 'Polygon',
                        'coordinates' => [[
                            [$minLng, $minLat],
                            [$maxLng, $minLat],
                            [$maxLng, $maxLat],
                            [$minLng, $maxLat],
                            [$minLng, $minLat],
                        ]],
                    ],
                ]
            );
        }
    }
}
