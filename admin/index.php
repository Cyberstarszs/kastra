<?php
require_once __DIR__ . '/../config/fungsi.php';
wajib_login();
if (user_admin()) {
    header('Location: ' . url('admin/dashboard.php'));
} else {
    header('Location: ' . url('user/dashboard.php'));
}
exit;
