<?php
/**
 * MARKETFLOW PRO - PRODUCT CONTROLLER (PUBLIC)
 * Affichage catalogue et produits pour visiteurs/acheteurs
 * Fichier : app/controllers/ProductController.php
 */

namespace App\Controllers;

use Core\Controller;
use App\Models\Product;

class ProductController extends Controller {
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
    }

    /**
     * Page catalogue de produits
     */
    public function index() {
        // Récupérer les filtres depuis l'URL
        $filters = [
            'category_id' => $_GET['category'] ?? null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'search' => $_GET['q'] ?? null,
            'tag' => $_GET['tag'] ?? null,
            'sort' => $_GET['sort'] ?? 'newest'
        ];

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 24;

        // Récupérer les produits
        $result = $this->productModel->getProducts($filters, $page, $perPage);

        // Récupérer les catégories pour le filtre
        $stmt = $this->db->query("
            SELECT c.*, COUNT(p.id) as product_count
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id AND p.status = 'approved' AND p.is_active = 1
            WHERE c.is_active = 1
            GROUP BY c.id
            ORDER BY c.display_order, c.name
        ");
        $categories = $stmt->fetchAll();

        // Tags populaires
        $stmt = $this->db->query("
            SELECT t.id, t.name, t.slug, COUNT(pt.product_id) as product_count
            FROM tags t
            JOIN product_tags pt ON t.id = pt.tag_id
            JOIN products p ON pt.product_id = p.id
            WHERE p.status = 'approved' AND p.is_active = 1
            GROUP BY t.id
            ORDER BY product_count DESC
            LIMIT 20
        ");
        $popularTags = $stmt->fetchAll();

        // Range de prix pour le slider
        $stmt = $this->db->query("
            SELECT MIN(price) as min_price, MAX(price) as max_price
            FROM products
            WHERE status = 'approved' AND is_active = 1
        ");
        $priceRange = $stmt->fetch();

        $this->view('products/index', [
            'title' => 'Catalogue de Produits',
            'products' => $result['products'],
            'pagination' => [
                'current' => $result['page'],
                'total' => $result['total_pages'],
                'total_items' => $result['total']
            ],
            'categories' => $categories,
            'popular_tags' => $popularTags,
            'price_range' => $priceRange,
            'active_filters' => $filters
        ]);
    }

    /**
     * Page détail d'un produit
     */
    public function show($slug) {
        $product = $this->productModel->getProductBySlug($slug);

        if (!$product) {
            redirectWithMessage('/', 'Produit introuvable', 'error');
            return;
        }

        // Vérifier si le produit est approuvé (sauf pour le vendeur)
        if ($product['status'] !== 'approved') {
            $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $product['seller_id'];
            $isAdmin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
            
            if (!$isOwner && !$isAdmin) {
                redirectWithMessage('/', 'Produit non disponible', 'error');
                return;
            }
        }

        // Récupérer les avis
        $stmt = $this->db->prepare("
            SELECT r.*, u.username, u.avatar,
                   DATE_FORMAT(r.created_at, '%d/%m/%Y') as review_date
            FROM reviews r
            JOIN users u ON r.buyer_id = u.id
            WHERE r.product_id = ? AND r.is_approved = 1
            ORDER BY r.created_at DESC
            LIMIT 10
        ");
        $stmt->execute([$product['id']]);
        $reviews = $stmt->fetchAll();

        // Produits similaires (même catégorie)
        $stmt = $this->db->prepare("
            SELECT p.*, u.username as seller_name, sp.shop_name
            FROM products p
            JOIN users u ON p.seller_id = u.id
            LEFT JOIN seller_profiles sp ON u.id = sp.user_id
            WHERE p.category_id = ? 
              AND p.id != ? 
              AND p.status = 'approved' 
              AND p.is_active = 1
            ORDER BY p.sales_count DESC
            LIMIT 6
        ");
        $stmt->execute([$product['category_id'], $product['id']]);
        $relatedProducts = $stmt->fetchAll();

        // Autres produits du vendeur
        $stmt = $this->db->prepare("
            SELECT p.*
            FROM products p
            WHERE p.seller_id = ? 
              AND p.id != ?
              AND p.status = 'approved' 
              AND p.is_active = 1
            ORDER BY p.created_at DESC
            LIMIT 4
        ");
        $stmt->execute([$product['seller_id'], $product['id']]);
        $sellerProducts = $stmt->fetchAll();

        // Vérifier si dans la wishlist
        $inWishlist = false;
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->db->prepare("
                SELECT id FROM wishlists 
                WHERE user_id = ? AND product_id = ?
            ");
            $stmt->execute([$_SESSION['user_id'], $product['id']]);
            $inWishlist = $stmt->fetch() !== false;
        }

        $this->view('products/show', [
            'title' => $product['title'],
            'product' => $product,
            'reviews' => $reviews,
            'related_products' => $relatedProducts,
            'seller_products' => $sellerProducts,
            'in_wishlist' => $inWishlist,
            'csrf_token' => generateCsrfToken()
        ]);
    }

    /**
     * Recherche de produits
     */
    public function search() {
        $query = $_GET['q'] ?? '';
        
        if (empty($query)) {
            $this->redirect('/products');
        }

        $filters = [
            'search' => $query,
            'sort' => $_GET['sort'] ?? 'relevance'
        ];

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $result = $this->productModel->getProducts($filters, $page, 24);

        // Suggestions de recherche basées sur les tags
        $stmt = $this->db->prepare("
            SELECT DISTINCT t.name
            FROM tags t
            WHERE t.name LIKE ?
            LIMIT 5
        ");
        $stmt->execute(['%' . $query . '%']);
        $suggestions = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $this->view('products/search', [
            'title' => 'Recherche : ' . $query,
            'query' => $query,
            'products' => $result['products'],
            'pagination' => [
                'current' => $result['page'],
                'total' => $result['total_pages'],
                'total_items' => $result['total']
            ],
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Produits par catégorie
     */
    public function category($slug) {
        // Récupérer la catégorie
        $stmt = $this->db->prepare("
            SELECT * FROM categories WHERE slug = ? AND is_active = 1
        ");
        $stmt->execute([$slug]);
        $category = $stmt->fetch();

        if (!$category) {
            redirectWithMessage('/', 'Catégorie introuvable', 'error');
            return;
        }

        $filters = [
            'category_id' => $category['id'],
            'sort' => $_GET['sort'] ?? 'newest'
        ];

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $result = $this->productModel->getProducts($filters, $page, 24);

        // Sous-catégories si existantes
        $stmt = $this->db->prepare("
            SELECT c.*, COUNT(p.id) as product_count
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id AND p.status = 'approved'
            WHERE c.parent_id = ? AND c.is_active = 1
            GROUP BY c.id
            ORDER BY c.display_order
        ");
        $stmt->execute([$category['id']]);
        $subcategories = $stmt->fetchAll();

        $this->view('products/category', [
            'title' => $category['name'],
            'category' => $category,
            'subcategories' => $subcategories,
            'products' => $result['products'],
            'pagination' => [
                'current' => $result['page'],
                'total' => $result['total_pages'],
                'total_items' => $result['total']
            ]
        ]);
    }

    /**
     * Ajouter à la wishlist (AJAX)
     */
    public function toggleWishlist() {
        if (!$this->isLoggedIn()) {
            $this->json(['success' => false, 'error' => 'Connexion requise'], 401);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'error' => 'Méthode non autorisée'], 405);
        }

        $productId = $_POST['product_id'] ?? null;
        
        if (!$productId) {
            $this->json(['success' => false, 'error' => 'Produit invalide'], 400);
        }

        $userId = $_SESSION['user_id'];

        // Vérifier si déjà dans la wishlist
        $stmt = $this->db->prepare("
            SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?
        ");
        $stmt->execute([$userId, $productId]);
        $exists = $stmt->fetch();

        if ($exists) {
            // Retirer de la wishlist
            $stmt = $this->db->prepare("
                DELETE FROM wishlists WHERE user_id = ? AND product_id = ?
            ");
            $stmt->execute([$userId, $productId]);
            
            $this->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'Retiré des favoris'
            ]);
        } else {
            // Ajouter à la wishlist
            $stmt = $this->db->prepare("
                INSERT INTO wishlists (user_id, product_id, created_at)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$userId, $productId, date('Y-m-d H:i:s')]);
            
            $this->json([
                'success' => true,
                'action' => 'added',
                'message' => 'Ajouté aux favoris'
            ]);
        }
    }

    /**
     * Soumettre un avis (AJAX)
     */
    public function submitReview() {
        if (!$this->isLoggedIn()) {
            $this->json(['success' => false, 'error' => 'Connexion requise'], 401);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'error' => 'Méthode non autorisée'], 405);
        }

        $productId = $_POST['product_id'] ?? null;
        $orderItemId = $_POST['order_item_id'] ?? null;
        $rating = $_POST['rating'] ?? null;
        $comment = $_POST['comment'] ?? '';

        // Validation
        if (!$productId || !$orderItemId || !$rating) {
            $this->json(['success' => false, 'error' => 'Données manquantes'], 400);
        }

        if ($rating < 1 || $rating > 5) {
            $this->json(['success' => false, 'error' => 'Note invalide'], 400);
        }

        $userId = $_SESSION['user_id'];

        // Vérifier que l'utilisateur a acheté le produit
        $stmt = $this->db->prepare("
            SELECT oi.seller_id 
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE oi.id = ? 
              AND oi.product_id = ? 
              AND o.buyer_id = ?
              AND o.payment_status = 'completed'
        ");
        $stmt->execute([$orderItemId, $productId, $userId]);
        $orderItem = $stmt->fetch();

        if (!$orderItem) {
            $this->json(['success' => false, 'error' => 'Achat non vérifié'], 403);
        }

        // Vérifier qu'il n'a pas déjà laissé un avis
        $stmt = $this->db->prepare("
            SELECT id FROM reviews 
            WHERE order_item_id = ? AND buyer_id = ?
        ");
        $stmt->execute([$orderItemId, $userId]);
        
        if ($stmt->fetch()) {
            $this->json(['success' => false, 'error' => 'Avis déjà soumis'], 400);
        }

        // Créer l'avis
        $stmt = $this->db->prepare("
            INSERT INTO reviews (
                product_id, order_item_id, buyer_id, seller_id, 
                rating, comment, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $productId,
            $orderItemId,
            $userId,
            $orderItem['seller_id'],
            $rating,
            $comment,
            date('Y-m-d H:i:s')
        ]);

        // Mettre à jour la note moyenne du produit
        $this->updateProductRating($productId);

        // Mettre à jour la note du vendeur
        $this->updateSellerRating($orderItem['seller_id']);

        $this->json([
            'success' => true,
            'message' => 'Avis publié avec succès'
        ]);
    }

    /**
     * Mettre à jour la note moyenne d'un produit
     */
    private function updateProductRating($productId) {
        $stmt = $this->db->prepare("
            UPDATE products 
            SET rating_average = (
                SELECT AVG(rating) 
                FROM reviews 
                WHERE product_id = ? AND is_approved = 1
            ),
            rating_count = (
                SELECT COUNT(*) 
                FROM reviews 
                WHERE product_id = ? AND is_approved = 1
            )
            WHERE id = ?
        ");
        $stmt->execute([$productId, $productId, $productId]);
    }

    /**
     * Mettre à jour la note moyenne d'un vendeur
     */
    private function updateSellerRating($sellerId) {
        $stmt = $this->db->prepare("
            UPDATE seller_profiles 
            SET rating_average = (
                SELECT AVG(rating) 
                FROM reviews 
                WHERE seller_id = ? AND is_approved = 1
            ),
            rating_count = (
                SELECT COUNT(*) 
                FROM reviews 
                WHERE seller_id = ? AND is_approved = 1
            )
            WHERE user_id = ?
        ");
        $stmt->execute([$sellerId, $sellerId, $sellerId]);
    }

    /**
     * Autocomplete pour la recherche (AJAX)
     */
    public function searchSuggestions() {
        $query = $_GET['q'] ?? '';
        
        if (strlen($query) < 2) {
            $this->json(['suggestions' => []]);
        }

        // Rechercher dans les titres de produits
        $stmt = $this->db->prepare("
            SELECT title, slug, thumbnail, price
            FROM products
            WHERE title LIKE ? 
              AND status = 'approved' 
              AND is_active = 1
            LIMIT 5
        ");
        $stmt->execute(['%' . $query . '%']);
        $products = $stmt->fetchAll();

        // Rechercher dans les tags
        $stmt = $this->db->prepare("
            SELECT name, slug
            FROM tags
            WHERE name LIKE ?
            LIMIT 3
        ");
        $stmt->execute(['%' . $query . '%']);
        $tags = $stmt->fetchAll();

        $this->json([
            'products' => $products,
            'tags' => $tags
        ]);
    }
}