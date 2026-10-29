# UI & Desain

## Desain adalah SSOT

- Bundle desain (HTML) diekstrak ke `.claude/design/extracted/` (markup layar + data/logic).
  Cari `data-screen-label` sebelum membuat halaman. **Jangan mengarang layout** kalau layarnya ada.
- Bandingkan desain ↔ aplikasi **berdampingan di Chrome** (lihat `04-testing-qa.md`): judul,
  subjudul, urutan kartu, label kolom, tombol & aksinya, warna status, empty state.
- Perbedaan yang disengaja (data palsu, fitur di luar cakupan) → sebut di laporan.

## Token & Gaya

- Warna hanya dari token `@theme` di `resources/css/app.css` (mis. `brand-700`, `danger-600`).
  **Tidak ada hex di Blade**. Merah/kuning/hijau hanya menyandikan status.
- Kelas kustom hanya di `app.css` `@layer components`.
- Kartu tanpa bayangan; bayangan hanya untuk lapisan (modal, drawer, dropdown, toast).

## Komponen Blade

- Bangun UI dari anonymous component `x-ui.*` (button, badge, card, kpi, table, modal, drawer,
  tabs, chip-filter, empty, avatar, stepper, progress, icon, flash, alert, ring) dan
  `x-form.*` (input, select, textarea, pills, radio-cards, checkbox, tokens, duration).
- Butuh varian → **tambah prop** di komponen (mis. `badge wrap`), jangan copy-paste kelas panjang.
- Komponen menerima nilai jadi; warna dari `enum->color()`.
- Ikon hanya SVG inline `x-ui.icon`. Tanpa icon font/CDN.
- Form: `@csrf` + `x-form.*` + `x-ui.button type="submit"`. Modal berisi form submit POST biasa;
  gagal validasi → modal terbuka lagi (`:show="$errors->has(...)"`).
- Layout: `x-app-layout` (sidebar + topbar), `x-focus-layout` (layar penuh tanpa menu:
  fasilitator, tautan email, operator), `layouts/auth`, `layouts/print` (PDF).
- Halaman bisa berganti layout lewat `<x-dynamic-component :component="$x ? 'focus-layout' : 'app-layout'">`.

## Alpine

- Interaksi lokal kecil inline; > 15 baris → `Alpine.data('nama', …)` di `resources/js/`.
- Toggle tampil/sembunyi: bila `x-show` bertabrakan dengan kelas display, pakai `:class="... && 'hidden'"` + `x-cloak`.
- Hindari `confirm()` untuk aksi baru — pakai konfirmasi dua langkah Alpine ("Tangguhkan" → "Yakin?").

## Responsif HP (wajib diperiksa di 390px)

| Kasus | Pola |
|---|---|
| Tabel umum | wrapper `overflow-x-auto` + `min-width` → digeser, tidak dipadatkan |
| Daftar yang butuh aksi cepat (persetujuan) | kartu `md:hidden` dengan tombol 44px, tabel `hidden md:block` |
| Visual lebar (peta, kalender, heatmap) | HP: daftar/agenda; `md+`: visual asli |
| Navigasi bagian / sub-menu pengaturan | pil horizontal bisa digeser; `lg`: sidebar kiri |
| Grid 2 kolom halaman | `grid grid-cols-1 … xl:grid-cols-[minmax(0,1fr)_380px]` (selalu `grid-cols-1` di HP agar kolom bisa menyusut) |
| Flex berisi teks panjang | teks `basis-full sm:basis-0 sm:flex-1`, bukan `flex-1` saja |
| Chip/badge berisi kalimat | `badge wrap` (boleh terlipat), bukan `whitespace-nowrap` |
| KPI | `grid-cols-2`, kartu aksen `col-span-2` |

Sidebar off-canvas < `lg`. Gutter 16px. Tidak boleh ada scroll horizontal halaman.
