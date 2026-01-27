<?php
namespace Core;

/**
 * Protection CSRF (Cross-Site Request Forgery)
 * Empêche les attaques où un site malveillant fait des requêtes à votre nom
 */
class CSRF {
    
    /**
     * Génère un token CSRF unique pour cette session
     * Ce token sera vérifié lors des soumissions de formulaires
     */
    public static function generateToken() {
        if (!isset($_SESSION['csrf_token'])) {
            // Créer un token aléatoire de 64 caractères
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Vérifie qu'un token CSRF est valide
     * Utilise hash_equals() pour éviter les attaques par timing
     */
    public static function validateToken($token) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        // hash_equals() compare en temps constant (sécurité)
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Génère un champ hidden HTML avec le token
     * À utiliser dans tous les formulaires
     */
    public static function field() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}
