<?php
/**
 * MARKETFLOW PRO - POINT D'ENTRÉE
 * Fichier : index.php (racine du projet)
 */

// Afficher les erreurs en mode développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Charger la configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';

// Autoloader corrigé pour PostgreSQL + Namespaces
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/';

    // Convertir namespace en chemin
    // App\Models\User -> app/models/User.php
    // Core\Model -> core/Model.php
    $path = str_replace('\\', '/', $class);

    // Remplacer les namespaces par les dossiers
    $path = str_replace('App/', 'app/', $path);
    $path = str_replace('Core/', 'core/', $path);

    // Convertir les dossiers en minuscules SAUF le nom du fichier
    // app/Models/User -> app/models/User
    $parts = explode('/', $path);

    // Mettre en minuscules tous les dossiers (sauf le dernier qui est le fichier)
    for ($i = 0; $i < count($parts) - 1; $i++) {
        $parts[$i] = strtolower($parts[$i]);
    }

    $path = implode('/', $parts);
    $file = $baseDir . $path . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Charger le routeur
require_once __DIR__ . '/core/Router.php';

// Charger et exécuter les routes
require_once __DIR__ . '/config/routes.php';
