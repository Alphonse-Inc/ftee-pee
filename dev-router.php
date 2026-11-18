<?php
// Local preview router for PHP's built-in server. Apache on cPanel uses .htaccess instead.
// Denied over HTTP by the project .htaccess. Run: php -S 127.0.0.1:8080 dev-router.php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
if ($uri !== '/' && is_file(__DIR__ . $uri)) {
    return false;
}
if (preg_match('#/install/?$#', $uri)) {
    require __DIR__ . '/install.php';
    return true;
}
require __DIR__ . '/index.php';
