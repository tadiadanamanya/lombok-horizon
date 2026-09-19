# PROMPT — Lombok Horizon Development

Salin prompt ini ke AI coding assistant (Claude Code, Cursor, Copilot, dll) sebagai **system context**, lalu tambahkan task spesifik di bagian akhir.

---

## 1. PROJECT CONTEXT

```
Proyek: Lombok Horizon
Tipe: Platform web properti (single-tenant agency)
Konteks: Proyek skripsi — penelitian GeoJSON untuk visualisasi spasial
Deadline: 4 minggu
Developer: 1 orang (full-stack)
Deployment: Lokal (Laravel Herd / Valet / XAMPP)
```

**Fokus penelitian**: GeoJSON sebagai format data spasial, Leaflet sebagai renderer, validasi geometri, visualisasi peta interaktif.

---

## 2. TECH STACK (FROZEN)

| Komponen | Versi | Catatan |
|---|---|---|
| PHP | 8.5 | |
| Laravel | 13.32.0 | |
| Filament | v4.13.2 | Jangan upgrade/downgrade |
| Livewire | (bawaan Filament v4) | |
| MySQL | 8.4 LTS | JSON column untuk GeoJSON |
| Frontend publik | Blade + Alpine.js + Leaflet | **Bukan** Inertia/React/Vue |
| Tailwind CSS | v4 | |
| Vite | Terbaru | |
| Leaflet | 1.9.4 | |
| PDF | `barryvdh/laravel-dompdf` | A4 |
| Activity Log | `spatie/laravel-activitylog` + Ali Harb UI | Minimal |
| Testing | Pest | Business logic only |
| API Docs | Scramble | Auto-generate |

**Tidak dipakai**:
- ❌ Redis
- ❌ Laravel Horizon
- ❌ Inertia + React
- ❌ Vue
- ❌ Excel export (`maatwebsite/excel`)
- ❌ KML import
- ❌ Cache layer
- ❌ Queue worker (sync only)
- ❌ CI/CD
- ❌ Monitoring
- ❌ Backup
- ❌ Multi-role/permission

---

## 3. DESIGN SYSTEM (FROZEN)

### Warna

```css
--paper: #F9F9F9;
--surface: #FFFFFF;
--ink: #111111;
--muted: #303537;
--hairline: #E5E5E5;

/* Semantic (status kavling) */
--available: #059669;
--booked: #D97706;
--sold: #64748B;
--disabled: #DC2626;
```

### Typography

- **Font**: Plus Jakarta Sans (public + admin)
- Display 36px/40px weight 700
- Headline 24px/32px weight 700
- Title 18px/28px weight 600
- Body 16px/24px weight 400
- Label 12px/16px weight 500
- Prose measure: 65-75ch

### Radius (Web)

- `radius-sm` 4px — chip, badge
- `radius-md` 6px — button kecil, input kecil
- `radius-lg` 8px — button default, input, card
- `radius-xl` 12px — modal, map popup

### Shadow

- Rest: none
- Hover: `shadow-sm`
- Modal/popup: small lift

### Icon

- **Lucide** (public), **Phosphor** (admin)
- Monochrome, satu family per surface
- Sizes: 16px / 20px / 24px

### Anti-Slop Rules (WAJIB)

- ❌ No emoji di UI
- ❌ No gradient background atau text
- ❌ No shadow di atas `shadow-sm` (kecuali popup/modal)
- ❌ No radius di atas 12px
- ❌ No generic stock imagery
- ❌ No placeholder AI copy
- ❌ No bounce/long fade animation
- ✅ Clear hierarchy, prose measure 65-75ch
- ✅ Status kavling selalu dengan teks, bukan warna saja

---

## 4. DATABASE SCHEMA (FROZEN)

### `users`
```
id, name, email, password, timestamps
```
Hanya 1 admin (via seeder).

### `projects`
```
id, nama, slug (unique), lokasi, deskripsi,
thumbnail_path (nullable), batas_proyek (json, GeoJSON Polygon),
timestamps
```

### `kavlings`
```
id, project_id (FK cascade), nomor, luas_m2 (nullable),
harga (nullable), status (enum: available|booked|sold|disabled),
koordinat_bidang (json, GeoJSON Polygon), timestamps
UNIQUE (project_id, nomor)
```

### `project_images`
```
id, project_id (FK cascade), image_path, sort_order, timestamps
```

### `bookings`
```
id, kavling_id (FK), user_id (FK), buyer_name, buyer_phone,
buyer_email (nullable), booking_fee (nullable, nice-to-know),
deal_price (revenue), status (enum: pending|verified|cancelled),
booked_at, verified_at (nullable), timestamps
```

### `inquiries`
```
id, nama, phone, kavling_id (FK nullable), project_id (FK nullable),
ref_code (unique, format LH-{year}-{seq}), 
status (enum: baru|dihubungi|deal|ditolak), timestamps
```

### `site_settings`
```
id, key (unique), value (json/text), updated_by (FK nullable), timestamps
```

---

## 5. BUSINESS RULES (FROZEN)

### Status Kavling — MANUAL
- Status kavling diubah **manual** oleh admin.
- **Tidak ada event-driven** — booking tidak otomatis mengubah status.
- Saat verify/cancel booking, admin **opsional** centang ubah status kavling.

### Booking — Admin-Driven
- Pengunjung **tidak** membuat booking.
- Booking dibuat admin di panel.
- **Revenue** = `deal_price` booking.
- **Booking fee** = nice to know, tidak masuk revenue.

### Inquiry — Lead Capture Only
- Form publik: nama + phone.
- Ref code: `LH-{year}-{seq}` (contoh: `LH-2026-0148`).
- Redirect ke `wa.me/{whatsapp_number}?text={pesan}`.
- **Inquiry tidak mengubah status kavling**.
- Rate limit: 5/menit per IP.

### Role — 1 Admin
- Tidak ada role/permission management.
- Full access untuk admin.

### GeoJSON
- Format: **Proper GeoJSON** (RFC 7946), bukan array of pairs.
- Validasi: **longgar** (JSON valid saja).
- Preview map sebagai safety net.
- `koordinat_bidang` dan `batas_proyek`: tipe `json` di database.

---

## 6. API CONTRACT (FROZEN)

```
GET  /api/v1/projects                          → list projects
GET  /api/v1/projects/{slug}                   → detail project
GET  /api/v1/projects/{slug}/kavlings          → list kavling (tanpa koordinat)
GET  /api/v1/projects/{slug}/geojson           → GeoJSON FeatureCollection
GET  /api/v1/kavlings/{id}                     → detail kavling
GET  /api/v1/kavlings/{id}/geojson             → GeoJSON Feature
POST /api/v1/inquiries                         → submit inquiry (throttle:5,1)
```

**Aturan**:
- Semua GET read-only, tanpa auth.
- POST inquiry tanpa auth, rate limit 5/menit.
- `koordinat_bidang` **tidak disertakan** di response kavling — fetch GeoJSON terpisah.
- GeoJSON response: proper format (`type: Feature` atau `FeatureCollection`).

---

## 7. STRUKTUR FOLDER (FROZEN)

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

## 8. TIMELINE (4 MINGGU)

| Fase | Durasi | Deliverable |
|---|---|---|
| **1. Setup & Fondasi** | 1 minggu | Laravel 13 + Filament v4 + DB + Auth |
| **2. CRUD + GeoJSON** | 1 minggu | Project, Kavling, GeoJSON input + preview |
| **3. Public Frontend** | 1 minggu | Landing, project list/detail, peta |
| **4. Polish + Demo** | 1 minggu | Seeder, PDF, Pest, dokumentasi |

**Fokus**: GeoJSON + peta + CRUD + public frontend basic.

**Yang bisa dipotong kalau tight**: Booking, Inquiry, Report PDF, Activity Log, Pest.

---

## 9. CONVENTIONS

### Code Style
- Ikuti **PSR-12**.
- Gunakan **Pest** untuk test (bukan PHPUnit).
- Gunakan **Laravel Pint** untuk formatting.
- Nama model: **singular** (`Kavling`, `Project`).
- Nama tabel: **plural** (`kavlings`, `projects`).
- Nama kolom: **snake_case** (`luas_m2`, `deal_price`).
- Nama method: **camelCase** (`getGeoJSON`, `verifyBooking`).

### Filament v4
- Gunakan **Resource** untuk CRUD.
- Gunakan **Page** untuk custom page (Site Settings, Reports).
- Gunakan **Widget** untuk dashboard.
- Custom field: extend `Filament\Forms\Components\Field`.
- Preview map: Alpine + Leaflet, bundle via Vite.

### GeoJSON
- Simpan sebagai **proper GeoJSON**:
  ```json
  {
    "type": "Polygon",
    "coordinates": [[[lng, lat], [lng, lat], ...]]
  }
  ```
- **Urutan koordinat**: `[lng, lat]` (GeoJSON spec).
- **Leaflet**: `[lat, lng]` — perlu konversi.
- **Ring tertutup**: titik pertama = titik terakhir.

### Blade + Alpine
- Blade untuk server-render.
- Alpine untuk UI state (toggle, modal, dropdown).
- Leaflet untuk peta (vanilla JS, bukan Alpine component).
- Jangan wrap Leaflet dalam `x-data`.

### API
- Gunakan **API Resource** untuk format response.
- Gunakan **Form Request** untuk validasi.
- Response selalu `{ data: ... }` untuk single/list.

---

## 10. CONSTRAINTS

### Wajib
- ✅ Ikuti PRD v1.0.0 (Lombok Horizon)
- ✅ Ikuti design system (warna, font, radius)
- ✅ Ikuti anti-slop rules
- ✅ Ikuti business rules (status manual, inquiry lead-only)
- ✅ Ikuti API contract

### Dilarang
- ❌ Jangan pakai Inertia/React/Vue
- ❌ Jangan pakai Redis/Horizon
- ❌ Jangan pakai Excel export
- ❌ Jangan pakai KML import
- ❌ Jangan tambah cache layer
- ❌ Jangan tambah queue worker
- ❌ Jangan tambah role/permission
- ❌ Jangan pakai emoji di UI
- ❌ Jangan pakai gradient
- ❌ Jangan pakai shadow berlebihan
- ❌ Jangan pakai radius > 12px

### Kalau Ragu
- **Ringan** > kompleks
- **Simple** > abstrak
- **Konsisten** > inovatif
- **Manual** > otomatis (untuk business logic)

---

## 11. DELIVERABLE EXPECTATION

Setiap task harus menghasilkan:
1. **Kode** — bersih, mengikuti conventions
2. **Test** (kalau business logic) — Pest
3. **Dokumentasi** — comment untuk logic kompleks
4. **Tidak ada regression** — fitur lama tetap jalan

---

## 12. TASK (ISI DI SINI)

```
[Task spesifik Anda]

Contoh:
"Buat migration untuk tabel `kavlings` sesuai schema di Section 4.
Include foreign key ke `projects` dengan cascade delete.
Buat juga model `Kavling` dengan relasi `belongsTo(Project::class)`.
Tambahkan cast untuk `koordinat_bidang` sebagai array."
```

---

## 13. OUTPUT FORMAT (PILIH)

- [ ] **Full file** — kode lengkap, siap copy-paste
- [ ] **Diff** — perubahan saja
- [ ] **Snippet** — bagian penting saja
- [ ] **Explanation** — penjelasan + snippet

---

## 14. CHECKLIST SEBELUM SUBMIT

- [ ] Kode mengikuti PSR-12
- [ ] Nama model/tabel/kolom sesuai conventions
- [ ] Tidak melanggar constraints (Section 10)
- [ ] Mengikuti design system (kalau UI)
- [ ] Mengikuti business rules (Section 5)
- [ ] Mengikuti API contract (Section 6, kalau API)
- [ ] Ada test (kalau business logic)
- [ ] Tidak ada emoji/gradient/shadow berlebihan
- [ ] GeoJSON format proper (`[lng, lat]`)

---

**Versi**: 1.0.0
**Terakhir update**: 2026-09-17
**Referensi**: PRD Lombok Horizon v1.0.0