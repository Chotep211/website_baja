-- ============================================================
-- DATABASE: website_baja
-- PT Cipta Pesona Teknik / Ventico.co
-- Versi: Supabase (PostgreSQL)
-- Cara import: Supabase Dashboard → SQL Editor → paste & Run
-- ============================================================

-- ------------------------------------------------------------
-- TABLE: produk
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS produk (
    id          SERIAL PRIMARY KEY,
    nama        VARCHAR(255)   NOT NULL,
    harga       NUMERIC(15,2)  NOT NULL DEFAULT 0,
    deskripsi   TEXT,
    gambar      VARCHAR(255),
    aktif       BOOLEAN        NOT NULL DEFAULT TRUE,
    urutan      INT            NOT NULL DEFAULT 0,
    created_at  TIMESTAMPTZ    DEFAULT NOW(),
    updated_at  TIMESTAMPTZ    DEFAULT NOW()
);

-- Auto-update updated_at via trigger
CREATE OR REPLACE FUNCTION update_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE TRIGGER trg_produk_updated_at
BEFORE UPDATE ON produk
FOR EACH ROW EXECUTE FUNCTION update_updated_at();

-- ------------------------------------------------------------
-- TABLE: pengaturan
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pengaturan (
    id          SERIAL PRIMARY KEY,
    kunci       VARCHAR(100)   NOT NULL UNIQUE,
    nilai       TEXT,
    keterangan  VARCHAR(255)
);

-- ------------------------------------------------------------
-- TABLE: pesan_kontak
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pesan_kontak (
    id           SERIAL PRIMARY KEY,
    nama         VARCHAR(150)  NOT NULL,
    no_hp        VARCHAR(20),
    email        VARCHAR(150),
    pesan        TEXT          NOT NULL,
    produk_id    INT           DEFAULT NULL REFERENCES produk(id) ON DELETE SET NULL,
    sudah_dibaca BOOLEAN       NOT NULL DEFAULT FALSE,
    created_at   TIMESTAMPTZ   DEFAULT NOW()
);

-- ------------------------------------------------------------
-- TABLE: admin
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin (
    id           SERIAL PRIMARY KEY,
    username     VARCHAR(100)  NOT NULL UNIQUE,
    password     VARCHAR(255)  NOT NULL,
    nama_lengkap VARCHAR(150),
    created_at   TIMESTAMPTZ   DEFAULT NOW()
);

-- ============================================================
-- ROW LEVEL SECURITY (RLS) – Supabase best practice
-- Aktifkan RLS agar tabel tidak bisa diakses publik sembarangan
-- ============================================================

ALTER TABLE produk        ENABLE ROW LEVEL SECURITY;
ALTER TABLE pengaturan    ENABLE ROW LEVEL SECURITY;
ALTER TABLE pesan_kontak  ENABLE ROW LEVEL SECURITY;
ALTER TABLE admin         ENABLE ROW LEVEL SECURITY;

-- Produk: boleh dibaca siapa saja (untuk ditampilkan di website)
CREATE POLICY "produk_baca_publik"
ON produk FOR SELECT
USING (aktif = TRUE);

-- Pengaturan: boleh dibaca siapa saja
CREATE POLICY "pengaturan_baca_publik"
ON pengaturan FOR SELECT
USING (TRUE);

-- Pesan kontak: siapa saja boleh INSERT (kirim pesan)
CREATE POLICY "pesan_insert_publik"
ON pesan_kontak FOR INSERT
WITH CHECK (TRUE);

-- Catatan: akses SELECT/UPDATE/DELETE pada pesan_kontak dan admin
-- hanya dilakukan melalui PHP backend menggunakan Service Role Key
-- (bukan anon key), sehingga tidak perlu policy publik tambahan.

-- ============================================================
-- DATA AWAL
-- ============================================================

-- Pengaturan default
INSERT INTO pengaturan (kunci, nilai, keterangan) VALUES
('nama_perusahaan',  'PT Cipta Pesona Teknik',                  'Nama perusahaan di header & footer'),
('tagline',          'Ellegant at all times, Fashionable every second', 'Tagline banner'),
('no_whatsapp',      '6285774918809',                           'Nomor WA tanpa tanda +'),
('instagram',        'https://www.instagram.com/ventico.co',    'Link Instagram'),
('copyright',        '© Copyright 2024 PT Cipta Pesona Teknik', 'Teks copyright footer'),
('meta_description', 'Supplier baja & konstruksi terpercaya',   'Meta description SEO')
ON CONFLICT (kunci) DO UPDATE SET nilai = EXCLUDED.nilai;

-- Produk contoh
INSERT INTO produk (nama, harga, deskripsi, gambar, urutan) VALUES
('Produk Baja 1', 119000, 'Produk baja berkualitas tinggi.',    'produk1.png', 1),
('Produk Baja 2', 119000, 'Produk baja tahan lama dan presisi.','produk2.png', 2);

-- Admin default (password: admin123 – ganti setelah login pertama!)
-- Hash bcrypt: $2y$10$5mVcMdRluNkrRHHJxM3KMezgJHBMf1HKvMVAJQ3mIB5j9Q4A6lQ3i
INSERT INTO admin (username, password, nama_lengkap) VALUES
('admin', '$2y$10$5mVcMdRluNkrRHHJxM3KMezgJHBMf1HKvMVAJQ3mIB5j9Q4A6lQ3i', 'Administrator')
ON CONFLICT (username) DO NOTHING;