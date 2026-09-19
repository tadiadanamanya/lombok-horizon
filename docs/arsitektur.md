# Arsitektur — Lombok Horizon

## Struktur Folder

```
app/
├── Filament/
│   ├── Forms/Components/   GeoJSONInput.php, MapPreviewField.php
│   ├── Pages/              SiteSettings + Reports/*
│   ├── Resources/          Project, Kavling, ProjectImage, Booking, Inquiry
│   └── Widgets/            Metrik dashboard, konfirmasi booking
├── Http/
│   ├── Controllers/Api/    7 endpoint /api/v1 (read-only + inquiry)
│   ├── Controllers/Public/ Home, Project (index/show), About
│   ├── Requests/           StoreInquiryRequest (validasi)
│   └── Resources/          Project/Kavling/InquiryResource (envelope {data})
├── Models/                 Project, Kavling, ProjectImage, Booking, Inquiry, SiteSetting
└── Services/               GeoJSONService, InquiryService, BookingService,
                            ReportService, ExportService
database/seeders/           AdminUser, Project, Kavling, Booking, Inquiry, SiteSetting
resources/views/            public/* (Blade+Alpine), filament/*, pdf/*
routes/                     web.php (4 halaman), api.php (/api/v1)
```

## Tiga Jalur Alur Data

1. **Pengunjung (publik)** — Blade server-render + Alpine untuk interaksi + Leaflet
   untuk peta. Daftar kavling diambil tanpa koordinat; geometri diambil terpisah
   via endpoint GeoJSON lalu digambar sebagai poligon warna status + popup.
2. **Admin (Filament)** — CRUD Project/Kavling (GeoJSON via textarea + toolbar gambar
   Leaflet.draw + preview live), Booking (verify/cancel manual + checkbox opsional
   ubah status kavling), Inquiry (update status follow-up), Reports + PDF A4,
   Site Settings, Activity Log minimal.
3. **API publik** — 7 endpoint `GET` read-only tanpa auth + `POST /inquiries`
   (throttle 5/menit). Lihat `docs/api.md`.

## Siklus GeoJSON (fokus penelitian)

```
[Admin] textarea / toolbar gambar (Leaflet.draw)
   → JSON.stringify(geometry) ke GeoJSONInput
   → validasi longgar (JSON valid saja)
   → dehydrate array → kolom json (koordinat_bidang / batas_proyek)
   → cast model array
   → GeoJSONService: Feature (kind: kavling, geometry null bila kosong)
                      / FeatureCollection (feature boundary pertama)
   → endpoint /api/v1/.../geojson
   → Leaflet publik: [lng,lat] dipakai native, poligon warna status
```

Konvensi: urutan koordinat `[lng, lat]` (RFC 7946), ring tertutup,
titik pertama = titik terakhir.

## Business Rules Kunci

- Status kavling **manual** oleh admin; booking/inquiry **tidak** mengubah status otomatis.
- Revenue = `deal_price` booking; `booking_fee` hanya nice-to-know.
- Inquiry: nama + phone (kavling/proyek opsional) → tersimpan dengan
  `ref_code LH-{tahun}-{seq}` → redirect ke WA **admin** dengan pesan terisi.
- 1 admin, full access; tanpa role/permission, cache, queue, atau CI/CD (sesuai PRD).
