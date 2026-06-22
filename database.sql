-- ============================================================
-- DATABASE: website_baja
-- PT Cipta Pesona Teknik / Ventico.co
-- ============================================================

CREATE DATABASE IF NOT EXISTS website_baja CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE website_baja;

-- ------------------------------------------------------------
-- TABLE: produk
-- Menyimpan data produk yang tampil di section "Product"
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS produk (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(255)   NOT NULL,
    harga       DECIMAL(15,2)  NOT NULL DEFAULT 0,
    deskripsi   TEXT,
    gambar      VARCHAR(255),             -- nama file di uploads/produk/
    aktif       TINYINT(1)     NOT NULL DEFAULT 1,
    urutan      INT            NOT NULL DEFAULT 0,
    created_at  DATETIME       DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLE: pengaturan
-- Konfigurasi konten website (nama perusahaan, WA, dll.)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pengaturan (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    kunci       VARCHAR(100)   NOT NULL UNIQUE,
    nilai       TEXT,
    keterangan  VARCHAR(255)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLE: pesan_kontak
-- Pesan yang masuk dari form kontak / pertanyaan pelanggan
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pesan_kontak (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(150)   NOT NULL,
    no_hp       VARCHAR(20),
    email       VARCHAR(150),
    pesan       TEXT           NOT NULL,
    produk_id   INT            DEFAULT NULL,   -- produk yang ditanyakan (opsional)
    sudah_dibaca TINYINT(1)    NOT NULL DEFAULT 0,
    created_at  DATETIME       DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLE: admin
-- Akun login untuk panel admin
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(100)   NOT NULL UNIQUE,
    password    VARCHAR(255)   NOT NULL,        -- bcrypt hash
    nama_lengkap VARCHAR(150),
    created_at  DATETIME       DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- DATA AWAL
-- ============================================================

-- Pengaturan default
INSERT INTO pengaturan (kunci, nilai, keterangan) VALUES
('nama_perusahaan',  'PT Cipta Pesona Teknik',       'Nama perusahaan di header & footer'),
('tagline',          'Ellegant at all times, Fashionable every second', 'Tagline banner'),
('no_whatsapp',      '6285774918809',                'Nomor WA tanpa tanda +'),
('instagram',        'https://www.instagram.com/ventico.co', 'Link Instagram'),
('copyright',        '© Copyright 2024 PT Cipta Pesona Teknik', 'Teks copyright footer'),
('meta_description', 'Supplier baja & konstruksi terpercaya',  'Meta description SEO')
ON DUPLICATE KEY UPDATE nilai = VALUES(nilai);

-- Produk contoh (sesuaikan gambar dengan file di uploads/produk/)
INSERT INTO produk (nama, harga, deskripsi, gambar, urutan) VALUES
('Produk Baja 1', 119000, 'Produk baja berkualitas tinggi.', 'produk1.png', 1),
('Produk Baja 2', 119000, 'Produk baja tahan lama dan presisi.', 'produk2.png', 2);

-- Admin default  (password: admin123  –  ganti setelah login pertama!)
INSERT INTO admin (username, password, nama_lengkap) VALUES
('admin', '$2y$10$5mVcMdRluNkrRHHJxM3KMezgJHBMf1HKvMVAJQ3mIB5j9Q4A6lQ3i', 'Administrator')
ON DUPLICATE KEY UPDATE username = username;
