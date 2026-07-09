<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();
$id_user = (int) $_SESSION['id_user'];
function ambil_tabungan(PDO $koneksi, int $id_user): array
{
    $sql = 'SELECT * FROM tabungan WHERE id_user = :id_user ORDER BY tanggal_target ASC, id_tabungan DESC';
    $query = $koneksi->prepare($sql);
    $query->execute(['id_user' => $id_user]);
    return $query->fetchAll();
}
function tambah_tabungan(PDO $koneksi, int $id_user, array $input): string
{
    if ($input['nama_tujuan'] === '' || $input['target_nominal'] <= 0 || $input['tanggal_target'] === '') {
        return 'Data tidak valid.';
    }
    $simpan = $koneksi->prepare('INSERT INTO tabungan (id_user, nama_tujuan, target_nominal, nominal_terkumpul, tanggal_target) VALUES (:id_user, :nama_tujuan, :target_nominal, :nominal_terkumpul, :tanggal_target)');
    $simpan->execute([
        'id_user' => $id_user,
        'nama_tujuan' => $input['nama_tujuan'],
        'target_nominal' => $input['target_nominal'],
        'nominal_terkumpul' => $input['nominal_terkumpul'],
        'tanggal_target' => $input['tanggal_target'],
    ]);
    return '';
}
function edit_tabungan(PDO $koneksi, int $id_user, int $id_tabungan, array $input): string
{
    if ($id_tabungan <= 0 || $input['nama_tujuan'] === '' || $input['target_nominal'] <= 0 || $input['tanggal_target'] === '') {
        return 'Data tidak valid.';
    }
    $simpan = $koneksi->prepare('UPDATE tabungan SET nama_tujuan = :nama_tujuan, target_nominal = :target_nominal, tanggal_target = :tanggal_target WHERE id_tabungan = :id_tabungan AND id_user = :id_user');
    $simpan->execute([
        'nama_tujuan' => $input['nama_tujuan'],
        'target_nominal' => $input['target_nominal'],
        'tanggal_target' => $input['tanggal_target'],
        'id_tabungan' => $id_tabungan,
        'id_user' => $id_user,
    ]);
    return '';
}
function tambah_dana(PDO $koneksi, int $id_user, int $id_tabungan, float $nominal_tambahan): string
{
    if ($id_tabungan <= 0 || $nominal_tambahan <= 0) {
        return 'Data tidak valid.';
    }
    $update = $koneksi->prepare('UPDATE tabungan SET nominal_terkumpul = nominal_terkumpul + :nominal_tambahan WHERE id_tabungan = :id_tabungan AND id_user = :id_user');
    $update->execute([
        'nominal_tambahan' => $nominal_tambahan,
        'id_tabungan' => $id_tabungan,
        'id_user' => $id_user,
    ]);
    return '';
}
function hapus_tabungan(PDO $koneksi, int $id_user, int $id_tabungan): void
{
    $hapus = $koneksi->prepare('DELETE FROM tabungan WHERE id_tabungan = :id_tabungan AND id_user = :id_user');
    $hapus->execute(['id_tabungan' => $id_tabungan, 'id_user' => $id_user]);
}
$pesan_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'tambah' || $aksi === 'edit') {
        $input_tabungan = [
            'nama_tujuan' => trim($_POST['nama_tujuan'] ?? ''),
            'target_nominal' => (float) ($_POST['target_nominal'] ?? 0),
            'nominal_terkumpul' => (float) ($_POST['nominal_terkumpul'] ?? 0),
            'tanggal_target' => $_POST['tanggal_target'] ?? '',
        ];
        if ($aksi === 'tambah') {
            $pesan_error = tambah_tabungan($koneksi, $id_user, $input_tabungan);
        } else {
            $id_tabungan_edit = (int) ($_POST['id_tabungan'] ?? 0);
            $pesan_error = edit_tabungan($koneksi, $id_user, $id_tabungan_edit, $input_tabungan);
        }
        if ($pesan_error === '') {
            header('Location: ' . url('user/tabungan.php?sukses=1'));
            exit;
        }
    }
    if ($aksi === 'tambah_dana') {
        $pesan_error = tambah_dana($koneksi, $id_user, (int) ($_POST['id_tabungan'] ?? 0), (float) ($_POST['nominal_tambahan'] ?? 0));
        if ($pesan_error === '') {
            header('Location: ' . url('user/tabungan.php?dana=1'));
            exit;
        }
    }
    if ($aksi === 'hapus') {
        hapus_tabungan($koneksi, $id_user, (int) ($_POST['id_tabungan'] ?? 0));
        header('Location: ' . url('user/tabungan.php?hapus=1'));
        exit;
    }
}
$data_tabungan = ambil_tabungan($koneksi, $id_user);
$total_target = 0;
$total_terkumpul = 0;
$jumlah_aktif = 0;
foreach ($data_tabungan as $tabungan) {
    $target = (float) $tabungan['target_nominal'];
    $terkumpul = (float) $tabungan['nominal_terkumpul'];
    $total_target += $target;
    $total_terkumpul += $terkumpul;
    if ($target > 0 && $terkumpul < $target) {
        $jumlah_aktif++;
    }
}
$judul_halaman = 'Target Tabungan';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-3">
  <div>
    <h2 class="mb-1" style="font-size:24px;">Target Tabungan</h2>
    <p class="text-muted mb-0">Atur target tabungan agar rencana keuangan lebih terarah.</p>
  </div>
  <button class="btn btn-utama" data-bs-toggle="modal" data-bs-target="#modalTabungan" data-mode="tambah"><i class="bi bi-plus-lg me-1"></i>Buat Target Baru</button>
</div>
<?php if ($pesan_error): ?><div class="alert alert-danger border-0"><?= h($pesan_error) ?></div><?php endif; ?>
<?php if (isset($_GET['sukses'])): ?><div class="alert alert-success border-0">Berhasil disimpan.</div><?php endif; ?>
<?php if (isset($_GET['dana'])): ?><div class="alert alert-success border-0">Perubahan berhasil diterapkan.</div><?php endif; ?>
<?php if (isset($_GET['hapus'])): ?><div class="alert alert-success border-0">Perubahan berhasil diterapkan.</div><?php endif; ?>
<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="card-modern p-3"><div class="d-flex align-items-center gap-2"><span class="icon-bulat bg-ungu"><i class="bi bi-bullseye"></i></span><div><div class="kartu-label">Total Target</div><h3 class="kartu-nominal mb-0" style="font-size:30px"><?= format_rupiah($total_target) ?></h3></div></div></div></div>
  <div class="col-md-4"><div class="card-modern p-3"><div class="d-flex align-items-center gap-2"><span class="icon-bulat bg-hijau"><i class="bi bi-wallet2"></i></span><div><div class="kartu-label">Total Terkumpul</div><h3 class="kartu-nominal mb-0" style="font-size:30px"><?= format_rupiah($total_terkumpul) ?></h3></div></div></div></div>
  <div class="col-md-4"><div class="card-modern p-3"><div class="d-flex align-items-center gap-2"><span class="icon-bulat bg-biru"><i class="bi bi-piggy-bank"></i></span><div><div class="kartu-label">Tabungan Aktif</div><h3 class="kartu-nominal mb-0" style="font-size:30px"><?= (int) $jumlah_aktif ?></h3></div></div></div></div>
</div>
<?php if (count($data_tabungan) === 0): ?>
<div class="card-modern p-5 text-center">
  <div class="icon-btn mx-auto mb-3"><i class="bi bi-piggy-bank"></i></div>
  <h5>Belum ada target tabungan</h5>
  <p class="text-muted">Buat target tabungan baru untuk mulai menyisihkan dana secara teratur.</p>
  <button class="btn btn-utama" data-bs-toggle="modal" data-bs-target="#modalTabungan" data-mode="tambah">Buat Tabungan Pertama</button>
</div>
<?php else: ?>
<div class="row g-3">
  <?php foreach ($data_tabungan as $item): ?>
    <?php
      $target = (float) $item['target_nominal'];
      $terkumpul = (float) $item['nominal_terkumpul'];
      $progress = $target > 0 ? ($terkumpul / $target) * 100 : 0;
      $progress = max(0, min(100, $progress));
      $status_tercapai = $progress >= 100;
    ?>
    <div class="col-12 col-md-6 col-xl-4">
      <div class="card-modern p-3 tabungan-card h-100">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <h5 class="mb-1" style="font-size:18px"><?= h($item['nama_tujuan']) ?></h5>
            <div class="small text-muted">Target: <?= h($item['tanggal_target']) ?></div>
          </div>
          <span class="badge-status <?= $status_tercapai ? 'badge-masuk' : 'badge-keluar' ?>"><?= $status_tercapai ? 'Tercapai' : 'Dalam Proses' ?></span>
        </div>
        <div class="small text-muted mb-1">Terkumpul</div>
        <div class="fw-semibold mb-2" style="font-size:20px"><?= format_rupiah($terkumpul) ?> <span class="text-muted fw-normal" style="font-size:14px">/ <?= format_rupiah($target) ?></span></div>
        <div class="d-flex justify-content-between small mb-1"><span>Kemajuan</span><span><?= number_format($progress, 1) ?>%</span></div>
        <div class="progress mb-3 tabungan-progress"><div class="progress-bar" style="width: <?= $progress ?>%"></div></div>
        <div class="d-flex gap-2 flex-wrap">
          <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#modalTambahDana" data-id="<?= (int) $item['id_tabungan'] ?>" data-nama="<?= h($item['nama_tujuan']) ?>" title="Tambah Dana"><i class="bi bi-plus-circle me-1"></i>Tambah Dana</button>
          <button class="btn btn-sm btn-light tombol-edit-tabungan"
                  data-bs-toggle="modal" data-bs-target="#modalTabungan"
                  data-id="<?= (int) $item['id_tabungan'] ?>"
                  data-nama="<?= h($item['nama_tujuan']) ?>"
                  data-target="<?= h($item['target_nominal']) ?>"
                  data-terkumpul="<?= h($item['nominal_terkumpul']) ?>"
                  data-tanggal="<?= h($item['tanggal_target']) ?>"
                  title="Ubah"><i class="bi bi-pencil"></i></button>
          <form method="post" class="d-inline" onsubmit="return konfirmasiHapus('Yakin ingin menghapus target ini?')">
            <input type="hidden" name="aksi" value="hapus">
            <input type="hidden" name="id_tabungan" value="<?= (int) $item['id_tabungan'] ?>">
            <button class="btn btn-sm btn-light" title="Hapus"><i class="bi bi-trash text-danger"></i></button>
          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
<div class="modal fade" id="modalTabungan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0" style="border-radius:16px;">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="judulModalTabungan">Buat Tabungan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" id="formTabungan">
        <div class="modal-body pt-0">
          <input type="hidden" name="aksi" id="aksiTabungan" value="tambah">
          <input type="hidden" name="id_tabungan" id="idTabungan" value="0">
          <div class="mb-2"><label class="form-label">Nama Tujuan</label><input type="text" class="form-control" name="nama_tujuan" id="inputNamaTujuan" required></div>
          <div class="mb-2"><label class="form-label">Target Nominal</label><input type="number" class="form-control" min="1" name="target_nominal" id="inputTargetNominal" required></div>
          <div class="mb-2"><label class="form-label">Nominal Terkumpul</label><input type="number" class="form-control" min="0" name="nominal_terkumpul" id="inputTerkumpul" value="0" required></div>
          <div class="mb-2"><label class="form-label">Tanggal Target</label><input type="date" class="form-control" name="tanggal_target" id="inputTanggalTarget" required></div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-utama">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="modalTambahDana" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0" style="border-radius:16px;">
      <div class="modal-header border-0">
        <h5 class="modal-title">Tambah Dana</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post">
        <div class="modal-body pt-0">
          <input type="hidden" name="aksi" value="tambah_dana">
          <input type="hidden" name="id_tabungan" id="idTabunganDana" value="0">
          <div class="mb-2"><label class="form-label">Tujuan</label><input type="text" class="form-control" id="namaTabunganDana" readonly></div>
          <div class="mb-2"><label class="form-label">Nominal Tambahan</label><input type="number" class="form-control" min="1" name="nominal_tambahan" required></div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-utama">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
const modalTabungan = document.getElementById('modalTabungan');
if (modalTabungan) {
  modalTabungan.addEventListener('show.bs.modal', function(event) {
    const tombol = event.relatedTarget;
    const judul = document.getElementById('judulModalTabungan');
    const aksi = document.getElementById('aksiTabungan');
    const idInput = document.getElementById('idTabungan');
    const nama = document.getElementById('inputNamaTujuan');
    const target = document.getElementById('inputTargetNominal');
    const terkumpul = document.getElementById('inputTerkumpul');
    const tanggal = document.getElementById('inputTanggalTarget');
    if (tombol && tombol.classList.contains('tombol-edit-tabungan')) {
      judul.textContent = 'Ubah Tabungan';
      aksi.value = 'edit';
      idInput.value = tombol.getAttribute('data-id') || '0';
      nama.value = tombol.getAttribute('data-nama') || '';
      target.value = tombol.getAttribute('data-target') || '';
      terkumpul.value = tombol.getAttribute('data-terkumpul') || '0';
      tanggal.value = tombol.getAttribute('data-tanggal') || '';
    } else {
      judul.textContent = 'Buat Tabungan';
      aksi.value = 'tambah';
      idInput.value = '0';
      nama.value = '';
      target.value = '';
      terkumpul.value = '0';
      tanggal.valueAsDate = new Date();
    }
  });
}
const modalDana = document.getElementById('modalTambahDana');
if (modalDana) {
  modalDana.addEventListener('show.bs.modal', function(event) {
    const tombol = event.relatedTarget;
    document.getElementById('idTabunganDana').value = tombol.getAttribute('data-id') || '0';
    document.getElementById('namaTabunganDana').value = tombol.getAttribute('data-nama') || '';
  });
}
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>
