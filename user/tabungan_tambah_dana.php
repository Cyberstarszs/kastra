<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();

$id_user = (int) $_SESSION['id_user'];
$id_tabungan = (int) ($_POST['id_tabungan'] ?? 0);
$nominal_tambahan = (float) ($_POST['nominal_tambahan'] ?? 0);

if ($id_tabungan > 0 && $nominal_tambahan > 0) {
    $update = $koneksi->prepare('UPDATE tabungan SET nominal_terkumpul = nominal_terkumpul + :nominal WHERE id_tabungan=:id_tabungan AND id_user=:id_user');
    $update->execute(['nominal'=>$nominal_tambahan, 'id_tabungan'=>$id_tabungan, 'id_user'=>$id_user]);
}
header('Location: ' . url('user/tabungan.php'));
exit;

