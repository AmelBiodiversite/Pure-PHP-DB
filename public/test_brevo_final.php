<?php
// test_brevo_final.php - ENVOI RÉEL AVEC DEBUG MAX (pas de die())

header('Content-Type: text/html; charset=utf-8');
ob_start(); // on bufferise pour capturer tout

echo "<!DOCTYPE html><html lang='fr'><head><meta charset='utf-8'><title>Envoi Email Brevo - Debug</title></head><body style='font-family:sans-serif; padding:40px; background:#f8f8f8;'>";
echo "<h1 style='color:#006400;'>TEST ENVOI EMAIL BREVO - VERSION FINALE</h1>";
echo "<p style='font-size:1.2em;'>Variables OK → on tente l'envoi réel maintenant.</p>";
echo "<hr style='border:2px solid #006400;'>";

// Chargement PHPMailer
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo "<h2>PHPMailer chargé</h2>";
echo "<p>Version : " . PHPMailer::VERSION . "</p>";

$mail = new PHPMailer(true);

try {
    echo "<h3>Configuration SMTP</h3><pre>";

    $host     = getenv('SMTP_HOST')     ?: 'smtp-relay.brevo.com';
    $port     = (int)(getenv('SMTP_PORT') ?: 587);
    $user     = getenv('SMTP_USER');
    $pass     = getenv('SMTP_PASSWORD');
    $from     = getenv('SMTP_FROM')     ?: 'contact@marketflow.fr';
    $fromName = getenv('SMTP_FROM_NAME') ?: 'Market Flow';

    echo "Host     : $host\n";
    echo "Port     : $port\n";
    echo "User     : " . ($user ? htmlspecialchars($user) : 'ABSENT') . "\n";
    echo "Password : " . ($pass ? '[présent]' : 'ABSENT') . "\n";
    echo "From     : $from ($fromName)\n";
    echo "</pre>";

    // SMTP config
    $mail->SMTPDebug  = SMTP::DEBUG_CONNECTION;  // 2 = conversations détaillées
    $mail->Debugoutput = 'html';
    $mail->isSMTP();
    $mail->Host       = $host;
    $mail->SMTPAuth   = true;
    $mail->Username   = $user;
    $mail->Password   = $pass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = $port;

    // Expéditeur
    $mail->setFrom($from, $fromName);

    // Destinataire
    $mail->addAddress('a.devance@proton.me', 'Test MarketFlow');

    // Contenu
    $mail->isHTML(true);
    $mail->Subject = 'Test ENVOI RÉEL Brevo depuis MarketFlow - ' . date('Y-m-d H:i:s');
    $mail->Body    = '
        <h2>Ça marche !</h2>
        <p>Ceci est un email envoyé en PROD depuis Railway via Brevo SMTP.</p>
        <p>Date : ' . date('Y-m-d H:i:s') . '</p>
        <p>Si tu reçois ça → tout est OK ✅</p>
    ';
    $mail->AltBody = strip_tags($mail->Body);

    echo "<h3>Tentative d'envoi...</h3>";

    $mail->send();

    echo "<h2 style='color:green;'>EMAIL ENVOYÉ AVEC SUCCÈS !</h2>";
    echo "<p>Vérifie ta boîte Proton (y compris spam / promotions).</p>";

} catch (Exception $e) {
    echo "<h2 style='color:red;'>ÉCHEC ENVOI</h2>";
    echo "<p><strong>Erreur :</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre style='background:#fee; padding:15px; border:1px solid red; overflow:auto;'>";
    echo nl2br(htmlspecialchars($mail->SMTPDebugOutput ?? 'Aucun debug disponible'));
    echo "</pre>";
}

echo "<hr><small>Fin du test - Recharge pour réessayer</small></body></html>";

// Flush tout
ob_end_flush();
