<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
require_once __DIR__ . '/../includes/pakasir_payment.php';
wajib_login();
header('Content-Type: application/json; charset=utf-8');
$pakasir = require __DIR__ . '/../config/pakasir.php';
$id_user = (int) $_SESSION['id_user'];
$invoice = trim((string) ($_GET['invoice'] ?? ''));
if ($invoice === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'invoice wajib diisi']);
    exit;
}
$q = $koneksi->prepare('SELECT * FROM transaksi_premium WHERE kode_invoice=:kode_invoice AND id_user=:id_user LIMIT 1');
$q->execute(['kode_invoice' => $invoice, 'id_user' => $id_user]);
$trx = $q->fetch();
if (!$trx) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'message' => 'invoice tidak ditemukan']);
    exit;
}
if ($trx['status'] === 'pending' && !empty($trx['expired_at']) && strtotime((string) $trx['expired_at']) <= time()) {
    $koneksi->prepare("UPDATE transaksi_premium SET status='expired' WHERE id_transaksi=:id_transaksi AND status='pending'")
        ->execute(['id_transaksi' => $trx['id_transaksi']]);
    $trx['status'] = 'expired';
}
$remote_status = null;
$trx_completed_at = null;
$trx_payment_method = null;
if ($trx['status'] === 'pending' || $trx['status'] === 'expired') {
    $detail = pakasir_transaction_detail($pakasir, (string) $trx['kode_invoice'], (int) $trx['jumlah']);
    if ($detail['ok']) {
        $remote_status = strtolower((string) ($detail['transaction']['status'] ?? 'pending'));
        $trx_completed_at = $detail['transaction']['completed_at'] ?? null;
        $trx_payment_method = $detail['transaction']['payment_method'] ?? null;
    }
}
$status_lokal = (string) $trx['status'];
$is_premium = false;
if ($status_lokal === 'berhasil') {
    $is_premium = true;
} elseif ($remote_status === 'completed') {
    $koneksi->beginTransaction();
    try {
        $waktu_bayar = !empty($trx_completed_at) ? date('Y-m-d H:i:s', strtotime((string)$trx_completed_at)) : date('Y-m-d H:i:s');
        $koneksi->prepare("UPDATE transaksi_premium
            SET status='berhasil', metode_pembayaran=:metode_pembayaran, waktu_bayar=:waktu_bayar
            WHERE id_transaksi=:id_transaksi AND status IN ('pending', 'expired')")
            ->execute([
                'metode_pembayaran' => !empty($trx_payment_method) ? $trx_payment_method : 'qris',
                'waktu_bayar' => $waktu_bayar,
                'id_transaksi' => $trx['id_transaksi'],
            ]);
        $koneksi->prepare("UPDATE users SET status_user='premium', sisa_prompt=-1, premium_expired=NULL WHERE id_user=:id_user")
            ->execute(['id_user' => $id_user]);
        $koneksi->commit();
        $status_lokal = 'berhasil';
        $is_premium = true;
    } catch (Throwable $e) {
        if ($koneksi->inTransaction()) {
            $koneksi->rollBack();
        }
        error_log('Premium status fallback update failed: ' . $e->getMessage());
    }
} elseif (in_array($remote_status, ['failed', 'expired', 'cancelled'], true)) {
    $status_akhir = $remote_status === 'expired' ? 'expired' : 'gagal';
    $koneksi->prepare("UPDATE transaksi_premium SET status=:status WHERE id_transaksi=:id_transaksi AND status IN ('pending', 'expired')")
        ->execute([
            'status' => $status_akhir,
            'id_transaksi' => $trx['id_transaksi'],
        ]);
    $status_lokal = $status_akhir;
}
echo json_encode([
    'ok' => true,
    'status' => $status_lokal,
    'remote_status' => $remote_status,
    'label_status' => status_label_premium($status_lokal),
    'badge_status' => status_badge_premium($status_lokal),
    'is_premium' => $is_premium,
]);
