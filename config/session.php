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
 * 🛡️ RÉGÉNÉRATION PÉRIODIQUE DE L'ID
 * Régénérer l'ID de session toutes les 15 minutes
 * Protection supplémentaire contre session hijacking
 * 
 * Note : La régénération complète est déjà faite dans AuthController
 * lors de la connexion (session_regenerate_id(true))
 */
if (isset($_SESSION['LAST_REGENERATION'])) {
    // Si plus de 15 minutes depuis la dernière régénération
    if (time() - $_SESSION['LAST_REGENERATION'] > 900) {
        session_regenerate_id(true);
        $_SESSION['LAST_REGENERATION'] = time();
    }
} else {
    $_SESSION['LAST_REGENERATION'] = time();
}

/**
 * 🌐 PATH ET DOMAIN
 * Path : Chemin où le cookie est valide (/ = tout le site)
 * Domain : Domaine où le cookie est valide (vide = domaine actuel uniquement)
 */
ini_set('session.cookie_path', '/');
// ini_set('session.cookie_domain', ''); // Par défaut : domaine actuel

/**
 * 💾 STOCKAGE DES SESSIONS
 * Par défaut : fichiers (/tmp ou /var/lib/php/sessions)
 * Pour un site à fort trafic, considérer Redis ou Memcached
 */
// ini_set('session.save_handler', 'files'); // Par défaut
// ini_set('session.save_path', '/path/to/sessions'); // Optionnel

/**
 * ✅ LOG DE DÉMARRAGE (pour debug)
 * En développement uniquement
 */
if (!$isProduction && PHP_SAPI === 'cli-server') {
    error_log('[SESSION CONFIG] Configuration sécurisée chargée');
}
