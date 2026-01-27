<?php
namespace Core;

class SecurityLogger {
    private static $logDir = __DIR__ . '/../logs/';
    private static $alertEmail = null;
    private static $alertThreshold = 10;

    const EVENT_LOGIN_SUCCESS = 'LOGIN_SUCCESS';
    const EVENT_LOGIN_FAILED = 'LOGIN_FAILED';
    const EVENT_LOGIN_BLOCKED = 'LOGIN_BLOCKED';
    const EVENT_CSRF_VIOLATION = 'CSRF_VIOLATION';
    const EVENT_XSS_ATTEMPT = 'XSS_ATTEMPT';
    const EVENT_SQLI_ATTEMPT = 'SQLI_ATTEMPT';
    const EVENT_SUSPICIOUS = 'SUSPICIOUS';
    const EVENT_REGISTER = 'REGISTER';
    const EVENT_LOGOUT = 'LOGOUT';
    const EVENT_SESSION_HIJACK = 'SESSION_HIJACK';

    public static function init() {
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
        if (getenv('SECURITY_ALERT_EMAIL')) {
            self::$alertEmail = getenv('SECURITY_ALERT_EMAIL');
        }
        self::rotateLogs();
    }

    public static function log($eventType, $data = [], $severity = 'INFO', $customIP = null) {
        self::init();
        $ip = $customIP ?? self::getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $timestamp = date('Y-m-d H:i:s');
        $uri = $_SERVER['REQUEST_URI'] ?? 'CLI';

        $logMessage = sprintf(
            "[%s] [%s] [%s] IP:%s | URI:%s | UA:%s | Data:%s\n",
            $timestamp, $severity, $eventType, $ip, $uri,
            substr($userAgent, 0, 100), json_encode($data)
        );

        $logFile = self::$logDir . 'security-' . date('Y-m-d') . '.log';
        file_put_contents($logFile, $logMessage, FILE_APPEND);

            if ($severity === 'CRITICAL') {
                // Appeler le service d'alertes email
                require_once __DIR__ . '/EmailAlertService.php';
                \Core\EmailAlertService::checkAndAlert($eventType, $ip, $data);

                self::checkAndAlert($eventType, $ip, $data);
            }
        
        if ($severity === 'CRITICAL' || $severity === 'WARNING') {
            error_log("[SECURITY] {$eventType} - IP:{$ip} - " . json_encode($data));
        }
    }

    public static function logLoginSuccess($email, $userId, $ip = null) {
        self::log(self::EVENT_LOGIN_SUCCESS, ['email' => $email, 'user_id' => $userId], 'INFO', $ip);
    }

    public static function logLoginFailed($email, $ip = null, $reason = 'invalid_credentials') {
        self::log(self::EVENT_LOGIN_FAILED, ['email' => $email, 'reason' => $reason], 'WARNING', $ip);
        self::checkBruteForce($email);
    }

    public static function logLoginBlocked($email, $ip = null, $blockedFor = 300) {
        self::log(self::EVENT_LOGIN_BLOCKED, [
            'email' => $email,
            'blocked_for_seconds' => $blockedFor,
            'blocked_for_readable' => self::formatTime($blockedFor)
        ], 'CRITICAL', $ip);
    }

    public static function logCsrfViolation($ip = null, $action = 'unknown', $postData = []) {
        $filteredData = self::filterSensitiveData($postData);
        self::log(self::EVENT_CSRF_VIOLATION, ['action' => $action, 'post_data' => $filteredData], 'CRITICAL', $ip);
    }

    public static function logXssAttempt($ip = null, $field = 'unknown', $value = '') {
        self::log(self::EVENT_XSS_ATTEMPT, ['field' => $field, 'value_preview' => substr($value, 0, 200)], 'CRITICAL', $ip);
    }

    public static function logSqliAttempt($ip = null, $query = '', $context = []) {
        self::log(self::EVENT_SQLI_ATTEMPT, ['query_preview' => substr($query, 0, 200), 'context' => $context], 'CRITICAL', $ip);
    }

    public static function logRegister($email, $userId, $ip = null) {
        self::log(self::EVENT_REGISTER, ['email' => $email, 'user_id' => $userId], 'INFO', $ip);
    }

    public static function logLogout($userId, $ip = null) {
        self::log(self::EVENT_LOGOUT, ['user_id' => $userId], 'INFO', $ip);
    }

    public static function logSuspicious($description, $data = [], $ip = null) {
        self::log(self::EVENT_SUSPICIOUS, array_merge(['description' => $description], $data), 'WARNING', $ip);
    }

    private static function getClientIP() {
        $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        foreach ($headers as $header) {
            if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return 'Unknown';
    }

    private static function filterSensitiveData($data) {
        $sensitiveKeys = ['password', 'password_confirmation', 'current_password', 'new_password', 'csrf_token', 'api_key', 'secret', 'token', 'credit_card', 'cvv', 'ssn'];
        $filtered = [];
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $filtered[$key] = '[FILTERED]';
            } else {
                $filtered[$key] = $value;
            }
        }
        return $filtered;
    }

    private static function checkBruteForce($email) {
        $logFile = self::$logDir . 'security-' . date('Y-m-d') . '.log';
        if (!file_exists($logFile)) return;
        $logs = file($logFile, FILE_IGNORE_NEW_LINES);
        $oneHourAgo = time() - 3600;
        $failedAttempts = 0;
        foreach ($logs as $line) {
            if (strpos($line, self::EVENT_LOGIN_FAILED) !== false && strpos($line, $email) !== false) {
                preg_match('/\[([^\]]+)\]/', $line, $matches);
                if (isset($matches[1])) {
                    $logTime = strtotime($matches[1]);
                    if ($logTime >= $oneHourAgo) {
                        $failedAttempts++;
                    }
                }
            }
        }
        if ($failedAttempts >= self::$alertThreshold) {
            self::log(self::EVENT_SUSPICIOUS, ['email' => $email, 'failed_attempts_last_hour' => $failedAttempts, 'description' => 'Possible brute force attack'], 'CRITICAL');
        }
    }

    private static function checkAndAlert($eventType, $ip, $data) {
        if (!self::$alertEmail) return;
        $lastAlertFile = self::$logDir . '.last_alert';
        if (file_exists($lastAlertFile)) {
            $lastAlert = (int)file_get_contents($lastAlertFile);
            if (time() - $lastAlert < 300) return;
        }
        $subject = "[SECURITY ALERT] {$eventType} - MarketFlow Pro";
        $message = "⚠️ ALERTE DE SÉCURITÉ\n\nType : {$eventType}\nDate : " . date('Y-m-d H:i:s') . "\nIP : {$ip}\nDonnées : " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
        mail(self::$alertEmail, $subject, $message, "From: security@marketflow.com\r\n");
        file_put_contents($lastAlertFile, time());
    }

    private static function rotateLogs() {
        $files = glob(self::$logDir . 'security-*.log');
        $thirtyDaysAgo = time() - (30 * 24 * 3600);
        foreach ($files as $file) {
            if (filemtime($file) < $thirtyDaysAgo) {
                $archiveFile = $file . '.gz';
                $data = file_get_contents($file);
                file_put_contents($archiveFile, gzencode($data, 9));
                unlink($file);
            }
        }
    }

    private static function formatTime($seconds) {
        if ($seconds < 60) return "{$seconds} secondes";
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        if ($remainingSeconds > 0) return "{$minutes} minutes {$remainingSeconds} secondes";
        return "{$minutes} minutes";
    }

    public static function getStats($days = 7) {
        $stats = [];
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $logFile = self::$logDir . "security-{$date}.log";
            if (!file_exists($logFile)) continue;
            $logs = file($logFile, FILE_IGNORE_NEW_LINES);
            foreach ($logs as $line) {
                if (preg_match('/\[([A-Z_]+)\]\s+IP:/', $line, $matches)) {
                    $eventType = $matches[1];
                    if (!isset($stats[$eventType])) {
                        $stats[$eventType] = 0;
                    }
                    $stats[$eventType]++;
                }
            }
        }
        return $stats;
    }

    public static function getLogDir() {
        return self::$logDir;
    }
}
