# 📊 Sistem Catatan Keuangan - CV. Zie Net

Aplikasi pencatatan keuangan berbasis web web app interaktif yang dirancang khusus untuk manajemen keuangan harian, khususnya model bisnis WiFi RT/RW Net. Dibangun dengan **PHP native**, **MySQL**, serta gaya antarmuka modern yang simpel dan elegan.

## ✨ Fitur Utama

- **Dashboard Interaktif**: Ringkasan saldo, statistik pemasukan, dan pengeluaran secara real-time.
- **Manajemen Transaksi**: Catat arus kas pemasukan dan pengeluaran dengan sangat detail disertai fitur bukti (attachment), keterangan, kategori, dan nominal.
- **Kategori / Jenis Transaksi**: Bebas menambah, mengubah, dan menghapus jenis transaksi yang spesifik untuk bisnis Anda (contoh: Iuran WiFi, Pemasangan, Belanja Alat, Gaji Teknisi).
- **Rekap Bulanan & Filter Cerdas**: Telusuri data transaksi berdasarkan rentang tanggal atau kategori tertentu, lalu lihat total rekapitulasi per bulan.
- **Ekspor Data (Export Laporan)**: Mengekspor laporan keuangan transaksi ke format ringkas untuk arsip dan pembukuan eksternal.
- **Multi-Level User Role**: Pembagian hak akses terstruktur yang mendukung super role `admin` dan staf `user` (operator).

## 🚀 Panduan Instalasi (Cara Memakai)

Ikuti langkah-langkah di bawah ini untuk menjalankan project ini di komputer (local server) Anda:

### 1. Persiapan Sistem (Requirements)

- Pastikan Anda sudah menginstal aplikasi web server lokal seperti **XAMPP**, **WAMP**, atau **MAMP**.
- Nyalakan servis **Apache** dan **MySQL** dari control panel web server Anda.

### 2. Memasukkan Project ke Local Server

- Salin folder project ini (`catatan_keuangan`) dan letakkan di dalam direktori document root server lokal Anda:
  - **XAMPP:** Letakkan di folder `C:\xampp\htdocs\`
  - **WAMP:** Letakkan di folder `C:\wamp\www\`
  - **MAMP:** Letakkan di folder `Applications/MAMP/htdocs/`

### 3. Konfigurasi Database

- Buka web browser kesayangan Anda lalu akses **phpMyAdmin** dengan mengetikkan URL: `http://localhost/phpmyadmin`
- Buat sebuah database baru dan beri nama: `catatan_keuangan`
- Setelah database dibuat, pilih tab **Import** (Impor) yang ada di menu atas.
- Pilih file `catatan_keuangan.sql` yang ada di dalam folder project ini.
- Gulir ke bawah dan klik tombol **Go** (Kirim) untuk mengeksekusi struktur tabel beserta data bawaannya.

### 4. Sesuaikan Pengaturan Koneksi (Opsional)

Aplikasi ini sudah diatur untuk terhubung secara default ke database XAMPP. Jika Anda memakai konfigurasi MySQL khusus (terdapat password atau nama database berbeda), buka file `koneksi.php` pakai code editor dan ubah sesuai milik Anda:

```php
$koneksi = mysqli_connect("localhost", "root", "password_database_anda", "catatan_keuangan");
```

### 5. Jalankan Aplikasi

- Buka tab baru pada web browser, kemudian ketik URL berikut untuk mulai mengakses sistem catatan keuangan:
  => **`http://localhost/catatan_keuangan/`**

### 6. Akses Login Default

Anda dapat masuk ke dalam dashboard utama menggunakan kredensial default bawaan database:

- **Username:** `admin`
- **Password:** `admin` _(Atau tergantung seeder sebelumnya. Anda bisa meresetnya via `reset_password.php` jika diperlukan)_

---

## 🗃️ Fitur Data Dummy / Seeder (Opsional)

Project ini dilengkapi dengan script data generator untuk mencoba bagaimana aplikasi berjalan bila menampung ratusan data simulasi riwayat (dari tahun 2021 hingga 2026). Jika Anda ingin mengisi database dengan sampel data bisnis:

1. Pastikan Anda sudah berhasil menjalankan langkah instalasi ke-1 sampai ke-4.
2. Akses `http://localhost/catatan_keuangan/seed_data.php` secara bergantian dari browser.
3. Tunggu hingga tulisan status sukses muncul seluruhnya.
4. Anda akan men-generate data keuangan kompleks dan akun operator baru (Username: `operator` / Password: `operator123`).

## 🛠️ Teknologi yang Digunakan

- **Backend:** PHP Native (Versi 7/8+)
- **Database:** MySQL / MariaDB
- **Frontend / Styling:** Vanilla CSS (`assets/css/style.css`), HTML5
- **Icons:** FontAwesome v6.5.1
- **Notifikasi Popup:** SweetAlert2

## 👨‍💻 Info Pengembang & Lisensi

Sistem ini diprogram oleh **Zizan / CV. Zie Net**. Bebas dimodifikasi atau dikembangkan lebih lanjut agar lebih pas dipakai di dalam usaha pribadi maupun perusahaan Anda.
