<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_admin();
$id_admin = (int) $_SESSION['id_user'];
$KATEGORI_DEFAULT_SISTEM = [
    ['nama_kategori' => 'Gaji',             'jenis' => 'pemasukan',   'icon' => 'bi-cash-stack'],
    ['nama_kategori' => 'Freelance',        'jenis' => 'pemasukan',   'icon' => 'bi-laptop'],
    ['nama_kategori' => 'Investasi',        'jenis' => 'pemasukan',   'icon' => 'bi-graph-up-arrow'],
    ['nama_kategori' => 'Bonus',            'jenis' => 'pemasukan',   'icon' => 'bi-gift'],
    ['nama_kategori' => 'Usaha',            'jenis' => 'pemasukan',   'icon' => 'bi-shop'],
    ['nama_kategori' => 'Lain-lain Masuk',  'jenis' => 'pemasukan',   'icon' => 'bi-plus-circle'],
    ['nama_kategori' => 'Makanan & Minuman','jenis' => 'pengeluaran', 'icon' => 'bi-cup-hot'],
    ['nama_kategori' => 'Transport',        'jenis' => 'pengeluaran', 'icon' => 'bi-bus-front'],
    ['nama_kategori' => 'Belanja',          'jenis' => 'pengeluaran', 'icon' => 'bi-bag'],
    ['nama_kategori' => 'Kesehatan',        'jenis' => 'pengeluaran', 'icon' => 'bi-heart-pulse'],
    ['nama_kategori' => 'Hiburan',          'jenis' => 'pengeluaran', 'icon' => 'bi-controller'],
    ['nama_kategori' => 'Rumah',            'jenis' => 'pengeluaran', 'icon' => 'bi-house-door'],
    ['nama_kategori' => 'Pendidikan',       'jenis' => 'pengeluaran', 'icon' => 'bi-book'],
    ['nama_kategori' => 'Tagihan',          'jenis' => 'pengeluaran', 'icon' => 'bi-receipt'],
    ['nama_kategori' => 'Lain-lain Keluar', 'jenis' => 'pengeluaran', 'icon' => 'bi-dash-circle'],
];
$nama_default_set = array_column($KATEGORI_DEFAULT_SISTEM, 'nama_kategori');
$pesan = '';
$tipe_pesan = 'success';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi          = $_POST['aksi'] ?? '';
    $id_kategori   = (int) ($_POST['id_kategori'] ?? 0);
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    $jenis         = in_array($_POST['jenis'] ?? '', ['pemasukan','pengeluaran']) ? $_POST['jenis'] : 'pengeluaran';
    if ($aksi === 'tambah' && $nama_kategori !== '') {
        if (in_array($nama_kategori, $nama_default_set, true)) {
            $pesan = 'Nama tersebut sudah ada di kategori bawaan sistem.';
            $tipe_pesan = 'danger';
        } else {
            $cek = $koneksi->prepare('SELECT id_kategori FROM kategori WHERE id_user=:id_user AND nama_kategori=:nama_kategori LIMIT 1');
            $cek->execute(['id_user' => $id_admin, 'nama_kategori' => $nama_kategori]);
            if ($cek->fetch()) {
                $pesan = 'Kategori dengan nama tersebut sudah ada.';
                $tipe_pesan = 'danger';
            } else {
                try {
                    $q = $koneksi->prepare('INSERT INTO kategori (id_user,nama_kategori,jenis,is_default) VALUES (:id_user,:nama_kategori,:jenis,0)');
                    $q->execute(['id_user' => $id_admin, 'nama_kategori' => $nama_kategori, 'jenis' => $jenis]);
                } catch (Throwable $e) {
                    $q = $koneksi->prepare('INSERT INTO kategori (id_user,nama_kategori,jenis) VALUES (:id_user,:nama_kategori,:jenis)');
                    $q->execute(['id_user' => $id_admin, 'nama_kategori' => $nama_kategori, 'jenis' => $jenis]);
                }
                header('Location: ' . url('admin/kategori_global.php?sukses=1'));
                exit;
            }
        }
    } elseif ($aksi === 'hapus' && $id_kategori > 0) {
        try {
            $q = $koneksi->prepare('DELETE FROM kategori WHERE id_kategori=:id_kategori AND id_user=:id_user AND is_default=0');
            $q->execute(['id_kategori' => $id_kategori, 'id_user' => $id_admin]);
        } catch (Throwable $e) {
            $q = $koneksi->prepare('DELETE FROM kategori WHERE id_kategori=:id_kategori AND id_user=:id_user');
            $q->execute(['id_kategori' => $id_kategori, 'id_user' => $id_admin]);
        }
        header('Location: ' . url('admin/kategori_global.php?hapus=1'));
        exit;
    }
}
try {
    $q_admin = $koneksi->prepare('SELECT * FROM kategori WHERE id_user=:id_user AND is_default=0 ORDER BY jenis ASC, nama_kategori ASC');
    $q_admin->execute(['id_user' => $id_admin]);
} catch (Throwable $e) {
    $q_admin = $koneksi->prepare('SELECT * FROM kategori WHERE id_user=:id_user ORDER BY jenis ASC, nama_kategori ASC');
    $q_admin->execute(['id_user' => $id_admin]);
}
$data_kategori_admin = $q_admin->fetchAll();
$warna_default = ['#4f46e5','#22c55e','#ef4444','#f59e0b','#3b82f6','#ec4899','#14b8a6','#8b5cf6','#f97316','#06b6d4'];
$judul_halaman = 'Kategori Global';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-3">
  <div>
    <h2 class="mb-1" style="font-size:24px;">Kategori Global</h2>
    <p class="text-muted mb-0">Kelola kategori default sistem yang tersedia untuk semua pengguna.</p>
  </div>
  <button class="btn btn-utama" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
    <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
  </button>
</div>
<?php if ($pesan): ?>
<div class="alert alert-<?= h($tipe_pesan) ?> border-0 d-flex align-items-center gap-2">
  <i class="bi bi-<?= $tipe_pesan === 'danger' ? 'exclamation-circle' : 'check-circle' ?>-fill"></i>
  <?= h($pesan) ?>
</div>
<?php endif; ?>
<?php if (isset($_GET['sukses'])): ?>
<div class="alert alert-success border-0"><i class="bi bi-check-circle-fill me-1"></i>Kategori berhasil ditambahkan.</div>
<?php endif; ?>
<?php if (isset($_GET['hapus'])): ?>
<div class="alert alert-warning border-0"><i class="bi bi-trash-fill me-1"></i>Kategori berhasil dihapus.</div>
<?php endif; ?>
<div class="card-modern p-4 mb-4">
  <div class="d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-shield-fill-check text-primary" style="font-size:18px;"></i>
    <div>
      <h6 class="mb-0 fw-bold">Kategori Bawaan Sistem</h6>
      <div class="text-muted" style="font-size:12px;">
        <?= count($KATEGORI_DEFAULT_SISTEM) ?> kategori otomatis tersedia untuk semua pengguna baru. Tidak dapat diubah atau dihapus.
      </div>
    </div>
    <span class="badge ms-auto" style="background:#4f46e5; font-size:11px;"><?= count($KATEGORI_DEFAULT_SISTEM) ?> Kategori</span>
  </div>
  <div class="mb-3">
    <div class="d-flex align-items-center gap-2 mb-2">
      <span class="badge" style="background:#22c55e22; color:#16a34a; font-size:11px; border:1px solid #22c55e44;">
        ▲ PEMASUKAN
      </span>
    </div>
    <div class="row g-2">
      <?php foreach ($KATEGORI_DEFAULT_SISTEM as $idx => $kat):
        if ($kat['jenis'] !== 'pemasukan') continue;
        $w = $warna_default[$idx % count($warna_default)];
      ?>
      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="d-flex align-items-center gap-2 p-2 rounded-3"
             style="background:<?= h($w) ?>12; border:1px solid <?= h($w) ?>30;">
          <span style="width:30px;height:30px;border-radius:8px;background:<?= h($w) ?>22;
                        color:<?= h($w) ?>;display:flex;align-items:center;justify-content:center;
                        font-size:13px;flex-shrink:0;">
            <i class="bi <?= h($kat['icon']) ?>"></i>
          </span>
          <div style="font-size:12px;font-weight:600;line-height:1.2;"><?= h($kat['nama_kategori']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div>
    <div class="d-flex align-items-center gap-2 mb-2">
      <span class="badge" style="background:#ef444422; color:#dc2626; font-size:11px; border:1px solid #ef444444;">
        ▼ PENGELUARAN
      </span>
    </div>
    <div class="row g-2">
      <?php foreach ($KATEGORI_DEFAULT_SISTEM as $idx => $kat):
        if ($kat['jenis'] !== 'pengeluaran') continue;
        $w = $warna_default[($idx + 3) % count($warna_default)];
      ?>
      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="d-flex align-items-center gap-2 p-2 rounded-3"
             style="background:<?= h($w) ?>12; border:1px solid <?= h($w) ?>30;">
          <span style="width:30px;height:30px;border-radius:8px;background:<?= h($w) ?>22;
                        color:<?= h($w) ?>;display:flex;align-items:center;justify-content:center;
                        font-size:13px;flex-shrink:0;">
            <i class="bi <?= h($kat['icon']) ?>"></i>
          </span>
          <div style="font-size:12px;font-weight:600;line-height:1.2;"><?= h($kat['nama_kategori']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<div class="card-modern p-4">
  <div class="d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-plus-square-fill" style="font-size:18px; color:#f59e0b;"></i>
    <div>
      <h6 class="mb-0 fw-bold">Kategori Tambahan</h6>
      <div class="text-muted" style="font-size:12px;">Kategori yang ditambahkan admin secara manual.</div>
    </div>
    <span class="badge ms-auto" style="background:#f59e0b; font-size:11px;"><?= count($data_kategori_admin) ?> Kategori</span>
  </div>
  <?php if (count($data_kategori_admin) === 0): ?>
    <div class="text-center py-4 rounded-3" style="background:#fafafa; border:2px dashed #e5e7eb;">
      <i class="bi bi-plus-circle" style="font-size:2rem; color:#d1d5db;"></i>
      <h6 class="mt-2 mb-1" style="color:#9ca3af;">Belum ada kategori tambahan</h6>
      <p class="text-muted mb-3" style="font-size:13px;">Tambahkan kategori jika ada yang belum tercakup oleh bawaan sistem.</p>
      <button class="btn btn-utama btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
        <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
      </button>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-clean align-middle mb-0">
        <thead>
          <tr>
            <th>Nama Kategori</th>
            <th>Jenis</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data_kategori_admin as $idx => $item): ?>
            <?php $w = $warna_default[$idx % count($warna_default)]; ?>
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span style="width:28px;height:28px;border-radius:7px;background:<?= h($w) ?>22;
                                color:<?= h($w) ?>;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-tag" style="font-size:12px;"></i>
                  </span>
                  <span class="fw-semibold"><?= h($item['nama_kategori']) ?></span>
                </div>
              </td>
              <td>
                <span class="badge-status <?= $item['jenis'] === 'pemasukan' ? 'badge-masuk' : 'badge-keluar' ?>">
                  <?= $item['jenis'] === 'pemasukan' ? '▲ Pemasukan' : '▼ Pengeluaran' ?>
                </span>
              </td>
              <td class="text-center">
                <form method="post" class="d-inline"
                      onsubmit="return confirm('Hapus kategori \'<?= h(addslashes($item['nama_kategori'])) ?>\'?')">
                  <input type="hidden" name="aksi" value="hapus">
                  <input type="hidden" name="id_kategori" value="<?= (int) $item['id_kategori'] ?>">
                  <button class="btn btn-sm btn-light" title="Hapus kategori ini">
                    <i class="bi bi-trash text-danger"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0" style="border-radius:16px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Kategori</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post">
        <div class="modal-body">
          <input type="hidden" name="aksi" value="tambah">
          <div class="mb-3">
            <label class="form-label fw-semibold" for="inputNamaKat">
              Nama Kategori <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" name="nama_kategori" id="inputNamaKat"
                   placeholder="contoh: Asuransi, Dana Darurat…" required maxlength="100">
            <div class="form-text text-warning">
              <i class="bi bi-exclamation-triangle me-1"></i>
              Nama tidak boleh sama dengan kategori bawaan sistem di atas.
            </div>
          </div>
          <div class="mb-2">
            <label class="form-label fw-semibold" for="inputJenisKat">
              Jenis <span class="text-danger">*</span>
            </label>
            <select class="form-select" name="jenis" id="inputJenisKat" required>
              <option value="pemasukan">▲ Pemasukan</option>
              <option value="pengeluaran" selected>▼ Pengeluaran</option>
            </select>
          </div>
          <div class="mt-3 p-2 rounded-2" style="background:#f8f9ff; font-size:11px; color:#6b7280;">
            <div class="fw-semibold mb-1" style="color:#374151;">
              <i class="bi bi-shield-check me-1 text-primary"></i>Nama yang sudah ada di sistem (tidak bisa digunakan):
            </div>
            <?= implode(', ', array_map('h', $nama_default_set)) ?>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-utama"><i class="bi bi-check-lg me-1"></i>Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
