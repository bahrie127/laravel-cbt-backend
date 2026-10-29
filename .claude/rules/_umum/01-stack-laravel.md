# Stack & Konvensi Laravel

## Stack Default (kecuali rules proyek berkata lain)

| Layer | Pilihan |
|---|---|
| Backend | Laravel versi terbaru yang terpasang (cek `composer show`), PHP 8.3+ |
| Frontend web | **Blade + Alpine.js 3 + Tailwind CSS 4** — tanpa Livewire, Inertia, Filament |
| Build | Vite + `@tailwindcss/vite`; font self-host (`@fontsource-*`) agar aman on-prem |
| DB | MariaDB/MySQL; test pakai SQLite in-memory → migration harus kompatibel keduanya |
| Session/cache/queue | `database` (Redis opsional) |
| Peran & audit | `spatie/laravel-permission`, `spatie/laravel-activitylog` |
| PDF | `barryvdh/laravel-dompdf` |
| Test & gaya | PHPUnit + Laravel Pint; `laravel/boost` (dev) |

- **Jangan menambah dependensi** tanpa persetujuan saya.
- Hasil `npm run build` (`public/build`) **ikut di-commit** supaya server tidak perlu Node.

## Konvensi PHP

- Constructor property promotion, return type & type hint eksplisit, `match` untuk mapping enum.
- Kurung kurawal selalu. PHPDoc untuk array shape; komentar inline hanya untuk logika tak jelas.
- Model: `#[Fillable([...])]` eksplisit (tanpa `$guarded = []`), `casts()` method, relasi ber-return type.
- Query reusable → local scope. Model tidak memanggil HTTP/notifikasi — itu Service/Job.
- Pakai `php artisan make:*` (`--no-interaction`) untuk membuat file.
- Tidak ada `dd()`, `dump()`, `ray()` tertinggal.

## Penamaan

- Class, kolom, nama route: **Inggris** (`BusinessProcess`, `business_processes`, `processes.index`).
- URL & teks UI: **Indonesia** (`/data-induk/proses`).
- Service = kata benda + `Service`/`Calculator`/`Dispatcher` (`PlanVersioningService`).
- Single-action controller untuk aksi domain non-CRUD.

## Migration

- Satu migration = satu tabel (+ pivot). Jangan ubah migration yang sudah jalan di server — buat baru.
- Enum disimpan `string(30)` (validasi di PHP Enum, bukan ENUM DB).
- Durasi `unsignedInteger('..._minutes')`, uang `unsignedBigInteger` rupiah.
- Isi terstruktur yang dibaca utuh → kolom `json`. Data yang harus bisa diaudit → `softDeletes()`.
- Index gabungan untuk filter list (`[tenant_id, status]`).

## Artisan & Scheduler

- Command domain bernama `<prefix>:<aksi>` dengan `#[Signature]` + `#[Description]`, opsi
  `--date=` / `--tenant=` agar bisa diuji manual.
- Job harian didaftarkan di `routes/console.php` (`Schedule::command(...)`).
- Command yang mengirim notifikasi harus **idempoten** (tidak dobel bila jalan dua kali).
