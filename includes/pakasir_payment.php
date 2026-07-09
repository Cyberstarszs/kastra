<?php
if (!function_exists('pakasir_api_base_url')) {
    function pakasir_api_base_url(array $pakasir): string
    {
        return rtrim((string) ($pakasir['base_url'] ?? 'https://app.pakasir.com'), '/');
    }
}
if (!function_exists('pakasir_call')) {
    function pakasir_call(string $method, string $url, ?array $payload = null): array
    {
        if (!function_exists('curl_init')) {
            return ['ok' => false, 'error' => 'cURL extension tidak aktif di server'];
        }
        $ch = curl_init($url);
        $headers = ['Accept: application/json'];
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => $headers,
        ];
        if ($payload !== null) {
            $jsonPayload = json_encode($payload);
            if ($jsonPayload === false) {
                return ['ok' => false, 'error' => 'Gagal encode payload'];
            }
            $headers[] = 'Content-Type: application/json';
            $options[CURLOPT_HTTPHEADER] = $headers;
            $options[CURLOPT_POSTFIELDS] = $jsonPayload;
        }
        curl_setopt_array($ch, $options);
        $raw = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            return ['ok' => false, 'error' => $err, 'http_code' => $httpCode];
        }
        $json = json_decode((string) $raw, true);
        if (!is_array($json)) {
            return ['ok' => false, 'error' => 'Respon API tidak valid', 'http_code' => $httpCode, 'raw' => (string) $raw];
        }
        return ['ok' => true, 'http_code' => $httpCode, 'data' => $json];
    }
}
if (!function_exists('pakasir_get_message')) {
    function pakasir_get_message(array $data): string
    {
        $candidates = [
            $data['message'] ?? null,
            $data['msg'] ?? null,
            $data['error'] ?? null,
            $data['errors'] ?? null,
        ];
        if (isset($data['data']) && is_array($data['data'])) {
            $candidates[] = $data['data']['message'] ?? null;
            $candidates[] = $data['data']['msg'] ?? null;
            $candidates[] = $data['data']['error'] ?? null;
        }
        foreach ($candidates as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
            if (is_array($candidate) && !empty($candidate)) {
                $flat = trim(implode(', ', array_map('strval', $candidate)));
                if ($flat !== '') {
                    return $flat;
                }
            }
        }
        return '';
    }
}
if (!function_exists('pakasir_is_success_response')) {
    function pakasir_is_success_response(array $data, int $httpCode): bool
    {
        if ($httpCode < 200 || $httpCode >= 300) {
            return false;
        }
        foreach (['success', 'status'] as $key) {
            if (!array_key_exists($key, $data)) {
                continue;
            }
            $val = $data[$key];
            if ($val === true || $val === 1 || $val === '1') {
                return true;
            }
            if (is_string($val) && in_array(strtolower($val), ['true', 'ok', 'success', 'completed', 'berhasil'], true)) {
                return true;
            }
            if ($val === false || $val === 0 || $val === '0') {
                return false;
            }
            if (is_string($val) && in_array(strtolower($val), ['false', 'fail', 'failed', 'error', 'gagal'], true)) {
                return false;
            }
        }
        return true;
    }
}
if (!function_exists('pakasir_create_qris')) {
    function pakasir_create_qris(array $pakasir, string $orderId, int $amount): array
    {
        $url = pakasir_api_base_url($pakasir) . '/api/transactioncreate/qris';
        $payload = [
            'project' => (string) ($pakasir['project_slug'] ?? ''),
            'order_id' => $orderId,
            'amount' => $amount,
            'api_key' => (string) ($pakasir['api_key'] ?? ''),
        ];
        $res = pakasir_call('POST', $url, $payload);
        if (!$res['ok']) {
            return $res;
        }
        $httpCode = (int) ($res['http_code'] ?? 0);
        $body = (array) ($res['data'] ?? []);
        if (!pakasir_is_success_response($body, $httpCode)) {
            $msg = pakasir_get_message($body);
            return [
                'ok' => false,
                'error' => $msg !== '' ? $msg : 'Create transaksi ditolak gateway',
                'http_code' => $httpCode,
                'raw' => $body,
            ];
        }
        $payment = pakasir_extract_payment_data($body);
        if (!is_array($payment)) {
            $msg = pakasir_get_message($body);
            return [
                'ok' => false,
                'error' => $msg !== '' ? $msg : 'Data payment tidak ditemukan',
                'http_code' => $httpCode,
                'raw' => $body,
            ];
        }
        return [
            'ok' => true,
            'payment_number' => trim((string) ($payment['payment_number'] ?? '')),
            'total_payment' => (int) ($payment['total_payment'] ?? $amount),
            'expired_at' => trim((string) ($payment['expired_at'] ?? '')),
            'payment_method' => trim((string) ($payment['payment_method'] ?? 'qris')),
            'raw' => $body,
        ];
    }
}
if (!function_exists('pakasir_transaction_detail')) {
    function pakasir_transaction_detail(array $pakasir, string $orderId, int $amount): array
    {
        $query = http_build_query([
            'project' => (string) ($pakasir['project_slug'] ?? ''),
            'amount' => $amount,
            'order_id' => $orderId,
            'api_key' => (string) ($pakasir['api_key'] ?? ''),
        ]);
        $url = pakasir_api_base_url($pakasir) . '/api/transactiondetail?' . $query;
        $res = pakasir_call('GET', $url);
        if (!$res['ok']) {
            return $res;
        }
        $httpCode = (int) ($res['http_code'] ?? 0);
        $body = (array) ($res['data'] ?? []);
        if (!pakasir_is_success_response($body, $httpCode)) {
            $msg = pakasir_get_message($body);
            return [
                'ok' => false,
                'error' => $msg !== '' ? $msg : 'Detail transaksi ditolak gateway',
                'http_code' => $httpCode,
                'raw' => $body,
            ];
        }
        $trx = $body['transaction'] ?? ($body['data']['transaction'] ?? null);
        if (!is_array($trx)) {
            $msg = pakasir_get_message($body);
            return [
                'ok' => false,
                'error' => $msg !== '' ? $msg : 'Data transaction tidak ditemukan',
                'http_code' => $httpCode,
                'raw' => $body,
            ];
        }
        return ['ok' => true, 'transaction' => $trx, 'raw' => $body];
    }
}
if (!function_exists('pakasir_payment_simulation')) {
    function pakasir_payment_simulation(array $pakasir, string $orderId, int $amount): array
    {
        $url = pakasir_api_base_url($pakasir) . '/api/paymentsimulation';
        $payload = [
            'project' => (string) ($pakasir['project_slug'] ?? ''),
            'order_id' => $orderId,
            'amount' => $amount,
            'api_key' => (string) ($pakasir['api_key'] ?? ''),
        ];
        return pakasir_call('POST', $url, $payload);
    }
}
if (!function_exists('pakasir_extract_payment_data')) {
    function pakasir_extract_payment_data(array $data): ?array
    {
        if (isset($data['data']['payment']) && is_array($data['data']['payment'])) {
            return $data['data']['payment'];
        }
        if (isset($data['data']['transaction']) && is_array($data['data']['transaction'])) {
            return $data['data']['transaction'];
        }
        if (isset($data['payment']) && is_array($data['payment'])) {
            return $data['payment'];
        }
        if (isset($data['transaction']) && is_array($data['transaction'])) {
            return $data['transaction'];
        }
        return null;
    }
}
if (!function_exists('pakasir_extract_payment_number')) {
    function pakasir_extract_payment_number(array $paymentData): string
    {
        $candidates = [
            'payment_number',
            'qr_string',
            'qris_string',
            'qr_code',
            'payment_code',
            'reference_number',
        ];
        foreach ($candidates as $key) {
            $val = trim((string) ($paymentData[$key] ?? ''));
            if ($val !== '') {
                return $val;
            }
        }
        return '';
    }
}
if (!function_exists('status_label_premium')) {
    function status_label_premium(string $status): string
    {
        if ($status === 'berhasil') {
            return 'Pembayaran berhasil';
        }
        if ($status === 'gagal') {
            return 'Pembayaran gagal';
        }
        if ($status === 'expired') {
            return 'QR Kadaluarsa';
        }
        return 'Menunggu pembayaran';
    }
}
if (!function_exists('status_badge_premium')) {
    function status_badge_premium(string $status): string
    {
        if ($status === 'berhasil') {
            return 'badge-masuk';
        }
        if ($status === 'gagal' || $status === 'expired') {
            return 'badge-keluar';
        }
        return 'badge-biasa';
    }
}
