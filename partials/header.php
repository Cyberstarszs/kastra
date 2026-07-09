<?php
require_once __DIR__ . '/../config/fungsi.php';
$konfigurasi_aplikasi = require __DIR__ . '/../config/app.php';
$base_url_seo = rtrim((string) ($konfigurasi_aplikasi['base_url'] ?? ''), '/');
$path_saat_ini = (string) ($_SERVER['REQUEST_URI'] ?? '/');
$url_kanonik = $base_url_seo . $path_saat_ini;
$deskripsi_halaman = $deskripsi_halaman ?? 'Kastra - aplikasi manajemen keuangan pribadi untuk transaksi, tabungan, laporan, dan AI assistant.';
$css_file_path = __DIR__ . '/../assets/css/style.css';
$css_version = file_exists($css_file_path) ? (string) filemtime($css_file_path) : '1';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($judul_halaman ?? 'Kastra') ?></title>
    <meta name="description" content="<?= h($deskripsi_halaman) ?>">
    <meta name="robots" content="noindex, nofollow">
    <link rel="canonical" href="<?= h($url_kanonik) ?>">
    <link rel="icon" type="image/jpeg" href="<?= url('assets/image/favicon.jpeg') ?>">
    <link rel="apple-touch-icon" href="<?= url('assets/image/favicon.jpeg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css?v=' . $css_version) ?>" rel="stylesheet">
</head>
<body>
<div class="layout-wrap">
