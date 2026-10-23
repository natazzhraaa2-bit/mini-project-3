#  Case HP Store - Product Manager Web App

Aplikasi web manajemen produk berbasis **PHP**, **MySQL (PDO)**, dan **CSS Flexbox** yang dikembangkan untuk kebutuhan tugas praktikum pemrograman web.

---

## Fitur Utama
* **CRUD Produk**: Tambah, lihat, ubah, dan hapus data katalog case HP.
* **Upload Gambar**: Unggah foto produk dengan validasi ekstensi aman (`jpg`, `jpeg`, `png`, `webp`).
* **Pencarian Real-time**: Fitur *search* berdasarkan nama produk atau tipe/kategori HP.
* **Keamanan Terjamin**: Menggunakan Prepared Statements (PDO) untuk mencegah SQL Injection, sanitasi `htmlspecialchars()` untuk XSS, serta validasi token CSRF.
* **Desain Responsif**: Antarmuka bersih menggunakan CSS Flexbox.

---

## Teknologi yang Digunakan
* **PHP** (Versi 8.x)
* **MySQL / MariaDB** (via PDO)
* **HTML5 & CSS3** (Flexbox)
* **XAMPP** (Local Server Environment)

---

##  Struktur Folder Proyek
```text
product-manager/
├── config/
│   └── db.php
├── database/
│   └── store_db.sql
└── public/
    ├── assets/
    │   └── style.css
    ├── uploads/
    ├── index.php
    ├── create.php
    ├── edit.php
    └── delete.php