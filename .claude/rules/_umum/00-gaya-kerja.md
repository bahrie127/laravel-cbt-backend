# Gaya Kerja

Cara saya (Bahri) ingin proyek dikerjakan. Berlaku untuk semua proyek.

## Komunikasi

- Bahasa Indonesia, ringkas, langsung ke hasil. Tanpa basa-basi pembuka.
- Kalau saya bertanya "sudah semua?", jawab **jujur**: sebutkan yang belum, yang beda dari
  desain, dan alasannya. Jangan klaim selesai kalau belum diuji.
- Laporan akhir: apa yang dikerjakan (dikelompokkan per fitur), bug yang ditemukan saat uji,
  yang **belum** + alasannya, langkah deploy bila ada perubahan skema/permission/scheduler.
- Beri update singkat ("sedang mengerjakan X") saat pekerjaan panjang.
- Jangan bertanya hal yang bisa diputuskan dengan default yang wajar — putuskan, sebutkan, lanjut.
  Tanya hanya untuk keputusan bisnis yang benar-benar milik saya (vendor pihak ketiga,
  harga, cakupan besar seperti aplikasi mobile).

## Alur Kerja Per Permintaan

1. **Baca rules proyek** (`.claude/rules/*`) dan SSOT (PRD, desain) yang relevan sebelum menulis kode.
2. **Rencanakan sebagai phase** di `development-plan` proyek: checklist `- [ ]`, keputusan desain
   teknis yang dicatat, kondisi "selesai" yang jelas.
3. **Kerjakan tuntas end-to-end**: migration → model/enum → service → controller/route →
   view → seeder demo → test. Jangan berhenti di "tampilan saja" — tombol di desain harus berfungsi.
   Kalau fitur di desain ternyata hanya label (mis. aturan pengingat), **bangun mesinnya** juga.
4. **Verifikasi** (wajib, urut):
   - `php artisan test --compact` hijau
   - `vendor/bin/pint --dirty --format agent`
   - `npm run build` bila ada perubahan CSS/JS/kelas Tailwind baru
   - `php artisan migrate:fresh --seed` hijau
   - **Uji di Chrome** alur nyata dengan akun demo (lihat `04-testing-qa.md`)
   - Audit responsif 390px untuk halaman yang disentuh
5. **Catat progres** di development plan (centang checklist + catatan phase: jumlah test,
   gotcha, yang belum).
6. **Lapor**, lalu **tawarkan** commit/push — jangan commit/push tanpa diminta.

## Prinsip

- **Desain menang** atas selera sendiri. Sebelum membuat layar, cari layar desainnya.
  Bandingkan berdampingan di Chrome, perbaiki sampai persis (label, urutan, aksi, warna status).
- **Data demo realistis** dan lengkap supaya setiap layar terisi saat demo (seeder demo).
  Jangan memalsukan angka pemasaran/klaim (mis. "42 lembaga") — ganti dengan fakta.
- **Bug yang ditemukan saat uji = diperbaiki + test regresi**, lalu disebut di laporan.
- Perubahan kecil yang muncul dari audit (label, empty state, tombol kurang) langsung dikerjakan,
  tidak perlu ditanyakan.
- Di luar cakupan → tulis di Backlog development plan, bukan dikerjakan diam-diam.
