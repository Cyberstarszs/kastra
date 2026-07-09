<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_admin();
$koneksi->prepare("UPDATE transaksi_premium SET status='expired' WHERE status='pending' AND expired_at < :now")
    ->execute(['now' => date('Y-m-d H:i:s')]);
$data_transaksi = $koneksi->query("SELECT tp.*, u.nama_lengkap, u.email FROM transaksi_premium tp JOIN users u ON u.id_user=tp.id_user ORDER BY tp.waktu_buat DESC")->fetchAll();
$judul_halaman = 'Transaksi Premium';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="mb-1" style="font-size:24px;">Transaksi Premium</h2><p class="text-muted mb-0">Status premium diverifikasi otomatis melalui webhook.</p></div></div>
<div class="card-modern p-3 table-responsive"><table class="table table-clean align-middle mb-0"><thead><tr><th>Pengguna</th><th>Faktur</th><th>Status</th><th>Jumlah</th><th>Metode</th><th>Dibuat</th><th>Dibayar</th><th>Kadaluarsa</th></tr></thead><tbody>
<?php foreach($data_transaksi as $item): ?><tr>
<td><div><?= h($item['nama_lengkap']) ?></div><small class="text-muted"><?= h($item['email']) ?></small></td>
<td><?= h($item['kode_invoice']) ?></td>
<td>
<?php $label = $item['status'] === 'berhasil' ? 'Pembayaran Berhasil' : ($item['status'] === 'pending' ? 'Menunggu Pembayaran' : ($item['status'] === 'expired' ? 'QR Kadaluarsa' : 'Pembayaran Gagal')); ?>
<span class="badge-status <?= $item['status']==='berhasil'?'badge-masuk':($item['status']==='pending'?'badge-biasa':'badge-keluar') ?>"><?= h($label) ?></span>
</td>
<td><?= h(format_rupiah((float)$item['jumlah'])) ?></td>
<td><?= h($item['metode_pembayaran'] ?: '-') ?></td>
<td><?= h($item['waktu_buat']) ?></td>
<td><?= h($item['waktu_bayar'] ?: '-') ?></td>
<td><?= h($item['expired_at']) ?></td>
</tr><?php endforeach; ?>
</tbody></table></div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
