<div class="topbar d-flex justify-content-between align-items-center">
    <div>
        <strong><?= h($judul_halaman ?? 'Dashboard') ?></strong>
    </div>
    <div>
        <span class="me-2">Halo, <?= h($_SESSION['nama_lengkap'] ?? 'User') ?></span>
    </div>
</div>
<div class="content-area">

