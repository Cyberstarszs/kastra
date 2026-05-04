<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();

$id_user = (int) $_SESSION['id_user'];
$filter_tanggal = $_GET['tanggal'] ?? '';
$filter_kategori = trim($_GET['kategori'] ?? '');

$sql = 'SELECT * FROM transaksi WHERE id_user = :id_user';
$param = ['id_user' => $id_user];
if ($filter_tanggal !== '') { $sql .= ' AND tanggal = :tanggal'; $param['tanggal'] = $filter_tanggal; }
if ($filter_kategori !== '') { $sql .= ' AND kategori LIKE :kategori'; $param['kategori'] = "%$filter_kategori%"; }
$sql .= ' ORDER BY tanggal DESC, id_transaksi DESC';

$query = $koneksi->prepare($sql);
$query->execute($param);
$data_transaksi = $query->fetchAll();

$judul_halaman = 'Transaksi';
include __DIR__ . '/../partials/header.php'; include __DIR__ . '/../partials/sidebar.php'; include __DIR__ . '/../partials/topbar.php';
?>
<div class="card card-modern p-3">
  <div class="d-flex justify-content-between mb-3"><h6>Data Transaksi</h6><a href="<?= url('user/transaksi_form.php') ?>" class="btn btn-utama btn-sm">+ Tambah</a></div>
  <form class="row g-2 mb-3" method="get">
    <div class="col-md-4"><input type="date" name="tanggal" class="form-control" value="<?= h($filter_tanggal) ?>"></div>
    <div class="col-md-4"><input type="text" name="kategori" class="form-control" placeholder="Filter kategori" value="<?= h($filter_kategori) ?>"></div>
    <div class="col-md-4"><button class="btn btn-dark">Filter</button> <a class="btn btn-light" href="<?= url('user/transaksi.php') ?>">Reset</a></div>
  </form>
  <div class="table-responsive">
    <table class="table">
      <thead><tr><th>Tanggal</th><th>Jenis</th><th>Kategori</th><th>Deskripsi</th><th>Nominal</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($data_transaksi as $trx): ?>
        <tr>
          <td><?= h($trx['tanggal']) ?></td><td><?= h($trx['jenis_transaksi']) ?></td><td><?= h($trx['kategori']) ?></td><td><?= h($trx['deskripsi']) ?></td>
          <td class="<?= $trx['jenis_transaksi'] === 'pemasukan' ? 'nilai-hijau' : 'nilai-merah' ?>"><?= format_rupiah((float)$trx['nominal']) ?></td>
          <td><a class="btn btn-warning btn-sm" href="<?= url('user/transaksi_form.php?id=' . (int)$trx['id_transaksi']) ?>">Edit</a> <a class="btn btn-danger btn-sm" onclick="return konfirmasiHapus()" href="<?= url('user/transaksi_hapus.php?id=' . (int)$trx['id_transaksi']) ?>">Hapus</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>

