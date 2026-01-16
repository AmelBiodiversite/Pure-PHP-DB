<?php
/**
 * MARKETFLOW PRO - CONFIGURATION PRINCIPALE
 * Fichier : config/config.php
 * Adapté pour PostgreSQL sur Replit
 */

// ============================================
// ENVIRONNEMENT
// ============================================

// Mode développement ou production
define('ENVIRONMENT', 'development'); // 'development' ou 'production'

// Affichage des erreurs (désactiver en production !)
if (ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// ============================================
// BASE DE DONNÉES POSTGRESQL (REPLIT)
// ============================================

// Replit fournit automatiquement DATABASE_URL
// Mais on peut aussi définir manuellement :

define('DB_HOST', 'localhost');
define('DB_PORT', '5432'); // Port PostgreSQL par défaut
define('DB_NAME', 'marketflow_db');
define('DB_USER', 'replit');
define('DB_PASS', '');

// Note: Sur Replit, la connexion se fait via DATABASE_URL
// qui est automatiquement disponible dans getenv('DATABASE_URL')

define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_FILE_TYPES', ['zip', 'pdf', 'jpg', 'jpeg', 'png', 'gif', 'psd', 'ai', 'sketch', 'fig']);
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB

// ============================================
// APPLICATION
// ============================================

// Chemin de base pour l'application
define('APP_PATH', __DIR__ . '/..');

// URL de base de l'application (Replit)
// Remplacez par votre URL Repl
define('APP_URL', 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));

// Chemins des ressources statiques
define('CSS_URL', APP_URL . '/public/css');
define('JS_URL', APP_URL . '/public/js');
define('IMG_URL', APP_URL . '/public/img');


/** // Paramètres de devise
define('CURRENCY_SYMBOL', '€');
if (!defined('CURRENCY_POSITION')) define('CURRENCY_POSITION', 'right'); // 'left' ou 'right'*/



// Nom de l'application
define('APP_NAME', 'MarketFlow Pro');


// Timezone
date_default_timezone_set('Europe/Paris');

// ============================================
// SÉCURITÉ
// ============================================

// Clé de sécurité pour les sessions et CSRF
// Générez une clé unique : openssl_rand_pseudo_bytes(32)
define('APP_KEY', 'votre_cle_securite_unique_32_caracteres_minimum');

// Session
define('SESSION_LIFETIME', 7200); // 2 heures en secondes
define('SESSION_COOKIE_NAME', 'marketflow_session');

// CSRF Token
define('CSRF_TOKEN_NAME', '_token');
define('CSRF_TOKEN_LIFETIME', 3600); // 1 heure

// ============================================
// STRIPE (PAIEMENTS)
// ============================================

// MODE TEST (pour développement)
define('STRIPE_PUBLIC_KEY', 'pk_test_VOTRE_CLE_PUBLIQUE');
define('STRIPE_SECRET_KEY', 'sk_test_VOTRE_CLE_SECRETE');
define('STRIPE_WEBHOOK_SECRET', 'whsec_VOTRE_SECRET_WEBHOOK');

// MODE LIVE (pour production - décommenter quand prêt)
// define('STRIPE_PUBLIC_KEY', 'pk_live_VOTRE_CLE_PUBLIQUE');
// define('STRIPE_SECRET_KEY', 'sk_live_VOTRE_CLE_SECRETE');
// define('STRIPE_WEBHOOK_SECRET', 'whsec_VOTRE_SECRET_WEBHOOK');

// ============================================
// COMMISSIONS & REVENUS
// ============================================

// Commission de la plateforme (pourcentage)
define('PLATFORM_COMMISSION', 10); // 10%

// Montant minimum de retrait pour les vendeurs
define('MIN_PAYOUT_AMOUNT', 50.00); // 50€

// ============================================
// UPLOAD DE FICHIERS
// ============================================

// Dossier de base pour les uploads
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/');


// ============================================
// EMAIL (SMTP)
// ============================================

// Activer l'envoi d'emails
define('SMTP_ENABLED', false); // Mettre à TRUE quand configuré

// Configuration SMTP
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls'); // 'tls' ou 'ssl'
define('SMTP_USER', 'votre@email.com');
define('SMTP_PASS', 'votre_mot_de_passe_application');

// Expéditeur par défaut
define('SMTP_FROM', 'noreply@marketflow.com');
define('SMTP_FROM_NAME', 'MarketFlow Pro');

// ============================================
// PAGINATION
// ============================================

define('PRODUCTS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 10);
define('USERS_PER_PAGE', 20);
define('REVIEWS_PER_PAGE', 10);

// ============================================
// LIMITES & RESTRICTIONS
// ============================================

// Téléchargements maximum par produit acheté
define('MAX_DOWNLOADS_PER_PRODUCT', 3);

// Nombre de tentatives de connexion avant blocage
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes en secondes

// Délai de remboursement (en jours)
define('REFUND_PERIOD_DAYS', 14);

// ============================================
// SEO
// ============================================

define('META_TITLE', 'MarketFlow Pro - Digital Marketplace');
define('META_DESCRIPTION', 'Achetez et vendez des produits digitaux de qualité');
define('META_KEYWORDS', 'marketplace, digital, produits, templates, designs');

// ============================================
// CACHE (optionnel - pour optimisation)
// ============================================

// Activer le cache
define('CACHE_ENABLED', false);

// Durée du cache (en secondes)
define('CACHE_LIFETIME', 3600); // 1 heure

// ============================================
// LOGS
// ============================================

// Activer les logs
define('LOGGING_ENABLED', true);

// Dossier des logs
define('LOG_DIR', __DIR__ . '/../logs/');

// Niveau de log (debug, info, warning, error)
define('LOG_LEVEL', ENVIRONMENT === 'development' ? 'debug' : 'error');

// ============================================
// API (pour extensions futures)
// ============================================

// Activer l'API REST
define('API_ENABLED', false);

// Clé API (à générer)
define('API_KEY', 'votre_cle_api_unique');

// ============================================
// SOCIAL LOGIN (optionnel - à implémenter)
// ============================================

// Google OAuth
define('GOOGLE_CLIENT_ID', '');
define('GOOGLE_CLIENT_SECRET', '');

// Facebook OAuth
define('FACEBOOK_APP_ID', '');
define('FACEBOOK_APP_SECRET', '');

// ============================================
// FONCTIONS UTILITAIRES
// ============================================

/**
 * Obtenir une configuration
 */
function config($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

/**
 * Vérifier si on est en mode développement
 */
function isDevelopment() {
    return ENVIRONMENT === 'development';
}

/**
 * Vérifier si on est en mode production
 */
function isProduction() {
    return ENVIRONMENT === 'production';
}

/**
 * Obtenir l'URL complète de l'application
 */
function appUrl($path = '') {
    return APP_URL . '/' . ltrim($path, '/');
}

/**
 * Obtenir le chemin d'upload
 */
function uploadPath($file = '') {
    return UPLOAD_DIR . ltrim($file, '/');
}

/**
 * Obtenir l'URL d'upload
 */
function uploadUrl($file = '') {
    return APP_URL . '/public/uploads/' . ltrim($file, '/');
}

/**
 * Logger un message
 */
function logMessage($message, $level = 'info') {
    if (!LOGGING_ENABLED) {
        return;
    }
    
    $logFile = LOG_DIR . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
    
    if (!is_dir(LOG_DIR)) {
        mkdir(LOG_DIR, 0755, true);
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

// ============================================
// INITIALISATION
// ============================================

// Créer les dossiers nécessaires si ils n'existent pas
$dirs = [
    UPLOAD_DIR . 'products/thumbnails',
    UPLOAD_DIR . 'products/files',
    UPLOAD_DIR . 'products/gallery',
    UPLOAD_DIR . 'avatars',
    UPLOAD_DIR . 'shops',
    LOG_DIR
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Configuration de session sécurisée
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    
    if (isProduction()) {
        ini_set('session.cookie_secure', 1); // HTTPS uniquement
    }
    
    session_name(SESSION_COOKIE_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'domain' => parse_url(APP_URL, PHP_URL_HOST),
        'secure' => isProduction(),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

// ============================================
// FIN DE LA CONFIGURATION
// ============================================