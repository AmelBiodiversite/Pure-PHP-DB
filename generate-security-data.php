<?php
// Charger l'autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Charger SecurityLogger manuellement si nécessaire
if (!class_exists('SecurityLogger')) {
    require_once __DIR__ . '/core/SecurityLogger.php';
}

echo "🔄 Génération de données de sécurité...\n\n";

try {
    $logger = new SecurityLogger();
    
    // Générer 50 événements variés
    for ($i = 0; $i < 50; $i++) {
        $eventTypes = ['LOGIN_FAILED', 'LOGIN_SUCCESS', 'CSRF_VIOLATION', 'XSS_ATTEMPT', 'SQLI_ATTEMPT', 'SUSPICIOUS_REQUEST'];
        $ips = ['192.168.1.100', '10.0.0.50', '203.0.113.45', '198.51.100.23', '192.0.2.150'];
        
        $type = $eventTypes[array_rand($eventTypes)];
        $ip = $ips[array_rand($ips)];
        
        $logger->log($type, [
            'ip' => $ip,
            'user_agent' => 'Mozilla/5.0 Test',
            'username' => 'user' . rand(1, 10),
            'details' => 'Generated test event #' . ($i + 1)
        ]);
    }
    
    echo "✅ 50 événements générés !\n\n";
    
    // Vérifier
    echo "📊 Fichiers créés :\n";
    if (file_exists('data/logs/security.log')) {
        echo "✅ security.log : " . filesize('data/logs/security.log') . " bytes\n";
        echo "Dernières lignes :\n";
        echo shell_exec('tail -5 data/logs/security.log');
    } else {
        echo "❌ security.log non créé\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
