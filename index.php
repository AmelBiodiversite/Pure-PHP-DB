<?php
/**
 * MARKETFLOW PRO - Point d'entrée
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

// Autoload
spl_autoload_register(function ($class) {
    // Debug
    // error_log("Autoloading: " . $class);
    
    $classFile = str_replace('\\', '/', $class);
    
    // Gérer les namespaces App et Core vers les dossiers minuscules app/ et core/
    if (str_starts_with($classFile, 'App/')) {
        $classFile = 'app/' . substr($classFile, 4);
    } elseif (str_starts_with($classFile, 'Core/')) {
        $classFile = 'core/' . substr($classFile, 5);
    }

    $file = __DIR__ . '/' . $classFile . '.php';
    // error_log("Looking for file: " . $file);
    
    if(file_exists($file)) {
        require_once $file;
    }
});

// Fichiers helpers et config
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/config/routes.php';
