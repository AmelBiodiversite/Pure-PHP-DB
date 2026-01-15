<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Product;

class ApiController extends Controller {
    
    /**
     * Liste des produits
     * GET /api/products
     */
    public function products() {
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['per_page'] ?? 20;
        $category = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;
        
        $filters = [];
        if ($category) $filters['category_id'] = $category;
        if ($search) $filters['search'] = $search;
        
        $productModel = new Product();
        $result = $productModel->getProducts($filters, $page, $perPage);
        
        $this->json([
            'success' => true,
            'data' => $result['products'],
            'pagination' => [
                'page' => $result['page'],
                'per_page' => $perPage,
                'total' => $result['total'],
                'total_pages' => $result['total_pages']
            ]
        ]);
    }
    
    /**
     * Détail d'un produit
     * GET /api/products/{slug}
     */
    public function product($slug) {
        $productModel = new Product();
        $product = $productModel->getProductBySlug($slug);
        
        if (!$product) {
            $this->json([
                'success' => false,
                'error' => 'Produit introuvable'
            ], 404);
            return;
        }
        
        $this->json([
            'success' => true,
            'data' => $product
        ]);
    }
    
    /**
     * Liste des catégories
     * GET /api/categories
     */
    public function categories() {
        $stmt = $this->db->query("
            SELECT c.*, COUNT(p.id) as products_count
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id AND p.status = 'approved'
            WHERE c.is_active = TRUE
            GROUP BY c.id
            ORDER BY c.name ASC
        ");
        
        $categories = $stmt->fetchAll();
        
        $this->json([
            'success' => true,
            'data' => $categories
        ]);
    }
    
    /**
     * Documentation API
     * GET /api
     */
    public function index() {
        $this->json([
            'name' => 'MarketFlow Pro API',
            'version' => '1.0',
            'endpoints' => [
                [
                    'method' => 'GET',
                    'path' => '/api/products',
                    'description' => 'Liste des produits',
                    'parameters' => [
                        'page' => 'Numéro de page (défaut: 1)',
                        'per_page' => 'Produits par page (défaut: 20)',
                        'category' => 'ID catégorie (optionnel)',
                        'search' => 'Recherche textuelle (optionnel)'
                    ]
                ],
                [
                    'method' => 'GET',
                    'path' => '/api/products/{slug}',
                    'description' => 'Détail d\'un produit',
                    'parameters' => [
                        'slug' => 'Slug du produit'
                    ]
                ],
                [
                    'method' => 'GET',
                    'path' => '/api/categories',
                    'description' => 'Liste des catégories',
                    'parameters' => []
                ]
            ],
            'authentication' => 'Non requis pour lecture',
            'rate_limit' => '100 requêtes/minute',
            'response_format' => 'JSON'
        ]);
    }
}