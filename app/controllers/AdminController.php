<?php
/**
 * MARKETFLOW PRO - ADMIN CONTROLLER
 * Gestion de l'administration
 * Fichier : app/controllers/AdminController.php
 */

namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;

class AdminController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->requireAdmin();
    }

    /**
     * Dashboard admin
     */
    public function index() {
        
        // Stats globales
        $stats = [
            'total_users' => $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'total_products' => $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
            'total_orders' => $this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
            'total_revenue' => $this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'completed'")->fetchColumn()
        ];

        // Produits en attente
        $pending_products = $this->db->query("
            SELECT p.*, u.username as seller_name 
            FROM products p 
            JOIN users u ON p.seller_id = u.id 
            WHERE p.status = 'pending' 
            ORDER BY p.created_at DESC 
            LIMIT 10
        ")->fetchAll();

        // Derniers utilisateurs
        $recent_users = $this->db->query("
            SELECT id, username, email, role, created_at, avatar_url, full_name
            FROM users 
            ORDER BY created_at DESC 
            LIMIT 10
        ")->fetchAll();

        // Dernières commandes
        $recent_orders = $this->db->query("
            SELECT o.*, u.username as buyer_name 
            FROM orders o 
            JOIN users u ON o.buyer_id = u.id 
            ORDER BY o.created_at DESC 
            LIMIT 10
        ")->fetchAll();

        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'pending_products' => $pending_products,
            'recent_users' => $recent_users,
            'recent_orders' => $recent_orders
        ]);
    }

    /**
     * Gestion des utilisateurs
     */
    public function users() {
        $users = $this->db->query("
            SELECT id, username, email, role, is_active, created_at 
            FROM users 
            ORDER BY created_at DESC
        ")->fetchAll();

        $this->view('admin/users', [
            'title' => 'Gestion Utilisateurs',
            'users' => $users
        ]);
    }

    /**
     * Gestion des produits
     */
    public function products() {
        $status = $_GET['status'] ?? 'all';

        $sql = "SELECT p.*, u.username as seller_name 
                FROM products p 
                JOIN users u ON p.seller_id = u.id";

        if ($status !== 'all') {
            $sql .= " WHERE p.status = :status";
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($sql);

        if ($status !== 'all') {
            $stmt->execute(['status' => $status]);
        } else {
            $stmt->execute();
        }

        $products = $stmt->fetchAll();

        $this->view('admin/products', [
            'title' => 'Gestion Produits',
            'products' => $products,
            'current_status' => $status
        ]);
    }

    /**
     * Valider un produit
     */
    public function approveProduct() {
        $product_id = $_POST['product_id'] ?? 0;

        $stmt = $this->db->prepare("UPDATE products SET status = 'active' WHERE id = :id");
        $success = $stmt->execute(['id' => $product_id]);

        if ($success) {
            $_SESSION['success'] = "Produit approuvé avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de l'approbation";
        }

        $this->redirect('/admin/products');
    }

    /**
     * Rejeter un produit
     */
    public function rejectProduct() {
        $product_id = $_POST['product_id'] ?? 0;
        $reason = $_POST['reason'] ?? 'Non conforme';

        $stmt = $this->db->prepare("UPDATE products SET status = 'rejected' WHERE id = :id");
        $success = $stmt->execute(['id' => $product_id]);

        if ($success) {
            $_SESSION['success'] = "Produit rejeté";
        } else {
            $_SESSION['error'] = "Erreur lors du rejet";
        }

        $this->redirect('/admin/products');
    }

    /**
     * Suspendre un utilisateur
     */
    public function suspendUser() {
        $user_id = $_POST['user_id'] ?? 0;

        $stmt = $this->db->prepare("UPDATE users SET is_active = FALSE WHERE id = :id");
        $success = $stmt->execute(['id' => $user_id]);

        if ($success) {
            $_SESSION['success'] = "Utilisateur suspendu";
        } else {
            $_SESSION['error'] = "Erreur lors de la suspension";
        }

        $this->redirect('/admin/users');
    }

    /**
     * Activer un utilisateur
     */
    public function activateUser() {
        $user_id = $_POST['user_id'] ?? 0;

        $stmt = $this->db->prepare("UPDATE users SET is_active = TRUE WHERE id = :id");
        $success = $stmt->execute(['id' => $user_id]);

        if ($success) {
            $_SESSION['success'] = "Utilisateur activé";
        } else {
            $_SESSION['error'] = "Erreur lors de l'activation";
        }

        $this->redirect('/admin/users');
    }

    /**
     * Statistiques détaillées
     */
    public function stats() {
        // Stats par mois (6 derniers mois)
        $monthly_stats = $this->db->query("
            SELECT 
                TO_CHAR(created_at, 'YYYY-MM') as month,
                COUNT(*) as total_orders,
                SUM(total_amount) as revenue
            FROM orders
            WHERE created_at >= CURRENT_DATE - INTERVAL '6 months'
            GROUP BY month
            ORDER BY month DESC
        ")->fetchAll();

        // Top vendeurs
        $top_sellers = $this->db->query("
            SELECT 
                u.username,
                COUNT(DISTINCT p.id) as products_count,
                COUNT(DISTINCT oi.order_id) as sales_count,
                COALESCE(SUM(oi.price), 0) as total_revenue
            FROM users u
            LEFT JOIN products p ON u.id = p.seller_id
            LEFT JOIN order_items oi ON p.id = oi.product_id
            WHERE u.role = 'seller'
            GROUP BY u.id, u.username
            ORDER BY total_revenue DESC
            LIMIT 10
        ")->fetchAll();

        $this->view('admin/stats', [
            'title' => 'Statistiques',
            'monthly_stats' => $monthly_stats,
            'top_sellers' => $top_sellers
        ]);
    }
}