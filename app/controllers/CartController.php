<?php
/**
 * MARKETFLOW PRO - CART CONTROLLER
 * Gestion du panier
 * Fichier : app/controllers/CartController.php
 */

namespace App\Controllers;

use Core\Controller;
use App\Models\Cart;
use App\Models\Order;

class CartController extends Controller {
    private $cart;

    public function __construct() {
        parent::__construct();
        $this->cart = new Cart();
    }

    /**
     * Afficher le panier
     */
    public function index() {
        $cartData = $this->cart->getCheckoutData();
        $promo = $this->cart->getPromoCode();

        $this->view('cart/index', [
            'title' => 'Mon Panier',
            'cart' => $cartData,
            'promo' => $promo,
            'csrf_token' => generateCsrfToken()
        ]);
    }

    /**
     * Ajouter au panier (AJAX ou POST)
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/products');
        }

        // Vérifier CSRF
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Token invalide'], 403);
            }
            redirectWithMessage('/products', 'Erreur de sécurité', 'error');
            return;
        }

        $productId = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;

        if (!$productId) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Produit invalide'], 400);
            }
            redirectWithMessage('/products', 'Produit invalide', 'error');
            return;
        }

        $result = $this->cart->add($productId, $quantity);

        if ($this->isAjax()) {
            $this->json($result);
        }

        if ($result['success']) {
            redirectWithMessage('/cart', $result['message'], 'success');
        } else {
            redirectWithMessage($_SERVER['HTTP_REFERER'] ?? '/products', $result['error'], 'error');
        }
    }

    /**
     * Retirer du panier
     */
    public function remove() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart');
        }

        $productId = $_POST['product_id'] ?? null;

        if (!$productId) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Produit invalide'], 400);
            }
            redirectWithMessage('/cart', 'Produit invalide', 'error');
            return;
        }

        $result = $this->cart->remove($productId);

        if ($this->isAjax()) {
            $this->json($result);
        }

        redirectWithMessage('/cart', $result['message'], $result['success'] ? 'success' : 'error');
    }

    /**
     * Mettre à jour la quantité
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart');
        }

        $productId = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;

        if (!$productId) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Produit invalide'], 400);
            }
            redirectWithMessage('/cart', 'Produit invalide', 'error');
            return;
        }

        $result = $this->cart->updateQuantity($productId, $quantity);

        if ($this->isAjax()) {
            $this->json($result);
        }

        redirectWithMessage('/cart', 'Panier mis à jour', 'success');
    }

    /**
     * Vider le panier
     */
    public function clear() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart');
        }

        $result = $this->cart->clear();

        if ($this->isAjax()) {
            $this->json($result);
        }

        redirectWithMessage('/cart', 'Panier vidé', 'success');
    }

    /**
     * Appliquer un code promo
     */
    public function applyPromo() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart');
        }

        $code = $_POST['promo_code'] ?? '';

        if (empty($code)) {
            redirectWithMessage('/cart', 'Veuillez entrer un code promo', 'error');
            return;
        }

        $result = $this->cart->applyPromoCode($code);

        redirectWithMessage('/cart', 
            $result['success'] ? $result['message'] : $result['error'],
            $result['success'] ? 'success' : 'error'
        );
    }

    /**
     * Retirer le code promo
     */
    public function removePromo() {
        $this->cart->removePromoCode();
        redirectWithMessage('/cart', 'Code promo retiré', 'success');
    }

    /**
     * Page de checkout
     */
    public function checkout() {
        // Vérifier que l'utilisateur est connecté
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = '/checkout';
            redirectWithMessage('/login', 'Veuillez vous connecter pour continuer', 'info');
            return;
        }

        // Valider le panier
        $validation = $this->cart->validate();
        
        if (!$validation['valid']) {
            redirectWithMessage('/cart', implode('<br>', $validation['errors']), 'error');
            return;
        }

        $cartData = $this->cart->getCheckoutData();
        $promo = $this->cart->getPromoCode();
        $user = $this->getCurrentUser();

        // Calculer le total final
        $subtotal = $cartData['total'];
        $discount = $promo ? $promo['discount'] : 0;
        $total = $subtotal - $discount;

        $this->view('cart/checkout', [
            'title' => 'Paiement',
            'cart' => $cartData,
            'promo' => $promo,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'user' => $user,
            'stripe_public_key' => STRIPE_PUBLIC_KEY,
            'csrf_token' => generateCsrfToken()
        ]);
    }

    /**
     * Traiter le paiement Stripe
     */
    public function processCheckout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/checkout');
        }

        // Vérifier connexion
        if (!$this->isLoggedIn()) {
            $this->json(['success' => false, 'error' => 'Connexion requise'], 401);
        }

        // Vérifier CSRF
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            $this->json(['success' => false, 'error' => 'Token invalide'], 403);
        }

        // Valider le panier
        $validation = $this->cart->validate();
        if (!$validation['valid']) {
            $this->json(['success' => false, 'error' => implode(', ', $validation['errors'])], 400);
        }

        // Récupérer les données
        $cartData = $this->cart->getCheckoutData();
        $promo = $this->cart->getPromoCode();
        $total = $this->cart->getTotalWithPromo();

        // Créer la commande en "pending"
        $orderModel = new Order();
        $orderData = [
            'buyer_id' => $_SESSION['user_id'],
            'total_amount' => $total,
            'items' => $cartData['items'],
            'promo_code' => $promo ? $promo['code'] : null,
            'discount_amount' => $promo ? $promo['discount'] : 0
        ];

        $orderResult = $orderModel->create($orderData);

        if (!$orderResult['success']) {
            $this->json(['success' => false, 'error' => 'Erreur lors de la création de la commande'], 500);
        }

        // Créer la session Stripe
        try {
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

            $lineItems = [];
            foreach ($cartData['items'] as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => strtolower(CURRENCY),
                        'product_data' => [
                            'name' => $item['title'],
                            'images' => [$item['thumbnail']],
                        ],
                        'unit_amount' => round($item['price'] * 100), // Stripe utilise les centimes
                    ],
                    'quantity' => $item['quantity'],
                ];
            }

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => APP_URL . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => APP_URL . '/payment/cancel',
                'client_reference_id' => $orderResult['order_id'],
                'customer_email' => $this->getCurrentUser()['email'],
                'metadata' => [
                    'order_id' => $orderResult['order_id'],
                    'user_id' => $_SESSION['user_id']
                ]
            ]);

            $this->json([
                'success' => true,
                'session_id' => $session->id,
                'order_id' => $orderResult['order_id']
            ]);

        } catch (\Exception $e) {
            // Supprimer la commande en cas d'erreur
            $orderModel->delete($orderResult['order_id']);
            
            $this->json([
                'success' => false,
                'error' => 'Erreur lors de la création de la session de paiement'
            ], 500);
        }
    }

    /**
     * Récupérer le panier (AJAX)
     */
    public function getCart() {
        $cartData = $this->cart->getCheckoutData();
        $promo = $this->cart->getPromoCode();

        $this->json([
            'success' => true,
            'cart' => $cartData,
            'promo' => $promo,
            'total' => $this->cart->getTotalWithPromo()
        ]);
    }

    /**
     * Vérifier si la requête est AJAX
     */
    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}