# Arsitektur Aplikasi

## Lapisan

1. **Controller tipis** — merangkai saja: validasi di Form Request (pesan Indonesia),
   otorisasi di Policy/`can()`, logika domain di `app/Services`.
2. **Controller tidak mengubah `status` langsung** — selalu lewat method transisi
   (`$model->transitionTo(Enum::X, $user, $note)`) atau Service, yang memvalidasi transisi,
   melempar `DomainException`, dan mencatat activity log (`from`/`to`/`note`).
3. `DomainException` dirender global → `back()->with('error', pesan)`.
4. Query list: `->with([...])` (hindari N+1), `->paginate(25)->withQueryString()` untuk tabel panjang.
5. Redirect sukses: `to_route(...)->with('success', 'Kalimat Indonesia.')`.
6. Data disiapkan controller/view-model — **tidak ada query di Blade**.

## Enum

- Semua status/jenis = string-backed Enum dengan `label()` dan `color()`
  (`danger|warn|ok|brand|violet|teal|neutral`), plus `options()` untuk select.
- Transisi valid didefinisikan di enum (`allowedTransitions()`).
- Kelas CSS yang bergantung status (teks/titik) → method enum (`textClass()`, `dotClass()`)
  dengan string kelas literal, supaya Tailwind memindainya (`@source` ke `app/Enums`).
- Tidak ada magic string status di controller/Blade.

## Alur Persetujuan (pola yang selalu dipakai)

- **Empat mata**: pengaju ≠ penyetuju. Dicek di Service/Policy, bukan hanya disembunyikan di UI.
- Status: `draft → pending/review → approved`; kembalikan = catatan **wajib**.
- **Approved = immutable**. Revisi = versi/record baru (`*_versions`, periode baru).
- Semua yang menunggu keputusan masuk **satu Kotak Persetujuan** + badge sidebar + aksi
  "setujui terpilih" (tetap lewat aturan empat mata per item).

## Audit Trail

- Semua model domain `LogsActivity` (`logFillable()->logOnlyDirty()->dontSubmitEmptyLogs()->useLogName('<Modul>')`).
- Aksi penting non-CRUD dicatat manual: `activity('<Modul>')->causedBy()->performedOn()->withProperties(['old'=>…,'attributes'=>…])->log('Kalimat aktif')`.
- Akses ditolak (403), login/logout, akses auditor → log `access`/`auth`.

## Kode Tampil

- Identitas yang dilihat pengguna = kolom `code` (`BIA-2026-004`, `TM-0142`) dari trait `HasCode`
  (lock + urutan per tenant). Route binding tetap pakai `id`.

## Pengaturan per Tenant

- Preferensi yang jarang berubah (template, aturan, toggle) → `tenants.settings` (json) lewat
  Service khusus (`get/save/history`), versi naik setiap simpan, riwayat disimpan.
- Data yang harus bisa di-query/di-join → tabel sendiri, bukan json.

## Notifikasi

- Database notification selalu; mail/WhatsApp/SMS hanya bila integrasi terhubung **dan**
  `config('<app>.outbound_enabled')` (bisa dimatikan on-prem). MVP pakai driver `log`.
- Kategori notifikasi diturunkan dari kelas notifikasi (enum `NotificationCategory::types()`).
