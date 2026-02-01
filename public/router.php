<?php
/**
 * Router pour le serveur PHP intégré
 */

// Si c'est un fichier statique (CSS, JS, images, etc.)
if (preg_match('/\.(?:png|jpg|jpeg|gif|ico|css|js|svg|woff|woff2|ttf|eot|map)$/i', $_SERVER["REQUEST_URI"])) {
    // Retourner false pour que PHP serve le fichier directement
    return false;
}

// Sinon, charger index.php
require_once __DIR__ . '/index.php';
