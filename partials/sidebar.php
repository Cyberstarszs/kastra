<?php
$halaman_aktif = basename($_SERVER['PHP_SELF']);
$is_premium_sidebar = false;
if (isset($_SESSION['id_user']) && !user_admin()) {
    if (isset($koneksi)) {
        $status_akun_sidebar = sinkron_status_premium($koneksi, (int)$_SESSION['id_user']);
        $is_premium_sidebar = ($status_akun_sidebar['status_user'] ?? 'biasa') === 'premium';
    } else {
        $is_premium_sidebar = ($_SESSION['status_user'] ?? 'biasa') === 'premium';
    }
}
?>
<aside id="sidebarUtama" class="sidebar">
    <div class="brand">
        <span class="brand-logo-wrap">
            <img src="<?= url('assets/image/logo.jpeg') ?>" alt="Logo Kastra" class="brand-logo" width="28" height="28">
        </span>
        <span class="sidebar-text">Kastra</span>
    </div>
    <?php if (user_admin()): ?>
        <a class="menu-link <?= $halaman_aktif === 'dashboard.php' ? 'active' : '' ?>" href="<?= url('admin/dashboard.php') ?>"><i class="bi bi-grid"></i> <span class="sidebar-text">Dasbor</span></a>
        <a class="menu-link <?= $halaman_aktif === 'data_user.php' ? 'active' : '' ?>" href="<?= url('admin/data_user.php') ?>"><i class="bi bi-people"></i> <span class="sidebar-text">Data Pengguna</span></a>
        <a class="menu-link <?= $halaman_aktif === 'kategori_global.php' ? 'active' : '' ?>" href="<?= url('admin/kategori_global.php') ?>"><i class="bi bi-tags"></i> <span class="sidebar-text">Kategori Global</span></a>
        <a class="menu-link <?= $halaman_aktif === 'laporan_sistem.php' ? 'active' : '' ?>" href="<?= url('admin/laporan_sistem.php') ?>"><i class="bi bi-bar-chart-line"></i> <span class="sidebar-text">Laporan Sistem</span></a>
        <a class="menu-link <?= $halaman_aktif === 'ai_usage.php' ? 'active' : '' ?>" href="<?= url('admin/ai_usage.php') ?>"><i class="bi bi-robot"></i> <span class="sidebar-text">Penggunaan AI</span></a>
        <a class="menu-link <?= $halaman_aktif === 'transaksi_premium.php' ? 'active' : '' ?>" href="<?= url('admin/transaksi_premium.php') ?>"><i class="bi bi-credit-card"></i> <span class="sidebar-text">Transaksi Premium</span></a>
        <a class="menu-link <?= $halaman_aktif === 'pengaturan_sistem.php' ? 'active' : '' ?>" href="<?= url('admin/pengaturan_sistem.php') ?>"><i class="bi bi-sliders"></i> <span class="sidebar-text">Pengaturan Sistem</span></a>
    <?php else: ?>
        <a class="menu-link <?= $halaman_aktif === 'dashboard.php' ? 'active' : '' ?>" href="<?= url('user/dashboard.php') ?>"><i class="bi bi-grid"></i> <span class="sidebar-text">Dasbor</span></a>
        <a class="menu-link <?= in_array($halaman_aktif, ['transaksi.php','transaksi_form.php'], true) ? 'active' : '' ?>" href="<?= url('user/transaksi.php') ?>"><i class="bi bi-wallet2"></i> <span class="sidebar-text">Transaksi</span></a>
        <a class="menu-link <?= in_array($halaman_aktif, ['tabungan.php'], true) ? 'active' : '' ?>" href="<?= url('user/tabungan.php') ?>"><i class="bi bi-piggy-bank"></i> <span class="sidebar-text">Tabungan</span></a>
        <a class="menu-link <?= $halaman_aktif === 'laporan.php' ? 'active' : '' ?>" href="<?= url('user/laporan.php') ?>"><i class="bi bi-bar-chart"></i> <span class="sidebar-text">Laporan</span></a>
        <a class="menu-link <?= $halaman_aktif === 'ai_assistant.php' ? 'active' : '' ?>" href="<?= url('user/ai_assistant.php') ?>"><i class="bi bi-robot"></i> <span class="sidebar-text">Asisten Keuangan</span></a>
        <a class="menu-link <?= in_array($halaman_aktif, ['kategori.php','kategori_form.php'], true) ? 'active' : '' ?>" href="<?= url('user/kategori.php') ?>"><i class="bi bi-grid-3x3-gap"></i> <span class="sidebar-text">Kategori Transaksi</span></a>
        <a class="menu-link <?= $halaman_aktif === 'pengaturan.php' ? 'active' : '' ?>" href="<?= url('user/pengaturan.php') ?>"><i class="bi bi-gear"></i> <span class="sidebar-text">Pengaturan Akun</span></a>
    <?php endif; ?>
    <a class="menu-link" href="<?= url('auth/logout.php') ?>" onclick="return konfirmasiHapus('Keluar dari akun?')"><i class="bi bi-box-arrow-right"></i> <span class="sidebar-text">Keluar</span></a>
    <?php if (isset($_SESSION['id_user']) && !user_admin() && !$is_premium_sidebar): ?>
        <div class="sidebar-upgrade-card">
            <div class="sidebar-upgrade-glow"></div>
            <div class="sidebar-upgrade-title">
                <i class="bi bi-gem text-warning sidebar-gem-bounce" style="font-size: 0.85rem;"></i>
                <span>Upgrade Premium</span>
            </div>
            <p class="sidebar-upgrade-desc">Dapatkan asisten AI tanpa batas & target tabungan tak terbatas.</p>
            <a class="btn btn-primary btn-sm w-100 fw-bold btn-sidebar-upgrade" href="<?= url('user/upgrade_premium.php') ?>">
                Upgrade Sekarang
            </a>
        </div>
    <?php endif; ?>
</aside>
<div class="main-content">
