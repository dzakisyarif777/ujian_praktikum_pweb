<?php
session_start();

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'inventory_app');
define('DB_USER', 'root');
define('DB_PASS', '');

define('BASE_URL', 'http://localhost/inventory-app/');

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_token() {
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token ?? '');
}
