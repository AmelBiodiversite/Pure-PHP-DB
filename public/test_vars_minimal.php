<?php
// test_vars_minimal.php - TEST VARIABLES UNIQUEMENT - PAS DE DIE, PAS DE PHPMailer

// Forcer affichage immédiat
ob_start();
header('Content-Type: text/html; charset=utf-8');

// Bloc visible dès le début
echo "<!DOCTYPE html><html lang='fr'><head><meta charset='utf-8'><title>Test Minimal Vars</title></head><body style='font-family:sans-serif; padding:50px; background:#f9f9f9;'>";
echo "<h1 style='color:#006400; text-align:center; font-size:2.5em;'>TEST VARIABLES MINIMAL - ÉTAPE ISOLÉE</h1>";
echo "<p style='font-size:1.4em; text-align:center; color:#333;'>Si tu vois ce titre vert → PHP tourne parfaitement sur Railway.</p>";
echo "<hr style='border:3px solid #006400; margin:30px 0;'>";

// Affichage des variables sans arrêter le script si vide
$vars = ['SMTP_HOST', 'SMTP_PORT', 'SMTP_USER', 'SMTP_PASSWORD', 'SMTP_FROM', 'SMTP_FROM_NAME', 'SMTP_REPLY_TO', 'APP_ENV', 'APP_DEBUG'];

echo "<h2>Variables d'environnement (sans die() - valeurs brutes)</h2>";
echo "<table border='1' cellpadding='12' cellspacing='0' style='border-collapse:collapse; width:100%; max-width:1000px; margin:20px auto; font-size:1.1em;'>";
echo "<tr style='background:#eee;'><th>Variable</th><th>getenv()</th><th>\$_ENV</th></tr>";

foreach ($vars as $var) {
    $get = getenv($var);
    $env = $_ENV[$var] ?? '(non défini)';

    $get_disp = $get !== false ? htmlspecialchars($get) : '<span style="color:red;">vide ou absent</span>';
    if ($var === 'SMTP_PASSWORD' && $get !== false) $get_disp = '<span style="color:purple;">[présent - valeur masquée pour sécurité]</span>';

    echo "<tr>";
    echo "<td><strong>$var</strong></td>";
    echo "<td>$get_disp</td>";
    echo "<td>" . htmlspecialchars($env) . "</td>";
    echo "</tr>";
}

echo "</table>";

// Message final pour confirmer que le script est allé jusqu'au bout
echo "<h2 style='color:#006400; text-align:center; margin-top:50px;'>Fin du script - Aucun die() n'a été déclenché</h2>";
echo "<p style='text-align:center;'>Recharge la page si besoin. Si tu vois ce message → les variables sont lues (ou vides), et on peut passer à PHPMailer.</p>";
echo "</body></html>";

// Flush tout
ob_end_flush();
