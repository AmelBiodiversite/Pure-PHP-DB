<?php
// Point d'entrée pour le serveur PHP intégré
// Redirige vers le vrai index.php à la racine

// Si c'est un fichier statique (CSS, JS, images), le servir directement
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = __DIR__ . $path;
    
    if (is_file($file)) {
        return false; // Servir le fichier statique
    }
}

// Sinon, router vers l'application
require_once __DIR__ . '/../index.php';
