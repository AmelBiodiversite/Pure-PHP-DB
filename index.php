<?php
// 1. Initialisation système
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2. Chargement des helpers
require_once __DIR__ . '/app/helpers/auth_helper.php';

// 3. Autoloader Flexible (Gère les problèmes de casse)
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/';
    $path = str_replace('\\', '/', $class);
    
    // Tentative 1 : Chemin exact (ex: Core/Database.php)
    $file1 = $baseDir . $path . '.php';
    
    // Tentative 2 : Chemin tout en minuscules (ex: core/database.php)
    $file2 = $baseDir . strtolower($path) . '.php';

    if (file_exists($file1)) {
        require_once $file1;
    } elseif (file_exists($file2)) {
        require_once $file2;
    }
});

// 4. Configuration et Routage
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/routes.php';
