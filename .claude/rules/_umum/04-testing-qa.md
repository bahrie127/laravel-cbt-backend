# Testing & QA

## Test Otomatis (PHPUnit)

- Setiap rumus/invarian bisnis di rules domain **wajib** punya test.
- Wajib untuk setiap proyek multi-peran:
  - isolasi tenant (tenant A dapat **404** untuk record tenant B — list, show, binding),
  - peran baca-saja (POST/PUT/DELETE → 403),
  - empat mata (pengaju tidak bisa menyetujui), transisi status valid/tidak valid,
  - smoke test: setiap halaman utama 200 untuk admin (data provider berisi URL).
- Nama test Bahasa Indonesia snake_case: `test_pengaju_tidak_boleh_menyetujui_bia_sendiri`.
- Helper `TestCase::tenantWithUser(string $role, ?Tenant $tenant = null)`; factory/seeder untuk data.
- Bug yang ketemu saat uji Chrome → tambah test regresi.
- Jalankan yang sempit dulu (`--filter` / file), lalu seluruh suite sebelum lapor.

## Uji Manual di Chrome (wajib per fitur)

- **Tab sendiri per proyek.** Beberapa proyek sering diuji paralel di Chrome yang sama:
  selalu buka tab baru (`tabs_create_mcp`) untuk sesi uji ini, jangan memakai/menutup tab yang
  sudah ada milik proyek lain, dan jangan memakai ulang ID tab dari sesi sebelumnya.
- **Port sendiri per proyek** (lihat rules proyek, mis. ClinicPro `8030`). Cek dulu port bebas
  (`netstat -ano | grep LISTENING`); jangan mematikan server proyek lain.
- Login di origin yang berbeda port tetap berbagi cookie domain `127.0.0.1` — beri
  `SESSION_COOKIE` unik per proyek di `.env` agar sesi tidak saling menimpa.
- Server dev: `php artisan serve --port=<port proyek>` (+ `npm run build` atau `npm run dev`).
- Login akun demo dari seeder (password `password`) — uji dengan **beberapa peran**:
  admin, pengaju, penyetuju, peran baca-saja, superadmin. Alur empat mata diuji dengan dua akun.
- Uji jalur gagal juga: validasi salah → pesan muncul & modal terbuka lagi; akses ditolak.
- Setelah `migrate:fresh --seed`, sesi hilang → login ulang.
- Untuk tautan email (mailer `log`), ambil URL dari `storage/logs/laravel.log`.

## Bandingkan dengan Desain

- Sajikan bundle desain lokal: `php -S 127.0.0.1:8099 -t <folder-desain>` lalu buka di tab sendiri.
- Per layar: screenshot desain & aplikasi, catat perbedaan, perbaiki, ulangi.

## Audit Responsif 390px

Jendela Chrome minimal ±500px, jadi ukur di **iframe 390×780 dari origin aplikasi**.
Jalankan di tab aplikasi (sudah login) lewat JavaScript, lalu baca `window.__resp`:

```js
window.__audit = async (urls, W = 390) => {
  window.__resp = { done: false, i: 0, out: [] };
  const f = document.createElement('iframe');
  f.style.cssText = `position:fixed;left:0;top:0;width:${W}px;height:780px;z-index:99999;background:#fff`;
  document.body.appendChild(f);
  for (const u of urls) {
    await new Promise(r => { f.onload = () => setTimeout(r, 600); f.src = u; });
    const sw = f.contentDocument.documentElement.scrollWidth;
    if (sw > W + 1) window.__resp.out.push({ u, sw });
    window.__resp.i++;
  }
  f.remove(); window.__resp.done = true;
};
// window.__audit([...daftar URL dari PageSmokeTest...]);
```

- Halaman dengan `scrollWidth > 390` = bug. Cari elemen pelakunya dengan menelusuri anak
  yang `scrollWidth > 390` (sering teks panjang di flex sempit, grid tanpa `grid-cols-1`,
  badge `nowrap`).
- Lihat juga secara visual beberapa layar baru (screenshot area iframe).
- Periksa juga halaman tamu (landing, login, daftar) dan area superadmin.
