# Product Requirements Document (PRD)

## Sistem Pendataan & Pelacakan Kiriman Santri (SIPENRI)

**Pondok Pesantren Salafiyah Syafi'iyah Sukorejo, Situbondo**

- **Dokumen Versi:** 1.0.0
- **Tanggal:** 10 Agustus 2026
- **Penulis:** Albi Guntara (el_gntara)

---

## 1. Ringkasan Eksekutif

Pondok Pesantren Salafiyah Syafi'iyah Sukorejo menampung puluhan ribu santri yang berasal dari berbagai daerah di Indonesia. Setiap bulan, wali santri secara rutin mengirimkan paket barang dan dana melalui perwakilan koordinator daerah IKSASS masing-masing (seperti Bali, Lombok, Jakarta, Situbondo, dan wilayah lainnya). Tantangan utama yang dihadapi saat ini adalah tingginya volume pengiriman manual, risiko miskomunikasi data, kesulitan dalam pelacakan status fisik barang, serta belum adanya standardisasi label stiker pengiriman yang seragam.

Aplikasi SIPENRI dibangun di atas fondasi teknologi Laravel dan Vue.js untuk menghadirkan pengalaman pengguna yang cepat, responsif, dan aman. Fitur utama mencakup isolasi data multi-wilayah (multi-region data isolation), manajemen data santri terpusat, pencetakan stiker termal otomatis, serta track record pengiriman berbasis linimasa (timeline) dengan lampiran bukti foto saat paket sampai dan diserahkan kepada santri.

## 2. Latar Belakang dan Tujuan Proyek

### 2.1 Latar Belakang Masalah

Proses pengiriman logistik bulanan santri dari wali santri melalui perwakilan IKSASS daerah menghadapi berbagai kendala operasional:

- **Pencatatan yang Terfragmentasi:** Sebagian pengurus daerah masih menggunakan pencatatan manual atau spreadsheet terpisah yang menyulitkan rekapitulasi tingkat pusat pesantren.
- **Potensi Kebocoran Data Antar-Wilayah:** Pengurus IKSASS daerah tertentu seharusnya hanya berhak mengelola dan melihat data kiriman dari daerahnya sendiri, tanpa akses silang ke data daerah lain demi menjaga privasi dan batasan administratif.
- **Identifikasi Paket yang Kurang Efisien:** Paket yang tiba di pesantren sering kali tertukar atau sulit diidentifikasi pemiliknya karena tidak memiliki stiker penanda standar yang mencantumkan Nomor Induk Santri (NIS), nama lengkap, asrama, dan jumlah barang.
- **Kurangnya Transparansi Status Pengiriman:** Wali santri dan pengurus sering kesulitan memantau posisi terkini barang, apakah sudah diberangkatkan dari daerah asal, tiba di pondok, hingga langsung diserahkan kepada santri terkait.

Sistem Pendataan & Pelacakan Kiriman Santri (SIPENRI) dirancang sebagai solusi digital terintegrasi untuk mengelola, mencatat, dan melacak arus pengiriman paket serta titipan uang dari wali santri melalui jaringan Ikatan Santri Alumni Salafiyah Syafi'iyah (IKSASS) se-Nusantara menuju Pondok Pesantren Salafiyah Syafi'iyah Sukorejo.

### 2.2 Tujuan Proyek

- Membangun sistem pencatatan kiriman santri yang terdigitalisasi dan terstruktur dengan basis data relasional.
- Menerapkan pembatasan akses ketat berbasis peran wilayah (Regional-based Access Control / Multi-Tenancy).
- Menyediakan fitur pencetakan stiker label paket secara massal maupun satuan dengan format standar pesantren.
- Menyediakan fitur pelacakan (tracking) lengkap dengan riwayat status dan bukti foto saat paket sampai di pondok dan diserahkan langsung kepada santri terkait.
- Memanfaatkan starter-kit Laravel dan Vue.js yang telah tersedia untuk mempercepat proses pengembangan perangkat lunak.

## 3. Pengguna Sistem dan Hak Akses (User Roles & Permissions)

Sistem ini membagi hak akses pengguna menjadi dua tingkat utama untuk memastikan keamanan dan isolasi data operasional antar-wilayah:

| Peran Pengguna (Role) | Cakupan Akses & Wilayah | Hak Istimewa & Operasional |
|---|---|---|
| **Super Admin / Pusat** | Seluruh Pondok Pesantren & Seluruh Wilayah IKSASS | Mengelola data master santri nasional, mengelola akun pengurus IKSASS dari berbagai wilayah, melihat rekapitulasi nasional, serta melakukan audit sistem secara menyeluruh. |
| **Admin IKSASS Daerah** | Wilayah spesifik (contoh: IKSASS Bali, Lombok, Jakarta, Situbondo, dll.) | Menginput data kiriman wali santri dari daerahnya, mencetak stiker paket daerah, memperbarui status pengiriman (berangkat ke pondok, tiba di pondok, hingga diserahkan langsung kepada santri), mengunggah bukti foto saat paket sampai/diserahkan, dan melihat laporan historis khusus daerah tersebut. Tidak dapat melihat data daerah lain. |

### Catatan Operasional

Berdasarkan realita di lapangan, **tidak ada peran khusus "Pengurus Logistik Pesantren"** dalam alur pengiriman SIPENRI. Setelah paket santri yang dikirimkan oleh IKSASS sampai di pondok, paket tersebut **langsung diserahkan kepada santri yang bersangkutan tanpa perantara pengurus logistik**.

Pengurus IKSASS daerah atau pengantar yang mendampingi pengiriman bertanggung jawab memberikan dokumentasi berupa foto sebagai bukti bahwa paket telah sampai dan/atau telah diserahkan kepada santri. Dokumentasi tersebut menjadi bukti dalam sistem.

## 4. Fitur Utama dan Kebutuhan Fungsional (Functional Requirements)

### 4.1 Manajemen Data Santri & Master Data

- **Impor dan Sinkronisasi Data Santri:** Sistem menyediakan fasilitas pengelolaan data santri yang mencakup Nomor Induk Santri (NIS), Nama Lengkap, Asrama, dan Lokasi Santri (Pusat Putra, Pusat Putri, Ma'had Aly, Nurul Jadid/Qodliyah, Al-Khuzaimah, Az-Zainiyah, dll.).
- **Pencarian Cepat:** Memudahkan pengurus IKSASS mencari data santri berdasarkan NIS atau nama saat melakukan input pengiriman agar tidak terjadi duplikasi atau salah alamat asrama.

### 4.2 Modul Input & Pengelolaan Kiriman

Berdasarkan data operasional historis, setiap entri kiriman memuat atribut penting berikut:

- `nis / santri_id`
- `jumlah_uang`
- `jumlah_paket`
- `nama_pengirim`
- `nomor_hp_pengirim`
- `lokasi_santri`
- `region_iksass_id`
- `nama_wilayah_asal_pengiriman` (IKSASS, otomatis terkunci sesuai akun login daerah)

Admin IKSASS daerah dapat:

- Menginput data kiriman wali santri dari daerahnya.
- Mencetak stiker paket daerah.
- Memperbarui status pengiriman.
- Mencatat status bahwa paket telah tiba di pondok.
- Mengunggah dokumentasi foto saat paket sampai dan/atau diserahkan kepada santri.
- Melihat laporan historis khusus daerah tersebut.
- Tidak dapat melihat data daerah lain.

### 4.3 Fitur Pencetakan Stiker Label Paket

- **Generator Stiker Otomatis:** Setiap transaksi kiriman yang berhasil disimpan akan menghasilkan data untuk dicetak pada stiker label ukuran standar thermal printer.
- **Informasi pada Stiker:** Stiker mencakup Logo Pesantren, Nama Santri, NIS, Asrama, Jumlah Paket (misal: Paket 1 dari 2), Nama Pengirim, Asal Daerah IKSASS, serta QR Code / Barcode unik untuk keperluan verifikasi cepat.
- **Cetak Massal (Batch Printing):** Pengurus IKSASS dapat memilih beberapa data kiriman sekaligus untuk dicetak dalam satu halaman lembar label.

### 4.4 Track Record & Bukti Pengiriman Berbasis Linimasa (Timeline)

Sistem menyediakan siklus hidup pengiriman (Shipping Lifecycle) yang mencerminkan kondisi riil di lapangan:

1. **Draft / Dibuat:** Pengurus IKSASS daerah mencatat data kiriman dan stiker dicetak untuk ditempel pada paket.
2. **Dalam Perjalanan (In Transit):** Paket diberangkatkan dari daerah asal menuju Pondok Pesantren Sukorejo.
3. **Tiba & Diserahkan ke Santri (Arrived & Delivered):** Paket telah sampai di pondok dan langsung diserahkan kepada santri yang bersangkutan tanpa perantara pengurus logistik pesantren. Pengurus IKSASS atau pengantar memberikan dokumentasi foto sebagai bukti bahwa paket telah sampai dan/atau telah diserahkan kepada santri.

> **Catatan:** Dokumentasi pada tahap akhir merupakan bukti dari pihak pengirim/pengantar bahwa paket telah sampai dan diserahkan. Sistem tidak mensyaratkan adanya petugas logistik pesantren sebagai perantara penerimaan paket.

## 5. Atribut Data Kiriman

| Nama Atribut | Tipe Data | Deskripsi / Keterangan |
|---|---|---|
| `nis / santri_id` | Foreign Key / String | Menghubungkan kiriman dengan data master santri. |
| `jumlah_uang` | Decimal / Integer | Nominal uang yang dikirimkan oleh wali santri (dalam Rupiah). |
| `jumlah_paket` | Integer | Jumlah koli/fisik paket barang yang dikirimkan. |
| `nama_pengirim` | String | Nama lengkap wali santri atau pihak pengirim di daerah. |
| `nomor_hp_pengirim` | String | Nomor telepon / WhatsApp pengirim untuk keperluan konfirmasi. |
| `lokasi_santri` | Enum / String | Penempatan asrama / unit pendidikan santri (Pusat, Cabang, dll.). |
| `region_iksass_id` | Foreign Key | Wilayah asal pengiriman IKSASS (otomatis terkunci sesuai akun login daerah). |

## 6. Rancangan Basis Data (Database Schema Design)

### 6.1 Tabel `regions` (Wilayah IKSASS)

- `id` — Primary Key, Incremental
- `name` — String, contoh: `"IKSASS Bali"`, `"IKSASS Jakarta"`
- `code` — String, contoh: `"BALI"`, `"JKT"`
- `created_at`
- `updated_at`

### 6.2 Tabel `users` (Pengguna Sistem)

- `id` — Primary Key
- `name` — String
- `email` — String, Unique
- `password` — String
- `role` — Enum: `super_admin`, `iksass_admin`
- `region_iksass_id` — Foreign Key ke `regions`, Nullable untuk Super Admin
- `created_at`
- `updated_at`

### 6.3 Tabel `students` (Data Santri)

- `id` — Primary Key
- `nis` — String, Unique
- `name` — String
- `dormitory` — String
- `location_type` — String, contoh: `"Pusat Putri"`, `"Pusat Putra"`, `"MQ Putri"`
- `created_at`
- `updated_at`

### 6.4 Tabel `shipments` (Data Kiriman)

- `id` — Primary Key
- `tracking_code` — String, Unique: generated barcode reference
- `student_id` — Foreign Key ke `students`
- `region_iksass_id` — Foreign Key ke `regions`: mengunci data milik daerah tertentu
- `sender_name` — String
- `sender_phone` — String
- `amount_money` — Decimal, default sesuai konfigurasi sistem
- `package_count` — Integer, default sesuai konfigurasi sistem
- `status` — Enum: `created`, `in_transit`, `delivered`
- `created_by` — Foreign Key ke `users`
- `created_at`, `updated_at`

### 6.5 Tabel `shipment_logs` (Riwayat & Lampiran Bukti Foto)

- `id` — Primary Key
- `shipment_id` — Foreign Key ke `shipments`
- `status_from` — String
- `status_to` — String
- `notes` — Text, Nullable
- `attachment_path` — String, Nullable: path file bukti foto saat paket sampai dan/atau diserahkan kepada santri
- `logged_by` — Foreign Key ke `users`
- `created_at` — Timestamp

## 7. Arsitektur Teknis dan Teknologi (Tech Stack)

Proyek ini memanfaatkan infrastruktur starter-kit Laravel-Vue yang telah disiapkan:

- **Backend Framework:** Laravel menyediakan RESTful API atau Inertia.js controller, manajemen autentikasi (Laravel Sanctum / Breeze), serta migrasi basis data yang kokoh.
- **Frontend Framework:** Vue.js (Composition API) dengan Vite untuk antarmuka pengguna yang reaktif dan cepat.
- **UI Styling:** Tailwind CSS untuk desain antarmuka yang bersih, modern, dan ramah perangkat seluler (mobile-responsive).
- **PDF & Sticker Rendering:** DomPDF atau pustaka JavaScript pembuat QR Code/Barcode untuk pencetakan stiker label termal berukuran presisi.
- **Manajemen Keamanan Data:** Implementasi Global Scopes atau Eloquent Local Scopes di Laravel untuk otomatis memfilter query berdasarkan `region_iksass_id` pengguna yang sedang login, memastikan isolasi data antar-wilayah secara ketat.

## 8. Rencana Implementasi & Pengembangan (Implementation Roadmap)

| Fase | Fokus Pekerjaan |
|---|---|
| **Fase 1** | Konfigurasi Database, Migrasi, & Model Relasi (Termasuk Multi-Tenant Scope)
| **Fase 2** | Pengembangan API & Interface Manajemen Santri & Autentikasi Peran Daerah
| **Fase 3** | Modul Input Kiriman, Validasi Data, & Desain Layout Cetak Stiker
| **Fase 4** | Modul Tracking Linimasa (Timeline) & Unggah Lampiran Bukti Foto
| **Fase 5** | Pengujian Integrasi, Uji Coba Pengguna (UAT), & Deployment Produksi

## 9. Kesimpulan

Dokumen Persyaratan Produk (PRD) ini telah disesuaikan dengan kondisi riil operasional di Pondok Pesantren Salafiyah Syafi'iyah Sukorejo. Alur pengiriman tidak menggunakan peran perantara logistik pesantren. Setelah paket sampai di pondok, paket langsung diserahkan kepada santri terkait, sedangkan dokumentasi foto diberikan oleh pengurus IKSASS atau pengantar sebagai bukti bahwa paket telah sampai dan/atau diserahkan.

Dengan alur tersebut, sistem SIPENRI dapat berjalan secara efisien, akurat, dan tetap menjaga privasi antar-wilayah pengurus IKSASS se-Nusantara.

---

> Dokumen ini disusun secara profesional oleh Manus AI untuk mendukung digitalisasi Pondok Pesantren Salafiyah Syafi'iyah Sukorejo.