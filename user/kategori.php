<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();
$id_user = (int) $_SESSION['id_user'];
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
function ambil_kategori(PDO $koneksi, int $id_user, string $jenis_filter = 'semua'): array
{
    try {
        $where_default = 'AND k.is_default = 0';
        $sql = "SELECT k.*, COALESCE(jumlah.jumlah_transaksi, 0) AS jumlah_transaksi
                FROM kategori k
                LEFT JOIN (
                    SELECT kategori, COUNT(*) AS jumlah_transaksi
                    FROM transaksi
                    WHERE id_user = :id_user_jumlah
                    GROUP BY kategori
                ) jumlah ON jumlah.kategori = k.nama_kategori
                WHERE k.id_user = :id_user {$where_default}";
        $parameter = ['id_user_jumlah' => $id_user, 'id_user' => $id_user];
        if ($jenis_filter !== 'semua') {
            $sql .= ' AND k.jenis = :jenis';
            $parameter['jenis'] = $jenis_filter;
        }
        $sql .= ' ORDER BY k.jenis ASC, k.nama_kategori ASC';
        $query = $koneksi->prepare($sql);
        $query->execute($parameter);
        return $query->fetchAll();
    } catch (Throwable $e) {
        $sql = "SELECT k.*, COALESCE(jumlah.jumlah_transaksi, 0) AS jumlah_transaksi
                FROM kategori k
                LEFT JOIN (
                    SELECT kategori, COUNT(*) AS jumlah_transaksi
                    FROM transaksi
                    WHERE id_user = :id_user_jumlah
                    GROUP BY kategori
                ) jumlah ON jumlah.kategori = k.nama_kategori
                WHERE k.id_user = :id_user";
        $parameter = ['id_user_jumlah' => $id_user, 'id_user' => $id_user];
        if ($jenis_filter !== 'semua') {
            $sql .= ' AND k.jenis = :jenis';
            $parameter['jenis'] = $jenis_filter;
        }
        $sql .= ' ORDER BY k.jenis ASC, k.nama_kategori ASC';
        $query = $koneksi->prepare($sql);
        $query->execute($parameter);
        return $query->fetchAll();
    }
}
function tambah_kategori(PDO $koneksi, int $id_user, string $nama_kategori, string $jenis, array $nama_default_set): string
{
    $nama_kategori = trim($nama_kategori);
    if ($nama_kategori === '' || !in_array($jenis, ['pemasukan', 'pengeluaran'], true)) {
        return 'Nama kategori dan jenis wajib diisi.';
    }
    if (in_array($nama_kategori, $nama_default_set, true)) {
        return 'Nama tersebut sudah ada di kategori bawaan sistem.';
    }
    $cek = $koneksi->prepare('SELECT id_kategori FROM kategori WHERE id_user = :id_user AND nama_kategori = :nama_kategori LIMIT 1');
    $cek->execute(['id_user' => $id_user, 'nama_kategori' => $nama_kategori]);
    if ($cek->fetch()) {
        return 'Nama kategori sudah ada.';
    }
    try {
        $query = $koneksi->prepare('INSERT INTO kategori (id_user, nama_kategori, jenis, is_default) VALUES (:id_user, :nama_kategori, :jenis, 0)');
        $query->execute(['id_user' => $id_user, 'nama_kategori' => $nama_kategori, 'jenis' => $jenis]);
    } catch (Throwable $e) {
        $query = $koneksi->prepare('INSERT INTO kategori (id_user, nama_kategori, jenis) VALUES (:id_user, :nama_kategori, :jenis)');
        $query->execute(['id_user' => $id_user, 'nama_kategori' => $nama_kategori, 'jenis' => $jenis]);
    }
}
function edit_kategori(PDO $koneksi, int $id_user, int $id_kategori, string $nama_kategori, string $jenis): string
{
    $nama_kategori = trim($nama_kategori);
    if ($id_kategori <= 0 || $nama_kategori === '' || !in_array($jenis, ['pemasukan', 'pengeluaran'], true)) {
        return 'Data kategori tidak valid.';
    }
    $query_data_lama = $koneksi->prepare('SELECT nama_kategori FROM kategori WHERE id_kategori = :id_kategori AND id_user = :id_user AND is_default = 0 LIMIT 1');
    $query_data_lama->execute(['id_kategori' => $id_kategori, 'id_user' => $id_user]);
    $data_lama = $query_data_lama->fetch();
    if (!$data_lama) {
        return 'Kategori tidak ditemukan atau tidak dapat diubah.';
    }
    $nama_lama = $data_lama['nama_kategori'];
    $cek_duplikat = $koneksi->prepare('SELECT id_kategori FROM kategori WHERE id_user = :id_user AND nama_kategori = :nama_kategori AND id_kategori != :id_kategori LIMIT 1');
    $cek_duplikat->execute([
        'id_user'        => $id_user,
        'nama_kategori'  => $nama_kategori,
        'id_kategori'    => $id_kategori,
    ]);
    if ($cek_duplikat->fetch()) {
        return 'Nama kategori sudah digunakan kategori lain.';
    }
    try {
        $koneksi->beginTransaction();
        $koneksi->prepare('UPDATE kategori SET nama_kategori = :nama_kategori, jenis = :jenis WHERE id_kategori = :id_kategori AND id_user = :id_user AND is_default = 0')
            ->execute([
                'nama_kategori' => $nama_kategori,
                'jenis'         => $jenis,
                'id_kategori'   => $id_kategori,
                'id_user'       => $id_user,
            ]);
        if ($nama_lama !== $nama_kategori) {
            $koneksi->prepare('UPDATE transaksi SET kategori = :nama_baru WHERE id_user = :id_user AND kategori = :nama_lama')
                ->execute([
                    'nama_baru' => $nama_kategori,
                    'id_user'   => $id_user,
                    'nama_lama' => $nama_lama,
                ]);
        }
        $koneksi->commit();
    } catch (Throwable $e) {
        if ($koneksi->inTransaction()) {
            $koneksi->rollBack();
        }
        return 'Gagal memperbarui kategori.';
    }
    return '';
}
function hapus_kategori(PDO $koneksi, int $id_user, int $id_kategori): string
{
    if ($id_kategori <= 0) {
        return 'ID kategori tidak valid.';
    }
    $query_data = $koneksi->prepare('SELECT nama_kategori, is_default FROM kategori WHERE id_kategori = :id_kategori AND id_user = :id_user LIMIT 1');
    $query_data->execute(['id_kategori' => $id_kategori, 'id_user' => $id_user]);
    $data = $query_data->fetch();
    if (!$data) {
        return 'Kategori tidak ditemukan.';
    }
    if ((int) $data['is_default'] === 1) {
        return 'Kategori bawaan sistem tidak dapat dihapus.';
    }
    $nama_kategori = $data['nama_kategori'];
    $query_cek = $koneksi->prepare('SELECT COUNT(*) AS total FROM transaksi WHERE id_user = :id_user AND kategori = :kategori');
    $query_cek->execute(['id_user' => $id_user, 'kategori' => $nama_kategori]);
    $jumlah_terpakai = (int) $query_cek->fetch()['total'];
    if ($jumlah_terpakai > 0) {
        return 'Kategori masih digunakan oleh ' . $jumlah_terpakai . ' transaksi, tidak bisa dihapus.';
    }
    $koneksi->prepare('DELETE FROM kategori WHERE id_kategori = :id_kategori AND id_user = :id_user AND is_default = 0')
        ->execute(['id_kategori' => $id_kategori, 'id_user' => $id_user]);
    return '';
}
$pesan_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'tambah') {
        $pesan_error = tambah_kategori(
            $koneksi,
            $id_user,
            $_POST['nama_kategori'] ?? '',
            $_POST['jenis'] ?? '',
            $nama_default_set
        );
        if ($pesan_error === '') {
            header('Location: ' . url('user/kategori.php?sukses=1'));
            exit;
        }
    }
    if ($aksi === 'edit') {
        $pesan_error = edit_kategori(
            $koneksi,
            $id_user,
            (int) ($_POST['id_kategori'] ?? 0),
            $_POST['nama_kategori'] ?? '',
            $_POST['jenis'] ?? ''
        );
        if ($pesan_error === '') {
            header('Location: ' . url('user/kategori.php?edit=1'));
            exit;
        }
    }
    if ($aksi === 'hapus') {
        $pesan_error = hapus_kategori($koneksi, $id_user, (int) ($_POST['id_kategori'] ?? 0));
        if ($pesan_error === '') {
            header('Location: ' . url('user/kategori.php?hapus=1'));
            exit;
        }
    }
}
$kategori_aktif = $_GET['jenis'] ?? 'semua';
if (!in_array($kategori_aktif, ['semua', 'pemasukan', 'pengeluaran'], true)) {
    $kategori_aktif = 'semua';
}
$data_kategori        = ambil_kategori($koneksi, $id_user, $kategori_aktif);
$data_kategori_default = $KATEGORI_DEFAULT_SISTEM;
if ($kategori_aktif !== 'semua') {
    $data_kategori_default = array_values(array_filter(
        $data_kategori_default,
        fn($k) => $k['jenis'] === $kategori_aktif
    ));
}
$map_icon = [
    'makanan'    => 'bi-cup-hot',
    'transport'  => 'bi-bus-front',
    'belanja'    => 'bi-bag',
    'gaji'       => 'bi-cash-stack',
    'freelance'  => 'bi-laptop',
    'kesehatan'  => 'bi-heart-pulse',
    'hiburan'    => 'bi-controller',
    'rumah'      => 'bi-house-door',
    'investasi'  => 'bi-graph-up-arrow',
    'bonus'      => 'bi-gift',
    'pendidikan' => 'bi-book',
    'tagihan'    => 'bi-receipt',
];
$warna_icon = ['#4f46e5', '#22c55e', '#ef4444', '#f59e0b', '#3b82f6', '#ec4899', '#14b8a6', '#8b5cf6', '#f97316', '#06b6d4'];
function resolveIcon(string $nama, array $map, string $defaultIcon = 'bi-tag'): string {
    $nama_lower = strtolower($nama);
    foreach ($map as $kunci => $icon) {
        if (strpos($nama_lower, $kunci) !== false) {
            return $icon;
        }
    }
    return $defaultIcon;
}
$judul_halaman = 'Kategori';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-3">
  <div>
    <h2 class="mb-1" style="font-size:24px;">Kategori</h2>
    <p class="text-muted mb-0">Kelola kategori agar pencatatan transaksi lebih konsisten.</p>
  </div>
  <button class="btn btn-utama" data-bs-toggle="modal" data-bs-target="#modalKategori" data-mode="tambah">
    <i class="bi bi-plus-lg me-1"></i>Tambah Kategori Kustom
  </button>
</div>
<?php if ($pesan_error): ?><div class="alert alert-danger border-0"><i class="bi bi-exclamation-circle me-1"></i><?= h($pesan_error) ?></div><?php endif; ?>
<?php if (isset($_GET['sukses'])): ?><div class="alert alert-success border-0"><i class="bi bi-check-circle me-1"></i>Kategori berhasil ditambahkan.</div><?php endif; ?>
<?php if (isset($_GET['edit'])): ?><div class="alert alert-info border-0"><i class="bi bi-pencil me-1"></i>Kategori berhasil diperbarui.</div><?php endif; ?>
<?php if (isset($_GET['hapus'])): ?><div class="alert alert-warning border-0"><i class="bi bi-trash me-1"></i>Kategori berhasil dihapus.</div><?php endif; ?>
<div class="card-modern p-3">
  <div class="tabs-scroll d-flex gap-2 flex-wrap mb-4">
    <a href="<?= url('user/kategori.php?jenis=semua') ?>" class="btn <?= $kategori_aktif==='semua' ? 'btn-utama' : 'btn-light' ?>">Semua</a>
    <a href="<?= url('user/kategori.php?jenis=pemasukan') ?>" class="btn <?= $kategori_aktif==='pemasukan' ? 'btn-utama' : 'btn-light' ?>">Pemasukan</a>
    <a href="<?= url('user/kategori.php?jenis=pengeluaran') ?>" class="btn <?= $kategori_aktif==='pengeluaran' ? 'btn-utama' : 'btn-light' ?>">Pengeluaran</a>
  </div>
  <div class="mb-4">
    <div class="d-flex align-items-center gap-2 mb-2">
      <span class="badge bg-secondary" style="font-size:11px; letter-spacing:.5px;">BAWAAN SISTEM</span>
      <span class="text-muted" style="font-size:12px;">Kategori ini tersedia untuk semua pengguna dan tidak dapat diubah.</span>
    </div>
    <div class="row g-2">
      <?php foreach ($data_kategori_default as $idx => $item): ?>
        <?php
          $warna = $warna_icon[$idx % count($warna_icon)];
          $ikon  = $item['icon'] ?: resolveIcon($item['nama_kategori'], $map_icon);
        ?>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
          <div class="d-flex align-items-center gap-2 p-2 rounded-3 border border-dashed"
               style="background:<?= h($warna) ?>10; border-color:<?= h($warna) ?>40 !important;">
            <span class="icon-kategori flex-shrink-0"
                  style="background:<?= h($warna) ?>20; color:<?= h($warna) ?>; width:32px; height:32px; font-size:14px;">
              <i class="bi <?= h($ikon) ?>"></i>
            </span>
            <div class="min-w-0">
              <div class="fw-semibold text-truncate" style="font-size:13px;"><?= h($item['nama_kategori']) ?></div>
              <div style="font-size:10px; color:<?= h($warna) ?>; font-weight:600; letter-spacing:.3px;"><?= $item['jenis'] === 'pemasukan' ? '▲ MASUK' : '▼ KELUAR' ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2 mb-2">
    <span class="badge" style="background:#4f46e5; font-size:11px; letter-spacing:.5px;">KUSTOM ANDA</span>
    <span class="text-muted" style="font-size:12px;">Kategori tambahan yang Anda buat sendiri.</span>
  </div>
  <?php if (count($data_kategori) === 0): ?>
    <div class="text-center py-4 rounded-3" style="background:#f8f9ff; border:2px dashed #e0e0f0;">
      <div class="icon-btn mx-auto mb-2" style="opacity:.5;"><i class="bi bi-grid-3x3-gap"></i></div>
      <h6 class="mb-1" style="color:#888;">Belum ada kategori kustom</h6>
      <p class="text-muted mb-3" style="font-size:13px;">Tambah kategori baru jika kategori bawaan sistem belum sesuai dengan kebutuhan Anda.</p>
      <button class="btn btn-utama btn-sm" data-bs-toggle="modal" data-bs-target="#modalKategori" data-mode="tambah">
        <i class="bi bi-plus-lg me-1"></i>Tambah Kategori Kustom
      </button>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-clean align-middle mb-0">
        <thead>
          <tr>
            <th>Ikon</th>
            <th>Nama Kategori</th>
            <th>Jenis</th>
            <th class="text-center">Jumlah Transaksi</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data_kategori as $index => $item): ?>
            <?php
              $ikon   = resolveIcon($item['nama_kategori'], $map_icon);
              $warna  = $warna_icon[$index % count($warna_icon)];
            ?>
            <tr>
              <td>
                <span class="icon-kategori" style="background:<?= h($warna) ?>22; color:<?= h($warna) ?>;">
                  <i class="bi <?= h($ikon) ?>"></i>
                </span>
              </td>
              <td class="fw-semibold"><?= h($item['nama_kategori']) ?></td>
              <td>
                <span class="badge-status <?= $item['jenis'] === 'pemasukan' ? 'badge-masuk' : 'badge-keluar' ?>">
                  <?= h(ucfirst($item['jenis'])) ?>
                </span>
              </td>
              <td class="text-center"><?= (int) $item['jumlah_transaksi'] ?></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-light tombol-edit-kategori"
                        data-bs-toggle="modal" data-bs-target="#modalKategori"
                        data-id="<?= (int) $item['id_kategori'] ?>"
                        data-nama="<?= h($item['nama_kategori']) ?>"
                        data-jenis="<?= h($item['jenis']) ?>"
                        title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <form method="post" class="d-inline" onsubmit="return konfirmasiHapus('Yakin ingin menghapus kategori ini?')">
                  <input type="hidden" name="aksi" value="hapus">
                  <input type="hidden" name="id_kategori" value="<?= (int) $item['id_kategori'] ?>">
                  <button class="btn btn-sm btn-light" title="Hapus" <?= (int)$item['jumlah_transaksi'] > 0 ? 'disabled title="Masih digunakan transaksi"' : '' ?>>
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
<div class="modal fade" id="modalKategori" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0" style="border-radius:16px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title" id="judulModalKategori">Tambah Kategori</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" id="formKategori">
        <div class="modal-body">
          <input type="hidden" name="aksi" id="aksiKategori" value="tambah">
          <input type="hidden" name="id_kategori" id="idKategori" value="0">
          <div class="mb-3">
            <label class="form-label fw-semibold" for="inputNamaKategori">
              Nama Kategori <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" name="nama_kategori" id="inputNamaKategori"
                   placeholder="contoh: Ojek Online, Netflix…" required maxlength="100">
            <div class="form-text">Nama tidak boleh sama dengan kategori bawaan sistem.</div>
          </div>
          <div class="mb-2">
            <label class="form-label fw-semibold" for="inputJenisKategori">
              Jenis <span class="text-danger">*</span>
            </label>
            <select class="form-select" name="jenis" id="inputJenisKategori" required>
              <option value="pemasukan">▲ Pemasukan</option>
              <option value="pengeluaran" selected>▼ Pengeluaran</option>
            </select>
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
<script>
const modalKategori = document.getElementById('modalKategori');
if (modalKategori) {
  modalKategori.addEventListener('show.bs.modal', function(event) {
    const tombol     = event.relatedTarget;
    const judul      = document.getElementById('judulModalKategori');
    const aksi       = document.getElementById('aksiKategori');
    const idInput    = document.getElementById('idKategori');
    const namaInput  = document.getElementById('inputNamaKategori');
    const jenisInput = document.getElementById('inputJenisKategori');
    if (tombol && tombol.classList.contains('tombol-edit-kategori')) {
      judul.textContent    = 'Edit Kategori';
      aksi.value           = 'edit';
      idInput.value        = tombol.getAttribute('data-id') || '0';
      namaInput.value      = tombol.getAttribute('data-nama') || '';
      jenisInput.value     = tombol.getAttribute('data-jenis') || 'pengeluaran';
    } else {
      judul.textContent    = 'Tambah Kategori Kustom';
      aksi.value           = 'tambah';
      idInput.value        = '0';
      namaInput.value      = '';
      jenisInput.value     = 'pengeluaran';
    }
  });
}
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>
