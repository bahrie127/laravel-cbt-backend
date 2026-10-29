# Konvensi Wajib — CBT TPA (backend)

`_umum/` berlaku; aturan di sini menang bila bentrok.

1. **Server = sumber kebenaran ujian.** Timer, kebenaran jawaban, nilai, dan status dihitung `UjianService`; app hanya
   menampilkan. Jangan menerima nilai/timer dari klien.
2. **Kunci jawaban tidak pernah keluar lewat API** (`get-soal-ujian`, `answers`). Panel web boleh menampilkannya.
3. **Kategori lewat `config('cbt.kategori')`**, bukan string tersebar. Kolom ujian memakai akhiran `angka/verbal/logika`.
4. **Penutupan subtes idempoten**; ujian baru hanya dibuat bila tidak ada ujian aktif.
5. **Kontrak JSON lama dipertahankan** (`timer`, `nilai`, `data`) karena dipakai app; field baru ditambahkan, tidak mengganti.
6. **Peran**: web hanya ADMIN/STAFF (`pengelola`), API peserta hanya USER; kelola akun hanya ADMIN.
7. **Data demo realistis** (bank soal TPA berbahasa Indonesia, riwayat relatif hari ini); tanpa lorem/faker di seeder utama.
8. Test di MySQL `cbt_test`; `travel()` untuk skenario waktu habis. Teks UI & pesan galat berbahasa Indonesia.

## Penyimpangan dari `_umum` (disengaja)

- Panel memakai **template Stisla (Bootstrap 4 + jQuery) dari `public/`**, bukan Tailwind/Alpine/komponen `x-ui` — tanpa
  build Vite. Halaman baru mengikuti pola Blade Stisla yang ada (`layouts.app`, `section-header`, `card`, `table-responsive`).
- Peran = kolom string `users.roles` (ADMIN/STAFF/USER) + Gate, tanpa Spatie; satu lembaga (tanpa multi-tenant).
- Struktur Laravel lama (Kernel/Handler/RouteServiceProvider) dipertahankan setelah upgrade.
- Hapus memakai `confirm()` bawaan (panel internal); uji Chrome menimpa `window.confirm` dulu.
