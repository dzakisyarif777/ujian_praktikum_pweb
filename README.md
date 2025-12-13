# 📦 Inventory Saya 

Aplikasi **Inventory Management berbasis Web** menggunakan **PHP, MySQL, AJAX, dan JavaScript vanilla** dengan tampilan **light mode modern ala Gen-Z**.  
Dirancang untuk **CRUD produk**, pencarian cepat, pagination, dan tampilan detail produk dengan gambar via URL.

---

## ✨ Fitur Utama

- ✅ **CRUD Produk** (Create, Read, Update, Delete)
- 🔍 **Search realtime** (Nama / SKU / Deskripsi)
- 📄 **Pagination & limit data**
- 🖼️ **Gambar produk via URL**
- 📌 **Detail produk inline (klik baris tabel)**
- 🎨 **Light mode**
- 📱 **Responsive layout**
- ⚡ **AJAX (tanpa reload halaman)**

---

## 🖼️ Tampilan

- Navbar clean & minimal
- Card / table dengan efek glassmorphism
- Font modern (Inter)
- Warna pastel & soft gradient
- Tombol besar, rounded, dan user-friendly

---

## 🧱 Teknologi yang Digunakan

- **Frontend**
  - HTML5
  - CSS3 (Custom, tanpa framework berat)
  - JavaScript (Vanilla)
  - Bootstrap 5 (layout & modal)

- **Backend**
  - PHP Native
  - MySQL
  - AJAX (Fetch API)

---

## 📁 Struktur Folder

```

inventory-app/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js
├── api.php
├── config.php
├── db.php
├── index.php
├── sql/
│   └── inventory.sql
└── README.md

```

---

## 🗄️ Struktur Database (MySQL)

Tabel utama: **products**

| Field        | Tipe Data        | Keterangan |
|-------------|------------------|------------|
| id          | INT (PK, AI)     | ID produk |
| name        | VARCHAR(150)     | Nama produk |
| sku         | VARCHAR(50)      | Kode SKU |
| price       | DECIMAL(12,2)    | Harga |
| quantity    | INT              | Stok |
| description | TEXT             | Deskripsi |
| image       | TEXT             | URL gambar |
| created_at  | DATETIME         | Waktu dibuat |

---

## 🚀 Cara Menjalankan Aplikasi

### 1️⃣ Persiapan
- Install **XAMPP / Laragon**
- Pastikan **Apache & MySQL aktif**

### 2️⃣ Database
- Buka `phpMyAdmin`
- Import file:
```

/sql/inventory.sql

````

### 3️⃣ Konfigurasi
Sesuaikan `config.php` jika perlu:
```php
define('BASE_URL', 'http://localhost/inventory-app/');
````

### 4️⃣ Jalankan

Buka browser:

```
http://localhost/inventory-app/
```

---

## 🖼️ Cara Mengatur Gambar Produk

* Gambar disimpan sebagai **URL**
* Isi field **Gambar (URL)** saat tambah / edit produk
* Jika kosong → sistem otomatis pakai **gambar default**
* Mendukung gambar berbeda untuk tiap produk

Contoh URL:

```
https://images.unsplash.com/photo-1517336714731-489689fd1ca8
```

---

## 🧠 Catatan Penting

* Klik **satu kali** pada baris tabel → buka detail produk
* Klik **Edit** → data otomatis masuk ke form
* Tidak ada upload file lokal (URL only)
* JavaScript sudah dirapikan tanpa mengubah output

---


## 👨‍💻 Author

- **Daffa Ramadhan Ulwan Setiwan**
- **Muhammad Dzaki Syarif**
- **Nur Aulia Rahman**

Dibuat sebagai **Aplikasi Inventory sederhana namun modern**
dengan fokus **clean code, UX, dan estetika**.

---

✨ Happy Coding & Semoga membantu!

```


