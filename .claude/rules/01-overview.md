# CBT TPA — Backend & Panel Pengelola (`laravel-cbt-backend`)

Ujian Tes Potensi Akademik berbasis komputer (kelas FIC 10). Pasangan app peserta: `../flutter_cbt_app`.
Basis kode: branch `05-getnilai` (master = commit awal). Laravel 13.39 (dari 10), struktur lama (Kernel, Handler,
RouteServiceProvider) dipertahankan.

## Peran

| `users.roles` | Akses |
|---|---|
| `ADMIN` | Panel web penuh termasuk Pengguna (`can:kelola-pengguna`) |
| `STAFF` | Panel web: Dashboard, Hasil Ujian, Soal, Materi, Konten |
| `USER` (peserta) | Hanya API/app; ditolak di login web (Fortify `authenticateUsing`) dan middleware `pengelola` |

Login API menolak akun ADMIN/STAFF (403). Registrasi web Fortify dimatikan; peserta mendaftar lewat `POST /api/register`.

## Alur ujian (`App\Services\UjianService`, `config/cbt.php`)

- Satu ujian aktif per peserta (`selesai_at` null). `POST /api/create-ujian` mengembalikan ujian aktif bila ada (200),
  atau membuat baru (201) dengan `cbt.jumlah_soal` (20) soal acak per subtes.
- Subtes: `Numeric` (Tes Angka, kolom `*_angka`), `Verbal`, `Logika`. Peta kode → kolom di `config('cbt.kategori')`.
- Timer dihitung server: `mulai_<field>_at` diisi saat `get-soal-ujian` pertama; durasi `timer_<field>` menit
  (default 30); toleransi 5 dtk. Lewat batas → subtes ditutup & dinilai otomatis (lazy, saat diakses).
- Jawaban tersimpan di `ujian_soal_lists.jawaban` + `kebenaran`; respons `answers` **tidak** membocorkan benar/salah.
- `get-nilai` menutup subtes (idempoten). Tiga subtes selesai → `selesai_at` + `hasil` (`Lulus` bila rata-rata ≥ 60).
- Soal yang sudah dipakai ujian tidak boleh dihapus (nilai lama tetap konsisten).

## API (`routes/api.php`, tanpa prefix versi, Sanctum)

`POST /register`, `/login`, `/logout` · `GET /user` · `GET /ujian` (aktif/null) · `POST /create-ujian` ·
`GET /get-soal-ujian?kategori=` (soal tanpa kunci + `jawaban`, `timer` menit, `sisa_detik`, `status`) ·
`POST /answers {soal_id, jawaban a-d}` · `GET /get-nilai?kategori=` (`nilai`, `data{benar,salah,kosong}`, `ujian`) ·
`GET /hasil-ujian` (`terakhir`, `riwayat`) · `GET /contents?id=` (1 = Tentang Kami, 2 = Tips & Trik) · `GET /materis`.
Semua `api/*` selalu JSON (Handler `shouldReturnJson`), tanpa login → 401.

## Panel web (`routes/web.php`, Stisla)

`/home` dashboard nyata · `/hasil` hasil + detail jawaban per soal · `/soal` CRUD + filter subtes · `/materi` CRUD +
unggah gambar (disk `public`, perlu `storage:link`) · `/konten` ubah 2 konten app · `/user` (admin).

## Lokal

Port 8380 (uji emulator: `php -S 0.0.0.0:8389` dari `public/` + `node .claude/playbook/tools/emulator-proxy.mjs 8380 8389`
dari root cos). DB `cbt`, test `cbt_test` (phpunit.xml). `SESSION_COOKIE=cbt_session`. Locale `id`, zona waktu Asia/Jakarta.
Akun demo (sandi `password`): `admin@cbttpa.test`, `staf@cbttpa.test`, peserta `peserta@cbttpa.test` (Nadia, 2 riwayat)
+ 7 peserta `@cbttpa.test`. Bank soal demo 75 butir di `database/seeders/data/soal_tpa.php`.
