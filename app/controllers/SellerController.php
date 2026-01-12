<?php
/**
 * MARKETFLOW PRO - SELLER CONTROLLER
 * Gestion des produits par les vendeurs
 * Fichier : app/controllers/SellerController.php
 */

namespace App\Controllers;

use Core\Controller;
use App\Models\Product;
use App\Models\User;

class SellerController extends Controller {
    private $productModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
        $this->userModel = new User();
        
        // Vérifier que l'utilisateur est vendeur
        $this->requireSeller();
    }

    /**
     * Dashboard vendeur
     */
    public function dashboard() {
        $sellerId = $_SESSION['user_id'];

        // Statistiques
        $stats = $this->userModel->getSellerStats($sellerId);

        // Dernières ventes
        $stmt = $this->db->prepare("
            SELECT oi.*, p.title as product_title, o.created_at as order_date,
                   o.order_number, u.username as buyer_name
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN products p ON oi.product_id = p.id
            JOIN users u ON o.buyer_id = u.id
            WHERE oi.seller_id = ? AND o.payment_status = 'completed'
            ORDER BY o.created_at DESC
            LIMIT 10
        ");
        $stmt->execute([$sellerId]);
        $recentSales = $stmt->fetchAll();

        // Produits en attente d'approbation
        $pendingProducts = $this->productModel->getSellerProducts($sellerId, 'pending');

        // Graphique des ventes (30 derniers jours)
        $stmt = $this->db->prepare("
            SELECT DATE(o.created_at) as date, 
                   COUNT(*) as orders,
                   SUM(oi.seller_amount) as revenue
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE oi.seller_id = ? 
              AND o.payment_status = 'completed'
              AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(o.created_at)
            ORDER BY date ASC
        ");
        $stmt->execute([$sellerId]);
        $salesChart = $stmt->fetchAll();

        $this->view('seller/dashboard', [
            'title' => 'Dashboard Vendeur',
            'stats' => $stats,
            'recent_sales' => $recentSales,
            'pending_products' => $pendingProducts,
            'sales_chart' => $salesChart
        ]);
    }

    /**
     * Liste des produits du vendeur
     */
    public function products() {
        $sellerId = $_SESSION['user_id'];
        $products = $this->productModel->getSellerProducts($sellerId);

        $this->view('seller/products', [
            'title' => 'Mes Produits',
            'products' => $products
        ]);
    }

    /**
     * Formulaire de création de produit
     */
    public function createProduct() {
        // Récupérer les catégories
        $stmt = $this->db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC");
        $categories = $stmt->fetchAll();

        $this->view('seller/product_form', [
            'title' => 'Ajouter un Produit',
            'categories' => $categories,
            'csrf_token' => generateCsrfToken(),
            'mode' => 'create'
        ]);
    }

    /**
     * Enregistrer un nouveau produit
     */
    public function storeProduct() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/seller/products');
        }

        // Vérifier CSRF
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            $this->json(['success' => false, 'error' => 'Token invalide'], 403);
        }

        $data = [
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'category_id' => $_POST['category_id'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'original_price' => $_POST['original_price'] ?? null,
            'file_type' => $_POST['file_type'] ?? '',
            'demo_url' => $_POST['demo_url'] ?? '',
            'tags' => $_POST['tags'] ?? ''
        ];

        // Validation des uploads
        $uploadErrors = $this->validateUploads($_FILES);
        if (!empty($uploadErrors)) {
            $this->view('seller/product_form', [
                'title' => 'Ajouter un Produit',
                'errors' => $uploadErrors,
                'old' => $data,
                'categories' => $this->getCategories(),
                'csrf_token' => generateCsrfToken(),
                'mode' => 'create'
            ]);
            return;
        }

        $result = $this->productModel->createProduct(
            $_SESSION['user_id'],
            $data,
            $_FILES
        );

        if ($result['success']) {
            redirectWithMessage(
                '/seller/products',
                $result['message'],
                'success'
            );
        } else {
            $this->view('seller/product_form', [
                'title' => 'Ajouter un Produit',
                'errors' => $result['errors'] ?? ['general' => $result['error']],
                'old' => $data,
                'categories' => $this->getCategories(),
                'csrf_token' => generateCsrfToken(),
                'mode' => 'create'
            ]);
        }
    }

    /**
     * Formulaire d'édition de produit
     */
    public function editProduct($id) {
        $product = $this->productModel->find($id);

        // Vérifier que le produit appartient au vendeur
        if (!$product || $product['seller_id'] != $_SESSION['user_id']) {
            redirectWithMessage('/seller/products', 'Produit introuvable', 'error');
            return;
        }

        // Récupérer les tags
        $stmt = $this->db->prepare("
            SELECT t.name
            FROM tags t
            JOIN product_tags pt ON t.id = pt.tag_id
            WHERE pt.product_id = ?
        ");
        $stmt->execute([$id]);
        $tags = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $product['tags_string'] = implode(', ', $tags);

        // Récupérer les images
        $stmt = $this->db->prepare("
            SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order
        ");
        $stmt->execute([$id]);
        $product['images'] = $stmt->fetchAll();

        $this->view('seller/product_form', [
            'title' => 'Modifier le Produit',
            'product' => $product,
            'categories' => $this->getCategories(),
            'csrf_token' => generateCsrfToken(),
            'mode' => 'edit'
        ]);
    }

    /**
     * Mettre à jour un produit
     */
    public function updateProduct($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/seller/products');
        }

        // Vérifier CSRF
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            $this->json(['success' => false, 'error' => 'Token invalide'], 403);
        }

        $data = [
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'category_id' => $_POST['category_id'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'original_price' => $_POST['original_price'] ?? null,
            'demo_url' => $_POST['demo_url'] ?? '',
            'tags' => $_POST['tags'] ?? ''
        ];

        $result = $this->productModel->updateProduct(
            $id,
            $_SESSION['user_id'],
            $data,
            $_FILES
        );

        if ($result['success']) {
            redirectWithMessage('/seller/products', $result['message'], 'success');
        } else {
            redirectWithMessage(
                "/seller/products/{$id}/edit",
                $result['error'] ?? 'Erreur lors de la mise à jour',
                'error'
            );
        }
    }

    /**
     * Supprimer un produit
     */
    public function deleteProduct($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'error' => 'Méthode non autorisée'], 405);
        }

        $result = $this->productModel->deleteProduct($id, $_SESSION['user_id']);

        if ($result['success']) {
            redirectWithMessage('/seller/products', $result['message'], 'success');
        } else {
            redirectWithMessage('/seller/products', $result['error'], 'error');
        }
    }

    /**
     * Page des ventes
     */
    public function sales() {
        $sellerId = $_SESSION['user_id'];

        $stmt = $this->db->prepare("
            SELECT oi.*, p.title as product_title, o.order_number,
                   o.created_at as order_date, o.payment_status,
                   u.username as buyer_name, u.email as buyer_email
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN products p ON oi.product_id = p.id
            JOIN users u ON o.buyer_id = u.id
            WHERE oi.seller_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$sellerId]);
        $sales = $stmt->fetchAll();

        $this->view('seller/sales', [
            'title' => 'Mes Ventes',
            'sales' => $sales
        ]);
    }

    /**
     * Page des revenus
     */
    public function earnings() {
        $sellerId = $_SESSION['user_id'];

        // Revenus totaux
        $stmt = $this->db->prepare("
            SELECT 
                COALESCE(SUM(oi.seller_amount), 0) as total_earnings,
                COALESCE(SUM(oi.commission_amount), 0) as total_commission,
                COUNT(DISTINCT oi.order_id) as total_orders
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE oi.seller_id = ? AND o.payment_status = 'completed'
        ");
        $stmt->execute([$sellerId]);
        $earnings = $stmt->fetch();

        // Revenus par mois (12 derniers mois)
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(o.created_at, '%Y-%m') as month,
                SUM(oi.seller_amount) as revenue,
                COUNT(*) as orders
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE oi.seller_id = ? 
              AND o.payment_status = 'completed'
              AND o.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
            ORDER BY month DESC
        ");
        $stmt->execute([$sellerId]);
        $monthlyEarnings = $stmt->fetchAll();

        // Historique des paiements reçus
        $stmt = $this->db->prepare("
            SELECT * FROM seller_payouts
            WHERE seller_id = ?
            ORDER BY requested_at DESC
        ");
        $stmt->execute([$sellerId]);
        $payouts = $stmt->fetchAll();

        $this->view('seller/earnings', [
            'title' => 'Mes Revenus',
            'earnings' => $earnings,
            'monthly_earnings' => $monthlyEarnings,
            'payouts' => $payouts,
            'csrf_token' => generateCsrfToken()
        ]);
    }

    /**
     * Demander un paiement
     */
    public function requestPayout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/seller/earnings');
        }

        $sellerId = $_SESSION['user_id'];

        // Vérifier le solde disponible
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(oi.seller_amount), 0) as available
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            LEFT JOIN seller_payouts sp ON oi.id = sp.id
            WHERE oi.seller_id = ? 
              AND o.payment_status = 'completed'
              AND sp.id IS NULL
        ");
        $stmt->execute([$sellerId]);
        $available = $stmt->fetch()['available'];

        if ($available < MIN_PAYOUT_AMOUNT) {
            redirectWithMessage(
                '/seller/earnings',
                'Montant minimum non atteint (' . formatPrice(MIN_PAYOUT_AMOUNT) . ')',
                'error'
            );
            return;
        }

        // Créer la demande de paiement
        $stmt = $this->db->prepare("
            INSERT INTO seller_payouts (seller_id, amount, status, requested_at)
            VALUES (?, ?, 'pending', ?)
        ");
        $stmt->execute([$sellerId, $available, date('Y-m-d H:i:s')]);

        redirectWithMessage(
            '/seller/earnings',
            'Demande de paiement envoyée',
            'success'
        );
    }

    /**
     * Analytics
     */
    public function analytics() {
        $sellerId = $_SESSION['user_id'];

        // Produits les plus vendus
        $stmt = $this->db->prepare("
            SELECT p.id, p.title, p.thumbnail, p.price,
                   COUNT(oi.id) as total_sales,
                   SUM(oi.seller_amount) as total_revenue
            FROM products p
            LEFT JOIN order_items oi ON p.id = oi.product_id
            LEFT JOIN orders o ON oi.order_id = o.id AND o.payment_status = 'completed'
            WHERE p.seller_id = ?
            GROUP BY p.id
            ORDER BY total_sales DESC
            LIMIT 10
        ");
        $stmt->execute([$sellerId]);
        $topProducts = $stmt->fetchAll();

        // Sources de trafic (vues par produit)
        $stmt = $this->db->prepare("
            SELECT id, title, views_count, sales_count,
                   CASE 
                       WHEN views_count > 0 THEN (sales_count / views_count * 100)
                       ELSE 0 
                   END as conversion_rate
            FROM products
            WHERE seller_id = ?
            ORDER BY views_count DESC
            LIMIT 10
        ");
        $stmt->execute([$sellerId]);
        $traffic = $stmt->fetchAll();

        $this->view('seller/analytics', [
            'title' => 'Analytics',
            'top_products' => $topProducts,
            'traffic' => $traffic
        ]);
    }

    /**
     * Récupérer les catégories
     */
    private function getCategories() {
        $stmt = $this->db->query("
            SELECT * FROM categories 
            WHERE is_active = 1 
            ORDER BY name ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Valider les uploads
     */
    private function validateUploads($files) {
        $errors = [];

        // Thumbnail obligatoire pour nouveau produit
        if (empty($files['thumbnail']['name'])) {
            $errors['thumbnail'] = 'L\'image principale est requise';
        } elseif (!isAllowedFileType($files['thumbnail']['name'], ALLOWED_IMAGE_TYPES)) {
            $errors['thumbnail'] = 'Format d\'image non autorisé';
        } elseif ($files['thumbnail']['size'] > MAX_IMAGE_SIZE) {
            $errors['thumbnail'] = 'Image trop volumineuse (max 5MB)';
        }

        // Fichier produit obligatoire
        if (empty($files['product_file']['name'])) {
            $errors['product_file'] = 'Le fichier produit est requis';
        } elseif (!isAllowedFileType($files['product_file']['name'], ALLOWED_FILE_TYPES)) {
            $errors['product_file'] = 'Format de fichier non autorisé';
        } elseif ($files['product_file']['size'] > MAX_FILE_SIZE) {
            $errors['product_file'] = 'Fichier trop volumineux (max 50MB)';
        }

        return $errors;
    }
}