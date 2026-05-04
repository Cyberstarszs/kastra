<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();

$id_user = (int) $_SESSION['id_user'];
$rekap = ambil_rekap_keuangan($koneksi, $id_user);

$transaksi_terakhir_query = $koneksi->prepare('SELECT * FROM transaksi WHERE id_user = :id_user ORDER BY tanggal DESC, id_transaksi DESC LIMIT 5');
$transaksi_terakhir_query->execute(['id_user' => $id_user]);
$transaksi_terakhir = $transaksi_terakhir_query->fetchAll();

$grafik_query = $koneksi->prepare("SELECT
COALESCE(SUM(CASE WHEN jenis_transaksi='pemasukan' THEN nominal END),0) AS pemasukan,
COALESCE(SUM(CASE WHEN jenis_transaksi='pengeluaran' THEN nominal END),0) AS pengeluaran
FROM transaksi WHERE id_user = :id_user AND DATE_FORMAT(tanggal,'%Y-%m') = DATE_FORMAT(CURDATE(),'%Y-%m')");
$grafik_query->execute(['id_user' => $id_user]);
$grafik = $grafik_query->fetch();

$judul_halaman = 'Dashboard';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card card-modern kartu-ringkas p-3"><small>Saldo Total</small><h5><?= format_rupiah($rekap['saldo_total']) ?></h5></div></div>
  <div class="col-md-3"><div class="card card-modern kartu-ringkas p-3"><small>Total Pemasukan</small><h5 class="nilai-hijau"><?= format_rupiah($rekap['total_pemasukan']) ?></h5></div></div>
  <div class="col-md-3"><div class="card card-modern kartu-ringkas p-3"><small>Total Pengeluaran</small><h5 class="nilai-merah"><?= format_rupiah($rekap['total_pengeluaran']) ?></h5></div></div>
  <div class="col-md-3"><div class="card card-modern kartu-ringkas p-3"><small>Total Tabungan</small><h5><?= format_rupiah($rekap['total_tabungan']) ?></h5></div></div>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card card-modern p-3">
      <h6>Grafik Pemasukan vs Pengeluaran (Bulan Ini)</h6>
      <canvas id="chartKeuangan" height="120"></canvas>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card card-modern p-3">
      <h6>Insight Sederhana</h6>
      <?php if ($rekap['total_pengeluaran'] > $rekap['total_pemasukan']): ?>
        <div class="alert alert-danger mb-0">Pengeluaran lebih besar dari pemasukan. Kurangi pengeluaran non-prioritas.</div>
      <?php else: ?>
        <div class="alert alert-success mb-0">Keuangan cukup sehat. Pertahankan ritme menabungmu.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="card card-modern p-3 mt-3">
  <h6>Transaksi Terakhir</h6>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>Tanggal</th><th>Jenis</th><th>Kategori</th><th>Nominal</th></tr></thead>
      <tbody>
      <?php foreach ($transaksi_terakhir as $item): ?>
        <tr>
          <td><?= h($item['tanggal']) ?></td>
          <td><?= h(ucfirst($item['jenis_transaksi'])) ?></td>
          <td><?= h($item['kategori']) ?></td>
          <td class="<?= $item['jenis_transaksi'] === 'pemasukan' ? 'nilai-hijau' : 'nilai-merah' ?>"><?= format_rupiah((float)$item['nominal']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
new Chart(document.getElementById('chartKeuangan'), {
  type: 'bar',
  data: {
    labels: ['Pemasukan', 'Pengeluaran'],
    datasets: [{
      data: [<?= (float)$grafik['pemasukan'] ?>, <?= (float)$grafik['pengeluaran'] ?>],
      backgroundColor: ['#1faa6f', '#e74c3c']
    }]
  },
  options: { plugins: { legend: { display: false } } }
});
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>

