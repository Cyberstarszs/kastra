<?php
$berkas_dotenv = __DIR__ . '/../.env';
if (file_exists($berkas_dotenv)) {
    $baris_baris = file($berkas_dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($baris_baris as $baris) {
        $baris = trim($baris);
        if ($baris === '' || strpos($baris, '#') === 0) {
            continue;
        }
        $posisi = strpos($baris, '=');
        if ($posisi !== false) {
            $kunci = trim(substr($baris, 0, $posisi));
            $nilai = trim(substr($baris, $posisi + 1));
            if (preg_match('/^([\'"])(.*)\1$/', $nilai, $pencocokan)) {
                $nilai = $pencocokan[2];
            }
            $_ENV[$kunci] = $nilai;
            $_SERVER[$kunci] = $nilai;
            putenv("$kunci=$nilai");
        }
    }
}
return [
    'app_env' => getenv('APP_ENV'),
    'base_url' => rtrim((string) getenv('APP_BASE_URL'), '/') . '/',
    'database' => [
        'host' => getenv('DB_HOST'),
        'nama_db' => getenv('DB_NAME'),
        'username_db' => getenv('DB_USER'),
        'password_db' => getenv('DB_PASS'),
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
    'ai' => [
        'api_key_deepseek' => getenv('DEEPSEEK_API_KEY'),
        'timeout' => getenv('AI_TIMEOUT') ?: 30,
    ],
    'mail' => [
        'from_email' => getenv('MAIL_FROM_EMAIL'),
        'from_name' => getenv('MAIL_FROM_NAME'),
        'transport' => getenv('MAIL_TRANSPORT'),
        'smtp_host' => getenv('MAIL_SMTP_HOST'),
        'smtp_port' => (int) getenv('MAIL_SMTP_PORT'),
        'smtp_username' => getenv('MAIL_SMTP_USERNAME'),
        'smtp_password' => getenv('MAIL_SMTP_PASSWORD'),
        'smtp_encryption' => getenv('MAIL_SMTP_ENCRYPTION'),
        'smtp_timeout' => (int) getenv('MAIL_SMTP_TIMEOUT'),
    ],
    'pakasir' => [
        'base_url' => getenv('PAKASIR_BASE_URL'),
        'project_slug' => getenv('PAKASIR_PROJECT'),
        'api_key' => getenv('PAKASIR_API_KEY'),
        'webhook_token' => getenv('PAKASIR_WEBHOOK_TOKEN'),
        'integration_mode' => getenv('PAKASIR_MODE'),
        'payment_method' => getenv('PAKASIR_METHOD'),
        'qris_only' => true,
        'simulate_mode' => false,
    ],
];
