<?php
/**
 * MARKETFLOW PRO - SECURITY LOGGER VERSION 2.0
 * 
 * ✅ CORRECTION MAJEURE v2 :
 *    Utilise Database::getInstance() (singleton existant) au lieu de créer
 *    une nouvelle connexion PDO avec des credentials hardcodés.
 *    La connexion Railway passe par DATABASE_URL dans le fichier .env.
 * 
 * FONCTIONNALITÉS :
 * - Écriture des événements de sécurité en PostgreSQL
 * - Filtrage avancé (date, type, sévérité, IP, recherche textuelle)
 * - Pagination pour grands volumes
 * - Export CSV / JSON
 * - Statistiques et timeline sur N jours
 * - IPs suspectes avec score de gravité
 * - Gestion des IPs bloquées et whitelistées
 * - Nettoyage automatique des vieux logs
 * 
 * @package  MarketFlow
 * @author   MarketFlow Team
 * @version  2.0
 * @file     core/SecurityLogger.php
 */

namespace Core;

use PDO;
use PDOException;

class SecurityLogger {

    /**
     * Référence au singleton Database déjà configuré
     * @var Database
     */
    private $db;

    /**
     * Constructeur : récupère le singleton Database
     * Aucun credential hardcodé - tout passe par DATABASE_URL du .env
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }

    // =========================================================================
    // SECTION 1 : ÉCRITURE DES ÉVÉNEMENTS
    // =========================================================================

    /**
     * Enregistre un événement de sécurité dans la table security_logs
     *
     * @param  string $eventType Type d'événement (LOGIN_FAILED, CSRF_VIOLATION, etc.)
     * @param  array  $data      Données contextuelles de l'événement
     * @return bool   True si l'insertion a réussi
     */
    public function log($eventType, $data = []) {
        try {
            // Déduire automatiquement la sévérité selon le type
            $severity = $this->getSeverity($eventType);

            // Extraire les champs connus des données (le reste ira en JSON)
            $ip    = $data['ip']         ?? ($_SERVER['REMOTE_ADDR']     ?? null);
            $ua    = $data['user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? null);
            $uri   = $data['uri']        ?? ($_SERVER['REQUEST_URI']     ?? '/');
            $uid   = $data['user_id']    ?? null;
            $email = $data['user_email'] ?? $data['email'] ?? null;

            // Supprimer les champs déjà mappés pour ne garder que le contexte en JSON
            $jsonData = array_diff_key($data, array_flip([
                'ip', 'user_agent', 'uri', 'user_id', 'user_email', 'email'
            ]));

            $stmt = $this->db->prepare(
                "INSERT INTO security_logs
                     (event_type, severity, ip, uri, user_agent, user_id, user_email, data)
                 VALUES
                     (:event_type, :severity, :ip, :uri, :user_agent, :user_id, :user_email, :data)"
            );

            $stmt->execute([
                ':event_type' => $eventType,
                ':severity'   => $severity,
                ':ip'         => $ip,
                ':uri'        => $uri,
                ':user_agent' => $ua,
                ':user_id'    => $uid,
                ':user_email' => $email,
                ':data'       => !empty($jsonData)
                                    ? json_encode($jsonData, JSON_UNESCAPED_UNICODE)
                                    : null,
            ]);

            return true;

        } catch (PDOException $e) {
            // Ne jamais faire crasher l'application à cause d'un log raté
            error_log("SecurityLogger::log() PDOException : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Détermine la sévérité d'un événement selon son type
     *
     * @param  string $eventType
     * @return string INFO | WARNING | CRITICAL
     */
    private function getSeverity($eventType) {
        // Attaques détectées → CRITICAL
        if (in_array($eventType, [
            'CSRF_VIOLATION', 'XSS_ATTEMPT', 'SQLI_ATTEMPT',
            'UNAUTHORIZED_ACCESS', 'FILE_UPLOAD_VIOLATION',
        ])) {
            return 'CRITICAL';
        }

        // Tentatives échouées / blocages → WARNING
        if (in_array($eventType, [
            'LOGIN_FAILED', 'LOGIN_BLOCKED',
            'RATE_LIMIT_EXCEEDED', 'SUSPICIOUS_REQUEST',
        ])) {
            return 'WARNING';
        }

        // Tout le reste → INFO (connexions réussies, inscriptions, etc.)
        return 'INFO';
    }

    // -------------------------------------------------------------------------
    // Méthodes helper pour les événements courants
    // -------------------------------------------------------------------------

    /** Connexion réussie */
    public function logLoginSuccess($email, $userId) {
        return $this->log('LOGIN_SUCCESS', ['user_email' => $email, 'user_id' => $userId]);
    }

    /** Échec de connexion */
    public function logLoginFailed($email, $reason) {
        return $this->log('LOGIN_FAILED', ['user_email' => $email, 'reason' => $reason]);
    }

    /** Compte bloqué après trop d'échecs */
    public function logLoginBlocked($email, $blockedFor) {
        return $this->log('LOGIN_BLOCKED', ['user_email' => $email, 'blocked_for' => $blockedFor]);
    }

    /** Violation de token CSRF */
    public function logCSRFViolation($action, $data = []) {
        return $this->log('CSRF_VIOLATION', array_merge(['action' => $action], $data));
    }

    /** Tentative d'injection XSS */
    public function logXSSAttempt($payload, $data = []) {
        return $this->log('XSS_ATTEMPT', array_merge(['payload' => $payload], $data));
    }

    /** Tentative d'injection SQL */
    public function logSQLiAttempt($payload, $data = []) {
        return $this->log('SQLI_ATTEMPT', array_merge(['payload' => $payload], $data));
    }

    /** Déconnexion */
    public function logLogout($userId) {
        return $this->log('LOGOUT', ['user_id' => $userId]);
    }

    /** Inscription d'un nouvel utilisateur */
    public function logRegister($email, $userId) {
        return $this->log('REGISTER', ['user_email' => $email, 'user_id' => $userId]);
    }

    // =========================================================================
    // SECTION 2 : LECTURE DES ÉVÉNEMENTS AVEC FILTRES + PAGINATION
    // =========================================================================

    /**
     * Récupère les événements avec filtrage dynamique et pagination
     *
     * @param  array $filters Clés disponibles :
     *   date_from  (Y-m-d), date_to (Y-m-d), event_type, severity,
     *   ip (adresse exacte), user_id, search (texte libre)
     * @param  int   $page    Page courante (commence à 1)
     * @param  int   $perPage Lignes par page
     * @return array [events, total, page, per_page, total_pages]
     */
    public function getEvents($filters = [], $page = 1, $perPage = 50) {
        try {
            $where  = [];
            $params = [];

            // --- Construction des clauses WHERE ---

            if (!empty($filters['date_from'])) {
                $where[]              = "timestamp >= :date_from";
                $params[':date_from'] = $filters['date_from'] . ' 00:00:00';
            }

            if (!empty($filters['date_to'])) {
                $where[]            = "timestamp <= :date_to";
                $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
            }

            if (!empty($filters['event_type'])) {
                $where[]               = "event_type = :event_type";
                $params[':event_type'] = $filters['event_type'];
            }

            if (!empty($filters['severity'])) {
                $where[]             = "severity = :severity";
                $params[':severity'] = $filters['severity'];
            }

            if (!empty($filters['ip'])) {
                $where[]    = "ip = :ip";
                $params[':ip'] = $filters['ip'];
            }

            if (!empty($filters['user_id'])) {
                $where[]           = "user_id = :user_id";
                $params[':user_id'] = (int)$filters['user_id'];
            }

            // ILIKE = LIKE insensible à la casse en PostgreSQL
            if (!empty($filters['search'])) {
                $where[]           = "(uri ILIKE :search OR user_email ILIKE :search OR CAST(ip AS TEXT) ILIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }

            $whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            // --- Compter le total pour la pagination ---
            $countStmt = $this->db->prepare("SELECT COUNT(*) as total FROM security_logs $whereSQL");
            $countStmt->execute($params);
            $total      = (int)$countStmt->fetch()['total'];
            $totalPages = $total > 0 ? (int)ceil($total / $perPage) : 1;
            $offset     = ($page - 1) * $perPage;

            // --- Récupérer la page demandée ---
            // CAST(ip AS TEXT) : convertit le type INET PostgreSQL en string PHP
            $sql = "SELECT
                        id,
                        timestamp,
                        event_type,
                        severity,
                        CAST(ip AS TEXT) AS ip,
                        uri,
                        user_agent,
                        user_id,
                        user_email,
                        data,
                        created_at
                    FROM security_logs
                    $whereSQL
                    ORDER BY timestamp DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);

            // Binder les filtres
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }

            // LIMIT et OFFSET doivent être des entiers (PDO::PARAM_INT)
            $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
            $stmt->execute();

            $events = $stmt->fetchAll();

            // Décoder le JSONB pour chaque événement
            foreach ($events as &$e) {
                $e['data'] = !empty($e['data']) ? json_decode($e['data'], true) : [];
            }
            unset($e);

            return [
                'events'      => $events,
                'total'       => $total,
                'page'        => $page,
                'per_page'    => $perPage,
                'total_pages' => $totalPages,
            ];

        } catch (PDOException $e) {
            error_log("SecurityLogger::getEvents() Error : " . $e->getMessage());
            return ['events' => [], 'total' => 0, 'page' => 1, 'per_page' => $perPage, 'total_pages' => 1];
        }
    }

    // =========================================================================
    // SECTION 3 : STATISTIQUES
    // =========================================================================

    /**
     * Retourne le nombre d'événements par type sur les N derniers jours
     *
     * @param  int   $days Nombre de jours (défaut 7)
     * @return array ['LOGIN_FAILED' => 12, 'CSRF_VIOLATION' => 3, ...]
     */
    public function getStats($days = 7) {
        try {
            $days = (int)$days; // Cast sécurisé avant interpolation SQL
            $sql  = "SELECT event_type, COUNT(*) as count
                     FROM security_logs
                     WHERE timestamp >= NOW() - INTERVAL '$days days'
                     GROUP BY event_type
                     ORDER BY count DESC";

            $stmt  = $this->db->query($sql);
            $stats = [];

            while ($row = $stmt->fetch()) {
                $stats[$row['event_type']] = (int)$row['count'];
            }

            return $stats;

        } catch (PDOException $e) {
            error_log("SecurityLogger::getStats() Error : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retourne l'évolution jour par jour sur N jours (pour graphique timeline)
     *
     * @param  int   $days Nombre de jours
     * @return array [['date' => 'Y-m-d', 'critical' => n, 'warning' => n, 'info' => n, 'total' => n], ...]
     */
    public function getTimeline($days = 7) {
        try {
            $days = (int)$days;
            $sql  = "SELECT
                         DATE(timestamp) AS date,
                         COUNT(*) FILTER (WHERE severity = 'CRITICAL') AS critical,
                         COUNT(*) FILTER (WHERE severity = 'WARNING')  AS warning,
                         COUNT(*) FILTER (WHERE severity = 'INFO')     AS info,
                         COUNT(*) AS total
                     FROM security_logs
                     WHERE timestamp >= NOW() - INTERVAL '$days days'
                     GROUP BY DATE(timestamp)
                     ORDER BY date ASC";

            return $this->db->query($sql)->fetchAll();

        } catch (PDOException $e) {
            error_log("SecurityLogger::getTimeline() Error : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retourne les IPs suspectes triées par score de gravité décroissant
     *
     * Score : LOGIN_FAILED×5 + LOGIN_BLOCKED×10 + CSRF×15 + XSS×20 + SQLi×25
     *
     * @param  int   $limit Nombre max d'IPs (défaut 10)
     * @param  int   $days  Fenêtre temporelle en jours (défaut 7)
     * @return array Liste des IPs suspectes
     */
    public function getSuspiciousIPs($limit = 10, $days = 7) {
        try {
            $limit = (int)$limit;
            $days  = (int)$days;

            $sql = "SELECT
                        CAST(ip AS TEXT) AS ip,
                        COUNT(*) AS total,
                        COUNT(*) FILTER (WHERE event_type = 'LOGIN_FAILED')   AS failed_logins,
                        COUNT(*) FILTER (WHERE event_type = 'LOGIN_BLOCKED')  AS blocks,
                        COUNT(*) FILTER (WHERE event_type = 'CSRF_VIOLATION') AS csrf_violations,
                        COUNT(*) FILTER (WHERE event_type = 'XSS_ATTEMPT')    AS xss_attempts,
                        COUNT(*) FILTER (WHERE event_type = 'SQLI_ATTEMPT')   AS sqli_attempts,
                        -- Calcul du score de gravité pondéré
                        (
                            COUNT(*) FILTER (WHERE event_type = 'LOGIN_FAILED')   * 5  +
                            COUNT(*) FILTER (WHERE event_type = 'LOGIN_BLOCKED')  * 10 +
                            COUNT(*) FILTER (WHERE event_type = 'CSRF_VIOLATION') * 15 +
                            COUNT(*) FILTER (WHERE event_type = 'XSS_ATTEMPT')    * 20 +
                            COUNT(*) FILTER (WHERE event_type = 'SQLI_ATTEMPT')   * 25
                        ) AS severity_score,
                        MAX(timestamp) AS last_event
                    FROM security_logs
                    WHERE timestamp >= NOW() - INTERVAL '$days days'
                      AND ip IS NOT NULL
                    GROUP BY ip
                    HAVING (
                        COUNT(*) FILTER (WHERE event_type IN (
                            'LOGIN_FAILED','LOGIN_BLOCKED','CSRF_VIOLATION',
                            'XSS_ATTEMPT','SQLI_ATTEMPT'
                        ))
                    ) > 0
                    ORDER BY severity_score DESC
                    LIMIT $limit";

            return $this->db->query($sql)->fetchAll();

        } catch (PDOException $e) {
            error_log("SecurityLogger::getSuspiciousIPs() Error : " . $e->getMessage());
            return [];
        }
    }

    // =========================================================================
    // SECTION 4 : EXPORT
    // =========================================================================

    /**
     * Exporte les événements filtrés au format CSV (téléchargement admin)
     *
     * @param  array  $filters Mêmes filtres que getEvents()
     * @return string Contenu CSV prêt à envoyer au navigateur
     */
    public function exportToCSV($filters = []) {
        $events = $this->getEvents($filters, 1, 100000)['events'];

        $csv = "ID,Timestamp,Type,Severity,IP,URI,Email,User ID,Data\n";

        foreach ($events as $e) {
            $csv .= implode(',', [
                $e['id'],
                $e['timestamp'],
                $e['event_type'],
                $e['severity'],
                $e['ip'] ?? '',
                '"' . str_replace('"', '""', $e['uri']        ?? '') . '"',
                '"' . str_replace('"', '""', $e['user_email'] ?? '') . '"',
                $e['user_id'] ?? '',
                '"' . str_replace('"', '""', json_encode($e['data'] ?? [])) . '"',
            ]) . "\n";
        }

        return $csv;
    }

    /**
     * Exporte les événements filtrés au format JSON (téléchargement admin)
     *
     * @param  array  $filters Mêmes filtres que getEvents()
     * @return string JSON formaté
     */
    public function exportToJSON($filters = []) {
        $events = $this->getEvents($filters, 1, 100000)['events'];
        return json_encode($events, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // =========================================================================
    // SECTION 5 : GESTION DES IPs BLOQUÉES
    // =========================================================================

    /**
     * Vérifie si une IP est actuellement bloquée (et le blocage non expiré)
     *
     * @param  string $ip Adresse IP
     * @return bool
     */
    public function isIPBlocked($ip) {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) AS cnt FROM blocked_ips
                 WHERE ip = :ip
                   AND is_active = TRUE
                   AND (expires_at IS NULL OR expires_at > NOW())"
            );
            $stmt->execute([':ip' => $ip]);
            return (int)$stmt->fetch()['cnt'] > 0;

        } catch (PDOException $e) {
            error_log("SecurityLogger::isIPBlocked() Error : " . $e->getMessage());
            return false; // Par défaut : ne pas bloquer en cas d'erreur DB
        }
    }

    /**
     * Bloque une adresse IP
     *
     * @param  string      $ip        Adresse IP à bloquer
     * @param  string      $reason    Raison affichée dans le dashboard
     * @param  string      $blockType 'MANUAL' ou 'AUTO'
     * @param  int|null    $adminId   ID de l'admin (pour blocage MANUAL)
     * @param  string|null $expiresAt Date d'expiration 'Y-m-d H:i:s' (null = permanent)
     * @return bool
     */
    public function blockIP($ip, $reason = '', $blockType = 'AUTO', $adminId = null, $expiresAt = null) {
        try {
            // ON CONFLICT : réactiver si l'IP existe déjà en base
            $stmt = $this->db->prepare(
                "INSERT INTO blocked_ips (ip, reason, block_type, blocked_by, expires_at)
                 VALUES (:ip, :reason, :block_type, :blocked_by, :expires_at)
                 ON CONFLICT (ip) DO UPDATE SET
                     reason     = EXCLUDED.reason,
                     block_type = EXCLUDED.block_type,
                     blocked_by = EXCLUDED.blocked_by,
                     expires_at = EXCLUDED.expires_at,
                     is_active  = TRUE,
                     blocked_at = NOW()"
            );

            $stmt->execute([
                ':ip'         => $ip,
                ':reason'     => $reason,
                ':block_type' => $blockType,
                ':blocked_by' => $adminId,
                ':expires_at' => $expiresAt,
            ]);

            return true;

        } catch (PDOException $e) {
            error_log("SecurityLogger::blockIP() Error : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Débloque une IP (is_active = FALSE — l'historique est conservé)
     *
     * @param  string $ip
     * @return bool
     */
    public function unblockIP($ip) {
        try {
            $stmt = $this->db->prepare("UPDATE blocked_ips SET is_active = FALSE WHERE ip = :ip");
            $stmt->execute([':ip' => $ip]);
            return true;

        } catch (PDOException $e) {
            error_log("SecurityLogger::unblockIP() Error : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ajoute une IP à la whitelist
     *
     * @param  string   $ip
     * @param  string   $description Description (ex: "Bureau Paris")
     * @param  int|null $adminId
     * @return bool
     */
    public function whitelistIP($ip, $description = '', $adminId = null) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO whitelisted_ips (ip, description, added_by)
                 VALUES (:ip, :description, :added_by)
                 ON CONFLICT (ip) DO NOTHING"
            );
            $stmt->execute([
                ':ip'          => $ip,
                ':description' => $description,
                ':added_by'    => $adminId,
            ]);
            return true;

        } catch (PDOException $e) {
            error_log("SecurityLogger::whitelistIP() Error : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retourne la liste des IPs bloquées actives
     *
     * @return array
     */
    public function getBlockedIPs() {
        try {
            return $this->db->query(
                "SELECT
                     CAST(ip AS TEXT) AS ip,
                     reason, block_type, blocked_at, expires_at
                 FROM blocked_ips
                 WHERE is_active = TRUE
                 ORDER BY blocked_at DESC"
            )->fetchAll();

        } catch (PDOException $e) {
            error_log("SecurityLogger::getBlockedIPs() Error : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Supprime les logs INFO de plus de 90 jours (politique de rétention)
     *
     * @return int Nombre de lignes supprimées
     */
    public function cleanupOldLogs() {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM security_logs
                 WHERE severity = 'INFO'
                   AND timestamp < NOW() - INTERVAL '90 days'"
            );
            $stmt->execute();
            return $stmt->rowCount();

        } catch (PDOException $e) {
            error_log("SecurityLogger::cleanupOldLogs() Error : " . $e->getMessage());
            return 0;
        }
    }
}
