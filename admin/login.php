<?php
// admin/login.php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama_lengkap'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Admin Login – PT Cipta Pesona Teknik</title>
    <link rel="stylesheet" href="../assets/css/vendors.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: #f4f6f9; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .login-box { background:#fff; border-radius:12px; padding:40px; width:100%; max-width:400px; box-shadow:0 4px 20px rgba(0,0,0,.1); }
        .login-box h2 { margin-bottom:24px; font-weight:700; }
        .btn-login { width:100%; padding:12px; font-weight:700; border-radius:8px; background:#c0974f; border:none; color:#fff; font-size:16px; cursor:pointer; }
        .btn-login:hover { background:#a07a38; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 class="text-center">🔐 Admin Panel</h2>
        <p class="text-center text-muted mb-4">PT Cipta Pesona Teknik</p>
        <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label fw-600">Username</label>
                <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label fw-600">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>
        <p class="text-center mt-3"><a href="../index.php">← Kembali ke Website</a></p>
    </div>
</body>
</html>
