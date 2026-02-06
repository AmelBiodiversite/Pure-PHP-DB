<?php
// test_brevo_api.php - ENVOI VIA API BREVO v3 (HTTP/HTTPS - port 443)

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html><html lang='fr'><head><meta charset='utf-8'><title>Test API Brevo v3</title></head><body style='font-family:sans-serif; padding:40px; background:#f0f8ff;'>";
echo "<h1 style='color:#006400; text-align:center;'>TEST ENVOI VIA API BREVO v3</h1>";
echo "<p style='font-size:1.3em; text-align:center;'>Méthode HTTP (port 443) → pas de blocage port SMTP sur Railway.</p><hr>";

// Récup clé API
$apiKey = getenv('BREVO_API_KEY');

if (!$apiKey) {
    die("<h2 style='color:red;'>BREVO_API_KEY manquante dans les variables Railway</h2>");
}

if (strpos($apiKey, 'xkeysib-') !== 0) {
    die("<h2 style='color:red;'>Clé invalide : doit commencer par xkeysib-</h2>");
}

echo "<p>Clé API détectée (longueur : " . strlen($apiKey) . " caractères)</p>";

// Payload API Brevo v3
$data = [
    'sender' => [
        'name'  => getenv('SMTP_FROM_NAME') ?: 'Market Flow',
        'email' => getenv('SMTP_FROM') ?: 'contact@marketflow.fr'
    ],
    'to' => [
        ['email' => 'a.devance@proton.me', 'name' => 'Test MarketFlow']
    ],
    'subject' => 'Test API Brevo v3 depuis Railway - ' . date('Y-m-d H:i:s'),
    'htmlContent' => '
        <html>
        <body>
            <h2>Ça marche via l\'API ! ✅</h2>
            <p>Ceci est un email envoyé en production depuis Railway via l\'API Brevo (sans SMTP).</p>
            <p>Date : ' . date('Y-m-d H:i:s') . '</p>
            <p>Vérifie ta boîte (y compris spam/promotions).</p>
        </body>
        </html>'
];

$ch = curl_init('https://api.brevo.com/v3/smtp/email');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: application/json',
    'api-key: ' . $apiKey,
    'content-type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);
curl_close($ch);

echo "<h3>Résultat de l'appel API</h3>";
echo "<pre style='background:#f8f8f8; padding:15px; border:1px solid #ccc; overflow:auto; font-size:1.1em;'>";
echo "HTTP Code : $httpCode\n";
echo "Erreur cURL : " . ($error ?: 'Aucune') . "\n";
echo "Réponse Brevo : " . htmlspecialchars($response) . "\n";
echo "</pre>";

if ($httpCode === 201) {
    echo "<h2 style='color:green; text-align:center;'>EMAIL ENVOYÉ AVEC SUCCÈS VIA API !</h2>";
    echo "<p style='text-align:center;'>Vérifie immédiatement ta boîte Proton.</p>";
} else {
    echo "<h2 style='color:red; text-align:center;'>ÉCHEC ENVOI API</h2>";
    echo "<p>Vérifie que ton domaine expéditeur (marketflow.fr) est vérifié dans Brevo → Senders → Domains.</p>";
}

echo "<hr><small>Fin du test - Recharge pour réessayer</small></body></html>";
