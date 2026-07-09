<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
$konfigurasi_aplikasi = require __DIR__ . '/../config/app.php';
$base_url = rtrim((string) ($konfigurasi_aplikasi['base_url'] ?? ''), '/');
if (sudah_login()) {
    header('Location: ' . url('user/dashboard.php'));
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email = normalisasi_email($_POST['email'] ?? '');
    $kata_sandi = $_POST['kata_sandi'] ?? '';
    $konfirmasi_sandi = $_POST['konfirmasi_sandi'] ?? '';
    if ($nama_lengkap === '' || $email === '' || $kata_sandi === '' || $konfirmasi_sandi === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif ($kata_sandi !== $konfirmasi_sandi) {
        $error = 'Konfirmasi kata sandi tidak cocok.';
    } elseif (!validasi_email_daftar($email)) {
        $error = 'Format email tidak valid.';
    } else {
        $cek = $koneksi->prepare('SELECT id_user FROM users WHERE email = :email');
        $cek->execute(['email' => $email]);
        if ($cek->fetch()) {
            $error = 'Email sudah terdaftar.';
        } else {
            $simpan = $koneksi->prepare('INSERT INTO users (nama_lengkap, email, kata_sandi, status_user, sisa_prompt, tanggal_reset_prompt, role, tanggal_daftar) VALUES (:nama_lengkap, :email, :kata_sandi, :status_user, :sisa_prompt, :tanggal_reset_prompt, :role, NOW())');
            $simpan->execute([
                'nama_lengkap' => $nama_lengkap,
                'email' => $email,
                'kata_sandi' => password_hash($kata_sandi, PASSWORD_DEFAULT),
                'status_user' => 'biasa',
                'sisa_prompt' => 15,
                'tanggal_reset_prompt' => date('Y-m-d'),
                'role' => 'user',
            ]);
            header('Location: ' . url('auth/login.php?register=sukses'));
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Kastra</title>
    <meta name="description" content="Buat akun Kastra dan mulai kelola keuangan secara lebih teratur.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= h($base_url . '/auth/register.php') ?>">
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
    <h3 class="mb-1 text-center">Buat Akun Baru</h3>
    <p class="text-muted mb-4 text-center" style="font-size: 13.5px;">Mulai kelola keuangan Anda secara lebih teratur.</p>
    <?php if ($error): ?><div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 13px;"><?= h($error) ?></div><?php endif; ?>
    <form method="post" class="auth-form">
      <div class="mb-3">
        <label class="form-label auth-label">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control auth-control" required autocomplete="name">
      </div>
      <div class="mb-3">
        <label class="form-label auth-label">Email</label>
        <input type="email" name="email" class="form-control auth-control" required autocomplete="email">
      </div>
      <div class="mb-3">
        <label class="form-label auth-label">Kata Sandi</label>
        <input type="password" name="kata_sandi" class="form-control auth-control" required autocomplete="new-password">
      </div>
      <div class="mb-3">
        <label class="form-label auth-label">Konfirmasi Kata Sandi</label>
        <input type="password" name="konfirmasi_sandi" class="form-control auth-control" required autocomplete="new-password">
      </div>
      <button class="btn btn-utama w-100 mb-3 auth-submit" type="submit">Daftar Sekarang</button>
    </form>
    <p class="mb-3 text-center" style="font-size: 13.5px; color: var(--teks-muted);">
      Sudah punya akun? <a href="<?= url('auth/login.php') ?>" class="auth-link">Masuk</a>
    </p>
    <p class="auth-legal mb-0 text-center text-muted">
      Dengan mendaftar, Anda menyetujui Ketentuan Layanan dan Kebijakan Privasi Kastra.
    </p>
  </div>
</div>
</body>
</html>
