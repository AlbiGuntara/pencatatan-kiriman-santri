# SIPENRI

> **Sistema Kiriman IKSASS** — Sistem pencatatan dan pelacakan kiriman (uang & paket) dari wali santri ke santri di lingkungan organisasi IKSASS (Pondok Pesantren Salafiyah Syafi'iyah Sukorejo).

SIPENRI adalah sebuah *single-page application* web untuk mencatat, melacak, dan mengaudit setiap **kiriman** (uang, paket, nominal) dari pengirim/wali hingga sampai ke santri. Data diorganisasi per **wilayah IKSASS daerah**, di mana setiap admin daerah hanya melihat dan mengelola datanya sendiri, sementara **Super Admin / Pusat** mengawasi seluruh wilayah.

## Fitur

- **Dashboard operasional** — hero sapaan, KPI (total kiriman, dalam perjalanan, diserahkan, nominal total), grafik aktivitas 7 hari terakhir, donat distribusi status, kiriman terbaru, dan distribusi per wilayah (super admin).
- **Manajemen kiriman** — pencatatan lengkap: kode tracking, pengirim, NIS & nama santri tujuan, jumlah paket/koli, nominal uang, dan wilayah. Siklus status `dibuat → dalam perjalanan → diserahkan` dengan riwayat log dan bukti foto.
- **Cetak stiker tracking** — halaman khusus *print stickers* untuk kebutuhan operasional (label tempel).
- **Import & ekspor Excel** — import massal kiriman (dengan template yang bisa diunduh), *bulk status update*, *bulk delete*, dan ekspor/cetak rekaman.
- **Master data santri** — data santri berkunci NIS, pencarian cepat (juga endpoint pencarian untuk form), filter lokasi & wilayah.
- **Manajemen wilayah & pengguna (Super Admin)** — kelola wilayah IKSASS dan akun pengguna.
- **Autentikasi Fortify** — email/password, verifikasi email, 2FA, dan pengelolaan *passkey*.
- **Mode gelap** — dark mode penuh yang di-*tuning* khusus (lihat `DESIGN.md`).

## Peran Pengguna

| Peran | Cakupan |
| --- | --- |
| **Admin IKSASS Daerah** (`iksass_admin`) | Mencatat & melacak kiriman milik wilayahnya sendiri, mengelola data santri; melihat dashboard ringkasannya. |
| **Super Admin / Pusat** (`super_admin`) | Mengawasi seluruh kiriman semua wilayah, mengelola wilayah IKSASS dan akun pengguna. |

## Teknologi

- **Backend:** Laravel 13 (PHP ≥ 8.3), Laravel Fortify, Inertia.js, Wayfinder (`--with-form`)
- **Frontend:** Vue 3 (Composition API + `<script setup>`), Tailwind CSS v4, shadcn-vue / Reka UI
- **Pendukung:** PHPSpreadsheet (Excel), Chart SVG buatan sendiri (tanpa dependensi charting)

## Struktur Proyek

```
app/
├── Http/Controllers/     # Dashboard, Shipment, Student, Region, User
├── Models/               # Shipment, ShipmentLog, Student, Region, User
└── (Fortify / 2FA)
resources/
├── css/app.css           # Token Tailwind v4 + tema light & dark
└── js/
    ├── pages/            # Dashboard, shipments/, students/, regions/, users/, settings/, auth/
    ├── components/       # DataTable, ShipmentStatusBadge, charts/, ui/ (shadcn)
    ├── layouts/          # Aplikasi (sidebar) & autentikasi
    └── routes/ …actions/ # Dibuat otomatis oleh Wayfinder saat build (gitignored)
routes/web.php            # Route aplikasi
DESIGN.md                 # Sistem desain (emerald elegan, token, dark mode)
PRODUCT.md                # Konteks produk, persona, dan prinsip
```

## Persiapan Instalasi

1. **Setup otomatis** (jika `.env` belum ada):

   ```bash
   composer setup
   ```

   Script ini menjalankan: `composer install` → salin `.env` → `php artisan key:generate` → `migrate --force` → `npm install` → `npm run build`.

2. **Setup manual**, langkah demi langkah:

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   # sesuaikan konfigurasi database di .env
   php artisan migrate
   npm install
   npm run build
   ```

3. **Jalankan aplikasi** (mode pengembangan):

   ```bash
   npm run dev
   ```
   atau dengan Vite + server Laravel sekaligus:
   ```bash
   composer dev
   ```

Akses aplikasi di `http://localhost:8000`.

## Perintah Pengembangan

| Perintah | Fungsi |
| --- | --- |
| `npm run build` | Build produksi assets frontend (Vite) |
| `npm run dev` | Vite dev server (HMR) |
| `npm run format` / `format:check` | Format prettier (`resources/`) |
| `npm run lint` / `lint:check` | ESLint (auto-fix) / cek |
| `npm run types:check` | Type-check Vue/TS (`vue-tsc --noEmit`) |
| `composer lint` | Pint (PHP code style) |
| `php artisan test` | Pest test suite |

## Desain

Identitas visual **emerald elegan**: emerald sebagai warna struktural/utama, aksen emas hanya untuk momen perayaan (CTA "Input Kiriman", nilai nominal, kartu Aksi Cepat). Semua token tema (warna, radius, bayangan) hidup di `resources/css/app.css`; detail lengkap ada di [`DESIGN.md`](./DESIGN.md).

> Aturan inti: gunakan token, jangan hardcode hex di komponen; uji setiap status/state di kedua tema (terang & gelap); chart tetap SVG buatan sendiri.

## Dokumentasi Terkait

- [`DESIGN.md`](./DESIGN.md) — sistem desain, token, komponen, dark mode.
- [`PRODUCT.md`](./PRODUCT.md) — konteks produk, pengguna, prinsip produk.

## Lisensi

Proyek berbasis skeleton [laravel/vue-starter-kit](https://github.com/laravel/vue-starter-kit) dengan lisensi MIT.