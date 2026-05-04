<?php $halaman_aktif = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar">
    <div class="brand">Kastra Finance</div>
    <a class="menu-link <?= $halaman_aktif === 'dashboard.php' ? 'active' : '' ?>" href="<?= url('user/dashboard.php') ?>">Dashboard</a>
    <a class="menu-link <?= in_array($halaman_aktif, ['transaksi.php','transaksi_form.php'], true) ? 'active' : '' ?>" href="<?= url('user/transaksi.php') ?>">Transaksi</a>
    <a class="menu-link <?= in_array($halaman_aktif, ['tabungan.php'], true) ? 'active' : '' ?>" href="<?= url('user/tabungan.php') ?>">Tabungan</a>
    <a class="menu-link <?= in_array($halaman_aktif, ['kategori.php','kategori_form.php'], true) ? 'active' : '' ?>" href="<?= url('user/kategori.php') ?>">Kategori</a>
    <a class="menu-link <?= $halaman_aktif === 'laporan.php' ? 'active' : '' ?>" href="<?= url('user/laporan.php') ?>">Laporan</a>
    <a class="menu-link <?= $halaman_aktif === 'ai_assistant.php' ? 'active' : '' ?>" href="<?= url('user/ai_assistant.php') ?>">AI Assistant</a>
    <a class="menu-link <?= $halaman_aktif === 'pengaturan.php' ? 'active' : '' ?>" href="<?= url('user/pengaturan.php') ?>">Pengaturan</a>
    <a class="menu-link" href="<?= url('auth/logout.php') ?>" onclick="return konfirmasiHapus('Keluar dari akun?')">Logout</a>
</aside>
<div class="main-content">

