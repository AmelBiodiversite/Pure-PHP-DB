<?php
namespace App\Controllers;

use Core\Controller;

class ExportController extends Controller {
    
    /**
     * Exporter les utilisateurs en CSV
     */
    public function users() {
        $this->requireAdmin();
        
        $stmt = $this->db->query("
            SELECT id, username, email, role, status, created_at
            FROM users
            ORDER BY created_at DESC
        ");
        $users = $stmt->fetchAll();
        
        $this->exportCSV('users', $users, [
            'ID', 'Username', 'Email', 'Role', 'Status', 'Date création'
        ]);
    }
    
    /**
     * Exporter les produits en CSV
     */
    public function products() {
        $this->requireAdmin();
        
        $stmt = $this->db->query("
            SELECT p.id, p.title, p.slug, p.price, p.status, 
                   u.username as seller, c.name as category,
                   p.downloads_count, p.created_at
            FROM products p
            LEFT JOIN users u ON p.seller_id = u.id
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.created_at DESC
        ");
        $products = $stmt->fetchAll();
        
        $this->exportCSV('products', $products, [
            'ID', 'Titre', 'Slug', 'Prix', 'Statut', 'Vendeur', 'Catégorie', 'Téléchargements', 'Date création'
        ]);
    }
    
    /**
     * Exporter les commandes en CSV
     */
    public function orders() {
        $this->requireAdmin();
        
        $stmt = $this->db->query("
            SELECT o.id, u.username as buyer, o.total_amount, 
                   o.payment_status, o.payment_method, o.created_at
            FROM orders o
            LEFT JOIN users u ON o.buyer_id = u.id
            ORDER BY o.created_at DESC
        ");
        $orders = $stmt->fetchAll();
        
        $this->exportCSV('orders', $orders, [
            'ID', 'Acheteur', 'Montant', 'Statut paiement', 'Méthode', 'Date'
        ]);
    }
    
    /**
     * Fonction helper pour exporter en CSV
     */
    private function exportCSV($filename, $data, $headers) {
        $filename = $filename . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $output = fopen('php://output', 'w');
        
        // BOM UTF-8 pour Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // En-têtes
        fputcsv($output, $headers, ';');
        
        // Données
        foreach ($data as $row) {
            fputcsv($output, array_values((array)$row), ';');
        }
        
        fclose($output);
        exit;
    }
}