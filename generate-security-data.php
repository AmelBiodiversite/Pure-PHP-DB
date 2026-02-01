<?php
require_once __DIR__ . '/core/SecurityLogger.php';

echo "🔄 Génération de données de sécurité...\n\n";

$logger = new Core\SecurityLogger();

for ($i = 0; $i < 50; $i++) {
    $eventTypes = ['LOGIN_FAILED', 'LOGIN_SUCCESS', 'CSRF_VIOLATION', 'XSS_ATTEMPT', 'SQLI_ATTEMPT', 'SUSPICIOUS_REQUEST'];
    $ips = ['192.168.1.100', '10.0.0.50', '203.0.113.45', '198.51.100.23', '192.0.2.150'];
    
    $logger->log($eventTypes[array_rand($eventTypes)], [
        'ip' => $ips[array_rand($ips)],
        'user_agent' => 'Mozilla/5.0 Test',
        'username' => 'user' . rand(1, 10),
        'details' => 'Test event #' . ($i + 1)
    ]);
}

echo "✅ 50 événements générés !\n";
