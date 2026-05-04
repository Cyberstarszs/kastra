<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();
$id_user=(int)$_SESSION['id_user'];
$id=(int)($_GET['id']??0);
if($id>0){$h=$koneksi->prepare('DELETE FROM kategori WHERE id_kategori=:id AND id_user=:id_user');$h->execute(['id'=>$id,'id_user'=>$id_user]);}
header('Location: ' . url('user/kategori.php')); exit;

