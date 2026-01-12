<?php
namespace App\Models;

use Core\Model;
use PDO;


/**
 * MARKETFLOW PRO - PRODUCT MODEL (POSTGRESQL)
 * Fichier : app/models/Product.php
 */

class Product extends Model {
    protected $table = 'products';
    protected $primaryKey = 'id';

    /**
     * Créer un nouveau produit
     */
    public function createProduct($data) {
        // Générer slug unique
        if (isset($data['title'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
        }

        // PostgreSQL : RETURNING id
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $fieldList = implode(', ', $fields);

        $sql = "INSERT INTO products ({$fieldList}) 
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
     * Obtenir un produit par ID avec infos vendeur
     */
    public function getProductWithSeller($id) {
        $sql = "SELECT p.*, 
                u.username as seller_username,
                u.full_name as seller_name,
                u.shop_name as seller_shop_name,
                u.shop_slug as seller_shop_slug,
                u.rating_average as seller_rating,
                c.name as category_name,
                c.slug as category_slug
                FROM products p
                LEFT JOIN users u ON p.seller_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir un produit par slug
     */
    public function findBySlug($slug) {
        $sql = "SELECT p.*, 
                u.username as seller_username,
                u.full_name as seller_name,
                u.shop_name as seller_shop_name,
                u.shop_slug as seller_shop_slug,
                u.rating_average as seller_rating,
                c.name as category_name,
                c.slug as category_slug
                FROM products p
                LEFT JOIN users u ON p.seller_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.slug = :slug
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir tous les produits avec filtres
     */
    public function getProducts($filters = []) {
        $sql = "SELECT p.*, 
                u.username as seller_username,
                u.shop_name as seller_shop_name,
                c.name as category_name
                FROM products p
                LEFT JOIN users u ON p.seller_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE 1=1";

        $params = [];

        // Statut
        if (isset($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        } else {
            // Par défaut, seulement les produits approuvés
            $sql .= " AND p.status = 'approved'";
        }

        // Vendeur
        if (isset($filters['seller_id'])) {
            $sql .= " AND p.seller_id = :seller_id";
            $params['seller_id'] = $filters['seller_id'];
        }

        // Catégorie
        if (isset($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        // Recherche (PostgreSQL : ILIKE pour insensible à la casse)
        if (isset($filters['search'])) {
            $sql .= " AND (p.title ILIKE :search OR p.description ILIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        // Prix min
        if (isset($filters['min_price'])) {
            $sql .= " AND p.price >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }

        // Prix max
        if (isset($filters['max_price'])) {
            $sql .= " AND p.price <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }

        // Featured
        if (isset($filters['is_featured'])) {
            $sql .= " AND p.is_featured = :is_featured";
            $params['is_featured'] = $filters['is_featured'];
        }

        // Tags
        if (isset($filters['tag'])) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM product_tags pt
                JOIN tags t ON pt.tag_id = t.id
                WHERE pt.product_id = p.id AND t.slug = :tag
            )";
            $params['tag'] = $filters['tag'];
        }

        // Tri
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDir = $filters['order_dir'] ?? 'DESC';

        $allowedOrders = ['created_at', 'price', 'sales', 'rating_average', 'title'];
        if (!in_array($orderBy, $allowedOrders)) {
            $orderBy = 'created_at';
        }

        $sql .= " ORDER BY p.{$orderBy} {$orderDir}";

        // Pagination
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

    /**
     * Compter les produits avec filtres
     */
    public function countProducts($filters = []) {
        $sql = "SELECT COUNT(*) as count FROM products p WHERE 1=1";
        $params = [];

        if (isset($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        } else {
            $sql .= " AND p.status = 'approved'";
        }

        if (isset($filters['seller_id'])) {
            $sql .= " AND p.seller_id = :seller_id";
            $params['seller_id'] = $filters['seller_id'];
        }

        if (isset($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        if (isset($filters['search'])) {
            $sql .= " AND (p.title ILIKE :search OR p.description ILIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] ?? 0;
    }

    /**
     * Incrémenter les vues
     */
    public function incrementViews($productId) {
        $sql = "UPDATE products SET views = views + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $productId]);
    }

    /**
     * Incrémenter les ventes
     */
    public function incrementSales($productId, $quantity = 1, $amount = 0) {
        $sql = "UPDATE products 
                SET sales = sales + :quantity,
                    downloads = downloads + :quantity,
                    revenue = revenue + :amount
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'quantity' => $quantity,
            'amount' => $amount,
            'id' => $productId
        ]);
    }

    /**
     * Incrémenter les téléchargements
     */
    public function incrementDownloads($productId) {
        $sql = "UPDATE products SET downloads = downloads + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $productId]);
    }

    /**
     * Approuver un produit
     */
    public function approve($productId) {
        return $this->update($productId, [
            'status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Rejeter un produit
     */
    public function reject($productId, $reason) {
        return $this->update($productId, [
            'status' => 'rejected',
            'rejection_reason' => $reason
        ]);
    }

    /**
     * Obtenir les tags d'un produit
     */
    public function getProductTags($productId) {
        $sql = "SELECT t.* 
                FROM tags t
                JOIN product_tags pt ON t.id = pt.tag_id
                WHERE pt.product_id = :product_id
                ORDER BY t.name";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ajouter des tags à un produit
     */
    public function addTags($productId, $tags) {
        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            if (empty($tagName)) continue;

            // Obtenir ou créer le tag
            $tagId = $this->getOrCreateTag($tagName);

            // Associer au produit (PostgreSQL : ON CONFLICT DO NOTHING)
            $sql = "INSERT INTO product_tags (product_id, tag_id) 
                    VALUES (:product_id, :tag_id)
                    ON CONFLICT (product_id, tag_id) DO NOTHING";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'product_id' => $productId,
                'tag_id' => $tagId
            ]);
        }
    }

    /**
     * Obtenir ou créer un tag
     */
    private function getOrCreateTag($name) {
        $slug = $this->slugify($name);

        // Vérifier si existe
        $sql = "SELECT id FROM tags WHERE slug = :slug LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        $tag = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tag) {
            // Incrémenter usage_count
            $sql = "UPDATE tags SET usage_count = usage_count + 1 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $tag['id']]);

            return $tag['id'];
        }

        // Créer nouveau tag (PostgreSQL : RETURNING)
        $sql = "INSERT INTO tags (name, slug, usage_count) 
                VALUES (:name, :slug, 1) 
                RETURNING id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['name' => $name, 'slug' => $slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['id'];
    }

    /**
     * Obtenir la galerie d'un produit
     */
    public function getGallery($productId) {
        $sql = "SELECT * FROM product_gallery 
                WHERE product_id = :product_id 
                ORDER BY display_order, id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ajouter une image à la galerie
     */
    public function addToGallery($productId, $imageUrl, $displayOrder = 0) {
        $sql = "INSERT INTO product_gallery (product_id, image_url, display_order) 
                VALUES (:product_id, :image_url, :display_order)
                RETURNING id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'product_id' => $productId,
            'image_url' => $imageUrl,
            'display_order' => $displayOrder
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }

    /**
     * Obtenir les produits populaires
     */
    public function getPopularProducts($limit = 10) {
        $sql = "SELECT p.*, u.shop_name as seller_shop_name 
                FROM products p
                LEFT JOIN users u ON p.seller_id = u.id
                WHERE p.status = 'approved'
                ORDER BY p.sales DESC, p.rating_average DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir les produits récents
     */
    public function getRecentProducts($limit = 10) {
        return $this->getProducts([
            'status' => 'approved',
            'order_by' => 'created_at',
            'order_dir' => 'DESC',
            'limit' => $limit
        ]);
    }

    /**
     * Générer un slug unique
     */
    private function generateUniqueSlug($title) {
        $slug = $this->slugify($title);
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
        $sql = "SELECT COUNT(*) as count FROM products WHERE slug = :slug";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Slugifier un texte
     */
    private function slugify($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9-]/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }
}