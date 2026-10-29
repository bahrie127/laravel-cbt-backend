# Development Plan — CBT TPA (backend)

**Kondisi awal (28 Okt 2026):** branch `05-getnilai` (Des 2023), Laravel 10. `get-nilai` berisi `dd()` (app tidak pernah
menerima nilai); ujian selalu memakai ujian pertama user (tidak bisa mengulang); timer `null` → app langsung "Waktu
Habis"; respons jawaban membocorkan benar/salah; peserta bisa login & mendaftar ke panel admin; registrasi API
menulis kolom `role` (salah); `apiResource` konten/materi membuka store/update/delete kosong; halaman soal admin error
(`$user` tak terdefinisi), tanpa ubah/hapus; dashboard angka statis; seeder lorem ipsum; Google Analytics milik template.

## Phase 1 — Hidup, alur ujian benar, panel nyata (SELESAI 28 Okt 2026)

- [x] Laravel 10 → 13.39 (sanctum 4, tinker 3, fortify 1.30, phpunit 12, boost), locale `id`, zona waktu WIB
- [x] Migrasi alur: `mulai_*_at`, `selesai_at`, `ujian_soal_lists.jawaban` + unik, `soals.pertanyaan` text
- [x] `UjianService`: satu ujian aktif, timer server + tutup otomatis, jawaban tersimpan, nilai idempoten, hasil Lulus/Belum Lulus
- [x] API baru `GET /ujian`, `GET /hasil-ujian`; `get-nilai` tanpa `dd`, kunci tidak bocor, `api/*` selalu JSON (401)
- [x] Auth: register `roles=USER`, pesan login seragam, admin/staf ditolak di API; panel hanya ADMIN/STAFF, registrasi web mati
- [x] Panel: dashboard nyata, hasil ujian + detail jawaban, CRUD soal (soal terpakai dilindungi), CRUD materi + gambar, konten, pengguna (admin)
- [x] Seeder: 75 soal TPA, 6 materi, 2 konten, 2 pengelola + 8 peserta + 14 riwayat ujian
- [x] Test: 17 (API alur ujian, waktu habis, peran, panel, unggah materi, regresi bocor nilai)
- Bug saat uji emulator/Chrome: ringkasan `GET /api/ujian` membocorkan nilai & benar/salah subtes berjalan (operator `+` PHP tidak menimpa kunci) → `array_merge` + test; tombol aksi tabel admin terlipat → `text-nowrap`; teks tips no. 5 keliru.

## Backlog

- Pembahasan per soal + tinjau jawaban di app setelah subtes selesai.
- Impor soal massal (CSV/Excel) dan bank soal per paket/tingkat.
- Penjadwalan ujian (jendela waktu) dan token ujian untuk kelas/batch.
- Penutupan subtes kedaluwarsa lewat scheduler (sekarang lazy saat diakses).
