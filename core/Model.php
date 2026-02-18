<?php
namespace Core;

use PDO;

/**
 * MARKETFLOW PRO - MODÈLE DE BASE (POSTGRESQL)
 * Fichier : core/Model.php
 */

abstract class Model {

    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Trouver par ID
     * 
     * @param int $id
     * @return array|false
     */
    public function find(int $id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Trouver tous
     * 
     * @param array $conditions
     * @param string|null $order
     * @param int|null $limit
     * @return array
     */
    public function findAll(array $conditions = [], ?string $order = null, ?int $limit = null): array {
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = :$key";
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        if ($order) {
            $sql .= " ORDER BY $order";
        }

        if ($limit) {
            // Enforce maximum limit to prevent DoS attacks
            $maxLimit = 1000;
            $limit = min($limit, $maxLimit);
            $sql .= " LIMIT " . $limit;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($conditions);
        return $stmt->fetchAll();
    }

    /**
     * Créer
     * 
     * @param array $data
     * @return int
     */
    public function create(array $data): int {
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return ":$field";
        }, $fields);

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ") 
                RETURNING {$this->primaryKey}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        $result = $stmt->fetch();
        return $result[$this->primaryKey];
    }

    /**
     * Mettre à jour
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool {
        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "$field = :$field";
        }

        $sql = "UPDATE {$this->table} 
                SET " . implode(', ', $fields) . " 
                WHERE {$this->primaryKey} = :id";

        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Supprimer
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Compter
     * 
     * @param array $conditions
     * @return int
     */
    public function count(array $conditions = []): int {
        $sql = "SELECT COUNT(*) FROM {$this->table}";

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = :$key";
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($conditions);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Trouver avec pagination
     * 
     * @param int $page
     * @param int $perPage
     * @param array $conditions
     * @return array
     */
    public function paginate(int $page = 1, int $perPage = 20, array $conditions = []): array {
        // Enforce maximum limits to prevent DoS attacks
        $maxPerPage = 100;
        $maxPage = 10000;
        
        $page = max(1, min($page, $maxPage));
        $perPage = max(1, min($perPage, $maxPerPage));
        
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$this->table}";

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = :$key";
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'total' => $this->count($conditions),
            'page' => $page,
            'per_page' => $perPage
        ];
    }
}