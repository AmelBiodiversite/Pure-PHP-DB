<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Paris');
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();
require_once __DIR__ . '/app/helpers/auth_helper.php';
require_once __DIR__ . '/app/helpers/functions.php';
require_once __DIR__ . '/vendor/autoload.php';
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/';
    $path = str_replace('\\', '/', $class);
    $attempts = [$baseDir . $path . '.php', $baseDir . strtolower($path) . '.php'];
    $parts = explode('/', $path);
    if (count($parts) >= 2) {
        $parts[0] = strtolower($parts[0]);
        $attempts[] = $baseDir . implode('/', $parts) . '.php';
    }
    if (count($parts) >= 3) {
        $parts[1] = strtolower($parts[1]);
        $attempts[] = $baseDir . implode('/', $parts) . '.php';
    }
    if (strpos($class, 'Core\\') === 0) {
        $attempts[] = $baseDir . str_replace('Core\\', 'core/', $class) . '.php';
    }
    foreach ($attempts as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/routes.php';
