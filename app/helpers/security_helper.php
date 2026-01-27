<?php
/**
 * Helper pour récupérer les alertes de sécurité
 * Utilisé dans le header pour afficher le badge
 */

/**
 * Récupérer le nombre d'alertes critiques (24h)
 * @return int Nombre d'alertes
 */
function getSecurityAlerts() {
    try {
        // Vérifier que la classe existe
        if (!class_exists('\Core\SecurityLogger')) {
            return 0;
        }
        
        // Récupérer les stats des dernières 24h
        $stats = \Core\SecurityLogger::getStats(1);
        
        // Compter les événements critiques
        $critical = ($stats['LOGIN_BLOCKED'] ?? 0) + 
                    ($stats['CSRF_VIOLATION'] ?? 0) + 
                    ($stats['XSS_ATTEMPT'] ?? 0) + 
                    ($stats['SQLI_ATTEMPT'] ?? 0);
        
        return $critical;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Récupérer toutes les stats de sécurité
 * @param int $days Nombre de jours
 * @return array Stats par type d'événement
 */
function getSecurityStats($days = 7) {
    try {
        if (!class_exists('\Core\SecurityLogger')) {
            return [];
        }
        return \Core\SecurityLogger::getStats($days);
    } catch (Exception $e) {
        return [];
    }
}
