<?php
require_once __DIR__ . '/../config/fungsi.php';
session_unset();
session_destroy();
header('Location: ' . url('auth/login.php'));
exit;
