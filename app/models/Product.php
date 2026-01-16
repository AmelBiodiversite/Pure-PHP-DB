<?php
namespace App\Models;

use Core\Model;
use PDO;

/**
 * Modèle Product
 * Responsable de :
 * - La création et gestion des produits
 * - L’upload des fichiers associés
 * - La gestion des tags
 * - Les requêtes catalogue (listing, filtres, pagination)
 */
class Product extends Model {

    protected $table = 'products';
    protected $primaryKey = 'id';

    /**
     * Crée un nouveau produit pour un vendeur
     * Gère :
     * - Génération d’un slug unique
     * - Upload image miniature et fichier produit
     * - Insertion en base
     * - Association des tags
     */
    public function createProduct($data, $sellerId, $files) {
        try {
            // Génération d’un slug unique basé sur le titre
            $slug = $this->generateUniqueSlug($data['title']);

            // Données principales du produit
            $productData = [
                'seller_id'       => $sellerId,
                'category_id'     => $data['category_id'],
                'title'           => $data['title'],
                'slug'            => $slug,
                'description'     => $data['description'],
                'price'           => $data['price'],
                'original_price' => $data['original_price'] ?? null,
                'file_type'       => $data['file_type'] ?? 'digital',
                'demo_url'        => $data['demo_url'] ?? null,
                'status'          => 'pending' // soumis à validation admin
            ];

            // Upload de l’image miniature si fournie
            if (!empty($files['thumbnail']['name'])) {
                $productData['thumbnail_url'] = $this->uploadThumbnail($files['thumbnail']);
            }

            // Upload du fichier principal du produit
            if (!empty($files['product_file']['name'])) {
                $productData['file_url']  = $this->uploadProductFile($files['product_file']);
                $productData['file_size'] = $files['product_file']['size'];
            }

            // Construction dynamique de la requête INSERT
            $fields       = array_keys($productData);
            $placeholders = ':' . implode(', :', $fields);

            $sql = "INSERT INTO products (" . implode(', ', $fields) . ")
                    VALUES ($placeholders)
                    RETURNING id";

            $stmt = $this->db->prepare($sql);

            foreach ($productData as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            $stmt->execute();
            $productId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

            // Association des tags si présents
            if (!empty($data['tags'])) {
                $this->attachTags($productId, $data['tags']);
            }

            return [
                'success'    => true,
                'product_id'=> $productId,
                'message'   => 'Produit créé avec succès'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }

    /**
     * Associe une liste de tags texte à un produit.
     * Crée les tags inexistants puis remplit la table pivot product_tags.
     */
    private function attachTags($productId, $tagsString) {
        $tags = explode(',', $tagsString);

        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            if (!$tagName) continue;

            // Recherche du tag existant
            $stmt = $this->db->prepare("SELECT id FROM tags WHERE name = :name");
            $stmt->execute(['name' => $tagName]);
            $tag = $stmt->fetch(PDO::FETCH_ASSOC);

            // Création du tag s’il n’existe pas encore
            if (!$tag) {
                $stmt = $this->db->prepare(
                    "INSERT INTO tags (name, slug) VALUES (:name, :slug) RETURNING id"
                );
                $stmt->execute([
                    'name' => $tagName,
                    'slug' => $this->slugify($tagName)
                ]);
                $tag = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            // Liaison produit ↔ tag (évite doublons via ON CONFLICT)
            $stmt = $this->db->prepare("
                INSERT INTO product_tags (product_id, tag_id)
                VALUES (:product_id, :tag_id)
                ON CONFLICT DO NOTHING
            ");
            $stmt->execute([
                'product_id' => $productId,
                'tag_id'     => $tag['id']
            ]);
        }
    }

    /**
     * Redimensionne une image pour éviter les fichiers trop lourds.
     * Utilisé uniquement pour les miniatures produits.
     */
    private function resizeImage($filepath, $maxWidth, $maxHeight) {
        [$width, $height] = getimagesize($filepath);

        if ($width <= $maxWidth && $height <= $maxHeight) return;

        $ratio      = min($maxWidth / $width, $maxHeight / $height);
        $newWidth  = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);

        $src = imagecreatefromstring(file_get_contents($filepath));
        $dst = imagecreatetruecolor($newWidth, $newHeight);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, 
                           $newWidth, $newHeight, $width, $height);

        imagejpeg($dst, $filepath, 85);

        imagedestroy($src);
        imagedestroy($dst);
    }

    /**
     * Upload et traitement de la miniature produit.
     * Retourne le chemin public stocké en base.
     */
    private function uploadThumbnail($file) {
        $uploadDir = UPLOAD_DIR . 'products/thumbnails/';
        $filename  = uniqid() . '_' . basename($file['name']);
        $filepath  = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new \Exception('Erreur lors de l\'upload de l\'image');
        }

        $this->resizeImage($filepath, 1200, 800);
        return '/public/uploads/products/thumbnails/' . $filename;
    }

    /**
     * Upload du fichier numérique vendu (zip, pdf, etc.)
     */
    private function uploadProductFile($file) {
        $uploadDir = UPLOAD_DIR . 'products/files/';
        $filename  = uniqid() . '_' . basename($file['name']);
        $filepath  = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new \Exception('Erreur lors de l\'upload du fichier');
        }

        return '/public/uploads/products/files/' . $filename;
    }

    /**
     * Génère un slug unique en base à partir d’un titre.
     */
    private function generateUniqueSlug($title) {
        $slug = $this->slugify($title);
        $base = $slug;
        $i = 1;

        while ($this->slugExists($slug)) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /**
     * Vérifie l’existence d’un slug produit.
     */
    private function slugExists($slug) {
        $stmt = $this->db->prepare("SELECT id FROM products WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        return (bool) $stmt->fetch();
    }

    /**
     * Transforme une chaîne en slug URL-safe.
     */
    private function slugify($text) {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }

    /**
     * Retourne les produits d’un vendeur.
     * Optionnellement filtrés par statut et limités en nombre.
     */
    public function getSellerProducts($sellerId, $status = null, $limit = null) {
        $sql = "SELECT * FROM products WHERE seller_id = :seller_id";

        if ($status) $sql .= " AND status = :status";

        $sql .= " ORDER BY created_at DESC";

        if ($limit) $sql .= " LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':seller_id', $sellerId, PDO::PARAM_INT);

        if ($status) $stmt->bindValue(':status', $status);
        if ($limit)  $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère un produit par ID avec informations vendeur et catégorie.
     */
    public function getProductWithSeller($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   u.username as seller_username,
                   u.full_name as seller_name,
                   u.rating_average as seller_rating,
                   c.name as category_name,
                   c.slug as category_slug
            FROM products p
            LEFT JOIN users u ON p.seller_id = u.id
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id
            LIMIT 1
        ");

        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un produit par slug (URL publique).
     */
    public function getProductBySlug($slug) {
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   u.username as seller_username,
                   u.full_name as seller_name,
                   u.rating_average as seller_rating,
                   c.name as category_name
            FROM products p
            LEFT JOIN users u ON p.seller_id = u.id
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.slug = :slug
            LIMIT 1
        ");

        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Catalogue produits avec filtres + pagination.
     */
    public function getProducts($filters = [], $page = 1, $perPage = 24) {
        $sql = "SELECT p.*, u.username as seller_name, c.name as category_name
                FROM products p
                LEFT JOIN users u ON p.seller_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'approved'";

        $params = [];

        // Application des filtres dynamiques
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.title ILIKE :search OR p.description ILIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (isset($filters['min_price'])) {
            $sql .= " AND p.price >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }

        if (isset($filters['max_price'])) {
            $sql .= " AND p.price <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }

        // Gestion du tri
        match ($filters['sort'] ?? 'newest') {
            'price_asc'  => $sql .= " ORDER BY p.price ASC",
            'price_desc'=> $sql .= " ORDER BY p.price DESC",
            'popular'   => $sql .= " ORDER BY p.sales DESC",
            default     => $sql .= " ORDER BY p.created_at DESC"
        };

        // Pagination
        $offset = ($page - 1) * $perPage;
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return [
            'products'     => $stmt->fetchAll(),
            'total'        => $this->countFilteredProducts($params),
            'page'         => $page,
            'total_pages'  => ceil($this->countFilteredProducts($params) / $perPage)
        ];
    }

    /**
     * Compte les produits pour la pagination.
     */
    private function countFilteredProducts($params) {
        $sql = "SELECT COUNT(*) FROM products p WHERE p.status = 'approved'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Mise à jour d’un produit par son vendeur.
     */
    public function updateProduct($id, $sellerId, $data, $files) {
        // Vérifie la propriété du produit
        $stmt = $this->db->prepare("
            SELECT id FROM products 
            WHERE id = :id AND seller_id = :seller_id
        ");
        $stmt->execute(['id' => $id, 'seller_id' => $sellerId]);

        if (!$stmt->fetch()) {
            return ['success' => false, 'error' => 'Produit introuvable'];
        }

        // Mise à jour des champs principaux
        $this->update($id, [
            'title'          => $data['title'],
            'description'    => $data['description'],
            'category_id'    => $data['category_id'],
            'price'          => $data['price'],
            'original_price'=> $data['original_price'] ?? null,
            'demo_url'       => $data['demo_url'] ?? null
        ]);

        // Remplacement des tags existants
        if (!empty($data['tags'])) {
            $stmt = $this->db->prepare("DELETE FROM product_tags WHERE product_id = :id");
            $stmt->execute(['id' => $id]);
            $this->attachTags($id, $data['tags']);
        }

        return ['success' => true, 'message' => 'Produit mis à jour'];
    }

    /**
     * Suppression d’un produit par son vendeur.
     */
    public function deleteProduct($id, $sellerId) {
        $stmt = $this->db->prepare("
            DELETE FROM products 
            WHERE id = :id AND seller_id = :seller_id
        ");

        if ($stmt->execute(['id' => $id, 'seller_id' => $sellerId])) {
            return ['success' => true, 'message' => 'Produit supprimé'];
        }

        return ['success' => false, 'error' => 'Erreur lors de la suppression'];
    }

    /**
     * Produits mis en avant sur la page d’accueil.
     */
    public function getPopular($limit = 4) {
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   c.name as category_name,
                   u.username as shop_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN users u ON p.seller_id = u.id
            WHERE p.status IN ('approved', 'active')
            ORDER BY p.rating_average DESC, p.created_at DESC
            LIMIT :limit
        ");

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
