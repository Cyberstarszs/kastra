<?php
$konfigurasi_aplikasi = require __DIR__ . '/app.php';
$basis_data = $konfigurasi_aplikasi['database'];
$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    $basis_data['host'],
    $basis_data['nama_db'],
    $basis_data['charset']
);
try {
    $koneksi = new PDO($dsn, $basis_data['username_db'], $basis_data['password_db'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    try {
        $koneksi->exec("SET time_zone = '+07:00'");
    } catch (Throwable $kesalahan_zona_waktu) {
    }
} catch (PDOException $kesalahan) {
    error_log('Koneksi database gagal: ' . $kesalahan->getMessage());
    http_response_code(500);
    exit('Terjadi kendala koneksi database. Silakan coba beberapa saat lagi.');
}
