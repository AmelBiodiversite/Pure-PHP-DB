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
            LEFT JOIN products p ON c.id = p.category_id AND p.status = 'approved' 
            WHERE c.is_active = TRUE
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
            WHERE p.status = 'approved'
            GROUP BY t.id
            ORDER BY product_count DESC
            LIMIT 20
        ");
        $popularTags = $stmt->fetchAll();

        // Range de prix pour le slider
        $stmt = $this->db->query("
            SELECT MIN(price) as min_price, MAX(price) as max_price
            FROM products
            WHERE status = 'approved'
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
            $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

            if (!$isOwner && !$isAdmin) {
                redirectWithMessage('/', 'Produit non disponible', 'error');
                return;
            }
        }

        // Récupérer les avis
        $stmt = $this->db->prepare("
            SELECT r.*, u.username, u.avatar_url,
                   TO_CHAR(r.created_at, 'DD/MM/YYYY') as review_date
            FROM reviews r
            JOIN users u ON r.buyer_id = u.id
            WHERE r.product_id = :product_id AND r.is_approved = TRUE
            ORDER BY r.created_at DESC
            LIMIT 10
        ");
        $stmt->execute(['product_id' => $product['id']]);
        $reviews = $stmt->fetchAll();

        // Produits similaires (même catégorie)
        $stmt = $this->db->prepare("
            SELECT p.*, u.username as seller_name
            FROM products p
            JOIN users u ON p.seller_id = u.id
            WHERE p.category_id = :category_id
              AND p.id != :product_id
              AND p.status = 'approved' 
              ORDER BY p.sales DESC
            LIMIT 6
        ");
        $stmt->execute([
            'category_id' => $product['category_id'], 
            'product_id' => $product['id']
        ]);
        $relatedProducts = $stmt->fetchAll();

        // Autres produits du vendeur
        $stmt = $this->db->prepare("
            SELECT p.*
            FROM products p
            WHERE p.seller_id = :seller_id
              AND p.id != :product_id
              AND p.status = 'approved' 
              
            ORDER BY p.created_at DESC
            LIMIT 4
        ");
        $stmt->execute([
            'seller_id' => $product['seller_id'],
            'product_id' => $product['id']
        ]);
        $sellerProducts = $stmt->fetchAll();

        // Vérifier si dans la wishlist
        $inWishlist = false;
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->db->prepare("
                SELECT id FROM wishlist 
                WHERE user_id = :user_id AND product_id = :product_id
            ");
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'product_id' => $product['id']
            ]);
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

        // Recherche avec filtres
        $filters = [
            'search' => $query,
            'category_id' => $_GET['category'] ?? null,
            'sort' => $_GET['sort'] ?? 'relevance'
        ];

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $result = $this->productModel->getProducts($filters, $page, 24);

        $this->view('products/search', [
            'title' => "Résultats pour : $query",
            'query' => $query,
            'products' => $result['products'],
            'pagination' => [
                'current' => $result['page'],
                'total' => $result['total_pages'],
                'total_items' => $result['total']
            ],
            'active_filters' => $filters
        ]);
    }

    /**
     * Ajouter/retirer de la wishlist (AJAX)
     */
    public function toggleWishlist() {
        if (!isset($_SESSION['user_id'])) {
            $this->json(['success' => false, 'message' => 'Connexion requise'], 401);
        }

        $productId = $_POST['product_id'] ?? 0;

        // Vérifier si déjà dans la wishlist
        $stmt = $this->db->prepare("
            SELECT id FROM wishlist 
            WHERE user_id = :user_id AND product_id = :product_id
        ");
        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'product_id' => $productId
        ]);
        $exists = $stmt->fetch();

        if ($exists) {
            // Retirer de la wishlist
            $stmt = $this->db->prepare("
                DELETE FROM wishlist 
                WHERE user_id = :user_id AND product_id = :product_id
            ");
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'product_id' => $productId
            ]);
            $this->json(['success' => true, 'action' => 'removed']);
        } else {
            // Ajouter à la wishlist
            $stmt = $this->db->prepare("
                INSERT INTO wishlist (user_id, product_id) 
                VALUES (:user_id, :product_id)
            ");
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'product_id' => $productId
            ]);
            $this->json(['success' => true, 'action' => 'added']);
        }
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
                WHERE product_id = :product_id1 AND is_approved = TRUE
            ),
            rating_count = (
                SELECT COUNT(*) 
                FROM reviews 
                WHERE product_id = :product_id2 AND is_approved = TRUE
            )
            WHERE id = :product_id3
        ");
        $stmt->execute([
            'product_id1' => $productId,
            'product_id2' => $productId,
            'product_id3' => $productId
        ]);
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
            SELECT title, slug, thumbnail_url, price
            FROM products
            WHERE title ILIKE :query
              AND status = 'approved' 
              
            LIMIT 5
        ");
        $stmt->execute(['query' => '%' . $query . '%']);
        $products = $stmt->fetchAll();

        // Rechercher dans les tags
        $stmt = $this->db->prepare("
            SELECT name, slug
            FROM tags
            WHERE name ILIKE :query
            LIMIT 3
        ");
        $stmt->execute(['query' => '%' . $query . '%']);
        $tags = $stmt->fetchAll();

        $this->json([
            'products' => $products,
            'tags' => $tags
        ]);
    }

    public function sellerProducts($username) {
        // Récupérer le vendeur
        $stmt = $this->db->prepare("SELECT id, username, full_name FROM users WHERE username = :username AND role = 'seller'");
        $stmt->execute(['username' => $username]);
        $seller = $stmt->fetch();

        if (!$seller) {
            redirectWithMessage('/', 'Vendeur introuvable', 'error');
            return;
        }

        // Récupérer ses produits
        $products = $this->productModel->getSellerProducts($seller['id'], 'approved');

        $this->view('products/seller', [
            'title' => 'Produits de ' . $seller['full_name'],
            'seller' => $seller,
            'products' => $products
        ]);
    }
    public function category($slug) {
        // Récupérer la catégorie
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        $category = $stmt->fetch();

        if (!$category) {
            redirectWithMessage('/', 'Catégorie introuvable', 'error');
            return;
        }

        // Produits de la catégorie
        $filters = ['category_id' => $category['id']];
        $result = $this->productModel->getProducts($filters, 1, 24);

        $this->view('products/category', [
            'title' => $category['name'],
            'category' => $category,
            'products' => $result['products'],
            'pagination' => [
                'current' => $result['page'],
                'total' => $result['total_pages']
            ]
        ]);
    }
    
}
