<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Key hero_title/hero_subtitle/about_story/whatsapp_number/social_links
     * dibaca langsung oleh frontend publik (lihat resources/views/public).
     * Key lama (email/phone/alamat) dipertahankan untuk keperluan admin.
     */
    public function run(): void
    {
        $settings = [
            'hero_title' => 'Properti Premium di Lombok',
            'hero_subtitle' => 'Investasi properti terpercaya dengan view pantai dan perbukitan. Survei lokasi gratis, surat lengkap.',
            'about_story' => "Lombok Horizon adalah agensi properti lokal yang fokus pada kavling tanah di Lombok. Kami memetakan setiap bidang dengan data spasial yang transparan sehingga pembeli bisa melihat batas kavling, luas, dan harga secara jelas sebelum survei.\n\nSetiap proyek kami lengkapi dengan peta interaktif, foto lokasi, dan pendampingan survei sampai akad.",
            'whatsapp_number' => '6281234567890',
            'social_links' => "Instagram | https://instagram.com/lombokhorizon\nFacebook | https://facebook.com/lombokhorizon",
            'email' => 'info@lombokhorizon.test',
            'phone' => '+62361xxxxxxx',
            'alamat' => 'Jl. Raya Senggigi No. 123, Lombok',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'updated_by' => 1]
            );
        }
    }
}
