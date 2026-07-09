<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
require_once __DIR__ . '/../includes/pakasir_payment.php';
wajib_login();
$pakasir = require __DIR__ . '/../config/pakasir.php';
$id_user = (int) $_SESSION['id_user'];
$pesan = '';
$error = '';
$key_config = $pakasir['api_key'] ?? '';
$slug_config = $pakasir['project_slug'] ?? '';
$config_invalid = (
    empty($key_config) ||
    empty($slug_config) ||
    $key_config === 'ISI_API_KEY_PAKASIR_DI_SINI' ||
    $key_config === 'ISI_API_KEY_PAKASIR' ||
    $key_config === '5rNf7vksuhV55CCtNPc53SlNMSJpvsIt' ||
    $slug_config === 'ISI_PROJECT_SLUG_PAKASIR_DI_SINI' ||
    $slug_config === 'ISI_PROJECT_SLUG_PAKASIR'
);
$pengaturan_sistem = ambil_pengaturan_sistem($koneksi);
$harga_premium = max(1000, (int) ($pengaturan_sistem['harga_premium'] ?? 2000));
try { $koneksi->exec("ALTER TABLE transaksi_premium ADD COLUMN payment_number TEXT NULL"); } catch (Throwable $e) {}
try { $koneksi->exec("ALTER TABLE transaksi_premium ADD COLUMN total_payment INT NULL"); } catch (Throwable $e) {}
$koneksi->prepare("UPDATE transaksi_premium SET status='expired' WHERE id_user=:id_user AND status='pending' AND expired_at < :now")
    ->execute(['id_user' => $id_user, 'now' => date('Y-m-d H:i:s')]);
$status_akun = sinkron_status_premium($koneksi, $id_user);
$status_user = $status_akun['status_user'] ?? 'biasa';
$_SESSION['status_user'] = $status_user;
$query_user = $koneksi->prepare('SELECT sisa_prompt FROM users WHERE id_user = :id_user LIMIT 1');
$query_user->execute(['id_user' => $id_user]);
$sisa_prompt = (int) (($query_user->fetch()['sisa_prompt'] ?? 0));
function buat_kode_invoice(): string
{
    return 'KAS-INV-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));
}
function kolom_transaksi_premium_ada(PDO $koneksi, string $namaKolom): bool
{
    static $cache = [];
    if (array_key_exists($namaKolom, $cache)) {
        return $cache[$namaKolom];
    }
    $q = $koneksi->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transaksi_premium' AND COLUMN_NAME = :kolom");
    $q->execute(['kolom' => $namaKolom]);
    $cache[$namaKolom] = ((int) $q->fetchColumn() > 0);
    return $cache[$namaKolom];
}
function pesan_error_gateway(string $rawError = ''): string
{
    $error = strtolower(trim($rawError));
    if ($error === '') {
        return 'Gagal memproses permintaan pembayaran.';
    }
    if (strpos($error, 'curl extension tidak aktif') !== false) {
        return 'Layanan pembayaran belum siap: cURL belum aktif di server.';
    }
    if (strpos($error, 'timed out') !== false || strpos($error, 'timeout') !== false) {
        return 'Gateway pembayaran sedang sibuk. Silakan coba beberapa saat lagi.';
    }
    if (strpos($error, 'could not resolve host') !== false || strpos($error, 'failed to connect') !== false) {
        return 'Tidak dapat terhubung ke gateway pembayaran.';
    }
    if (strpos($error, 'api key') !== false || strpos($error, 'invalid key') !== false) {
        return 'Konfigurasi API key pembayaran tidak valid.';
    }
    if (strpos($error, 'project') !== false) {
        return 'Konfigurasi project pembayaran tidak valid.';
    }
    return 'Gagal memproses permintaan pembayaran.';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array(($_POST['aksi'] ?? ''), ['upgrade', 'buat_ulang'], true)) {
    if ($status_user === 'premium') {
        $pesan = 'Akun Anda sudah premium.';
    } elseif ($config_invalid) {
        $error = 'Konfigurasi pembayaran Pakasir belum diatur dengan benar. Silakan atur PAKASIR_PROJECT dan PAKASIR_API_KEY di file .env Anda terlebih dahulu.';
    } else {
        try {
        if (($_POST['aksi'] ?? '') === 'buat_ulang') {
            $invoice_lama = trim((string) ($_POST['invoice_lama'] ?? ''));
            if ($invoice_lama !== '') {
                $koneksi->prepare("UPDATE transaksi_premium SET status='expired' WHERE id_user=:id_user AND kode_invoice=:kode_invoice AND status='pending'")
                    ->execute(['id_user' => $id_user, 'kode_invoice' => $invoice_lama]);
            }
        }
        $cek_pending = $koneksi->prepare("SELECT kode_invoice FROM transaksi_premium WHERE id_user=:id_user AND status='pending' AND expired_at > :now ORDER BY id_transaksi DESC LIMIT 1");
        $cek_pending->execute(['id_user' => $id_user, 'now' => date('Y-m-d H:i:s')]);
        $pending = $cek_pending->fetch();
        if ($pending) {
            $error = 'Gagal memproses permintaan. Silakan selesaikan pembayaran sebelumnya.';
        } else {
            $kode_invoice = buat_kode_invoice();
            $expired_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $kolom = ['id_user', 'jumlah', 'status'];
            $nilai = [':id_user', ':jumlah', "'pending'"];
            $params = [
                'id_user' => $id_user,
                'jumlah' => $harga_premium,
            ];
            if (kolom_transaksi_premium_ada($koneksi, 'kode_invoice')) {
                $kolom[] = 'kode_invoice';
                $nilai[] = ':kode_invoice';
                $params['kode_invoice'] = $kode_invoice;
            }
            if (kolom_transaksi_premium_ada($koneksi, 'kode_pembayaran')) {
                $kolom[] = 'kode_pembayaran';
                $nilai[] = ':kode_pembayaran';
                $params['kode_pembayaran'] = $kode_invoice;
            }
            if (kolom_transaksi_premium_ada($koneksi, 'waktu_buat')) {
                $kolom[] = 'waktu_buat';
                $nilai[] = 'NOW()';
            }
            if (kolom_transaksi_premium_ada($koneksi, 'tanggal')) {
                $kolom[] = 'tanggal';
                $nilai[] = 'NOW()';
            }
            if (kolom_transaksi_premium_ada($koneksi, 'expired_at')) {
                $kolom[] = 'expired_at';
                $nilai[] = ':expired_at';
                $params['expired_at'] = $expired_at;
            }
            $sqlInsert = 'INSERT INTO transaksi_premium (' . implode(',', $kolom) . ') VALUES (' . implode(',', $nilai) . ')';
            $simpan = $koneksi->prepare($sqlInsert);
            $simpan->execute($params);
            $hasil_api = pakasir_create_qris($pakasir, $kode_invoice, $harga_premium);
            if (!$hasil_api['ok']) {
                $koneksi->prepare("UPDATE transaksi_premium SET status='gagal' WHERE kode_invoice=:kode_invoice")
                    ->execute(['kode_invoice' => $kode_invoice]);
                $rawErr = (string) ($hasil_api['error'] ?? '');
                $error = pesan_error_gateway($rawErr);
                $httpCode = (int) ($hasil_api['http_code'] ?? 0);
                $rawBody = isset($hasil_api['raw']) ? json_encode($hasil_api['raw']) : '';
                error_log('Pakasir create failed invoice=' . $kode_invoice . ' http=' . $httpCode . ' err=' . $rawErr . ' raw=' . $rawBody);
            } else {
                $metode = trim((string) ($hasil_api['payment_method'] ?? 'qris'));
                $expired_api = trim((string) ($hasil_api['expired_at'] ?? ''));
                $expired_fix = $expired_api !== '' ? date('Y-m-d H:i:s', strtotime($expired_api)) : $expired_at;
                $payment_number = trim((string) ($hasil_api['payment_number'] ?? ''));
                if ($payment_number === '') {
                    $detail_awal = pakasir_transaction_detail($pakasir, $kode_invoice, $harga_premium);
                    if ($detail_awal['ok']) {
                        $payment_number = pakasir_extract_payment_number((array) ($detail_awal['transaction'] ?? []));
                    }
                }
                $total_payment = (int) ($hasil_api['total_payment'] ?? $harga_premium);
                try {
                    $koneksi->prepare('UPDATE transaksi_premium SET metode_pembayaran=:metode_pembayaran, expired_at=:expired_at, payment_number=:payment_number, total_payment=:total_payment WHERE kode_invoice=:kode_invoice')
                        ->execute([
                            'metode_pembayaran' => $metode !== '' ? $metode : null,
                            'expired_at' => $expired_fix,
                            'payment_number' => $payment_number !== '' ? $payment_number : null,
                            'total_payment' => $total_payment > 0 ? $total_payment : $harga_premium,
                            'kode_invoice' => $kode_invoice,
                        ]);
                } catch (Throwable $e) {
                    $koneksi->prepare('UPDATE transaksi_premium SET metode_pembayaran=:metode_pembayaran, expired_at=:expired_at WHERE kode_invoice=:kode_invoice')
                        ->execute([
                            'metode_pembayaran' => $metode !== '' ? $metode : null,
                            'expired_at' => $expired_fix,
                            'kode_invoice' => $kode_invoice,
                        ]);
                }
                header('Location: ' . url('user/upgrade_premium.php?invoice=' . urlencode($kode_invoice)));
                exit;
            }
        }
        } catch (Throwable $e) {
            error_log('Upgrade premium error user=' . $id_user . ' msg=' . $e->getMessage());
            $error = 'Gagal memproses permintaan.';
        }
    }
}
if (($_GET['status'] ?? '') === 'return') {
    $pesan = 'Pembayaran sedang diverifikasi.';
}
$query_transaksi = $koneksi->prepare('SELECT * FROM transaksi_premium WHERE id_user = :id_user ORDER BY waktu_buat DESC LIMIT 10');
$query_transaksi->execute(['id_user' => $id_user]);
$transaksi_premium = $query_transaksi->fetchAll();
$transaksi_pending = null;
$invoice_aktif = trim((string) ($_GET['invoice'] ?? ''));
if ($invoice_aktif !== '') {
    $q_pending = $koneksi->prepare("SELECT * FROM transaksi_premium WHERE id_user=:id_user AND kode_invoice=:kode_invoice LIMIT 1");
    $q_pending->execute(['id_user' => $id_user, 'kode_invoice' => $invoice_aktif]);
    $transaksi_pending = $q_pending->fetch() ?: null;
}
if (!$transaksi_pending) {
    $q_pending = $koneksi->prepare("SELECT * FROM transaksi_premium WHERE id_user=:id_user AND status='pending' AND expired_at > :now ORDER BY id_transaksi DESC LIMIT 1");
    $q_pending->execute(['id_user' => $id_user, 'now' => date('Y-m-d H:i:s')]);
    $transaksi_pending = $q_pending->fetch() ?: null;
}
if ($transaksi_pending && ($transaksi_pending['status'] === 'pending' || $transaksi_pending['status'] === 'expired')) {
    $detail_qr = pakasir_transaction_detail($pakasir, (string) $transaksi_pending['kode_invoice'], (int) $transaksi_pending['jumlah']);
    if ($detail_qr['ok']) {
        $remote_status = strtolower((string) ($detail_qr['transaction']['status'] ?? 'pending'));
        if ($remote_status === 'completed') {
            $trx_completed_at = $detail_qr['transaction']['completed_at'] ?? null;
            $trx_payment_method = $detail_qr['transaction']['payment_method'] ?? null;
            $koneksi->beginTransaction();
            try {
                $waktu_bayar = !empty($trx_completed_at) ? date('Y-m-d H:i:s', strtotime((string)$trx_completed_at)) : date('Y-m-d H:i:s');
                $koneksi->prepare("UPDATE transaksi_premium
                    SET status='berhasil', metode_pembayaran=:metode_pembayaran, waktu_bayar=:waktu_bayar
                    WHERE id_transaksi=:id_transaksi AND status IN ('pending', 'expired')")
                    ->execute([
                        'metode_pembayaran' => !empty($trx_payment_method) ? $trx_payment_method : 'qris',
                        'waktu_bayar' => $waktu_bayar,
                        'id_transaksi' => $transaksi_pending['id_transaksi'],
                    ]);
                $koneksi->prepare("UPDATE users SET status_user='premium', sisa_prompt=-1, premium_expired=NULL WHERE id_user=:id_user")
                    ->execute(['id_user' => $id_user]);
                $koneksi->commit();
                $status_user = 'premium';
                $_SESSION['status_user'] = 'premium';
                $transaksi_pending = null;
            } catch (Throwable $e) {
                if ($koneksi->inTransaction()) {
                    $koneksi->rollBack();
                }
                error_log('Premium status inline upgrade failed: ' . $e->getMessage());
            }
        } elseif (in_array($remote_status, ['failed', 'expired', 'cancelled'], true)) {
            $status_akhir = $remote_status === 'expired' ? 'expired' : 'gagal';
            $koneksi->prepare("UPDATE transaksi_premium SET status=:status WHERE id_transaksi=:id_transaksi AND status IN ('pending', 'expired')")
                ->execute([
                    'status' => $status_akhir,
                    'id_transaksi' => $transaksi_pending['id_transaksi'],
                ]);
            $transaksi_pending['status'] = $status_akhir;
        } else {
            if (empty($transaksi_pending['payment_number'])) {
                $payment_number_fix = pakasir_extract_payment_number((array) ($detail_qr['transaction'] ?? []));
                if ($payment_number_fix !== '') {
                    $koneksi->prepare('UPDATE transaksi_premium SET payment_number=:payment_number WHERE id_transaksi=:id_transaksi')
                        ->execute([
                            'payment_number' => $payment_number_fix,
                            'id_transaksi' => $transaksi_pending['id_transaksi'],
                        ]);
                    $transaksi_pending['payment_number'] = $payment_number_fix;
                }
            }
        }
    }
}
$judul_halaman = 'Tingkatkan Premium';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
include __DIR__ . '/../partials/topbar.php';
?>
<div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-3">
  <div>
    <h2 class="mb-1" style="font-size:24px;">Akses Premium</h2>
    <p class="text-muted mb-0">Akses fitur analisis keuangan bertenaga AI dan pengelolaan target tanpa batasan kuota.</p>
  </div>
</div>
<?php if ($pesan): ?><div class="alert alert-info border-0"><?= h($pesan) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger border-0"><?= h($error) ?></div><?php endif; ?>
<div class="row g-3">
  <div class="<?= $transaksi_pending ? 'col-xl-7' : 'col-xl-8' ?>">
    <div class="premium-card">
      <div class="premium-badge">
        <i class="bi bi-gem"></i>
        <span>Kastra Pro</span>
      </div>
      <h3 class="premium-title">Keunggulan Premium</h3>
      <p class="premium-desc">Dapatkan analisis arus kas bulanan secara rinci, pembuatan target tabungan tanpa batas, serta konsultasi AI tanpa batasan kuota harian.</p>
      <div class="premium-price-box">
        <span class="premium-price-original"><?= h(format_rupiah(7000)) ?></span>
        <span class="premium-price"><?= h(format_rupiah((float) $harga_premium)) ?></span>
        <span class="premium-price-period">/ akses selamanya</span>
      </div>
      <ul class="premium-features">
        <li><i class="bi bi-patch-check-fill"></i><span>Akses asisten AI finansial tanpa batas</span></li>
        <li><i class="bi bi-patch-check-fill"></i><span>Rekomendasi alokasi anggaran terpersonalisasi</span></li>
        <li><i class="bi bi-patch-check-fill"></i><span>Dukungan pengelolaan target tabungan tanpa batas</span></li>
        <li><i class="bi bi-patch-check-fill"></i><span>Proses aktivasi instan sekali bayar</span></li>
      </ul>
      <?php if ($status_user === 'premium'): ?>
        <div class="alert alert-success border-0 mb-0 d-flex align-items-center gap-2" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.25); color: #34d399;">
          <i class="bi bi-patch-check-fill fs-5"></i>
          <div>Status Anda saat ini adalah <strong>Kastra Premium</strong>. Akses penuh telah aktif!</div>
        </div>
      <?php elseif ($config_invalid): ?>
        <div class="alert alert-warning border-0 mb-0 d-flex align-items-start gap-2" style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.25); color: #fbbf24;">
          <i class="bi bi-exclamation-triangle-fill fs-5"></i>
          <div>
            <strong>Integrasi Pembayaran Belum Siap:</strong><br>
            Konfigurasi API Key atau Project Slug Pakasir belum diatur atau masih default. Silakan perbarui file <code>config/app.php</code> atau <code>config/pakasir.php</code> di hosting dengan kredensial dari dashboard Pakasir Anda terlebih dahulu.
          </div>
        </div>
      <?php else: ?>
        <form method="post">
          <input type="hidden" name="aksi" value="upgrade">
          <button class="btn-premium-cta" type="submit">
            <span>Tingkatkan Ke Premium</span>
            <i class="bi bi-arrow-right-short fs-4"></i>
          </button>
        </form>
        <div class="premium-trust-badge">
          <div class="premium-trust-item"><i class="bi bi-shield-lock-fill text-warning"></i><span>Pembayaran Aman</span></div>
          <div class="premium-trust-item"><i class="bi bi-lightning-charge-fill text-warning"></i><span>Aktivasi Instan</span></div>
          <div class="premium-trust-item"><i class="bi bi-check-circle-fill text-warning"></i><span>Sekali Bayar</span></div>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="<?= $transaksi_pending ? 'col-xl-5' : 'col-xl-4' ?>">
    <?php if ($transaksi_pending): ?>
      <?php
        $expired_ts = strtotime((string) $transaksi_pending['expired_at']);
        $detik_sisa = max(0, $expired_ts - time());
        $total_bayar = (int) ($transaksi_pending['total_payment'] ?? $transaksi_pending['jumlah']);
      ?>
      <div class="card-modern qris-pay-card" id="cardPembayaranQR"
           data-invoice="<?= h($transaksi_pending['kode_invoice']) ?>"
           data-expired-seconds="<?= (int) $detik_sisa ?>">
        <div class="qris-pay-header">
          <h6 class="mb-1 text-white"><i class="bi bi-qr-code-scan me-2"></i>Pembayaran QRIS</h6>
          <p class="text-white-50 small mb-0">Pindai kode QR untuk menyelesaikan pembayaran.</p>
        </div>
        <div class="p-3 text-center bg-light-subtle">
          <div id="qrisCanvas" class="qris-scanner-box mb-2"></div>
          <div id="labelQrInfo" class="small text-muted mt-1">Pindai untuk membayar otomatis</div>
        </div>
        <div class="p-3 border-top">
          <div class="detail-row">
            <span class="text-muted">Faktur</span>
            <strong><?= h($transaksi_pending['kode_invoice']) ?></strong>
          </div>
          <div class="detail-row">
            <span class="text-muted">Total Bayar</span>
            <strong class="text-primary"><?= h(format_rupiah((float) $total_bayar)) ?></strong>
          </div>
          <div class="detail-row">
            <span class="text-muted">Status</span>
            <span id="statusPembayaran" class="badge-status badge-biasa"><?= h(status_label_premium((string) $transaksi_pending['status'])) ?></span>
          </div>
          <div class="detail-row">
            <span class="text-muted">Sisa Waktu</span>
            <strong id="countdownPembayaran" class="text-danger">--:--</strong>
          </div>
          <div class="mt-3 d-flex gap-2 flex-wrap">
            <a href="<?= url('user/upgrade_premium.php') ?>" class="btn btn-light btn-sm w-100"><i class="bi bi-arrow-clockwise me-1"></i>Perbarui Data</a>
            <?php if (!$config_invalid): ?>
            <form method="post" class="w-100" id="formBuatUlang" style="display:none;">
              <input type="hidden" name="aksi" value="buat_ulang">
              <input type="hidden" name="invoice_lama" value="<?= h((string) $transaksi_pending['kode_invoice']) ?>">
              <button class="btn btn-utama btn-sm w-100" type="submit"><i class="bi bi-plus-circle me-1"></i>Buat Ulang Pembayaran</button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
      <script>
      (function () {
        const card = document.getElementById('cardPembayaranQR');
        if (!card) return;
        const invoice = card.getAttribute('data-invoice') || '';
        let detikSisa = parseInt(card.getAttribute('data-expired-seconds') || '0', 10);
        const statusEl = document.getElementById('statusPembayaran');
        const countdownEl = document.getElementById('countdownPembayaran');
        const qrisCanvas = document.getElementById('qrisCanvas');
        const labelQrInfo = document.getElementById('labelQrInfo');
        const formBuatUlang = document.getElementById('formBuatUlang');
        const qrString = <?= json_encode((string) ($transaksi_pending['payment_number'] ?? '')) ?>;
        let isFinished = false;
        let statusInterval = null;
        if (qrisCanvas && qrString) {
          qrisCanvas.innerHTML = '';
          new QRCode(qrisCanvas, {
            text: qrString,
            width: 220,
            height: 220,
            correctLevel: QRCode.CorrectLevel.M
          });
        } else if (labelQrInfo) {
          labelQrInfo.textContent = 'QR belum tersedia. Klik "Buat Ulang Pembayaran".';
        }
        function formatDetik(totalDetik) {
          const menit = Math.floor(totalDetik / 60);
          const detik = totalDetik % 60;
          return String(menit).padStart(2, '0') + ':' + String(detik).padStart(2, '0');
        }
        function updateCountdown() {
          if (!countdownEl) return;
          if (detikSisa <= 0) {
            countdownEl.textContent = '00:00';
            if (statusEl) {
              statusEl.textContent = 'QR Kadaluarsa';
              statusEl.className = 'badge-status badge-keluar';
            }
            if (qrisCanvas) {
              qrisCanvas.classList.remove('qris-scanner-container');
            }
            if (formBuatUlang) {
              formBuatUlang.style.display = 'inline-block';
            }
            return;
          }
          countdownEl.textContent = formatDetik(detikSisa);
          detikSisa--;
        }
        async function cekStatus() {
          if (!invoice || isFinished) return;
          try {
            const response = await fetch(<?= json_encode(url('user/premium_status.php')) ?> + '?invoice=' + encodeURIComponent(invoice), {
              headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            if (!data.ok) return;
            let label = data.label_status || 'Menunggu pembayaran';
            let badge = data.badge_status || 'badge-biasa';
            if (data.status === 'pending' && data.remote_status === 'completed') {
              label = 'Pembayaran diterima, menunggu verifikasi.';
            }
            if (statusEl) {
              statusEl.textContent = label;
              statusEl.className = 'badge-status ' + badge;
            }
            if (data.status === 'berhasil') {
              isFinished = true;
              if (statusInterval) clearInterval(statusInterval);
              if (qrisCanvas) {
                qrisCanvas.classList.remove('qris-scanner-container');
                qrisCanvas.innerHTML = '<div style="width: 220px; height: 220px; display: flex; align-items: center; justify-content: center; animation: scale-in 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275);"><i class="bi bi-check-circle-fill" style="font-size: 80px; color: var(--sukses);"></i></div>';
              }
              if (labelQrInfo) {
                labelQrInfo.innerHTML = '<span class="text-success font-weight-bold">Pembayaran Diterima!</span>';
              }
              if (typeof tampilToast === 'function') {
                tampilToast('Pembayaran berhasil', 'sukses');
              }
              setTimeout(function () {
                window.location.href = <?= json_encode(url('user/dashboard.php?premium=aktif')) ?>;
              }, 1500);
            } else if (data.status === 'expired' || data.status === 'gagal') {
              isFinished = true;
              if (statusInterval) clearInterval(statusInterval);
              if (formBuatUlang) {
                formBuatUlang.style.display = 'inline-block';
              }
              if (qrisCanvas) {
                qrisCanvas.classList.remove('qris-scanner-container');
              }
              if (typeof tampilToast === 'function') {
                tampilToast(data.status === 'expired' ? 'Silakan coba kembali' : 'Pembayaran gagal', 'gagal');
              }
            }
          } catch (e) {
          }
        }
        updateCountdown();
        setInterval(updateCountdown, 1000);
        statusInterval = setInterval(cekStatus, 2000); // Polling status every 2 seconds for a smoother refresh rate
      })();
      </script>
    <?php else: ?>
      <div class="card-modern p-4 d-flex flex-column justify-content-between h-100">
        <div>
          <h6 class="mb-3 text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em; font-weight: 700;">Status Akun Anda</h6>
          <div class="status-badge-container mb-3">
            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: rgba(79, 70, 229, 0.1); color: var(--primary);">
              <i class="bi <?= $status_user === 'premium' ? 'bi-gem text-warning' : 'bi-person-fill' ?> fs-5"></i>
            </div>
            <div>
              <div class="small text-muted" style="font-size: 0.75rem;">Paket Saat Ini</div>
              <strong style="font-size: 1.05rem;" class="<?= $status_user === 'premium' ? 'text-warning' : '' ?>"><?= h(ucfirst($status_user)) ?></strong>
            </div>
          </div>
          <div class="detail-row">
            <span class="text-muted">Sisa Prompt AI</span>
            <strong><?= $status_user === 'premium' ? 'Tanpa batas' : (int) $sisa_prompt ?></strong>
          </div>
        </div>
        <?php if ($status_user !== 'premium'): ?>
          <div class="mt-4 pt-3 border-top small text-muted text-center">
            <i class="bi bi-info-circle me-1"></i> Tipe Akun Biasa dibatasi maksimal 15 prompt AI per hari.
          </div>
        <?php else: ?>
          <div class="mt-4 pt-3 border-top small text-success text-center">
            <i class="bi bi-check-circle-fill me-1"></i> Selamat! Anda dapat menikmati semua fitur tanpa batas.
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
<div class="card-modern p-3 mt-3">
  <h6 class="mb-3">Riwayat Transaksi Premium</h6>
  <div class="table-responsive">
    <table class="table table-clean mb-0">
      <thead><tr><th>Invoice</th><th>Status</th><th>Jumlah</th><th>Dibuat</th><th>Dibayar</th><th>Kadaluarsa</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($transaksi_premium as $item): ?>
        <tr>
          <td><?= h($item['kode_invoice']) ?></td>
          <td>
            <?php
              $label = status_label_premium((string) $item['status']);
            ?>
            <span class="badge-status <?= $item['status'] === 'berhasil' ? 'badge-masuk' : ($item['status'] === 'gagal' || $item['status'] === 'expired' ? 'badge-keluar' : 'badge-biasa') ?>"><?= h($label) ?></span>
          </td>
          <td><?= h(format_rupiah((float) $item['jumlah'])) ?></td>
          <td><?= h($item['waktu_buat']) ?></td>
          <td><?= h($item['waktu_bayar'] ?: '-') ?></td>
          <td><?= h($item['expired_at']) ?></td>
          <td>
            <?php if ($item['status'] === 'pending'): ?>
              <a class="btn btn-sm btn-light" href="<?= url('user/upgrade_premium.php?invoice=' . urlencode((string) $item['kode_invoice'])) ?>">Cek Status</a>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
