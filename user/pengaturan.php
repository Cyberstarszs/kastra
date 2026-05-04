<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();
$id_user=(int)$_SESSION['id_user'];
$pesan='';

if($_SERVER['REQUEST_METHOD']==='POST'){
  if(($_POST['aksi']??'')==='profil'){
    $nama_lengkap=trim($_POST['nama_lengkap']??'');
    $email=trim($_POST['email']??'');
    $u=$koneksi->prepare('UPDATE users SET nama_lengkap=:nama_lengkap,email=:email WHERE id_user=:id_user');
    $u->execute(['nama_lengkap'=>$nama_lengkap,'email'=>$email,'id_user'=>$id_user]);
    $_SESSION['nama_lengkap']=$nama_lengkap; $_SESSION['email']=$email; $pesan='Profil berhasil diperbarui.';
  }
  if(($_POST['aksi']??'')==='password'){
    $baru=$_POST['kata_sandi_baru']??'';
    if(strlen($baru)>=6){
      $u=$koneksi->prepare('UPDATE users SET kata_sandi=:kata_sandi WHERE id_user=:id_user');
      $u->execute(['kata_sandi'=>password_hash($baru,PASSWORD_DEFAULT),'id_user'=>$id_user]);
      $pesan='Password berhasil diperbarui.';
    }else{$pesan='Password minimal 6 karakter.';}
  }
  if(($_POST['aksi']??'')==='tema'){
    $_SESSION['tema']=($_POST['tema']??'light')==='dark'?'dark':'light';
    $pesan='Tema diperbarui (sesi saat ini).';
  }
}
$q=$koneksi->prepare('SELECT nama_lengkap,email FROM users WHERE id_user=:id_user');
$q->execute(['id_user'=>$id_user]);
$user=$q->fetch();
$judul_halaman='Pengaturan';
include __DIR__ . '/../partials/header.php'; include __DIR__ . '/../partials/sidebar.php'; include __DIR__ . '/../partials/topbar.php';
?>
<?php if($pesan): ?><div class="alert alert-info"><?= h($pesan) ?></div><?php endif; ?>
<div class="row g-3">
<div class="col-lg-6"><div class="card card-modern p-3"><h6>Ubah Profil</h6><form method="post" class="row g-2"><input type="hidden" name="aksi" value="profil"><div class="col-12"><input name="nama_lengkap" class="form-control" value="<?= h($user['nama_lengkap']) ?>" required></div><div class="col-12"><input name="email" type="email" class="form-control" value="<?= h($user['email']) ?>" required></div><div class="col-12"><button class="btn btn-utama">Simpan</button></div></form></div></div>
<div class="col-lg-6"><div class="card card-modern p-3"><h6>Ubah Password</h6><form method="post" class="row g-2"><input type="hidden" name="aksi" value="password"><div class="col-12"><input name="kata_sandi_baru" type="password" class="form-control" placeholder="Password baru" required></div><div class="col-12"><button class="btn btn-dark">Update Password</button></div></form></div></div>
<div class="col-lg-6"><div class="card card-modern p-3"><h6>Pilihan Tema</h6><form method="post" class="row g-2"><input type="hidden" name="aksi" value="tema"><div class="col-12"><select class="form-select" name="tema"><option value="light">Light</option><option value="dark">Dark</option></select></div><div class="col-12"><button class="btn btn-secondary">Simpan Tema</button></div></form></div></div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>

