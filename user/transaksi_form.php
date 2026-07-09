<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();
$id_user = (int) $_SESSION['id_user'];
$id_transaksi = (int) ($_GET['id'] ?? 0);
$mode_edit = $id_transaksi > 0;
$data = ['jenis_transaksi' => 'pengeluaran', 'nominal' => '', 'kategori' => '', 'deskripsi' => '', 'tanggal' => date('Y-m-d')];
if ($mode_edit) {
    $q = $koneksi->prepare('SELECT * FROM transaksi WHERE id_transaksi = :id AND id_user = :id_user');
    $q->execute(['id' => $id_transaksi, 'id_user' => $id_user]);
    $hasil = $q->fetch();
    if ($hasil) { $data = $hasil; }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
      'jenis_transaksi' => $_POST['jenis_transaksi'] ?? 'pengeluaran',
      'nominal' => (float) ($_POST['nominal'] ?? 0),
      'kategori' => trim($_POST['kategori'] ?? ''),
      'deskripsi' => trim($_POST['deskripsi'] ?? ''),
      'tanggal' => $_POST['tanggal'] ?? date('Y-m-d')
    ];
    if ($mode_edit) {
        $simpan = $koneksi->prepare('UPDATE transaksi SET jenis_transaksi=:jenis_transaksi, nominal=:nominal, kategori=:kategori, deskripsi=:deskripsi, tanggal=:tanggal WHERE id_transaksi=:id AND id_user=:id_user');
        $simpan->execute($data + ['id' => $id_transaksi, 'id_user' => $id_user]);
    } else {
        $simpan = $koneksi->prepare('INSERT INTO transaksi (id_user, jenis_transaksi, nominal, kategori, deskripsi, tanggal) VALUES (:id_user, :jenis_transaksi, :nominal, :kategori, :deskripsi, :tanggal)');
        $simpan->execute($data + ['id_user' => $id_user]);
    }
    header('Location: ' . url('user/transaksi.php'));
    exit;
}
$judul_halaman = $mode_edit ? 'Edit Transaksi' : 'Tambah Transaksi';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="card-modern p-4">
  <h5 class="section-title"><?= h($judul_halaman) ?></h5>
  <form method="post" class="row g-3">
    <div class="col-md-6"><label class="form-label">Jenis</label><select name="jenis_transaksi" class="form-select"><option value="pemasukan" <?= $data['jenis_transaksi']==='pemasukan'?'selected':'' ?>>Pemasukan</option><option value="pengeluaran" <?= $data['jenis_transaksi']==='pengeluaran'?'selected':'' ?>>Pengeluaran</option></select></div>
    <div class="col-md-6"><label class="form-label">Nominal</label><input type="number" name="nominal" class="form-control" value="<?= h($data['nominal']) ?>" required></div>
    <div class="col-md-6"><label class="form-label">Kategori</label><input type="text" name="kategori" class="form-control" value="<?= h($data['kategori']) ?>" required></div>
    <div class="col-md-6"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-control" value="<?= h(substr($data['tanggal'],0,10)) ?>" required></div>
    <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="4"><?= h($data['deskripsi']) ?></textarea></div>
    <div class="col-12 d-flex gap-2"><button class="btn btn-utama">Simpan</button><a href="<?= url('user/transaksi.php') ?>" class="btn btn-light">Kembali</a></div>
  </form>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
