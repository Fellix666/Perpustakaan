## 1. Judul & Deskripsi Singkat

Nama Aplikasi: Perpustakaan SMP Negeri 1 Sanggau Ledo

Deskripsi: Aplikasi web untuk mengelola data anggota, buku, transaksi peminjaman/pengembalian, denda, pengunjung, serta laporan dan analisis peminjaman.

---

## 2. Deskripsi Ringkas Fungsi dan Tujuan

Aplikasi ini membantu pustakawan dalam:

- Mencatat dan mengelola data anggota dan buku
- Mengelola transaksi peminjaman dan pengembalian
- Menghitung keterlambatan dan denda
- Mencatat data pengunjung perpustakaan
- Menyajikan laporan dan statistik untuk pengambilan keputusan

### Fitur Utama

- Data Anggota: tambah, ubah, hapus, cetak kartu, import Excel, upload foto ZIP
- Data Buku: tambah, ubah, hapus, kategori buku, data rak
- Transaksi: peminjaman, pengembalian, perhitungan denda
- Data Pengunjung: pencatatan kunjungan harian
- Laporan: peminjaman, pengunjung, denda & keterlambatan, analisis peminjaman per kelas
- Dashboard: ringkasan metrik (anggota aktif, judul buku, peminjaman, denda)

---

## 3. Teknologi yang Digunakan

- Framework: Laravel (PHP 8.x)
- Frontend: Blade + Bootstrap 5, Vite
- Database: MySQL/MariaDB
- Tooling: Node.js & npm

---

## 4. Instalasi & Konfigurasi (Ringkas)

1) Clone ke contoh: `C:\xampp\htdocs\TUGASAKHIR\Perpustakaan`
2) Jalankan:
   - `composer install`
   - `npm install`
3) Salin `.env` & key:
   - `copy .env.example .env`
   - `php artisan key:generate`
4) Atur database pada `.env` lalu buat database tersebut
5) Migrasi & (opsional) seeder:
   - `php artisan migrate`
   - `php artisan db:seed`
6) Jalankan aplikasi:
   - `php artisan serve` (<http://127.0.0.1:8000>) atau via XAMPP ke folder `public/`
7) Aset: `npm run dev` (dev) atau `npm run build` (produksi)

---

## 5. Screenshots (Opsional)

- Login: `resources/screenshots/login.png`
- Dashboard: `resources/screenshots/dashboard.png`
- Data Anggota: `resources/screenshots/anggota.png`
- Peminjaman: `resources/screenshots/peminjaman.png`
- Pengembalian: `resources/screenshots/pengembalian.png`
- Laporan: `resources/screenshots/laporan.png`

Letakkan file gambar sesuai path di atas atau sesuaikan dengan lokasi Anda.

---

## 6. Struktur Folder (Opsional)

```
Perpustakaan/
  app/
  resources/
    views/
    js, css
  public/
  routes/
  database/
  storage/
```

---

## 7. Lisensi

Private (Internal). Hak cipta milik pengembang dan pihak sekolah terkait.
