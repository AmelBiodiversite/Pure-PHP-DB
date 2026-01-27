<?php
/**
 * MARKETFLOW PRO - SERVICE D'ALERTES EMAIL
 * Gestion des notifications de sécurité par email
 * 
 * Ce service envoie des emails automatiques quand des événements
 * critiques de sécurité sont détectés (brute force, XSS, CSRF, etc.)
 */

namespace Core;

class EmailAlertService
{
    /**
     * Email destinataire des alertes
     * @var string|null
     */
    private static $alertEmail = null;

    /**
     * Email expéditeur
     * @var string
     */
    private static $fromEmail = 'security@marketflow.com';

    /**
     * Seuil d'événements critiques avant alerte (par heure)
     * @var int
     */
    private static $threshold = 5;

    /**
     * Délai minimum entre deux alertes (en secondes)
     * Par défaut : 15 minutes (900 secondes)
     * @var int
     */
    private static $cooldown = 900;

    /**
     * Fichier de stockage de la dernière alerte
     * @var string
     */
    private static $lastAlertFile = __DIR__ . '/../logs/.last_security_alert';

    /**
     * Initialise le service avec les paramètres de configuration
     * Charge les variables d'environnement depuis .env
     */
    public static function init()
    {
        // Charger le fichier .env si disponible
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Ignorer les commentaires
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                // Parser les variables
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Stocker dans $_ENV
                    if (!isset($_ENV[$key])) {
                        $_ENV[$key] = $value;
                    }
                }
            }
        }

        // Configurer les paramètres depuis l'environnement
        self::$alertEmail = $_ENV['SECURITY_ALERT_EMAIL'] ?? null;
        self::$fromEmail = $_ENV['SECURITY_ALERT_FROM'] ?? 'security@marketflow.com';
        self::$threshold = (int)($_ENV['SECURITY_ALERT_THRESHOLD'] ?? 5);
        self::$cooldown = (int)($_ENV['SECURITY_ALERT_COOLDOWN'] ?? 900);
    }

    /**
     * Vérifie s'il y a des événements critiques et envoie une alerte si nécessaire
     * 
     * @param string $eventType Type d'événement qui a déclenché la vérification
     * @param string $ip Adresse IP impliquée
     * @param array $data Données supplémentaires de l'événement
     * @return bool True si une alerte a été envoyée, false sinon
     */
    public static function checkAndAlert($eventType, $ip, $data = [])
    {
        self::init();

        // Si pas d'email configuré, ne rien faire
        if (!self::$alertEmail) {
            return false;
        }

        // Vérifier le cooldown (éviter de spammer)
        if (!self::canSendAlert()) {
            return false;
        }

        // Compter les événements critiques de la dernière heure
        $criticalCount = self::getCriticalEventsLastHour();

        // Si le seuil est dépassé, envoyer l'alerte
        if ($criticalCount >= self::$threshold) {
            return self::sendAlert($eventType, $ip, $data, $criticalCount);
        }

        return false;
    }

    /**
     * Vérifie si on peut envoyer une alerte (respecte le cooldown)
     * 
     * @return bool True si on peut envoyer, false sinon
     */
    private static function canSendAlert()
    {
        // Si le fichier de dernière alerte n'existe pas, on peut envoyer
        if (!file_exists(self::$lastAlertFile)) {
            return true;
        }

        // Lire la date de la dernière alerte
        $lastAlert = (int)file_get_contents(self::$lastAlertFile);
        
        // Vérifier si le cooldown est écoulé
        return (time() - $lastAlert) >= self::$cooldown;
    }

    /**
     * Compte le nombre d'événements critiques dans la dernière heure
     * 
     * @return int Nombre d'événements critiques
     */
    private static function getCriticalEventsLastHour()
    {
        $logDir = __DIR__ . '/../logs/';
        $logFile = $logDir . 'security-' . date('Y-m-d') . '.log';

        // Si le fichier n'existe pas, retourner 0
        if (!file_exists($logFile)) {
            return 0;
        }

        // Lire le fichier de log
        $logs = file($logFile, FILE_IGNORE_NEW_LINES);
        $oneHourAgo = time() - 3600;
        $criticalCount = 0;

        // Types d'événements considérés comme critiques
        $criticalTypes = [
            'LOGIN_BLOCKED',
            'CSRF_VIOLATION',
            'XSS_ATTEMPT',
            'SQLI_ATTEMPT',
            'SESSION_HIJACK',
            'SUSPICIOUS'
        ];

        // Parcourir les logs
        foreach ($logs as $line) {
            // Extraire la date du log
            if (preg_match('/\[([^\]]+)\]/', $line, $matches)) {
                $logTime = strtotime($matches[1]);
                
                // Si le log est dans la dernière heure
                if ($logTime >= $oneHourAgo) {
                    // Vérifier si c'est un événement critique
                    foreach ($criticalTypes as $type) {
                        if (strpos($line, $type) !== false) {
                            $criticalCount++;
                            break;
                        }
                    }
                }
            }
        }

        return $criticalCount;
    }

    /**
     * Envoie l'email d'alerte de sécurité
     * 
     * @param string $eventType Type d'événement déclencheur
     * @param string $ip Adresse IP impliquée
     * @param array $data Données de l'événement
     * @param int $criticalCount Nombre total d'événements critiques
     * @return bool True si l'email a été envoyé, false sinon
     */
    private static function sendAlert($eventType, $ip, $data, $criticalCount)
    {
        // Créer le sujet de l'email
        $subject = "🚨 [ALERTE SÉCURITÉ] {$criticalCount} événements critiques - MarketFlow Pro";

        // Créer le corps de l'email
        $message = self::buildEmailBody($eventType, $ip, $data, $criticalCount);

        // Headers de l'email
        $headers = [
            'From: ' . self::$fromEmail,
            'Reply-To: ' . self::$fromEmail,
            'X-Mailer: PHP/' . phpversion(),
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8'
        ];

        // Envoyer l'email
        $sent = mail(
            self::$alertEmail,
            $subject,
            $message,
            implode("\r\n", $headers)
        );

        // Si l'email a été envoyé, enregistrer la date
        if ($sent) {
            file_put_contents(self::$lastAlertFile, time());
            error_log("[SECURITY] Alerte email envoyée à " . self::$alertEmail);
        }

        return $sent;
    }

    /**
     * Construit le corps HTML de l'email d'alerte
     * 
     * @param string $eventType Type d'événement
     * @param string $ip Adresse IP
     * @param array $data Données de l'événement
     * @param int $criticalCount Nombre d'événements critiques
     * @return string HTML de l'email
     */
    private static function buildEmailBody($eventType, $ip, $data, $criticalCount)
    {
        // Récupérer les statistiques récentes
        require_once __DIR__ . '/SecurityLogger.php';
        $stats = SecurityLogger::getStats(1);

        // Construire le HTML
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .alert-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0; }
                .critical-box { background: #f8d7da; border-left: 4px solid #dc3545; padding: 20px; margin: 20px 0; }
                .stats { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .stat-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #dee2e6; }
                .stat-label { font-weight: bold; }
                .stat-value { color: #dc3545; font-weight: bold; }
                .footer { background: #f8f9fa; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; font-size: 0.9em; color: #6c757d; }
                .btn { display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🚨 Alerte de Sécurité</h1>
                    <p>MarketFlow Pro</p>
                </div>
                
                <div class="critical-box">
                    <h2 style="margin-top: 0;">⚠️ Activité suspecte détectée</h2>
                    <p><strong>' . $criticalCount . ' événements critiques</strong> ont été détectés au cours de la dernière heure.</p>
                    <p><strong>Dernier événement :</strong> ' . htmlspecialchars($eventType) . '</p>
                    <p><strong>Adresse IP :</strong> ' . htmlspecialchars($ip) . '</p>
                    <p><strong>Date :</strong> ' . date('d/m/Y H:i:s') . '</p>
                </div>

                <div class="stats">
                    <h3>📊 Statistiques des dernières 24h</h3>';

        foreach ($stats as $type => $count) {
            if ($count > 0) {
                $html .= '
                    <div class="stat-item">
                        <span class="stat-label">' . htmlspecialchars($type) . '</span>
                        <span class="stat-value">' . $count . '</span>
                    </div>';
            }
        }

        $html .= '
                </div>

                <div class="alert-box">
                    <h3>🔐 Actions recommandées</h3>
                    <ul>
                        <li>Vérifiez le dashboard de sécurité immédiatement</li>
                        <li>Identifiez les adresses IP suspectes</li>
                        <li>Bloquez les IPs malveillantes si nécessaire</li>
                        <li>Vérifiez l\'intégrité des données</li>
                    </ul>
                </div>

                <div style="text-align: center;">
                    <a href="https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/admin/security" class="btn">
                        🔒 Accéder au Dashboard
                    </a>
                </div>

                <div class="footer">
                    <p>Cet email a été envoyé automatiquement par le système de monitoring de sécurité.</p>
                    <p>MarketFlow Pro - Monitoring de Sécurité</p>
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Envoie un email de test pour vérifier la configuration
     * 
     * @return bool True si l'email a été envoyé, false sinon
     */
    public static function sendTestEmail()
    {
        self::init();

        if (!self::$alertEmail) {
            return false;
        }

        $subject = "✅ Test - Système d'alertes MarketFlow Pro";
        
        $message = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #ffffff; padding: 30px; }
                .success-box { background: #d4edda; border-left: 4px solid #28a745; padding: 20px; margin: 20px 0; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✅ Email de Test</h1>
                    <p>Système d\'alertes de sécurité</p>
                </div>
                <div class="content">
                    <div class="success-box">
                        <h2 style="margin-top: 0;">Configuration réussie !</h2>
                        <p>Si vous recevez cet email, cela signifie que le système d\'alertes est correctement configuré.</p>
                    </div>
                    <h3>📋 Configuration actuelle :</h3>
                    <ul>
                        <li><strong>Email destinataire :</strong> ' . self::$alertEmail . '</li>
                        <li><strong>Seuil d\'alerte :</strong> ' . self::$threshold . ' événements/heure</li>
                        <li><strong>Délai entre alertes :</strong> ' . (self::$cooldown / 60) . ' minutes</li>
                    </ul>
                    <p>Le système surveillera automatiquement les événements de sécurité et vous alertera en cas d\'activité suspecte.</p>
                </div>
            </div>
        </body>
        </html>';

        $headers = [
            'From: ' . self::$fromEmail,
            'Reply-To: ' . self::$fromEmail,
            'X-Mailer: PHP/' . phpversion(),
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8'
        ];

        return mail(
            self::$alertEmail,
            $subject,
            $message,
            implode("\r\n", $headers)
        );
    }
}
