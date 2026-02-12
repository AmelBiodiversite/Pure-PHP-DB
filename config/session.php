<?php
/**
 * ============================================================================
 * MARKETFLOW PRO - CONFIGURATION SESSIONS SÉCURISÉES
 * ============================================================================
 * 
 * Ce fichier configure les sessions PHP avec des paramètres de sécurité
 * renforcés pour protéger contre :
 * - Session hijacking (vol de session)
 * - Session fixation (forçage d'ID)
 * - XSS attacks (accès JavaScript aux cookies)
 * - CSRF attacks (requêtes cross-site)
 * 
 * ⚠️ IMPORTANT : À charger AVANT session_start() dans index.php
 * ============================================================================
 */

/**
 * Détecter si on est en environnement de production
 * En production : HTTPS requis pour les cookies
 */
$isProduction = ($_SERVER['SERVER_NAME'] ?? '') !== 'localhost' 
             && ($_SERVER['SERVER_NAME'] ?? '') !== '127.0.0.1';

/**
 * 🔒 HTTPONLY
 * Empêche JavaScript d'accéder au cookie de session
 * Protection contre les attaques XSS qui tentent de voler le session ID
 */
ini_set('session.cookie_httponly', '1');

/**
 * 🔒 SECURE (uniquement en production)
 * Force l'envoi du cookie uniquement via HTTPS
 * En développement local (HTTP), on désactive pour pouvoir tester
 */
if ($isProduction) {
    ini_set('session.cookie_secure', '1');
}

/**
 * 🔒 SAMESITE
 * Empêche l'envoi du cookie dans les requêtes cross-site
 * Protection supplémentaire contre CSRF
 * 
 * Options :
 * - Strict : Cookie jamais envoyé depuis un autre site (recommandé)
 * - Lax : Cookie envoyé pour navigation GET uniquement
 * - None : Aucune restriction (déconseillé)
 */
ini_set('session.cookie_samesite', 'Strict');

/**
 * 🔒 USE_STRICT_MODE
 * Refuse les IDs de session non initialisés par le serveur
 * Protection contre session fixation
 * 
 * Exemple d'attaque sans strict mode :
 * 1. Pirate envoie un lien avec ?PHPSESSID=abc123
 * 2. Victime clique et se connecte avec cet ID
 * 3. Pirate réutilise abc123 pour se connecter en tant que victime
 */
ini_set('session.use_strict_mode', '1');

/**
 * 🔒 USE_ONLY_COOKIES
 * Force l'utilisation exclusive des cookies (pas d'ID dans l'URL)
 * Empêche la transmission d'ID de session via GET (?PHPSESSID=...)
 */
ini_set('session.use_only_cookies', '1');

/**
 * ⏱️ DURÉE DE VIE DU COOKIE
 * 0 = Cookie de session (supprimé à la fermeture du navigateur)
 * Si "Remember me" est coché, on prolonge à 30 jours dans AuthController
 */
ini_set('session.cookie_lifetime', '0');

/**
 * ⏱️ GARBAGE COLLECTION
 * Nettoyage automatique des sessions expirées
 * 
 * gc_maxlifetime : Durée de vie maximale d'une session (24h = 86400s)
 * gc_probability / gc_divisor : Probabilité de lancement du GC (1%)
 */
ini_set('session.gc_maxlifetime', '86400'); // 24 heures
ini_set('session.gc_probability', '1');
ini_set('session.gc_divisor', '100');

/**
 * 📝 NOM DU COOKIE
 * Personnaliser le nom pour éviter les conflits avec d'autres apps
 * Par défaut : PHPSESSID (trop générique)
 */
ini_set('session.name', 'MARKETFLOW_SESSION');

/**
 * 🌐 PATH ET DOMAIN
 * Path : Chemin où le cookie est valide (/ = tout le site)
 * Domain : Domaine où le cookie est valide (vide = domaine actuel uniquement)
 */
ini_set('session.cookie_path', '/');
// ini_set('session.cookie_domain', ''); // Par défaut : domaine actuel

/**
 * 💾 STOCKAGE DES SESSIONS
 * ⚠️ CRITIQUE : Définir explicitement le chemin de stockage
 * 
 * Stratégie multi-environnement :
 * 1. Essayer /tmp/php-sessions (Railway/prod)
 * 2. Sinon créer et utiliser /tmp/marketflow-sessions (local)
 */
$sessionPath = '/tmp/php-sessions';

// Si le répertoire n'existe pas, utiliser un chemin alternatif
if (!is_dir($sessionPath)) {
    $sessionPath = '/tmp/marketflow-sessions';
    
    // Créer le répertoire s'il n'existe pas
    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0700, true); // 0700 = lecture/écriture proprio uniquement
    }
}

// Vérifier que le répertoire est accessible en écriture
if (!is_writable($sessionPath)) {
    // Tenter de corriger les permissions
    @chmod($sessionPath, 0700);
}

// Définir le chemin de sauvegarde
ini_set('session.save_path', $sessionPath);

/**
 * ✅ LOG DE DÉMARRAGE (pour debug)
 * En développement uniquement
 */
if (!$isProduction) {
    error_log('[SESSION CONFIG] Configuration sécurisée chargée');
    error_log('[SESSION CONFIG] Save path: ' . $sessionPath);
}
