<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();
$id_user=(int)$_SESSION['id_user'];
$bulan=$_GET['bulan']??date('Y-m');

$q1=$koneksi->prepare("SELECT DATE_FORMAT(tanggal,'%Y-%m') AS bulan,
SUM(CASE WHEN jenis_transaksi='pemasukan' THEN nominal ELSE 0 END) AS pemasukan,
SUM(CASE WHEN jenis_transaksi='pengeluaran' THEN nominal ELSE 0 END) AS pengeluaran
FROM transaksi WHERE id_user=:id_user AND DATE_FORMAT(tanggal,'%Y-%m')=:bulan GROUP BY DATE_FORMAT(tanggal,'%Y-%m')");
$q1->execute(['id_user'=>$id_user,'bulan'=>$bulan]);
$rekap=$q1->fetch()?:['pemasukan'=>0,'pengeluaran'=>0];

$q2=$koneksi->prepare("SELECT kategori, SUM(nominal) AS total FROM transaksi WHERE id_user=:id_user AND jenis_transaksi='pengeluaran' AND DATE_FORMAT(tanggal,'%Y-%m')=:bulan GROUP BY kategori ORDER BY total DESC");
$q2->execute(['id_user'=>$id_user,'bulan'=>$bulan]);
$kategori_pengeluaran=$q2->fetchAll();
$label_kategori=[]; $nilai_kategori=[];
foreach($kategori_pengeluaran as $k){$label_kategori[]=$k['kategori'];$nilai_kategori[]=(float)$k['total'];}

$judul_halaman='Laporan';
include __DIR__ . '/../partials/header.php'; include __DIR__ . '/../partials/sidebar.php'; include __DIR__ . '/../partials/topbar.php';
?>
<div class="card card-modern p-3 mb-3">
<form class="row g-2" method="get"><div class="col-md-3"><input type="month" name="bulan" value="<?= h($bulan) ?>" class="form-control"></div><div class="col-md-3"><button class="btn btn-dark">Terapkan</button></div></form>
</div>
<div class="row g-3">
<div class="col-md-6"><div class="card card-modern p-3"><h6>Total Pemasukan</h6><h4 class="nilai-hijau"><?= format_rupiah((float)$rekap['pemasukan']) ?></h4></div></div>
<div class="col-md-6"><div class="card card-modern p-3"><h6>Total Pengeluaran</h6><h4 class="nilai-merah"><?= format_rupiah((float)$rekap['pengeluaran']) ?></h4></div></div>
</div>
<div class="card card-modern p-3 mt-3"><h6>Grafik Kategori Pengeluaran</h6><canvas id="chartKategori" height="110"></canvas></div>
<script>
new Chart(document.getElementById('chartKategori'), {type:'doughnut',data:{labels:<?= json_encode($label_kategori) ?>,datasets:[{data:<?= json_encode($nilai_kategori) ?>}]}});
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>

