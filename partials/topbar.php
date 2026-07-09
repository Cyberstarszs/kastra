<?php
if (isset($_SESSION['id_user']) && isset($koneksi)) {
    $status_akun = sinkron_status_premium($koneksi, (int) $_SESSION['id_user']);
    $_SESSION['status_user'] = $status_akun['status_user'] ?? 'biasa';
}
$nama_pengguna = (string) ($_SESSION['nama_lengkap'] ?? 'Pengguna');
$inisial_pengguna = strtoupper(substr($nama_pengguna, 0, 1));
$foto_pengguna = trim((string) ($_SESSION['foto'] ?? ''));
$url_foto_pengguna = $foto_pengguna !== '' ? url($foto_pengguna) : '';
?>
<div class="topbar">
    <div class="topbar-grid">
        <div>
            <button id="tombolSidebar" class="icon-btn mobile-toggle" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
            <?php if (!user_admin()): ?>
                <a href="<?= url('user/upgrade_premium.php') ?>" class="badge-status <?= (($_SESSION['status_user'] ?? 'biasa') === 'premium') ? 'badge-premium' : 'badge-biasa' ?> text-decoration-none"><?= (($_SESSION['status_user'] ?? 'biasa') === 'premium') ? 'Premium' : 'Standar' ?></a>
            <?php else: ?>
                <span class="badge-status badge-premium">Admin</span>
            <?php endif; ?>
            <div class="avatar-chip">
                <?php if ($url_foto_pengguna !== ''): ?>
                    <span class="avatar-media">
                        <img src="<?= h($url_foto_pengguna) ?>" alt="Foto Profil" class="avatar-foto" width="32" height="32">
                    </span>
                <?php else: ?>
                    <div class="avatar-circle"><?= h($inisial_pengguna) ?></div>
                <?php endif; ?>
                <div class="small"><?= h($nama_pengguna) ?></div>
            </div>
        </div>
    </div>
</div>
<div class="content-area">
