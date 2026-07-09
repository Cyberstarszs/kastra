<?php
date_default_timezone_set('Asia/Jakarta');
if (session_status() === PHP_SESSION_NONE) {
    $apakah_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') == '443');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $apakah_https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
if (!isset($konfigurasi_aplikasi)) {
    $konfigurasi_aplikasi = require __DIR__ . '/app.php';
}
if (($konfigurasi_aplikasi['app_env'] ?? 'production') === 'production') {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE & ~E_WARNING);
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}
function base_url(): string
{
    global $konfigurasi_aplikasi;
    if (!empty($konfigurasi_aplikasi['base_url'])) {
        return (string) $konfigurasi_aplikasi['base_url'];
    }
    return '/';
}
function url(string $jalur = ''): string
{
    $basis = rtrim(base_url(), '/');
    return $basis . '/' . ltrim($jalur, '/');
}
function sudah_login(): bool
{
    return isset($_SESSION['id_user']);
}
function wajib_login(): void
{
    if (!sudah_login()) {
        header('Location: ' . url('auth/login.php'));
        exit;
    }
}
function role_user(): string
{
    return (string) ($_SESSION['role'] ?? 'user');
}
function user_admin(): bool
{
    return role_user() === 'admin';
}
function wajib_admin(): void
{
    wajib_login();
    if (!user_admin()) {
        header('Location: ' . url('user/dashboard.php'));
        exit;
    }
}
function ambil_pengaturan_sistem(PDO $koneksi): array
{
    $bawaan = [
        'harga_premium' => '2000',
        'limit_prompt' => '15',
    ];
    $kueri = $koneksi->query('SELECT nama_pengaturan, nilai_pengaturan FROM pengaturan_sistem');
    $data_pengaturan = $bawaan;
    foreach ($kueri->fetchAll() as $baris) {
        $data_pengaturan[$baris['nama_pengaturan']] = $baris['nilai_pengaturan'];
    }
    return $data_pengaturan;
}
function sinkron_status_premium(PDO $koneksi, int $id_user): array
{
    $kueri = $koneksi->prepare('SELECT status_user, premium_expired FROM users WHERE id_user=:id_user LIMIT 1');
    $kueri->execute(['id_user' => $id_user]);
    $data_pengguna = $kueri->fetch() ?: ['status_user' => 'biasa', 'premium_expired' => null];
    if (($data_pengguna['status_user'] ?? 'biasa') === 'premium' && !empty($data_pengguna['premium_expired'])) {
        $kedaluwarsa = strtotime((string) $data_pengguna['premium_expired']);
        if ($kedaluwarsa !== false && $kedaluwarsa < time()) {
            $koneksi->prepare("UPDATE users SET status_user='biasa' WHERE id_user=:id_user")->execute(['id_user' => $id_user]);
            $data_pengguna['status_user'] = 'biasa';
        }
    }
    if (($data_pengguna['status_user'] ?? 'biasa') === 'biasa') {
        $kueri_transaksi = $koneksi->prepare("SELECT id_transaksi, kode_invoice, jumlah, status FROM transaksi_premium WHERE id_user=:id_user AND status IN ('pending', 'expired') ORDER BY id_transaksi DESC LIMIT 1");
        $kueri_transaksi->execute(['id_user' => $id_user]);
        $transaksi = $kueri_transaksi->fetch();
        if ($transaksi) {
            $kunci_sesi = 'last_pakasir_check_' . $transaksi['id_transaksi'];
            if (!isset($_SESSION[$kunci_sesi]) || (time() - (int)$_SESSION[$kunci_sesi]) > 15) {
                $_SESSION[$kunci_sesi] = time();
                try {
                    require_once __DIR__ . '/../includes/pakasir_payment.php';
                    $konfigurasi_pakasir = require __DIR__ . '/pakasir.php';
                    $pembayaran_pakasir = new PakasirPayment($konfigurasi_pakasir['api_key'] ?? '', $konfigurasi_pakasir['project_slug'] ?? '');
                    $detail_transaksi = pakasir_transaction_detail($pembayaran_pakasir, (string) $transaksi['kode_invoice'], (int) $transaksi['jumlah']);
                    if ($detail_transaksi['ok']) {
                        $status_pakasir = strtolower((string) ($detail_transaksi['transaction']['status'] ?? 'pending'));
                        if ($status_pakasir === 'completed') {
                            $transaksi_selesai_pada = $detail_transaksi['transaction']['completed_at'] ?? null;
                            $metode_pembayaran_transaksi = $detail_transaksi['transaction']['payment_method'] ?? null;
                            $waktu_bayar = !empty($transaksi_selesai_pada) ? date('Y-m-d H:i:s', strtotime((string)$transaksi_selesai_pada)) : date('Y-m-d H:i:s');
                            $koneksi->beginTransaction();
                            try {
                                $koneksi->prepare("UPDATE transaksi_premium
                                    SET status='berhasil', metode_pembayaran=:metode_pembayaran, waktu_bayar=:waktu_bayar
                                    WHERE id_transaksi=:id_transaksi AND status IN ('pending', 'expired')")
                                    ->execute([
                                        'metode_pembayaran' => !empty($metode_pembayaran_transaksi) ? $metode_pembayaran_transaksi : 'qris',
                                        'waktu_bayar' => $waktu_bayar,
                                        'id_transaksi' => $transaksi['id_transaksi'],
                                    ]);
                                $koneksi->prepare("UPDATE users SET status_user='premium', sisa_prompt=-1, premium_expired=NULL WHERE id_user=:id_user")
                                    ->execute(['id_user' => $id_user]);
                                $koneksi->commit();
                                $data_pengguna['status_user'] = 'premium';
                                $data_pengguna['premium_expired'] = null;
                                $_SESSION['status_user'] = 'premium';
                            } catch (Throwable $kesalahan_transaksi) {
                                if ($koneksi->inTransaction()) {
                                    $koneksi->rollBack();
                                }
                                error_log('Premium sync auto-upgrade failed: ' . $kesalahan_transaksi->getMessage());
                            }
                        }
                    }
                } catch (Throwable $kesalahan_sinkronisasi) {
                    error_log('Premium sync exception: ' . $kesalahan_sinkronisasi->getMessage());
                }
            }
        }
    }
    return $data_pengguna;
}
function h($teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}
function format_rupiah(float $nominal): string
{
    return 'Rp ' . number_format($nominal, 0, ',', '.');
}
function normalisasi_email(string $surel): string
{
    return strtolower(trim($surel));
}
function validasi_email_daftar(string $surel): bool
{
    $surel = normalisasi_email($surel);
    if ($surel === '' || strlen($surel) > 120) {
        return false;
    }
    if (!filter_var($surel, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    if (strpos($surel, '..') !== false) {
        return false;
    }
    $bagian = explode('@', $surel);
    $domain = $bagian[1] ?? '';
    if ($domain === '' || strpos($domain, '.') === false) {
        return false;
    }
    if (function_exists('checkdnsrr')) {
        $memiliki_mx = checkdnsrr($domain, 'MX');
        $memiliki_a = checkdnsrr($domain, 'A');
        $memiliki_aaaa = checkdnsrr($domain, 'AAAA');
        if (!$memiliki_mx && !$memiliki_a && !$memiliki_aaaa) {
            return false;
        }
    }
    return true;
}
function kirim_email_reset_password(string $surel_tujuan, string $nama_tujuan, string $tautan_reset): bool
{
    global $konfigurasi_aplikasi;
    $konfigurasi_email = $konfigurasi_aplikasi['mail'] ?? [];
    $surel_pengirim = (string) ($konfigurasi_email['from_email'] ?? 'no-reply@kastra.web.id');
    $nama_pengirim = (string) ($konfigurasi_email['from_name'] ?? 'Kastra');
    $metode_pengiriman = strtolower((string) ($konfigurasi_email['transport'] ?? 'mail'));
    $subjek = 'Permintaan Reset Kata Sandi - Kastra';
    $nama_aman = $nama_tujuan !== '' ? $nama_tujuan : 'Pengguna';
    $pesan = "Halo {$nama_aman},\n\n" .
        "Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda.\n" .
        "Buka tautan berikut untuk membuat kata sandi baru:\n{$tautan_reset}\n\n" .
        "Tautan ini berlaku selama 30 menit.\n" .
        "Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.\n\n" .
        "Salam,\nTim Kastra";
    if ($metode_pengiriman === 'smtp') {
        return kirim_email_smtp($konfigurasi_email, $surel_pengirim, $nama_pengirim, $surel_tujuan, $subjek, $pesan);
    }
    $header = [];
    $header[] = 'MIME-Version: 1.0';
    $header[] = 'Content-Type: text/plain; charset=UTF-8';
    $header[] = 'From: ' . $nama_pengirim . ' <' . $surel_pengirim . '>';
    $header[] = 'Reply-To: ' . $surel_pengirim;
    $teks_header = implode("\r\n", $header);
    return mail($surel_tujuan, '=?UTF-8?B?' . base64_encode($subjek) . '?=', $pesan, $teks_header);
}
function kirim_email_smtp(array $konfigurasi_email, string $surel_pengirim, string $nama_pengirim, string $surel_penerima, string $subjek, string $isi_pesan): bool
{
    $host = (string) ($konfigurasi_email['smtp_host'] ?? '');
    $port = (int) ($konfigurasi_email['smtp_port'] ?? 587);
    $nama_pengguna = (string) ($konfigurasi_email['smtp_username'] ?? '');
    $kata_sandi = (string) ($konfigurasi_email['smtp_password'] ?? '');
    $enkripsi = strtolower((string) ($konfigurasi_email['smtp_encryption'] ?? 'tls'));
    $batas_waktu = (int) ($konfigurasi_email['smtp_timeout'] ?? 20);
    if ($host === '' || $nama_pengguna === '' || $kata_sandi === '' || $port <= 0) {
        error_log('SMTP config tidak lengkap.');
        return false;
    }
    $metode_pengiriman = $enkripsi === 'ssl' ? 'ssl://' : '';
    $koneksi_socket = @fsockopen($metode_pengiriman . $host, $port, $nomor_error, $pesan_error, $batas_waktu);
    if (!$koneksi_socket) {
        error_log('SMTP connect gagal: ' . $pesan_error . ' (' . $nomor_error . ')');
        return false;
    }
    stream_set_timeout($koneksi_socket, $batas_waktu);
    if (!smtp_expect($koneksi_socket, [220])) {
        fclose($koneksi_socket);
        return false;
    }
    smtp_write($koneksi_socket, 'EHLO kastra.web.id');
    if (!smtp_expect($koneksi_socket, [250])) {
        fclose($koneksi_socket);
        return false;
    }
    if ($enkripsi === 'tls') {
        smtp_write($koneksi_socket, 'STARTTLS');
        if (!smtp_expect($koneksi_socket, [220])) {
            fclose($koneksi_socket);
            return false;
        }
        if (!stream_socket_enable_crypto($koneksi_socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            error_log('SMTP gagal mengaktifkan TLS.');
            fclose($koneksi_socket);
            return false;
        }
        smtp_write($koneksi_socket, 'EHLO kastra.web.id');
        if (!smtp_expect($koneksi_socket, [250])) {
            fclose($koneksi_socket);
            return false;
        }
    }
    smtp_write($koneksi_socket, 'AUTH LOGIN');
    if (!smtp_expect($koneksi_socket, [334])) {
        fclose($koneksi_socket);
        return false;
    }
    smtp_write($koneksi_socket, base64_encode($nama_pengguna));
    if (!smtp_expect($koneksi_socket, [334])) {
        fclose($koneksi_socket);
        return false;
    }
    smtp_write($koneksi_socket, base64_encode($kata_sandi));
    if (!smtp_expect($koneksi_socket, [235])) {
        fclose($koneksi_socket);
        return false;
    }
    smtp_write($koneksi_socket, 'MAIL FROM:<' . $surel_pengirim . '>');
    if (!smtp_expect($koneksi_socket, [250])) {
        fclose($koneksi_socket);
        return false;
    }
    smtp_write($koneksi_socket, 'RCPT TO:<' . $surel_penerima . '>');
    if (!smtp_expect($koneksi_socket, [250, 251])) {
        fclose($koneksi_socket);
        return false;
    }
    smtp_write($koneksi_socket, 'DATA');
    if (!smtp_expect($koneksi_socket, [354])) {
        fclose($koneksi_socket);
        return false;
    }
    $header = [];
    $header[] = 'Date: ' . date(DATE_RFC2822);
    $header[] = 'From: ' . $nama_pengirim . ' <' . $surel_pengirim . '>';
    $header[] = 'To: <' . $surel_penerima . '>';
    $header[] = 'Subject: =?UTF-8?B?' . base64_encode($subjek) . '?=';
    $header[] = 'MIME-Version: 1.0';
    $header[] = 'Content-Type: text/plain; charset=UTF-8';
    $header[] = 'Content-Transfer-Encoding: 8bit';
    $data_isi = implode("\r\n", $header) . "\r\n\r\n" . str_replace("\n.", "\n..", $isi_pesan) . "\r\n.";
    smtp_write($koneksi_socket, $data_isi);
    if (!smtp_expect($koneksi_socket, [250])) {
        fclose($koneksi_socket);
        return false;
    }
    smtp_write($koneksi_socket, 'QUIT');
    smtp_expect($koneksi_socket, [221]);
    fclose($koneksi_socket);
    return true;
}
function smtp_write($koneksi_socket, string $perintah): void
{
    fwrite($koneksi_socket, $perintah . "\r\n");
}
function smtp_expect($koneksi_socket, array $kode_yang_diharapkan): bool
{
    $respons = '';
    while (!feof($koneksi_socket)) {
        $baris = fgets($koneksi_socket, 515);
        if ($baris === false) {
            break;
        }
        $respons .= $baris;
        if (preg_match('/^\d{3}\s/', $baris)) {
            break;
        }
    }
    if ($respons === '') {
        error_log('SMTP tidak memberi respons.');
        return false;
    }
    $kode = (int) substr($respons, 0, 3);
    if (!in_array($kode, $kode_yang_diharapkan, true)) {
        error_log('SMTP respons tidak sesuai: ' . trim($respons));
        return false;
    }
    return true;
}
function ambil_rekap_keuangan(PDO $koneksi, int $id_user): array
{
    $kueri = $koneksi->prepare("SELECT
        COALESCE(SUM(CASE WHEN jenis_transaksi = 'pemasukan' THEN nominal END),0) AS total_pemasukan,
        COALESCE(SUM(CASE WHEN jenis_transaksi = 'pengeluaran' THEN nominal END),0) AS total_pengeluaran
        FROM transaksi WHERE id_user = :id_user");
    $kueri->execute(['id_user' => $id_user]);
    $rekap = $kueri->fetch();
    $kueri_tabungan = $koneksi->prepare('SELECT COALESCE(SUM(nominal_terkumpul),0) AS total_tabungan FROM tabungan WHERE id_user = :id_user');
    $kueri_tabungan->execute(['id_user' => $id_user]);
    $tabungan = $kueri_tabungan->fetch();
    $total_pemasukan = (float) $rekap['total_pemasukan'];
    $total_pengeluaran = (float) $rekap['total_pengeluaran'];
    return [
        'total_pemasukan' => $total_pemasukan,
        'total_pengeluaran' => $total_pengeluaran,
        'saldo_total' => $total_pemasukan - $total_pengeluaran,
        'total_tabungan' => (float) $tabungan['total_tabungan'],
    ];
}
