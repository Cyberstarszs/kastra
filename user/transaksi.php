<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();
$id_user = (int) $_SESSION['id_user'];
function ambil_transaksi(PDO $koneksi, int $id_user, array $filter): array
{
    $sql = 'SELECT * FROM transaksi WHERE id_user = :id_user';
    $parameter = ['id_user' => $id_user];
    if ($filter['tab'] !== 'semua') {
        $sql .= ' AND jenis_transaksi = :jenis_transaksi';
        $parameter['jenis_transaksi'] = $filter['tab'];
    }
    if ($filter['tanggal'] !== '') {
        $sql .= ' AND tanggal = :tanggal';
        $parameter['tanggal'] = $filter['tanggal'];
    }
    if ($filter['kategori'] !== '') {
        $sql .= ' AND kategori = :kategori';
        $parameter['kategori'] = $filter['kategori'];
    }
    if ($filter['nominal_min'] > 0) {
        $sql .= ' AND nominal >= :nominal_min';
        $parameter['nominal_min'] = $filter['nominal_min'];
    }
    $sql .= ' ORDER BY tanggal DESC, id_transaksi DESC';
    $query = $koneksi->prepare($sql);
    $query->execute($parameter);
    return $query->fetchAll();
}
function tambah_transaksi(PDO $koneksi, int $id_user, array $input): string
{
    if ($input['nominal'] <= 0 || $input['kategori'] === '' || $input['deskripsi'] === '' || $input['tanggal'] === '') {
        return 'Data tidak valid.';
    }
    $simpan = $koneksi->prepare('INSERT INTO transaksi (id_user, jenis_transaksi, nominal, kategori, deskripsi, tanggal) VALUES (:id_user, :jenis_transaksi, :nominal, :kategori, :deskripsi, :tanggal)');
    $simpan->execute([
        'id_user' => $id_user,
        'jenis_transaksi' => $input['jenis_transaksi'],
        'nominal' => $input['nominal'],
        'kategori' => $input['kategori'],
        'deskripsi' => $input['deskripsi'],
        'tanggal' => $input['tanggal'],
    ]);
    return '';
}
function edit_transaksi(PDO $koneksi, int $id_user, int $id_transaksi, array $input): string
{
    if ($id_transaksi <= 0 || $input['nominal'] <= 0 || $input['kategori'] === '' || $input['deskripsi'] === '' || $input['tanggal'] === '') {
        return 'Data tidak valid.';
    }
    $simpan = $koneksi->prepare('UPDATE transaksi SET jenis_transaksi = :jenis_transaksi, nominal = :nominal, kategori = :kategori, deskripsi = :deskripsi, tanggal = :tanggal WHERE id_transaksi = :id_transaksi AND id_user = :id_user');
    $simpan->execute([
        'jenis_transaksi' => $input['jenis_transaksi'],
        'nominal' => $input['nominal'],
        'kategori' => $input['kategori'],
        'deskripsi' => $input['deskripsi'],
        'tanggal' => $input['tanggal'],
        'id_transaksi' => $id_transaksi,
        'id_user' => $id_user,
    ]);
    return '';
}
function hapus_transaksi(PDO $koneksi, int $id_user, int $id_transaksi): void
{
    $hapus = $koneksi->prepare('DELETE FROM transaksi WHERE id_transaksi = :id_transaksi AND id_user = :id_user');
    $hapus->execute(['id_transaksi' => $id_transaksi, 'id_user' => $id_user]);
}
$pesan_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'tambah' || $aksi === 'edit') {
        $input_transaksi = [
            'jenis_transaksi' => ($_POST['jenis_transaksi'] ?? 'pengeluaran') === 'pemasukan' ? 'pemasukan' : 'pengeluaran',
            'nominal' => (float) ($_POST['nominal'] ?? 0),
            'kategori' => trim($_POST['kategori'] ?? ''),
            'deskripsi' => trim($_POST['deskripsi'] ?? ''),
            'tanggal' => $_POST['tanggal'] ?? '',
        ];
        if ($aksi === 'tambah') {
            $pesan_error = tambah_transaksi($koneksi, $id_user, $input_transaksi);
        } else {
            $id_transaksi_edit = (int) ($_POST['id_transaksi'] ?? 0);
            $pesan_error = edit_transaksi($koneksi, $id_user, $id_transaksi_edit, $input_transaksi);
        }
        if ($pesan_error === '') {
            header('Location: ' . url('user/transaksi.php?sukses=1'));
            exit;
        }
    }
    if ($aksi === 'hapus') {
        hapus_transaksi($koneksi, $id_user, (int) ($_POST['id_transaksi'] ?? 0));
        header('Location: ' . url('user/transaksi.php?hapus=1'));
        exit;
    }
}
$filter = [
    'tab' => $_GET['tab'] ?? 'semua',
    'tanggal' => $_GET['tanggal'] ?? '',
    'kategori' => trim($_GET['kategori'] ?? ''),
    'nominal_min' => (float) ($_GET['nominal_min'] ?? 0),
];
if (!in_array($filter['tab'], ['semua', 'pemasukan', 'pengeluaran'], true)) {
    $filter['tab'] = 'semua';
}
$data_transaksi = ambil_transaksi($koneksi, $id_user, $filter);
$kategori_default_sistem = [
    'pemasukan' => [
        'Gaji'            => 'bi-cash-stack',
        'Freelance'       => 'bi-laptop',
        'Investasi'       => 'bi-graph-up-arrow',
        'Bonus'           => 'bi-gift',
        'Usaha'           => 'bi-shop',
        'Lain-lain Masuk' => 'bi-plus-circle',
    ],
    'pengeluaran' => [
        'Makanan & Minuman' => 'bi-cup-hot',
        'Transport'         => 'bi-bus-front',
        'Belanja'           => 'bi-bag',
        'Kesehatan'         => 'bi-heart-pulse',
        'Hiburan'           => 'bi-controller',
        'Rumah'             => 'bi-house-door',
        'Pendidikan'        => 'bi-book',
        'Tagihan'           => 'bi-receipt',
        'Lain-lain Keluar'  => 'bi-dash-circle',
    ],
];
try {
    $query_kategori_user = $koneksi->prepare(
        'SELECT nama_kategori, jenis FROM kategori WHERE id_user = :id_user AND is_default = 0 ORDER BY nama_kategori ASC'
    );
    $query_kategori_user->execute(['id_user' => $id_user]);
} catch (Throwable $e) {
    $query_kategori_user = $koneksi->prepare(
        'SELECT nama_kategori, jenis FROM kategori WHERE id_user = :id_user ORDER BY nama_kategori ASC'
    );
    $query_kategori_user->execute(['id_user' => $id_user]);
}
$data_kategori_user = $query_kategori_user->fetchAll();
$kategori_grouped = [
    'pemasukan'   => [],
    'pengeluaran' => [],
];
foreach ($kategori_default_sistem as $jenis => $items) {
    foreach (array_keys($items) as $nama) {
        $kategori_grouped[$jenis][$nama] = 'default';
    }
}
foreach ($data_kategori_user as $kat) {
    if (isset($kategori_grouped[$kat['jenis']][$kat['nama_kategori']])) {
        $kategori_grouped[$kat['jenis']][$kat['nama_kategori']] = 'user_override';
    } else {
        $kategori_grouped[$kat['jenis']][$kat['nama_kategori']] = 'user';
    }
}
$kategori_unik = [];
foreach ($kategori_grouped as $jenis => $items) {
    foreach (array_keys($items) as $nama) {
        $kategori_unik[$nama] = $jenis;
    }
}
$warna_kategori = ['#4f46e5', '#22c55e', '#ef4444', '#f59e0b', '#3b82f6', '#ec4899', '#14b8a6'];
$judul_halaman = 'Riwayat Transaksi';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-3">
  <div>
    <h2 class="mb-1" style="font-size:24px;">Riwayat Transaksi</h2>
    <p class="text-muted mb-0">Catat pemasukan dan pengeluaran dengan rapi.</p>
  </div>
  <button class="btn btn-utama" data-bs-toggle="modal" data-bs-target="#modalTransaksi" data-mode="tambah"><i class="bi bi-plus-lg me-1"></i>Tambah Transaksi</button>
</div>
<?php if ($pesan_error): ?><div class="alert alert-danger border-0"><?= h($pesan_error) ?></div><?php endif; ?>
<?php if (isset($_GET['sukses'])): ?><div class="alert alert-success border-0">Berhasil disimpan.</div><?php endif; ?>
<?php if (isset($_GET['hapus'])): ?><div class="alert alert-success border-0">Perubahan berhasil diterapkan.</div><?php endif; ?>
<div class="card-modern p-3">
      <div class="d-flex gap-2 flex-wrap mb-3">
        <a href="<?= url('user/transaksi.php?tab=semua') ?>" class="btn <?= $filter['tab']==='semua' ? 'btn-utama' : 'btn-light' ?>">Semua</a>
        <a href="<?= url('user/transaksi.php?tab=pemasukan') ?>" class="btn <?= $filter['tab']==='pemasukan' ? 'btn-utama' : 'btn-light' ?>">Pemasukan</a>
        <a href="<?= url('user/transaksi.php?tab=pengeluaran') ?>" class="btn <?= $filter['tab']==='pengeluaran' ? 'btn-utama' : 'btn-light' ?>">Pengeluaran</a>
      </div>
      <form method="get" class="row g-2 mb-3">
        <input type="hidden" name="tab" value="<?= h($filter['tab']) ?>">
        <div class="col-md-4"><input type="date" class="form-control" name="tanggal" value="<?= h($filter['tanggal']) ?>" title="Filter tanggal transaksi"></div>
        <div class="col-md-3">
          <select class="form-select" name="kategori">
            <option value="">Semua Kategori</option>
            <?php foreach ($kategori_unik as $nama_kategori => $jenis): ?>
              <option value="<?= h($nama_kategori) ?>" <?= $filter['kategori'] === $nama_kategori ? 'selected' : '' ?>><?= h($nama_kategori) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3"><input type="number" class="form-control" name="nominal_min" value="<?= h($filter['nominal_min']) ?>" placeholder="Nominal min"></div>
        <div class="col-md-2"><button class="btn btn-dark w-100"><i class="bi bi-funnel"></i></button></div>
      </form>
      <?php if (count($data_transaksi) === 0): ?>
        <div class="text-center py-5">
          <div class="icon-btn mx-auto mb-3"><i class="bi bi-wallet2"></i></div>
          <h5>Belum ada transaksi</h5>
          <p class="text-muted">Catat pemasukan dan pengeluaran harian Anda untuk mulai melacak keuangan.</p>
          <button class="btn btn-utama" data-bs-toggle="modal" data-bs-target="#modalTransaksi" data-mode="tambah">
            <i class="bi bi-plus-lg me-1"></i>Tambah Transaksi
          </button>
        </div>
      <?php else: ?>
      <div class="table-responsive">
        <table class="table table-clean align-middle mb-0">
          <thead>
            <tr><th>Tanggal</th><th>Kategori</th><th>Deskripsi</th><th>Jenis</th><th class="text-end">Nominal</th><th class="text-center">Aksi</th></tr>
          </thead>
          <tbody>
            <?php foreach ($data_transaksi as $index => $trx): ?>
              <?php $warna = $warna_kategori[$index % count($warna_kategori)]; ?>
              <tr>
                <td><?= h($trx['tanggal']) ?></td>
                <td><span class="d-inline-flex align-items-center gap-2"><span style="width:10px;height:10px;border-radius:50%;display:inline-block;background:<?= h($warna) ?>"></span><?= h($trx['kategori']) ?></span></td>
                <td><?= h($trx['deskripsi']) ?></td>
                <td><span class="badge-status <?= $trx['jenis_transaksi']==='pemasukan' ? 'badge-masuk' : 'badge-keluar' ?>"><?= h(ucfirst($trx['jenis_transaksi'])) ?></span></td>
                <td class="text-end <?= $trx['jenis_transaksi']==='pemasukan' ? 'nilai-hijau' : 'nilai-merah' ?>"><?= $trx['jenis_transaksi']==='pemasukan' ? '+' : '-' ?> <?= format_rupiah((float) $trx['nominal']) ?></td>
                <td class="text-center">
                  <button type="button" class="btn btn-sm btn-light tombol-edit"
                          data-bs-toggle="modal" data-bs-target="#modalTransaksi"
                          data-id="<?= (int) $trx['id_transaksi'] ?>"
                          data-jenis="<?= h($trx['jenis_transaksi']) ?>"
                          data-nominal="<?= h($trx['nominal']) ?>"
                          data-kategori="<?= h($trx['kategori']) ?>"
                          data-deskripsi="<?= h($trx['deskripsi']) ?>"
                          data-tanggal="<?= h($trx['tanggal']) ?>"><i class="bi bi-pencil"></i></button>
                  <form method="post" class="d-inline" onsubmit="return konfirmasiHapus('Yakin ingin menghapus transaksi ini?')">
                    <input type="hidden" name="aksi" value="hapus">
                    <input type="hidden" name="id_transaksi" value="<?= (int) $trx['id_transaksi'] ?>">
                    <button class="btn btn-sm btn-light"><i class="bi bi-trash text-danger"></i></button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
</div>
<div class="modal fade" id="modalTransaksi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0" style="border-radius:16px;">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="judulModalTransaksi">Tambah Transaksi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" id="formTransaksi">
        <div class="modal-body pt-0">
          <input type="hidden" name="aksi" id="aksiFormTransaksi" value="tambah">
          <input type="hidden" name="id_transaksi" id="idTransaksiEdit" value="0">
          <div class="mb-3">
            <label class="form-label fw-semibold">Jenis Transaksi <span class="text-danger">*</span></label>
            <select class="form-select" name="jenis_transaksi" id="inputJenis" required>
              <option value="pemasukan">▲ Pemasukan</option>
              <option value="pengeluaran" selected>▼ Pengeluaran</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Nominal <span class="text-danger">*</span></label>
            <input type="number" class="form-control" min="1" name="nominal" id="inputNominal"
                   placeholder="0" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
            <select class="form-select" name="kategori" id="inputKategori" required>
              <option value="">— Pilih kategori —</option>
              <optgroup label="▲ Pemasukan" id="grpPemasukan">
                <?php foreach ($kategori_grouped['pemasukan'] as $nama => $src): ?>
                <option value="<?= h($nama) ?>" data-jenis="pemasukan"><?= h($nama) ?><?= $src === 'default' ? '' : ' ✦' ?></option>
                <?php endforeach; ?>
              </optgroup>
              <optgroup label="▼ Pengeluaran" id="grpPengeluaran">
                <?php foreach ($kategori_grouped['pengeluaran'] as $nama => $src): ?>
                <option value="<?= h($nama) ?>" data-jenis="pengeluaran"><?= h($nama) ?><?= $src === 'default' ? '' : ' ✦' ?></option>
                <?php endforeach; ?>
              </optgroup>
            </select>
            <div class="form-text">Tanda ✦ = kategori kustom Anda.</div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="deskripsi" id="inputDeskripsi"
                   placeholder="Catatan singkat transaksi ini…" required>
          </div>
          <div class="mb-2">
            <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
            <input type="date" class="form-control" name="tanggal" id="inputTanggal" required>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-utama">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
const modalTransaksi = document.getElementById('modalTransaksi');
if (modalTransaksi) {
  const inputJenis    = document.getElementById('inputJenis');
  const inputKategori = document.getElementById('inputKategori');
  // ── Filter optgroup sesuai jenis transaksi yang dipilih ──────────────────
  function filterKategoriByJenis(jenisAktif) {
    const options = inputKategori.querySelectorAll('option[data-jenis]');
    const grpMasuk  = document.getElementById('grpPemasukan');
    const grpKeluar = document.getElementById('grpPengeluaran');
    if (jenisAktif === 'pemasukan') {
      if (grpMasuk)  grpMasuk.style.display  = '';
      if (grpKeluar) grpKeluar.style.display = 'none';
    } else {
      if (grpMasuk)  grpMasuk.style.display  = 'none';
      if (grpKeluar) grpKeluar.style.display = '';
    }
    // Reset ke placeholder jika nilai aktif tidak cocok jenis
    const current = inputKategori.value;
    const matchedOpt = inputKategori.querySelector(`option[value="${CSS.escape(current)}"][data-jenis="${jenisAktif}"]`);
    if (!matchedOpt) {
      inputKategori.value = '';
    }
  }
  inputJenis.addEventListener('change', () => filterKategoriByJenis(inputJenis.value));
  modalTransaksi.addEventListener('show.bs.modal', function(event) {
    const tombol       = event.relatedTarget;
    const judul        = document.getElementById('judulModalTransaksi');
    const aksi         = document.getElementById('aksiFormTransaksi');
    const idInput      = document.getElementById('idTransaksiEdit');
    const inputNominal = document.getElementById('inputNominal');
    const inputDeskripsi = document.getElementById('inputDeskripsi');
    const inputTanggal = document.getElementById('inputTanggal');
    if (tombol && tombol.classList.contains('tombol-edit')) {
      judul.textContent      = 'Ubah Transaksi';
      aksi.value             = 'edit';
      idInput.value          = tombol.getAttribute('data-id') || '0';
      inputJenis.value       = tombol.getAttribute('data-jenis') || 'pengeluaran';
      inputNominal.value     = tombol.getAttribute('data-nominal') || '';
      inputDeskripsi.value   = tombol.getAttribute('data-deskripsi') || '';
      inputTanggal.value     = tombol.getAttribute('data-tanggal') || '';
      filterKategoriByJenis(inputJenis.value);
      inputKategori.value    = tombol.getAttribute('data-kategori') || '';
    } else {
      judul.textContent      = 'Tambah Transaksi';
      aksi.value             = 'tambah';
      idInput.value          = '0';
      inputJenis.value       = 'pengeluaran';
      inputNominal.value     = '';
      inputKategori.value    = '';
      inputDeskripsi.value   = '';
      inputTanggal.valueAsDate = new Date();
      filterKategoriByJenis('pengeluaran');
    }
  });
}
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>
