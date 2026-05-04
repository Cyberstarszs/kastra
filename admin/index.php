<?php
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();
$judul_halaman='Admin';
include __DIR__ . '/../partials/header.php'; include __DIR__ . '/../partials/sidebar.php'; include __DIR__ . '/../partials/topbar.php';
?>
<div class="card card-modern p-3">Halaman admin opsional sederhana.</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>

