<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_admin();
$penggunaan_ai = $koneksi->query("SELECT u.nama_lengkap, u.email, COUNT(c.id_chat) AS total_request FROM users u LEFT JOIN chat_ai c ON c.id_user=u.id_user WHERE u.role='user' GROUP BY u.id_user ORDER BY total_request DESC")->fetchAll();
$total_penggunaan = 0;
foreach ($penggunaan_ai as $row) { $total_penggunaan += (int) $row['total_request']; }
$judul_halaman = 'Penggunaan AI';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="mb-1" style="font-size:24px;">Penggunaan AI</h2><p class="text-muted mb-0">Pemantauan penggunaan AI per pengguna.</p></div><div class="badge-status badge-premium">Total Permintaan: <?= $total_penggunaan ?></div></div>
<div class="card-modern p-3 table-responsive">
<table class="table table-clean align-middle mb-0"><thead><tr><th>Nama</th><th>Email</th><th class="text-end">Jumlah Permintaan</th></tr></thead><tbody>
<?php foreach ($penggunaan_ai as $item): ?><tr><td><?= h($item['nama_lengkap']) ?></td><td><?= h($item['email']) ?></td><td class="text-end"><?= (int)$item['total_request'] ?></td></tr><?php endforeach; ?>
</tbody></table>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
