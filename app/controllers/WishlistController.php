<?php
/**
 * MARKETFLOW PRO - WISHLIST CONTROLLER
 */

namespace App\Controllers;

use Core\Controller;
use App\Models\Wishlist;
use App\Models\Product;

class WishlistController extends Controller {
    private $wishlistModel;
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->wishlistModel = new Wishlist();
        $this->productModel = new Product();
    }

    /**
     * Page "Mes Favoris"
     */
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            redirectWithMessage('/login', 'Connexion requise', 'error');
            return;
        }

        $userId = $_SESSION['user_id'];
        $wishlistItems = $this->wishlistModel->getUserWishlist($userId);
        
        $this->render('wishlist/index', [
            'title' => 'Mes Favoris (' . count($wishlistItems) . ')',
            'wishlist_items' => $wishlistItems,
            'csrf_token' => generateCsrfToken()
        ]);
    }

    /**
     * Ajouter aux favoris (AJAX)
     */
    public function add() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Connexion requise']);
            exit;
        }

        // Récupérer product_id depuis JSON ou POST
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? ($_POST['product_id'] ?? null);

        if (!$productId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID produit manquant']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $result = $this->wishlistModel->add($userId, $productId);

        if ($result) {
            $newCount = $this->wishlistModel->getCount($userId);
            echo json_encode([
                'success' => true,
                'message' => 'Ajouté aux favoris !',
                'count' => $newCount
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur']);
        }
        exit;
    }

    /**
     * Retirer des favoris (AJAX)
     */
    public function remove() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Connexion requise']);
            exit;
        }

        // Récupérer product_id depuis JSON ou POST
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = $input['product_id'] ?? ($_POST['product_id'] ?? null);

        if (!$productId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID produit manquant']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $result = $this->wishlistModel->remove($userId, $productId);

        if ($result) {
            $newCount = $this->wishlistModel->getCount($userId);
            echo json_encode([
                'success' => true,
                'message' => 'Retiré des favoris',
                'count' => $newCount
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur']);
        }
        exit;
    }

    /**
     * Compteur favoris (AJAX)
     */
    public function count() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => true, 'count' => 0]);
            exit;
        }

        $count = $this->wishlistModel->getCount($_SESSION['user_id']);
        echo json_encode(['success' => true, 'count' => $count]);
        exit;
    }
}