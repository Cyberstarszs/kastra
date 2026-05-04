<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();
$id_user=(int)$_SESSION['id_user'];
$q=$koneksi->prepare('SELECT * FROM kategori WHERE id_user=:id_user ORDER BY jenis,nama_kategori');
$q->execute(['id_user'=>$id_user]);
$data_kategori=$q->fetchAll();
$judul_halaman='Kategori';
include __DIR__ . '/../partials/header.php'; include __DIR__ . '/../partials/sidebar.php'; include __DIR__ . '/../partials/topbar.php';
?>
<div class="card card-modern p-3">
<div class="d-flex justify-content-between mb-3"><h6>Data Kategori</h6><a class="btn btn-utama btn-sm" href="<?= url('user/kategori_form.php') ?>">+ Tambah</a></div>
<table class="table"><thead><tr><th>Nama</th><th>Jenis</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($data_kategori as $item): ?>
<tr><td><?= h($item['nama_kategori']) ?></td><td><?= h($item['jenis']) ?></td><td><a class="btn btn-warning btn-sm" href="<?= url('user/kategori_form.php?id=' . (int)$item['id_kategori']) ?>">Edit</a> <a class="btn btn-danger btn-sm" onclick="return konfirmasiHapus()" href="<?= url('user/kategori_hapus.php?id=' . (int)$item['id_kategori']) ?>">Hapus</a></td></tr>
<?php endforeach; ?>
</tbody></table>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>

