<?php
// Test ultra-simple pour voir si PHP fonctionne
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>✅ PHP fonctionne !</h1>";
echo "<p>Version PHP : " . phpversion() . "</p>";

// Test des variables d'environnement
echo "<h2>Variables d'environnement :</h2>";
echo "<ul>";
echo "<li>SMTP_HOST: " . ($_ENV['SMTP_HOST'] ?? $_SERVER['SMTP_HOST'] ?? '❌ Absent') . "</li>";
echo "<li>SMTP_USER: " . ($_ENV['SMTP_USER'] ?? $_SERVER['SMTP_USER'] ?? '❌ Absent') . "</li>";
echo "<li>SMTP_PASSWORD: " . (($_ENV['SMTP_PASSWORD'] ?? $_SERVER['SMTP_PASSWORD'] ?? false) ? '✅ Défini (caché)' : '❌ Absent') . "</li>";
echo "<li>SMTP_FROM: " . ($_ENV['SMTP_FROM'] ?? $_SERVER['SMTP_FROM'] ?? '❌ Absent') . "</li>";
echo "</ul>";

// Test de l'autoloader
echo "<h2>Test Composer autoloader :</h2>";
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "✅ Fichier autoload.php existe<br>";
    require_once __DIR__ . '/../vendor/autoload.php';
    echo "✅ Autoloader chargé avec succès<br>";
    
    // Test PHPMailer
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "✅ Classe PHPMailer disponible<br>";
    } else {
        echo "❌ Classe PHPMailer introuvable<br>";
    }
} else {
    echo "❌ Fichier autoload.php introuvable<br>";
}

echo "<hr>";
echo "<p><a href='test_brevo_final.php'>→ Tester l'envoi d'email</a></p>";
