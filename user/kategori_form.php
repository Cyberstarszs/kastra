<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();

$id_user=(int)$_SESSION['id_user'];
$id_kategori=(int)($_GET['id']??0);
$mode_edit=$id_kategori>0;
$data=['nama_kategori'=>'','jenis'=>'pengeluaran'];

if($mode_edit){
  $q=$koneksi->prepare('SELECT * FROM kategori WHERE id_kategori=:id AND id_user=:id_user');
  $q->execute(['id'=>$id_kategori,'id_user'=>$id_user]);
  $cek=$q->fetch(); if($cek){$data=$cek;}
}
if($_SERVER['REQUEST_METHOD']==='POST'){
  $data=['nama_kategori'=>trim($_POST['nama_kategori']??''),'jenis'=>$_POST['jenis']??'pengeluaran'];
  if($mode_edit){
    $s=$koneksi->prepare('UPDATE kategori SET nama_kategori=:nama_kategori, jenis=:jenis WHERE id_kategori=:id AND id_user=:id_user');
    $s->execute($data+['id'=>$id_kategori,'id_user'=>$id_user]);
  }else{
    $s=$koneksi->prepare('INSERT INTO kategori (id_user,nama_kategori,jenis) VALUES (:id_user,:nama_kategori,:jenis)');
    $s->execute($data+['id_user'=>$id_user]);
  }
  header('Location: ' . url('user/kategori.php')); exit;
}
$judul_halaman=$mode_edit?'Edit Kategori':'Tambah Kategori';
include __DIR__ . '/../partials/header.php'; include __DIR__ . '/../partials/sidebar.php'; include __DIR__ . '/../partials/topbar.php';
?>
<div class="card card-modern p-3">
<form method="post" class="row g-3">
<div class="col-md-6"><label class="form-label">Nama Kategori</label><input name="nama_kategori" class="form-control" value="<?= h($data['nama_kategori']) ?>" required></div>
<div class="col-md-6"><label class="form-label">Jenis</label><select name="jenis" class="form-select"><option value="pemasukan" <?= $data['jenis']==='pemasukan'?'selected':'' ?>>Pemasukan</option><option value="pengeluaran" <?= $data['jenis']==='pengeluaran'?'selected':'' ?>>Pengeluaran</option></select></div>
<div class="col-12"><button class="btn btn-utama">Simpan</button> <a class="btn btn-light" href="<?= url('user/kategori.php') ?>">Kembali</a></div>
</form>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>

