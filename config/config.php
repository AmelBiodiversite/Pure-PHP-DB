<?php
/**
 * MARKETFLOW PRO - CONFIGURATION PRINCIPALE
 * Fichier : config/config.php
 */

// ================================================
// CONFIGURATION BASE DE DONNÉES
// ================================================
define('DATABASE_URL', getenv('DATABASE_URL'));

// ================================================
// CONFIGURATION APPLICATION
// ================================================
// URL de base de l'application
define('APP_URL', 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));

// URLs des ressources statiques (CORRECTION : ajout de /public)
define('CSS_URL', APP_URL . '/public/css');
define('JS_URL', APP_URL . '/public/js');
define('IMG_URL', APP_URL . '/public/img');
define('UPLOAD_URL', APP_URL . '/public/uploads');

// ================================================
// CONFIGURATION STRIPE
// ================================================
define('STRIPE_PUBLIC_KEY', getenv('STRIPE_PUBLIC_KEY') ?: 'pk_test_votre_cle_publique');
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY') ?: 'sk_test_votre_cle_secrete');
define('STRIPE_WEBHOOK_SECRET', getenv('STRIPE_WEBHOOK_SECRET') ?: 'whsec_votre_webhook_secret');

// ================================================
// CONFIGURATION EMAIL (pour les notifications futures)
// ================================================
define('MAIL_FROM', 'noreply@marketflowpro.com');
define('MAIL_FROM_NAME', 'MarketFlow Pro');

// ================================================
// CONFIGURATION UPLOADS
// ================================================
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'zip']);

// ================================================
// CONFIGURATION SESSION
// ================================================
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

// ================================================
// FONCTIONS UTILITAIRES
// ================================================

/**
 * Générer une URL complète
 */
function url($path = '') {
    return APP_URL . '/' . ltrim($path, '/');
}

/**
 * Générer une URL pour un fichier uploadé
 */
function uploadUrl($file = '') {
    return APP_URL . '/public/uploads/' . ltrim($file, '/');
}

/**
 * Générer un token CSRF
 */
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Formater un prix
 */
function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}

/**
 * Redirection
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Configuration des cookies de session
 */
session_set_cookie_params([
    'lifetime' => 86400, // 24 heures
    'path' => '/',
    'domain' => parse_url(APP_URL, PHP_URL_HOST),
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);
