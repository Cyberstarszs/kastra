<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();
$id_user = (int) $_SESSION['id_user'];
$pesan_sukses = '';
$pesan_error = '';
$tab_aktif = $_GET['tab'] ?? 'profil';
if (!in_array($tab_aktif, ['profil', 'keamanan', 'preferensi'], true)) {
    $tab_aktif = 'profil';
}
try {
    $koneksi->exec("ALTER TABLE users ADD COLUMN foto VARCHAR(255) NULL");
} catch (Throwable $e) {
}
try {
    $koneksi->exec("ALTER TABLE users ADD COLUMN preferensi JSON NULL");
} catch (Throwable $e) {
}
$query_user = $koneksi->prepare('SELECT id_user, nama_lengkap, email, kata_sandi, foto, preferensi FROM users WHERE id_user = :id_user LIMIT 1');
$query_user->execute(['id_user' => $id_user]);
$data_user = $query_user->fetch();
if (!$data_user) {
    session_destroy();
    header('Location: ' . url('auth/login.php'));
    exit;
}
$preferensi_default = [
    'tema' => 'light',
    'notifikasi' => true,
    'mata_uang' => 'IDR',
];
$preferensi_user = $preferensi_default;
if (!empty($data_user['preferensi'])) {
    $decode = json_decode((string) $data_user['preferensi'], true);
    if (is_array($decode)) {
        $preferensi_user = array_merge($preferensi_default, $decode);
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'profil') {
        $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $foto_lama = $data_user['foto'] ?? null;
        $path_foto = $foto_lama;
        if ($nama_lengkap === '') {
            $pesan_error = 'Data tidak valid.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $pesan_error = 'Format email tidak valid.';
        } else {
            $cek_email = $koneksi->prepare('SELECT id_user FROM users WHERE email = :email AND id_user != :id_user LIMIT 1');
            $cek_email->execute(['email' => $email, 'id_user' => $id_user]);
            if ($cek_email->fetch()) {
                $pesan_error = 'Data tidak valid.';
            }
        }
        if ($pesan_error === '' && isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['foto'];
            $ekstensi_valid = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $pesan_error = 'Gagal memproses permintaan.';
            } elseif (!in_array($ext, $ekstensi_valid, true)) {
                $pesan_error = 'Data tidak valid.';
            } elseif ((int) $file['size'] > 2 * 1024 * 1024) {
                $pesan_error = 'Data tidak valid.';
            } else {
                $nama_baru = 'profil_' . $id_user . '_' . time() . '.' . $ext;
                $tujuan_relatif = 'assets/uploads/profil/' . $nama_baru;
                $tujuan_abs = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $tujuan_relatif);
                if (move_uploaded_file($file['tmp_name'], $tujuan_abs)) {
                    $path_foto = $tujuan_relatif;
                } else {
                    $pesan_error = 'Gagal memproses permintaan.';
                }
            }
        }
        if ($pesan_error === '') {
            $update = $koneksi->prepare('UPDATE users SET nama_lengkap = :nama_lengkap, email = :email, foto = :foto WHERE id_user = :id_user');
            $update->execute([
                'nama_lengkap' => $nama_lengkap,
                'email' => $email,
                'foto' => $path_foto,
                'id_user' => $id_user,
            ]);
            $_SESSION['nama_lengkap'] = $nama_lengkap;
            $_SESSION['email'] = $email;
            $_SESSION['foto'] = $path_foto;
            $pesan_sukses = 'Berhasil disimpan.';
            $tab_aktif = 'profil';
        }
    }
    if ($aksi === 'keamanan') {
        $password_lama = $_POST['password_lama'] ?? '';
        $kata_sandi_baru = $_POST['kata_sandi_baru'] ?? '';
        $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';
        if (!password_verify($password_lama, (string) $data_user['kata_sandi'])) {
            $pesan_error = 'Data tidak valid.';
        } elseif (strlen($kata_sandi_baru) < 6) {
            $pesan_error = 'Data tidak valid.';
        } elseif ($kata_sandi_baru !== $konfirmasi_password) {
            $pesan_error = 'Data tidak valid.';
        } else {
            $update_password = $koneksi->prepare('UPDATE users SET kata_sandi = :kata_sandi WHERE id_user = :id_user');
            $update_password->execute([
                'kata_sandi' => password_hash($kata_sandi_baru, PASSWORD_DEFAULT),
                'id_user' => $id_user,
            ]);
            $pesan_sukses = 'Perubahan berhasil diterapkan.';
        }
        $tab_aktif = 'keamanan';
    }
    if ($aksi === 'preferensi') {
        $tema = ($_POST['tema'] ?? 'light') === 'dark' ? 'dark' : 'light';
        $notifikasi = isset($_POST['notifikasi']) && $_POST['notifikasi'] === '1';
        $mata_uang = ($_POST['mata_uang'] ?? 'IDR') === 'IDR' ? 'IDR' : 'IDR';
        $preferensi_baru = [
            'tema' => $tema,
            'notifikasi' => $notifikasi,
            'mata_uang' => $mata_uang,
        ];
        $update_preferensi = $koneksi->prepare('UPDATE users SET preferensi = :preferensi WHERE id_user = :id_user');
        $update_preferensi->execute([
            'preferensi' => json_encode($preferensi_baru),
            'id_user' => $id_user,
        ]);
        $_SESSION['tema'] = $tema;
        $pesan_sukses = 'Perubahan berhasil diterapkan.';
        $tab_aktif = 'preferensi';
    }
    $query_user->execute(['id_user' => $id_user]);
    $data_user = $query_user->fetch();
    $preferensi_user = $preferensi_default;
    if (!empty($data_user['preferensi'])) {
        $decode = json_decode((string) $data_user['preferensi'], true);
        if (is_array($decode)) {
            $preferensi_user = array_merge($preferensi_default, $decode);
        }
    }
}
$foto_preview = !empty($data_user['foto']) ? url((string) $data_user['foto']) : '';
$inisial = strtoupper(substr((string) $data_user['nama_lengkap'], 0, 1));
$judul_halaman = 'Pengaturan Akun';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-3">
  <div>
    <h2 class="mb-1" style="font-size:24px;">Pengaturan Akun</h2>
    <p class="text-muted mb-0">Kelola profil, keamanan, dan preferensi akun Anda.</p>
  </div>
</div>
<?php if ($pesan_sukses): ?><div class="alert alert-success border-0"><?= h($pesan_sukses) ?></div><?php endif; ?>
<?php if ($pesan_error): ?><div class="alert alert-danger border-0"><?= h($pesan_error) ?></div><?php endif; ?>
<div class="card-modern p-3">
  <div class="tab-nav settings-tab-nav">
    <a href="<?= url('user/pengaturan.php?tab=profil') ?>" class="btn <?= $tab_aktif==='profil'?'btn-utama':'btn-light' ?>">Profil</a>
    <a href="<?= url('user/pengaturan.php?tab=keamanan') ?>" class="btn <?= $tab_aktif==='keamanan'?'btn-utama':'btn-light' ?>">Keamanan</a>
    <a href="<?= url('user/pengaturan.php?tab=preferensi') ?>" class="btn <?= $tab_aktif==='preferensi'?'btn-utama':'btn-light' ?>">Preferensi</a>
  </div>
  <?php if ($tab_aktif === 'profil'): ?>
  <form method="post" enctype="multipart/form-data" class="row g-3 mt-1">
    <input type="hidden" name="aksi" value="profil">
    <div class="col-md-4">
      <label class="form-label">Foto Profil</label>
      <div class="profile-preview-box">
        <img id="fotoPreview" src="<?= h($foto_preview) ?>" alt="Foto Profil" class="profile-preview-img <?= $foto_preview ? '' : 'd-none' ?>">
        <div id="fotoFallback" class="profile-preview-fallback <?= $foto_preview ? 'd-none' : '' ?>"><?= h($inisial) ?></div>
      </div>
      <input type="file" class="form-control mt-2" name="foto" id="inputFoto" accept="image/png,image/jpeg,image/webp">
      <div class="small text-muted mt-1">Maks 2MB (jpg, png, webp)</div>
    </div>
    <div class="col-md-8">
      <div class="row g-3">
        <div class="col-12"><label class="form-label">Nama Lengkap</label><input name="nama_lengkap" class="form-control" value="<?= h($data_user['nama_lengkap']) ?>" required></div>
        <div class="col-12"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="<?= h($data_user['email']) ?>" required></div>
        <div class="col-12 d-flex gap-2"><button class="btn btn-utama">Simpan Perubahan</button><a href="<?= url('user/pengaturan.php?tab=profil') ?>" class="btn btn-light">Batal</a></div>
      </div>
    </div>
  </form>
  <?php endif; ?>
  <?php if ($tab_aktif === 'keamanan'): ?>
  <form method="post" class="row g-3 mt-1">
    <input type="hidden" name="aksi" value="keamanan">
    <div class="col-md-6">
      <label class="form-label">Kata Sandi Lama</label>
      <div class="input-group"><input name="password_lama" type="password" class="form-control" id="passwordLama" required><button class="btn btn-light" type="button" onclick="togglePassword('passwordLama')"><i class="bi bi-eye"></i></button></div>
    </div>
    <div class="col-md-6">
      <label class="form-label">Kata Sandi Baru</label>
      <div class="input-group"><input name="kata_sandi_baru" type="password" class="form-control" id="passwordBaru" required><button class="btn btn-light" type="button" onclick="togglePassword('passwordBaru')"><i class="bi bi-eye"></i></button></div>
    </div>
    <div class="col-md-6">
      <label class="form-label">Konfirmasi Kata Sandi</label>
      <div class="input-group"><input name="konfirmasi_password" type="password" class="form-control" id="konfirmasiPassword" required><button class="btn btn-light" type="button" onclick="togglePassword('konfirmasiPassword')"><i class="bi bi-eye"></i></button></div>
    </div>
    <div class="col-12"><button class="btn btn-utama">Ubah Kata Sandi</button></div>
  </form>
  <?php endif; ?>
  <?php if ($tab_aktif === 'preferensi'): ?>
  <form method="post" class="row g-3 mt-1">
    <input type="hidden" name="aksi" value="preferensi">
    <div class="col-md-4">
      <label class="form-label">Tema</label>
      <select class="form-select" name="tema">
        <option value="light" <?= $preferensi_user['tema']==='light'?'selected':'' ?>>Terang</option>
        <option value="dark" <?= $preferensi_user['tema']==='dark'?'selected':'' ?>>Gelap</option>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Mata Uang</label>
      <select class="form-select" name="mata_uang">
        <option value="IDR" selected>Rupiah (Rp)</option>
      </select>
    </div>
    <div class="col-md-4 d-flex align-items-end">
      <div class="form-check form-switch mb-2">
        <input class="form-check-input switch-ungu" type="checkbox" name="notifikasi" value="1" id="notifikasiSwitch" <?= !empty($preferensi_user['notifikasi']) ? 'checked' : '' ?>>
        <label class="form-check-label" for="notifikasiSwitch">Aktifkan Notifikasi</label>
      </div>
    </div>
    <div class="col-12"><button class="btn btn-utama">Simpan Preferensi</button></div>
  </form>
  <?php endif; ?>
</div>
<script>
(function () {
  const inputFoto = document.getElementById('inputFoto');
  const fotoPreview = document.getElementById('fotoPreview');
  const fotoFallback = document.getElementById('fotoFallback');
  if (inputFoto && fotoPreview && fotoFallback) {
    inputFoto.addEventListener('change', function () {
      const file = this.files && this.files[0] ? this.files[0] : null;
      if (!file) return;
      const reader = new FileReader();
      reader.onload = function (e) {
        fotoPreview.src = e.target.result;
        fotoPreview.classList.remove('d-none');
        fotoFallback.classList.add('d-none');
      };
      reader.readAsDataURL(file);
    });
  }
})();
function togglePassword(idInput) {
  const elemen = document.getElementById(idInput);
  if (!elemen) return;
  elemen.type = elemen.type === 'password' ? 'text' : 'password';
}
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>
