<?php
namespace App\Controllers;

use Core\Controller;

class AccountController extends Controller {
    
    /**
     * Dashboard compte utilisateur
     */
    public function index() {
        $this->requireLogin();
        
        $user = $this->getCurrentUser();
        
        // Statistiques utilisateur
        if ($user['role'] === 'buyer') {
            // Achats de l'acheteur
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total_orders,
                       COALESCE(SUM(total_amount), 0) as total_spent
                FROM orders
                WHERE buyer_id = :user_id AND payment_status = 'completed'
            ");
            $stmt->execute(['user_id' => $user['id']]);
            $stats = $stmt->fetch();
            
            // Derniers achats
            $stmt = $this->db->prepare("
                SELECT o.*, COUNT(oi.id) as items_count
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.buyer_id = :user_id
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT 5
            ");
            $stmt->execute(['user_id' => $user['id']]);
            $orders = $stmt->fetchAll();
            
        } else {
            $stats = [];
            $orders = [];
        }
        
        $this->view('account/index', [
            'title' => 'Mon Compte',
            'user' => $user,
            'stats' => $stats,
            'orders' => $orders
        ]);
    }
    
    /**
     * Téléchargements
     */
    public function downloads() {
        $this->requireLogin();
        
        $user = $this->getCurrentUser();
        
        // Produits achetés
        $stmt = $this->db->prepare("
            SELECT DISTINCT p.*, oi.created_at as purchased_at
            FROM products p
            INNER JOIN order_items oi ON p.id = oi.product_id
            INNER JOIN orders o ON oi.order_id = o.id
            WHERE o.buyer_id = :user_id AND o.payment_status = 'completed'
            ORDER BY oi.created_at DESC
        ");
        $stmt->execute(['user_id' => $user['id']]);
        $products = $stmt->fetchAll();
        
        $this->view('account/downloads', [
            'title' => 'Mes Téléchargements',
            'products' => $products
        ]);
    }
}