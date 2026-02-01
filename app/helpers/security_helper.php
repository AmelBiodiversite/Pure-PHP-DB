<?php

use Core\SecurityLogger;

/**
 * Récupérer le nombre d'alertes de sécurité critiques
 * Pour afficher le badge dans le menu admin
 */
function getSecurityAlerts() {
    try {
        $logger = new SecurityLogger();
        $stats = $logger->getStats();
        
        // Compter les événements critiques des dernières 24h
        $critical = ($stats['CSRF_VIOLATION'] ?? 0) + 
                    ($stats['XSS_ATTEMPT'] ?? 0) + 
                    ($stats['SQLI_ATTEMPT'] ?? 0);
        
        return $critical;
    } catch (Exception $e) {
        return 0;
    }
}
