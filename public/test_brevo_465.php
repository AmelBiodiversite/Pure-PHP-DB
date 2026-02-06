<?php
header('Content-Type: text/html; charset=utf-8');
ob_start();

echo "<!DOCTYPE html><html><head><title>Test Brevo Port 465</title></head><body>";
echo "<h1>Test ENVOI sur PORT 465 (SSL)</h1>";

require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug  = SMTP::DEBUG_CONNECTION;
    $mail->Debugoutput = 'html';

    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST') ?: 'smtp-relay.brevo.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USER');
    $mail->Password   = getenv('SMTP_PASSWORD');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  // ← CHANGEMENT : SSL au lieu de STARTTLS
    $mail->Port       = 465;  // ← PORT 465

    $mail->setFrom(getenv('SMTP_FROM') ?: 'contact@marketflow.fr', getenv('SMTP_FROM_NAME') ?: 'Market Flow');
    $mail->addAddress('a.devance@proton.me');

    $mail->isHTML(true);
    $mail->Subject = 'Test Brevo PORT 465 - ' . date('Y-m-d H:i:s');
    $mail->Body    = '<h2>Test sur port 465</h2><p>Si tu reçois ça → succès !</p>';

    $mail->send();
    echo "<h2 style='color:green;'>EMAIL ENVOYÉ (port 465)</h2>";

} catch (Exception $e) {
    echo "<h2 style='color:red;'>ÉCHEC</h2>";
    echo "<p>Erreur : " . htmlspecialchars($mail->ErrorInfo) . "</p>";
    echo "<pre>" . nl2br(htmlspecialchars($mail->SMTPDebugOutput ?? 'No debug')) . "</pre>";
}

ob_end_flush();
