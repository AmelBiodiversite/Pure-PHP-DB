<?php
// ============================================
// MARKETFLOW PRO - POINT D'ENTRÉE
// ============================================

// 1️⃣ Démarrer la session en tout premier
session_start();

// 2️⃣ Afficher les erreurs en mode développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 3️⃣ Charger la configuration et helpers
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';

// 4️⃣ Autoloader simple pour Core et App
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/';

    // Convertir namespace en chemin
    $class = str_replace('\\', '/', $class);
    $class = str_replace('App/', 'app/', $class);
    $class = str_replace('Core/', 'core/', $class);

    $file = $baseDir . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// 5️⃣ Charger explicitement le Router et les classes de base
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Model.php';

// 6️⃣ Charger et exécuter les routes
require_once __DIR__ . '/config/routes.php';
