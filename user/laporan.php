<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();
$id_user = (int) $_SESSION['id_user'];
$bulan = (int) ($_GET['bulan'] ?? (int) date('m'));
$tahun = (int) ($_GET['tahun'] ?? (int) date('Y'));
if ($bulan < 1 || $bulan > 12) { $bulan = (int) date('m'); }
if ($tahun < 2000 || $tahun > 2100) { $tahun = (int) date('Y'); }
$periode = sprintf('%04d-%02d', $tahun, $bulan);
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$query_ringkasan = $koneksi->prepare("SELECT
COALESCE(SUM(CASE WHEN jenis_transaksi='pemasukan' THEN nominal END),0) AS total_pemasukan,
COALESCE(SUM(CASE WHEN jenis_transaksi='pengeluaran' THEN nominal END),0) AS total_pengeluaran,
COALESCE(SUM(CASE WHEN jenis_transaksi='pengeluaran' THEN 1 ELSE 0 END),0) AS jumlah_transaksi_pengeluaran
FROM transaksi
WHERE id_user = :id_user AND DATE_FORMAT(tanggal, '%Y-%m') = :periode");
$query_ringkasan->execute(['id_user' => $id_user, 'periode' => $periode]);
$hasil_ringkasan = $query_ringkasan->fetch();
$total_pemasukan = (float) $hasil_ringkasan['total_pemasukan'];
$total_pengeluaran = (float) $hasil_ringkasan['total_pengeluaran'];
$jumlah_transaksi_pengeluaran = (int) $hasil_ringkasan['jumlah_transaksi_pengeluaran'];
$saldo_bersih = $total_pemasukan - $total_pengeluaran;
$rata_pengeluaran = $jumlah_transaksi_pengeluaran > 0 ? $total_pengeluaran / $jumlah_transaksi_pengeluaran : 0;
$query_harian = $koneksi->prepare("SELECT DAY(tanggal) AS hari,
COALESCE(SUM(CASE WHEN jenis_transaksi='pemasukan' THEN nominal END),0) AS pemasukan,
COALESCE(SUM(CASE WHEN jenis_transaksi='pengeluaran' THEN nominal END),0) AS pengeluaran
FROM transaksi
WHERE id_user = :id_user AND DATE_FORMAT(tanggal, '%Y-%m') = :periode
GROUP BY DAY(tanggal)
ORDER BY DAY(tanggal)");
$query_harian->execute(['id_user' => $id_user, 'periode' => $periode]);
$data_harian_db = $query_harian->fetchAll();
$peta_harian = [];
foreach ($data_harian_db as $baris) {
    $peta_harian[(int) $baris['hari']] = [
        'pemasukan' => (float) $baris['pemasukan'],
        'pengeluaran' => (float) $baris['pengeluaran'],
    ];
}
$label_hari = [];
$data_pemasukan = [];
$data_pengeluaran = [];
for ($i = 1; $i <= $jumlah_hari; $i++) {
    $label_hari[] = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
    $data_pemasukan[] = $peta_harian[$i]['pemasukan'] ?? 0;
    $data_pengeluaran[] = $peta_harian[$i]['pengeluaran'] ?? 0;
}
$query_kategori = $koneksi->prepare("SELECT kategori, SUM(nominal) AS total
FROM transaksi
WHERE id_user = :id_user AND jenis_transaksi='pengeluaran' AND DATE_FORMAT(tanggal, '%Y-%m') = :periode
GROUP BY kategori
ORDER BY total DESC");
$query_kategori->execute(['id_user' => $id_user, 'periode' => $periode]);
$laporan_kategori = $query_kategori->fetchAll();
$label_kategori = [];
$nilai_kategori = [];
foreach ($laporan_kategori as $kategori) {
    $label_kategori[] = $kategori['kategori'];
    $nilai_kategori[] = (float) $kategori['total'];
}
$ada_data_donut = count($nilai_kategori) > 0 && array_sum($nilai_kategori) > 0;
$kategori_terbesar = '-';
$persentase_kategori_terbesar = 0;
if (count($laporan_kategori) > 0 && $total_pengeluaran > 0) {
    $kategori_terbesar = $laporan_kategori[0]['kategori'];
    $persentase_kategori_terbesar = ((float) $laporan_kategori[0]['total'] / $total_pengeluaran) * 100;
}
$insight = [];
if ($total_pemasukan == 0 && $total_pengeluaran == 0) {
    $insight[] = 'Belum ada aktivitas transaksi di periode ini.';
} else {
    if ($total_pengeluaran > $total_pemasukan) {
        $insight[] = 'Pengeluaran bulan ini lebih tinggi dari pemasukan.';
    } else {
        $insight[] = 'Pemasukan bulan ini masih lebih tinggi dari pengeluaran.';
    }
    if ($kategori_terbesar !== '-') {
        $insight[] = 'Kategori terbesar: ' . $kategori_terbesar . '.';
        $insight[] = 'Pengeluaran pada kategori ini mencapai ' . number_format($persentase_kategori_terbesar, 1) . '%.';
    }
}
$bulan_opsi = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
$judul_halaman = 'Laporan Keuangan';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-3 laporan-header-mobile">
  <div>
    <h2 class="mb-1" style="font-size:24px;">Laporan Keuangan</h2>
    <p class="text-muted mb-0">Analisis pemasukan dan pengeluaran Anda.</p>
  </div>
  <div class="d-flex gap-2 laporan-filter-wrap">
    <form method="get" class="d-flex gap-2 laporan-filter-form">
      <select class="form-select" name="bulan" onchange="this.form.submit()">
        <?php foreach ($bulan_opsi as $nomor_bulan => $nama_bulan): ?>
          <option value="<?= $nomor_bulan ?>" <?= $bulan === $nomor_bulan ? 'selected' : '' ?>><?= h($nama_bulan) ?></option>
        <?php endforeach; ?>
      </select>
      <select class="form-select" name="tahun" onchange="this.form.submit()">
        <?php for ($y = (int) date('Y') - 3; $y <= (int) date('Y') + 1; $y++): ?>
          <option value="<?= $y ?>" <?= $tahun === $y ? 'selected' : '' ?>><?= $y ?></option>
        <?php endfor; ?>
      </select>
    </form>
    <button class="btn btn-light laporan-export-btn" onclick="window.print()"><i class="bi bi-download me-1"></i>Export</button>
  </div>
</div>
<?php if ($total_pemasukan == 0 && $total_pengeluaran == 0): ?>
  <div class="card-modern p-5 text-center">
    <div class="icon-btn mx-auto mb-3"><i class="bi bi-bar-chart"></i></div>
    <h5>Belum ada data laporan</h5>
    <p class="text-muted">Tambahkan transaksi untuk melihat analisis keuangan.</p>
    <a href="<?= url('user/transaksi.php') ?>" class="btn btn-utama">Tambah Transaksi</a>
  </div>
<?php else: ?>
<div class="row g-3 mb-3">
  <div class="col-md-6 col-xl-3"><div class="card-modern kartu-ringkas"><div class="kartu-head"><span class="icon-bulat bg-ungu"><i class="bi bi-graph-up"></i></span></div><div class="kartu-label">Total Pemasukan</div><h3 class="kartu-nominal"><?= format_rupiah($total_pemasukan) ?></h3></div></div>
  <div class="col-md-6 col-xl-3"><div class="card-modern kartu-ringkas"><div class="kartu-head"><span class="icon-bulat bg-merah"><i class="bi bi-graph-down"></i></span></div><div class="kartu-label">Total Pengeluaran</div><h3 class="kartu-nominal"><?= format_rupiah($total_pengeluaran) ?></h3></div></div>
  <div class="col-md-6 col-xl-3"><div class="card-modern kartu-ringkas"><div class="kartu-head"><span class="icon-bulat bg-biru"><i class="bi bi-wallet2"></i></span></div><div class="kartu-label">Saldo Bersih</div><h3 class="kartu-nominal <?= $saldo_bersih >= 0 ? 'nilai-hijau' : 'nilai-merah' ?>"><?= format_rupiah($saldo_bersih) ?></h3></div></div>
  <div class="col-md-6 col-xl-3"><div class="card-modern kartu-ringkas"><div class="kartu-head"><span class="icon-bulat bg-hijau"><i class="bi bi-calculator"></i></span></div><div class="kartu-label">Rata-rata Pengeluaran</div><h3 class="kartu-nominal"><?= format_rupiah($rata_pengeluaran) ?></h3></div></div>
</div>
<div class="row g-3 mb-3">
  <div class="col-xl-8">
    <div class="card-modern p-3 h-100">
      <h5 class="section-title">Tren Pemasukan vs Pengeluaran (Harian)</h5>
      <canvas id="chartLineLaporan" height="130"></canvas>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="card-modern p-3 h-100">
      <h5 class="section-title">Distribusi Pengeluaran per Kategori</h5>
      <?php if (!$ada_data_donut): ?>
        <div class="text-center py-5 text-muted">Belum ada data untuk ditampilkan</div>
      <?php else: ?>
        <canvas id="chartDonutKategori" height="220"></canvas>
      <?php endif; ?>
    </div>
  </div>
</div>
<div class="row g-3">
  <div class="col-xl-8">
    <div class="card-modern p-3">
      <h5 class="section-title">Ringkasan Kategori</h5>
      <div class="table-responsive">
        <table class="table table-clean align-middle mb-0">
          <thead><tr><th>Kategori</th><th class="text-end">Total Pengeluaran</th><th class="text-end">Persentase</th></tr></thead>
          <tbody>
            <?php foreach ($laporan_kategori as $kategori): ?>
              <?php $persen = $total_pengeluaran > 0 ? ((float) $kategori['total'] / $total_pengeluaran) * 100 : 0; ?>
              <tr>
                <td><?= h($kategori['kategori']) ?></td>
                <td class="text-end"><?= format_rupiah((float) $kategori['total']) ?></td>
                <td class="text-end"><?= number_format($persen, 1) ?>%</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="card-modern p-3 ai-insight">
      <h5 class="section-title"><i class="bi bi-lightbulb me-2"></i>Wawasan Keuangan</h5>
      <ul class="mb-0 ps-3">
        <?php foreach ($insight as $teks_insight): ?>
          <li class="mb-2"><?= h($teks_insight) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>
<script>
new Chart(document.getElementById('chartLineLaporan'), {
  type: 'line',
  data: {
    labels: <?= json_encode($label_hari) ?>,
    datasets: [
      {
        label: 'Pemasukan',
        data: <?= json_encode($data_pemasukan) ?>,
        borderColor: '#4f46e5',
        backgroundColor: 'rgba(79, 70, 229, .12)',
        tension: 0.35,
        fill: true
      },
      {
        label: 'Pengeluaran',
        data: <?= json_encode($data_pengeluaran) ?>,
        borderColor: '#ef4444',
        backgroundColor: 'rgba(239,68,68,.12)',
        tension: 0.35,
        fill: true
      }
    ]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'top' } },
    interaction: { intersect: false, mode: 'index' }
  }
});
<?php if ($ada_data_donut): ?>
new Chart(document.getElementById('chartDonutKategori'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode($label_kategori) ?>,
    datasets: [{
      data: <?= json_encode($nilai_kategori) ?>,
      backgroundColor: ['#4f46e5', '#10b981', '#ef4444', '#f59e0b', '#3b82f6', '#ec4899', '#14b8a6']
    }]
  },
  options: {
    cutout: '62%',
    plugins: { legend: { position: 'right' } }
  }
});
<?php endif; ?>
</script>
<?php endif; ?>
<?php include __DIR__ . '/../partials/footer.php'; ?>
