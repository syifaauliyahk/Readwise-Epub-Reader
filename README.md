# 📚 ReadWise - Web Based ePub Reader

**ReadWise** adalah aplikasi pembaca buku digital (e-reader) berbasis web yang mendukung format `.epub`. Aplikasi ini memungkinkan pengguna untuk mengunggah dan membaca buku digital secara online.
Proyek ini dibuat untuk memenuhi **Ujian Akhir Semester (UAS)** mata kuliah **Pemrograman Berbasis Platform**.

---
## 👨‍💻 Identitas Kelompok
**Anggota Tim:**
1.  Fadiya Tsabita
2.  Salsa Sabrina
3.  Syifa Auliyah Kusumawardani

* **Fakultas:** Sains dan Teknologi
* **Mata Kuliah:** Pemrograman Berbasis Platform
* **Dosen Pengampu:** Hendra Bayu Suseno, M.Kom.
---
## 🚀 Fitur Utama
Aplikasi ini memiliki fitur lengkap sebagai berikut:
### 1. Manajemen Pengguna & Buku
* ✅ **Login & Register:** Sistem autentikasi dengan hashing password.
* ✅ **Upload ePub:** Pengguna dapat mengunggah file `.epub`. Sistem akan otomatis mengekstrak konten buku.
* ✅ **Kategori Prodi:** Buku dikelompokkan berdasarkan Program Studi di FST (Teknik Informatika, Sistem Informasi, Agribisnis, Matematika, Fisika, Biologi, Kimia, Teknik Pertambangan).
### 2. Fitur Pembaca (Reader)
* ✅ **EPub Parser:** Menampilkan teks dan gambar dari file ePub langsung di browser tanpa aplikasi tambahan.
* ✅ **Table of Contents (TOC):** Navigasi antar bab yang mudah melalui sidebar.
* ✅ **Search in Book:** Fitur pencarian kata kunci di dalam buku.
* ✅ **Language Switcher:** Dukungan dua bahasa antarmuka (**Indonesia** / **Inggris**).
### 3. Fitur Personal (Bonus)
* ✅ **Bookmark & Notes:** Menandai halaman/bab penting dan menambahkan catatan pribadi. 
---
## 🛠️ Teknologi yang Digunakan
* **Backend:** PHP 
* **Frontend:** HTML5, CSS3, JavaScript
* **Database:** MySQL 
* **Server:** Apache (via XAMPP)
* **Library:** PHP `ZipArchive` 
---
## 📦 Cara Instalasi (How to Run)
Ikuti langkah berikut untuk menjalankan proyek ini di komputer lokal:
### 1. Clone Repository
Download atau clone repository ini ke dalam folder `htdocs`.
```bash
git clone https://github.com/syifaauliyahk/Readwise-Epub-Reader.git
```
### 2. Siapkan Database
* Buka **phpMyAdmin** (biasanya di `http://localhost/phpmyadmin`).
* Buat database baru dengan nama: **`epubread`**.
* Import file **`epubread.sql`** yang ada di dalam folder root proyek ini.
### 3. Konfigurasi Koneksi (Jika Perlu)
* Buka file `core/db.php`.
* Pastikan pengaturan `host`, `username`, `password`, dan `dbname` sesuai dengan server lokal Anda.
```php
$host = "localhost"; 
$user = "root";
$pass = ""; 
$dbname = "epubread";
```
### 4. Jalankan Aplikasi
* Buka browser dan akses: `http://localhost/Readwise-Epub-Reader`.
* Silakan **Register** akun baru untuk mulai menggunakan.
---
© 2025 Kelompok. All Rights Reserved.
