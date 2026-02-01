<?php
namespace Core;
use PDO;
use PDOException;
use Exception;

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            // Charger le fichier .env
            Env::load();

            // Essayer d'abord DATABASE_URL (Railway/Replit)
            $databaseUrl = Env::get('DATABASE_URL');

            if ($databaseUrl) {
                $parts = parse_url($databaseUrl);
                if (!$parts) {
                    throw new Exception("URL de base de données invalide");
                }
                $host = $parts['host'] ?? 'localhost';
                $port = $parts['port'] ?? 5432;
                $dbname = ltrim($parts['path'] ?? '', '/');
                $user = $parts['user'] ?? 'postgres';
                $pass = $parts['pass'] ?? '';
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
                $this->pdo = new PDO($dsn, $user, $pass);
            } else {
                // Fallback : utiliser les variables du .env
                $host = Env::get('DB_HOST', 'localhost');
                $port = Env::get('DB_PORT', '5432');
                $dbname = Env::get('DB_DATABASE', 'heliumdb');
                $user = Env::get('DB_USERNAME', 'postgres');
                $pass = Env::get('DB_PASSWORD', '');
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
                $this->pdo = new PDO($dsn, $user, $pass);
            }

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->pdo->exec("SET search_path TO public");

        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Database Error: " . $e->getMessage());
            die("Erreur de configuration : " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }

    public function query($sql) {
        return $this->pdo->query($sql);
    }

    public function exec($sql) {
        return $this->pdo->exec($sql);
    }

    private function __clone() {}

    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }

    public function getTables() {
        $sql = "SELECT table_name FROM information_schema.tables 
                WHERE table_schema = 'public' 
                ORDER BY table_name";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }
}
