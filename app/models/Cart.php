<?php
namespace App\Models;
use \Core\Database;

/**
 * MARKETFLOW PRO - CART MODEL (VERSION SÉCURISÉE)
 * Gestion du panier en session avec validation prix temps réel
 * 
 * SÉCURITÉ: Les prix sont TOUJOURS vérifiés depuis la BDD avant checkout
 * pour éviter toute manipulation via session
 * 
 * Fichier : app/models/Cart.php
 * Version : 2.0 - Sécurisée
 * Date : 2026-02-04
 */

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
            'price' => $product['price'], // Prix initial (sera re-vérifié au checkout)
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
     * Mettre à jour la quantité
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

    public function get() {
        return $_SESSION[$this->sessionKey];
    }

    public function count() {
        return $_SESSION[$this->sessionKey]['count'];
    }

    public function total() {
        return $_SESSION[$this->sessionKey]['total'];
    }

    public function isEmpty() {
        return empty($_SESSION[$this->sessionKey]['items']);
    }

    public function has($productId) {
        return isset($_SESSION[$this->sessionKey]['items'][$productId]);
    }

    public function items() {
        return $_SESSION[$this->sessionKey]['items'];
    }

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
     * ✅ MÉTHODE SÉCURISÉE: Calculer les données de checkout avec PRIX BDD ACTUELS
     * 
     * SÉCURITÉ CRITIQUE:
     * - Re-récupère les prix depuis la BDD (ignore les prix session)
     * - Vérifie la disponibilité de chaque produit
     * - Log les différences de prix pour détecter fraudes
     */
    public function getCheckoutData() {
        $sessionItems = $this->items();
        $secureItems = [];
        $subtotal = 0;
        $errors = [];
        $warnings = [];

        $db = \Core\Database::getInstance();

        foreach ($sessionItems as $productId => $item) {
            // ✅ CORRECTION: Re-récupérer le produit depuis la BDD
            $stmt = $db->prepare("
                SELECT p.id, p.title, p.slug, p.thumbnail_url, p.price, 
                       p.status, p.seller_id, u.username as seller_name
                FROM products p
                JOIN users u ON p.seller_id = u.id
                WHERE p.id = :product_id
            ");
            $stmt->execute(['product_id' => $productId]);
            $currentProduct = $stmt->fetch();

            if (!$currentProduct) {
                $errors[] = "Le produit \"{$item['title']}\" n'est plus disponible";
                continue;
            }

            if ($currentProduct['status'] !== 'approved') {
                $errors[] = "Le produit \"{$currentProduct['title']}\" n'est plus disponible à la vente";
                continue;
            }

            // ✅ SÉCURITÉ: Utiliser le prix ACTUEL de la BDD
            $actualPrice = (float)$currentProduct['price'];
            $sessionPrice = (float)$item['price'];

            // Détecter différences de prix
            if (abs($actualPrice - $sessionPrice) > 0.01) {
                $priceDiff = $actualPrice - $sessionPrice;
                $percentDiff = (($priceDiff / $sessionPrice) * 100);
                
                error_log(sprintf(
                    "PRICE_MISMATCH: Product #%d '%s' | Session: %.2f € | Current: %.2f € | Diff: %.2f € (%.1f%%) | User: %s",
                    $productId,
                    $currentProduct['title'],
                    $sessionPrice,
                    $actualPrice,
                    $priceDiff,
                    $percentDiff,
                    $_SESSION['user_id'] ?? 'guest'
                ));

                if ($priceDiff > 0) {
                    $warnings[] = "Le prix de \"{$currentProduct['title']}\" a augmenté de " . 
                                  number_format($priceDiff, 2) . " €";
                } else {
                    $warnings[] = "Le prix de \"{$currentProduct['title']}\" a baissé de " . 
                                  number_format(abs($priceDiff), 2) . " €";
                }
            }

            $commissionRate = defined('PLATFORM_COMMISSION') ? PLATFORM_COMMISSION : 10;
            $commissionAmount = ($actualPrice * $commissionRate) / 100;
            $sellerAmount = $actualPrice - $commissionAmount;

            $secureItems[] = [
                'product_id' => $currentProduct['id'],
                'title' => $currentProduct['title'],
                'slug' => $currentProduct['slug'],
                'thumbnail' => $currentProduct['thumbnail_url'] ?? '/public/img/placeholder.png',
                'price' => $actualPrice,  // ✅ Prix actuel BDD
                'quantity' => $item['quantity'],
                'seller_id' => $currentProduct['seller_id'],
                'seller_name' => $currentProduct['seller_name'],
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'seller_amount' => $sellerAmount
            ];

            $subtotal += $actualPrice * $item['quantity'];
        }

        $promo = $this->getPromoCode();
        $discount = 0;

        if ($promo && !empty($secureItems)) {
            if ($promo['type'] === 'percentage') {
                $discount = ($subtotal * $promo['value']) / 100;
            } else {
                $discount = $promo['value'];
            }
            $discount = min($discount, $subtotal);
        }

        $total = max(0, $subtotal - $discount);

        return [
            'items' => $secureItems,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'promo_code' => $promo['code'] ?? null,
            'total' => $total,
            'count' => count($secureItems),
            'errors' => $errors,
            'warnings' => $warnings,
            'needs_revalidation' => !empty($errors)
        ];
    }

    /**
     * ✅ NOUVELLE: Valider le panier AVANT paiement
     */
    public function validateForCheckout() {
        if ($this->isEmpty()) {
            return [
                'valid' => false,
                'data' => null,
                'errors' => ['Votre panier est vide'],
                'warnings' => []
            ];
        }

        $checkoutData = $this->getCheckoutData();

        if (!empty($checkoutData['errors'])) {
            return [
                'valid' => false,
                'data' => null,
                'errors' => $checkoutData['errors'],
                'warnings' => $checkoutData['warnings']
            ];
        }

        if (empty($checkoutData['items'])) {
            return [
                'valid' => false,
                'data' => null,
                'errors' => ['Aucun produit valide dans le panier'],
                'warnings' => []
            ];
        }

        return [
            'valid' => true,
            'data' => $checkoutData,
            'errors' => [],
            'warnings' => $checkoutData['warnings']
        ];
    }

    /**
     * ✅ NOUVELLE: Rafraîchir les prix depuis la BDD
     */
    public function refreshPrices() {
        $cart = $_SESSION[$this->sessionKey]['items'] ?? [];
        $db = \Core\Database::getInstance();
        $updatedCount = 0;

        foreach ($cart as $productId => $item) {
            $stmt = $db->prepare("
                SELECT price, status 
                FROM products 
                WHERE id = :product_id
            ");
            $stmt->execute(['product_id' => $productId]);
            $product = $stmt->fetch();

            if ($product && $product['status'] === 'approved') {
                if (abs((float)$product['price'] - (float)$item['price']) > 0.01) {
                    $_SESSION[$this->sessionKey]['items'][$productId]['price'] = $product['price'];
                    $updatedCount++;
                }
            } else {
                unset($_SESSION[$this->sessionKey]['items'][$productId]);
                $updatedCount++;
            }
        }

        if ($updatedCount > 0) {
            $this->updateTotals();
        }

        return [
            'updated' => $updatedCount,
            'message' => $updatedCount > 0 ? 
                "Les prix de {$updatedCount} produit(s) ont été mis à jour" : 
                "Tous les prix sont à jour"
        ];
    }

    public function validate() {
        $errors = [];
        
        if ($this->isEmpty()) {
            $errors[] = 'Votre panier est vide';
        }

        $db = \Core\Database::getInstance()->getPdo();
        
        foreach ($this->items() as $productId => $item) {
            $stmt = $db->prepare("SELECT status FROM products WHERE id = ?");
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

        if ($this->total() < $promo['min_purchase']) {
            return [
                'success' => false,
                'error' => 'Montant minimum de ' . formatPrice($promo['min_purchase']) . ' requis'
            ];
        }

        if ($promo['type'] === 'percentage') {
            $discount = ($this->total() * $promo['value']) / 100;
        } else {
            $discount = $promo['value'];
        }

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

    public function removePromoCode() {
        unset($_SESSION[$this->sessionKey]['promo']);
        return ['success' => true];
    }

    public function getPromoCode() {
        return $_SESSION[$this->sessionKey]['promo'] ?? null;
    }

    public function getTotalWithPromo() {
        $total = $this->total();
        $promo = $this->getPromoCode();

        if ($promo) {
            return $total - $promo['discount'];
        }

        return $total;
    }
}
