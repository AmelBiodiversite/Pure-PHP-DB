<?php
/**
 * MARKETFLOW PRO - ADMIN CONTROLLER
 * Gestion administrative de la plateforme
 * Fichier : app/controllers/AdminController.php
 */

namespace App\Controllers;

use Core\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;

class AdminController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->requireAdmin();
    }

    /**
     * Dashboard admin
     */
    public function dashboard() {
        // Stats globales
        $stats = [
            'total_users' => $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'total_sellers' => $this->db->query("SELECT COUNT(*) FROM users WHERE user_type = 'seller'")->fetchColumn(),
            'total_products' => $this->db->query("SELECT COUNT(*) FROM products WHERE status = 'approved'")->fetchColumn(),
            'pending_products' => $this->db->query("SELECT COUNT(*) FROM products WHERE status = 'pending'")->fetchColumn(),
            'total_orders' => $this->db->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'completed'")->fetchColumn(),
            'total_revenue' => $this->db->query("SELECT SUM(total_amount) FROM orders WHERE payment_status = 'completed'")->fetchColumn(),
            'platform_fees' => $this->db->query("SELECT SUM(platform_fee) FROM orders WHERE payment_status = 'completed'")->fetchColumn(),
        ];

        // Dernières inscriptions
        $stmt = $this->db->query("
            SELECT id, username, email, user_type, created_at 
            FROM users 
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        $recent_users = $stmt->fetchAll();

        // Produits en attente
        $stmt = $this->db->query("
            SELECT p.*, u.username as seller_name 
            FROM products p
            JOIN users u ON p.seller_id = u.id
            WHERE p.status = 'pending'
            ORDER BY p.created_at DESC
            LIMIT 10
        ");
        $pending_products = $stmt->fetchAll();

        // Dernières commandes
        $stmt = $this->db->query("
            SELECT o.*, u.username as buyer_name
            FROM orders o
            JOIN users u ON o.buyer_id = u.id
            ORDER BY o.created_at DESC
            LIMIT 10
        ");
        $recent_orders = $stmt->fetchAll();

        $this->view('admin/dashboard', [
            'title' => 'Dashboard Admin',
            'stats' => $stats,
            'recent_users' => $recent_users,
            'pending_products' => $pending_products,
            'recent_orders' => $recent_orders
        ]);
    }

    /**
     * Gestion des produits
     */
    public function products() {
        $status = $_GET['status'] ?? 'all';
        
        $where = "1=1";
        if ($status === 'pending') $where = "p.status = 'pending'";
        elseif ($status === 'approved') $where = "p.status = 'approved'";
        elseif ($status === 'rejected') $where = "p.status = 'rejected'";

        $stmt = $this->db->query("
            SELECT p.*, 
                   u.username as seller_name,
                   sp.shop_name,
                   c.name as category_name
            FROM products p
            JOIN users u ON p.seller_id = u.id
            LEFT JOIN seller_profiles sp ON u.id = sp.user_id
            JOIN categories c ON p.category_id = c.id
            WHERE {$where}
            ORDER BY p.created_at DESC
        ");
        $products = $stmt->fetchAll();

        $this->view('admin/products', [
            'title' => 'Gestion des Produits',
            'products' => $products,
            'status' => $status,
            'csrf_token' => generateCsrfToken()
        ]);
    }

    /**
     * Approuver un produit
     */
    public function approveProduct($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/products');
        }

        $stmt = $this->db->prepare("
            UPDATE products 
            SET status = 'approved', updated_at = ?
            WHERE id = ?
        ");
        $stmt->execute([date('Y-m-d H:i:s'), $id]);

        // Log
        $this->logActivity($_SESSION['user_id'], 'product_approved', 'product', $id);

        redirectWithMessage('/admin/products', 'Produit approuvé', 'success');
    }

    /**
     * Rejeter un produit
     */
    public function rejectProduct($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/products');
        }

        $reason = $_POST['reason'] ?? 'Non conforme aux règles de la plateforme';

        $stmt = $this->db->prepare("
            UPDATE products 
            SET status = 'rejected', rejection_reason = ?, updated_at = ?
            WHERE id = ?
        ");
        $stmt->execute([$reason, date('Y-m-d H:i:s'), $id]);

        // Log
        $this->logActivity($_SESSION['user_id'], 'product_rejected', 'product', $id, ['reason' => $reason]);

        redirectWithMessage('/admin/products', 'Produit rejeté', 'success');
    }

    /**
     * Gestion des utilisateurs
     */
    public function users() {
        $type = $_GET['type'] ?? 'all';
        
        $where = "1=1";
        if ($type === 'buyers') $where = "user_type = 'buyer'";
        elseif ($type === 'sellers') $where = "user_type = 'seller'";

        $stmt = $this->db->query("
            SELECT u.*, 
                   sp.shop_name,
                   sp.total_sales,
                   sp.rating_average
            FROM users u
            LEFT JOIN seller_profiles sp ON u.id = sp.user_id
            WHERE {$where}
            ORDER BY u.created_at DESC
        ");
        $users = $stmt->fetchAll();

        $this->view('admin/users', [
            'title' => 'Gestion des Utilisateurs',
            'users' => $users,
            'type' => $type,
            'csrf_token' => generateCsrfToken()
        ]);
    }

    /**
     * Toggle actif/inactif utilisateur
     */
    public function toggleUser($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
        }

        $stmt = $this->db->prepare("
            UPDATE users 
            SET is_active = NOT is_active
            WHERE id = ?
        ");
        $stmt->execute([$id]);

        redirectWithMessage('/admin/users', 'Statut utilisateur modifié', 'success');
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
        }

        // Vérifier qu'il ne se supprime pas lui-même
        if ($id == $_SESSION['user_id']) {
            redirectWithMessage('/admin/users', 'Vous ne pouvez pas vous supprimer', 'error');
            return;
        }

        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        redirectWithMessage('/admin/users', 'Utilisateur supprimé', 'success');
    }

    /**
     * Gestion des vendeurs
     */
    public function sellers() {
        $stmt = $this->db->query("
            SELECT u.*, 
                   sp.shop_name,
                   sp.total_sales,
                   sp.total_products,
                   sp.rating_average,
                   sp.is_approved
            FROM users u
            JOIN seller_profiles sp ON u.id = sp.user_id
            WHERE u.user_type = 'seller'
            ORDER BY sp.is_approved ASC, u.created_at DESC
        ");
        $sellers = $stmt->fetchAll();

        $this->view('admin/sellers', [
            'title' => 'Gestion des Vendeurs',
            'sellers' => $sellers,
            'csrf_token' => generateCsrfToken()
        ]);
    }

    /**
     * Approuver un vendeur
     */
    public function approveSeller($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/sellers');
        }

        $stmt = $this->db->prepare("
            UPDATE seller_profiles 
            SET is_approved = 1
            WHERE user_id = ?
        ");
        $stmt->execute([$id]);

        redirectWithMessage('/admin/sellers', 'Vendeur approuvé', 'success');
    }

    /**
     * Gestion des commandes
     */
    public function orders() {
        $stmt = $this->db->query("
            SELECT o.*, u.username as buyer_name
            FROM orders o
            JOIN users u ON o.buyer_id = u.id
            ORDER BY o.created_at DESC
            LIMIT 50
        ");
        $orders = $stmt->fetchAll();

        $this->view('admin/orders', [
            'title' => 'Gestion des Commandes',
            'orders' => $orders
        ]);
    }

    /**
     * Paramètres système
     */
    public function settings() {
        // Récupérer les paramètres
        $stmt = $this->db->query("SELECT * FROM settings");
        $settings = [];
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        $this->view('admin/settings', [
            'title' => 'Paramètres',
            'settings' => $settings,
            'csrf_token' => generateCsrfToken()
        ]);
    }

    /**
     * Mettre à jour les paramètres
     */
    public function updateSettings() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/settings');
        }

        foreach ($_POST as $key => $value) {
            if ($key === 'csrf_token') continue;

            $stmt = $this->db->prepare("
                UPDATE settings 
                SET setting_value = ?
                WHERE setting_key = ?
            ");
            $stmt->execute([$value, $key]);
        }

        redirectWithMessage('/admin/settings', 'Paramètres mis à jour', 'success');
    }

    /**
     * Statistiques avancées
     */
    public function stats() {
        // Stats détaillées par période
        $stmt = $this->db->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as orders,
                SUM(total_amount) as revenue,
                SUM(platform_fee) as fees
            FROM orders
            WHERE payment_status = 'completed'
              AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month DESC
        ");
        $monthly_stats = $stmt->fetchAll();

        // Top vendeurs
        $stmt = $this->db->query("
            SELECT u.username, sp.shop_name, sp.total_sales, sp.total_products, sp.rating_average
            FROM seller_profiles sp
            JOIN users u ON sp.user_id = u.id
            ORDER BY sp.total_sales DESC
            LIMIT 10
        ");
        $top_sellers = $stmt->fetchAll();

        // Top produits
        $stmt = $this->db->query("
            SELECT p.title, p.sales_count, p.price, u.username as seller_name
            FROM products p
            JOIN users u ON p.seller_id = u.id
            ORDER BY p.sales_count DESC
            LIMIT 10
        ");
        $top_products = $stmt->fetchAll();

        $this->view('admin/stats', [
            'title' => 'Statistiques',
            'monthly_stats' => $monthly_stats,
            'top_sellers' => $top_sellers,
            'top_products' => $top_products
        ]);
    }

    /**
     * Logger une activité
     */
    private function logActivity($userId, $action, $entityType, $entityId, $details = null) {
        $stmt = $this->db->prepare("
            INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, created_at)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $action,
            $entityType,
            $entityId,
            $details ? json_encode($details) : null,
            date('Y-m-d H:i:s')
        ]);
    }
}