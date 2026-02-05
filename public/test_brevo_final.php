<?php
/**
 * Test SMTP Brevo sur Railway
 * Fichier de test pour vérifier la configuration email
 */

// Activer l'affichage des erreurs pour le debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "🔍 Début du test Brevo SMTP...<br><br>";

// Charger l'autoloader Composer
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Fonction helper pour récupérer les variables d'environnement
// Railway utilise $_ENV ou $_SERVER, pas getenv()
function env($key, $default = null) {
    return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
}

// Vérification des variables d'environnement
echo "<h3>📋 Vérification des variables d'environnement :</h3>";
echo "<ul>";
echo "<li>SMTP_HOST: " . (env('SMTP_HOST') ? '✅ Défini' : '❌ Manquant') . "</li>";
echo "<li>SMTP_PORT: " . (env('SMTP_PORT') ? '✅ Défini (' . env('SMTP_PORT') . ')' : '❌ Manquant') . "</li>";
echo "<li>SMTP_USER: " . (env('SMTP_USER') ? '✅ Défini' : '❌ Manquant') . "</li>";
echo "<li>SMTP_PASSWORD: " . (env('SMTP_PASSWORD') ? '✅ Défini (caché)' : '❌ Manquant') . "</li>";
echo "<li>SMTP_FROM: " . (env('SMTP_FROM') ? '✅ ' . htmlspecialchars(env('SMTP_FROM')) : '❌ Manquant') . "</li>";
echo "<li>SMTP_FROM_NAME: " . (env('SMTP_FROM_NAME') ? '✅ ' . htmlspecialchars(env('SMTP_FROM_NAME')) : '⚠️ Optionnel') . "</li>";
echo "</ul><br>";

// Vérification que toutes les variables nécessaires sont présentes
$required_vars = ['SMTP_HOST', 'SMTP_USER', 'SMTP_PASSWORD', 'SMTP_FROM'];
$missing_vars = [];

foreach ($required_vars as $var) {
    if (!env($var)) {
        $missing_vars[] = $var;
    }
}

if (!empty($missing_vars)) {
    echo "<h3 style='color: red;'>❌ Variables d'environnement manquantes :</h3>";
    echo "<ul>";
    foreach ($missing_vars as $var) {
        echo "<li><code>$var</code></li>";
    }
    echo "</ul>";
    echo "<p><strong>Action :</strong> Ajoutez ces variables dans Railway → Settings → Variables</p>";
    exit;
}

// Création de l'instance PHPMailer
$mail = new PHPMailer(true);

try {
    echo "<h3>📧 Configuration de PHPMailer...</h3>";
    
    // Configuration SMTP avec debug activé
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;  // Niveau de debug maximal
    $mail->Debugoutput = 'html';            // Format HTML pour le debug
    
    $mail->isSMTP();
    $mail->Host       = env('SMTP_HOST');
    $mail->SMTPAuth   = true;
    $mail->Username   = env('SMTP_USER');
    $mail->Password   = env('SMTP_PASSWORD');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int)(env('SMTP_PORT', 587));
    
    // Configuration de l'expéditeur
    $mail->setFrom(
        env('SMTP_FROM', 'contact@marketflow.fr'),
        env('SMTP_FROM_NAME', 'MarketFlow')
    );
    
    // Destinataire
    $mail->addAddress('a.devance@proton.me', 'Test MarketFlow');
    
    // Reply-To optionnel
    if (env('SMTP_REPLY_TO')) {
        $mail->addReplyTo(env('SMTP_REPLY_TO'));
    }
    
    // Contenu de l'email
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = '✅ Test SMTP Brevo – MarketFlow – ' . date('Y-m-d H:i:s');
    $mail->Body    = '
        <div style="font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5;">
            <div style="background: white; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto;">
                <h2 style="color: #4CAF50;">✅ Test réussi !</h2>
                <p>Ceci est un email de test envoyé depuis <strong>MarketFlow</strong> via <strong>Brevo SMTP</strong>.</p>
                
                <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">
                
                <p><strong>📅 Date/heure :</strong> ' . date('d/m/Y à H:i:s') . '</p>
                <p><strong>🚀 Serveur :</strong> Railway</p>
                <p><strong>📧 SMTP :</strong> ' . htmlspecialchars(env('SMTP_HOST')) . '</p>
                
                <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">
                
                <p style="color: #4CAF50; font-weight: bold;">Si tu reçois ce message → la configuration fonctionne parfaitement ! 🎉</p>
                
                <p style="font-size: 12px; color: #999; margin-top: 30px;">
                    Debug activé pour ce test — à désactiver en production.
                </p>
            </div>
        </div>
    ';
    
    // Version texte brut (fallback)
    $mail->AltBody = strip_tags(str_replace('<br>', "\n", $mail->Body));
    
    echo "<h3>📤 Envoi de l'email en cours...</h3>";
    echo "<div style='background: #f0f0f0; padding: 15px; border-left: 4px solid #2196F3; margin: 20px 0;'>";
    
    // Envoi
    $mail->send();
    
    echo "</div>";
    
    echo '<div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 20px; border-radius: 5px; margin-top: 20px;">';
    echo '<h3 style="margin-top: 0;">✅ Email envoyé avec succès !</h3>';
    echo '<p><strong>Destinataire :</strong> a.devance@proton.me</p>';
    echo '<p><strong>Action :</strong> Vérifie ta boîte de réception (y compris spam / promotions).</p>';
    echo '</div>';
    
} catch (Exception $e) {
    echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 5px; margin-top: 20px;">';
    echo '<h3 style="margin-top: 0;">❌ Échec de l\'envoi</h3>';
    echo '<p><strong>Erreur PHPMailer :</strong> ' . htmlspecialchars($mail->ErrorInfo) . '</p>';
    
    // Afficher les détails SMTP debug
    if (!empty($mail->SMTPDebugOutput)) {
        echo '<details style="margin-top: 20px;">';
        echo '<summary style="cursor: pointer; font-weight: bold;">📋 Détails SMTP (cliquez pour voir)</summary>';
        echo '<pre style="background: #fff; padding: 15px; border: 1px solid #ddd; margin-top: 10px; overflow-x: auto;">';
        echo htmlspecialchars($mail->SMTPDebugOutput);
        echo '</pre>';
        echo '</details>';
    }
    
    echo '</div>';
}

echo "<hr>";
echo "<p style='color: #666; font-size: 12px;'>Test effectué depuis : " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "</p>";
