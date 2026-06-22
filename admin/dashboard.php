<?php
// admin/dashboard.php
session_start();
require_once __DIR__ . '/../includes/config.php';
requireLogin();

$pdo = getDB();

$totalProduk  = $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
$totalPesan   = $pdo->query("SELECT COUNT(*) FROM pesan_kontak")->fetchColumn();
$pesanBaru    = $pdo->query("SELECT COUNT(*) FROM pesan_kontak WHERE sudah_dibaca = FALSE")->fetchColumn();
$pesanTerbaru = $pdo->query("SELECT pk.*, p.nama AS nama_produk FROM pesan_kontak pk LEFT JOIN produk p ON pk.produk_id = p.id ORDER BY pk.created_at DESC LIMIT 5")->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/vendors.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background:#f4f6f9; }
        .sidebar { width:220px; min-height:100vh; background:#1a1a2e; position:fixed; top:0; left:0; padding:20px 0; }
        .sidebar a { display:block; color:#ccc; padding:12px 24px; text-decoration:none; font-size:14px; }
        .sidebar a:hover, .sidebar a.active { background:#c0974f; color:#fff; }
        .sidebar .brand { color:#fff; font-weight:700; font-size:18px; padding:10px 24px 20px; border-bottom:1px solid #333; margin-bottom:10px; }
        .main-content { margin-left:220px; padding:30px; }
        .stat-card { background:#fff; border-radius:10px; padding:24px; text-align:center; box-shadow:0 2px 10px rgba(0,0,0,.07); }
        .stat-card .number { font-size:36px; font-weight:700; color:#c0974f; }
        .topbar { background:#fff; padding:14px 24px; margin-bottom:24px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.06); display:flex; justify-content:space-between; align-items:center; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand">⚙️ Admin Panel</div>
        <a href="dashboard.php" class="active">📊 Dashboard</a>
        <a href="produk.php">📦 Produk</a>
        <a href="pesan.php">✉️ Pesan <?php if($pesanBaru > 0): ?><span style="background:#e74c3c;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;"><?= $pesanBaru ?></span><?php endif; ?></a>
        <a href="pengaturan.php">⚙️ Pengaturan</a>
        <a href="ganti_password.php">🔑 Ganti Password</a>
        <a href="logout.php" style="margin-top:auto;color:#e74c3c;">🚪 Logout</a>
    </div>
    <div class="main-content">
        <div class="topbar">
            <h4 class="mb-0 fw-700">Dashboard</h4>
            <span>Halo, <strong><?= htmlspecialchars($_SESSION['admin_nama']) ?></strong></span>
        </div>
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <div class="number"><?= $totalProduk ?></div>
                    <div class="text-muted">Total Produk</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <div class="number"><?= $totalPesan ?></div>
                    <div class="text-muted">Total Pesan Masuk</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <div class="number" style="color:#e74c3c"><?= $pesanBaru ?></div>
                    <div class="text-muted">Pesan Belum Dibaca</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded p-4 shadow-sm">
            <h5 class="fw-700 mb-3">Pesan Terbaru</h5>
            <table class="table table-hover">
                <thead><tr><th>Nama</th><th>No. HP</th><th>Produk</th><th>Waktu</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($pesanTerbaru as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['nama']) ?></td>
                        <td><?= htmlspecialchars($m['no_hp'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($m['nama_produk'] ?? '-') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
                        <td><?= $m['sudah_dibaca'] ? '<span class="badge bg-success">Dibaca</span>' : '<span class="badge bg-danger">Baru</span>' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($pesanTerbaru)): ?>
                    <tr><td colspan="5" class="text-center text-muted">Belum ada pesan</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <a href="pesan.php" class="btn btn-sm" style="background:#c0974f;color:#fff;">Lihat Semua Pesan</a>
        </div>
    </div>
</body>
</html>
