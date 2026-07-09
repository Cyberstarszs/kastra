<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $harga_premium = max(1000, (int) ($_POST['harga_premium'] ?? 2000));
    $limit_prompt = max(1, (int) ($_POST['limit_prompt'] ?? 15));
    $update = $koneksi->prepare('UPDATE pengaturan_sistem SET nilai_pengaturan=:nilai WHERE nama_pengaturan=:nama');
    $update->execute(['nilai' => (string) $harga_premium, 'nama' => 'harga_premium']);
    $update->execute(['nilai' => (string) $limit_prompt, 'nama' => 'limit_prompt']);
    header('Location: ' . url('admin/pengaturan_sistem.php?sukses=1'));
    exit;
}
$pengaturan = ambil_pengaturan_sistem($koneksi);
$judul_halaman = 'Pengaturan Sistem';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="mb-1" style="font-size:24px;">Pengaturan Sistem</h2><p class="text-muted mb-0">Atur parameter inti aplikasi</p></div></div>
<?php if(isset($_GET['sukses'])): ?><div class="alert alert-success border-0">Pengaturan sistem berhasil disimpan.</div><?php endif; ?>
<div class="card-modern p-3">
  <form method="post" class="row g-3">
    <div class="col-md-4"><label class="form-label">Harga Premium (Rp)</label><input type="number" class="form-control" name="harga_premium" min="1000" value="<?= (int)$pengaturan['harga_premium'] ?>" required></div>
    <div class="col-md-4"><label class="form-label">Batas Pertanyaan Harian</label><input type="number" class="form-control" name="limit_prompt" min="1" value="<?= (int)$pengaturan['limit_prompt'] ?>" required></div>
    <div class="col-12"><small class="text-muted">API key dikelola terpusat di file <code>config/app.php</code>.</small></div>
    <div class="col-12"><button class="btn btn-utama">Simpan Pengaturan</button></div>
  </form>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
