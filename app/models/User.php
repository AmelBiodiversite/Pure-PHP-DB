<?php
namespace App\Models;

use Core\Model;
use PDO;
use Exception;

/* * MARKETFLOW PRO - USER MODEL (POSTGRESQL)
 * Fichier : app/models/User.php
 */

class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    /**
     * Créer un nouvel utilisateur
     */
    public function createUser($data) {
        // Hash du mot de passe
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }
        
        // Générer shop_slug si vendeur
        if (isset($data['role']) && $data['role'] === 'seller' && isset($data['shop_name'])) {
            $data['shop_slug'] = $this->generateUniqueSlug($data['shop_name']);
        }
        
        // PostgreSQL : RETURNING id
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $fieldList = implode(', ', $fields);
        
        $sql = "INSERT INTO users ({$fieldList}) 
                VALUES ({$placeholders}) 
                RETURNING id";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['id'];
        }
        
        return false;
    }
    
    /**
     * Authentifier un utilisateur
     */
    public function authenticate($emailOrUsername, $password) {
        // PostgreSQL : ILIKE pour recherche insensible à la casse
        $sql = "SELECT * FROM users 
                WHERE (email ILIKE :login OR username ILIKE :login) 
                AND is_active = TRUE 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['login' => $emailOrUsername]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Mettre à jour last_login
            $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
            return $user;
        }
        
        return false;
    }

    /**
     * Alias pour authenticate (compatibilité)
     */
    public function login($emailOrUsername, $password) {
        return $this->authenticate($emailOrUsername, $password);
    }
    
    /**
     * Vérifier si email existe
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email ILIKE :email";
        $params = ['email' => $email];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }
    
    /**
     * Vérifier si username existe
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username ILIKE :username";
        $params = ['username' => $username];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }
    
    /**
     * Obtenir un utilisateur par email
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email ILIKE :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtenir un utilisateur par username
     */
    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username ILIKE :username LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtenir un utilisateur par shop_slug
     */
    public function findByShopSlug($slug) {
        $sql = "SELECT * FROM users WHERE shop_slug = :slug AND role = 'seller' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Changer le mot de passe
     */
    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->update($userId, ['password' => $hashedPassword]);
    }
    
    /**
     * Mettre à jour le profil
     */
    public function updateProfile($userId, $data) {
        // Ne jamais permettre de changer le rôle via cette méthode
        unset($data['role']);
        unset($data['password']);
        
        return $this->update($userId, $data);
    }
    
    /**
     * Mettre à jour les stats vendeur
     */
    public function updateSellerStats($userId) {
        // Utiliser une transaction PostgreSQL
        $this->db->beginTransaction();
        
        try {
            // Total ventes
            $sql = "UPDATE users SET 
                    total_sales = (
                        SELECT COALESCE(SUM(oi.quantity), 0)
                        FROM order_items oi
                        JOIN orders o ON oi.order_id = o.id
                        WHERE oi.seller_id = :user_id AND o.status = 'completed'
                    ),
                    total_earnings = (
                        SELECT COALESCE(SUM(oi.seller_amount), 0)
                        FROM order_items oi
                        JOIN orders o ON oi.order_id = o.id
                        WHERE oi.seller_id = :user_id AND o.status = 'completed'
                    ),
                    total_products = (
                        SELECT COUNT(*)
                        FROM products
                        WHERE seller_id = :user_id AND status = 'approved'
                    )
                    WHERE id = :user_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    /**
     * Obtenir les vendeurs populaires
     */
    public function getPopularSellers($limit = 10) {
        $sql = "SELECT u.*, 
                COUNT(DISTINCT p.id) as product_count,
                COUNT(DISTINCT r.id) as review_count
                FROM users u
                LEFT JOIN products p ON u.id = p.seller_id AND p.status = 'approved'
                LEFT JOIN reviews r ON p.id = r.product_id AND r.is_approved = TRUE
                WHERE u.role = 'seller' AND u.is_active = TRUE
                GROUP BY u.id
                ORDER BY u.total_sales DESC, u.rating_average DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtenir les statistiques vendeur
     */
    public function getSellerStats($userId) {
        $sql = "SELECT 
                COUNT(DISTINCT p.id) as total_products,
                COALESCE(u.rating_average, 0) as rating_average,
                COALESCE(u.rating_count, 0) as rating_count,
                COALESCE(SUM(oi.seller_amount), 0) as total_sales,
                COUNT(DISTINCT o.id) as total_orders
                FROM users u
                LEFT JOIN products p ON u.id = p.seller_id AND p.status = 'approved'
                LEFT JOIN order_items oi ON u.id = oi.seller_id
                LEFT JOIN orders o ON oi.order_id = o.id AND o.payment_status = 'completed'
                WHERE u.id = :user_id
                GROUP BY u.id, u.rating_average, u.rating_count";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Suspendre un utilisateur
     */
    public function suspend($userId) {
        return $this->update($userId, ['is_active' => false]);
    }
    
    /**
     * Activer un utilisateur
     */
    public function activate($userId) {
        return $this->update($userId, ['is_active' => true]);
    }
    
    /**
     * Logger une activité
     */
    public function logActivity($userId, $action, $entityType = null, $entityId = null, $metadata = null) {
        $sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, ip_address, user_agent, metadata) 
                VALUES (:user_id, :action, :entity_type, :entity_id, :ip_address, :user_agent, :metadata)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'metadata' => $metadata ? json_encode($metadata) : null
        ]);
    }
    
    /**
     * Générer un slug unique
     */
    private function generateUniqueSlug($name) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Vérifier si un slug existe
     */
    private function slugExists($slug) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE shop_slug = :slug";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    /**
     * Obtenir tous les utilisateurs avec filtres
     */
    public function getAllUsers($filters = []) {
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];
        
        if (isset($filters['role'])) {
            $sql .= " AND role = :role";
            $params['role'] = $filters['role'];
        }
        
        if (isset($filters['is_active'])) {
            $sql .= " AND is_active = :is_active";
            $params['is_active'] = $filters['is_active'];
        }
        
        if (isset($filters['search'])) {
            $sql .= " AND (full_name ILIKE :search OR email ILIKE :search OR username ILIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if (isset($filters['limit'])) {
            $sql .= " LIMIT :limit";
        }
        
        if (isset($filters['offset'])) {
            $sql .= " OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        if (isset($filters['limit'])) {
            $stmt->bindValue(':limit', $filters['limit'], PDO::PARAM_INT);
        }
        
        if (isset($filters['offset'])) {
            $stmt->bindValue(':offset', $filters['offset'], PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}