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

/**
 * Récupérer les statistiques de sécurité détaillées
 * @param int $days Nombre de jours à analyser
 * @return array Statistiques par type d'événement
 */
function getSecurityStats($days = 7) {
    try {
        $logger = new SecurityLogger();
        return $logger->getStats($days);
    } catch (Exception $e) {
        return [
            'LOGIN_BLOCKED' => 0,
            'CSRF_VIOLATION' => 0,
            'XSS_ATTEMPT' => 0,
            'SQLI_ATTEMPT' => 0,
            'UNAUTHORIZED_ACCESS' => 0
        ];
    }
}
