<?php
// includes/config.php
// Konfigurasi koneksi Supabase (PostgreSQL) — via Connection Pooler
// Ambil dari: Supabase Dashboard → Connect → Session pooler / Transaction pooler

// ─── ISI SESUAI DATA POOLER SUPABASE ANDA ────────────────────
define('DB_HOST',getenv('DB_HOST') ?:'aws-1-ap-southeast-1.pooler.supabase.com'); // Host dari tab "Connect" -> pooler
define('DB_PORT',getenv('DB_PORT') ?:'5432');                            // 5432 = session pooler, 6543 = transaction pooler
define('DB_USER',getenv('DB_USER') ?:'postgres.bsyltelsvxspfqajpqwn');            // Username pooler (ada project ref di belakang)
define('DB_PASS',getenv('DB_PASS') ?:'PESONATEKNIK21');                    // Password database Anda
define('DB_NAME',getenv('DB_NAME') ?:'postgres');
define('DB_CHARSET',  'utf8');

// Base URL website Anda (tanpa slash di akhir)
define('BASE_URL',getenv('BASE_URL') ?:'http://localhost/website_baja');
// ─────────────────────────────────────────────────────────────

// Path folder upload lokal (fallback, tidak dipakai lagi untuk produk)
define('UPLOAD_PRODUK', __DIR__ . '/../uploads/produk/');
define('UPLOAD_BANNER', __DIR__ . '/../uploads/banner/');

// ─── KONFIGURASI SUPABASE STORAGE (untuk upload gambar produk) ─
define('SUPABASE_URL', getenv('SUPABASE_URL')?:'https://bsyltelsvxspfqajpqwn.supabase.co');
define('SUPABASE_KEY', getenv('SUPABASE_KEY')?:'sb_publishable_NJVf4TCwcQVxAseTkE_dUQ_NqE2AZJh'); // publishable/anon key
define('SUPABASE_BUCKET', getenv('SUPABASE_BUCKET') ?:'produk-images');
// ─────────────────────────────────────────────────────────────

// Upload file ke Supabase Storage, return URL publik atau null jika gagal
// $prefix: subfolder di dalam bucket, contoh 'produk' atau 'banner'
function uploadKeSupabaseStorage(array $file, string $prefix = 'produk'): ?string {
    if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($ext, $allowed)) {
        return null;
    }

    $fileName = $prefix . '/' . $prefix . '_' . time() . '_' . uniqid() . '.' . $ext;
    $fileData = file_get_contents($file['tmp_name']);
    $mimeType = mime_content_type($file['tmp_name']);

    $url = SUPABASE_URL . '/storage/v1/object/' . SUPABASE_BUCKET . '/' . $fileName;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . SUPABASE_KEY,
        'apikey: ' . SUPABASE_KEY,
        'Content-Type: ' . $mimeType,
        'x-upsert: true',
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        return SUPABASE_URL . '/storage/v1/object/public/' . SUPABASE_BUCKET . '/' . $fileName;
    }
    return null;
}

// Hapus file dari Supabase Storage berdasarkan URL publik
function hapusDariSupabaseStorage(string $publicUrl): bool {
    $prefix = SUPABASE_URL . '/storage/v1/object/public/' . SUPABASE_BUCKET . '/';
    if (strpos($publicUrl, $prefix) !== 0) {
        return false; // bukan file dari bucket ini (mungkin gambar lokal lama)
    }
    $fileName = substr($publicUrl, strlen($prefix));
    $url = SUPABASE_URL . '/storage/v1/object/' . SUPABASE_BUCKET . '/' . $fileName;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . SUPABASE_KEY,
        'apikey: ' . SUPABASE_KEY,
    ]);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $httpCode >= 200 && $httpCode < 300;
}

// Dapatkan URL gambar produk untuk ditampilkan di <img>.
// Mendukung: URL penuh Supabase (data baru) ATAU nama file lokal lama (data lama/fallback).
function urlGambarProduk(?string $gambar): string {
    if (empty($gambar)) {
        return 'assets/images/produk1.png'; // gambar default jika kosong
    }
    if (str_starts_with($gambar, 'http://') || str_starts_with($gambar, 'https://')) {
        return $gambar; // sudah URL Supabase Storage
    }
    return 'uploads/produk/' . $gambar; // nama file lama, fallback ke folder lokal
}

// Dapatkan URL gambar banner untuk ditampilkan sebagai background.
// Mendukung: URL penuh Supabase (data baru) ATAU fallback ke gambar default lama.
function urlBanner(?string $banner): string {
    if (empty($banner)) {
        return 'assets/images/baja.png'; // gambar default banner asli
    }
    if (str_starts_with($banner, 'http://') || str_starts_with($banner, 'https://')) {
        return $banner; // sudah URL Supabase Storage
    }
    return 'uploads/banner/' . $banner; // nama file lama, fallback ke folder lokal
}

// Koneksi PDO ke Supabase (PostgreSQL)
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            DB_HOST, DB_PORT, DB_NAME
        );
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Koneksi Supabase gagal: " . $e->getMessage());
        }
    }
    return $pdo;
}

// Ambil semua pengaturan sebagai array key => nilai
function getPengaturan(): array {
    $pdo  = getDB();
    $rows = $pdo->query("SELECT kunci, nilai FROM pengaturan")->fetchAll();
    $cfg  = [];
    foreach ($rows as $r) {
        $cfg[$r['kunci']] = $r['nilai'];
    }
    return $cfg;
}

// Helper format rupiah
function formatRupiah(float $angka): string {
    return 'Rp. ' . number_format($angka, 0, ',', '.') . ',00';
}

// Session helper
function isAdminLoggedIn(): bool {
    return isset($_SESSION['admin_id']);
}

function requireLogin(): void {
    if (!isAdminLoggedIn()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}
