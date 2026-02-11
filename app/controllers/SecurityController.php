<?php
namespace App\Controllers;
use Core\Controller;
use Core\SecurityLogger;
class SecurityController extends Controller {
    private $logger;
    public function __construct() {
        parent::__construct();
        $this->requireAdmin();
        $this->logger = new SecurityLogger();
    }
    public function index() {
        $stats = $this->logger->getStats();
        $suspiciousIPs = $this->logger->getSuspiciousIPs();
        
        $totalEvents = array_sum($stats);
        $criticalEvents = ($stats['CSRF_VIOLATION'] ?? 0) + 
                          ($stats['XSS_ATTEMPT'] ?? 0) + 
                          ($stats['SQLI_ATTEMPT'] ?? 0);
        $warningEvents = ($stats['LOGIN_FAILED'] ?? 0);
        $infoEvents = ($stats['LOGIN_SUCCESS'] ?? 0);
        $chartLabels = array_keys($stats);
        $chartData = array_values($stats);
        $chartColors = array_map(function($type) {
            return match($type) {
                'LOGIN_SUCCESS' => '#2ecc71',
                'LOGIN_FAILED' => '#e74c3c',
                'CSRF_VIOLATION' => '#f39c12',
                'XSS_ATTEMPT' => '#9b59b6',
                'SQLI_ATTEMPT' => '#e67e22',
                default => '#3498db'
            };
        }, $chartLabels);
        $this->render('admin/security-dashboard', [
            'title' => 'Monitoring Sécurité',
            'stats' => $stats,
            'suspiciousIPs' => $suspiciousIPs,
            'totalEvents' => $totalEvents,
            'criticalEvents' => $criticalEvents,
            'warningEvents' => $warningEvents,
            'infoEvents' => $infoEvents,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'chartColors' => $chartColors
        ]);
    }
    public function downloadLog() {
        $logFile = $this->logger->getLogFile();
        
        if (!file_exists($logFile)) {
            die('Fichier de log non trouvé');
        }
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="security-' . date('Y-m-d') . '.log"');
        header('Content-Length: ' . filesize($logFile));
        readfile($logFile);
        exit;
    }
}

