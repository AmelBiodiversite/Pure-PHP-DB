<?php
/**
 * Test ultra-verbeux pour Railway
 */

// Capturer TOUTES les erreurs
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

// Buffer de sortie pour capturer les erreurs
ob_start();

try {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Ping Test</title></head><body>";
    echo "<h1>Début du test...</h1>";
    
    echo "<p>✅ Étape 1 : PHP démarre</p>";
    
    echo "<p>✅ Étape 2 : Date = " . date('Y-m-d H:i:s') . "</p>";
    
    echo "<p>✅ Étape 3 : PHP Version = " . phpversion() . "</p>";
    
    echo "<p>✅ Étape 4 : Host = " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "</p>";
    
    echo "<p>✅ Étape 5 : REQUEST_URI = " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</p>";
    
    echo "<h2>🎉 Test réussi !</h2>";
    echo "<p><a href='/'>Retour à l'accueil</a></p>";
    echo "</body></html>";
    
} catch (Throwable $e) {
    echo "<h1 style='color:red;'>❌ ERREUR</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

// Envoyer le buffer
ob_end_flush();
