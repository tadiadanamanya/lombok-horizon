# PRD — Lombok Horizon Property Platform

**Version**: 1.0.0
**Date**: 2026-09-17
**Status**: Final — Ready for Development
**Author**: Development Team
**Product**: Lombok Horizon
**Konteks**: Proyek skripsi — penelitian GeoJSON untuk platform properti

---

## 1. Executive Summary

Lombok Horizon adalah platform web properti untuk pasar Lombok. Platform ini menyajikan inventory kavling dan properti secara transparan melalui peta interaktif berbasis **GeoJSON**, dengan lead capture terintegrasi WhatsApp.

**Model bisnis**: Website agency properti (single-tenant). Bukan SaaS, bukan marketplace.

**Konteks penelitian**: Proyek skripsi yang fokus pada **implementasi GeoJSON** untuk visualisasi data spasial properti menggunakan **Leaflet**.

**Value proposition**:
1. **Transparansi data** — calon pembeli lihat kavling, status, harga, dan posisi langsung di peta
2. **Lead capture efisien** — form minimal → WhatsApp admin dengan pesan terisi otomatis
3. **Data center internal** — admin kelola inventory, booking, inquiry, dan report

**Fokus penelitian**: GeoJSON sebagai format data spasial, Leaflet sebagai renderer, validasi geometri, dan visualisasi peta interaktif.

---

## 2. Business Goals

| No | Goal | Success Metric |
| --- | --- | --- |
| 1 | Bangun brand credibility Lombok Horizon | Positive buyer feedback; website jadi referensi saat meeting offline |
| 2 | Transparansi inventory meningkatkan trust | Reduction in "harga berapa?" chat tanpa konteks |
| 3 | Lead capture terstruktur | 100% inquiry tersimpan di database dengan status follow-up |
| 4 | Admin bisa kelola bisnis tanpa Excel | Report PDF A4 ter-generate < 3 detik |
| 5 | Implementasi GeoJSON untuk visualisasi spasial | Polygon render akurat, validasi longgar, preview map real-time |

---

## 3. Scope

### 3.1 In Scope

**Admin Panel (Filament v4)**:
- Dashboard: metrics, chart, widget konfirmasi booking
- Resource: Project (CRUD + `batas_proyek` GeoJSON manual)
- Resource: Kavling (CRUD + `koordinat_bidang` GeoJSON manual + preview map)
- Resource: Project Image (CRUD + sortable)
- Resource: Booking (CRUD + verify/cancel manual)
- Resource: Inquiry (view + update status)
- Page: Site Settings (key-value)
- Page: Reports (Sales, Performance, Buyers) + PDF export A4
- Activity Log: Minimal (booking verify/cancel + settings)

**Public Frontend (Blade + Alpine + Leaflet)**:
- Landing page: hero, featured projects, CTA WhatsApp
- Project list: grid card, filter, search
- Project detail: peta interaktif, filter kavling, list, CTA
- About page: narasi, kontak
- Inquiry form: modal, nama + phone, WA redirect

**API (Public, Read-Only)**:
- 7 endpoint (lihat Section 9)
- Rate limit: inquiry 5/menit per IP

**Testing**:
- Pest: business logic (5 test)
- Manual demo: di depan penguji

**Design System**:
- Warna: Paper `#F9F9F9`, Ink `#111111`, Muted `#303537`
- Font: Plus Jakarta Sans (public + admin)
- Radius: 4/6/8/12px (web)
- Anti-slop: no emoji, no gradient, no shadow berlebihan

### 3.2 Out of Scope (Explicit)

- Multi-tenancy / SaaS platform
- Payment gateway / transaksi online
- Mobile app (native atau PWA)
- Real-time chat / WebSocket
- Super admin panel
- Integrasi listing aggregator (99.co, Rumah123, dll)
- CRM lengkap (pipeline, email marketing, automation)
- **Excel export** (dihapus)
- **KML import** (tidak dipakai)
- **Auto-hitung luas** (manual)
- **Harga per are** (manual)
- **Cache** (skip)
- **Queue worker** (sync)
- **CI/CD** (skip)
- **Monitoring** (skip)
- **Backup** (skip)
- **Deployment VPS** (lokal)
- **Multi-role** (1 admin)
- **Owner dashboard** (tidak)
- **Scheduled report** (manual)
- **Report API** (dihapus)
- **Testimonial** (skip)

---

## 4. User Personas

### 4.1 Pengunjung Anonim (Calon Pembeli)

- Tidak perlu login, tidak bisa transaksi online
- Bisa: browse project, lihat peta kavling, filter, lihat detail, kirim inquiry
- Goal: cari properti di Lombok, dapat info akurat tanpa harus tanya satu per satu

### 4.2 Admin (Developer / Staff)

- Login ke panel admin Filament
- Bisa: CRUD project/kavling/foto, input booking, kelola inquiry, lihat report, edit site settings
- Tidak ada role terpisah — **1 admin** (full access)
- Owner tidak akses dashboard — terima laporan manual via export PDF

---

## 5. Functional Requirements

### 5.1 Web Publik (Blade + Alpine + Leaflet)

#### 5.1.1 Landing Page

- Hero: solid `paper` background, headline Plus Jakarta Sans 700, subheadline, single CTA
- Section: featured projects, value props, CTA WhatsApp
- Footer: kontak, copyright, legal links
- **Tidak ada testimonial** (skip)

#### 5.1.2 Project List Page

- Grid card: thumbnail, nama, lokasi, jumlah kavling available
- Filter: lokasi, harga range, status ketersediaan
- Search: nama project

#### 5.1.3 Project Detail Page

- Header: nama, lokasi, deskripsi, thumbnail gallery
- **Interactive map**: full width Leaflet container, polygon kavling dari GeoJSON, hover popup (nomor, luas, harga, status)
- **Filter kavling**: status select, price range, area range
- **Kavling list**: status labeled (available emerald, booked amber, sold slate, disabled red — selalu dengan teks)
- **CTA per kavling available**: tombol "Minta Info" → modal inquiry

#### 5.1.4 About Page

- Narasi perusahaan Lombok Horizon
- Kontak & lokasi kantor
- Tidak ada team section (skip)

#### 5.1.5 Inquiry Form (Modal)

- Field: **nama**, **nomor WhatsApp** (wajib)
- Data kavling: dari **state frontend** (GeoJSON yang sudah di-load)
- Hidden/auto: `kavling_id`, `project_id`
- Submit → tersimpan → redirect ke `wa.me/{admin}?text=...`
- Pesan WA terisi: nama, nomor, kavling, project, luas, harga, ref code

### 5.2 Panel Admin (Filament v4)

#### 5.2.1 Dashboard

- Widget metrics: total kavling, available, booked, sold, revenue
- Chart: penjualan bulanan (bar/line)
- Widget: **konfirmasi booking** (booking pending, verify/cancel)
- Shortcut link: tambah project, tambah kavling, export report

#### 5.2.2 Resource: Project

- Form: nama, slug (auto), lokasi, deskripsi, thumbnail upload, `batas_proyek` (GeoJSON manual)
- Table: sortable columns, filter lokasi, search nama
- Relation: list kavling & foto di bawah form
- **Preview map**: render `batas_proyek`

#### 5.2.3 Resource: Kavling

- Form: project (select), nomor, luas (m²), harga, status (manual), `koordinat_bidang` (GeoJSON manual)
- **Map Preview**: komponen read-only, render polygon dari `koordinat_bidang`, warna sesuai status, auto-fit bounds
- Table: filter per project/status, search nomor
- **GeoJSON Input**: textarea + validasi longgar (JSON valid)

#### 5.2.4 Resource: Project Image

- Form: project (select), image upload, sort_order
- Table: thumbnail preview, sortable

#### 5.2.5 Resource: Booking

- Form: kavling (select), buyer_name, buyer_phone, buyer_email, **booking_fee** (nice to know), **deal_price** (revenue)
- Aksi: **verify** (manual + opsi checkbox ubah status kavling), **cancel** (manual + opsi checkbox)
- Table: filter status, tanggal, project
- **Status kavling tidak otomatis berubah** — admin ubah manual

#### 5.2.6 Resource: Inquiry

- Table: nama, phone, kavling, project, ref_code, status, tanggal
- Filter: status, project, tanggal
- Aksi: update status (baru → dihubungi → deal/ditolak)
- Search: nama, phone, ref_code
- **Inquiry tidak mengubah status kavling**

#### 5.2.7 Page: Site Settings

- Form edit key-value: hero_title, hero_subtitle, about_story, contact_email, contact_phone, whatsapp_number, social_links, seo_meta

#### 5.2.8 Report Pages

- **Sales Report**: filter date range, project, status; metrics (total kavling, available, booked, sold, revenue, avg deal price, transaction count); chart per project & per bulan; **export PDF A4**
- **Performance Report**: filter project, period; metrics (sold %, booked %, avg days to sell); stacked chart
- **Buyer Report**: filter date range, project, booking status; metrics (unique buyers, total bookings, active, completed); detail 20 booking terakhir

#### 5.2.9 Activity Log (Minimal)

- Log hanya: **booking verify/cancel** + **settings change**
- Library: `spatie/laravel-activitylog`
- UI: Ali Harb Activity Log (Filament plugin)

### 5.3 API Publik

```
GET  /api/v1/projects
GET  /api/v1/projects/{slug}
GET  /api/v1/projects/{slug}/kavlings
GET  /api/v1/projects/{slug}/geojson
GET  /api/v1/kavlings/{id}
GET  /api/v1/kavlings/{id}/geojson
POST /api/v1/inquiries
```

Semua GET read-only, tanpa auth. POST inquiry tanpa auth, rate limited 5/menit.

---

## 6. Database Schema

### 6.1 users

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint PK | |
| name | varchar | |
| email | varchar unique | |
| password | varchar | |
| timestamps | | |

**1 admin** — dibuat via seeder.

### 6.2 projects

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint PK | |
| nama | varchar | |
| slug | varchar unique | |
| lokasi | varchar | |
| deskripsi | text | |
| thumbnail_path | varchar nullable | |
| batas_proyek | json nullable | **Proper GeoJSON Polygon** |
| timestamps | | |

### 6.3 kavlings

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint PK | |
| project_id | bigint FK → projects | cascade delete |
| nomor | varchar | Manual |
| luas_m2 | decimal(10,2) nullable | Manual |
| harga | bigint nullable | Manual |
| status | enum | available, booked, sold, disabled |
| koordinat_bidang | json nullable | **Proper GeoJSON Polygon** |
| timestamps | | |

Unique: (project_id, nomor).

### 6.4 project_images

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint PK | |
| project_id | bigint FK | cascade delete |
| image_path | varchar | |
| sort_order | int default 0 | |
| timestamps | | |

### 6.5 bookings

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint PK | |
| kavling_id | bigint FK | |
| user_id | bigint FK → users | admin yang input |
| buyer_name | varchar | |
| buyer_phone | varchar | |
| buyer_email | varchar nullable | |
| booking_fee | bigint nullable | Nice to know |
| deal_price | bigint | Revenue |
| status | enum | pending, verified, cancelled |
| booked_at | timestamp | |
| verified_at | timestamp nullable | |
| timestamps | | |

### 6.6 inquiries

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint PK | |
| nama | varchar | |
| phone | varchar | |
| kavling_id | bigint FK nullable | |
| project_id | bigint FK nullable | |
| ref_code | varchar unique | `LH-{year}-{seq}` |
| status | enum | baru, dihubungi, deal, ditolak |
| timestamps | | |

### 6.7 site_settings

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint PK | |
| key | varchar unique | |
| value | json/text | |
| updated_by | bigint FK → users nullable | |
| timestamps | | |

### 6.8 activity_log

Table dari `spatie/laravel-activitylog`.

---

## 7. Business Rules

### 7.1 Status Kavling (Manual)

- Status kavling **diubah manual** oleh admin.
- Admin bisa set: `available`, `booked`, `sold`, `disabled`.
- **Tidak ada event-driven** — booking tidak otomatis mengubah status.
- Saat verify booking, admin **opsional** centang "Ubah status kavling ke sold".
- Saat cancel booking, admin **opsional** centang "Ubah status kavling ke available".

### 7.2 Booking Admin-Driven

- Pengunjung web **tidak** membuat booking.
- Semua booking dibuat admin di panel.
- Tidak ada payment gateway.
- **Revenue** dihitung dari `deal_price` booking.
- **Booking fee** = nice to know, tidak masuk revenue.

### 7.3 Inquiry & WhatsApp

- Form publik minimal: nama + nomor WhatsApp.
- Setelah tersimpan, sistem redirect ke `wa.me/{whatsapp_number}?text={encoded_message}`.
- Nomor admin diambil dari `site_settings.whatsapp_number`.
- Ref code format: `LH-{tahun}-{nomor urut 4 digit}`, contoh `LH-2026-0148`.
- **Inquiry tidak mengubah status kavling**.

### 7.4 Role Access

- **1 admin** — full access.
- Tidak ada role/permission management.
- Owner tidak akses dashboard — terima laporan manual.

---

## 8. Non-Functional Requirements

### 8.1 Performance

- Web TTFB < 200ms (warm)
- GeoJSON load < 1 detik untuk project < 500 kavling
- Report generation < 3 detik (< 10.000 records)
- PDF export < 10 detik

### 8.2 Security

- CSRF protection semua form
- Rate limit: inquiry max 5/menit per IP
- File upload: validasi mime type & size max 5MB
- Activity log untuk booking verify/cancel & settings change
- Password bcrypt, min 8 karakter

### 8.3 Accessibility

- Kontras teks minimum 4.5:1
- Label terasosiasi di semua input
- Navigasi keyboard friendly
- Text alternative untuk tabel data

### 8.4 Browser Support

- Chrome, Edge, Firefox, Safari (2 versi terakhir)
- iOS Safari, Android Chrome

### 8.5 Design Anti-Slop

- [ ] No emoji di UI
- [ ] No gradient background atau text
- [ ] No shadow di atas `shadow-sm` (kecuali popup/modal)
- [ ] No radius di atas 12px (web)
- [ ] No generic stock imagery
- [ ] No placeholder AI copy
- [ ] Single icon family per surface
- [ ] No bounce/long fade animation
- [ ] Clear type hierarchy, prose measure 65–75ch

---

## 9. API Specification

### 9.1 GET /api/v1/projects

Response:
```json
{
  "data": [
    {
      "id": 1,
      "nama": "Sky Lancing",
      "slug": "sky-lancing",
      "lokasi": "Lancing, Lombok Tengah, NTB",
      "thumbnail_url": "https://...",
      "kavling_total": 29,
      "kavling_available": 25
    }
  ]
}
```

### 9.2 GET /api/v1/projects/{slug}

Response:
```json
{
  "data": {
    "id": 1,
    "nama": "Sky Lancing",
    "slug": "sky-lancing",
    "lokasi": "Lancing, Lombok Tengah, NTB",
    "deskripsi": "...",
    "thumbnail_url": "https://...",
    "kavling_total": 29,
    "kavling_available": 25
  }
}
```

### 9.3 GET /api/v1/projects/{slug}/kavlings

Response:
```json
{
  "data": [
    {
      "id": 1,
      "nomor": "A-001",
      "luas_m2": 500,
      "harga": 350000000,
      "status": "available"
    }
  ]
}
```

**Catatan**: `koordinat_bidang` **tidak disertakan** — fetch GeoJSON terpisah.

### 9.4 GET /api/v1/projects/{slug}/geojson

Response: GeoJSON FeatureCollection
```json
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "geometry": { "type": "Polygon", "coordinates": [] },
      "properties": { "kind": "boundary", "project": "Sky Lancing" }
    },
    {
      "type": "Feature",
      "geometry": { "type": "Polygon", "coordinates": [] },
      "properties": {
        "kind": "kavling",
        "id": 1,
        "nomor": "A-001",
        "luas_m2": 500,
        "harga": 350000000,
        "status": "available"
      }
    }
  ]
}
```

### 9.5 GET /api/v1/kavlings/{id}

Response:
```json
{
  "data": {
    "id": 1,
    "nomor": "A-001",
    "luas_m2": 500,
    "harga": 350000000,
    "status": "available"
  }
}
```

### 9.6 GET /api/v1/kavlings/{id}/geojson

Response: GeoJSON Feature
```json
{
  "type": "Feature",
  "geometry": { "type": "Polygon", "coordinates": [] },
  "properties": {
    "kind": "kavling",
    "id": 1,
    "nomor": "A-001",
    "luas_m2": 500,
    "harga": 350000000,
    "status": "available"
  }
}
```

### 9.7 POST /api/v1/inquiries

Request:
```json
{
  "nama": "Budi Santoso",
  "phone": "081234567890",
  "kavling_id": 12
}
```

Response:
```json
{
  "ref_code": "LH-2026-0148",
  "redirect_url": "https://wa.me/6281234567890?text=Halo%2C%20saya%20Budi..."
}
```

**Rate limit**: 5/menit per IP → 429 Too Many Requests.

---

## 10. User Flows

### 10.1 Pengunjung → Lead

1. Buka web → browse project list
2. Buka project detail → lihat peta kavling (GeoJSON)
3. Klik kavling available → popup (nomor, luas, harga, status)
4. Klik "Minta Info" → modal inquiry
5. Isi nama + nomor WA → submit
6. Redirect ke WA admin dengan pesan terisi + ref code
7. Admin follow-up via WA, update status inquiry di panel

### 10.2 Admin → Booking Offline

1. Pembeli datang ke kantor, deal kavling A-001
2. Admin buka panel → Booking → Create
3. Pilih kavling A-001, isi data pembeli, booking fee, deal price
4. Save → status booking `pending`
5. Admin set status kavling `booked` **manual**
6. Pembayaran DP masuk → admin klik Verify
7. Opsional: centang "Ubah status kavling ke sold"
8. Revenue tercatat: `deal_price`

### 10.3 Admin → Input GeoJSON

1. Buka Kavling → Create
2. Isi nomor, luas, harga, status
3. Paste GeoJSON di textarea `koordinat_bidang`
4. Preview map render real-time
5. Cek visual → simpan
6. Kalau GeoJSON rusak → preview error → perbaiki

### 10.4 Admin → Monitor

1. Buka dashboard → lihat metrics & chart
2. Buka widget konfirmasi booking → verify/cancel
3. Buka Sales Report → filter → export PDF A4
4. Buka Inquiry → cek lead baru → follow-up

---

## 11. Testing Requirements (Pest)

Wajib ada test untuk:

1. **Booking flow**: verify/cancel, status kavling
2. **Inquiry**: tersimpan, ref_code unik, redirect_url benar, rate limit
3. **API GeoJSON**: shape valid, koordinat benar
4. **PDF Export**: ter-generate, format A4
5. **Activity Log**: verify/cancel tercatat

**Manual demo**: di depan penguji.

---

## 12. Deployment & Ops

- **Hosting**: **Lokal** (Laravel Herd / Valet / XAMPP / Laragon)
- **Storage**: local disk (`storage/app/public/`)
- **Queue**: sync (tidak ada queue worker)
- **Cache**: skip
- **Monitoring**: skip
- **Backup**: skip
- **CI/CD**: skip

---

## 13. Timeline

**Deadline**: 4 minggu.

| Fase | Durasi | Deliverable |
| --- | --- | --- |
| 1. Setup & Fondasi | 1 minggu | Laravel 13 + Filament v4 + DB + Auth |
| 2. CRUD + GeoJSON | 1 minggu | Project, Kavling, GeoJSON input + preview |
| 3. Public Frontend | 1 minggu | Landing, project list/detail, peta |
| 4. Polish + Demo | 1 minggu | Seeder, PDF, Pest, dokumentasi |

**Total: 4 minggu (tight).**

> Lingkungan terkonfirmasi: PHP 8.5.10, Laravel 13.32.0, Filament v4.13.2 (rilis 2026-09-15) — semua kompatibel.

---

## 14. Risks & Mitigations

| Risk | Impact | Mitigation |
| --- | --- | --- |
| GeoJSON tidak akurat | High | Preview map sebagai safety net |
| Timeline 4 minggu tight | High | Potong scope non-critical |
| Library tidak kompatibel | Medium | Verifikasi awal, fallback custom |
| Data sample tidak siap | Medium | Seeder generate data |
| Bug di Filament v4 | Medium | Dokumentasi komunitas |

---

## 15. Appendix

### 15.1 Design System

**Warna**:
- Base: paper `#F9F9F9`, surface `#FFFFFF`, hairline `#E5E5E5`
- Text: ink `#111111`, muted `#303537`
- Semantic: available `#059669`, booked `#D97706`, sold `#64748B`, disabled `#DC2626`

**Typography**:
- Font: **Plus Jakarta Sans** (public + admin)
- Display 36px/40px weight 700
- Headline 24px/32px weight 700
- Title 18px/28px weight 600
- Body 16px/24px weight 400, 65-75ch
- Label 12px/16px weight 500

**Spacing**:
- Scale: 4, 8, 16, 24, 32, 48, 64, 96
- Section: 48px desktop, 24px mobile
- Card: 24px
- Grid gap: 24px

**Radius** (Web):
- `radius-sm` 4px: chip, badge
- `radius-md` 6px: button kecil, input kecil
- `radius-lg` 8px: button default, input, card
- `radius-xl` 12px: modal, map popup

**Shadow**:
- Rest: none
- Hover: `shadow-sm`
- Modal/popup: small lift

### 15.2 Status Kavling Color

| Status | Color | Text Label |
| --- | --- | --- |
| available | emerald `#059669` | "Tersedia" |
| booked | amber `#D97706` | "Dibooking" |
| sold | slate `#64748B` | "Terjual" |
| disabled | red `#DC2626` | "Nonaktif" |

Selalu tampil dengan teks, tidak pernah warna saja.

### 15.3 Icon Set

- **Lucide** (public), **Phosphor** (admin)
- Monochrome, satu family per surface
- Sizes: 16px inline, 20px buttons, 24px features

### 15.4 Maps

- **Library**: Leaflet 1.9.4
- **Tiles**: CartoDB Positron atau monochrome OSM
- **Polygon**: status fill 0.2 opacity, 2px stroke, hover 0.4 opacity 3px stroke
- **GeoJSON**: proper format (RFC 7946)

### 15.5 State

| State | Keputusan |
| --- | --- |
| Placeholder image | Placeholder geometris (icon + text) |
| Testimonial | Tidak ada |
| Empty state | Text + icon |
| Loading state | Skeleton (peta, list), Spinner (form) |
| Error state | Inline (form), Toast (action), Page (404/500) |

### 15.6 Struktur Folder

```
app/
├── Filament/
│   ├── Forms/Components/
│   │   ├── GeoJSONInput.php
│   │   └── MapPreviewField.php
│   ├── Pages/
│   ├── Resources/
│   └── Widgets/
├── Http/
│   ├── Controllers/Api/
│   ├── Controllers/Public/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Services/
│   ├── GeoJSONService.php
│   ├── InquiryService.php
│   ├── ReportService.php
│   └── ExportService.php
database/
├── factories/
├── migrations/
└── seeders/
resources/
├── css/
├── js/
│   ├── map.js
│   ├── inquiry.js
│   └── filament/components/
└── views/
    ├── public/
    ├── pdf/
    └── filament/
routes/
├── web.php
├── api.php
tests/
├── Feature/
└── Unit/
```

---

*Dokumen ini adalah single source of truth untuk development Lombok Horizon. Update versi dan tanggal setiap perubahan signifikan.*

**Total keputusan**: ~90.
**Status**: Final — Ready for Development.