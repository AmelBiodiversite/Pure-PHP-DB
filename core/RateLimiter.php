<?php
/**
 * ============================================================================
 * MARKETFLOW PRO - RATE LIMITER (LIMITATION DE DÉBIT)
 * ============================================================================
 * 
 * Protège contre :
 * - Attaques par force brute (tentatives de connexion)
 * - Spam de formulaires
 * - Abus d'API
 * 
 * Fonctionnement :
 * - Compte les tentatives par IP + identifiant (email, user_id, etc.)
 * - Bloque temporairement après X tentatives
 * - Réinitialise automatiquement après Y minutes
 * 
 * Fichier : core/RateLimiter.php
 * ============================================================================
 */

namespace Core;

class RateLimiter {
    
    /**
     * Vérifier et incrémenter le compteur de tentatives
     * 
     * @param string $action Type d'action (ex: 'login', 'register', 'api_call')
     * @param string $identifier Identifiant unique (email, IP, user_id)
     * @param int $maxAttempts Nombre max de tentatives autorisées
     * @param int $decayMinutes Durée du blocage en minutes
     * @return bool True si autorisé, False si bloqué
     */
    public static function attempt($action, $identifier, $maxAttempts = 5, $decayMinutes = 15) {
        // Générer une clé unique pour cette action + identifiant
        $key = self::generateKey($action, $identifier);
        
        // Vérifier si déjà bloqué
        if (self::isBlocked($key)) {
            return false;
        }
        
        // Récupérer les tentatives actuelles
        $attempts = self::getAttempts($key);
        
        // Si trop de tentatives, bloquer
        if ($attempts >= $maxAttempts) {
            self::block($key, $decayMinutes);
            return false;
        }
        
        // Incrémenter le compteur
        self::incrementAttempts($key, $decayMinutes);
        
        return true;
    }
    
    /**
     * Vérifier seulement (sans incrémenter)
     * Utile pour afficher un message avant le formulaire
     */
    public static function check($action, $identifier, $maxAttempts = 5) {
        $key = self::generateKey($action, $identifier);
        
        if (self::isBlocked($key)) {
            return false;
        }
        
        $attempts = self::getAttempts($key);
        return $attempts < $maxAttempts;
    }
    
    /**
     * Réinitialiser le compteur (après connexion réussie)
     */
    public static function clear($action, $identifier) {
        $key = self::generateKey($action, $identifier);
        unset($_SESSION[$key]);
        unset($_SESSION[$key . '_blocked_until']);
    }
    
    /**
     * Obtenir le nombre de tentatives restantes
     */
    public static function remaining($action, $identifier, $maxAttempts = 5) {
        $key = self::generateKey($action, $identifier);
        $attempts = self::getAttempts($key);
        return max(0, $maxAttempts - $attempts);
    }
    
    /**
     * Obtenir le temps restant de blocage (en secondes)
     */
    public static function blockedFor($action, $identifier) {
        $key = self::generateKey($action, $identifier);
        $blockedUntil = $_SESSION[$key . '_blocked_until'] ?? 0;
        
        if ($blockedUntil > time()) {
            return $blockedUntil - time();
        }
        
        return 0;
    }
    
    /**
     * ========================================================================
     * MÉTHODES PRIVÉES
     * ========================================================================
     */
    
    /**
     * Générer une clé unique pour l'action + identifiant
     */
    private static function generateKey($action, $identifier) {
        // Utiliser l'IP + identifiant pour éviter les contournements
        $ip = self::getClientIp();
        return 'rate_limit_' . md5($action . '_' . $identifier . '_' . $ip);
    }
    
    /**
     * Vérifier si l'utilisateur est bloqué
     */
    private static function isBlocked($key) {
        $blockedUntil = $_SESSION[$key . '_blocked_until'] ?? 0;
        
        // Si le délai de blocage est dépassé, débloquer
        if ($blockedUntil > 0 && $blockedUntil < time()) {
            unset($_SESSION[$key]);
            unset($_SESSION[$key . '_blocked_until']);
            return false;
        }
        
        return $blockedUntil > time();
    }
    
    /**
     * Bloquer temporairement
     */
    private static function block($key, $minutes) {
        $_SESSION[$key . '_blocked_until'] = time() + ($minutes * 60);
    }
    
    /**
     * Obtenir le nombre de tentatives
     */
    private static function getAttempts($key) {
        return $_SESSION[$key] ?? 0;
    }
    
    /**
     * Incrémenter le compteur de tentatives
     */
    private static function incrementAttempts($key, $decayMinutes) {
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = 0;
            $_SESSION[$key . '_first_attempt'] = time();
        }
        
        // Si la première tentative date de plus de $decayMinutes, réinitialiser
        $firstAttempt = $_SESSION[$key . '_first_attempt'] ?? time();
        if (time() - $firstAttempt > ($decayMinutes * 60)) {
            $_SESSION[$key] = 0;
            $_SESSION[$key . '_first_attempt'] = time();
        }
        
        $_SESSION[$key]++;
    }
    
    /**
     * Obtenir l'IP réelle du client (même derrière un proxy)
     */
    private static function getClientIp() {
        // Vérifier les headers de proxy
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',  // Proxy standard
            'HTTP_X_REAL_IP',        // Nginx
            'REMOTE_ADDR'            // IP directe
        ];
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                // Si plusieurs IPs (proxy chain), prendre la première
                $ip = explode(',', $_SERVER[$header])[0];
                return trim($ip);
            }
        }
        
        return '0.0.0.0';
    }
    
    /**
     * Formater le temps restant en texte lisible
     */
    public static function formatBlockedTime($seconds) {
        if ($seconds < 60) {
            return $seconds . ' seconde' . ($seconds > 1 ? 's' : '');
        }
        
        $minutes = ceil($seconds / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    }
}
