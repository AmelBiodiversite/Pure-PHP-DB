<?php
die('
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Maintenance - MarketFlow</title>
<style>
body{margin:0;font-family:Arial;display:flex;align-items:center;justify-content:center;height:100vh;background:#0a0c0f;color:#fff}
.box{text-align:center;padding:40px}
h1{font-size:48px;margin:0 0 20px}
p{font-size:18px;color:#8494b0}
</style>
</head>
<body>
<div class="box">
<h1>🔧 Maintenance en cours</h1>
<p>Nous revenons très bientôt.<br>Merci de votre patience.</p>
</div>
</body>
</html>
');


/**
 * ============================================================================
 * MARKETFLOW PRO - POINT D'ENTRÉE PRINCIPAL
 * ============================================================================
 * Fichier : index.php
 * ============================================================================
 */

// Configuration PHP
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Paris');

// 🔒 CHARGER LA CONFIGURATION DES SESSIONS SÉCURISÉES
// ⚠️ IMPORTANT : Doit être chargé AVANT session_start()
require_once __DIR__ . '/config/session.php';

// Démarrer la session
session_start();

// 🔒 CHARGER LES HEADERS DE SÉCURITÉ
require_once __DIR__ . '/config/security_headers.php';

// Charger les helpers
// require_once __DIR__ . '/app/helpers/auth_helper.php'; // Chargé par Composer
// require_once __DIR__ . '/app/helpers/functions.php'; // Chargé par Composer
// require_once __DIR__ . '/app/helpers/security_helper.php'; // Chargé par Composer

// Charger Composer (pour Stripe, etc.)
require_once __DIR__ . '/vendor/autoload.php';

// Autoloader personnalisé pour les classes de l'application
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/';
    $path = str_replace('\\', '/', $class);
    
    // Tentatives de chargement avec différentes casses
    $attempts = [
        $baseDir . $path . '.php',
        $baseDir . strtolower($path) . '.php'
    ];
    
    $parts = explode('/', $path);
    
    // Essayer avec le premier segment en minuscule
    if (count($parts) >= 2) {
        $parts[0] = strtolower($parts[0]);
        $attempts[] = $baseDir . implode('/', $parts) . '.php';
    }
    
    // Essayer avec les deux premiers segments en minuscule
    if (count($parts) >= 3) {
        $parts[1] = strtolower($parts[1]);
        $attempts[] = $baseDir . implode('/', $parts) . '.php';
    }
    
    // Gestion spéciale du namespace Core\
    if (strpos($class, 'Core\\') === 0) {
        $attempts[] = $baseDir . str_replace('Core\\', 'core/', $class) . '.php';
    }
    
    // Essayer de charger le fichier
    foreach ($attempts as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Charger la configuration de l'application
require_once __DIR__ . '/config/config.php';

// Charger et exécuter le routeur
require_once __DIR__ . '/config/routes.php';
