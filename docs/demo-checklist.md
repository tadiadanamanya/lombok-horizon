# Demo Checklist — Lombok Horizon (±15 menit)

## H-1: Reset & Bekukan

```bash
php artisan migrate:fresh --seed
npm run build
php artisan test        # harus hijau
php artisan serve       # http://127.0.0.1:8000
```

Pastikan data sample ada: ≥1 project dengan `batas_proyek`, beberapa kavling
tiap status (`available/booked/sold`), booking `pending`, dan
Site Settings → Nomor WhatsApp Admin terisi nomor yang bisa dibuka saat demo.

## Skrip Demo

1. **Landing (2')** — buka `/`: hero, proyek terbaru, CTA. Tunjukkan font + status Tersedia.
2. **Project list (2')** — `/projects`: filter proyek, buka salah satu.
3. **Peta interaktif (3')** — halaman detail: poligon batas + kavling warna status;
   klik poligon → popup (nomor, luas, harga, status). Tekankan: data dari GeoJSON,
   koordinat `[lng, lat]`.
4. **Inquiry (3')** — tombol Ajukan Pertanyaan → isi nama + WA → submit →
   tampil ref code `LH-{tahun}-{seq}` → redirect WA admin berpesan terisi.
5. **Admin booking (2')** — `/admin`: widget booking pending → verify +
   centang ubah status → kavling jadi Terjual; revenue = `deal_price`.
6. **Admin GeoJSON (2')** — Kavling → Edit: gambar poligon via toolbar →
   textarea terisi → preview → simpan → kembali ke peta publik, poligon berubah.
7. **Report (1')** — Sales Report → filter → export PDF A4.

## Fallback Bila Live Gagal

- Jaringan mati (tiles/peta): tunjukkan screenshot `docs/images/` + payload GeoJSON
  via `/api/v1/projects/{slug}/geojson` (JSON tetap jalan offline bila DB lokal).
- WA redirect: tunjukkan `redirect_url` di respons API + data inquiry tersimpan di admin.
- PDF gagal: buka halaman report (angka tetap tampil) + contoh PDF yang sudah diekspor.

## Daftar Screenshot (`docs/images/`)

`01-landing.png`, `02-peta-detail.png`, `03-modal-inquiry.png`,
`04-admin-dashboard.png`, `05-form-geojson.png`, `06-contoh-pdf.png`.
