<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();

$id_transaksi = (int) ($_GET['id'] ?? 0);
$id_user = (int) $_SESSION['id_user'];
if ($id_transaksi > 0) {
    $hapus = $koneksi->prepare('DELETE FROM transaksi WHERE id_transaksi = :id AND id_user = :id_user');
    $hapus->execute(['id' => $id_transaksi, 'id_user' => $id_user]);
}
header('Location: ' . url('user/transaksi.php'));
exit;

