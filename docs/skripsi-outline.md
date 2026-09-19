# Outline Skripsi: Implementasi GeoJSON untuk Visualisasi Spasial pada Platform Properti Lombok Horizon

> Judul kerja: *Implementasi Format Data GeoJSON untuk Visualisasi Spasial Inventory Kavling pada Platform Web Properti (Studi Kasus: Lombok Horizon)*
> Rujukan: `PRD.md` v1.0.0; file implementasi di repo ini.

## Bab 1: Pendahuluan

1.1 **Latar Belakang** — kebutuhan transparansi posisi/status/harga kavling;
data spasial konvensional (shapefile, KML, GIS desktop) tidak web-friendly;
GeoJSON (RFC 7946) ringan, berbasis JSON, dirender langsung Leaflet.

1.2 **Rumusan Masalah**
1. Bagaimana merancang skema penyimpanan data spasial kavling berbasis GeoJSON?
2. Bagaimana membangun alur input GeoJSON oleh admin non-teknis yang aman?
3. Bagaimana menyajikan poligon kavling secara akurat dan performan di peta web?

1.3 **Batasan Masalah** — single-tenant, 1 admin, lokal; out of scope PRD §3.2
(payment gateway, mobile app, KML import, Excel export, cache/queue, CI/CD);
validasi geometri longgar + preview sebagai safety net.

1.4 **Tujuan** — 1-ke-1 dengan rumusan masalah. 1.5 **Manfaat** — akademis + praktis.
1.6 **Sistematika Penulisan**.

## Bab 2: Landasan Teori

2.1 Data spasial & SIG (vektor, WGS84). 2.2 GeoJSON RFC 7946 (`Feature`/
`FeatureCollection`, `[lng,lat]`, ring tertutup, `geometry: null` valid;
contoh dari `GET /api/v1/projects/{slug}/geojson`). 2.3 Leaflet 1.9.4
(konversi koordinat, styling poligon, Leaflet.draw). 2.4 Laravel
(MVC, Eloquent cast JSON, API Resource, Form Request, `throttle:5,1`).
2.5 Filament v4 (Resource/Page/Widget, custom field `GeoJSONInput`,
`MapPreviewField`). 2.6 Kolom JSON MySQL + unik `(project_id, nomor)`.
2.7 Pengujian (Pest, black-box). 2.8 Penelitian terdahulu — tabel pembanding
+ celah yang diisi penelitian ini.

## Bab 3: Analisis & Perancangan

3.1 Sistem berjalan (Excel/WA tanpa konteks) vs masalah (PRD §2).
3.2 Kebutuhan fungsional (PRD §5; 7 endpoint §9) dan non-fungsional
(TTFB < 200ms, GeoJSON < 1 dtk, kontras 4.5:1, anti-slop §8).
3.3 Basis data — ERD + 8 tabel (PRD §6), termasuk `ref_code LH-{tahun}-{seq}`.
3.4 Arsitektur & alur data — 3 jalur + siklus GeoJSON (lihat `docs/arsitektur.md`);
business rules §7 (status manual, revenue = `deal_price`).
3.5 Antarmuka + design system (lihat `docs/design-system.md`).
3.6 Matriks rancangan pengujian.

## Bab 4: Implementasi

4.1 Lingkungan — tabel stack final (PHP 8.5, Laravel 13.32.0, Filament v4.13.2).
4.2 Basis data — migrations + casts (`batas_proyek`, `koordinat_bidang` array).
4.3 **Inti GeoJSON (paling detail)** — `GeoJSONService.php` (`toGeoJsonFeature`:
sniffing Polygon/MultiPolygon/LineString/Point, kosong → `null`;
`toGeoJsonCollection`: feature `boundary` pertama + `kind`);
`GeoJSONInput.php` (validasi longgar + dehydrate array);
`MapPreviewField.php` + blade (preview live + Leaflet.draw → tulis `geometry`);
endpoint (list tanpa koordinat, GeoJSON terpisah).
4.4 Frontend publik — landing, list/detail (fetch geojson → poligon warna status +
popup), modal inquiry → redirect `wa.me` admin berpesan terisi.
4.5 Panel admin — Resource, verify/cancel + checkbox status, Site Settings
(`whatsapp_number`), Reports + PDF A4, Activity Log minimal.
4.6 Tangkapan layar + potongan kode kunci per subbab.

## Bab 5: Pengujian & Hasil

5.1 Rencana (merujuk §3.6). 5.2 Fungsional black-box — tabel kasus:
GeoJSON valid/invalid, draw→simpan→tampil publik, edit vertex, hapus,
inquiry→WA, verify booking±status, export PDF.
5.3 Otomatis (Pest, 37 test) — booking flow; inquiry (format `LH-YYYY-NNNN`,
unik, 429); GeoJSON (shape, `[lng,lat]`, ring tertutup, boundary, null geometry);
HTTP API (envelope, 404); PDF; activity log.
5.4 Non-fungsional terukur — TTFB warm ~16ms, GeoJSON ~25ms; checklist aksesibilitas/browser.
5.5 Pembahasan — jawab 3 rumusan masalah dengan bukti; keterbatasan
(validasi longgar, tanpa topologi, single-tenant, lokal).

## Bab 6: Kesimpulan

6.1 Kesimpulan — 3 poin menjawab rumusan masalah. 6.2 Saran — validasi topologi,
riwayat versi geometri, multi-admin, produksi + caching tiles, PWA/mobile.

**Lampiran**: (A) kode kunci, (B) skema DB, (C) contoh payload GeoJSON aktual,
(D) hasil suite, (E) docs API Scramble, (F) administrasi kampus.
