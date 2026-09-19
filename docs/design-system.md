# Design System — Lombok Horizon

Acuan: PRD §15. Implementasi token: `resources/css/app.css` (`@theme` Tailwind v4).

## Warna

| Token | Hex | Pemakaian |
|---|---|---|
| `paper` | `#F9F9F9` | Background section |
| `surface` / putih | `#FFFFFF` | Kartu, modal, navbar |
| `hairline` | `#E5E5E5` | Border, divider |
| `ink` / `primary` | `#111111` | Teks utama, tombol primer |
| `muted` | `#303537` | Teks sekunder (kontras ≥ 4.5:1) |

### Status kavling (selalu dengan teks, tidak pernah warna saja)

| Status | Warna | Label | Varian teks (kontras) |
|---|---|---|---|
| `available` | `#059669` | Tersedia | `#047857` |
| `booked` | `#D97706` | Dibooking | `#92400E` |
| `sold` | `#64748B` | Terjual | `#475569` |
| `disabled` | `#DC2626` | Nonaktif | `#B91C1C` |

Badge: background `{warna}/10` + teks varian gelap di atas.

## Tipografi

Font: **Plus Jakarta Sans** (publik + admin, self-host via Fontsource).

| Gaya | Ukuran/line-height | Weight |
|---|---|---|
| Display | 36px/40px | 700 |
| Headline | 24px/32px | 700 |
| Title | 18px/28px | 600 |
| Body | 16px/24px | 400 (measure 65–75ch) |
| Label | 12px/16px | 500 |

## Radius (maks 12px)

| Token | Nilai | Pemakaian |
|---|---|---|
| `rounded` | 4px | Chip, badge |
| `rounded-md` | 6px | Button kecil, input kecil |
| `rounded-lg` | 8px | Button default, input, kartu |
| `rounded-xl` | 12px | Modal, popup peta |

## Shadow

- Rest: none. Hover: `shadow-sm`. Modal/popup: small lift (`shadow-sm`).

## Ikon & Anti-Slop

- Satu family monokrom per permukaan; ukuran 16/20/24px.
- Checklist wajib: tanpa emoji, tanpa gradient, shadow ≤ `shadow-sm`,
  radius ≤ 12px, tanpa stock imagery generik, tanpa placeholder copy,
  tanpa animasi bounce/fade panjang.
