<?php
namespace Core;
use PDO;
use PDOException;
use Exception;

/**
 * Classe Database - Singleton pour la gestion de la connexion PostgreSQL
 * Compatible avec Railway (SSL requis)
 */
class Database {
    private static $instance = null;
    private $pdo;

    /**
     * Constructeur privé - établit la connexion à la base de données
     * Charge automatiquement les variables depuis .env ou variables d'environnement
     */
    private function __construct() {
     
  try {
            // Charger le fichier .env
            Env::load();

            // Essayer d'abord DATABASE_URL (Railway/Replit)
            $databaseUrl = Env::get('DATABASE_URL');

            if ($databaseUrl) {
                // Parser l'URL de connexion (format: postgresql://user:pass@host:port/dbname)
                $parts = parse_url($databaseUrl);
                if (!$parts) {
                    throw new Exception("URL de base de données invalide");
                }
                
                // Extraire les composants de l'URL
                $host = $parts['host'] ?? 'localhost';
                $port = $parts['port'] ?? 5432;
                $dbname = ltrim($parts['path'] ?? '', '/');
                $user = $parts['user'] ?? 'postgres';
                $pass = $parts['pass'] ?? '';
                
                // Construction du DSN avec SSL requis pour Railway
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
                
                // Options PDO définies dès la création de la connexion
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Lancer des exceptions en cas d'erreur
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Récupérer les résultats en tableau associatif
                    PDO::ATTR_EMULATE_PREPARES => false,  // Utiliser les vraies requêtes préparées
                    PDO::ATTR_TIMEOUT => 30  // Timeout de 30 secondes pour éviter les blocages
                ];
                
                // Créer la connexion PDO avec les options
                $this->pdo = new PDO($dsn, $user, $pass, $options);
                
            } else {
                // Fallback : utiliser les variables individuelles du .env
                $host = Env::get('DB_HOST', 'localhost');
                $port = Env::get('DB_PORT', '5432');
                $dbname = Env::get('DB_DATABASE', 'heliumdb');
                $user = Env::get('DB_USERNAME', 'postgres');
                $pass = Env::get('DB_PASSWORD', '');
                
                // Construction du DSN avec SSL pour connexion sécurisée
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
                
                // Options PDO définies dès la création de la connexion
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 30
                ];
                
                // Créer la connexion PDO avec les options
                $this->pdo = new PDO($dsn, $user, $pass, $options);
            }

            // Configuration supplémentaire : définir le schéma par défaut
            $this->pdo->exec("SET search_path TO public");

        } catch (PDOException $e) {
            // Logger l'erreur dans les logs PHP
            error_log("Database Connection Error: " . $e->getMessage());
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        } catch (Exception $e) {
            // Logger les autres erreurs
            error_log("Database Error: " . $e->getMessage());
            die("Erreur de configuration : " . $e->getMessage());
        }
    }

    /**
     * Retourne l'instance unique de Database (pattern Singleton)
     * @return Database Instance unique de la classe
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retourne l'objet PDO pour des opérations avancées
     * @return PDO Instance PDO
     */
    public function getPdo() {
        return $this->pdo;
    }

    /**
     * Prépare une requête SQL
     * @param string $sql Requête SQL avec des placeholders
     * @return PDOStatement Statement préparé
     */
    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }

    /**
     * Exécute une requête SQL et retourne les résultats
     * @param string $sql Requête SQL
     * @return PDOStatement Résultats de la requête
     */
    public function query($sql) {
        return $this->pdo->query($sql);
    }

    /**
     * Exécute une requête SQL sans retourner de résultats
     * @param string $sql Requête SQL (INSERT, UPDATE, DELETE, etc.)
     * @return int Nombre de lignes affectées
     */
    public function exec($sql) {
        return $this->pdo->exec($sql);
    }

    /**
     * Empêche le clonage de l'instance (pattern Singleton)
     */
    private function __clone() {}

    /**
     * Empêche la désérialisation de l'instance (pattern Singleton)
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }

    /**
     * Récupère la liste de toutes les tables du schéma public
     * @return array Liste des noms de tables
     */
    public function getTables() {
        $sql = "SELECT table_name FROM information_schema.tables 
                WHERE table_schema = 'public' 
                ORDER BY table_name";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Démarre une transaction
     * @return bool True si la transaction a démarré
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    /**
     * Valide une transaction
     * @return bool True si la transaction a été validée
     */
    public function commit() {
        return $this->pdo->commit();
    }

    /**
     * Annule une transaction
     * @return bool True si la transaction a été annulée
     */
    public function rollBack() {
        return $this->pdo->rollBack();
    }
}
