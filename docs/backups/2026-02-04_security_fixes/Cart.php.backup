<?php
namespace App\Models;
use \Core\Database;
/**
 * MARKETFLOW PRO - CART MODEL
 * Gestion du panier en session
 * Fichier : app/models/Cart.php
 */

namespace App\Models;

class Cart {
    private $sessionKey = 'cart';

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialiser le panier si n'existe pas
        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [
                'items' => [],
                'total' => 0,
                'count' => 0
            ];
        }
    }

    /**
     * Ajouter un produit au panier
     */
    public function add($productId, $quantity = 1) {
        // Récupérer les infos du produit
        $product = $this->getProductInfo($productId);
        
        if (!$product) {
            return ['success' => false, 'error' => 'Produit introuvable'];
        }

        // Vérifier que le produit est disponible
            if ($product['status'] !== 'approved') {
            return ['success' => false, 'error' => 'Produit non disponible'];
        }

        // Vérifier si le produit est déjà dans le panier
        if (isset($_SESSION[$this->sessionKey]['items'][$productId])) {
            return ['success' => false, 'error' => 'Produit déjà dans le panier'];
        }

        // Ajouter au panier
        $_SESSION[$this->sessionKey]['items'][$productId] = [
            'product_id' => $product['id'],
            'title' => $product['title'],
            'slug' => $product['slug'],
            'thumbnail' => $product['thumbnail_url'] ?? '/public/img/placeholder.png',
            'price' => $product['price'],
            'seller_id' => $product['seller_id'],
            'seller_name' => $product['seller_name'],
            
            'quantity' => $quantity,
            'added_at' => time()
        ];

        // Mettre à jour le total
        $this->updateTotals();

        return [
            'success' => true,
            'message' => 'Produit ajouté au panier',
            'cart' => $this->get()
        ];
    }

    /**
     * Retirer un produit du panier
     */
    public function remove($productId) {
        if (!isset($_SESSION[$this->sessionKey]['items'][$productId])) {
            return ['success' => false, 'error' => 'Produit non trouvé dans le panier'];
        }

        unset($_SESSION[$this->sessionKey]['items'][$productId]);
        $this->updateTotals();

        return [
            'success' => true,
            'message' => 'Produit retiré du panier',
            'cart' => $this->get()
        ];
    }

    /**
     * Mettre à jour la quantité (pas utilisé pour produits digitaux, mais gardé pour flexibilité)
     */
    public function updateQuantity($productId, $quantity) {
        if (!isset($_SESSION[$this->sessionKey]['items'][$productId])) {
            return ['success' => false, 'error' => 'Produit non trouvé'];
        }

        if ($quantity <= 0) {
            return $this->remove($productId);
        }

        $_SESSION[$this->sessionKey]['items'][$productId]['quantity'] = $quantity;
        $this->updateTotals();

        return [
            'success' => true,
            'cart' => $this->get()
        ];
    }

    /**
     * Vider le panier
     */
    public function clear() {
        $_SESSION[$this->sessionKey] = [
            'items' => [],
            'total' => 0,
            'count' => 0
        ];

        return ['success' => true, 'message' => 'Panier vidé'];
    }

    /**
     * Récupérer le contenu du panier
     */
    public function get() {
        return $_SESSION[$this->sessionKey];
    }

    /**
     * Récupérer le nombre d'articles
     */
    public function count() {
        return $_SESSION[$this->sessionKey]['count'];
    }

    /**
     * Récupérer le total
     */
    public function total() {
        return $_SESSION[$this->sessionKey]['total'];
    }

    /**
     * Vérifier si le panier est vide
     */
    public function isEmpty() {
        return empty($_SESSION[$this->sessionKey]['items']);
    }

    /**
     * Vérifier si un produit est dans le panier
     */
    public function has($productId) {
        return isset($_SESSION[$this->sessionKey]['items'][$productId]);
    }

    /**
     * Récupérer les articles du panier
     */
    public function items() {
        return $_SESSION[$this->sessionKey]['items'];
    }

    /**
     * Mettre à jour les totaux
     */
    private function updateTotals() {
        $total = 0;
        $count = 0;

        foreach ($_SESSION[$this->sessionKey]['items'] as $item) {
            $total += $item['price'] * $item['quantity'];
            $count += $item['quantity'];
        }

        $_SESSION[$this->sessionKey]['total'] = $total;
        $_SESSION[$this->sessionKey]['count'] = $count;
    }

    /**
     * Récupérer les infos d'un produit
     */
    private function getProductInfo($productId) {
        $db = \Core\Database::getInstance();

        $stmt = $db->prepare("
            SELECT p.*, 
                   u.username as seller_name
            FROM products p
            JOIN users u ON p.seller_id = u.id
            WHERE p.id = :product_id
        ");

        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetch();
    }

    /**
     * Calculer les commissions pour checkout
     */
    public function getCheckoutData() {
        $items = $this->items();
        $subtotal = 0;
        $checkoutItems = [];

        foreach ($items as $item) {
            
            
            // Commission par défaut
            $commissionRate = PLATFORM_COMMISSION; // 10%
            $commissionAmount = ($item['price'] * $commissionRate) / 100;
            $sellerAmount = $item['price'] - $commissionAmount;

            $checkoutItems[] = [
                'product_id' => $item['product_id'],
                'title' => $item['title'],
                'slug' => $item['slug'],
                'thumbnail' => $item['thumbnail'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'seller_id' => $item['seller_id'],
                'seller_name' => $item['seller_name'],
                
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'seller_amount' => $sellerAmount
            ];

            $subtotal += $item['price'] * $item['quantity'];
        }

        return [
            'items' => $checkoutItems,
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'count' => $this->count()
        ];
    }

    /**
     * Valider le panier avant checkout
     */
    public function validate() {
        $errors = [];
        
        if ($this->isEmpty()) {
            $errors[] = 'Votre panier est vide';
        }

        // Vérifier que tous les produits sont toujours disponibles
        $db = \Core\Database::getInstance()->getPdo();
        
        foreach ($this->items() as $productId => $item) {
            $stmt = $db->prepare("
                SELECT status 
                FROM products 
                WHERE id = ?
            ");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();

            if (!$product || $product["status"] !== "approved") {
                $errors[] = "Le produit \"{$item['title']}\" n'est plus disponible";
                $this->remove($productId);
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Appliquer un code promo
     */
    public function applyPromoCode($code) {
        $db = \Core\Database::getInstance()->getPdo();
        
        $stmt = $db->prepare("
            SELECT * FROM promo_codes
            WHERE code = ? 
              AND (expires_at IS NULL OR expires_at > NOW())
              AND (max_uses IS NULL OR used_count < max_uses)
        ");
        
        $stmt->execute([strtoupper($code)]);
        $promo = $stmt->fetch();

        if (!$promo) {
            return ['success' => false, 'error' => 'Code promo invalide ou expiré'];
        }

        // Vérifier le montant minimum
        if ($this->total() < $promo['min_purchase']) {
            return [
                'success' => false,
                'error' => 'Montant minimum de ' . formatPrice($promo['min_purchase']) . ' requis'
            ];
        }

        // Calculer la réduction
        if ($promo['type'] === 'percentage') {
            $discount = ($this->total() * $promo['value']) / 100;
        } else {
            $discount = $promo['value'];
        }

        // Ne pas dépasser le total
        $discount = min($discount, $this->total());

        $_SESSION[$this->sessionKey]['promo'] = [
            'code' => $promo['code'],
            'type' => $promo['type'],
            'value' => $promo['value'],
            'discount' => $discount
        ];

        return [
            'success' => true,
            'message' => 'Code promo appliqué',
            'discount' => $discount,
            'new_total' => $this->total() - $discount
        ];
    }

    /**
     * Retirer le code promo
     */
    public function removePromoCode() {
        unset($_SESSION[$this->sessionKey]['promo']);
        return ['success' => true];
    }

    /**
     * Récupérer le code promo actif
     */
    public function getPromoCode() {
        return $_SESSION[$this->sessionKey]['promo'] ?? null;
    }

    /**
     * Récupérer le total avec promo
     */
    public function getTotalWithPromo() {
        $total = $this->total();
        $promo = $this->getPromoCode();

        if ($promo) {
            return $total - $promo['discount'];
        }

        return $total;
    }
}