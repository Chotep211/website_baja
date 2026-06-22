<?php
// admin/produk.php  –  CRUD produk
session_start();
require_once __DIR__ . '/../includes/config.php';
requireLogin();

$pdo = getDB();
$msg = '';

// HAPUS
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $row = $pdo->prepare("SELECT gambar FROM produk WHERE id=?");
    $row->execute([$id]);
    $old = $row->fetch();
    if ($old && $old['gambar']) {
        hapusDariSupabaseStorage($old['gambar']);
    }
    $pdo->prepare("DELETE FROM produk WHERE id=?")->execute([$id]);
    $msg = 'Produk berhasil dihapus.';
}

// TOGGLE AKTIF
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE produk SET aktif = NOT aktif WHERE id=?")->execute([$id]);
}

// SIMPAN (tambah / edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = (int)($_POST['id'] ?? 0);
    $nama     = trim($_POST['nama']    ?? '');
    $harga    = (float)str_replace(['.', ','], ['', '.'], $_POST['harga'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $urutan   = (int)($_POST['urutan'] ?? 0);

    // Upload gambar ke Supabase Storage
    $gambar = $_POST['gambar_lama'] ?? null;
    if (!empty($_FILES['gambar']['name'])) {
        $hasilUpload = uploadKeSupabaseStorage($_FILES['gambar']);
        if ($hasilUpload) {
            // Hapus gambar lama dari storage jika ada penggantian
            if (!empty($_POST['gambar_lama'])) {
                hapusDariSupabaseStorage($_POST['gambar_lama']);
            }
            $gambar = $hasilUpload;
        } else {
            $msg = 'Gagal upload gambar. Pastikan format jpg/png/webp dan koneksi internet stabil.';
        }
    }

    if ($id > 0) {
        $pdo->prepare("UPDATE produk SET nama=?, harga=?, deskripsi=?, gambar=?, urutan=? WHERE id=?")
            ->execute([$nama, $harga, $deskripsi, $gambar, $urutan, $id]);
        $msg = 'Produk berhasil diperbarui.';
    } else {
        $pdo->prepare("INSERT INTO produk (nama, harga, deskripsi, gambar, urutan) VALUES (?,?,?,?,?)")
            ->execute([$nama, $harga, $deskripsi, $gambar, $urutan]);
        $msg = 'Produk berhasil ditambahkan.';
    }
}

$produkList = $pdo->query("SELECT * FROM produk ORDER BY urutan ASC, id ASC")->fetchAll();

// Edit mode
$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Manajemen Produk – Admin</title>
    <link rel="stylesheet" href="../assets/css/vendors.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body{background:#f4f6f9}
        .sidebar{width:220px;min-height:100vh;background:#1a1a2e;position:fixed;top:0;left:0;padding:20px 0}
        .sidebar a{display:block;color:#ccc;padding:12px 24px;text-decoration:none;font-size:14px}
        .sidebar a:hover,.sidebar a.active{background:#c0974f;color:#fff}
        .sidebar .brand{color:#fff;font-weight:700;font-size:18px;padding:10px 24px 20px;border-bottom:1px solid #333;margin-bottom:10px}
        .main-content{margin-left:220px;padding:30px}
        .topbar{background:#fff;padding:14px 24px;margin-bottom:24px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.06);display:flex;justify-content:space-between;align-items:center}
        .btn-gold{background:#c0974f;color:#fff;border:none;padding:8px 18px;border-radius:6px;cursor:pointer}
        .btn-gold:hover{background:#a07a38}
        img.thumb{width:60px;height:60px;object-fit:cover;border-radius:6px}
    </style>
</head>
<body>
<div class="sidebar">
    <div class="brand">⚙️ Admin Panel</div>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="produk.php" class="active">📦 Produk</a>
    <a href="pesan.php">✉️ Pesan</a>
    <a href="pengaturan.php">⚙️ Pengaturan</a>
    <a href="ganti_password.php">🔑 Ganti Password</a>
    <a href="logout.php" style="color:#e74c3c">🚪 Logout</a>
</div>
<div class="main-content">
    <div class="topbar">
        <h4 class="mb-0 fw-700">📦 Manajemen Produk</h4>
        <a href="../index.php" target="_blank">← Lihat Website</a>
    </div>
    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <!-- FORM TAMBAH / EDIT -->
    <div class="bg-white rounded p-4 shadow-sm mb-4">
        <h5 class="fw-700 mb-3"><?= $edit ? 'Edit Produk' : 'Tambah Produk Baru' ?></h5>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>">
            <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($edit['gambar'] ?? '') ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Nama Produk *</label>
                    <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($edit['nama'] ?? '') ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-600">Harga (Rp) *</label>
                    <input type="number" class="form-control" name="harga" value="<?= $edit['harga'] ?? '' ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-600">Urutan Tampil</label>
                    <input type="number" class="form-control" name="urutan" value="<?= $edit['urutan'] ?? 0 ?>">
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-600">Deskripsi</label>
                    <textarea class="form-control" name="deskripsi" rows="2"><?= htmlspecialchars($edit['deskripsi'] ?? '') ?></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-600">Gambar Produk</label>
                    <?php if (!empty($edit['gambar'])): ?>
                    <div class="mb-1"><img src="<?= htmlspecialchars(urlGambarProduk($edit['gambar'])) ?>" class="thumb"></div>
                    <?php endif; ?>
                    <input type="file" class="form-control" name="gambar" accept="image/*">
                </div>
            </div>
            <button type="submit" class="btn-gold">💾 <?= $edit ? 'Update Produk' : 'Simpan Produk' ?></button>
            <?php if ($edit): ?><a href="produk.php" class="btn btn-secondary ms-2">Batal</a><?php endif; ?>
        </form>
    </div>

    <!-- TABEL PRODUK -->
    <div class="bg-white rounded p-4 shadow-sm">
        <h5 class="fw-700 mb-3">Daftar Produk</h5>
        <table class="table table-hover">
            <thead><tr><th>#</th><th>Gambar</th><th>Nama</th><th>Harga</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($produkList as $p): ?>
            <tr>
                <td><?= $p['urutan'] ?></td>
                <td>
                    <?php if ($p['gambar']): ?>
                    <img src="<?= htmlspecialchars(urlGambarProduk($p['gambar'])) ?>" class="thumb">
                    <?php else: ?>–<?php endif; ?>
                </td>
                <td><?= htmlspecialchars($p['nama']) ?></td>
                <td><?= formatRupiah((float)$p['harga']) ?></td>
                <td>
                    <a href="?toggle=<?= $p['id'] ?>">
                        <?= $p['aktif'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?>
                    </a>
                </td>
                <td>
                    <a href="?edit=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="?hapus=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus produk ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
