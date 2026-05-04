<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';

if (sudah_login()) {
    header('Location: ' . url('user/dashboard.php'));
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

        header('Location: ' . url('user/dashboard.php'));
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kastra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="bg-auth">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-modern p-4">
                <h3 class="text-center mb-3 text-utama">Login Kastra</h3>
                <?php if (isset($_GET['register'])): ?><div class="alert alert-success">Register berhasil, silakan login.</div><?php endif; ?>
                <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
                <form method="post">
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Kata Sandi</label><input type="password" name="kata_sandi" class="form-control" required></div>
                    <button class="btn btn-utama w-100" type="submit">Login</button>
                </form>
                <p class="text-center mt-3 mb-0">Belum punya akun? <a href="<?= url('auth/register.php') ?>">Register</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>

