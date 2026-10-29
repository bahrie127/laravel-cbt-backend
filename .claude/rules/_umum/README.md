# Rules Umum — Gaya Kerja Bahri (portable)

Folder ini **tidak berisi detail proyek** — bisa disalin apa adanya ke proyek Laravel lain:

```bash
cp -r .claude/rules/_umum /path/proyek-lain/.claude/rules/_umum
```

Lalu di proyek baru, buat rules khusus proyek di `.claude/rules/01-…` (lihat tabel di bawah)
yang mengisi "slot" yang dirujuk rules umum ini.

| File | Isi |
|---|---|
| `00-gaya-kerja.md` | Alur kerja: rencana per phase → kerjakan → test → uji Chrome → lapor jujur |
| `01-stack-laravel.md` | Stack default & konvensi Laravel (Blade + Alpine + Tailwind 4) |
| `02-arsitektur.md` | Controller tipis, Form Request, Policy, Service, Enum, transisi status, audit log |
| `03-ui-desain.md` | Desain = SSOT, token warna, komponen Blade, responsif HP |
| `04-testing-qa.md` | Test wajib, uji Chrome, perbandingan desain, audit responsif 390px |
| `05-keamanan.md` | Multi-tenant, peran, empat mata, tautan bertanda tangan, rahasia |
| `06-git-deploy.md` | Commit/push hanya bila diminta, format pesan, checklist deploy |
| `07-bahasa-format.md` | Bahasa Indonesia di UI, format tanggal/angka/uang/durasi |

## Slot yang wajib diisi rules proyek

Rules umum merujuk hal-hal ini; definisikan di rules proyek (contoh nama file dari Tangguh BCM):

| Slot | Contoh file proyek |
|---|---|
| Ringkasan produk, persona, modul, SSOT (PRD + desain) | `01-overview.md` |
| Token warna & tipografi dari desain | `02-design.md` |
| Katalog komponen `x-ui.*` / `x-form.*` | `03-components.md` |
| Peta tabel & seeder demo + akun demo | `07-database.md` |
| Rumus & invarian bisnis (yang wajib ada test-nya) | `08-domain-rules.md` |
| Matriks peran × permission | `10-security.md` |
| Development plan per phase + status | `14-development-plan.md` |

Kalau rules umum bentrok dengan rules proyek → **rules proyek menang** (lebih spesifik).
