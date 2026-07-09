<?php
require_once __DIR__ . '/koneksi.php';
try {
    try { $koneksi->exec("ALTER TABLE users ADD COLUMN status_user ENUM('biasa','premium') NOT NULL DEFAULT 'biasa'"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("ALTER TABLE users MODIFY status_user ENUM('biasa','premium') NOT NULL DEFAULT 'biasa'"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("ALTER TABLE users ADD COLUMN sisa_prompt INT NOT NULL DEFAULT 15"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("ALTER TABLE users ADD COLUMN foto VARCHAR(255) NULL"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("ALTER TABLE users ADD COLUMN preferensi JSON NULL"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("ALTER TABLE users ADD COLUMN tanggal_reset_prompt DATE NULL"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("ALTER TABLE users ADD COLUMN role ENUM('admin','user') NOT NULL DEFAULT 'user'"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("ALTER TABLE users ADD COLUMN premium_expired DATETIME NULL"); } catch (Throwable $kesalahan) {}
    $koneksi->exec("CREATE TABLE IF NOT EXISTS chat_ai (
      id_chat INT AUTO_INCREMENT PRIMARY KEY,
      id_user INT NOT NULL,
      pesan_user TEXT NOT NULL,
      respon_ai TEXT NOT NULL,
      waktu DATETIME NOT NULL,
      CONSTRAINT fk_chat_ai_user FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
    )");
    $koneksi->exec("CREATE TABLE IF NOT EXISTS transaksi_premium (
      id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
      id_user INT NOT NULL,
      kode_invoice VARCHAR(100) NOT NULL UNIQUE,
      jumlah INT NOT NULL DEFAULT 2000,
      status ENUM('pending','berhasil','gagal','expired') NOT NULL DEFAULT 'pending',
      metode_pembayaran VARCHAR(100) NULL,
      waktu_buat DATETIME NOT NULL,
      waktu_bayar DATETIME NULL,
      expired_at DATETIME NOT NULL,
      CONSTRAINT fk_transaksi_premium_user FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
    )");
    try { $koneksi->exec("ALTER TABLE transaksi_premium ADD COLUMN kode_invoice VARCHAR(100) NULL"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("ALTER TABLE transaksi_premium ADD COLUMN metode_pembayaran VARCHAR(100) NULL"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("ALTER TABLE transaksi_premium ADD COLUMN waktu_buat DATETIME NULL"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("ALTER TABLE transaksi_premium ADD COLUMN waktu_bayar DATETIME NULL"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("ALTER TABLE transaksi_premium ADD COLUMN expired_at DATETIME NULL"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("ALTER TABLE transaksi_premium ADD COLUMN payment_number TEXT NULL"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("ALTER TABLE transaksi_premium ADD COLUMN total_payment INT NULL"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("ALTER TABLE transaksi_premium MODIFY status ENUM('pending','berhasil','gagal','expired') NOT NULL DEFAULT 'pending'"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("UPDATE transaksi_premium SET kode_invoice = kode_pembayaran WHERE (kode_invoice IS NULL OR kode_invoice='') AND kode_pembayaran IS NOT NULL"); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("UPDATE transaksi_premium SET waktu_buat = tanggal WHERE waktu_buat IS NULL AND tanggal IS NOT NULL"); } catch (Throwable $kesalahan) {}
    try { $koneksi->prepare("UPDATE transaksi_premium SET expired_at = DATE_ADD(COALESCE(waktu_buat, :now), INTERVAL 1 HOUR) WHERE expired_at IS NULL")->execute(['now' => date('Y-m-d H:i:s')]); } catch (Throwable $kesalahan) {}
    try { $koneksi->exec("UPDATE transaksi_premium SET status='berhasil' WHERE status='sukses'"); } catch (Throwable $kesalahan) {}
    try { $koneksi->prepare("UPDATE transaksi_premium SET status='expired' WHERE status='pending' AND expired_at < :now")->execute(['now' => date('Y-m-d H:i:s')]); } catch (Throwable $kesalahan) {}
    $koneksi->exec("CREATE TABLE IF NOT EXISTS pengaturan_sistem (
      id_pengaturan INT AUTO_INCREMENT PRIMARY KEY,
      nama_pengaturan VARCHAR(100) NOT NULL UNIQUE,
      nilai_pengaturan TEXT NOT NULL,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    $koneksi->exec("CREATE TABLE IF NOT EXISTS password_resets (
      id_reset INT AUTO_INCREMENT PRIMARY KEY,
      id_user INT NOT NULL,
      email VARCHAR(120) NOT NULL,
      token_hash VARCHAR(255) NOT NULL,
      expired_at DATETIME NOT NULL,
      used_at DATETIME NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      INDEX idx_password_reset_email (email),
      INDEX idx_password_reset_expired (expired_at),
      CONSTRAINT fk_password_reset_user FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
    )");
    $koneksi->exec("INSERT IGNORE INTO pengaturan_sistem (nama_pengaturan, nilai_pengaturan) VALUES
      ('harga_premium','2000'),
      ('limit_prompt','15')
    ");
} catch (Throwable $kesalahan) {
    error_log('Gagal inisialisasi schema: ' . $kesalahan->getMessage());
    http_response_code(500);
    exit('Terjadi kendala sistem. Silakan coba beberapa saat lagi.');
}
