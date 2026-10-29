# Git & Deploy

## Commit / Push

- **Commit dan push hanya bila saya minta** ("commit dan push", "iya commit"). Setelah lapor,
  cukup tawarkan dalam satu kalimat.
- Sebelum commit: pastikan tidak ada `.env`, `*.log`, database lokal (`*.sqlite`) yang ter-stage.
- `git add -A` lalu satu commit per phase/permintaan. Format pesan:

```
Phase N: ringkasan singkat dalam Bahasa Indonesia

- Poin perubahan utama (fitur / perbaikan / test)
- …
- <jumlah> test hijau

Co-Authored-By: <attribution sesuai instruksi sesi>
```

- Branch kerja mengikuti proyek (biasanya `main`). Tidak force-push, tidak rewrite history.

## Checklist Deploy (sebut di laporan bila relevan)

- `php artisan migrate` bila ada migration baru.
- `php artisan db:seed --class=RolePermissionSeeder` bila ada permission baru.
- Cron `* * * * * php artisan schedule:run` bila ada job terjadwal baru.
- Aset sudah ter-build & ter-commit (`public/build`) — server tidak perlu `npm run build`.
- `php artisan optimize:clear` bila config/route berubah.
