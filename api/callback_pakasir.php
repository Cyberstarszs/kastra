<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
require_once __DIR__ . '/../includes/pakasir_payment.php';
$pakasir = require __DIR__ . '/../config/pakasir.php';
$input_raw = file_get_contents('php://input');
$payload = json_decode((string) $input_raw, true);
if (!is_array($payload)) {
    http_response_code(400);
    echo 'invalid payload';
    exit;
}
$order_id = trim((string) ($payload['order_id'] ?? ''));
$amount = (int) ($payload['amount'] ?? 0);
$project = trim((string) ($payload['project'] ?? ''));
$status = strtolower(trim((string) ($payload['status'] ?? '')));
$payment_method = trim((string) ($payload['payment_method'] ?? ''));
$completed_at = trim((string) ($payload['completed_at'] ?? ''));
$token_config = trim((string) ($pakasir['webhook_token'] ?? ''));
if ($token_config !== '') {
    $token_header = trim((string) ($_SERVER['HTTP_X_PAKASIR_TOKEN'] ?? ''));
    if (!hash_equals($token_config, $token_header)) {
        http_response_code(401);
        echo 'invalid token';
        exit;
    }
}
if ($order_id === '' || $amount <= 0) {
    http_response_code(400);
    echo 'invalid order';
    exit;
}
if ($project !== '' && $project !== (string) $pakasir['project_slug']) {
    http_response_code(400);
    echo 'invalid project';
    exit;
}
$q = $koneksi->prepare('SELECT * FROM transaksi_premium WHERE kode_invoice=:kode_invoice LIMIT 1');
$q->execute(['kode_invoice' => $order_id]);
$transaksi = $q->fetch();
if (!$transaksi) {
    http_response_code(404);
    echo 'invoice not found';
    exit;
}
if ((int) $transaksi['jumlah'] !== $amount) {
    http_response_code(400);
    echo 'invalid amount';
    exit;
}
if ($transaksi['status'] === 'berhasil') {
    http_response_code(200);
    echo 'ok';
    exit;
}
$detail = pakasir_transaction_detail($pakasir, $order_id, $amount);
if (!$detail['ok']) {
    error_log('Pakasir callback detail check failed order_id=' . $order_id . ' err=' . ($detail['error'] ?? 'unknown'));
    http_response_code(502);
    echo 'detail check failed';
    exit;
}
$trx_detail = $detail['transaction'];
$status_valid = strtolower((string) ($trx_detail['status'] ?? ''));
$amount_valid = (int) ($trx_detail['amount'] ?? 0);
$order_valid = (string) ($trx_detail['order_id'] ?? '');
if ($order_valid !== $order_id || $amount_valid !== $amount) {
    error_log('Pakasir callback mismatch detail order_id=' . $order_id);
    http_response_code(400);
    echo 'detail mismatch';
    exit;
}
if ($status === 'completed' && $status_valid === 'completed') {
    $koneksi->beginTransaction();
    try {
        $waktu_bayar = $completed_at !== '' ? date('Y-m-d H:i:s', strtotime($completed_at)) : date('Y-m-d H:i:s');
        $koneksi->prepare("UPDATE transaksi_premium
            SET status='berhasil', metode_pembayaran=:metode_pembayaran, waktu_bayar=:waktu_bayar
            WHERE id_transaksi=:id_transaksi AND status IN ('pending', 'expired')")
            ->execute([
                'metode_pembayaran' => $payment_method !== '' ? $payment_method : null,
                'waktu_bayar' => $waktu_bayar,
                'id_transaksi' => $transaksi['id_transaksi'],
            ]);
        $koneksi->prepare("UPDATE users SET status_user='premium', sisa_prompt=-1, premium_expired=NULL WHERE id_user=:id_user")
            ->execute(['id_user' => $transaksi['id_user']]);
        $koneksi->commit();
    } catch (Throwable $e) {
        if ($koneksi->inTransaction()) {
            $koneksi->rollBack();
        }
        http_response_code(500);
        echo 'update failed';
        exit;
    }
} elseif (in_array($status_valid, ['failed', 'expired', 'cancelled'], true)) {
    $status_akhir = $status_valid === 'expired' ? 'expired' : 'gagal';
    $koneksi->prepare('UPDATE transaksi_premium SET status=:status, metode_pembayaran=:metode_pembayaran WHERE id_transaksi=:id_transaksi')
        ->execute([
            'status' => $status_akhir,
            'metode_pembayaran' => $payment_method !== '' ? $payment_method : null,
            'id_transaksi' => $transaksi['id_transaksi'],
        ]);
}
http_response_code(200);
echo 'ok';
