# Website Baja – PT Cipta Pesona Teknik
## Panduan Setup PHP + MySQL

---

## 📁 Struktur Folder

```
website_baja/
├── index.php               ← Halaman utama (gantikan index.html)
├── database.sql            ← Script database (import ke phpMyAdmin/MySQL)
├── includes/
│   └── config.php          ← Konfigurasi DB & fungsi helper
├── admin/
│   ├── login.php           ← Halaman login admin
│   ├── dashboard.php       ← Dashboard statistik
│   ├── produk.php          ← CRUD Produk
│   ├── pesan.php           ← Lihat pesan dari form kontak
│   ├── pengaturan.php      ← Edit teks/konten website
│   └── logout.php
├── uploads/
│   ├── produk/             ← Upload gambar produk (chmod 775)
│   └── banner/             ← Upload gambar banner (chmod 775)
└── assets/                 ← CSS, JS, font (tidak diubah)
```

---

## 🗄️ DATABASE YANG DIPERLUKAN

Buat 1 database bernama: **`website_baja`**

### Tabel-tabel:
| Tabel | Fungsi |
|-------|--------|
| `produk` | Data produk yang tampil di website |
| `pengaturan` | Konfigurasi teks website (nama perusahaan, WA, dll) |
| `pesan_kontak` | Pesan dari pengunjung via form kontak |
| `admin` | Akun login admin panel |

---

## ⚙️ LANGKAH SETUP

### 1. Import Database
```sql
-- Buka phpMyAdmin atau MySQL CLI, lalu:
mysql -u root -p < database.sql
-- ATAU buka phpMyAdmin → Import → pilih database.sql
```

### 2. Edit Konfigurasi
Buka file `includes/config.php` dan sesuaikan:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');         // username MySQL Anda
define('DB_PASS', '');             // password MySQL Anda
define('BASE_URL', 'http://localhost/website_baja');
```

### 3. Set Permission Folder Upload
```bash
chmod 775 uploads/produk
chmod 775 uploads/banner
```

### 4. Copy Gambar Produk Lama
Salin file `produk1.png` dan `produk2.png` dari `assets/images/` ke `uploads/produk/`

### 5. Akses Website
- **Website**: `http://localhost/website_baja/`
- **Admin panel**: `http://localhost/website_baja/admin/login.php`

---

## 🔐 LOGIN ADMIN DEFAULT
| Field | Value |
|-------|-------|
| Username | `admin` |
| Password | `admin123` |

> ⚠️ **Segera ganti password setelah login pertama!**

---

## ✨ FITUR YANG DITAMBAHKAN

### Halaman Utama (index.php)
- ✅ Konten dinamis dari database (nama, tagline, WhatsApp, dsb)
- ✅ Produk tampil dari database (tidak hardcode)
- ✅ Link klik produk → langsung ke WhatsApp dengan pesan otomatis
- ✅ **Form Kontak baru** – pengunjung bisa kirim pesan/pertanyaan

### Admin Panel
- ✅ Login aman dengan password bcrypt
- ✅ Dashboard statistik (total produk, pesan masuk, pesan baru)
- ✅ Manajemen Produk: tambah, edit, hapus, aktif/nonaktif, upload foto
- ✅ Manajemen Pesan: lihat semua pesan, tandai sudah dibaca, hapus
- ✅ Pengaturan Website: edit nama perusahaan, tagline, WA, Instagram, copyright

---

## 📋 REQUIREMENTS SERVER
- PHP 7.4 atau lebih baru
- MySQL 5.7 / MariaDB 10.3 atau lebih baru
- Ekstensi PHP: `pdo`, `pdo_mysql`, `fileinfo`
- Web server: Apache (dengan mod_rewrite) atau Nginx
