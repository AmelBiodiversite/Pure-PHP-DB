<?php
/**
 * MARKETFLOW PRO - HOME CONTROLLER
 * Fichier : app/controllers/HomeController.php
 */


namespace App\Controllers;
use Core\Controller;

class HomeController extends Controller {
    public function index() {
        $this->view('home/index', ['title'=>'Accueil - MarketFlow Pro']);
    }

    public function sellers() {
        // Récupérer les vendeurs
        $stmt = $this->db->query("
            SELECT u.id, u.username, u.full_name, u.avatar_url, u.rating_average, u.rating_count,
                   COUNT(DISTINCT p.id) as products_count,
                   COALESCE(SUM(p.sales), 0) as total_sales
            FROM users u
            LEFT JOIN products p ON u.id = p.seller_id AND p.status = 'approved'
            WHERE u.role = 'seller'
            GROUP BY u.id, u.username, u.full_name, u.avatar_url, u.rating_average, u.rating_count
            ORDER BY COALESCE(SUM(p.sales), 0) DESC
            LIMIT 50
        ");
        $sellers = $stmt->fetchAll();

        $this->view('seller/index', [
            'title' => 'Nos Vendeurs',
            'sellers' => $sellers
        ]);
    }

    public function about() {
        $this->view('home/about', ['title' => 'À propos']);
    }

    public function contact() {
        $this->view('home/contact', ['title' => 'Contact', 'csrf_token' => generateCsrfToken()]);
    }

    public function contactSubmit() {
        // TODO: Envoyer email
        redirectWithMessage('/contact', 'Message envoyé !', 'success');
    }

    public function terms() {
        $this->view('home/terms', ['title' => 'CGU']);
    }

    public function privacy() {
        $this->view('home/privacy', ['title' => 'Confidentialité']);
    }

    public function help() {
        $this->view('home/help', ['title' => 'Centre d\'aide']);
    }
}