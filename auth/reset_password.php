<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
$konfigurasi_aplikasi = require __DIR__ . '/../config/app.php';
$base_url = rtrim((string) ($konfigurasi_aplikasi['base_url'] ?? ''), '/');
if (sudah_login()) {
    header('Location: ' . (user_admin() ? url('admin/dashboard.php') : url('user/dashboard.php')));
    exit;
}
$email = normalisasi_email($_GET['email'] ?? $_POST['email'] ?? '');
$token = trim((string) ($_GET['token'] ?? $_POST['token'] ?? ''));
$error = '';
$pesan = '';
$tokenValid = false;
$resetData = null;
if ($email !== '' && $token !== '') {
    $q = $koneksi->prepare('SELECT * FROM password_resets WHERE email = :email AND used_at IS NULL AND expired_at > :now ORDER BY id_reset DESC LIMIT 5');
    $q->execute(['email' => $email, 'now' => date('Y-m-d H:i:s')]);
    $kandidat = $q->fetchAll();
    foreach ($kandidat as $row) {
        if (password_verify($token, (string) $row['token_hash'])) {
            $tokenValid = true;
            $resetData = $row;
            break;
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kataSandiBaru = (string) ($_POST['kata_sandi_baru'] ?? '');
    $konfirmasi = (string) ($_POST['konfirmasi_password'] ?? '');
    if (!$tokenValid || !$resetData) {
        $error = 'Data tidak valid.';
    } elseif (strlen($kataSandiBaru) < 8) {
        $error = 'Data tidak valid.';
    } elseif ($kataSandiBaru !== $konfirmasi) {
        $error = 'Data tidak valid.';
    } else {
        $koneksi->beginTransaction();
        try {
            $koneksi->prepare('UPDATE users SET kata_sandi = :kata_sandi WHERE id_user = :id_user LIMIT 1')
                ->execute([
                    'kata_sandi' => password_hash($kataSandiBaru, PASSWORD_DEFAULT),
                    'id_user' => (int) $resetData['id_user'],
                ]);
            $koneksi->prepare('UPDATE password_resets SET used_at = :now WHERE id_reset = :id_reset LIMIT 1')
                ->execute(['id_reset' => (int) $resetData['id_reset'], 'now' => date('Y-m-d H:i:s')]);
            $koneksi->commit();
            header('Location: ' . url('auth/login.php?reset=sukses'));
            exit;
        } catch (Throwable $e) {
            if ($koneksi->inTransaction()) {
                $koneksi->rollBack();
            }
            $error = 'Gagal memproses permintaan.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi - Kastra</title>
    <meta name="description" content="Buat kata sandi baru akun Kastra Anda.">
    <meta name="robots" content="noindex, nofollow">
    <link rel="canonical" href="<?= h($base_url . '/auth/reset_password.php') ?>">
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
    <h3 class="mb-1 text-center">Reset Kata Sandi</h3>
    <p class="text-muted mb-4 text-center" style="font-size: 13.5px;">Masukkan kata sandi baru untuk akun Anda.</p>
    <?php if (!$tokenValid): ?>
      <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 13px;">Data tidak valid.</div>
      <p class="mb-2 text-center" style="font-size: 13.5px;"><a href="<?= url('auth/lupa_password.php') ?>" class="auth-link">Minta tautan baru</a></p>
    <?php else: ?>
      <?php if ($pesan): ?><div class="alert alert-success py-2 px-3 mb-3" style="font-size: 13px;"><?= h($pesan) ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 13px;"><?= h($error) ?></div><?php endif; ?>
      <form method="post" class="auth-form">
        <input type="hidden" name="email" value="<?= h($email) ?>">
        <input type="hidden" name="token" value="<?= h($token) ?>">
        <div class="mb-3">
          <label class="form-label auth-label">Kata Sandi Baru</label>
          <input type="password" name="kata_sandi_baru" class="form-control auth-control" minlength="8" required autocomplete="new-password">
        </div>
        <div class="mb-3">
          <label class="form-label auth-label">Konfirmasi Kata Sandi</label>
          <input type="password" name="konfirmasi_password" class="form-control auth-control" minlength="8" required autocomplete="new-password">
        </div>
        <button class="btn btn-utama w-100 mb-3 auth-submit" type="submit">Simpan Kata Sandi</button>
      </form>
    <?php endif; ?>
    <p class="mb-0 text-center" style="font-size: 13.5px;">
      <a href="<?= url('auth/login.php') ?>" class="auth-link">Kembali ke halaman masuk</a>
    </p>
  </div>
</div>
</body>
</html>
