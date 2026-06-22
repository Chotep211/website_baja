<?php
// admin/pesan.php
session_start();
require_once __DIR__ . '/../includes/config.php';
requireLogin();

$pdo = getDB();

// Tandai sudah dibaca
if (isset($_GET['baca'])) {
    $pdo->prepare("UPDATE pesan_kontak SET sudah_dibaca=TRUE WHERE id=?")->execute([(int)$_GET['baca']]);
}
// Hapus pesan
if (isset($_GET['hapus'])) {
    $pdo->prepare("DELETE FROM pesan_kontak WHERE id=?")->execute([(int)$_GET['hapus']]);
}

$pesanList = $pdo->query("SELECT pk.*, p.nama AS nama_produk FROM pesan_kontak pk LEFT JOIN produk p ON pk.produk_id = p.id ORDER BY pk.created_at DESC")->fetchAll();
$pesanBaru = $pdo->query("SELECT COUNT(*) FROM pesan_kontak WHERE sudah_dibaca=FALSE")->fetchColumn();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Pesan Masuk – Admin</title>
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
        tr.unread{background:#fff9f0}
    </style>
</head>
<body>
<div class="sidebar">
    <div class="brand">⚙️ Admin Panel</div>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="produk.php">📦 Produk</a>
    <a href="pesan.php" class="active">✉️ Pesan <?php if($pesanBaru>0): ?><span style="background:#e74c3c;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;"><?= $pesanBaru ?></span><?php endif; ?></a>
    <a href="pengaturan.php">⚙️ Pengaturan</a>
    <a href="ganti_password.php">🔑 Ganti Password</a>
    <a href="logout.php" style="color:#e74c3c">🚪 Logout</a>
</div>
<div class="main-content">
    <div class="topbar">
        <h4 class="mb-0 fw-700">✉️ Pesan Masuk (<?= $pesanBaru ?> belum dibaca)</h4>
    </div>
    <div class="bg-white rounded p-4 shadow-sm">
        <table class="table table-hover">
            <thead><tr><th>Nama</th><th>No. HP</th><th>Email</th><th>Produk</th><th>Pesan</th><th>Waktu</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($pesanList as $m): ?>
            <tr class="<?= !$m['sudah_dibaca'] ? 'unread' : '' ?>">
                <td><strong><?= htmlspecialchars($m['nama']) ?></strong><?= !$m['sudah_dibaca'] ? ' 🔴' : '' ?></td>
                <td>
                    <?php if ($m['no_hp']): ?>
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $m['no_hp']) ?>" target="_blank"><?= htmlspecialchars($m['no_hp']) ?></a>
                    <?php else: ?>–<?php endif; ?>
                </td>
                <td><?= htmlspecialchars($m['email'] ?? '–') ?></td>
                <td><?= htmlspecialchars($m['nama_produk'] ?? '–') ?></td>
                <td style="max-width:200px;white-space:pre-wrap"><?= htmlspecialchars($m['pesan']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
                <td>
                    <?php if (!$m['sudah_dibaca']): ?>
                    <a href="?baca=<?= $m['id'] ?>" class="btn btn-sm btn-success mb-1">✔ Baca</a>
                    <?php endif; ?>
                    <a href="?hapus=<?= $m['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pesan ini?')">🗑</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($pesanList)): ?>
            <tr><td colspan="7" class="text-center text-muted">Belum ada pesan masuk</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
