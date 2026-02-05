<?php
// test_debug_vars.php - Debug VARIABLES SEULEMENT (pas PHPMailer)

// Forcer affichage immédiat (pas de buffer)
ob_start();
header('Content-Type: text/html; charset=utf-8');

// Titre visible dès le chargement
echo "<!DOCTYPE html><html lang='fr'><head><meta charset='utf-8'><title>Debug Vars - MarketFlow</title></head><body style='font-family:sans-serif; padding:40px;'>";
echo "<h1 style='color:darkgreen; text-align:center;'>DEBUG VARIABLES ENV - Étape isolée</h1>";
echo "<p style='font-size:1.2em;'>Si tu vois ce message → PHP tourne et le fichier est servi (200 OK confirmé).</p>";
echo "<hr style='border:2px solid green;margin:30px 0;'>";

// Liste des variables Railway + SMTP
$vars = [
    'RAILWAY_ENVIRONMENT', 'RAILWAY_GIT_COMMIT_SHA', // variables Railway natives
    'SMTP_HOST', 'SMTP_PORT', 'SMTP_USER', 'SMTP_PASSWORD', 'SMTP_FROM', 'SMTP_FROM_NAME', 'SMTP_REPLY_TO',
    'APP_ENV', 'APP_DEBUG' // pour vérifier debug
];

echo "<h2>Variables d'environnement</h2>";
echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse:collapse; width:100%; max-width:900px;'>";
echo "<tr><th>Variable</th><th>getenv()</th><th>\$_ENV</th><th>\$_SERVER</th></tr>";

foreach ($vars as $var) {
    $get = getenv($var);
    $env = $_ENV[$var] ?? '(absent)';
    $srv = $_SERVER[$var] ?? '(absent)';

    $get_disp = $get !== false ? htmlspecialchars($get) : '<span style="color:red;">absent</span>';
    if ($var === 'SMTP_PASSWORD' && $get !== false) $get_disp = '<span style="color:purple;">[présent - masqué]</span>';

    echo "<tr>";
    echo "<td><strong>$var</strong></td>";
    echo "<td>$get_disp</td>";
    echo "<td>" . htmlspecialchars($env) . "</td>";
    echo "<td>" . htmlspecialchars($srv) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p style='margin-top:40px; font-style:italic;'>Fin du debug. Recharge si besoin.</p>";
echo "</body></html>";

// Flush pour forcer affichage
ob_end_flush();
