# Lombok Horizon — Platform Web Properti Berbasis GeoJSON

Platform web agency properti single-tenant untuk pasar Lombok, dengan fokus penelitian
**implementasi GeoJSON untuk visualisasi data spasial** (poligon kavling dirender interaktif via Leaflet).

> Konteks: proyek skripsi. Spesifikasi acuan: `PRD.md` v1.0.0 (single source of truth).

## Tech Stack

| Komponen | Versi |
|---|---|
| PHP | ^8.3 |
| Laravel | 13.32.0 |
| Filament (admin) | v4 |
| MySQL | 8.4 LTS (`lombok_horizon`, kolom `json` untuk GeoJSON) |
| Frontend publik | Blade + Alpine.js + Leaflet 1.9.4 (tanpa React/Vue) |
| CSS | Tailwind CSS v4 + Vite (font Plus Jakarta Sans self-host) |
| PDF | `barryvdh/laravel-dompdf` (A4) |
| Activity Log | `spatie/laravel-activitylog` + Ali Harb UI |
| Testing | Pest |
| API Docs | Scramble (auto-generate) |

## Cara Menjalankan

Syarat: PHP ^8.3 + Composer, MySQL 8.4, Node 20+.

```bash
# 1. Install dependensi
composer install
npm install

# 2. Konfigurasi environment (atur DB_CONNECTION=mysql, DB_DATABASE=lombok_horizon)
cp .env.example .env
php artisan key:generate

# 3. Migrasi + data sample
php artisan migrate --fresh --seed

# 4. Build aset frontend
npm run build

# 5. Jalankan server
php artisan serve   # http://127.0.0.1:8000
```

Untuk pengembangan CSS: `npm run dev` di terminal terpisah.

## Kredensial & URL Penting

| Akses | Detail |
|---|---|
| Admin panel | `http://127.0.0.1:8000/admin` |
| Email admin | `admin@lombokhorizon.test` |
| Password admin | `password` |
| Halaman publik | `/`, `/projects`, `/projects/{slug}`, `/about` |
| Dokumentasi API | `/docs/api` (Scramble UI) |

> Ganti password admin setelah instalasi bila dipakai di luar lokal.

## Perintah Berguna

```bash
php artisan test            # full suite (Pest)
vendor/bin/pint             # format kode (PSR-12)
vendor/bin/pint --test      # cek format tanpa mengubah
npm run build               # build produksi (wajib setelah ubah CSS)
```

## Struktur Folder (ringkas)

```
app/Filament/          Panel admin: Resources, Pages, Widgets, Forms/Components
app/Http/Controllers/  Api/* (7 endpoint /api/v1) dan Public/* (web)
app/Services/          GeoJSONService, InquiryService, BookingService, ReportService, ExportService
database/seeders/      Data sample demo (admin, project, kavling, booking, inquiry, settings)
resources/views/       public/* (Blade), filament/*, pdf/*
resources/css|js/      Tailwind @theme + app.js (dibangun Vite)
routes/                web.php (4 halaman) dan api.php (/api/v1)
tests/Feature|Unit/    Pest — hanya business logic
docs/                  Dokumentasi (api, design-system, arsitektur, skripsi-outline, demo-checklist)
```

## Troubleshooting

| Gejala | Penyebab umum → solusi |
|---|---|
| Halaman tanpa gaya (HTML polos) | Aset belum di-build → `npm run build`; pastikan `@vite` ter-render (cek `public/build/manifest.json` ada) |
| Peta kosong | GeoJSON kavling belum diisi / tiles CDN terblokir jaringan |
| Inquiry 422 | `nama` min 2 karakter; `phone` format Indonesia; `kavling_id`/`project_id` opsional tapi harus valid bila diisi |
| Redirect WA salah nomor | Cek Site Settings → Nomor WhatsApp Admin (`whatsapp_number`) |
| Admin form GeoJSON error | Isi harus JSON valid (Polygon/MultiPolygon); gunakan toolbar gambar atau preview |
| Test gagal di DB | Suite memakai database terpisah/transaksi — jangan jalankan `--seed` manual di DB test |

## Screenshot

Lihat `docs/images/` (diisi manual dari browser): landing, peta detail + popup kavling,
modal inquiry, dashboard admin, form GeoJSON + preview + toolbar gambar, contoh PDF laporan.
