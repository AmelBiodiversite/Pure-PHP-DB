<?php
/**
 * ============================================================================
 * MARKETFLOW PRO - HEADERS DE SÉCURITÉ HTTP
 * ============================================================================
 * 
 * Ces headers protègent contre :
 * - Clickjacking (X-Frame-Options)
 * - MIME sniffing (X-Content-Type-Options)
 * - XSS (X-XSS-Protection, CSP)
 * - Fuite d'informations (Referrer-Policy)
 * - Injection de scripts (Content-Security-Policy)
 * 
 * ⚠️ IMPORTANT : À charger dans index.php APRÈS session_start()
 * ============================================================================
 */

/**
 * 🔒 X-FRAME-OPTIONS
 * Empêche l'affichage de ton site dans une iframe
 * 
 * Protection contre le "clickjacking" :
 * - Un pirate met ton site dans une iframe invisible
 * - Il superpose des boutons malveillants sur tes vrais boutons
 * - La victime clique sans savoir qu'elle interagit avec ton site
 * 
 * Options :
 * - DENY : Aucune iframe autorisée (recommandé)
 * - SAMEORIGIN : Iframe uniquement depuis ton propre domaine
 * - ALLOW-FROM uri : Iframe depuis une URL spécifique (obsolète)
 */
header('X-Frame-Options: DENY');

/**
 * 🔒 X-CONTENT-TYPE-OPTIONS
 * Force le navigateur à respecter le Content-Type déclaré
 * 
 * Protection contre le "MIME sniffing" :
 * - Tu envoies un fichier image.jpg
 * - Un pirate injecte du JavaScript dans l'image
 * - Sans ce header, le navigateur peut l'exécuter comme du JS
 * - Avec ce header, le navigateur respecte le type "image/jpeg"
 */
header('X-Content-Type-Options: nosniff');

/**
 * 🔒 X-XSS-PROTECTION
 * Active la protection XSS du navigateur (legacy)
 * 
 * Note : Ce header est obsolète dans les navigateurs modernes
 * (remplacé par CSP), mais on le garde pour les vieux navigateurs
 * 
 * Options :
 * - 0 : Désactiver
 * - 1 : Activer
 * - 1; mode=block : Activer et bloquer la page entière si XSS détecté
 */
header('X-XSS-Protection: 1; mode=block');

/**
 * 🔒 REFERRER-POLICY
 * Contrôle les informations envoyées dans le header Referer
 * 
 * Protection de la vie privée :
 * - Un utilisateur est sur /checkout/payment?token=secret123
 * - Il clique sur un lien externe
 * - Sans Referrer-Policy, l'URL complète est envoyée au site externe
 * - Avec strict-origin-when-cross-origin, seul le domaine est envoyé
 * 
 * Options recommandées :
 * - strict-origin-when-cross-origin : Domaine uniquement en HTTPS cross-origin
 * - no-referrer : Aucune information (peut casser les analytics)
 * - same-origin : Referer uniquement sur ton propre site
 */
header('Referrer-Policy: strict-origin-when-cross-origin');

/**
 * 🔒 CONTENT-SECURITY-POLICY (CSP)
 * Définit les sources autorisées pour chaque type de contenu
 * 
 * PROTECTION LA PLUS PUISSANTE contre XSS et injection de code
 * 
 * Explication de la policy ci-dessous :
 * 
 * default-src 'self'
 * → Par défaut, tout doit venir de ton propre domaine
 * 
 * script-src 'self' 'unsafe-inline' https://js.stripe.com
 * → JavaScript autorisé depuis :
 *   - Ton domaine ('self')
 *   - Inline scripts dans les balises <script> ('unsafe-inline')
 *   - Stripe pour le paiement
 * 
 * style-src 'self' 'unsafe-inline'
 * → CSS autorisé depuis :
 *   - Ton domaine ('self')
 *   - Styles inline dans <style> et style="" ('unsafe-inline')
 * 
 * img-src 'self' data: https:
 * → Images autorisées depuis :
 *   - Ton domaine
 *   - Data URIs (base64)
 *   - N'importe quel site HTTPS (pour les images de produits externes)
 * 
 * font-src 'self' data:
 * → Polices autorisées depuis :
 *   - Ton domaine
 *   - Data URIs (polices embarquées)
 * 
 * connect-src 'self' https://api.stripe.com
 * → Requêtes AJAX/fetch autorisées vers :
 *   - Ton domaine (pour les API internes)
 *   - Stripe (pour le paiement)
 * 
 * frame-src https://js.stripe.com
 * → Iframes autorisées uniquement depuis Stripe (pour le paiement)
 * 
 * ⚠️ IMPORTANT : Si tu ajoutes d'autres services (Google Analytics, CDN, etc.),
 * tu dois les ajouter ici !
 */
header("Content-Security-Policy: " .
    "default-src 'self'; " .
    "script-src 'self' 'unsafe-inline' https://js.stripe.com https://cdn.jsdelivr.net; " . // Ajout du ; manquant
    "style-src 'self' 'unsafe-inline'; " .
    "img-src 'self' data: https:; " .
    "font-src 'self' data:; " .
    "connect-src 'self' https://api.stripe.com https://cdn.jsdelivr.net; " . ; " .
    "frame-src https://js.stripe.com; " .
    "object-src 'none'; " .
    "base-uri 'self'; " .
    "form-action 'self';"
);

/**
 * 🔒 PERMISSIONS-POLICY (anciennement Feature-Policy)
 * Contrôle l'accès aux APIs du navigateur
 * 
 * Désactive les APIs non nécessaires pour réduire la surface d'attaque
 * 
 * APIs désactivées :
 * - geolocation : Localisation GPS
 * - microphone : Accès au micro
 * - camera : Accès à la caméra
 * - payment : API de paiement navigateur (on utilise Stripe)
 * - usb : Accès USB
 * - interest-cohort : FLoC de Google (tracking)
 */
header("Permissions-Policy: " .
    "geolocation=(), " .
    "microphone=(), " .
    "camera=(), " .
    "payment=(), " .
    "usb=(), " .
    "interest-cohort=()"
);

/**
 * 🔒 STRICT-TRANSPORT-SECURITY (HSTS)
 * Force l'utilisation de HTTPS pendant 1 an
 * 
 * ⚠️ ATTENTION : Activé UNIQUEMENT en production (HTTPS disponible)
 * 
 * max-age=31536000 : Durée de validité (1 an)
 * includeSubDomains : Applique aussi aux sous-domaines
 * preload : Permet l'ajout à la liste HSTS des navigateurs
 * 
 * Une fois activé, le navigateur refusera TOUTE connexion HTTP
 * pendant 1 an, même si l'utilisateur tape http://...
 */
$isProduction = ($_SERVER['SERVER_NAME'] ?? '') !== 'localhost' 
             && ($_SERVER['SERVER_NAME'] ?? '') !== '127.0.0.1';

if ($isProduction && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}

/**
 * ✅ LOG DE CHARGEMENT (debug uniquement)
 */
if (!$isProduction && PHP_SAPI === 'cli-server') {
    error_log('[SECURITY HEADERS] Headers de sécurité chargés');
}
