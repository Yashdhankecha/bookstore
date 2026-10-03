<?php
// ===============================
// Bookstore - Auto Configuration
// ===============================

// --- Database Config (XAMPP defaults) ---
// Setup wizard (setup.php) auto-updates these values if needed
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bookstore');

// --- App URL - AUTO DETECT ---
// Automatically detects http/https, host, port and folder name
// Works for: localhost/book, localhost:8080/book, 127.0.0.1/bookstore etc.
if (!defined('APP_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Detect folder name dynamically (e.g. /book , /bookstore)
    $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    // Normalize: remove /admin, /core etc. to get root
    $scriptDir = str_replace(['\\', '//'], '/', $scriptDir);
    $scriptDir = preg_replace('#/(admin|core|includes|assets)(/.*)?$#', '', $scriptDir);
    $scriptDir = rtrim($scriptDir, '/');
    // If installed in htdocs root, $scriptDir will be empty -> use ''
    if ($scriptDir === '' || $scriptDir === '.' || $scriptDir === '/') {
        $scriptDir = '';
    }
    define('APP_URL', $protocol . '://' . $host . $scriptDir);
}

define('CURRENCY', '₹');

// Output buffering to allow header() redirects even after includes/header.php outputs HTML
// Fixes: "Cannot modify header information - headers already sent" in login.php:5, cart.php:5, etc.
if (!ob_get_level()) {
    ob_start();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
