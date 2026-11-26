# SI-PROG

Sistem informasi praktikum mata kuliah Algoritma Pemrograman. Satu tempat untuk presensi QR, pre-test, tugas mingguan, penilaian, dan transparansi nilai.

Aplikasi punya tiga peran: mahasiswa, asisten, admin. Halaman `/` adalah portal publik berisi jadwal dan pengumuman kelas. Fitur lain baru terbuka setelah login.

## Fitur

**Mahasiswa**

- Presensi dengan memindai QR yang tampil di proyektor, lewat kamera HP atau unggah gambar.
- Pre-test 15 menit dengan soal esai dan potongan kode. Berpindah tab atau aplikasi tercatat sebagai pelanggaran dan memotong waktu pengerjaan.
- Unduh soal tugas, unggah jawaban PDF, dan lihat status pengumpulan.
- Halaman nilai yang menampilkan skor pre-test, tugas, nilai tambahan, total, catatan asisten, dan status kelulusan.
- Feedback setelah sesi selesai, plus pengumuman dari asisten.

**Asisten**

- Membuat dan mengatur sesi praktikum untuk kelas yang diampu.
- Menampilkan QR presensi di proyektor. Token QR berputar otomatis dan hanya token terbaru yang berlaku; daftar kehadiran ikut diperbarui otomatis.
- Menilai pre-test, tugas, dan nilai tambahan. Satu tugas per pertemuan.
- Review jawaban PDF mahasiswa, menyusun soal pre-test, memantau pengerjaan, memberi peringatan, dan membuka kunci pre-test.
- Mengelola pengumuman kelas.

**Admin**

- Data master kelas, mahasiswa, dan asisten.
- Semua sesi dari seluruh kelas.
- Export nilai per sesi dan rekap keseluruhan ke Excel.

## Teknologi

| Bagian    | Dipakai                                                                                    |
| --------- | ------------------------------------------------------------------------------------------ |
| Backend   | Laravel 13, PHP 8.3+                                                                       |
| Database  | MySQL atau MariaDB (SQLite juga bisa untuk lokal)                                          |
| Frontend  | Blade, Tailwind CSS, Alpine.js, jQuery, Vite                                               |
| Pendukung | endroid/qr-code (QR SVG), maatwebsite/excel (export), html5-qrcode, Chart.js, highlight.js |

URL resource memakai UUID, bukan ID angka. Zona waktu default `Asia/Makassar`.

## Tim Kelompok 6 Ilmu Komputer B

| Nama                                 | NIM       | Peran                              | Fokus kerja                                   |
| ------------------------------------ | --------- | ---------------------------------- | --------------------------------------------- |
| Muhammad Hidayat                     | F1G125043 | Sprint Orchestrator & Quality Gate | Scrum Master, pengujian, dan kontrol kualitas |
| Fitria Rizki Oktavia                 | F1G125006 | Visual Experience Architect        | Desain antarmuka dan aset visual              |
| Siti Munawara                        | F1G125018 | Data Architect & Schema Engineer   | Migrasi database dan seeder                   |
| Purnama Fatmawansyah Tri Putra Kilat | F1G125071 | Backend Flow Architect             | Controller dan pengaturan route               |
| Zolahuddin Ahmad                     | F1G125053 | Interface Markup Engineer          | Tampilan web berbasis HTML/Blade              |

## Instalasi

Kebutuhan:

- PHP 8.3 atau lebih baru dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `ctype`, `fileinfo`, `tokenizer`, `xml`, `zip`, `gd`, `iconv`
- Composer 2
- Node.js 22 dan npm
- MySQL 8 atau MariaDB

Langkah di lokal:

```bash
git clone <url-repositori> si-prog
cd si-prog

composer install
cp .env.example .env
php artisan key:generate
```

Buka `.env`, lalu sesuaikan bagian ini:

```env
APP_NAME="SI-PROG"
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Makassar

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=si_prog
DB_USERNAME=root
DB_PASSWORD=
```

Buat database dan jalankan migrasi:

```bash
mysql -u root -p -e "CREATE DATABASE si_prog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

php artisan migrate
npm install
npm run build
php artisan serve
```

Aplikasi terbuka di `http://127.0.0.1:8000`.

### Data awal (opsional)

`php artisan db:seed` mengisi kelas, asisten, dan mahasiswa dari file `PRAKTIKUM MATAKULIAH ALGORITMA PEMROGRAMAN (ALPRO).xlsx` di root proyek. Proses ini **menghapus data yang sudah ada**, jadi jangan jalankan di server yang sudah dipakai.

```bash
php artisan db:seed
```

Akun admin memakai `SEED_ADMIN_EMAIL` dan `SEED_ADMIN_PASSWORD` dari `.env`. Kalau `SEED_ADMIN_PASSWORD` kosong, seeder membuat kata sandi acak dan menampilkannya sekali di terminal. Akun mahasiswa dan asisten memakai NIM sebagai kata sandi awal, jadi ganti sebelum dipakai di server publik.

## Menjalankan saat pengembangan

```bash
php artisan serve
npm run dev
```

Jalankan keduanya di terminal terpisah. `npm run dev` menyediakan hot reload untuk CSS dan JavaScript. Untuk mengecek hasil build:

```bash
npm run build
```

## Pengujian

```bash
php artisan test
```

Test memakai SQLite in-memory sesuai `phpunit.xml`, jadi ekstensi `pdo_sqlite` perlu aktif.

## Deploy

Panduan lengkap ada di [`docs/DEPLOY-CHECKLIST.md`](docs/DEPLOY-CHECKLIST.md). Poin yang sering terlewat: `public/build` harus ikut terunggah, `APP_URL` harus memakai domain HTTPS yang benar, dan batas upload PHP minimal 12 MB.

## Lisensi

Aplikasi ini dibangun di atas Laravel dan memakai lisensi MIT.
