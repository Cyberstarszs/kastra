<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fungsi.php';
wajib_admin();
header('Location: ' . url('admin/dashboard.php'));
exit;
