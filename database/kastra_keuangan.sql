CREATE DATABASE IF NOT EXISTS kastra_keuangan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kastra_keuangan;

CREATE TABLE IF NOT EXISTS users (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  nama_lengkap VARCHAR(120) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  kata_sandi VARCHAR(255) NOT NULL,
  tanggal_daftar DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS transaksi (
  id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT NOT NULL,
  jenis_transaksi ENUM('pemasukan','pengeluaran') NOT NULL,
  nominal DECIMAL(15,2) NOT NULL,
  kategori VARCHAR(100) NOT NULL,
  deskripsi TEXT NULL,
  tanggal DATE NOT NULL,
  CONSTRAINT fk_transaksi_user FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tabungan (
  id_tabungan INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT NOT NULL,
  nama_tujuan VARCHAR(150) NOT NULL,
  target_nominal DECIMAL(15,2) NOT NULL,
  nominal_terkumpul DECIMAL(15,2) NOT NULL DEFAULT 0,
  tanggal_target DATE NOT NULL,
  CONSTRAINT fk_tabungan_user FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS kategori (
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT NOT NULL,
  nama_kategori VARCHAR(100) NOT NULL,
  jenis ENUM('pemasukan','pengeluaran') NOT NULL,
  CONSTRAINT fk_kategori_user FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
);
