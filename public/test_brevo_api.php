<?php
/**
 * Test envoi email via API HTTP Brevo (pas SMTP)
 * Utilise curl — fonctionne même si Railway bloque les ports SMTP
 */
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html><html lang='fr'><head><meta charset='utf-8'><title>Test API Brevo</title></head>";
echo "<body style='font-family:sans-serif; padding:40px; background:#f8f8f8;'>";
echo "<h1 style='color:#006400;'>TEST ENVOI EMAIL - API HTTP BREVO</h1>";

// Récupérer la clé API depuis les variables d'environnement
$apiKey   = getenv('BREVO_API_KEY') ?: getenv('SMTP_PASSWORD');
$from     = getenv('SMTP_FROM')     ?: 'contact@marketflow.fr';
$fromName = getenv('SMTP_FROM_NAME') ?: 'MarketFlow';

echo "<h3>Configuration</h3><pre>";
echo "API Key  : " . ($apiKey ? '[présente - ' . strlen($apiKey) . ' chars]' : 'ABSENTE') . "\n";
echo "From     : $from ($fromName)\n";
echo "</pre>";

// Préparer la requête API Brevo
$data = [
    'sender'  => ['name' => $fromName, 'email' => $from],
    'to'      => [['email' => 'a.devance@proton.me', 'name' => 'Test MarketFlow']],
    'subject' => 'Test API Brevo depuis MarketFlow - ' . date('Y-m-d H:i:s'),
    'htmlContent' => '<h2>Email envoyé via API HTTP Brevo</h2>
        <p>Ceci prouve que l\'envoi fonctionne depuis Railway.</p>
        <p>Date : ' . date('Y-m-d H:i:s') . '</p>
        <p>Si tu reçois ça → tout est OK ✅</p>'
];

echo "<h3>Tentative d'envoi via API HTTP...</h3>";

// Appel API Brevo via curl
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => 'https://api.brevo.com/v3/smtp/email',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($data),
    CURLOPT_HTTPHEADER     => [
        'accept: application/json',
        'api-key: ' . $apiKey,
        'content-type: application/json'
    ],
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_VERBOSE        => true
]);

// Capturer le debug curl
$verbose = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

$response   = curl_exec($ch);
$httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError  = curl_error($ch);
curl_close($ch);

// Afficher le debug curl
rewind($verbose);
$verboseLog = stream_get_contents($verbose);
fclose($verbose);

echo "<h3>Résultat</h3>";
echo "<p>Code HTTP : <strong>$httpCode</strong></p>";

if ($httpCode === 201) {
    echo "<h2 style='color:green;'>✅ EMAIL ENVOYÉ AVEC SUCCÈS !</h2>";
    echo "<p>Vérifie ta boîte Proton (y compris spam/promotions).</p>";
} else {
    echo "<h2 style='color:red;'>❌ ÉCHEC ENVOI</h2>";
    if ($curlError) {
        echo "<p><strong>Erreur curl :</strong> " . htmlspecialchars($curlError) . "</p>";
    }
}

echo "<h3>Réponse Brevo</h3>";
echo "<pre style='background:#eee; padding:15px; border:1px solid #ccc; overflow:auto;'>";
echo htmlspecialchars($response);
echo "</pre>";

echo "<h3>Debug curl</h3>";
echo "<pre style='background:#eee; padding:15px; border:1px solid #ccc; overflow:auto;'>";
echo htmlspecialchars($verboseLog);
echo "</pre>";

echo "<hr><small>Fin du test API</small></body></html>";
