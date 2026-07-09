<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_admin();
$query_user = $koneksi->query("SELECT COUNT(*) FROM users WHERE role='user'");
$total_user = (int) $query_user->fetchColumn();
$query_premium = $koneksi->query("SELECT COUNT(*) FROM users WHERE status_user='premium' AND role='user'");
$user_premium = (int) $query_premium->fetchColumn();
$query_aktif = $koneksi->query("SELECT COUNT(DISTINCT id_user) FROM transaksi WHERE MONTH(tanggal)=MONTH(CURDATE()) AND YEAR(tanggal)=YEAR(CURDATE())");
$user_aktif_bulan_ini = (int) $query_aktif->fetchColumn();
$query_pendapatan = $koneksi->query("SELECT COALESCE(SUM(jumlah),0) FROM transaksi_premium WHERE status='berhasil'");
$total_pendapatan_platform = (float) $query_pendapatan->fetchColumn();
$query_trx_count = $koneksi->query("SELECT COUNT(*) FROM transaksi");
$total_transaksi_count = (int) $query_trx_count->fetchColumn();
$query_ai = $koneksi->query("SELECT DATE(waktu) AS tanggal, COUNT(*) AS total FROM chat_ai GROUP BY DATE(waktu) ORDER BY tanggal DESC LIMIT 7");
$data_ai = array_reverse($query_ai->fetchAll());
$label_ai = [];
$grafik_ai = [];
foreach ($data_ai as $baris) {
    $label_ai[] = date('d M', strtotime($baris['tanggal']));
    $grafik_ai[] = (int) $baris['total'];
}
$query_daftar = $koneksi->query("SELECT DATE_FORMAT(tanggal_daftar, '%Y-%m') AS bulan, COUNT(*) AS total
    FROM users WHERE role='user' GROUP BY DATE_FORMAT(tanggal_daftar, '%Y-%m') ORDER BY bulan DESC LIMIT 6");
$data_daftar = array_reverse($query_daftar->fetchAll());
$label_daftar = [];
$grafik_daftar = [];
foreach ($data_daftar as $baris) {
    $label_daftar[] = $baris['bulan'];
    $grafik_daftar[] = (int) $baris['total'];
}
$judul_halaman = 'Dashboard Admin';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="d-flex justify-content-between align-items-end mb-4">
  <div>
    <h2 class="mb-1" style="font-size:24px;">Dashboard Admin</h2>
    <p class="text-muted mb-0">Ringkasan performa operasional sistem Kastra.</p>
  </div>
</div>
<div class="row g-3 mb-4">
  <div class="col-md-6 col-xl-3">
    <div class="card-modern kartu-ringkas">
      <div class="kartu-label">Total Pengguna</div>
      <h3 class="kartu-nominal"><?= $total_user ?></h3>
      <div class="text-muted" style="font-size:12px;">Akun terdaftar</div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card-modern kartu-ringkas">
      <div class="kartu-label">Pengguna Premium</div>
      <h3 class="kartu-nominal" style="color:#f59e0b;"><?= $user_premium ?></h3>
      <div class="text-muted" style="font-size:12px;"><?= $total_user > 0 ? round($user_premium/$total_user*100) : 0 ?>% dari total</div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card-modern kartu-ringkas">
      <div class="kartu-label">Pengguna Aktif Bulan Ini</div>
      <h3 class="kartu-nominal" style="color:#22c55e;"><?= $user_aktif_bulan_ini ?></h3>
      <div class="text-muted" style="font-size:12px;">Mencatat transaksi</div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card-modern kartu-ringkas">
      <div class="kartu-label">Pendapatan Platform</div>
      <h3 class="kartu-nominal" style="color:#4f46e5;"><?= h(format_rupiah($total_pendapatan_platform)) ?></h3>
      <div class="text-muted" style="font-size:12px;">Dari langganan premium</div>
    </div>
  </div>
</div>
<div class="alert d-flex align-items-center gap-2 mb-4" style="background:#fffbeb; border:1px solid #fcd34d; border-radius:10px; font-size:13px; color:#92400e;">
  <i class="bi bi-shield-lock-fill" style="font-size:18px; color:#d97706; flex-shrink:0;"></i>
  <div>
    <strong>Data Keuangan Pengguna Dilindungi.</strong>
    Data transaksi, nominal, deskripsi, dan saldo pribadi pengguna bersifat rahasia dan tidak ditampilkan di panel admin sesuai kebijakan privasi Kastra.
    Admin hanya dapat melihat metrik operasional platform.
  </div>
</div>
<div class="row g-3">
  <div class="col-xl-7">
    <div class="card-modern p-3">
      <h6 class="mb-3">Pertumbuhan Pengguna (6 Bulan)</h6>
      <canvas id="grafikDaftar" height="120"></canvas>
    </div>
  </div>
  <div class="col-xl-5">
    <div class="card-modern p-3">
      <h6 class="mb-3">Penggunaan AI (7 Hari Terakhir)</h6>
      <canvas id="grafikAi" height="120"></canvas>
    </div>
  </div>
</div>
<div class="card-modern p-3 mt-3">
  <div class="d-flex align-items-center justify-content-between">
    <div>
      <div class="text-muted" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px;">Total Entri Transaksi (Jumlah Catatan)</div>
      <div class="fw-bold" style="font-size:22px;"><?= number_format($total_transaksi_count) ?> <span class="text-muted fw-normal" style="font-size:14px;">catatan</span></div>
    </div>
    <i class="bi bi-file-earmark-text" style="font-size:2.5rem; color:#e0e0f0;"></i>
  </div>
  <div class="text-muted mt-1" style="font-size:11px;">
    <i class="bi bi-info-circle me-1"></i>
    Hanya jumlah catatan yang ditampilkan. Nilai nominal, kategori, dan deskripsi transaksi pengguna tidak dapat diakses admin.
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('grafikDaftar'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($label_daftar) ?>,
    datasets: [{
      label: 'Pengguna Baru',
      data: <?= json_encode($grafik_daftar) ?>,
      backgroundColor: '#4f46e5',
      borderRadius: 8
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
  }
});
new Chart(document.getElementById('grafikAi'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($label_ai) ?>,
    datasets: [{
      label: 'Request AI',
      data: <?= json_encode($grafik_ai) ?>,
      backgroundColor: '#8b5cf6',
      borderRadius: 8
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
  }
});
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>
