<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function base_url(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $document_root = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
    $folder_proyek = realpath(__DIR__ . '/..') ?: '';

    if ($document_root !== '' && $folder_proyek !== '' && strpos($folder_proyek, $document_root) === 0) {
        $relatif = str_replace('\\', '/', substr($folder_proyek, strlen($document_root)));
        $base = rtrim($relatif, '/');
        return $base;
    }

    $script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $base = rtrim(str_replace('\\', '/', dirname($script_name)), '/.');
    return $base;
}

function url(string $path = ''): string
{
    $base = rtrim(base_url(), '/');
    return $base . '/' . ltrim($path, '/');
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

function h($teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

function format_rupiah(float $nominal): string
{
    return 'Rp ' . number_format($nominal, 0, ',', '.');
}

function ambil_rekap_keuangan(PDO $koneksi, int $id_user): array
{
    $query = $koneksi->prepare("SELECT
        COALESCE(SUM(CASE WHEN jenis_transaksi = 'pemasukan' THEN nominal END),0) AS total_pemasukan,
        COALESCE(SUM(CASE WHEN jenis_transaksi = 'pengeluaran' THEN nominal END),0) AS total_pengeluaran
        FROM transaksi WHERE id_user = :id_user");
    $query->execute(['id_user' => $id_user]);
    $rekap = $query->fetch();

    $query_tabungan = $koneksi->prepare('SELECT COALESCE(SUM(nominal_terkumpul),0) AS total_tabungan FROM tabungan WHERE id_user = :id_user');
    $query_tabungan->execute(['id_user' => $id_user]);
    $tabungan = $query_tabungan->fetch();

    $total_pemasukan = (float) $rekap['total_pemasukan'];
    $total_pengeluaran = (float) $rekap['total_pengeluaran'];

    return [
        'total_pemasukan' => $total_pemasukan,
        'total_pengeluaran' => $total_pengeluaran,
        'saldo_total' => $total_pemasukan - $total_pengeluaran,
        'total_tabungan' => (float) $tabungan['total_tabungan'],
    ];
}

