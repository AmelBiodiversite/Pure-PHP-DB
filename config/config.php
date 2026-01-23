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
define('CSS_URL', APP_URL . '/public/css');
define('JS_URL', APP_URL . '/public/js');
define('IMG_URL', APP_URL . '/public/img');
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
define('STRIPE_PUBLIC_KEY', getenv('STRIPE_PUBLIC_KEY') ?: 'pk_test_51Ry71xRNkjwOv11X1xlyYOwgZI0823L8X9vb0G4PKPe7XsodDAUQW056o8Mvlxp0ghi8b2F7z6L8nSh5PWX2essG00SKvsHxNN');
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY') ?: 'sk_test_51Ry71xRNkjwOv11X1cF3hlUVglXKjnGf0xPCXxqwQGLLtUTDJWJqfXzeQqvmVfz4rb2WQFMRqPWbot6YwLxZ8GJy00Mig0enav');
define('STRIPE_WEBHOOK_SECRET', getenv('STRIPE_WEBHOOK_SECRET') ?: 'whsec_votre_webhook_secret');

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
