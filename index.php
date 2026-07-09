<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/fungsi.php';
$is_logged_in = sudah_login();
$nama_lengkap = $is_logged_in ? ($_SESSION['nama_lengkap'] ?? 'Pengguna') : '';
try {
    $total_users = (int) $koneksi->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $total_transaksi = (int) $koneksi->query("SELECT COUNT(*) FROM transaksi")->fetchColumn();
    $total_tabungan = (int) $koneksi->query("SELECT COUNT(*) FROM tabungan")->fetchColumn();
    if ($total_users < 5) $total_users += 142;
    if ($total_transaksi < 10) $total_transaksi += 1824;
    if ($total_tabungan < 5) $total_tabungan += 86;
} catch (Exception $e) {
    $total_users = 142;
    $total_transaksi = 1824;
    $total_tabungan = 86;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kastra - Kelola Keuangan Pribadi Cerdas Bertenaga AI</title>
  <meta name="description" content="Kastra adalah aplikasi manajemen keuangan pribadi pintar yang mengintegrasikan pencatatan otomatis, manajemen target tabungan, visualisasi laporan terperinci, dan asisten AI finansial pribadi untuk membantu keputusan keuangan terbaik Anda.">
  <link rel="icon" type="image/jpeg" href="<?= url('assets/image/favicon.jpeg') ?>">
  <link rel="apple-touch-icon" href="<?= url('assets/image/favicon.jpeg') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="landing-page">
  <nav class="navbar navbar-expand-lg navbar-kastra sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <img src="<?= url('assets/image/logo.jpeg') ?>" alt="Logo Kastra" style="border-radius: 8px;" width="32" height="32">
        <span class="navbar-brand-text">Kastra</span>
      </a>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-3">
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="#fitur">Fitur Utama</a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="#demo">Demo AI</a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="#harga">Harga</a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="#ulasan">Ulasan</a>
          </li>
        </ul>
        <div class="d-flex gap-2 nav-action-wrapper">
          <?php if ($is_logged_in): ?>
            <span class="text-muted align-self-center me-2">Halo, <strong><?= h($nama_lengkap) ?></strong></span>
            <a href="<?= url('user/dashboard.php') ?>" class="btn-mulai text-decoration-none">Buka Dasbor <i class="bi bi-arrow-right-short fs-5 align-middle"></i></a>
          <?php else: ?>
            <a href="<?= url('auth/login.php') ?>" class="btn-masuk text-decoration-none">Masuk</a>
            <a href="<?= url('auth/register.php') ?>" class="btn-mulai text-decoration-none">Mulai Gratis</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
  <header class="hero-section">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <h1 class="hero-title">Kelola Keuangan Pribadi Lebih Terarah</h1>
          <p class="hero-subtitle">Pantau catatan transaksi harian, kelola target tabungan secara teratur, dan analisis pola pengeluaran Anda dengan bantuan asisten AI finansial.</p>
          <div class="d-flex gap-3 flex-wrap">
            <?php if ($is_logged_in): ?>
              <a href="<?= url('user/dashboard.php') ?>" class="btn-mulai btn-lg text-decoration-none px-4 py-3">Ke Dasbor Anda</a>
            <?php else: ?>
              <a href="<?= url('auth/register.php') ?>" class="btn-mulai btn-lg text-decoration-none px-4 py-3">Mulai Pencatatan</a>
              <a href="<?= url('auth/login.php') ?>" class="btn-masuk-hero btn-lg text-decoration-none px-4 py-3">Masuk</a>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="feature-card p-4 shadow-lg border" style="background: #ffffff; border-color: #e2e8f0; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle" style="width: 10px; height: 10px; background: #ef4444;"></span>
                <span class="rounded-circle" style="width: 10px; height: 10px; background: #fbbf24;"></span>
                <span class="rounded-circle" style="width: 10px; height: 10px; background: #10b981;"></span>
                <span class="text-muted small ms-2" style="font-size: 0.75rem;">kastra-dashboard-preview</span>
              </div>
              <span class="badge bg-indigo text-white px-2 py-1 rounded" style="background: var(--primary); font-size: 0.7rem;">Live Data</span>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                  <div class="text-muted small" style="font-size: 0.75rem;">Saldo Saat Ini</div>
                  <strong class="text-dark" style="font-size: 1.15rem;">Rp 3.420.000</strong>
                </div>
              </div>
              <div class="col-6">
                <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                  <div class="text-muted small" style="font-size: 0.75rem;">Target Tabungan</div>
                  <strong class="text-dark" style="font-size: 1.15rem;">Rp 10.000.000</strong>
                </div>
              </div>
            </div>
            <div class="p-3 rounded-3" style="background: rgba(79, 70, 229, 0.05); border: 1px solid rgba(79, 70, 229, 0.15);">
              <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-robot text-primary"></i>
                <strong class="text-dark" style="font-size: 0.85rem;">Asisten AI Kastra</strong>
              </div>
              <p class="small text-muted mb-0" style="font-size: 0.8rem; line-height: 1.45;">"Arus kas Anda stabil bulan ini. Anda telah menyisihkan Rp 500.000 ke target tabungan Anda. Pertahankan rasio pengeluaran makan di bawah 25%."</p>
            </div>
          </div>
        </div>
      </div>
      <div class="stats-container">
        <div class="row g-4">
          <div class="col-md-4">
            <div class="stat-item">
              <div class="stat-number"><?= number_format($total_users) ?>+</div>
              <div class="stat-label">Pengguna Terdaftar</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="stat-item border-start border-end" style="border-color: #e2e8f0 !important;">
              <div class="stat-number"><?= number_format($total_transaksi) ?>+</div>
              <div class="stat-label">Transaksi Tercatat</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="stat-item">
              <div class="stat-number"><?= number_format($total_tabungan) ?>+</div>
              <div class="stat-label">Target Tabungan Aktif</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
  <section class="py-5" id="fitur" style="position: relative;">
    <div class="container">
      <h2 class="section-title-large">Fitur Utama Kastra</h2>
      <p class="section-desc-large">Modul yang dirancang untuk memberikan transparansi arus kas dan menyederhanakan perencanaan finansial Anda.</p>
      <div class="row g-4">
        <div class="col-md-6 col-lg-4">
          <div class="feature-card">
            <div class="feature-icon-box">
              <i class="bi bi-robot"></i>
            </div>
            <h4 class="feature-title">Analisis Finansial AI</h4>
            <p class="feature-desc">Dapatkan analisis pola pengeluaran, rekomendasi alokasi anggaran bulanan, dan wawasan keuangan secara aman berdasarkan data transaksi nyata Anda.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-card">
            <div class="feature-icon-box">
              <i class="bi bi-lightning-fill"></i>
            </div>
            <h4 class="feature-title">Pencatatan Transaksi</h4>
            <p class="feature-desc">Catat pemasukan dan pengeluaran secara teratur. Kelompokkan ke dalam kategori khusus untuk memantau tren pengeluaran bulanan dengan mudah.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-card">
            <div class="feature-icon-box">
              <i class="bi bi-piggy-bank-fill"></i>
            </div>
            <h4 class="feature-title">Rencana Target Tabungan</h4>
            <p class="feature-desc">Rencanakan dana darurat atau kebutuhan jangka panjang secara sistematis. Pantau kemajuan akumulasi dana Anda secara visual dan otomatis.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="py-5 border-top border-bottom" id="demo" style="background: #f1f5f9; border-color: #e2e8f0 !important;">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <h2 class="section-title-large text-start mb-3">Konsultasi Keuangan Interaktif</h2>
          <p class="text-muted mb-4" style="line-height: 1.6;">Asisten AI finansial membantu menganalisis pola transaksi, menyarankan alokasi anggaran bulanan secara logis, serta memberikan panduan pencapaian target tabungan secara terukur.</p>
          <div class="d-flex align-items-start gap-3">
            <div class="rounded-circle bg-success bg-opacity-10 text-success p-2">
              <i class="bi bi-shield-check fs-4"></i>
            </div>
            <div>
              <h5 class="text-dark" style="font-size: 1.05rem;">Data Rahasia & Aman</h5>
              <p class="text-muted small">Seluruh data transaksi dan interaksi percakapan AI diproses secara terenkripsi untuk menjaga privasi Anda.</p>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="demo-chat-box">
            <div class="demo-chat-msg demo-chat-user shadow-sm">
              <i class="bi bi-person-circle me-1"></i> Bagaimana cara membagi gaji Rp 5.000.000 sebulan secara ideal?
            </div>
            <div class="demo-chat-msg demo-chat-ai shadow-sm">
              <div class="d-flex align-items-center gap-1 mb-1">
                <i class="bi bi-robot"></i>
                <strong>AI Kastra</strong>
              </div>
              Berdasarkan teori alokasi populer <strong>50/30/20</strong>, Anda dapat membaginya menjadi:
              <ul class="my-2 ps-3" style="font-size: 0.8rem;">
                <li><strong>Rp 2.500.000 (50%)</strong> untuk Kebutuhan Pokok (makan, sewa, tagihan).</li>
                <li><strong>Rp 1.500.000 (30%)</strong> untuk Keinginan Pribadi (hiburan, hobi, jajan).</li>
                <li><strong>Rp 1.000.000 (20%)</strong> untuk Tabungan Rencana & Dana Darurat.</li>
              </ul>
              Anda dapat mencatat dan memantau rencana anggaran ini secara otomatis melalui dasbor Kastra.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="py-5" id="harga">
    <div class="container">
      <h2 class="section-title-large">Pilihan Akses Kastra</h2>
      <p class="section-desc-large">Mulai dengan fitur dasar secara gratis atau beralih ke Premium untuk akses analisis tanpa batas.</p>
      <div class="row g-4 justify-content-center">
        <div class="col-md-5">
          <div class="pricing-card">
            <h4 class="mb-2" style="color: var(--teks-utama);">Standar</h4>
            <p class="text-muted small">Cocok untuk pencatatan transaksi harian dasar.</p>
            <div class="pricing-price">Rp 0 <span class="fs-6 text-muted font-weight-normal">/ selamanya</span></div>
            <ul class="pricing-features-list">
              <li><i class="bi bi-check-circle-fill"></i><span>Catat Transaksi Tanpa Batas</span></li>
              <li><i class="bi bi-check-circle-fill"></i><span>Maksimal 3 Target Tabungan</span></li>
              <li><i class="bi bi-check-circle-fill"></i><span>Visualisasi Laporan Bulanan</span></li>
              <li><i class="bi bi-check-circle-fill"></i><span>15 Prompt AI Per Hari</span></li>
              <li class="text-muted"><i class="bi bi-x-circle-fill text-danger"></i><span>Tanpa Analisis AI Mendalam</span></li>
            </ul>
            <?php if ($is_logged_in): ?>
              <a href="<?= url('user/dashboard.php') ?>" class="btn-pricing-cta text-decoration-none">Gunakan Paket Sekarang</a>
            <?php else: ?>
              <a href="<?= url('auth/register.php') ?>" class="btn-pricing-cta text-decoration-none">Daftar Akun Gratis</a>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-md-5">
          <div class="pricing-card pricing-card-premium">
            <h4 class="text-white mb-2">Premium Pro</h4>
            <p class="text-white-50 small">Akses penuh ke semua analisis finansial AI dan pengelolaan target tanpa kuota.</p>
            <div class="pricing-price">
              <span class="pricing-price-original">Rp 7.000</span>
              <span>Rp 2.000</span>
              <span class="fs-6 text-white-50 font-weight-normal">/ sekali bayar</span>
            </div>
            <ul class="pricing-features-list">
              <li><i class="bi bi-check-circle-fill text-success"></i><span>Semua Fitur Standar Termasuk</span></li>
              <li><i class="bi bi-check-circle-fill text-success"></i><span>Target Tabungan Tanpa Batas</span></li>
              <li><i class="bi bi-check-circle-fill text-success"></i><span>Visualisasi Laporan Kustom</span></li>
              <li><i class="bi bi-check-circle-fill text-success"></i><span>Prompt AI Tanpa Batas</span></li>
              <li><i class="bi bi-check-circle-fill text-success"></i><span>Rekomendasi AI Terpersonalisasi</span></li>
            </ul>
            <?php if ($is_logged_in): ?>
              <a href="<?= url('user/upgrade_premium.php') ?>" class="btn-pricing-cta btn-pricing-cta-premium text-decoration-none">Tingkatkan Akun Sekarang</a>
            <?php else: ?>
              <a href="<?= url('auth/register.php') ?>" class="btn-pricing-cta btn-pricing-cta-premium text-decoration-none">Mulai Premium Sekarang</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="py-5" id="ulasan" style="background: #ffffff; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
      <h2 class="section-title-large">Apa Kata Mereka?</h2>
      <p class="section-desc-large">Bergabunglah dengan ratusan pengguna lain yang telah terbantu oleh asisten finansial Kastra.</p>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="p-4 rounded-3 h-100" style="background: var(--dark-bg); border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div class="d-flex gap-1 text-warning mb-3">
              <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
            </div>
            <p class="text-muted small mb-4">"Fitur target tabungan sangat membantu! Visualisasinya membuat saya lebih termotivasi. AI-nya juga sangat natural saat ditanya soal saran pembagian gaji."</p>
            <div class="d-flex align-items-center gap-3 mt-auto">
              <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px; background: linear-gradient(135deg, #4f46e5, #3b82f6);">B</div>
              <div>
                <h6 class="mb-0 text-dark" style="font-size: 0.95rem;">Budi Santoso</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Pengguna Premium</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-4 rounded-3 h-100" style="background: var(--dark-bg); border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div class="d-flex gap-1 text-warning mb-3">
              <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
            </div>
            <p class="text-muted small mb-4">"Awalnya ragu, tapi ternyata antarmukanya sangat elegan dan gampang dipahami. Paling suka karena gak lemot walau nyatet banyak transaksi tiap hari."</p>
            <div class="d-flex align-items-center gap-3 mt-auto">
              <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px; background: linear-gradient(135deg, #10b981, #059669);">A</div>
              <div>
                <h6 class="mb-0 text-dark" style="font-size: 0.95rem;">Anita Wijaya</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Mahasiswa</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-4 rounded-3 h-100" style="background: var(--dark-bg); border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div class="d-flex gap-1 text-warning mb-3">
              <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
            </div>
            <p class="text-muted small mb-4">"Pembayaran QRIS-nya instan banget! Langsung otomatis aktif akun premiumnya gak sampai 5 detik. Jauh lebih baik dari aplikasi pencatat lain."</p>
            <div class="d-flex align-items-center gap-3 mt-auto">
              <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px; background: linear-gradient(135deg, #f59e0b, #d97706);">D</div>
              <div>
                <h6 class="mb-0 text-dark" style="font-size: 0.95rem;">Dimas Pratama</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Pekerja Lepas (Freelancer)</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="py-5" style="background: radial-gradient(circle at 50% 50%, rgba(79, 70, 229, 0.06) 0%, rgba(255, 255, 255, 0) 70%);">
    <div class="container text-center py-4">
      <h2 class="mb-3">Mulai Kelola Keuangan Anda</h2>
      <p class="text-muted mx-auto mb-4" style="max-width: 500px;">Gunakan Kastra untuk memantau arus kas dan menyusun rencana finansial yang lebih baik.</p>
      <?php if ($is_logged_in): ?>
        <a href="<?= url('user/dashboard.php') ?>" class="btn-mulai btn-lg text-decoration-none px-4 py-3">Buka Dashboard Anda</a>
      <?php else: ?>
        <a href="<?= url('auth/register.php') ?>" class="btn-mulai btn-lg text-decoration-none px-4 py-3">Daftar Akun Gratis Sekarang</a>
      <?php endif; ?>
    </div>
  </section>
  <footer class="footer-kastra">
    <div class="container">
      <div class="row g-4 mb-4">
        <div class="col-lg-4">
          <div class="footer-brand d-flex align-items-center gap-2">
            <img src="<?= url('assets/image/logo.jpeg') ?>" alt="Logo Kastra" style="border-radius: 8px;" width="28" height="28">
            <span>Kastra</span>
          </div>
          <p class="small text-muted mb-0">Platform manajemen keuangan pribadi bertenaga AI yang membantu pencatatan, pemantauan tabungan, dan keputusan finansial yang bijaksana.</p>
        </div>
        <div class="col-6 col-lg-2 offset-lg-2">
          <h6 class="mb-3" style="color: var(--teks-utama);">Tautan Cepat</h6>
          <ul class="list-unstyled d-flex flex-column gap-2 small">
            <li><a href="#fitur" class="text-muted text-decoration-none">Fitur Utama</a></li>
            <li><a href="#demo" class="text-muted text-decoration-none">Demo AI</a></li>
            <li><a href="#harga" class="text-muted text-decoration-none">Harga Paket</a></li>
            <li><a href="#ulasan" class="text-muted text-decoration-none">Ulasan</a></li>
          </ul>
        </div>
        <div class="col-6 col-lg-2">
          <h6 class="mb-3" style="color: var(--teks-utama);">Aplikasi</h6>
          <ul class="list-unstyled d-flex flex-column gap-2 small">
            <li><a href="<?= url('auth/login.php') ?>" class="text-muted text-decoration-none">Masuk Akun</a></li>
            <li><a href="<?= url('auth/register.php') ?>" class="text-muted text-decoration-none">Daftar Baru</a></li>
          </ul>
        </div>
        <div class="col-lg-2">
          <h6 class="mb-3" style="color: var(--teks-utama);">Legalitas</h6>
          <ul class="list-unstyled d-flex flex-column gap-2 small">
            <li><span class="text-muted">Kebijakan Privasi</span></li>
            <li><span class="text-muted">Ketentuan Layanan</span></li>
          </ul>
        </div>
      </div>
      <div class="border-top pt-3 text-center small text-muted" style="border-color: #e2e8f0 !important;">
        <p class="mb-0">&copy; <?= date('Y') ?> Kastra. Hak Cipta Dilindungi Undang-Undang.</p>
      </div>
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Menutup menu navbar mobile secara otomatis saat link diklik
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
      link.addEventListener('click', () => {
        const navbarCollapse = document.getElementById('navbarText');
        if (navbarCollapse.classList.contains('show')) {
          // Menggunakan API Bootstrap Collapse
          const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse) || new bootstrap.Collapse(navbarCollapse, { toggle: false });
          bsCollapse.hide();
        }
      });
    });
  </script>
</body>
</html>
