<?php
namespace Core;

use PDO;
use PDOException;
use Exception;

/**
 * MARKETFLOW PRO - CONNEXION POSTGRESQL (REPLIT)
 * Fichier : config/database.php
 */

class Database {
    private static $instance = null;
    private $pdo;

    /**
     * Constructeur privé (Singleton)
     */
    private function __construct() {
        try {
            // Récupérer l'URL de connexion depuis Replit
            $databaseUrl = getenv('DATABASE_URL');

            if ($databaseUrl) {
                // Parser l'URL PostgreSQL
                $parts = parse_url($databaseUrl);

                if (!$parts) {
                    throw new Exception("URL de base de données invalide");
                }

                // Extraire les composants
                $host = $parts['host'] ?? 'localhost';
                $port = $parts['port'] ?? 5432;
                $dbname = ltrim($parts['path'] ?? '', '/');
                $user = $parts['user'] ?? 'postgres';
                $pass = $parts['pass'] ?? '';

                // IMPORTANT : Construire le DSN avec pgsql: (pas postgresql:)
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

                // Créer la connexion PDO avec user/pass séparés
                $this->pdo = new PDO($dsn, $user, $pass);

            } else {
                // Fallback : connexion manuelle
                throw new Exception("DATABASE_URL non définie");
            }

            // Configuration PDO pour PostgreSQL
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            // Définir le schéma par défaut
            $this->pdo->exec("SET search_path TO public");

        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Database Error: " . $e->getMessage());
            die("Erreur de configuration : " . $e->getMessage());
        }
    }

    /**
     * Obtenir l'instance unique (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtenir la connexion PDO
     */
    public function getConnection() {
        return $this->pdo;
    }

    /**
     * Exécuter une requête SELECT
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Exécuter une requête INSERT/UPDATE/DELETE
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Execute Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir le dernier ID inséré (PostgreSQL utilise RETURNING)
     */
    public function lastInsertId($sequence = null) {
        // Pour PostgreSQL, on utilise currval() sur la séquence
        // Ou on utilise RETURNING id dans la requête INSERT
        return $this->pdo->lastInsertId($sequence);
    }

    /**
     * Démarrer une transaction
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    /**
     * Valider une transaction
     */
    public function commit() {
        return $this->pdo->commit();
    }

    /**
     * Annuler une transaction
     */
    public function rollback() {
        return $this->pdo->rollBack();
    }

    /**
     * Échapper une chaîne (prévention SQL Injection)
     * Note: Utilisez plutôt les prepared statements
     */
    public function escape($value) {
        return $this->pdo->quote($value);
    }

    /**
     * Vérifier si une table existe
     */
    public function tableExists($tableName) {
        $sql = "SELECT EXISTS (
            SELECT FROM information_schema.tables 
            WHERE table_schema = 'public' 
            AND table_name = :table_name
        )";
        $result = $this->query($sql, ['table_name' => $tableName]);
        return $result[0]['exists'] ?? false;
    }

    /**
     * Obtenir toutes les tables
     */
    public function getTables() {
        $sql = "SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = 'public' 
                ORDER BY table_name";
        return $this->query($sql);
    }

    /**
     * Compter les lignes d'une table
     */
    public function countRows($tableName) {
        $sql = "SELECT COUNT(*) as count FROM " . $this->escapeIdentifier($tableName);
        $result = $this->query($sql);
        return $result[0]['count'] ?? 0;
    }

    /**
     * Échapper un identifiant (table, colonne)
     * PostgreSQL utilise des guillemets doubles
     */
    private function escapeIdentifier($identifier) {
        return '"' . str_replace('"', '""', $identifier) . '"';
    }

    /**
     * Tester la connexion
     */
    public function testConnection() {
        try {
            $this->pdo->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Obtenir la version PostgreSQL
     */
    public function getVersion() {
        try {
            $result = $this->query('SELECT version()');
            return $result[0]['version'] ?? 'Unknown';
        } catch (PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Empêcher le clonage (Singleton)
     */
    private function __clone() {}

    /**
     * Empêcher la désérialisation (Singleton)
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Fonction helper globale pour obtenir la connexion
function db() {
    return \Core\Database::getInstance()->getConnection();
}

// Fonction helper pour exécuter une requête simple
function dbQuery($sql, $params = []) {
    return \Core\Database::getInstance()->query($sql, $params);
}

// Fonction helper pour exécuter une commande
function dbExecute($sql, $params = []) {
    return \Core\Database::getInstance()->execute($sql, $params);
}