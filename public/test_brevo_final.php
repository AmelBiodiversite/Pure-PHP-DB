<?php
// ----------------------------------------------------------------------
// DEBUG ULTRA-PRÉCOCE - LIGNE 1 : on affiche ÇA AVANT TOUT LE RESTE
// Si tu vois ce bloc → PHP s'exécute et le fichier est bien servi
// ----------------------------------------------------------------------
header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html><html lang=\"fr\"><head><meta charset=\"utf-8\"><title>Debug Brevo - Étape 0</title></head><body style=\"font-family: sans-serif;\">";
echo "<h1 style=\"color: darkblue; text-align: center; margin: 40px 0;\">";
echo "PHASE 0 : SCRIPT DÉMARRÉ - PHP FONCTIONNE";
echo "</h1>";
echo "<p style=\"font-size: 1.4em; color: #333; text-align: center;\">";
echo "Tu vois cette page → le problème de page blanche n'est PAS l'exécution PHP ni le chemin du fichier.";
echo "<br><strong>Maintenant on va tester les variables d'environnement.</strong>";
echo "</p>";
echo "<hr style=\"border: 3px solid darkblue;\">";

// Test immédiat des variables (getenv + $_ENV)
echo "<h2>Variables d'environnement (getenv + \$_ENV)</h2>";
echo "<pre style=\"background:#f8f8f8; padding:15px; border:1px solid #ccc; font-size:1.1em;\">";

// Liste des variables qu'on cherche
$vars = ['SMTP_HOST', 'SMTP_PORT', 'SMTP_USER', 'SMTP_PASSWORD', 'SMTP_FROM', 'SMTP_FROM_NAME'];

foreach ($vars as $v) {
    $val_getenv = getenv($v);
    $val_env    = $_ENV[$v] ?? '(non défini dans \$_ENV)';
    $val_server = $_SERVER[$v] ?? '(non défini dans \$_SERVER)';

    echo "<strong>$v</strong>:\n";
    echo "  getenv()     → " . ($val_getenv !== false ? htmlspecialchars($val_getenv) : '(vide ou absent)') . "\n";
    echo "  \$_ENV        → " . htmlspecialchars($val_env) . "\n";
    echo "  \$_SERVER     → " . htmlspecialchars($val_server) . "\n\n";
}

echo "</pre>";

// On continue seulement si on veut (le reste du code PHPMailer arrive après)
echo "<h2>Si tu vois ça → les variables sont affichées. On continue ?</h2>";

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    echo "<p style='color:green'>PHPMailer chargé avec succès</p>";
    // ... (le reste du code original ici, on le remettra après)
    echo "<p>Fin du try (temporaire)</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>Exception : " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
