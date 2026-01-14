<?php
// ============================================
// FICHIER 6 : helpers/functions.php
// ============================================

/**
 * Fonctions utilitaires globales
 */

// Echapper du HTML
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Générer un slug
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

// Formater un prix
function formatPrice($price) {
    // Convertir en float pour être sûr
    $price = floatval($price);

      if (APP_CURRENCY_POS === 'left') {
        return APP_CURRENCY . number_format($price, 2, ',', ' ');
    }
    return number_format($price, 2, ',', ' ') . ' ' . APP_CURRENCY;
}

// Générer une clé de licence
function generateLicenseKey() {
    return sprintf(
        '%s-%s-%s-%s',
        bin2hex(random_bytes(4)),
        bin2hex(random_bytes(4)),
        bin2hex(random_bytes(4)),
        bin2hex(random_bytes(4))
    );
}

// Générer un numéro de commande
function generateOrderNumber() {
    return 'MF-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
}

// Formater une date
function formatDate($date) {
    return date('d/m/Y à H:i', strtotime($date));
}

// Calculer temps relatif
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    if ($difference < 60) return 'À l\'instant';
    if ($difference < 3600) return floor($difference / 60) . ' min';
    if ($difference < 86400) return floor($difference / 3600) . ' h';
    if ($difference < 604800) return floor($difference / 86400) . ' j';
    
    return date('d/m/Y', $timestamp);
}

// Tronquer du texte
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

// Vérifier si une URL est valide
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// Rediriger avec message flash
function redirectWithMessage($url, $message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header("Location: {$url}");
    exit;
}

// Afficher message flash
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

// Vérifier extension de fichier
function isAllowedFileType($filename, $allowedTypes) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, $allowedTypes);
}

// Générer token CSRF
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Vérifier token CSRF
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}