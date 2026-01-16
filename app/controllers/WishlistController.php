<?php
/**
 * ================================================
 * MARKETFLOW PRO - CONTRÔLEUR WISHLIST
 * ================================================
 * 
 * Fichier : app/controllers/WishlistController.php
 * Version : 1.0
 * Date : 16 janvier 2025
 * 
 * DESCRIPTION :
 * Gère toutes les actions liées à la wishlist (favoris) :
 * - Affichage de la page "Mes Favoris"
 * - Ajout d'un produit aux favoris (AJAX)
 * - Suppression d'un produit des favoris (AJAX)
 * - Récupération du compteur (AJAX)
 * 
 * ROUTES ASSOCIÉES :
 * GET  /wishlist        → index() : Affiche la page des favoris
 * POST /wishlist/add    → add() : Ajoute un produit (AJAX)
 * POST /wishlist/remove → remove() : Supprime un produit (AJAX)
 * GET  /wishlist/count  → count() : Récupère le compteur (AJAX)
 * 
 * ================================================
 */

class WishlistController {
    /**
     * Instance du modèle Wishlist
     * @var Wishlist
     */
    private $wishlistModel;

    /**
     * Instance du modèle Product
     * @var Product
     */
    private $productModel;

    /**
     * ============================================
     * CONSTRUCTEUR
     * ============================================
     * Initialise les modèles nécessaires
     */
    public function __construct() {
        $this->wishlistModel = new Wishlist();
        $this->productModel = new Product();
    }

    /**
     * ============================================
     * AFFICHER LA PAGE "MES FAVORIS"
     * ============================================
     * 
     * Route : GET /wishlist
     * 
     * Affiche la liste complète des produits en favoris de l'utilisateur.
     * Requiert une authentification (redirection si non connecté).
     */
    public function index() {
        // Vérifier l'authentification
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_message'] = 'Vous devez être connecté pour accéder à vos favoris';
            $_SESSION['flash_type'] = 'error';
            header('Location: /login?redirect=/wishlist');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        // Récupérer tous les produits en favoris
        $wishlistItems = $this->wishlistModel->getUserWishlist($userId);
        
        // Compter le nombre total
        $wishlistCount = count($wishlistItems);

        // Variables pour la vue
        $title = "Mes Favoris ({$wishlistCount})";
        
        // Affichage de la vue
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/wishlist/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    /**
     * ============================================
     * AJOUTER UN PRODUIT AUX FAVORIS (AJAX)
     * ============================================
     * 
     * Route : POST /wishlist/add
     * 
     * Renvoie du JSON pour mise à jour dynamique de l'UI.
     * 
     * RÉPONSE JSON :
     * {
     *   "success": true/false,
     *   "message": "Message de confirmation",
     *   "count": 5,
     *   "inWishlist": true
     * }
     */
    public function add() {
        header('Content-Type: application/json');

        // Vérifier l'authentification
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Vous devez être connecté pour ajouter des favoris'
            ]);
            exit;
        }

        // Valider les données
        $productId = $_POST['product_id'] ?? null;

        if (!$productId || !is_numeric($productId)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID de produit invalide'
            ]);
            exit;
        }

        // Vérifier que le produit existe
        $product = $this->productModel->getProductWithSeller($productId);

        if (!$product) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Produit introuvable'
            ]);
            exit;
        }

        // Ajouter à la wishlist
        $userId = $_SESSION['user_id'];
        $result = $this->wishlistModel->add($userId, $productId);

        if ($result) {
            $newCount = $this->wishlistModel->getCount($userId);

            echo json_encode([
                'success' => true,
                'message' => 'Produit ajouté aux favoris !',
                'count' => $newCount,
                'inWishlist' => true
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout aux favoris'
            ]);
        }
        
        exit;
    }

    /**
     * ============================================
     * RETIRER UN PRODUIT DES FAVORIS (AJAX)
     * ============================================
     * 
     * Route : POST /wishlist/remove
     */
    public function remove() {
        header('Content-Type: application/json');

        // Vérifier l'authentification
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Vous devez être connecté'
            ]);
            exit;
        }

        // Valider les données
        $productId = $_POST['product_id'] ?? null;

        if (!$productId || !is_numeric($productId)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID de produit invalide'
            ]);
            exit;
        }

        // Supprimer de la wishlist
        $userId = $_SESSION['user_id'];
        $result = $this->wishlistModel->remove($userId, $productId);

        if ($result) {
            $newCount = $this->wishlistModel->getCount($userId);

            echo json_encode([
                'success' => true,
                'message' => 'Produit retiré des favoris',
                'count' => $newCount,
                'inWishlist' => false
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ]);
        }
        
        exit;
    }

    /**
     * ============================================
     * RÉCUPÉRER LE COMPTEUR DE FAVORIS (AJAX)
     * ============================================
     * 
     * Route : GET /wishlist/count
     */
    public function count() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => true, 'count' => 0]);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $count = $this->wishlistModel->getCount($userId);

        echo json_encode([
            'success' => true,
            'count' => $count
        ]);
        exit;
    }
}
