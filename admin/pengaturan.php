<?php
// admin/pengaturan.php
session_start();
require_once __DIR__ . '/../includes/config.php';
requireLogin();

$pdo = getDB();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['nama_perusahaan','tagline','no_whatsapp','instagram','copyright','meta_description'];
    foreach ($fields as $key) {
        $val = trim($_POST[$key] ?? '');
        $pdo->prepare("UPDATE pengaturan SET nilai=? WHERE kunci=?")->execute([$val, $key]);
    }

    // Upload banner baru jika ada file dipilih
    if (!empty($_FILES['banner']['name'])) {
        $hasilUpload = uploadKeSupabaseStorage($_FILES['banner'], 'banner');
        if ($hasilUpload) {
            // Hapus banner lama dari storage jika sebelumnya pakai Supabase juga
            $bannerLama = $cfg['banner_url'] ?? null;
            if (!empty($bannerLama)) {
                hapusDariSupabaseStorage($bannerLama);
            }
            // Simpan key baru (insert jika belum ada, update jika sudah ada)
            $pdo->prepare("
                INSERT INTO pengaturan (kunci, nilai, keterangan)
                VALUES ('banner_url', ?, 'URL gambar banner utama')
                ON CONFLICT (kunci) DO UPDATE SET nilai = EXCLUDED.nilai
            ")->execute([$hasilUpload]);
        } else {
            $msg = 'Pengaturan teks tersimpan, tapi upload banner gagal. Cek format file (jpg/png/webp).';
        }
    }

    $msg = $msg ?: 'Pengaturan berhasil disimpan.';
}

$cfg = getPengaturan();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Pengaturan – Admin</title>
    <link rel="stylesheet" href="../assets/css/vendors.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body{background:#f4f6f9}
        .sidebar{width:220px;min-height:100vh;background:#1a1a2e;position:fixed;top:0;left:0;padding:20px 0}
        .sidebar a{display:block;color:#ccc;padding:12px 24px;text-decoration:none;font-size:14px}
        .sidebar a:hover,.sidebar a.active{background:#c0974f;color:#fff}
        .sidebar .brand{color:#fff;font-weight:700;font-size:18px;padding:10px 24px 20px;border-bottom:1px solid #333;margin-bottom:10px}
        .main-content{margin-left:220px;padding:30px}
        .topbar{background:#fff;padding:14px 24px;margin-bottom:24px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.06)}
        .btn-gold{background:#c0974f;color:#fff;border:none;padding:10px 24px;border-radius:6px;cursor:pointer;font-weight:700}
        .banner-preview{width:100%;max-height:220px;object-fit:cover;border-radius:8px;margin-bottom:10px}
    </style>
</head>
<body>
<div class="sidebar">
    <div class="brand">⚙️ Admin Panel</div>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="produk.php">📦 Produk</a>
    <a href="pesan.php">✉️ Pesan</a>
    <a href="pengaturan.php" class="active">⚙️ Pengaturan</a>
    <a href="ganti_password.php">🔑 Ganti Password</a>
    <a href="logout.php" style="color:#e74c3c">🚪 Logout</a>
</div>
<div class="main-content">
    <div class="topbar">
        <h4 class="mb-0 fw-700">⚙️ Pengaturan Website</h4>
    </div>
    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <div class="bg-white rounded p-4 shadow-sm">
        <form method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="form-label fw-600">Gambar Banner Utama</label>
                <div>
                    <img src="<?= htmlspecialchars(urlBanner($cfg['banner_url'] ?? null)) ?>" class="banner-preview">
                </div>
                <input type="file" class="form-control" name="banner" accept="image/*">
                <small class="text-muted">Upload gambar baru untuk mengganti banner di halaman utama. Kosongkan jika tidak ingin mengubah.</small>
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Nama Perusahaan</label>
                <input type="text" class="form-control" name="nama_perusahaan" value="<?= htmlspecialchars($cfg['nama_perusahaan'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Tagline Banner</label>
                <input type="text" class="form-control" name="tagline" value="<?= htmlspecialchars($cfg['tagline'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Nomor WhatsApp <small class="text-muted">(format: 628xxx tanpa tanda +)</small></label>
                <input type="text" class="form-control" name="no_whatsapp" value="<?= htmlspecialchars($cfg['no_whatsapp'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Link Instagram</label>
                <input type="url" class="form-control" name="instagram" value="<?= htmlspecialchars($cfg['instagram'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Teks Copyright Footer</label>
                <input type="text" class="form-control" name="copyright" value="<?= htmlspecialchars($cfg['copyright'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="form-label fw-600">Meta Description (SEO)</label>
                <textarea class="form-control" name="meta_description" rows="2"><?= htmlspecialchars($cfg['meta_description'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn-gold">💾 Simpan Pengaturan</button>
        </form>
    </div>
</div>
</body>
</html>
