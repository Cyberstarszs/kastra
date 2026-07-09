<?php
$konfigurasi_aplikasi = require __DIR__ . '/app.php';
$url_dasar = rtrim((string) ($konfigurasi_aplikasi['base_url'] ?? ''), '/');
$pakasir = $konfigurasi_aplikasi['pakasir'] ?? [];
return [
    'base_url' => $pakasir['base_url'] ?? 'https://app.pakasir.com',
    'project_slug' => $pakasir['project_slug'] ?? '',
    'api_key' => $pakasir['api_key'] ?? '',
    'webhook_token' => $pakasir['webhook_token'] ?? '',
    'integration_mode' => $pakasir['integration_mode'] ?? 'url',
    'payment_method' => $pakasir['payment_method'] ?? 'qris',
    'qris_only' => (bool) ($pakasir['qris_only'] ?? true),
    'callback_url' => $url_dasar . '/api/callback_pakasir.php',
    'return_url' => $url_dasar . '/user/upgrade_premium.php?status=return',
    'simulate_mode' => (bool) ($pakasir['simulate_mode'] ?? false),
];
