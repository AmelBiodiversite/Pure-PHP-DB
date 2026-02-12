<?php
/**
 * ============================================================================
 * MARKETFLOW PRO - SECURITY CONTROLLER v3.1 FINAL
 * ============================================================================
 *
 * 🔐 VERSION FINALE - Utilise les fonctions CSRF existantes du projet
 *
 * CORRECTIONS v3.1 :
 *   ✅ Utilise verifyCsrfToken() de functions.php (au lieu de créer une nouvelle)
 *   ✅ Utilise CSRF::validateToken() comme fallback
 *   ✅ Compatible avec l'architecture existante
 *   ✅ Rate limiting sur actions sensibles
 *   ✅ Logging complet de toutes les actions admin
 *   ✅ Validation stricte des paramètres
 *   ✅ Gestion d'erreurs robuste
 *
 * Contrôleur pour le dashboard de monitoring de sécurité admin.
 *
 * @package  MarketFlow
 * @author   MarketFlow Security Team
 * @version  3.1
 * @file     app/controllers/SecurityController.php
 */

namespace App\Controllers;

use Core\Controller;
use Core\SecurityLogger;
use Core\CSRF;

class SecurityController extends Controller {

    /**
     * Instance du SecurityLogger v2
     * @var SecurityLogger
     */
    private $logger;

    /**
     * Compteur d'actions pour rate limiting simple
     * @var array
     */
    private static $actionCounter = [];

    /**
     * Constructeur : vérifie les droits admin et instancie le logger
     */
    public function __construct() {
        parent::__construct();    // Initialise $this->db via Database::getInstance()
        $this->requireAdmin();    // Redirige vers / si l'utilisateur n'est pas admin
        $this->logger = new SecurityLogger();
    }

    // =========================================================================
    // SECTION 1 : ROUTE PRINCIPALE
    // =========================================================================

    /**
     * Dashboard principal de sécurité
     * Route: GET /admin/security
     */
    public function index() {

        // --- Récupération des filtres depuis l'URL (avec validation) ---
        $filters = [
            'date_from'  => $this->sanitizeDate($_GET['date_from']  ?? date('Y-m-d', strtotime('-7 days'))),
            'date_to'    => $this->sanitizeDate($_GET['date_to']    ?? date('Y-m-d')),
            'event_type' => $this->sanitizeString($_GET['event_type'] ?? ''),
            'severity'   => $this->sanitizeSeverity($_GET['severity'] ?? ''),
            'ip'         => $this->sanitizeIP($_GET['ip'] ?? ''),
            'search'     => $this->sanitizeString($_GET['search'] ?? ''),
        ];

        // Numéro de page et nombre d'items par page (avec limites)
        $page    = isset($_GET['page'])     ? max(1, min(10000, (int)$_GET['page']))    : 1;
        $perPage = isset($_GET['per_page']) ? max(10, min(200, (int)$_GET['per_page'])) : 50;

        try {
            // --- Statistiques globales (7 derniers jours) ---
            $stats = $this->logger->getStats(7);

            // Calculer les totaux par sévérité
            $totalEvents    = array_sum($stats);
            $criticalEvents = 0;
            $warningEvents  = 0;
            $infoEvents     = 0;

            foreach ($stats as $type => $count) {
                if (in_array($type, ['CSRF_VIOLATION', 'XSS_ATTEMPT', 'SQLI_ATTEMPT', 'UNAUTHORIZED_ACCESS'])) {
                    $criticalEvents += $count;
                } elseif (in_array($type, ['LOGIN_FAILED', 'LOGIN_BLOCKED', 'RATE_LIMIT_EXCEEDED'])) {
                    $warningEvents += $count;
                } else {
                    $infoEvents += $count;
                }
            }

            // --- Timeline (évolution jour par jour sur 7 jours) ---
            $timeline = $this->logger->getTimeline(7);

            // --- Top 10 IPs suspectes ---
            $suspiciousIPs = $this->logger->getSuspiciousIPs(10, 7);

            // --- Événements filtrés + paginés ---
            $eventsData = $this->logger->getEvents($filters, $page, $perPage);

            // --- Types d'événements connus (pour le dropdown de filtre) ---
            $eventTypes = array_keys($stats);

            // --- Données pour les graphiques Chart.js ---
            $chartLabels = array_keys($stats);
            $chartData   = array_values($stats);

            // Couleur associée à chaque type d'événement
            $chartColors = array_map(function ($type) {
                return match ($type) {
                    'LOGIN_SUCCESS'       => '#2ecc71',
                    'LOGIN_FAILED'        => '#e74c3c',
                    'LOGIN_BLOCKED'       => '#c0392b',
                    'CSRF_VIOLATION'      => '#f39c12',
                    'XSS_ATTEMPT'         => '#9b59b6',
                    'SQLI_ATTEMPT'        => '#e67e22',
                    'UNAUTHORIZED_ACCESS' => '#e74c3c',
                    'RATE_LIMIT_EXCEEDED' => '#f1c40f',
                    default               => '#3498db',
                };
            }, $chartLabels);

            // Timeline : labels de dates et datasets par sévérité
            $timelineLabels   = array_column($timeline, 'date');
            $timelineCritical = array_column($timeline, 'critical');
            $timelineWarning  = array_column($timeline, 'warning');
            $timelineInfo     = array_column($timeline, 'info');

            // --- Rendre la vue ---
            $this->render('admin/security-dashboard', [
                'title'          => 'Monitoring Sécurité Pro',
                'stats'          => $stats,
                'totalEvents'    => $totalEvents,
                'criticalEvents' => $criticalEvents,
                'warningEvents'  => $warningEvents,
                'infoEvents'     => $infoEvents,
                'suspiciousIPs'  => $suspiciousIPs,
                'recentEvents'   => $eventsData['events'],
                'pagination'     => [
                    'total'       => $eventsData['total'],
                    'page'        => $eventsData['page'],
                    'per_page'    => $eventsData['per_page'],
                    'total_pages' => $eventsData['total_pages'],
                ],
                'filters'        => $filters,
                'eventTypes'     => $eventTypes,
                'chartLabels'    => $chartLabels,
                'chartData'      => $chartData,
                'chartColors'    => $chartColors,
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
    // SECTION 2 : API AJAX (GET - pas de CSRF nécessaire)
    // =========================================================================

    /**
     * API AJAX : retourne les événements filtrés en JSON
     * Route: GET /admin/security/api/events
     */
    public function apiEvents() {
        if (!$this->isAjaxRequest()) {
            $this->jsonResponse(['error' => 'Requête AJAX attendue'], 400);
            return;
        }

        $filters = [
            'date_from'  => $this->sanitizeDate($_GET['date_from'] ?? ''),
            'date_to'    => $this->sanitizeDate($_GET['date_to'] ?? ''),
            'event_type' => $this->sanitizeString($_GET['event_type'] ?? ''),
            'severity'   => $this->sanitizeSeverity($_GET['severity'] ?? ''),
            'ip'         => $this->sanitizeIP($_GET['ip'] ?? ''),
            'search'     => $this->sanitizeString($_GET['search'] ?? ''),
        ];

        $page    = isset($_GET['page'])     ? max(1, min(10000, (int)$_GET['page']))    : 1;
        $perPage = isset($_GET['per_page']) ? max(10, min(200, (int)$_GET['per_page'])) : 50;

        try {
            $result = $this->logger->getEvents($filters, $page, $perPage);
            $this->jsonResponse($result);
        } catch (\Exception $e) {
            error_log('[SecurityController::apiEvents] Erreur: ' . $e->getMessage());
            $this->jsonResponse(['error' => 'Erreur lors de la récupération des événements'], 500);
        }
    }

    /**
     * API AJAX : Retourne les statistiques en JSON
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
            $this->jsonResponse(['error' => 'Erreur lors de la récupération des statistiques'], 500);
        }
    }

    /**
     * API AJAX : Retourne les IPs suspectes en JSON
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
            $this->jsonResponse(['error' => 'Erreur lors de la récupération des IPs suspectes'], 500);
        }
    }

    // =========================================================================
    // SECTION 3 : ACTIONS POST (avec validation CSRF obligatoire)
    // =========================================================================

    /**
     * API AJAX : Bloquer une IP
     * Route: POST /admin/security/block-ip
     * 
     * ✅ PROTECTION CSRF : Utilise verifyCsrfToken() de functions.php
     * ✅ RATE LIMITING : Max 10 blocages/minute par admin
     * ✅ LOGGING : Toutes les actions sont loguées
     */
    public function blockIP() {
        // 1. Vérifier que c'est bien du POST
        $this->requirePOST();

        // 2. ✅ VALIDATION CSRF (utilise la fonction existante)
        if (!$this->validateCSRFToken()) {
            $this->logger->log('CSRF_VIOLATION', [
                'action'  => 'block_ip',
                'user_id' => $_SESSION['user_id'] ?? null,
            ]);
            $this->jsonResponse([
                'success' => false, 
                'message' => 'Token CSRF invalide ou expiré'
            ], 403);
            return;
        }

        // 3. ✅ RATE LIMITING
        if (!$this->checkRateLimit('block_ip', 10, 60)) {
            $this->jsonResponse([
                'success' => false, 
                'message' => 'Trop de requêtes, veuillez patienter'
            ], 429);
            return;
        }

        // 4. Valider et récupérer les paramètres
        $ip     = $_POST['ip']     ?? '';
        $reason = $_POST['reason'] ?? 'Bloquée manuellement';

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->jsonResponse(['success' => false, 'message' => 'Adresse IP invalide'], 400);
            return;
        }

        $reason = substr(trim($reason), 0, 500);

        try {
            // 5. Effectuer le blocage
            $success = $this->logger->blockIP($ip, $reason, 'MANUAL', $_SESSION['user_id'] ?? null);

            if ($success) {
                // 6. ✅ LOGGER l'action admin
                $this->logger->log('IP_BLOCKED_BY_ADMIN', [
                    'ip'         => $ip,
                    'reason'     => $reason,
                    'blocked_by' => $_SESSION['user_id'] ?? null,
                    'admin_ip'   => $_SERVER['REMOTE_ADDR'] ?? null,
                ]);

                $this->jsonResponse([
                    'success' => true,
                    'message' => "IP $ip bloquée avec succès"
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Erreur lors du blocage de l\'IP'
                ], 500);
            }

        } catch (\Exception $e) {
            error_log('[SecurityController::blockIP] Exception: ' . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Erreur serveur lors du blocage'
            ], 500);
        }
    }

    /**
     * API AJAX : Débloquer une IP
     * Route: POST /admin/security/unblock-ip
     */
    public function unblockIP() {
        $this->requirePOST();

        // ✅ VALIDATION CSRF
        if (!$this->validateCSRFToken()) {
            $this->logger->log('CSRF_VIOLATION', [
                'action'  => 'unblock_ip',
                'user_id' => $_SESSION['user_id'] ?? null,
            ]);
            $this->jsonResponse([
                'success' => false, 
                'message' => 'Token CSRF invalide ou expiré'
            ], 403);
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

                $this->jsonResponse([
                    'success' => true,
                    'message' => "IP $ip débloquée avec succès"
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Erreur lors du déblocage de l\'IP'
                ], 500);
            }

        } catch (\Exception $e) {
            error_log('[SecurityController::unblockIP] Exception: ' . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Erreur serveur lors du déblocage'
            ], 500);
        }
    }

    /**
     * API AJAX : Ajouter une IP à la whitelist
     * Route: POST /admin/security/whitelist-ip
     */
    public function whitelistIP() {
        $this->requirePOST();

        // ✅ VALIDATION CSRF
        if (!$this->validateCSRFToken()) {
            $this->logger->log('CSRF_VIOLATION', [
                'action'  => 'whitelist_ip',
                'user_id' => $_SESSION['user_id'] ?? null,
            ]);
            $this->jsonResponse([
                'success' => false, 
                'message' => 'Token CSRF invalide ou expiré'
            ], 403);
            return;
        }

        $ip          = $_POST['ip']          ?? '';
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

                $this->jsonResponse([
                    'success' => true,
                    'message' => "IP $ip ajoutée à la whitelist"
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Erreur lors de l\'ajout à la whitelist'
                ], 500);
            }

        } catch (\Exception $e) {
            error_log('[SecurityController::whitelistIP] Exception: ' . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Erreur serveur lors de l\'ajout'
            ], 500);
        }
    }

    // =========================================================================
    // SECTION 4 : EXPORT (CSV / JSON)
    // =========================================================================

    public function downloadLog($date = null) {
        if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }

        try {
            $csv = $this->logger->exportToCSV([
                'date_from' => $date,
                'date_to'   => $date,
            ]);

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="security-' . $date . '.csv"');
            header('Content-Length: ' . strlen($csv));
            header('Cache-Control: no-cache, must-revalidate');

            echo $csv;
            exit;

        } catch (\Exception $e) {
            error_log('[SecurityController::downloadLog] Erreur: ' . $e->getMessage());
            die('Erreur lors de l\'export des logs');
        }
    }

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
            error_log('[SecurityController::exportCSV] Erreur: ' . $e->getMessage());
            die('Erreur lors de l\'export CSV');
        }
    }

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
            error_log('[SecurityController::exportJSON] Erreur: ' . $e->getMessage());
            die('Erreur lors de l\'export JSON');
        }
    }

    // =========================================================================
    // SECTION 5 : MÉTHODES DE SÉCURITÉ
    // =========================================================================

    /**
     * ✅ VALIDATION CSRF - Utilise les fonctions existantes du projet
     * 
     * Essaie d'abord verifyCsrfToken() de functions.php
     * Puis CSRF::validateToken() comme fallback
     * 
     * @return bool True si le token est valide
     */
    private function validateCSRFToken(): bool {
        // 1. Chercher le token dans le header (prioritaire pour AJAX)
        $headerToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        // 2. Sinon dans le body POST
        $bodyToken = $_POST['csrf_token'] ?? null;

        // 3. Utiliser le premier token trouvé
        $providedToken = $headerToken ?? $bodyToken;

        // 4. Si aucun token fourni, échec immédiat
        if (!$providedToken || empty($providedToken)) {
            error_log('[CSRF] Token non fourni dans la requête');
            return false;
        }

        // 5. ✅ UTILISER LA FONCTION EXISTANTE verifyCsrfToken()
        if (function_exists('verifyCsrfToken')) {
            return verifyCsrfToken($providedToken);
        }

        // 6. Fallback : utiliser CSRF::validateToken()
        if (class_exists('Core\CSRF')) {
            return CSRF::validateToken($providedToken);
        }

        // 7. Si aucune fonction disponible, échec par sécurité
        error_log('[CSRF] Aucune fonction de validation CSRF disponible !');
        return false;
    }

    /**
     * ✅ RATE LIMITING - Prévient l'abus
     */
    private function checkRateLimit(string $action, int $maxAttempts, int $timeWindow): bool {
        $userId = $_SESSION['user_id'] ?? 'anonymous';
        $key = $action . '_' . $userId;
        $now = time();

        if (!isset(self::$actionCounter[$key])) {
            self::$actionCounter[$key] = [
                'count' => 0,
                'reset_at' => $now + $timeWindow
            ];
        }

        if ($now >= self::$actionCounter[$key]['reset_at']) {
            self::$actionCounter[$key] = [
                'count' => 0,
                'reset_at' => $now + $timeWindow
            ];
        }

        self::$actionCounter[$key]['count']++;

        if (self::$actionCounter[$key]['count'] > $maxAttempts) {
            error_log("[RateLimit] Action '$action' bloquée pour user $userId");
            return false;
        }

        return true;
    }

    /**
     * Vérifie que la requête est bien en POST
     */
    private function requirePOST(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Méthode non autorisée'], 405);
            exit;
        }
    }

    /**
     * Vérifie que la requête est AJAX
     */
    private function isAjaxRequest(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    // =========================================================================
    // SECTION 6 : VALIDATION DES DONNÉES
    // =========================================================================

    private function sanitizeDate(string $date): string {
        if (empty($date)) return '';
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) return '';
        $parts = explode('-', $date);
        if (count($parts) !== 3) return '';
        if (!checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0])) return '';
        return $date;
    }

    private function sanitizeString(string $str): string {
        return trim(strip_tags($str));
    }

    private function sanitizeSeverity(string $severity): string {
        $validSeverities = ['INFO', 'WARNING', 'CRITICAL'];
        $severity = strtoupper(trim($severity));
        return in_array($severity, $validSeverities) ? $severity : '';
    }

    private function sanitizeIP(string $ip): string {
        $ip = trim($ip);
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '';
    }

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
