<?php
// Boots config, the session cookie, and the database. Sends visitors to the installer when needed.

date_default_timezone_set('Africa/Lagos');
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/helpers.php';
require __DIR__ . '/Database.php';
require __DIR__ . '/Csrf.php';
require __DIR__ . '/Auth.php';
require __DIR__ . '/Uploader.php';
require __DIR__ . '/Mailer.php';
require __DIR__ . '/Router.php';

$configPath = app_root() . '/config/config.php';
if (is_file($configPath)) {
    $loaded = require $configPath;
    if (is_array($loaded)) {
        $GLOBALS['ftee_config'] = $loaded;
    }
}

$cookiePath = base_url() === '' ? '/' : base_url() . '/';
$secure = is_https();
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', $secure ? '1' : '0');
session_name('FTEEPEESESSID');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => $cookiePath,
    'domain' => '',
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
]);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; form-action 'self'; base-uri 'self'; frame-ancestors 'self'");

if (!defined('FTEE_INSTALLER') && !is_installed()) {
    $script = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', (string) $_SERVER['SCRIPT_NAME']) : '/index.php';
    $dir = rtrim(dirname($script), '/');
    if ($dir === '/' || $dir === '.' || $dir === '') {
        $prefix = '';
    } else {
        $prefix = $dir;
    }
    header('Location: ' . $prefix . '/install');
    exit;
}

if (is_installed()) {
    try {
        $GLOBALS['ftee_db'] = Database::connect($GLOBALS['ftee_config']);
        try {
            db()->pdo()->exec("SET time_zone = '+01:00'");
        } catch (Exception $ignored) {
            // Some hosts lock the MySQL time zone. PHP still uses Africa/Lagos.
        }
    } catch (Exception $e) {
        error_log('FTEE-PEE database: ' . $e->getMessage());
        http_response_code(500);
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>Database unavailable</title></head><body>';
        echo '<h1>Database unavailable</h1><p>The site could not connect. Check the database name, user, and password in config/config.php, then reload.</p>';
        echo '</body></html>';
        exit;
    }
    $path = request_path();
    if (strpos($path, '/admin') === 0) {
        header('X-Robots-Tag: noindex, nofollow');
        header('Cache-Control: no-store');
    }
}
