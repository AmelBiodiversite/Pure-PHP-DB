<?php
if (!function_exists('setFlashMessage')) {
    function setFlashMessage($type, $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
}
if (!function_exists('getFlashMessage')) {
    function getFlashMessage() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash'], $_SESSION['flash_message'], $_SESSION['flash_type']);
            return $flash;
        }
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            $type = $_SESSION['flash_type'] ?? 'info';
            unset($_SESSION['flash_message'], $_SESSION['flash_type']);
            return ['message' => $message, 'type' => $type];
        }
        return null;
    }
}
if (!function_exists('redirectWithMessage')) {
    function redirectWithMessage($url, $message, $type = 'success') {
        setFlashMessage($type, $message);
        header("Location: {$url}");
        exit;
    }
}
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}
if (!function_exists('generateCsrfToken')) {
    function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
if (!function_exists('csrf_token')) {
    function csrf_token() {
        return generateCsrfToken();
    }
}
if (!function_exists('verifyCsrfToken')) {
    function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
if (!function_exists('truncate')) {
    function truncate($text, $length = 100, $suffix = '...') {
        if (mb_strlen($text) <= $length) return $text;
        $truncated = mb_substr($text, 0, $length);
        $lastSpace = mb_strrpos($truncated, ' ');
        if ($lastSpace !== false) $truncated = mb_substr($truncated, 0, $lastSpace);
        return $truncated . $suffix;
    }
}
if (!function_exists('slugify')) {
    function slugify($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        return empty(strtolower($text)) ? 'n-a' : strtolower($text);
    }
}
if (!function_exists('formatPrice')) {
    function formatPrice($price) {
        $price = floatval($price);
        if (defined('APP_CURRENCY')) {
            $formatted = number_format($price, 2, '.', ' ');
            if (defined('APP_CURRENCY_POS') && APP_CURRENCY_POS === 'left') {
                return APP_CURRENCY . $formatted;
            }
            return $formatted . ' ' . APP_CURRENCY;
        }
        return number_format($price, 2, '.', ' ') . ' €';
    }
}
if (!function_exists('formatDate')) {
    function formatDate($date) {
        return date('d/m/Y à H:i', strtotime($date));
    }
}
if (!function_exists('timeAgo')) {
    function timeAgo($datetime) {
        $timestamp = strtotime($datetime);
        $difference = time() - $timestamp;
        if ($difference < 60) return "À l'instant";
        if ($difference < 3600) return floor($difference / 60) . ' min';
        if ($difference < 86400) return floor($difference / 3600) . ' h';
        if ($difference < 604800) return floor($difference / 86400) . ' j';
        return date('d/m/Y', $timestamp);
    }
}
if (!function_exists('generateLicenseKey')) {
    function generateLicenseKey() {
        return sprintf('%s-%s-%s-%s', bin2hex(random_bytes(4)), bin2hex(random_bytes(4)), bin2hex(random_bytes(4)), bin2hex(random_bytes(4)));
    }
}
if (!function_exists('generateOrderNumber')) {
    function generateOrderNumber() {
        return 'MF-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }
}
if (!function_exists('isValidUrl')) {
    function isValidUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
if (!function_exists('isAllowedFileType')) {
    function isAllowedFileType($filename, $allowedTypes) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, $allowedTypes);
    }
}
if (!function_exists('dd')) {
    function dd($var) {
        echo '<pre style="background:#1a1a1a;color:#0f0;padding:20px;border-radius:8px;font-family:monospace;">';
        var_dump($var);
        echo '</pre>';
        die();
    }
}
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: {$url}");
        exit;
    }
}

/**
 * Formate une taille de fichier en octets vers un format lisible
 * 
 * @param int $bytes Taille en octets
 * @param int $precision Nombre de décimales
 * @return string Taille formatée (ex: "2.5 MB", "1.2 GB")
 */
function formatFileSize($bytes, $precision = 2) {
    if ($bytes <= 0) return '0 B';
    
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}
