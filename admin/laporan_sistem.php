<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_admin();
$bulan = (int) ($_GET['bulan'] ?? date('n'));
$tahun = (int) ($_GET['tahun'] ?? date('Y'));
$bulan = max(1, min(12, $bulan));
$tahun = max(2020, min(2100, $tahun));
$q_user_baru = $koneksi->prepare("SELECT COUNT(*) FROM users WHERE role='user'
    AND MONTH(tanggal_daftar)=:bulan AND YEAR(tanggal_daftar)=:tahun");
$q_user_baru->execute(['bulan' => $bulan, 'tahun' => $tahun]);
$user_baru_bulan = (int) $q_user_baru->fetchColumn();
$q_aktif = $koneksi->prepare("SELECT COUNT(DISTINCT id_user) FROM transaksi
    WHERE MONTH(tanggal)=:bulan AND YEAR(tanggal)=:tahun");
$q_aktif->execute(['bulan' => $bulan, 'tahun' => $tahun]);
$user_aktif = (int) $q_aktif->fetchColumn();
$q_count = $koneksi->prepare("SELECT COUNT(*) FROM transaksi
    WHERE MONTH(tanggal)=:bulan AND YEAR(tanggal)=:tahun");
$q_count->execute(['bulan' => $bulan, 'tahun' => $tahun]);
$jumlah_catatan = (int) $q_count->fetchColumn();
$q_pendapatan = $koneksi->prepare("SELECT COALESCE(SUM(jumlah),0) FROM transaksi_premium
    WHERE status='berhasil'
    AND MONTH(waktu_bayar)=:bulan AND YEAR(waktu_bayar)=:tahun");
$q_pendapatan->execute(['bulan' => $bulan, 'tahun' => $tahun]);
$pendapatan_platform = (float) $q_pendapatan->fetchColumn();
$q_ai = $koneksi->prepare("SELECT COUNT(*) FROM chat_ai
    WHERE MONTH(waktu)=:bulan AND YEAR(waktu)=:tahun");
$q_ai->execute(['bulan' => $bulan, 'tahun' => $tahun]);
$jumlah_ai = (int) $q_ai->fetchColumn();
$q_premium_baru = $koneksi->prepare("SELECT COUNT(*) FROM transaksi_premium
    WHERE status='berhasil' AND MONTH(waktu_bayar)=:bulan AND YEAR(waktu_bayar)=:tahun");
$q_premium_baru->execute(['bulan' => $bulan, 'tahun' => $tahun]);
$premium_baru = (int) $q_premium_baru->fetchColumn();
$bulan_opsi = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
$judul_halaman = 'Laporan Sistem';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h2 class="mb-1" style="font-size:24px;">Laporan Sistem</h2>
    <p class="text-muted mb-0">Statistik operasional platform — <?= h($bulan_opsi[$bulan]) ?> <?= $tahun ?>.</p>
  </div>
</div>
<div class="card-modern p-3 mb-4">
  <form class="row g-2 align-items-end" method="get">
    <div class="col-md-4">
      <label class="form-label fw-semibold" style="font-size:13px;">Bulan</label>
      <select name="bulan" class="form-select">
        <?php foreach ($bulan_opsi as $val => $label_bulan): ?>
          <option value="<?= $val ?>" <?= $bulan===$val?'selected':'' ?>><?= h($label_bulan) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold" style="font-size:13px;">Tahun</label>
      <input type="number" name="tahun" class="form-control" min="2020" max="2100" value="<?= $tahun ?>">
    </div>
    <div class="col-md-4">
      <button class="btn btn-utama w-100">Terapkan Filter</button>
    </div>
  </form>
</div>
<div class="alert d-flex align-items-center gap-2 mb-4" style="background:#fffbeb; border:1px solid #fcd34d; border-radius:10px; font-size:13px; color:#92400e;">
  <i class="bi bi-shield-lock-fill" style="font-size:18px; color:#d97706; flex-shrink:0;"></i>
  <div>
    <strong>Privasi Data Dijaga.</strong>
    Laporan ini hanya menampilkan statistik operasional platform.
    Nominal transaksi, saldo, dan data keuangan pribadi pengguna tidak dapat diakses oleh admin.
  </div>
</div>
<div class="row g-3 mb-4">
  <div class="col-md-6 col-xl-4">
    <div class="card-modern kartu-ringkas">
      <div class="kartu-label">Pengguna Baru</div>
      <h3 class="kartu-nominal"><?= $user_baru_bulan ?></h3>
      <div class="text-muted" style="font-size:12px;">Pendaftar bulan ini</div>
    </div>
  </div>
  <div class="col-md-6 col-xl-4">
    <div class="card-modern kartu-ringkas">
      <div class="kartu-label">Pengguna Aktif</div>
      <h3 class="kartu-nominal" style="color:#22c55e;"><?= $user_aktif ?></h3>
      <div class="text-muted" style="font-size:12px;">Mencatat transaksi bulan ini</div>
    </div>
  </div>
  <div class="col-md-6 col-xl-4">
    <div class="card-modern kartu-ringkas">
      <div class="kartu-label">Jumlah Catatan</div>
      <h3 class="kartu-nominal" style="color:#3b82f6;"><?= number_format($jumlah_catatan) ?></h3>
      <div class="text-muted" style="font-size:12px;">Entri transaksi tercatat</div>
    </div>
  </div>
  <div class="col-md-6 col-xl-4">
    <div class="card-modern kartu-ringkas">
      <div class="kartu-label">Pendapatan Platform</div>
      <h3 class="kartu-nominal" style="color:#4f46e5;"><?= h(format_rupiah($pendapatan_platform)) ?></h3>
      <div class="text-muted" style="font-size:12px;">Dari langganan premium bulan ini</div>
    </div>
  </div>
  <div class="col-md-6 col-xl-4">
    <div class="card-modern kartu-ringkas">
      <div class="kartu-label">Langganan Premium Baru</div>
      <h3 class="kartu-nominal" style="color:#f59e0b;"><?= $premium_baru ?></h3>
      <div class="text-muted" style="font-size:12px;">Berhasil berlangganan</div>
    </div>
  </div>
  <div class="col-md-6 col-xl-4">
    <div class="card-modern kartu-ringkas">
      <div class="kartu-label">Request AI</div>
      <h3 class="kartu-nominal" style="color:#8b5cf6;"><?= number_format($jumlah_ai) ?></h3>
      <div class="text-muted" style="font-size:12px;">Pertanyaan ke AI bulan ini</div>
    </div>
  </div>
</div>
<div class="card-modern p-3" style="border-left:4px solid #4f46e5;">
  <div class="fw-semibold mb-1" style="font-size:13px;"><i class="bi bi-info-circle me-1 text-primary"></i>Catatan Kebijakan Privasi Admin</div>
  <ul class="mb-0 text-muted" style="font-size:12px; padding-left:18px; line-height:2;">
    <li>Admin <strong>tidak dapat</strong> melihat nominal transaksi, saldo, atau detail keuangan pengguna.</li>
    <li>Admin <strong>tidak dapat</strong> melihat deskripsi atau kategori transaksi milik pengguna.</li>
    <li>Laporan hanya mencakup metrik <strong>operasional platform</strong> (jumlah catatan, user aktif, pendapatan platform).</li>
    <li>Data keuangan pengguna hanya dapat diakses oleh pengguna yang bersangkutan.</li>
  </ul>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
