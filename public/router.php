<?php
/**
 * Router pour le serveur PHP intégré
 * 
 * Ce fichier est passé en argument au serveur PHP intégré :
 *   php -S 0.0.0.0:8080 -t public public/router.php
 * 
 * "return false" dit au serveur de servir le fichier tel quel.
 * Cela ne fonctionne QUE dans ce fichier (pas dans un fichier require'd).
 */

// Récupérer le chemin demandé (sans query string)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Construire le chemin réel du fichier dans public/
$requestedFile = __DIR__ . $uri;

// Si le fichier existe physiquement dans public/, le servir directement.
// Cela couvre : les fichiers .php de test (ping.php, test_simple.php, etc.),
// les fichiers .html statiques, ET les assets (CSS, JS, images).
if ($uri !== '/' && is_file($requestedFile)) {
    return false;
}

// Sinon, charger le framework MarketFlow via index.php
require_once __DIR__ . '/index.php';
