<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();

$id_user = (int) $_SESSION['id_user'];
$data_tabungan = $koneksi->prepare('SELECT *,(CASE WHEN target_nominal>0 THEN (nominal_terkumpul/target_nominal)*100 ELSE 0 END) AS progress FROM tabungan WHERE id_user=:id_user ORDER BY tanggal_target ASC');
$data_tabungan->execute(['id_user'=>$id_user]);
$list_tabungan = $data_tabungan->fetchAll();

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['aksi']) && $_POST['aksi']==='tambah_tujuan') {
    $simpan = $koneksi->prepare('INSERT INTO tabungan (id_user,nama_tujuan,target_nominal,nominal_terkumpul,tanggal_target) VALUES (:id_user,:nama_tujuan,:target_nominal,:nominal_terkumpul,:tanggal_target)');
    $simpan->execute([
      'id_user'=>$id_user,
      'nama_tujuan'=>trim($_POST['nama_tujuan']??''),
      'target_nominal'=>(float)($_POST['target_nominal']??0),
      'nominal_terkumpul'=>(float)($_POST['nominal_terkumpul']??0),
      'tanggal_target'=>$_POST['tanggal_target']??date('Y-m-d')
    ]);
    header('Location: ' . url('user/tabungan.php')); exit;
}

$judul_halaman='Tabungan';
include __DIR__ . '/../partials/header.php'; include __DIR__ . '/../partials/sidebar.php'; include __DIR__ . '/../partials/topbar.php';
?>
<div class="row g-3">
<div class="col-lg-5">
  <div class="card card-modern p-3">
    <h6>Tambah Target Tabungan</h6>
    <form method="post" class="row g-2">
      <input type="hidden" name="aksi" value="tambah_tujuan">
      <div class="col-12"><input class="form-control" name="nama_tujuan" placeholder="Nama tujuan" required></div>
      <div class="col-12"><input class="form-control" type="number" name="target_nominal" placeholder="Target nominal" required></div>
      <div class="col-12"><input class="form-control" type="number" name="nominal_terkumpul" placeholder="Nominal terkumpul awal" value="0" required></div>
      <div class="col-12"><input class="form-control" type="date" name="tanggal_target" required></div>
      <div class="col-12"><button class="btn btn-utama">Simpan</button></div>
    </form>
  </div>
</div>
<div class="col-lg-7">
  <div class="card card-modern p-3">
    <h6>Daftar Tabungan</h6>
    <?php foreach($list_tabungan as $tab): $progress=min(100,max(0,(float)$tab['progress'])); ?>
      <div class="mb-3">
        <div class="d-flex justify-content-between"><strong><?= h($tab['nama_tujuan']) ?></strong><small><?= format_rupiah((float)$tab['nominal_terkumpul']) ?> / <?= format_rupiah((float)$tab['target_nominal']) ?></small></div>
        <div class="progress my-2"><div class="progress-bar" style="width: <?= $progress ?>%"></div></div>
        <form class="d-flex gap-2" method="post" action="<?= url('user/tabungan_tambah_dana.php') ?>">
          <input type="hidden" name="id_tabungan" value="<?= (int)$tab['id_tabungan'] ?>">
          <input type="number" class="form-control form-control-sm" name="nominal_tambahan" placeholder="Tambah dana" required>
          <button class="btn btn-sm btn-dark">Tambah</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>

