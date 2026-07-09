<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
$konfigurasi_aplikasi = require __DIR__ . '/../config/app.php';
$base_url = rtrim((string) ($konfigurasi_aplikasi['base_url'] ?? ''), '/');
if (sudah_login()) {
    header('Location: ' . (user_admin() ? url('admin/dashboard.php') : url('user/dashboard.php')));
    exit;
}
$pesan = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = normalisasi_email($_POST['email'] ?? '');
    if (!validasi_email_daftar($email)) {
        $error = 'Data tidak valid.';
    } else {
        $q = $koneksi->prepare('SELECT id_user, nama_lengkap, email FROM users WHERE email = :email LIMIT 1');
        $q->execute(['email' => $email]);
        $user = $q->fetch();
        $pesan = 'Jika email terdaftar, tautan reset kata sandi akan dikirim.';
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $tokenHash = password_hash($token, PASSWORD_DEFAULT);
            $expiredAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            $koneksi->prepare('UPDATE password_resets SET used_at = :now WHERE email = :email AND used_at IS NULL')
                ->execute(['email' => $email, 'now' => date('Y-m-d H:i:s')]);
            $koneksi->prepare('INSERT INTO password_resets (id_user, email, token_hash, expired_at) VALUES (:id_user, :email, :token_hash, :expired_at)')
                ->execute([
                    'id_user' => (int) $user['id_user'],
                    'email' => $email,
                    'token_hash' => $tokenHash,
                    'expired_at' => $expiredAt,
                ]);
            $resetLink = url('auth/reset_password.php?token=' . urlencode($token) . '&email=' . urlencode($email));
            $terkirim = kirim_email_reset_password($email, (string) ($user['nama_lengkap'] ?? 'Pengguna'), $resetLink);
            if (!$terkirim) {
                $error = 'Layanan email sedang mengalami gangguan. Silakan coba beberapa saat lagi.';
                $pesan = '';
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Kastra</title>
    <meta name="description" content="Minta tautan reset kata sandi akun Kastra Anda.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= h($base_url . '/auth/lupa_password.php') ?>">
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
    <h3 class="mb-1 text-center">Lupa Kata Sandi</h3>
    <p class="text-muted mb-4 text-center" style="font-size: 13.5px;">Masukkan email terdaftar untuk menerima tautan reset.</p>
    <?php if ($pesan): ?><div class="alert alert-success py-2 px-3 mb-3" style="font-size: 13px;"><?= h($pesan) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 13px;"><?= h($error) ?></div><?php endif; ?>
    <form method="post" class="auth-form">
      <div class="mb-3">
        <label class="form-label auth-label">Email</label>
        <input type="email" name="email" class="form-control auth-control" required autocomplete="email">
      </div>
      <button class="btn btn-utama w-100 mb-3 auth-submit" type="submit">Kirim Tautan Reset</button>
    </form>
    <p class="mb-0 text-center" style="font-size: 13.5px;">
      <a href="<?= url('auth/login.php') ?>" class="auth-link">Kembali ke halaman masuk</a>
    </p>
  </div>
</div>
</body>
</html>
