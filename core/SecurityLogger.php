<?php
namespace Core;

class SecurityLogger {
    private $logFile;
    
    public function __construct() {
        $logDir = __DIR__ . '/../data/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $this->logFile = $logDir . '/security.log';
    }
    
    public function getLogFile() {
        return $this->logFile;
    }
    
    public function log($type, $data = []) {
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'ip' => $data['ip'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'data' => $data
        ];
        
        file_put_contents($this->logFile, json_encode($entry) . PHP_EOL, FILE_APPEND);
        return true;
    }
    
    public function logLoginBlocked($email, $blockedFor) {
        $this->log('LOGIN_BLOCKED', ['email' => $email, 'blocked_for' => $blockedFor]);
    }
    
    public function logCSRFViolation($action, $data = []) {
        $this->log('CSRF_VIOLATION', ['action' => $action, 'data' => $data]);
    }
    
    public function logLoginSuccess($email, $userId) {
        $this->log('LOGIN_SUCCESS', ['email' => $email, 'user_id' => $userId]);
    }
    
    public function logLoginFailed($email, $reason) {
        $this->log('LOGIN_FAILED', ['email' => $email, 'reason' => $reason]);
    }
    
    public function logRegister($email, $userId) {
        $this->log('REGISTER', ['email' => $email, 'user_id' => $userId]);
    }
    
    public function logLogout($userId) {
        $this->log('LOGOUT', ['user_id' => $userId]);
    }
    
    public function getStats() {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $stats = [];
        
        foreach ($lines as $line) {
            $entry = json_decode($line, true);
            if ($entry && isset($entry['type'])) {
                $type = $entry['type'];
                $stats[$type] = ($stats[$type] ?? 0) + 1;
            }
        }
        
        return $stats;
    }
    
    public function getSuspiciousIPs() {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $ips = [];
        
        foreach ($lines as $line) {
            $entry = json_decode($line, true);
            if (!$entry || !isset($entry['ip'])) continue;
            
            $ip = $entry['ip'];
            if (!isset($ips[$ip])) {
                $ips[$ip] = [
                    'ip' => $ip,
                    'total' => 0,
                    'failed_logins' => 0,
                    'blocks' => 0,
                    'csrf_violations' => 0,
                    'xss_attempts' => 0,
                    'sqli_attempts' => 0,
                    'severity_score' => 0,
                    'last_event' => ''
                ];
            }
            
            $ips[$ip]['total']++;
            $ips[$ip]['last_event'] = $entry['timestamp'];
            
            switch ($entry['type']) {
                case 'LOGIN_FAILED':
                    $ips[$ip]['failed_logins']++;
                    $ips[$ip]['severity_score'] += 5;
                    break;
                case 'LOGIN_BLOCKED':
                    $ips[$ip]['blocks']++;
                    $ips[$ip]['severity_score'] += 10;
                    break;
                case 'CSRF_VIOLATION':
                    $ips[$ip]['csrf_violations']++;
                    $ips[$ip]['severity_score'] += 15;
                    break;
                case 'XSS_ATTEMPT':
                    $ips[$ip]['xss_attempts']++;
                    $ips[$ip]['severity_score'] += 20;
                    break;
                case 'SQLI_ATTEMPT':
                    $ips[$ip]['sqli_attempts']++;
                    $ips[$ip]['severity_score'] += 25;
                    break;
            }
        }
        
        usort($ips, fn($a, $b) => $b['severity_score'] <=> $a['severity_score']);
        return array_filter($ips, fn($ip) => $ip['severity_score'] > 0);
    }
}

