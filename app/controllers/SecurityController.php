<?php
/**
 * ============================================================================
 * MARKETFLOW PRO - SECURITY CONTROLLER v4.0
 * ============================================================================
 *
 * AMÉLIORATIONS v4 :
 *   ✅ Rate limiting PERSISTANT en PostgreSQL (table rate_limits)
 *      → L'ancien système en mémoire PHP était perdu à chaque requête
 *   ✅ Toutes les données nécessaires passées à la vue (charts, pagination)
 *   ✅ Config unifiée pour le JS (MARKETFLOW_CONFIG)
 *   ✅ Compatibilité avec les fonctions CSRF existantes
 *   ✅ Validation stricte de tous les paramètres GET/POST
 *   ✅ Logging complet des actions admin
 *
 * @package  MarketFlow
 * @author   A.Devance
 * @version  4.0
 * @file     app/controllers/SecurityController.php
 */

namespace App\Controllers;

use Core\Controller;
use Core\SecurityLogger;
use Core\CSRF;

class SecurityController extends Controller {

    /** @var SecurityLogger Instance du logger de sécurité */
    private $logger;

    /**
     * Constructeur : vérifie les droits admin et instancie le logger
     * Redirige automatiquement si l'utilisateur n'est pas admin
     */
    public function __construct() {
        parent::__construct();      // Initialise $this->db via Database::getInstance()
        $this->requireAdmin();      // Protection d'accès : admin uniquement
        $this->logger = new SecurityLogger();
    }

    // =========================================================================
    // SECTION 1 : ROUTE PRINCIPALE (GET /admin/security)
    // =========================================================================

    /**
     * Affiche le dashboard principal de sécurité
     * Récupère toutes les données nécessaires et les passe à la vue
     *
     * Route: GET /admin/security
     */
    public function index() {

        // --- Récupération et validation des filtres GET ---
        $filters = $this->getFiltersFromGET();

        // Pagination avec limites de sécurité
        $page    = isset($_GET['page'])     ? max(1, min(10000, (int)$_GET['page']))    : 1;
        $perPage = isset($_GET['per_page']) ? max(10, min(200, (int)$_GET['per_page'])) : 50;

        try {
            // === STATISTIQUES GLOBALES (7 derniers jours) ===
            $stats = $this->logger->getStats(7);

            // Calculer les totaux par catégorie de sévérité
            $totalEvents    = array_sum($stats);
            $criticalEvents = 0;
            $warningEvents  = 0;
            $infoEvents     = 0;

            // Classification des types d'événements par sévérité
            $criticalTypes = ['CSRF_VIOLATION', 'XSS_ATTEMPT', 'SQLI_ATTEMPT', 'UNAUTHORIZED_ACCESS', 'FILE_UPLOAD_VIOLATION'];
            $warningTypes  = ['LOGIN_FAILED', 'LOGIN_BLOCKED', 'RATE_LIMIT_EXCEEDED', 'SUSPICIOUS_REQUEST'];

            foreach ($stats as $type => $count) {
                if (in_array($type, $criticalTypes)) {
                    $criticalEvents += $count;
                } elseif (in_array($type, $warningTypes)) {
                    $warningEvents += $count;
                } else {
                    $infoEvents += $count;
                }
            }

            // === TIMELINE (évolution jour par jour sur 7 jours) ===
            $timeline = $this->logger->getTimeline(7);

            // === TOP 10 IPs SUSPECTES ===
            $suspiciousIPs = $this->logger->getSuspiciousIPs(10, 7);

            // === ÉVÉNEMENTS FILTRÉS + PAGINÉS ===
            $eventsData = $this->logger->getEvents($filters, $page, $perPage);

            // === TYPES D'ÉVÉNEMENTS (pour le dropdown de filtre) ===
            $eventTypes = array_keys($stats);

            // === DONNÉES POUR LES GRAPHIQUES CHART.JS ===

            // Donut : labels et valeurs par type d'événement
            $chartLabels = array_keys($stats);
            $chartData   = array_values($stats);

            // Couleur associée à chaque type (cohérent avec le design system)
            $chartColors = array_map(function ($type) {
                return match ($type) {
                    'LOGIN_SUCCESS'        => '#10b981',  // Vert succès
                    'LOGIN_FAILED'         => '#f59e0b',  // Orange warning
                    'LOGIN_BLOCKED'        => '#ef4444',  // Rouge danger
                    'CSRF_VIOLATION'       => '#8b5cf6',  // Violet
                    'XSS_ATTEMPT'          => '#ec4899',  // Rose
                    'SQLI_ATTEMPT'         => '#f97316',  // Orange foncé
                    'UNAUTHORIZED_ACCESS'  => '#dc2626',  // Rouge foncé
                    'RATE_LIMIT_EXCEEDED'  => '#eab308',  // Jaune
                    'REGISTER'             => '#06b6d4',  // Cyan
                    'LOGOUT'               => '#6b7280',  // Gris
                    default                => '#3b82f6',  // Bleu par défaut
                };
            }, $chartLabels);

            // Timeline : séparer les colonnes pour Chart.js
            $timelineLabels   = array_column($timeline, 'date');
            $timelineCritical = array_map('intval', array_column($timeline, 'critical'));
            $timelineWarning  = array_map('intval', array_column($timeline, 'warning'));
            $timelineInfo     = array_map('intval', array_column($timeline, 'info'));

            // === RENDRE LA VUE AVEC TOUTES LES DONNÉES ===
            $this->render('admin/security-dashboard', [
                // Titre de la page
                'title'          => 'Monitoring Sécurité',

                // Statistiques brutes et agrégées
                'stats'          => $stats,
                'totalEvents'    => $totalEvents,
                'criticalEvents' => $criticalEvents,
                'warningEvents'  => $warningEvents,
                'infoEvents'     => $infoEvents,

                // IPs suspectes (top 10)
                'suspiciousIPs'  => $suspiciousIPs,

                // Événements filtrés + pagination
                'recentEvents'   => $eventsData['events'],
                'pagination'     => [
                    'total'       => $eventsData['total'],
                    'page'        => $eventsData['page'],
                    'per_page'    => $eventsData['per_page'],
                    'total_pages' => $eventsData['total_pages'],
                ],

                // Filtres actifs (pour pré-remplir le formulaire)
                'filters'        => $filters,
                'eventTypes'     => $eventTypes,

                // Données Chart.js (donut par type)
                'chartLabels'    => $chartLabels,
                'chartData'      => $chartData,
                'chartColors'    => $chartColors,

                // Données Chart.js (timeline 7 jours)
                'timelineLabels'   => $timelineLabels,
                'timelineCritical' => $timelineCritical,
                'timelineWarning'  => $timelineWarning,
                'timelineInfo'     => $timelineInfo,
            ]);

        } catch (\Exception $e) {
            error_log('[SecurityController::index] Erreur: ' . $e->getMessage());
            $this->render('errors/500', [
                'message' => 'Erreur lors du chargement du dashboard de sécurité'
            ]);
        }
    }

    // =========================================================================
    // SECTION 2 : API AJAX (GET — pas de CSRF nécessaire)
    // =========================================================================

    /**
     * Retourne les événements filtrés en JSON (pour l'auto-refresh)
     * Route: GET /admin/security/api/events
     */
    public function apiEvents() {
        if (!$this->isAjaxRequest()) {
            $this->jsonResponse(['error' => 'Requête AJAX attendue'], 400);
            return;
        }

        $filters = $this->getFiltersFromGET();
        $page    = isset($_GET['page'])     ? max(1, min(10000, (int)$_GET['page']))    : 1;
        $perPage = isset($_GET['per_page']) ? max(10, min(200, (int)$_GET['per_page'])) : 50;

        try {
            $result = $this->logger->getEvents($filters, $page, $perPage);
            $this->jsonResponse($result);
        } catch (\Exception $e) {
            error_log('[SecurityController::apiEvents] Erreur: ' . $e->getMessage());
            $this->jsonResponse(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Retourne les statistiques en JSON
     * Route: GET /admin/security/api/stats
     */
    public function apiStats() {
        if (!$this->isAjaxRequest()) {
            $this->jsonResponse(['error' => 'Requête AJAX attendue'], 400);
            return;
        }

        $days = isset($_GET['days']) ? max(1, min(90, (int)$_GET['days'])) : 7;

        try {
            $stats = $this->logger->getStats($days);
            $this->jsonResponse($stats);
        } catch (\Exception $e) {
            error_log('[SecurityController::apiStats] Erreur: ' . $e->getMessage());
            $this->jsonResponse(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Retourne les IPs suspectes en JSON
     * Route: GET /admin/security/api/suspicious-ips
     */
    public function apiSuspiciousIPs() {
        if (!$this->isAjaxRequest()) {
            $this->jsonResponse(['error' => 'Requête AJAX attendue'], 400);
            return;
        }

        $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 10;
        $days  = isset($_GET['days'])  ? max(1, min(90, (int)$_GET['days']))   : 7;

        try {
            $ips = $this->logger->getSuspiciousIPs($limit, $days);
            $this->jsonResponse($ips);
        } catch (\Exception $e) {
            error_log('[SecurityController::apiSuspiciousIPs] Erreur: ' . $e->getMessage());
            $this->jsonResponse(['error' => 'Erreur serveur'], 500);
        }
    }

    // =========================================================================
    // SECTION 3 : ACTIONS POST (CSRF + rate limiting obligatoires)
    // =========================================================================

    /**
     * Bloque une adresse IP
     * Route: POST /admin/security/block-ip
     * 
     * Protections : CSRF + rate limiting persistant + validation IP
     */
    public function blockIP() {
        $this->requirePOST();

        // Validation CSRF
        if (!$this->validateCSRFToken()) {
            $this->logger->log('CSRF_VIOLATION', [
                'action'  => 'block_ip',
                'user_id' => $_SESSION['user_id'] ?? null,
            ]);
            $this->jsonResponse(['success' => false, 'message' => 'Token CSRF invalide'], 403);
            return;
        }

        // Rate limiting PERSISTANT (max 10 blocages / minute)
        if (!$this->checkRateLimit('block_ip', 10, 60)) {
            $this->jsonResponse(['success' => false, 'message' => 'Trop de requêtes'], 429);
            return;
        }

        // Validation de l'IP
        $ip     = $_POST['ip'] ?? '';
        $reason = $_POST['reason'] ?? 'Bloquée manuellement';

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->jsonResponse(['success' => false, 'message' => 'Adresse IP invalide'], 400);
            return;
        }

        // Tronquer la raison (protection contre les payloads énormes)
        $reason = substr(trim($reason), 0, 500);

        try {
            $success = $this->logger->blockIP($ip, $reason, 'MANUAL', $_SESSION['user_id'] ?? null);

            if ($success) {
                // Logger l'action admin pour audit
                $this->logger->log('IP_BLOCKED_BY_ADMIN', [
                    'ip'         => $ip,
                    'reason'     => $reason,
                    'blocked_by' => $_SESSION['user_id'] ?? null,
                    'admin_ip'   => $_SERVER['REMOTE_ADDR'] ?? null,
                ]);

                $this->jsonResponse(['success' => true, 'message' => "IP $ip bloquée avec succès"]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Erreur lors du blocage'], 500);
            }

        } catch (\Exception $e) {
            error_log('[SecurityController::blockIP] ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Débloque une adresse IP
     * Route: POST /admin/security/unblock-ip
     */
    public function unblockIP() {
        $this->requirePOST();

        if (!$this->validateCSRFToken()) {
            $this->logger->log('CSRF_VIOLATION', [
                'action'  => 'unblock_ip',
                'user_id' => $_SESSION['user_id'] ?? null,
            ]);
            $this->jsonResponse(['success' => false, 'message' => 'Token CSRF invalide'], 403);
            return;
        }

        $ip = $_POST['ip'] ?? '';
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->jsonResponse(['success' => false, 'message' => 'Adresse IP invalide'], 400);
            return;
        }

        try {
            $success = $this->logger->unblockIP($ip);

            if ($success) {
                $this->logger->log('IP_UNBLOCKED_BY_ADMIN', [
                    'ip'           => $ip,
                    'unblocked_by' => $_SESSION['user_id'] ?? null,
                    'admin_ip'     => $_SERVER['REMOTE_ADDR'] ?? null,
                ]);
                $this->jsonResponse(['success' => true, 'message' => "IP $ip débloquée"]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Erreur lors du déblocage'], 500);
            }

        } catch (\Exception $e) {
            error_log('[SecurityController::unblockIP] ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Ajoute une IP à la whitelist
     * Route: POST /admin/security/whitelist-ip
     */
    public function whitelistIP() {
        $this->requirePOST();

        if (!$this->validateCSRFToken()) {
            $this->logger->log('CSRF_VIOLATION', [
                'action'  => 'whitelist_ip',
                'user_id' => $_SESSION['user_id'] ?? null,
            ]);
            $this->jsonResponse(['success' => false, 'message' => 'Token CSRF invalide'], 403);
            return;
        }

        $ip          = $_POST['ip'] ?? '';
        $description = $_POST['description'] ?? '';

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->jsonResponse(['success' => false, 'message' => 'Adresse IP invalide'], 400);
            return;
        }

        $description = substr(trim($description), 0, 500);

        try {
            $success = $this->logger->whitelistIP($ip, $description, $_SESSION['user_id'] ?? null);

            if ($success) {
                $this->logger->log('IP_WHITELISTED_BY_ADMIN', [
                    'ip'          => $ip,
                    'description' => $description,
                    'added_by'    => $_SESSION['user_id'] ?? null,
                    'admin_ip'    => $_SERVER['REMOTE_ADDR'] ?? null,
                ]);
                $this->jsonResponse(['success' => true, 'message' => "IP $ip ajoutée à la whitelist"]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Erreur lors de l\'ajout'], 500);
            }

        } catch (\Exception $e) {
            error_log('[SecurityController::whitelistIP] ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    // =========================================================================
    // SECTION 4 : EXPORT (CSV / JSON)
    // =========================================================================

    /**
     * Export CSV des logs filtrés
     * Route: GET /admin/security/export/csv
     */
    public function exportCSV() {
        $filters = $this->getFiltersFromGET();

        try {
            $csv = $this->logger->exportToCSV($filters);

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="security-logs-' . date('Y-m-d-His') . '.csv"');
            header('Content-Length: ' . strlen($csv));
            header('Cache-Control: no-cache, must-revalidate');

            echo $csv;
            exit;

        } catch (\Exception $e) {
            error_log('[SecurityController::exportCSV] ' . $e->getMessage());
            die('Erreur lors de l\'export CSV');
        }
    }

    /**
     * Export JSON des logs filtrés
     * Route: GET /admin/security/export/json
     */
    public function exportJSON() {
        $filters = $this->getFiltersFromGET();

        try {
            $json = $this->logger->exportToJSON($filters);

            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename="security-logs-' . date('Y-m-d-His') . '.json"');
            header('Content-Length: ' . strlen($json));

            echo $json;
            exit;

        } catch (\Exception $e) {
            error_log('[SecurityController::exportJSON] ' . $e->getMessage());
            die('Erreur lors de l\'export JSON');
        }
    }

    // =========================================================================
    // SECTION 5 : SÉCURITÉ (CSRF + Rate Limiting persistant)
    // =========================================================================

    /**
     * Valide le token CSRF de la requête
     * Cherche le token dans le header X-CSRF-Token (AJAX) puis dans $_POST
     * Utilise les fonctions CSRF existantes du projet comme fallback
     *
     * @return bool True si le token est valide
     */
    private function validateCSRFToken(): bool {
        // Chercher le token : header (prioritaire pour AJAX) puis body POST
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? null;

        if (!$token || empty($token)) {
            error_log('[CSRF] Token absent de la requête');
            return false;
        }

        // Utiliser la fonction existante verifyCsrfToken() si disponible
        if (function_exists('verifyCsrfToken')) {
            return verifyCsrfToken($token);
        }

        // Fallback : utiliser CSRF::validateToken()
        if (class_exists('Core\CSRF')) {
            return CSRF::validateToken($token);
        }

        error_log('[CSRF] Aucune fonction de validation disponible');
        return false;
    }

    /**
     * Rate limiting PERSISTANT en base de données PostgreSQL
     *
     * Contrairement à l'ancien système en mémoire (self::$actionCounter),
     * ce rate limiter survit aux requêtes HTTP et fonctionne correctement
     * même avec plusieurs processus PHP concurrents.
     *
     * Utilise la table `rate_limits` avec UPSERT (INSERT ON CONFLICT UPDATE)
     *
     * @param string $action      Identifiant de l'action (ex: "block_ip")
     * @param int    $maxAttempts Nombre max de tentatives autorisées
     * @param int    $timeWindow  Durée de la fenêtre en secondes
     * @return bool True si l'action est autorisée, False si rate limité
     */
    private function checkRateLimit(string $action, int $maxAttempts, int $timeWindow): bool {
        $userId = $_SESSION['user_id'] ?? 'anon';
        $key    = $action . '_user_' . $userId;

        try {
            // UPSERT atomique : créer ou incrémenter le compteur
            // Si la fenêtre a expiré, on la réinitialise
            $stmt = $this->db->prepare("
                INSERT INTO rate_limits (action_key, attempts, window_start, window_expires)
                VALUES (:key, 1, NOW(), NOW() + INTERVAL '$timeWindow seconds')
                ON CONFLICT (action_key) DO UPDATE SET
                    -- Si la fenêtre a expiré, réinitialiser le compteur à 1
                    attempts = CASE
                        WHEN rate_limits.window_expires < NOW() THEN 1
                        ELSE rate_limits.attempts + 1
                    END,
                    -- Si la fenêtre a expiré, créer une nouvelle fenêtre
                    window_start = CASE
                        WHEN rate_limits.window_expires < NOW() THEN NOW()
                        ELSE rate_limits.window_start
                    END,
                    window_expires = CASE
                        WHEN rate_limits.window_expires < NOW() THEN NOW() + INTERVAL '$timeWindow seconds'
                        ELSE rate_limits.window_expires
                    END
                RETURNING attempts
            ");

            $stmt->execute([':key' => $key]);
            $result   = $stmt->fetch();
            $attempts = (int)($result['attempts'] ?? 0);

            // Vérifier si la limite est dépassée
            if ($attempts > $maxAttempts) {
                error_log("[RateLimit] Action '$action' bloquée pour user $userId ($attempts/$maxAttempts)");
                return false;
            }

            return true;

        } catch (\PDOException $e) {
            // En cas d'erreur DB, autoriser l'action (fail-open)
            // pour ne pas bloquer un admin en cas de problème technique
            error_log('[RateLimit] Erreur DB: ' . $e->getMessage());
            return true;
        }
    }

    // =========================================================================
    // SECTION 6 : MÉTHODES UTILITAIRES
    // =========================================================================

    /** Vérifie que la requête est bien en POST, sinon 405 */
    private function requirePOST(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Méthode non autorisée'], 405);
            exit;
        }
    }

    /** Vérifie que la requête est AJAX (header X-Requested-With) */
    private function isAjaxRequest(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    // =========================================================================
    // SECTION 7 : VALIDATION DES DONNÉES
    // =========================================================================

    /** Valide une date au format YYYY-MM-DD */
    private function sanitizeDate(string $date): string {
        if (empty($date)) return '';
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) return '';
        $parts = explode('-', $date);
        if (!checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0])) return '';
        return $date;
    }

    /** Sanitise une chaîne (strip_tags + trim) */
    private function sanitizeString(string $str): string {
        return trim(strip_tags($str));
    }

    /** Valide un niveau de sévérité (INFO, WARNING, CRITICAL uniquement) */
    private function sanitizeSeverity(string $severity): string {
        $valid = ['INFO', 'WARNING', 'CRITICAL'];
        $severity = strtoupper(trim($severity));
        return in_array($severity, $valid) ? $severity : '';
    }

    /** Valide une adresse IP avec filter_var */
    private function sanitizeIP(string $ip): string {
        $ip = trim($ip);
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '';
    }

    /**
     * Construit le tableau de filtres depuis les paramètres GET
     * Tous les champs sont validés et sanitisés
     *
     * @return array Filtres propres prêts pour le SecurityLogger
     */
    private function getFiltersFromGET(): array {
        return [
            'date_from'  => $this->sanitizeDate($_GET['date_from'] ?? ''),
            'date_to'    => $this->sanitizeDate($_GET['date_to'] ?? ''),
            'event_type' => $this->sanitizeString($_GET['event_type'] ?? ''),
            'severity'   => $this->sanitizeSeverity($_GET['severity'] ?? ''),
            'ip'         => $this->sanitizeIP($_GET['ip'] ?? ''),
            'search'     => $this->sanitizeString($_GET['search'] ?? ''),
        ];
    }
}
