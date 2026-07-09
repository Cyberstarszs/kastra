<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();
$id_user = (int) $_SESSION['id_user'];
$rekap = ambil_rekap_keuangan($koneksi, $id_user);
$bulan = (int) ($_GET['bulan'] ?? (int) date('m'));
$tahun = (int) ($_GET['tahun'] ?? (int) date('Y'));
if ($bulan < 1 || $bulan > 12) { $bulan = (int) date('m'); }
if ($tahun < 2000 || $tahun > 2100) { $tahun = (int) date('Y'); }
$periode = sprintf('%04d-%02d', $tahun, $bulan);
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$transaksi_terakhir_query = $koneksi->prepare('SELECT * FROM transaksi WHERE id_user = :id_user ORDER BY tanggal DESC, id_transaksi DESC LIMIT 5');
$transaksi_terakhir_query->execute(['id_user' => $id_user]);
$transaksi_terakhir = $transaksi_terakhir_query->fetchAll();
$grafik_query = $koneksi->prepare("SELECT DAY(tanggal) AS hari,
COALESCE(SUM(CASE WHEN jenis_transaksi='pemasukan' THEN nominal END),0) AS pemasukan,
COALESCE(SUM(CASE WHEN jenis_transaksi='pengeluaran' THEN nominal END),0) AS pengeluaran
FROM transaksi
WHERE id_user = :id_user AND DATE_FORMAT(tanggal, '%Y-%m') = :periode
GROUP BY DAY(tanggal)
ORDER BY DAY(tanggal)");
$grafik_query->execute(['id_user' => $id_user, 'periode' => $periode]);
$data_grafik = $grafik_query->fetchAll();
$peta_harian = [];
foreach ($data_grafik as $baris) {
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
$kategori_query = $koneksi->prepare("SELECT kategori, SUM(nominal) AS total
FROM transaksi
WHERE id_user = :id_user AND jenis_transaksi = 'pengeluaran' AND DATE_FORMAT(tanggal, '%Y-%m') = :periode
GROUP BY kategori
ORDER BY total DESC
LIMIT 6");
$kategori_query->execute(['id_user' => $id_user, 'periode' => $periode]);
$kategori_pengeluaran = $kategori_query->fetchAll();
$label_kategori = [];
$nilai_kategori = [];
foreach ($kategori_pengeluaran as $item) {
    $label_kategori[] = $item['kategori'];
    $nilai_kategori[] = (float) $item['total'];
}
$ada_data_line = array_sum($data_pemasukan) > 0 || array_sum($data_pengeluaran) > 0;
$ada_data_donut = count($nilai_kategori) > 0 && array_sum($nilai_kategori) > 0;
$bulan_opsi = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
$judul_halaman = 'Ringkasan Keuangan';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="row g-3 mb-3 dashboard-summary-row">
  <div class="col-md-6 col-xl-3 dashboard-summary-item"><div class="card-modern kartu-ringkas"><div class="kartu-head"><span class="icon-bulat bg-ungu"><i class="bi bi-wallet2"></i></span></div><div class="kartu-label">Saldo Saat Ini</div><h3 class="kartu-nominal"><?= format_rupiah($rekap['saldo_total']) ?></h3><div class="kartu-tren nilai-hijau"><i class="bi bi-arrow-up-right"></i> Kondisi stabil</div></div></div>
  <div class="col-md-6 col-xl-3 dashboard-summary-item"><div class="card-modern kartu-ringkas"><div class="kartu-head"><span class="icon-bulat bg-hijau"><i class="bi bi-graph-up-arrow"></i></span></div><div class="kartu-label">Total Pemasukan</div><h3 class="kartu-nominal"><?= format_rupiah($rekap['total_pemasukan']) ?></h3><div class="kartu-tren nilai-hijau"><i class="bi bi-arrow-up-right"></i> Arus kas positif</div></div></div>
  <div class="col-md-6 col-xl-3 dashboard-summary-item"><div class="card-modern kartu-ringkas"><div class="kartu-head"><span class="icon-bulat bg-merah"><i class="bi bi-arrow-down"></i></span></div><div class="kartu-label">Total Pengeluaran</div><h3 class="kartu-nominal"><?= format_rupiah($rekap['total_pengeluaran']) ?></h3><div class="kartu-tren nilai-merah"><i class="bi bi-exclamation-circle"></i> Tetap terkontrol</div></div></div>
  <div class="col-md-6 col-xl-3 dashboard-summary-item"><div class="card-modern kartu-ringkas"><div class="kartu-head"><span class="icon-bulat bg-biru"><i class="bi bi-piggy-bank"></i></span></div><div class="kartu-label">Target Tabungan</div><h3 class="kartu-nominal"><?= format_rupiah($rekap['total_tabungan']) ?></h3><div class="kartu-tren nilai-hijau"><i class="bi bi-arrow-up-right"></i> Perkembangan stabil</div></div></div>
</div>
<div class="row g-3 mb-3">
  <div class="col-xl-8">
    <div class="card-modern p-3 h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="section-title mb-0">Ringkasan Keuangan</h5>
        <form method="get" class="d-flex gap-2">
          <select class="form-select form-select-sm" name="bulan" onchange="this.form.submit()">
            <?php foreach ($bulan_opsi as $nomor_bulan => $nama_bulan): ?>
              <option value="<?= $nomor_bulan ?>" <?= $bulan === $nomor_bulan ? 'selected' : '' ?>><?= h($nama_bulan) ?></option>
            <?php endforeach; ?>
          </select>
          <select class="form-select form-select-sm" name="tahun" onchange="this.form.submit()">
            <?php for ($y = (int) date('Y') - 3; $y <= (int) date('Y') + 1; $y++): ?>
              <option value="<?= $y ?>" <?= $tahun === $y ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
          </select>
        </form>
      </div>
      <?php if (!$ada_data_line): ?>
        <div class="text-center py-5 text-muted">Belum ada data untuk ditampilkan</div>
      <?php else: ?>
        <canvas id="chartKeuangan" height="120"></canvas>
      <?php endif; ?>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="card-modern p-3 h-100">
      <h5 class="section-title">Pengeluaran per Kategori</h5>
      <?php if (!$ada_data_donut): ?>
        <div class="text-center py-5 text-muted">Belum ada data untuk ditampilkan</div>
      <?php else: ?>
        <canvas id="chartKategori" height="175"></canvas>
      <?php endif; ?>
    </div>
  </div>
</div>
<div class="card-modern p-3 ai-insight">
  <h5 class="section-title mb-2">Wawasan AI</h5>
  <p class="mb-0"><?php if ($rekap['total_pengeluaran'] > $rekap['total_pemasukan']): ?>Pengeluaran lebih tinggi dari pemasukan. Prioritaskan pengeluaran inti dan kurangi belanja impulsif.<?php else: ?>Arus kas sehat. Pertahankan alokasi tabungan rutin minimal 20% dari pemasukan.<?php endif; ?></p>
</div>
<script>
<?php if ($ada_data_line): ?>
new Chart(document.getElementById('chartKeuangan'), {
  type: 'line',
  data: {
    labels: <?= json_encode($label_hari) ?>,
    datasets: [
      { label: 'Pemasukan', data: <?= json_encode($data_pemasukan) ?>, borderColor: '#4f46e5', backgroundColor: 'rgba(79, 70, 229, .12)', tension: .35, fill: false, borderWidth: 2 },
      { label: 'Pengeluaran', data: <?= json_encode($data_pengeluaran) ?>, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,.12)', tension: .35, fill: false, borderWidth: 2 }
    ]
  },
  options: { responsive: true, plugins: { legend: { position: 'top' } }, interaction: { intersect: false, mode: 'index' } }
});
<?php endif; ?>
<?php if ($ada_data_donut): ?>
new Chart(document.getElementById('chartKategori'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode($label_kategori) ?>,
    datasets: [{
      data: <?= json_encode($nilai_kategori) ?>,
      backgroundColor: ['#4f46e5','#10b981','#f59e0b','#3b82f6','#ef4444','#64748b']
    }]
  },
  options: { cutout: '62%' }
});
<?php endif; ?>
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>
