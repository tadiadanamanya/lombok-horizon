# Dokumentasi API — Lombok Horizon

Basis: `routes/api.php` (prefix `/api/v1`). Dokumentasi interaktif auto-generate:
**`http://127.0.0.1:8000/docs/api`** (Scramble UI).

Aturan umum (PRD §9):

- Semua `GET` read-only, tanpa auth.
- `POST /inquiries` tanpa auth, rate limit **5/menit per IP** → `429 Too Many Requests`.
- Respons list/detail dibungkus envelope `{ "data": ... }`.
- Endpoint GeoJSON mengembalikan GeoJSON mentah (tanpa envelope) dengan `Content-Type: application/json`.
- `koordinat_bidang` **tidak pernah** disertakan di respons kavling — ambil via endpoint GeoJSON terpisah.

## GET /api/v1/projects

```json
{ "data": [{ "id": 1, "nama": "Sky Lancing", "slug": "sky-lancing",
  "lokasi": "Lancing, Lombok Tengah, NTB", "thumbnail_url": "https://...",
  "kavling_total": 29, "kavling_available": 25 }] }
```

## GET /api/v1/projects/{slug}

```json
{ "data": { "id": 1, "nama": "Sky Lancing", "slug": "sky-lancing",
  "lokasi": "...", "deskripsi": "...", "thumbnail_url": "https://...",
  "kavling_total": 29, "kavling_available": 25 } }
```

Slug tidak dikenal → `404`.

## GET /api/v1/projects/{slug}/kavlings

```json
{ "data": [{ "id": 1, "nomor": "A-001", "luas_m2": 500,
  "harga": 350000000, "status": "available" }] }
```

## GET /api/v1/projects/{slug}/geojson

GeoJSON `FeatureCollection`. Feature pertama = batas proyek (`kind: "boundary"`),
diikuti tiap kavling (`kind: "kavling"`). Urutan koordinat `[lng, lat]`; ring tertutup;
kavling tanpa geometri memakai `geometry: null` (RFC 7946, bukan titik palsu).

```json
{ "type": "FeatureCollection", "features": [
  { "type": "Feature",
    "geometry": { "type": "Polygon", "coordinates": [] },
    "properties": { "kind": "boundary", "project": "Sky Lancing" } },
  { "type": "Feature",
    "geometry": { "type": "Polygon", "coordinates": [] },
    "properties": { "kind": "kavling", "id": 1, "nomor": "A-001",
      "luas_m2": 500, "harga": 350000000, "status": "available" } }
] }
```

## GET /api/v1/kavlings/{id}

```json
{ "data": { "id": 1, "nomor": "A-001", "luas_m2": 500,
  "harga": 350000000, "status": "available" } }
```

ID tidak dikenal → `404`.

## GET /api/v1/kavlings/{id}/geojson

GeoJSON `Feature` tunggal (bentuk sama seperti feature kavling di atas).

## POST /api/v1/inquiries

Request (`kavling_id` dan `project_id` opsional):

```json
{ "nama": "Budi Santoso", "phone": "081234567890", "kavling_id": 12 }
```

Respons `201`:

```json
{ "data": { "ref_code": "LH-2026-0148",
  "redirect_url": "https://wa.me/6281234567890?text=Halo%2C%20saya%20Budi..." } }
```

- `ref_code` unik format `LH-{tahun}-{seq 4 digit}`.
- `redirect_url` mengarah ke **nomor WhatsApp admin** (`site_settings.whatsapp_number`)
  dengan pesan terisi: nama, nomor, kavling, proyek, luas, harga, ref code.
- Inquiry **tidak mengubah** status kavling.
- Payload tidak valid → `422` (nama min 2 karakter, phone format Indonesia).
