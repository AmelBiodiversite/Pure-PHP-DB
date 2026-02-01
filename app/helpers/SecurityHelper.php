<?php
/**
 * Helper pour les statistiques de sécurité
 */

if (!function_exists('getSecurityStats')) {
    /**
     * Récupère les statistiques de sécurité
     * @param int $days Nombre de jours à analyser
     * @return array Statistiques par type d'événement
     */
    function getSecurityStats($days = 7) {
        $stats = [
            'LOGIN_BLOCKED' => 0,
            'CSRF_VIOLATION' => 0,
            'XSS_ATTEMPT' => 0,
            'SQLI_ATTEMPT' => 0,
            'UNAUTHORIZED_ACCESS' => 0
        ];
        
        // Chemin vers les logs de sécurité
        $logPath = __DIR__ . '/../../data/logs/security.log';
        
        if (!file_exists($logPath)) {
            return $stats;
        }
        
        $cutoffDate = date('Y-m-d', strtotime("-$days days"));
        $logContent = file_get_contents($logPath);
        $lines = explode("\n", $logContent);
        
        foreach ($lines as $line) {
            if (empty($line)) continue;
            
            // Format attendu: [2026-02-01 12:00:00] TYPE: message
            if (preg_match('/\[(\d{4}-\d{2}-\d{2}).*?\]\s*(\w+):/', $line, $matches)) {
                $logDate = $matches[1];
                $eventType = $matches[2];
                
                if ($logDate >= $cutoffDate && isset($stats[$eventType])) {
                    $stats[$eventType]++;
                }
            }
        }
        
        return $stats;
    }
}
