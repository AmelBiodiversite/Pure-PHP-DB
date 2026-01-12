<?php
namespace Core;

use PDO;
/**
 * MARKETFLOW PRO - MODÈLE DE BASE (POSTGRESQL)
 * Fichier : core/Model.php
 * Adapté pour PostgreSQL sur Replit
 */

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Récupérer tous les enregistrements
     */
    public function all($orderBy = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer par ID
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Créer un enregistrement
     * POSTGRESQL : utilise RETURNING pour récupérer l'ID
     */
    public function create($data) {
        $fields = array_keys($data);
        $values = array_values($data);

        $fieldList = implode(', ', $fields);
        $placeholders = ':' . implode(', :', $fields);

        // PostgreSQL : RETURNING pour obtenir l'ID inséré
        $sql = "INSERT INTO {$this->table} ({$fieldList}) 
                VALUES ({$placeholders}) 
                RETURNING {$this->primaryKey}";

        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result[$this->primaryKey] ?? null;
        }

        return false;
    }

    /**
     * Mettre à jour
     */
    public function update($id, $data) {
        $setParts = [];
        foreach (array_keys($data) as $field) {
            $setParts[] = "{$field} = :{$field}";
        }
        $setClause = implode(', ', $setParts);

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id";

        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    /**
     * Supprimer
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Compter
     */
    public function count($where = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";

        if (!empty($where)) {
            $conditions = [];
            foreach (array_keys($where) as $field) {
                $conditions[] = "{$field} = :{$field}";
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($where);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] ?? 0;
    }

    /**
     * Trouver avec une condition WHERE
     */
    public function where($conditions, $orderBy = null, $limit = null) {
        $sql = "SELECT * FROM {$this->table} WHERE ";

        $whereParts = [];
        foreach (array_keys($conditions) as $field) {
            $whereParts[] = "{$field} = :{$field}";
        }
        $sql .= implode(' AND ', $whereParts);

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($conditions);

        if ($limit === 1) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Requête personnalisée
     */
    protected function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Exécuter une requête et retourner un résultat unique
     */
    protected function queryOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Exécuter une requête et retourner tous les résultats
     */
    protected function queryAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifier si un enregistrement existe
     */
    public function exists($conditions) {
        return $this->count($conditions) > 0;
    }

    /**
     * Obtenir le premier enregistrement
     */
    public function first($orderBy = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        $sql .= " LIMIT 1";

        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir le dernier enregistrement
     */
    public function last($orderBy = null) {
        $orderBy = $orderBy ?? $this->primaryKey;
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} DESC LIMIT 1";

        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Paginer les résultats
     */
    public function paginate($page = 1, $perPage = 10, $where = [], $orderBy = null) {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$this->table}";

        if (!empty($where)) {
            $conditions = [];
            foreach (array_keys($where) as $field) {
                $conditions[] = "{$field} = :{$field}";
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($where as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $this->count($where),
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($this->count($where) / $perPage)
        ];
    }
}