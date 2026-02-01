<?php
/**
 * ============================================================================
 * MARKETFLOW PRO - FONCTIONS HELPER GLOBALES (VERSION FINALE)
 * ============================================================================
 * Fichier : core/Helpers.php
 * 
 * VERSION OPTIMISÉE avec :
 * - Support multi-headers CSRF (X-CSRF-Token, X-XSRF-Token)
 * - format_price() avec config automatique
 * - DRY avec get_base_url()
 * - Tous les helpers essentiels
 * ============================================================================
 */

/**
 * ============================================================================
 * PROTECTION XSS
 * ============================================================================
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * ============================================================================
 * CSRF - GÉNÉRATION DU CHAMP HIDDEN
 * ============================================================================
 */
function csrf_field() {
    return \Core\CSRF::field();
}

/**
 * ============================================================================
 * CSRF - RÉCUPÉRER LE TOKEN
 * ============================================================================
 */
function csrf_token() {
    return \Core\CSRF::generateToken();
}

/**
 * ============================================================================
 * CSRF - VALIDATION STRICTE (DIE)
 * ============================================================================
 * ✅ AMÉLIORÉ : Support de plusieurs headers pour compatibilité frameworks
 */
function csrf_check() {
    // Récupérer le token depuis plusieurs sources possibles
    // Support de X-CSRF-Token (standard) et X-XSRF-Token (Angular/Laravel)
    $token = $_POST['csrf_token']              // Formulaire classique
          ?? $_SERVER['HTTP_X_CSRF_TOKEN']      // AJAX standard
          ?? $_SERVER['HTTP_XSRF_TOKEN']        // Angular/Laravel
          ?? '';
    
    if (!\Core\CSRF::validateToken($token)) {
        http_response_code(403);
        
        // Si requête AJAX, renvoyer JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Token CSRF invalide. Veuillez recharger la page.'
            ]);
        } else {
            // Sinon afficher une page d'erreur
            echo '<!DOCTYPE html>
<html>
<head>
    <title>Erreur de sécurité</title>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 50px; 
            text-align: center;
            background: #f5f5f5;
        }
        .error { 
            background: #fff;
            border: 2px solid #e74c3c;
            padding: 30px; 
            border-radius: 10px; 
            display: inline-block;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        h1 { color: #e74c3c; margin-top: 0; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover { background: #2980b9; }
    </style>
</head>
<body>
    <div class="error">
        <h1>⚠️ Erreur de sécurité</h1>
        <p>Le token de sécurité est invalide ou a expiré.</p>
        <p>Veuillez recharger la page et réessayer.</p>
        <a href="javascript:history.back()" class="btn">← Retour</a>
        <a href="/" class="btn">🏠 Accueil</a>
    </div>
</body>
</html>';
        }
        die();
    }
}

/**
 * ============================================================================
 * CSRF - VALIDATION SOUPLE (RETURN BOOL)
 * ============================================================================
 * ✅ AMÉLIORÉ : Support multi-headers
 */
function csrf_validate() {
    // Récupérer le token depuis plusieurs sources
    $token = $_POST['csrf_token'] 
          ?? $_SERVER['HTTP_X_CSRF_TOKEN']
          ?? $_SERVER['HTTP_XSRF_TOKEN']
          ?? '';
    return \Core\CSRF::validateToken($token);
}

/**
 * ============================================================================
 * URL - OBTENIR L'URL DE BASE (DRY)
 * ============================================================================
 * ✅ NOUVEAU : Fonction centralisée pour éviter la répétition
 */
function get_base_url() {
    // Si défini dans la config, utiliser cette valeur
    if (defined('APP_URL')) {
        return rtrim(APP_URL, '/');
    }
    
    // Sinon, détecter automatiquement
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    return $protocol . $host;
}

/**
 * ============================================================================
 * URL - GÉNÉRER URL ABSOLUE
 * ============================================================================
 * ✅ AMÉLIORÉ : Utilise get_base_url()
 */
function url($path = '') {
    return get_base_url() . '/' . ltrim($path, '/');
}

/**
 * ============================================================================
 * URL - GÉNÉRER URL ASSET
 * ============================================================================
 * ✅ AMÉLIORÉ : Utilise get_base_url()
 */
function asset($path) {
    return get_base_url() . '/public/' . ltrim($path, '/');
}

/**
 * ============================================================================
 * REDIRECTION SIMPLE
 * ============================================================================
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * ============================================================================
 * REDIRECTION AVEC MESSAGE FLASH
 * ============================================================================
 */
function redirectWithMessage($url, $message, $type = 'info') {
    // Centralise l'écriture via setFlashMessage pour éviter les doublons
    setFlashMessage($type, $message);
    header("Location: $url");
    exit;
}

/**
 * ============================================================================
 * RÉCUPÉRER MESSAGE FLASH
 * ============================================================================
 */
function get_flash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'message' => $_SESSION['flash_message'],
            'type' => $_SESSION['flash_type'] ?? 'info'
        ];
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return $flash;
    }
    
    return null;
}

/**
 * ============================================================================
 * AFFICHER MESSAGE FLASH (HTML)
 * ============================================================================
 * Nouveau helper pour afficher directement le HTML du message flash
 * 
 * Usage dans les vues :
 * <?= flash_message() ?>
 */
function flash_message() {
    $flash = get_flash();
    
    if (!$flash) {
        return '';
    }
    
    // Classes Bootstrap selon le type
    $classes = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info'
    ];
    
    $class = $classes[$flash['type']] ?? 'alert-info';
    
    return '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">
        ' . e($flash['message']) . '
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}

/**
 * ============================================================================
 * FORMULAIRE - RÉCUPÉRER ANCIENNE VALEUR
 * ============================================================================
 */
function old($key, $default = '') {
    return $_SESSION['old'][$key] ?? $default;
}

/**
 * ============================================================================
 * FORMULAIRE - SAUVEGARDER ANCIENNES VALEURS
 * ============================================================================
 */
function save_old_input() {
    $_SESSION['old'] = array_filter($_POST, function($key) {
        return !in_array($key, ['password', 'password_confirm', 'csrf_token']);
    }, ARRAY_FILTER_USE_KEY);
}

/**
 * ============================================================================
 * FORMULAIRE - EFFACER ANCIENNES VALEURS
 * ============================================================================
 */
function clear_old_input() {
    unset($_SESSION['old']);
}

/**
 * ============================================================================
 * RÉPONSE JSON
 * ============================================================================
 */
function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * ============================================================================
 * VÉRIFIER SI REQUÊTE AJAX
 * ============================================================================
 */
function is_ajax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * ============================================================================
 * AUTHENTIFICATION - VÉRIFIER SI CONNECTÉ
 * ============================================================================
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * ============================================================================
 * AUTHENTIFICATION - OBTENIR ID UTILISATEUR
 * ============================================================================
 */
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * ============================================================================
 * FORMATAGE - PRIX AVEC DEVISE
 * ============================================================================
 * ✅ AMÉLIORÉ : Utilise la config + gère plusieurs devises
 * 
 * Usage :
 * <?= format_price(29.99) ?>           // "29,99 €"
 * <?= format_price(29.99, 'USD') ?>    // "$29.99"
 */
function format_price($price, $currency = null) {
    // Utiliser la devise de la config si non spécifiée
    $currency = $currency ?? (defined('APP_CURRENCY') ? APP_CURRENCY : 'EUR');
    
    // Normaliser le code devise
    $currency = strtoupper($currency);
    
    // Symboles de devises
    $symbols = [
        'EUR' => '€',
        'USD' => '$',
        'GBP' => '£',
        'CHF' => 'CHF',
        'CAD' => 'CA$',
        'JPY' => '¥',
        'CNY' => '¥'
    ];
    
    // Obtenir le symbole (ou utiliser le code si inconnu)
    $symbol = $symbols[$currency] ?? $currency;
    
    // Format différent selon la devise
    if (in_array($currency, ['USD', 'GBP', 'CAD', 'JPY', 'CNY'])) {
        // Dollar/Livre/Yen : symbole AVANT + point décimal
        return $symbol . number_format($price, 2, '.', ',');
    } else {
        // Euro/Franc : symbole APRÈS + virgule décimale
        return number_format($price, 2, ',', ' ') . ' ' . $symbol;
    }
}

/**
 * ============================================================================
 * FORMATAGE - DATE
 * ============================================================================
 */
function format_date($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * ============================================================================
 * FORMATAGE - DATE ET HEURE
 * ============================================================================
 */
function format_datetime($datetime) {
    if (empty($datetime)) return '';
    return date('d/m/Y à H:i', strtotime($datetime));
}

/**
 * ============================================================================
 * FORMATAGE - NOMBRE ABRÉGÉ (1K, 1M, etc.)
 * ============================================================================
 * Nouveau helper pour afficher les grands nombres de façon compacte
 * 
 * Usage :
 * <?= format_number_short(1500) ?>     // "1.5K"
 * <?= format_number_short(1500000) ?>  // "1.5M"
 */
function format_number_short($number) {
    if ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    }
    return $number;
}

/**
 * ============================================================================
 * SÉCURITÉ - GÉNÉRER TOKEN ALÉATOIRE
 * ============================================================================
 * Utile pour tokens de vérification email, reset password, etc.
 * 
 * Usage :
 * $token = generate_token(32);
 */
function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * ============================================================================
 * SÉCURITÉ - HASHER UN MOT DE PASSE
 * ============================================================================
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * ============================================================================
 * SÉCURITÉ - VÉRIFIER UN MOT DE PASSE
 * ============================================================================
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * ============================================================================
 * DEBUG - DUMP
 * ============================================================================
 */
function dump($var, $die = false) {
    echo '<pre style="background:#2c3e50;color:#ecf0f1;border:2px solid #e74c3c;padding:20px;margin:10px;border-radius:5px;font-size:14px;">';
    print_r($var);
    echo '</pre>';
    
    if ($die) {
        die();
    }
}

/**
 * ============================================================================
 * DEBUG - DUMP AND DIE
 * ============================================================================
 */
function dd($var) {
    dump($var, true);
}

/**
 * ============================================================================
 * STRING - TRUNCATE (COUPER TEXTE)
 * ============================================================================
 * Coupe un texte à une longueur donnée avec "..."
 * 
 * Usage :
 * <?= str_limit($product['description'], 100) ?>
 */
function str_limit($string, $limit = 100, $end = '...') {
    if (mb_strlen($string) <= $limit) {
        return $string;
    }
    return mb_substr($string, 0, $limit) . $end;
}

/**
 * ============================================================================
 * STRING - SLUG (URL-FRIENDLY)
 * ============================================================================
 * Convertit un texte en slug pour URL
 * 
 * Usage :
 * $slug = str_slug("Mon Super Produit !"); // "mon-super-produit"
 */
function str_slug($string) {
    $string = mb_strtolower($string);
    $string = preg_replace('/[^a-z0-9]+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}

/**
 * ============================================================================
 * ARRAY - PLUCK (EXTRAIRE COLONNE)
 * ============================================================================
 * Extrait une colonne d'un tableau d'objets/arrays
 * 
 * Usage :
 * $ids = array_pluck($products, 'id');
 */
function array_pluck($array, $key) {
    return array_map(function($item) use ($key) {
        return is_object($item) ? $item->$key : $item[$key];
    }, $array);
}

/**
 * ============================================================================
 * VALIDATION - EMAIL
 * ============================================================================
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * ============================================================================
 * VALIDATION - URL
 * ============================================================================
 */
function is_valid_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * ============================================================================
 * TEMPS - IL Y A (TIME AGO)
 * ============================================================================
 * Affiche "il y a X minutes/heures/jours"
 * 
 * Usage :
 * <?= time_ago($comment['created_at']) ?> // "il y a 2 heures"
 */
function time_ago($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'à l\'instant';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return 'il y a ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return 'il y a ' . $hours . ' heure' . ($hours > 1 ? 's' : '');
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return 'il y a ' . $days . ' jour' . ($days > 1 ? 's' : '');
    } else {
        return format_date($datetime);
    }
}