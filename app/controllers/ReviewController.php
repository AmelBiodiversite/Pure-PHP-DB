<?php
namespace App\Controllers;

use Core\Controller;

/**
 * Gestion des avis produits :
 * - Création d'avis par un acheteur authentifié
 * - Suppression par l'auteur ou un administrateur
 * - Modération par un administrateur
 */
class ReviewController extends Controller {

    /**
     * Création d’un nouvel avis sur un produit
     * Conditions :
     * - Utilisateur connecté
     * - Produit existant et approuvé
     * - L'utilisateur ne peut pas noter son propre produit
     * - Un seul avis par produit et par acheteur
     * - Vérifie si l'achat a bien été effectué (achat vérifié)
     */
    public function create() {
        $this->requireAuth();

        // Sécurité : refuse toute requête autre que POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithMessage('/', 'Méthode non autorisée', 'error');
            return;
        }

        // Récupération et nettoyage des données du formulaire
        $productId = $_POST['product_id'] ?? '';
        $rating    = (int)($_POST['rating'] ?? 0);
        $title     = trim($_POST['title'] ?? '');
        $comment   = trim($_POST['comment'] ?? '');
        $buyerId   = $_SESSION['user_id'];

        // Validation basique des entrées
        if (!$productId || $rating < 1 || $rating > 5) {
            redirectWithMessage('/', 'Données invalides', 'error');
            return;
        }

        // Vérifie que le produit existe et est visible publiquement
        $stmt = $this->db->prepare("
            SELECT id, seller_id, title, slug
            FROM products 
            WHERE id = :id AND status = 'approved'
        ");
        $stmt->execute(['id' => $productId]);
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$product) {
            redirectWithMessage('/', 'Produit introuvable', 'error');
            return;
        }

        // Empêche un vendeur de noter son propre produit
        if ($product['seller_id'] == $buyerId) {
            redirectWithMessage("/products/{$product['slug']}", 
                'Vous ne pouvez pas noter votre propre produit', 'error');
            return;
        }

        // Vérifie qu’un avis n’a pas déjà été posté par cet acheteur
        $stmt = $this->db->prepare("
            SELECT id FROM reviews 
            WHERE product_id = :product_id AND buyer_id = :buyer_id
        ");
        $stmt->execute([
            'product_id' => $productId,
            'buyer_id'   => $buyerId
        ]);

        if ($stmt->fetch()) {
            redirectWithMessage("/products/{$product['slug']}", 
                'Vous avez déjà noté ce produit', 'warning');
            return;
        }

        // Vérifie si l’acheteur a réellement acheté ce produit
        // Permet d’afficher un badge "achat vérifié"
        $stmt = $this->db->prepare("
            SELECT oi.id as order_item_id
            FROM order_items oi
            INNER JOIN orders o ON oi.order_id = o.id
            WHERE oi.product_id = :product_id 
              AND o.buyer_id = :buyer_id
              AND o.payment_status = 'completed'
            LIMIT 1
        ");
        $stmt->execute([
            'product_id' => $productId,
            'buyer_id'   => $buyerId
        ]);
        $purchase    = $stmt->fetch(\PDO::FETCH_ASSOC);
        $isVerified  = $purchase ? true : false;
        $orderItemId = $purchase['order_item_id'] ?? null;

        // Insertion de l’avis en base
        // is_approved = TRUE ici → publication immédiate
        // (peut être changé si tu veux une modération préalable)
        $stmt = $this->db->prepare("
            INSERT INTO reviews 
            (product_id, buyer_id, order_item_id, rating, title, comment, 
             is_verified_purchase, is_approved)
            VALUES 
            (:product_id, :buyer_id, :order_item_id, :rating, :title, :comment, 
             :is_verified, TRUE)
        ");

        $success = $stmt->execute([
            'product_id'   => $productId,
            'buyer_id'     => $buyerId,
            'order_item_id'=> $orderItemId,
            'rating'       => $rating,
            'title'        => $title,
            'comment'      => $comment,
            'is_verified'  => $isVerified
        ]);

        // Redirection avec message utilisateur
        if ($success) {
            redirectWithMessage("/products/{$product['slug']}", 
                'Merci pour votre avis !', 'success');
        } else {
            redirectWithMessage("/products/{$product['slug']}", 
                'Erreur lors de l\'ajout', 'error');
        }
    }

    /**
     * Suppression d’un avis
     * Autorisé pour :
     * - L’auteur de l’avis
     * - Un administrateur
     */
    public function delete($reviewId) {
        $this->requireAuth();
        $buyerId = $_SESSION['user_id'];

        // Récupération de l’avis et du slug produit pour redirection
        $stmt = $this->db->prepare("
            SELECT r.*, p.slug
            FROM reviews r
            INNER JOIN products p ON r.product_id = p.id
            WHERE r.id = :id
        ");
        $stmt->execute(['id' => $reviewId]);
        $review = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$review) {
            redirectWithMessage('/', 'Avis introuvable', 'error');
            return;
        }

        // Vérifie les droits : auteur ou administrateur
        if (
            $review['buyer_id'] != $buyerId 
            && ($_SESSION['user_role'] ?? '') !== 'admin'
        ) {
            redirectWithMessage('/', 'Action non autorisée', 'error');
            return;
        }

        // Suppression définitive de l’avis
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->execute(['id' => $reviewId]);

        redirectWithMessage("/products/{$review['slug']}", 
            'Avis supprimé', 'success');
    }

    /**
     * ADMIN
     * Liste tous les avis en attente de modération
     */
    public function moderate() {
        $this->requireAdmin();

        $stmt = $this->db->query("
            SELECT r.*, p.title as product_title, p.slug, u.username
            FROM reviews r
            INNER JOIN products p ON r.product_id = p.id
            INNER JOIN users u ON r.buyer_id = u.id
            WHERE r.is_approved = FALSE
            ORDER BY r.created_at DESC
        ");
        $pendingReviews = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('admin/reviews/index', [
            'title' => 'Modération des avis',
            'pending_reviews' => $pendingReviews
        ]);
    }

    /**
     * ADMIN : Approuve un avis
     */
    public function approve($reviewId) {
        $this->requireAdmin();

        $stmt = $this->db->prepare("
            UPDATE reviews SET is_approved = TRUE WHERE id = :id
        ");
        $stmt->execute(['id' => $reviewId]);

        redirectWithMessage('/admin/reviews', 'Avis approuvé', 'success');
    }

    /**
     * ADMIN : Rejette (désapprouve) un avis
     */
    public function reject($reviewId) {
        $this->requireAdmin();

        $stmt = $this->db->prepare("
            UPDATE reviews SET is_approved = FALSE WHERE id = :id
        ");
        $stmt->execute(['id' => $reviewId]);

        redirectWithMessage('/admin/reviews', 'Avis rejeté', 'success');
    }
}
