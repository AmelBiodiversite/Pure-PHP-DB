<?php
// test_brevo_final.php - Test SMTP Brevo 2026 (exécuté depuis la racine)

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Debug activé (niveau 2 = conversations SMTP) — enlève ou mets à 0 en prod
    $mail->SMTPDebug = 2;          // DEBUG_CONNECTION ou 2 pour voir le dialogue
    $mail->Debugoutput = 'html';   // Pour que ce soit lisible dans le navigateur

    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST')     ?: die('SMTP_HOST absent');
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USER')     ?: die('SMTP_USER absent');
    $mail->Password   = getenv('SMTP_PASSWORD') ?: die('SMTP_PASSWORD absent');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // 'tls' pour port 587
    $mail->Port       = (int)(getenv('SMTP_PORT') ?: 587);

    // Expéditeur
    $mail->setFrom(
        getenv('SMTP_FROM') ?: 'contact@marketflow.fr',
        getenv('SMTP_FROM_NAME') ?: 'MarketFlow Pro'
    );

    // Destinataire de test (ton email Proton)
    $mail->addAddress('a.devance@proton.me', 'Test MarketFlow');

    // Optionnel : Reply-To (si tu as SMTP_REPLY_TO)
    if (getenv('SMTP_REPLY_TO')) {
        $mail->addReplyTo(getenv('SMTP_REPLY_TO'));
    }

    // Contenu du mail
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
    echo '<h3 style="color: green;">Email envoyé avec succès !</h3>' . PHP_EOL;
    echo '<p>Vérifie ta boîte de réception (y compris spam / promotions / updates).</p>' . PHP_EOL;

} catch (Exception $e) {
    echo '<h3 style="color: red;">Échec de l\'envoi</h3>' . PHP_EOL;
    echo 'Erreur PHPMailer : ' . $mail->ErrorInfo . '<br><br>' . PHP_EOL;
    echo '<pre>Sortie debug SMTP :' . PHP_EOL;
    echo nl2br(htmlentities($mail->SMTPDebugOutput ?? 'Aucun détail disponible')) . '</pre>' . PHP_EOL;
}
