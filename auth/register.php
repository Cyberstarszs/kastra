<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';

if (sudah_login()) {
    header('Location: ' . url('user/dashboard.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $kata_sandi = $_POST['kata_sandi'] ?? '';

    if ($nama_lengkap === '' || $email === '' || $kata_sandi === '') {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $cek = $koneksi->prepare('SELECT id_user FROM users WHERE email = :email');
        $cek->execute(['email' => $email]);
        if ($cek->fetch()) {
            $error = 'Email sudah terdaftar.';
        } else {
            $simpan = $koneksi->prepare('INSERT INTO users (nama_lengkap, email, kata_sandi, tanggal_daftar) VALUES (:nama_lengkap, :email, :kata_sandi, NOW())');
            $simpan->execute([
                'nama_lengkap' => $nama_lengkap,
                'email' => $email,
                'kata_sandi' => password_hash($kata_sandi, PASSWORD_DEFAULT),
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
    <title>Register - Kastra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="bg-auth">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-modern p-4">
                <h3 class="text-center mb-3 text-utama">Daftar Akun</h3>
                <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
                <form method="post">
                    <div class="mb-3"><label class="form-label">Nama Lengkap</label><input type="text" name="nama_lengkap" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Kata Sandi</label><input type="password" name="kata_sandi" class="form-control" required></div>
                    <button class="btn btn-utama w-100" type="submit">Register</button>
                </form>
                <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="<?= url('auth/login.php') ?>">Login</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>

