<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
$konfigurasi_aplikasi = require __DIR__ . '/../config/app.php';
$base_url = rtrim((string) ($konfigurasi_aplikasi['base_url'] ?? ''), '/');
if (sudah_login()) {
    header('Location: ' . (user_admin() ? url('admin/dashboard.php') : url('user/dashboard.php')));
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $kata_sandi = $_POST['kata_sandi'] ?? '';
    $query = $koneksi->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $query->execute(['email' => $email]);
    $user = $query->fetch();
    if (!$user || !password_verify($kata_sandi, $user['kata_sandi'])) {
        $error = 'Email atau kata sandi salah.';
    } else {
        session_regenerate_id(true);
        $_SESSION['id_user'] = (int) $user['id_user'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['foto'] = $user['foto'] ?? '';
        $_SESSION['status_user'] = $user['status_user'] ?? 'biasa';
        $_SESSION['role'] = $user['role'] ?? 'user';
        header('Location: ' . (($_SESSION['role'] ?? 'user') === 'admin' ? url('admin/dashboard.php') : url('user/dashboard.php')));
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Kastra</title>
    <meta name="description" content="Masuk ke Kastra untuk mengelola keuangan secara mudah dan terstruktur.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= h($base_url . '/auth/login.php') ?>">
    <link rel="icon" type="image/jpeg" href="<?= url('assets/image/favicon.jpeg') ?>">
    <link rel="apple-touch-icon" href="<?= url('assets/image/favicon.jpeg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="page-ready">
<div class="auth-shell">
  <div class="auth-card">
    <div class="auth-logo-container">
      <div class="auth-logo-icon">
        <i class="bi bi-wallet2"></i>
      </div>
      <div class="auth-logo-text">Kastra</div>
    </div>
    <h3 class="mb-1 text-center">Masuk ke Akun</h3>
    <p class="text-muted mb-4 text-center" style="font-size: 13.5px;">Kelola keuangan Anda secara mudah dan terstruktur.</p>
    <?php if (isset($_GET['register'])): ?><div class="alert alert-success py-2 px-3 mb-3" style="font-size: 13px;">Pendaftaran berhasil. Silakan masuk.</div><?php endif; ?>
    <?php if (isset($_GET['reset'])): ?><div class="alert alert-success py-2 px-3 mb-3" style="font-size: 13px;">Kata sandi berhasil diperbarui. Silakan masuk.</div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 13px;"><?= h($error) ?></div><?php endif; ?>
    <form method="post" class="auth-form">
      <div class="mb-3">
        <label class="form-label auth-label">Email</label>
        <input type="email" name="email" class="form-control auth-control" required autocomplete="email">
      </div>
      <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center">
          <label class="form-label auth-label mb-0">Kata Sandi</label>
          <a href="<?= url('auth/lupa_password.php') ?>" class="auth-link text-muted" style="font-size: 12px; font-weight: 500;">Lupa kata sandi?</a>
        </div>
        <input type="password" name="kata_sandi" class="form-control auth-control mt-1" required autocomplete="current-password">
      </div>
      <button class="btn btn-utama w-100 mb-3 auth-submit" type="submit">Masuk</button>
    </form>
    <p class="mb-3 text-center" style="font-size: 13.5px; color: var(--teks-muted);">
      Belum punya akun? <a href="<?= url('auth/register.php') ?>" class="auth-link">Daftar</a>
    </p>
    <p class="auth-legal mb-0 text-center text-muted">
      Dengan masuk, Anda menyetujui Ketentuan Layanan dan Kebijakan Privasi Kastra.
    </p>
  </div>
</div>
</body>
</html>
