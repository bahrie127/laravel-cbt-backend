# Keamanan

## Multi-Tenant (sejak hari pertama, walau on-prem satu tenant)

- Semua tabel domain punya `tenant_id` + trait `BelongsToTenant` (global scope + isi otomatis).
- **Middleware tenant wajib di prioritas sebelum `SubstituteBindings`** (`bootstrap/app.php`
  → `prependToPriorityList`). Tanpa ini route model binding jalan tanpa scope dan record
  tenant lain bisa dibuka lewat ID. Wajib ada test regresinya.
- Model anak tanpa `tenant_id` dicek lewat induk yang ter-scope.
- Query lintas tenant hanya di Service superadmin, dengan `withoutGlobalScopes()` + komentar,
  atau konteks tenant diganti sementara lalu **dikembalikan di `finally`**.
- Upload di `storage/app/private/tenants/{id}/…`, diunduh lewat controller yang memeriksa tenant.

## Peran & Akses

- Permission `<modul>.<aksi>` (Spatie), matriks peran di rules proyek + seeder `RolePermissionSeeder`.
  Tambah permission baru → update seeder, matriks tampilan, dan langkah deploy (`db:seed --class=…`).
- Cek di server (Policy/`can()`), lalu sembunyikan tombol di Blade (`@can`).
- Peran baca-saja (auditor) → middleware menolak non-GET + banner + akses dicatat.
- Menu sidebar sesuai permission.
- Halaman 403 menyebut nama menu + peran saat ini, menawarkan "Minta akses", dan mencatat percobaan.

## Tautan Tanpa Login

- Pakai `URL::temporarySignedRoute()` + middleware `signed` + throttle; masa berlaku jelas.
- Sesi dari tautan **dibatasi** (middleware) hanya ke rute tujuan; dihapus saat login biasa.
- Token konfirmasi publik: acak ≥ 40 karakter, sekali pakai, kedaluwarsa.

## Auth

- Throttle login, `is_active` wajib, session regenerate saat login, invalidate saat logout.
- Login/logout dicatat. "Sesi berakhir" ditangani di halaman login (cookie email terakhir +
  `url.intended`) — Laravel modern meloloskan request same-origin tanpa token CSRF, jadi 419 jarang.

## Rahasia

- `.env` tidak pernah dibaca, dicetak, atau di-commit.
- Kredensial integrasi `encrypted:array` + `#[Hidden]`, tidak pernah dikirim ke browser
  (kolom kosong = tidak diubah).
- Integrasi keluar (WhatsApp, email, LLM) bisa dimatikan lewat config untuk on-prem.
- Mass assignment eksplisit.
