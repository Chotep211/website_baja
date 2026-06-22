<?php
// admin/ganti_password.php
session_start();
require_once __DIR__ . '/../includes/config.php';
requireLogin();

$pdo = getDB();
$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passwordLama  = $_POST['password_lama']  ?? '';
    $passwordBaru  = $_POST['password_baru']  ?? '';
    $passwordUlang = $_POST['password_ulang'] ?? '';

    // Ambil data admin yang sedang login
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($passwordLama, $admin['password'])) {
        $error = 'Password lama salah.';
    } elseif (strlen($passwordBaru) < 6) {
        $error = 'Password baru minimal 6 karakter.';
    } elseif ($passwordBaru !== $passwordUlang) {
        $error = 'Konfirmasi password baru tidak cocok.';
    } else {
        $hashBaru = password_hash($passwordBaru, PASSWORD_BCRYPT);
        $pdo->prepare("UPDATE admin SET password = ? WHERE id = ?")
            ->execute([$hashBaru, $_SESSION['admin_id']]);
        $msg = 'Password berhasil diganti! Gunakan password baru saat login berikutnya.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Ganti Password – Admin</title>
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
        .card-box{max-width:480px}
    </style>
</head>
<body>
<div class="sidebar">
    <div class="brand">⚙️ Admin Panel</div>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="produk.php">📦 Produk</a>
    <a href="pesan.php">✉️ Pesan</a>
    <a href="pengaturan.php">⚙️ Pengaturan</a>
    <a href="ganti_password.php" class="active">🔑 Ganti Password</a>
    <a href="logout.php" style="color:#e74c3c">🚪 Logout</a>
</div>
<div class="main-content">
    <div class="topbar">
        <h4 class="mb-0 fw-700">🔑 Ganti Password Admin</h4>
    </div>
    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <div class="bg-white rounded p-4 shadow-sm card-box">
        <form method="post">
            <div class="mb-3">
                <label class="form-label fw-600">Password Lama</label>
                <input type="password" class="form-control" name="password_lama" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Password Baru</label>
                <input type="password" class="form-control" name="password_baru" minlength="6" required>
                <small class="text-muted">Minimal 6 karakter</small>
            </div>
            <div class="mb-4">
                <label class="form-label fw-600">Ulangi Password Baru</label>
                <input type="password" class="form-control" name="password_ulang" minlength="6" required>
            </div>
            <button type="submit" class="btn-gold">💾 Simpan Password Baru</button>
        </form>
    </div>
</div>
</body>
</html>
