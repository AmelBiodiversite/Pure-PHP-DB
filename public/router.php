<?php
// Router pour le serveur PHP intégré
// Sert les fichiers statiques correctement

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$file = __DIR__ . $uri;

// Si c'est un fichier statique qui existe
if ($uri !== '/' && file_exists($file)) {
    // Détecter le type MIME
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
    ];
    
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }
    
    return false; // Servir le fichier
}

// Sinon, router vers l'application
require_once __DIR__ . '/../index.php';

