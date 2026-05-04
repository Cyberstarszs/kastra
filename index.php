<?php
require_once __DIR__ . '/config/fungsi.php';

if (sudah_login()) {
    header('Location: ' . url('user/dashboard.php'));
} else {
    header('Location: ' . url('auth/login.php'));
}
exit;

