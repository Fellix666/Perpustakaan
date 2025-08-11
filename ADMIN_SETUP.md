# 📋 PANDUAN SETUP ADMIN - PERPUSTAKAAN SMP NEGERI 1 SANGGAU LEDO

## 🎯 AKUN DEFAULT

- **Email:** `admin@perpustakaan.com`
- **Password:** `admin123`
- **Role:** Administrator

## 🔧 CARA MEMBUAT ADMIN BARU

### 1. Via Web Interface (Setelah Login)

1. Login sebagai Administrator
2. Klik dropdown "Administrator" di navbar
3. Pilih "Profile"
4. Klik tab "Manage Admin" (atau langsung ke halaman Manage Admin)
5. Klik tombol "Tambah Admin"

### 2. Via Command Line (Jika Tidak Bisa Login)

```bash
# Buat admin baru
php artisan admin:create "Nama Admin" "email@domain.com" "password123" admin

# Contoh:
php artisan admin:create "Kepala Perpus" "kepala@perpustakaan.com" "kepala123" kepala_perpus
```

### 3. Reset Password Admin

```bash
# Reset password admin yang sudah ada
php artisan admin:reset-password admin@perpustakaan.com password123
```

## 🚨 JIKA TIDAK BISA LOGIN

### Langkah 1: Cek Database

```bash
# Pastikan database ada
ls database/database.sqlite
```

### Langkah 2: Jalankan Migration

```bash
php artisan migrate
```

### Langkah 3: Buat Admin Pertama

```bash
php artisan admin:create "Administrator" "admin@perpustakaan.com" "admin123" admin
```

### Langkah 4: Clear Cache

```bash
php artisan config:clear
php artisan view:clear
```

## 📊 PANDUAN DATA BESAR (RATUSAN/RIBUAN DATA)

### 🎯 KAPASITAS UPLOAD YANG DIOPTIMASI

#### **📁 File ZIP (Foto/Cover):**

- ✅ **Maksimal:** `100 MB` (ditingkatkan dari 20MB)
- ✅ **Foto Anggota:** ~500-1000 foto (100KB per foto)
- ✅ **Cover Buku:** ~200-500 cover (200KB per cover)

#### **📊 File Excel (Data):**

- ✅ **Maksimal:** `100 MB` (ditingkatkan dari 40MB)
- ✅ **Data Anggota:** ~10.000-50.000 baris
- ✅ **Data Buku:** ~5.000-20.000 baris

### 🚀 OPTIMASI UNTUK DATA BESAR

#### **1. Import Excel dengan Chunking:**

- ✅ **Proses per 100 baris** - Menghemat memory
- ✅ **Progress feedback** - Untuk data >1000 baris
- ✅ **Error handling** - Batasi tampilan error (max 10)

#### **2. Upload ZIP yang Dioptimasi:**

- ✅ **Ekstraksi manual** - Lebih cepat dan aman
- ✅ **Skip file sistem** - Lewati __MACOSX, ._files
- ✅ **Batch processing** - Proses file per batch

### 📋 TIPS UNTUK DATA BESAR

#### **A. Persiapan File Excel:**

1. **Kompresi file** - Pastikan file tidak terlalu besar
2. **Format yang benar** - Gunakan template yang disediakan
3. **Validasi data** - Cek data sebelum import
4. **Backup database** - Sebelum import data besar

#### **B. Persiapan File ZIP:**

1. **Kompresi optimal** - Gunakan ZIP dengan kompresi
2. **Nama file sesuai** - Foto: nomor_anggota.jpg, Cover: kode_buku.jpg
3. **Ukuran foto optimal** - 100-200KB per foto
4. **Format yang didukung** - JPG, JPEG, PNG

#### **C. Saat Import:**

1. **Jangan refresh halaman** - Tunggu proses selesai
2. **Monitor progress** - Lihat feedback di halaman
3. **Cek hasil import** - Verifikasi data yang masuk
4. **Handle error** - Perbaiki data yang gagal

### 🔧 KONFIGURASI SERVER (UNTUK DATA SANGAT BESAR)

Jika butuh kapasitas lebih besar, ubah di `php.ini`:

```ini
upload_max_filesize = 200M
post_max_size = 200M
memory_limit = 512M
max_execution_time = 300
```

## 📞 KONTAK DARURAT

Jika semua cara di atas tidak berhasil, hubungi developer untuk bantuan.

## 🔐 KEAMANAN

- Jangan bagikan password admin ke sembarang orang
- Ganti password default setelah login pertama
- Backup database secara berkala
- Logout setelah selesai menggunakan sistem

## 📝 CATATAN PERUBAHAN

**Versi Terbaru:**

- ✅ **Tab "Profil Saya" dihapus** - Tidak ada duplikasi fungsi
- ✅ **Semua admin** dikelola di satu tempat (Manage Admin)
- ✅ **Interface lebih sederhana** - Tidak ada kebingungan
- ✅ **Badge "Saya"** untuk menandai admin yang sedang login
- ✅ **Kapasitas upload ditingkatkan** - ZIP: 100MB, Excel: 100MB
- ✅ **Optimasi data besar** - Chunking untuk import Excel
- ✅ **Memory efficient** - Proses data per batch

---

**Dokumentasi ini dibuat untuk memudahkan pengelolaan admin di sekolah.**
