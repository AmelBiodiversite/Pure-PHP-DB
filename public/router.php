<?php
/**
 * Router pour le serveur PHP intégré
 * 
 * Ordre de traitement :
 * 1. Assets statiques (CSS, JS, images) → servir directement
 * 2. Fichiers PHP existants dans public/ (ping.php, test_*.php) → servir directement  
 * 3. Fichiers HTML existants dans public/ → servir directement
 * 4. Tout le reste → framework MarketFlow via index.php
 */

// 1. Assets statiques : CSS, JS, images, fonts, etc.
if (preg_match('/\.(?:png|jpg|jpeg|gif|ico|css|js|svg|woff|woff2|ttf|eot|map)$/i', $_SERVER["REQUEST_URI"])) {
    return false;
}

// 2. Fichiers PHP/HTML existants dans public/ (sauf index.php)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestedFile = __DIR__ . $uri;
if ($uri !== '/' && $uri !== '/index.php' && is_file($requestedFile)) {
    // C'est un fichier PHP ? L'inclure directement pour l'exécuter
    if (pathinfo($requestedFile, PATHINFO_EXTENSION) === 'php') {
        require $requestedFile;
        return true;
    }
    // Sinon (HTML, etc.) laisser le serveur le servir
    return false;
}

// 3. Tout le reste → framework MarketFlow
require_once __DIR__ . '/index.php';
