# Checklist Sebelum Selesai — CBT TPA (backend)

## Setiap fitur
- [ ] Rute di grup yang benar (`auth:sanctum` untuk app, `auth` + `pengelola` untuk web, `can:kelola-pengguna` untuk akun)
- [ ] Logika ujian di `UjianService`, bukan controller; kunci jawaban tidak keluar lewat API
- [ ] Bentuk JSON lama tetap (cek model Dart `flutter_cbt_app`); perubahan diikuti app
- [ ] Test ditambah (happy path, peran, validasi, waktu habis bila relevan)
- [ ] `vendor/bin/pint --dirty --format agent`

## Setiap phase
- [ ] `php artisan migrate:fresh --seed` hijau di MySQL
- [ ] `php artisan test --compact` hijau (DB `cbt_test`)
- [ ] Uji panel di Chrome (admin & staf, 390px) + alur peserta di emulator via proxy 8380
- [ ] Centang `14-development-plan.md`, laporan + langkah deploy (migrate, `storage:link`, `.env`)
