<?php
/**
 * MARKETFLOW PRO - SECURITY CONTROLLER VERSION 2.0
 *
 * Contrôleur pour le dashboard de monitoring de sécurité admin.
 *
 * ✅ CORRECTIONS v2 :
 *    - Utilise $this->db (hérité de Core\Controller) — pas de nouvelle connexion
 *    - Noms de méthodes conformes aux routes existantes dans routes.php :
 *        index(), apiEvents(), downloadLog()
 *    - Nouvelles routes à ajouter dans routes.php pour les fonctionnalités avancées
 *
 * FONCTIONNALITÉS :
 * - Dashboard principal avec statistiques, graphiques, IPs suspectes
 * - Filtrage dynamique transmis via $_GET
 * - Pagination des événements récents
 * - Export CSV / JSON
 * - Actions sur IPs : bloquer / débloquer / whitelist (AJAX)
 * - API AJAX pour stats et IPs suspectes
 *
 * @package  MarketFlow
 * @author   MarketFlow Team
 * @version  2.0
 * @file     app/controllers/SecurityController.php
 */

namespace App\Controllers;

use Core\Controller;
use Core\SecurityLogger;

class SecurityController extends Controller {

    /**
     * Instance du SecurityLogger v2
     * @var SecurityLogger
     */
    private $logger;

    /**
     * Constructeur : vérifie les droits admin et instancie le logger
     */
    public function __construct() {
        parent::__construct();    // Initialise $this->db via Database::getInstance()
        $this->requireAdmin();    // Redirige vers / si l'utilisateur n'est pas admin
        $this->logger = new SecurityLogger();
    }

    // =========================================================================
    // ROUTE : GET /admin/security
    // =========================================================================

    /**
     * Dashboard principal de sécurité
     * Affiche statistiques globales, timeline, IPs suspectes, événements récents
     */
    public function index() {

        // --- Récupération des filtres depuis l'URL ---
        $filters = [
            'date_from'  => $_GET['date_from']  ?? date('Y-m-d', strtotime('-7 days')),
            'date_to'    => $_GET['date_to']    ?? date('Y-m-d'),
            'event_type' => $_GET['event_type'] ?? '',
            'severity'   => $_GET['severity']   ?? '',
            'ip'         => $_GET['ip']         ?? '',
            'search'     => $_GET['search']     ?? '',
        ];

        // Numéro de page et nombre d'items par page
        $page    = isset($_GET['page'])     ? max(1, (int)$_GET['page'])    : 1;
        $perPage = isset($_GET['per_page']) ? min(200, (int)$_GET['per_page']) : 50;

        // --- Statistiques globales (7 derniers jours) ---
        $stats = $this->logger->getStats(7);

        // Calculer les totaux par sévérité depuis les stats
        $totalEvents   = array_sum($stats);
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

        // Donut : répartition par type
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

            // Statistiques
            'stats'          => $stats,
            'totalEvents'    => $totalEvents,
            'criticalEvents' => $criticalEvents,
            'warningEvents'  => $warningEvents,
            'infoEvents'     => $infoEvents,

            // IPs suspectes
            'suspiciousIPs'  => $suspiciousIPs,

            // Événements paginés
            'recentEvents'   => $eventsData['events'],
            'pagination'     => [
                'total'       => $eventsData['total'],
                'page'        => $eventsData['page'],
                'per_page'    => $eventsData['per_page'],
                'total_pages' => $eventsData['total_pages'],
            ],

            // Filtres actifs (pour pré-remplir le formulaire dans la vue)
            'filters'        => $filters,
            'eventTypes'     => $eventTypes,

            // Graphique Donut
            'chartLabels'    => $chartLabels,
            'chartData'      => $chartData,
            'chartColors'    => $chartColors,

            // Graphique Timeline
            'timelineLabels'   => $timelineLabels,
            'timelineCritical' => $timelineCritical,
            'timelineWarning'  => $timelineWarning,
            'timelineInfo'     => $timelineInfo,
        ]);
    }

    // =========================================================================
    // ROUTE : GET /admin/security/api/events  (existante dans routes.php)
    // =========================================================================

    /**
     * API AJAX : retourne les événements filtrés en JSON
     * Utilisée par le dashboard pour recharger le tableau sans rechargement de page
     */
    public function apiEvents() {
        $filters = [
            'date_from'  => $_GET['date_from']  ?? '',
            'date_to'    => $_GET['date_to']    ?? '',
            'event_type' => $_GET['event_type'] ?? '',
            'severity'   => $_GET['severity']   ?? '',
            'ip'         => $_GET['ip']         ?? '',
            'search'     => $_GET['search']     ?? '',
        ];

        $page    = isset($_GET['page'])     ? max(1, (int)$_GET['page'])       : 1;
        $perPage = isset($_GET['per_page']) ? min(200, (int)$_GET['per_page']) : 50;

        $result = $this->logger->getEvents($filters, $page, $perPage);

        $this->jsonResponse($result);
    }

    // =========================================================================
    // ROUTE : GET /admin/security/download/{date}  (existante dans routes.php)
    // =========================================================================

    /**
     * Télécharger les logs d'une journée spécifique en CSV
     * Rétrocompatible avec l'ancien système de téléchargement par date
     *
     * @param string|null $date Date au format Y-m-d
     */
    public function downloadLog($date = null) {
        // Utiliser aujourd'hui si pas de date fournie
        if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }

        // Filtrer sur la journée demandée
        $csv = $this->logger->exportToCSV([
            'date_from' => $date,
            'date_to'   => $date,
        ]);

        // Headers HTTP pour forcer le téléchargement du fichier
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="security-' . $date . '.csv"');
        header('Content-Length: ' . strlen($csv));
        header('Cache-Control: no-cache, must-revalidate');

        echo $csv;
        exit;
    }

    // =========================================================================
    // NOUVELLES ROUTES À AJOUTER DANS routes.php
    // =========================================================================

    /**
     * Export CSV de tous les événements filtrés
     * ROUTE À AJOUTER : $router->get('/admin/security/export/csv', 'SecurityController@exportCSV');
     */
    public function exportCSV() {
        $filters = $this->getFiltersFromGET();
        $csv     = $this->logger->exportToCSV($filters);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="security-logs-' . date('Y-m-d-His') . '.csv"');
        header('Content-Length: ' . strlen($csv));
        header('Cache-Control: no-cache, must-revalidate');

        echo $csv;
        exit;
    }

    /**
     * Export JSON de tous les événements filtrés
     * ROUTE À AJOUTER : $router->get('/admin/security/export/json', 'SecurityController@exportJSON');
     */
    public function exportJSON() {
        $filters = $this->getFiltersFromGET();
        $json    = $this->logger->exportToJSON($filters);

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="security-logs-' . date('Y-m-d-His') . '.json"');
        header('Content-Length: ' . strlen($json));

        echo $json;
        exit;
    }

    /**
     * API AJAX : Bloquer une IP
     * ROUTE À AJOUTER : $router->post('/admin/security/block-ip', 'SecurityController@blockIP');
     */
    public function blockIP() {
        $this->requirePOST();

        $ip     = $_POST['ip']     ?? '';
        $reason = $_POST['reason'] ?? 'Bloquée manuellement';

        // Valider le format IP (IPv4 et IPv6)
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->jsonResponse(['success' => false, 'message' => 'Adresse IP invalide'], 400);
        }

        $success = $this->logger->blockIP($ip, $reason, 'MANUAL', $_SESSION['user_id'] ?? null);

        if ($success) {
            // Logger l'action de blocage elle-même
            $this->logger->log('IP_BLOCKED_BY_ADMIN', [
                'ip'         => $ip,
                'reason'     => $reason,
                'blocked_by' => $_SESSION['user_id'] ?? null,
            ]);
        }

        $this->jsonResponse([
            'success' => $success,
            'message' => $success ? "IP $ip bloquée avec succès" : "Erreur lors du blocage",
        ]);
    }

    /**
     * API AJAX : Débloquer une IP
     * ROUTE À AJOUTER : $router->post('/admin/security/unblock-ip', 'SecurityController@unblockIP');
     */
    public function unblockIP() {
        $this->requirePOST();

        $ip = $_POST['ip'] ?? '';

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->jsonResponse(['success' => false, 'message' => 'Adresse IP invalide'], 400);
        }

        $success = $this->logger->unblockIP($ip);

        $this->jsonResponse([
            'success' => $success,
            'message' => $success ? "IP $ip débloquée avec succès" : "Erreur lors du déblocage",
        ]);
    }

    /**
     * API AJAX : Ajouter une IP à la whitelist
     * ROUTE À AJOUTER : $router->post('/admin/security/whitelist-ip', 'SecurityController@whitelistIP');
     */
    public function whitelistIP() {
        $this->requirePOST();

        $ip          = $_POST['ip']          ?? '';
        $description = $_POST['description'] ?? '';

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->jsonResponse(['success' => false, 'message' => 'Adresse IP invalide'], 400);
        }

        $success = $this->logger->whitelistIP($ip, $description, $_SESSION['user_id'] ?? null);

        $this->jsonResponse([
            'success' => $success,
            'message' => $success ? "IP $ip ajoutée à la whitelist" : "Erreur lors de l'ajout",
        ]);
    }

    /**
     * API AJAX : Retourne les statistiques en JSON (pour refresh temps réel)
     * ROUTE À AJOUTER : $router->get('/admin/security/api/stats', 'SecurityController@apiStats');
     */
    public function apiStats() {
        $days  = isset($_GET['days']) ? (int)$_GET['days'] : 7;
        $stats = $this->logger->getStats($days);
        $this->jsonResponse($stats);
    }

    /**
     * API AJAX : Retourne les IPs suspectes en JSON
     * ROUTE À AJOUTER : $router->get('/admin/security/api/suspicious-ips', 'SecurityController@apiSuspiciousIPs');
     */
    public function apiSuspiciousIPs() {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $days  = isset($_GET['days'])  ? (int)$_GET['days']  : 7;
        $ips   = $this->logger->getSuspiciousIPs($limit, $days);
        $this->jsonResponse($ips);
    }

    // =========================================================================
    // MÉTHODES PRIVÉES HELPERS
    // =========================================================================

    /**
     * Extrait les filtres depuis les paramètres GET
     *
     * @return array
     */
    private function getFiltersFromGET() {
        return [
            'date_from'  => $_GET['date_from']  ?? '',
            'date_to'    => $_GET['date_to']    ?? '',
            'event_type' => $_GET['event_type'] ?? '',
            'severity'   => $_GET['severity']   ?? '',
            'ip'         => $_GET['ip']         ?? '',
            'search'     => $_GET['search']     ?? '',
        ];
    }

    /**
     * Vérifie que la requête est bien en POST (pour les actions AJAX)
     * Retourne une erreur JSON 405 si ce n'est pas le cas
     */
    private function requirePOST() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
    }
}
