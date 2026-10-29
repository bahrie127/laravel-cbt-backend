# Bahasa & Format

## Bahasa

- `APP_LOCALE=id`, `APP_FALLBACK_LOCALE=en`, `APP_FAKER_LOCALE=id_ID`, timezone `Asia/Jakarta`.
- UI, pesan validasi (`lang/id`), notifikasi, email, dan PDF **Bahasa Indonesia penuh**.
  Kode (class, kolom, route name) Inggris.
- Gaya: kalimat pendek, kata kerja aktif, sapaan "Anda". Ikuti copy desain persis.
- Flash sukses berupa kalimat lengkap yang menyebut objeknya ("Laporan LAT-2026-08 dikunci sebagai bukti audit.").

## Helper Format (`app/Support/helpers.php`)

| Helper | Hasil |
|---|---|
| `id_date($d)` | `14 Okt 2026` (bulan singkat: Jan Feb Mar Apr Mei Jun Jul Agu Sep Okt Nov Des) |
| `id_datetime($d)` | `14 Okt 2026, 09:14` |
| `duration_label($menit)` | `15 mnt`, `4 jam`, `3 jam 10 mnt`, `1 hari`, `1 minggu` |
| `target_label($menit)` | `T+15 mnt`, `T+7,5 jam` |
| `rupiah($n)` | `Rp15.000.000` |
| `initials($nama)` | `DA` (maks 2 huruf) |

- Relatif: `$date->locale('id')->diffForHumans()` → "3 hari lalu".
- Angka: ribuan titik, desimal koma (`number_format($n, 0, ',', '.')`), angka tabel `tabular-nums` rata kanan.
- Durasi disimpan **menit (int)**; uang disimpan **rupiah (int)**.
- Tenggat lewat → warna danger + tebal, teks "Lewat · 30 Sep 2026".
- Format offset jadwal: `H-30, H-7, H+1` (H = tanggal acuan).
