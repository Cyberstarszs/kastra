<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $id_user = (int) ($_POST['id_user'] ?? 0);
    if ($id_user > 0) {
        if ($aksi === 'reset_prompt') {
            $limit = (int) (ambil_pengaturan_sistem($koneksi)['limit_prompt'] ?? 15);
            $q = $koneksi->prepare('UPDATE users SET sisa_prompt=:limit_prompt, tanggal_reset_prompt=:tanggal WHERE id_user=:id_user AND role=\'user\'');
            $q->execute(['limit_prompt' => $limit, 'tanggal' => date('Y-m-d'), 'id_user' => $id_user]);
        } elseif ($aksi === 'hapus_user') {
            $q = $koneksi->prepare('DELETE FROM users WHERE id_user=:id_user AND role=\'user\'');
            $q->execute(['id_user' => $id_user]);
        }
    }
    header('Location: ' . url('admin/data_user.php'));
    exit;
}
$query = $koneksi->query("SELECT u.*, (SELECT COUNT(*) FROM transaksi t WHERE t.id_user=u.id_user) AS jumlah_transaksi FROM users u WHERE u.role='user' ORDER BY u.id_user DESC");
$data_user = $query->fetchAll();
$judul_halaman = 'Data Pengguna';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div><h2 class="mb-1" style="font-size:24px;">Data Pengguna</h2><p class="text-muted mb-0">Kelola akun pengguna pada sistem.</p></div>
</div>
<div class="card-modern p-3 table-responsive">
  <table class="table table-clean align-middle mb-0">
    <thead><tr>
      <th>Nama</th>
      <th><i class="bi bi-shield-lock text-warning me-1"></i>Email <span class="text-muted fw-normal" style="font-size:10px;">(sebagian)</span></th>
      <th>Status</th>
      <th>Sisa Pertanyaan</th>
      <th>Jml. Catatan</th>
      <th class="text-end">Aksi</th>
    </tr></thead>
    <tbody>
    <?php foreach ($data_user as $user): ?>
      <tr>
        <td><?= h($user['nama_lengkap']) ?></td>
        <td>
          <?php
            $email_parts = explode('@', $user['email']);
            $local = $email_parts[0] ?? '';
            $domain = $email_parts[1] ?? '';
            $masked_local = substr($local, 0, 2) . str_repeat('*', max(0, strlen($local) - 2));
            echo h($masked_local . '@' . $domain);
          ?>
        </td>
        <td><span class="badge-status <?= $user['status_user'] === 'premium' ? 'badge-premium' : 'badge-biasa' ?>"><?= $user['status_user'] === 'premium' ? 'Premium' : 'Standar' ?></span></td>
        <td><?= $user['status_user'] === 'premium' ? 'Tanpa Batas' : (int) $user['sisa_prompt'] ?></td>
        <td><?= (int) $user['jumlah_transaksi'] ?></td>
        <td class="text-end">
          <form method="post" class="d-inline"><input type="hidden" name="id_user" value="<?= (int) $user['id_user'] ?>"><input type="hidden" name="aksi" value="reset_prompt"><button class="btn btn-sm btn-light">Atur Ulang Kuota</button></form>
          <form method="post" class="d-inline" onsubmit="return confirm('Hapus pengguna ini?')"><input type="hidden" name="id_user" value="<?= (int) $user['id_user'] ?>"><input type="hidden" name="aksi" value="hapus_user"><button class="btn btn-sm btn-outline-danger">Hapus</button></form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
