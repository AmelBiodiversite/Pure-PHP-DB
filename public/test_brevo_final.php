<?php
// -----------------------------------------------------------------
// DEBUG IMMEDIAT - LIGNE 1 : on affiche ça AVANT tout le reste
// Si tu vois ce message → le fichier est exécuté et PHP tourne
// -----------------------------------------------------------------
echo "<!DOCTYPE html><html><head><title>Debug Test Brevo</title></head><body>";
echo "<h1 style='color: blue; text-align: center; margin: 50px;'>";
echo "SCRIPT TEST_BREVO_FINAL.PHP → EXÉCUTÉ AVEC SUCCÈS (étape 1/3)";
echo "</h1>";
echo "<p style='font-size: 1.3em; color: #444;'>";
echo "Si tu vois cette page bleue → le problème n'est PAS l'autoload ni le chemin du fichier.<br>";
echo "On va maintenant tester les variables d'environnement et PHPMailer.";
echo "</p>";
echo "<hr style='border: 2px solid blue;'>";

// Le reste du code original suit ici (require, PHPMailer, etc.)
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'html';

    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST')     ?: die('SMTP_HOST absent');
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USER')     ?: die('SMTP_USER absent');
    $mail->Password   = getenv('SMTP_PASSWORD') ?: die('SMTP_PASSWORD absent');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int)(getenv('SMTP_PORT') ?: 587);

    $mail->setFrom(
        getenv('SMTP_FROM') ?: 'contact@marketflow.fr',
        getenv('SMTP_FROM_NAME') ?: 'MarketFlow Pro'
    );

    $mail->addAddress('a.devance@proton.me', 'Test MarketFlow');

    if (getenv('SMTP_REPLY_TO')) {
        $mail->addReplyTo(getenv('SMTP_REPLY_TO'));
    }

    $mail->isHTML(true);
    $mail->Subject = 'Test SMTP Brevo – MarketFlow – ' . date('Y-m-d H:i:s');
    $mail->Body    = '
        <h2>Test réussi ? ✅</h2>
        <p>Ceci est un email de test envoyé depuis MarketFlow via Brevo SMTP.</p>
        <p>Date/heure : ' . date('Y-m-d H:i:s') . '</p>
        <p>Si tu reçois ce message → la configuration fonctionne !</p>
        <br><small>Debug activé pour ce test — à désactiver après.</small>
    ';
    $mail->AltBody = strip_tags($mail->Body);

    $mail->send();
    echo '<h3 style="color: green;">Email envoyé avec succès !</h3>';
    echo '<p>Vérifie ta boîte (spam/promotions inclus).</p>';

} catch (Exception $e) {
    echo '<h3 style="color: red;">Échec de l\'envoi</h3>';
    echo '<p>Erreur : ' . htmlspecialchars($mail->ErrorInfo) . '</p>';
    echo '<pre style="background:#fee;padding:15px;border:1px solid red;">';
    echo nl2br(htmlspecialchars($mail->SMTPDebugOutput ?? 'Aucun détail disponible'));
    echo '</pre>';
}

echo "</body></html>";
