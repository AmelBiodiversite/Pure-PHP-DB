<?php
/**
 * MARKETFLOW PRO - CONFIGURATION
 * Configuration principale de l'application
 */

// ================================================================
// CONFIGURATION DATABASE
// ================================================================
define('DATABASE_URL', getenv('DATABASE_URL'));

// ================================================================
// CONFIGURATION URLs
// ================================================================
define('APP_URL', 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
// Détection automatique du chemin CSS selon l'environnement
if (getenv('RAILWAY_ENVIRONMENT') || isset($_SERVER['RAILWAY_ENVIRONMENT'])) {
    // Sur Railway : pas de /public/ dans l'URL
    define('CSS_URL', APP_URL . '/css');
    define('JS_URL', APP_URL . '/js');
} else {
    // Sur Replit/Local : avec /public/ dans l'URL
    define('CSS_URL', APP_URL . '/public/css');
    define('JS_URL', APP_URL . '/public/js');
}
define('IMG_URL', APP_URL . '/img');
define('UPLOAD_URL', APP_URL . '/public/uploads');

// ================================================================
// CONFIGURATION CURRENCY
// ================================================================
if (file_exists(__DIR__ . '/currency.php')) {
    require_once __DIR__ . '/currency.php';
}

if (!defined('APP_CURRENCY')) define('APP_CURRENCY', '€');
if (!defined('APP_CURRENCY_POS')) define('APP_CURRENCY_POS', 'right');

// Alias pour compatibilité
if (!defined('CURRENCY')) define('CURRENCY', APP_CURRENCY);

// ================================================================
// CONFIGURATION STRIPE
// ================================================================
define('STRIPE_PUBLIC_KEY', getenv('STRIPE_PUBLIC_KEY') ?: 'pk_test_YOUR_PUBLIC_KEY');
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY') ?: 'sk_test_YOUR_SECRET_KEY');
define('STRIPE_WEBHOOK_SECRET', getenv('STRIPE_WEBHOOK_SECRET') ?: 'whsec_YOUR_WEBHOOK_SECRET');

// ================================================================
// CONFIGURATION MAIL
// ================================================================
define('MAIL_FROM', 'noreply@marketflowpro.com');

// ================================================================
// CONFIGURATION UPLOADS
// ================================================================
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'zip']);

// ================================================================
// CONFIGURATION PLATEFORME
// ================================================================
define('PLATFORM_COMMISSION', 10); // 10% de commission

// ================================================================
// HELPER FUNCTIONS
// ================================================================
if (!function_exists('url')) {
    function url($path = '') {
        return APP_URL . '/' . ltrim($path, '/');
    }
}

if (!function_exists('uploadUrl')) {
    function uploadUrl($file = '') {
        return APP_URL . '/public/uploads/' . ltrim($file, '/');
    }
}

// ================================================================
// CHARGER .env SI PRÉSENT (pour développement local)
// ================================================================
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!empty($name) && !empty($value)) {
            putenv($name . '=' . $value);
        }
    }
}
