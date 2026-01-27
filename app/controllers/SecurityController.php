<?php
/**
 * MARKETFLOW PRO - SECURITY CONTROLLER
 * Gestion du dashboard de monitoring de sécurité
 * 
 * Ce contrôleur permet aux administrateurs de :
 * - Visualiser les événements de sécurité en temps réel
 * - Consulter les statistiques des 7 derniers jours
 * - Identifier les IPs suspectes
 * - Voir les tentatives d'attaque (brute force, CSRF, XSS, etc.)
 * 
 * SÉCURITÉ :
 * - Accessible uniquement aux administrateurs (via requireAdmin())
 * - Données sensibles filtrées avant affichage
 * - Logs en lecture seule (pas de modification possible)
 * 
 * Fichier : app/controllers/SecurityController.php
 */

namespace App\Controllers;

use Core\Controller;
use Core\SecurityLogger;

class SecurityController extends Controller {

    /**
     * Constructeur
     * Force l'authentification admin pour toutes les méthodes de ce contrôleur
     */
    public function __construct() {
        parent::__construct();
        
        // Vérifier que l'utilisateur connecté est bien un admin
        // Si ce n'est pas le cas, redirection vers la page d'accueil
        $this->requireAdmin();
    }

    /**
     * Dashboard principal de sécurité
     * Affiche les statistiques et les événements récents
     */
    public function index() {
        // Récupérer les statistiques des 7 derniers jours
        // Retourne un tableau : ['LOGIN_SUCCESS' => 10, 'LOGIN_FAILED' => 5, ...]
        $stats = SecurityLogger::getStats(7);

        // Récupérer les 50 événements les plus récents
        // Pour afficher dans le tableau du dashboard
        $recentEvents = $this->getRecentEvents(50);

        // Identifier les IPs les plus actives (potentiellement suspectes)
        // Par exemple : une IP qui a 20 tentatives de connexion échouées
        $suspiciousIPs = $this->getSuspiciousIPs();

        // Calculer le nombre total d'événements sur 7 jours
        $totalEvents = array_sum($stats);

        // Calculer le nombre d'événements critiques (CRITICAL severity)
        // Ce sont les événements les plus graves : blocages, violations CSRF, etc.
        $criticalEvents = ($stats['LOGIN_BLOCKED'] ?? 0) + 
                          ($stats['CSRF_VIOLATION'] ?? 0) + 
                          ($stats['XSS_ATTEMPT'] ?? 0) + 
                          ($stats['SQLI_ATTEMPT'] ?? 0);

        // Calculer le nombre d'événements d'alerte (WARNING severity)
        // Ce sont les tentatives suspectes mais non critiques
        $warningEvents = ($stats['LOGIN_FAILED'] ?? 0) + 
                         ($stats['SUSPICIOUS'] ?? 0);

        // Calculer le nombre d'événements normaux (INFO severity)
        // Connexions réussies, inscriptions, déconnexions
        $infoEvents = ($stats['LOGIN_SUCCESS'] ?? 0) + 
                      ($stats['REGISTER'] ?? 0) + 
                      ($stats['LOGOUT'] ?? 0);

        // Afficher la vue du dashboard avec toutes ces données
        $this->render('admin/security-dashboard', [
            'title' => 'Monitoring Sécurité',           // Titre de la page
            'stats' => $stats,                           // Stats par type d'événement
            'recentEvents' => $recentEvents,             // 50 derniers événements
            'suspiciousIPs' => $suspiciousIPs,           // IPs suspectes
            'totalEvents' => $totalEvents,               // Total sur 7 jours
            'criticalEvents' => $criticalEvents,         // Nombre d'événements critiques
            'warningEvents' => $warningEvents,           // Nombre d'avertissements
            'infoEvents' => $infoEvents                  // Nombre d'événements normaux
        ]);
    }

    /**
     * Récupérer les événements récents depuis les fichiers de logs
     * 
     * @param int $limit Nombre d'événements à retourner (par défaut : 100)
     * @return array Tableau d'événements parsés
     */
    private function getRecentEvents($limit = 100) {
        $events = [];
        
        // Récupérer le chemin du répertoire des logs
        $logDir = SecurityLogger::getLogDir();

        // Lister tous les fichiers de logs (7 derniers jours)
        // Format : security-2026-01-26.log, security-2026-01-25.log, etc.
        $logFiles = [];
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $logFile = $logDir . "security-{$date}.log";
            
            // Vérifier que le fichier existe avant de l'ajouter
            if (file_exists($logFile)) {
                $logFiles[] = $logFile;
            }
        }

        // Parcourir tous les fichiers de logs
        foreach ($logFiles as $logFile) {
            // Lire toutes les lignes du fichier
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            // Parcourir chaque ligne et la parser
            foreach ($lines as $line) {
                // Parser la ligne de log
                // Format : [2026-01-26 15:30:12] [INFO] [LOGIN_SUCCESS] IP:127.0.0.1 | URI:/login | UA:Mozilla/5.0 | Data:{...}
                $event = $this->parseLogLine($line);
                
                // Si le parsing a réussi, ajouter l'événement
                if ($event) {
                    $events[] = $event;
                }
            }
        }

        // Trier les événements par date (du plus récent au plus ancien)
        usort($events, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        // Limiter au nombre d'événements demandés
        return array_slice($events, 0, $limit);
    }

    /**
     * Parser une ligne de log en tableau structuré
     * 
     * @param string $line Ligne brute du fichier de log
     * @return array|null Événement parsé ou null si erreur
     */
    private function parseLogLine($line) {
        // Exemple de ligne :
        // [2026-01-26 15:30:12] [INFO] [LOGIN_SUCCESS] IP:127.0.0.1 | URI:/login | UA:Mozilla/5.0 | Data:{"email":"test@test.com"}

        // Regex pour extraire les différentes parties
        $pattern = '/\[([^\]]+)\] \[([^\]]+)\] \[([^\]]+)\] IP:([^\|]+) \| URI:([^\|]+) \| UA:([^\|]+) \| Data:(.+)/';
        
        // Tenter de matcher la ligne
        if (preg_match($pattern, $line, $matches)) {
            // Décoder les données JSON
            $data = json_decode(trim($matches[7]), true);
            
            // Retourner un tableau structuré
            return [
                'timestamp' => trim($matches[1]),     // Date et heure
                'severity' => trim($matches[2]),      // INFO, WARNING, CRITICAL
                'event_type' => trim($matches[3]),    // LOGIN_SUCCESS, LOGIN_FAILED, etc.
                'ip' => trim($matches[4]),            // Adresse IP
                'uri' => trim($matches[5]),           // URI de la requête
                'user_agent' => trim($matches[6]),    // User agent du navigateur
                'data' => $data ?? []                 // Données supplémentaires (email, etc.)
            ];
        }

        // Si le parsing échoue, retourner null
        return null;
    }

    /**
     * Identifier les IPs suspectes
     * Une IP est considérée suspecte si elle a :
     * - Plus de 5 tentatives de connexion échouées
     * - Au moins 1 violation CSRF
     * - Au moins 1 tentative d'injection
     * 
     * @return array Top 10 des IPs suspectes avec leurs statistiques
     */
    private function getSuspiciousIPs() {
        $ipStats = [];
        
        // Récupérer tous les événements récents (sans limite)
        $events = $this->getRecentEvents(1000);

        // Compter les événements par IP et par type
        foreach ($events as $event) {
            $ip = $event['ip'];
            
            // Ignorer les IPs inconnues (CLI)
            if ($ip === 'Unknown') {
                continue;
            }

            // Initialiser les compteurs pour cette IP si besoin
            if (!isset($ipStats[$ip])) {
                $ipStats[$ip] = [
                    'ip' => $ip,
                    'total' => 0,
                    'failed_logins' => 0,
                    'blocks' => 0,
                    'csrf_violations' => 0,
                    'xss_attempts' => 0,
                    'sqli_attempts' => 0,
                    'last_event' => $event['timestamp'],
                    'severity_score' => 0  // Score de gravité calculé
                ];
            }

            // Incrémenter le compteur total
            $ipStats[$ip]['total']++;

            // Incrémenter les compteurs par type d'événement
            switch ($event['event_type']) {
                case 'LOGIN_FAILED':
                    $ipStats[$ip]['failed_logins']++;
                    $ipStats[$ip]['severity_score'] += 1;  // +1 point par échec
                    break;
                case 'LOGIN_BLOCKED':
                    $ipStats[$ip]['blocks']++;
                    $ipStats[$ip]['severity_score'] += 10; // +10 points par blocage
                    break;
                case 'CSRF_VIOLATION':
                    $ipStats[$ip]['csrf_violations']++;
                    $ipStats[$ip]['severity_score'] += 15; // +15 points par CSRF
                    break;
                case 'XSS_ATTEMPT':
                    $ipStats[$ip]['xss_attempts']++;
                    $ipStats[$ip]['severity_score'] += 20; // +20 points par XSS
                    break;
                case 'SQLI_ATTEMPT':
                    $ipStats[$ip]['sqli_attempts']++;
                    $ipStats[$ip]['severity_score'] += 20; // +20 points par SQLi
                    break;
            }
        }

        // Filtrer les IPs vraiment suspectes (score >= 5)
        $suspicious = array_filter($ipStats, function($stats) {
            return $stats['severity_score'] >= 5;
        });

        // Trier par score de gravité (du plus dangereux au moins dangereux)
        usort($suspicious, function($a, $b) {
            return $b['severity_score'] - $a['severity_score'];
        });

        // Retourner les 10 IPs les plus suspectes
        return array_slice($suspicious, 0, 10);
    }

    /**
     * API JSON pour récupérer les événements
     * Permet de créer des graphiques dynamiques en JavaScript
     * 
     * Exemple d'utilisation :
     * fetch('/admin/security/api/events?days=7')
     *   .then(res => res.json())
     *   .then(data => console.log(data))
     */
    public function apiEvents() {
        // Récupérer le nombre de jours depuis les paramètres GET
        $days = isset($_GET['days']) ? (int)$_GET['days'] : 7;
        
        // Limiter à maximum 30 jours
        if ($days > 30) {
            $days = 30;
        }

        // Récupérer les statistiques
        $stats = SecurityLogger::getStats($days);

        // Retourner les données en JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'days' => $days,
            'stats' => $stats,
            'total' => array_sum($stats)
        ]);
        exit;
    }

    /**
     * Télécharger un fichier de log complet
     * Permet à l'admin de télécharger les logs pour analyse externe
     * 
     * @param string $date Date du log à télécharger (format: YYYY-MM-DD)
     */
    public function downloadLog($date) {
        // Valider le format de la date
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            die('Format de date invalide');
        }

        // Construire le chemin du fichier
        $logFile = SecurityLogger::getLogDir() . "security-{$date}.log";

        // Vérifier que le fichier existe
        if (!file_exists($logFile)) {
            die('Fichier de log non trouvé');
        }

        // Définir les headers pour le téléchargement
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="security-' . $date . '.log"');
        header('Content-Length: ' . filesize($logFile));

        // Envoyer le fichier
        readfile($logFile);
        exit;
    }
}
