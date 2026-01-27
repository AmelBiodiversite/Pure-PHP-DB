<?php
/**
 * ============================================================================
 * MARKETFLOW PRO - CART CONTROLLER (VERSION SÉCURISÉE)
 * ============================================================================
 * Gestion complète du panier avec protection CSRF sur TOUTES les actions
 * 
 * SÉCURITÉ IMPLÉMENTÉE :
 * - Protection CSRF sur tous les POST
 * - Validation des méthodes HTTP
 * - Vérification de l'authentification
 * - Validation des données entrantes
 * - Gestion des erreurs avec try/catch
 * 
 * Fichier : app/controllers/CartController.php
 * ============================================================================
 */

namespace App\Controllers;

use Core\Controller;
use Core\CSRF; // Utilisation de la classe CSRF
use App\Models\Cart;
use App\Models\Order;

class CartController extends Controller {
    private $cart;

    public function __construct() {
        parent::__construct();
        $this->cart = new Cart();
    }

    /**
     * ========================================================================
     * AFFICHER LE PANIER
     * ========================================================================
     * Page principale du panier avec tous les produits ajoutés
     * Génère un token CSRF pour les formulaires de la page
     */
    public function index() {
        // Récupérer les données du panier (produits, quantités, prix)
        $cartData = $this->cart->getCheckoutData();

        // Récupérer le code promo actif s'il y en a un
        $promo = $this->cart->getPromoCode();

        // Afficher la vue avec toutes les données nécessaires
        $this->render('cart/index', [
            'title' => 'Mon Panier',
            'cart' => $cartData,
            'promo' => $promo,
            'csrf_token' => CSRF::generateToken() // Token CSRF pour les formulaires
        ]);
    }

    /**
     * ========================================================================
     * AJOUTER UN PRODUIT AU PANIER
     * ========================================================================
     * 🔒 PROTÉGÉ PAR CSRF
     * Ajoute un produit au panier (via formulaire ou AJAX)
     */
    public function add() {
        // 1️⃣ VÉRIFICATION DE LA MÉTHODE HTTP
        // Accepter uniquement les requêtes POST (pas GET, PUT, DELETE)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/products');
            return;
        }

        // 2️⃣ VALIDATION DU TOKEN CSRF (SÉCURITÉ CRITIQUE)
        // Récupérer le token depuis le formulaire ($_POST) ou les headers AJAX
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        // Valider que le token correspond à celui en session
        if (!CSRF::validateToken($token)) {
            // Si requête AJAX : réponse JSON avec erreur 403
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Token de sécurité invalide'], 403);
                return;
            }
            // Sinon : redirection avec message d'erreur
            redirectWithMessage('/products', 'Erreur de sécurité. Veuillez recharger la page.', 'error');
            return;
        }

        // 3️⃣ RÉCUPÉRATION ET VALIDATION DES DONNÉES
        // Récupérer l'ID du produit et la quantité depuis le formulaire
        $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
        $quantity = filter_var($_POST['quantity'] ?? 1, FILTER_VALIDATE_INT);

        // Vérifier que l'ID du produit est valide
        if (!$productId || $quantity < 1) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Produit ou quantité invalide'], 400);
                return;
            }
            redirectWithMessage('/products', 'Données invalides', 'error');
            return;
        }

        // 4️⃣ AJOUT AU PANIER
        // Appeler la méthode du modèle Cart pour ajouter le produit
        $result = $this->cart->add($productId, $quantity);

        // 5️⃣ RÉPONSE SELON LE TYPE DE REQUÊTE
        if ($this->isAjax()) {
            // Requête AJAX : réponse JSON
            $this->json($result);
            return;
        }

        // Requête normale : redirection avec message flash
        if ($result['success']) {
            redirectWithMessage('/cart', $result['message'], 'success');
        } else {
            redirectWithMessage($_SERVER['HTTP_REFERER'] ?? '/products', $result['error'], 'error');
        }
    }

    /**
     * ========================================================================
     * RETIRER UN PRODUIT DU PANIER
     * ========================================================================
     * 🔒 PROTÉGÉ PAR CSRF
     * Supprime complètement un produit du panier
     */
    public function remove() {
        // 1️⃣ VÉRIFICATION DE LA MÉTHODE HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart');
            return;
        }

        // 2️⃣ VALIDATION DU TOKEN CSRF
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!CSRF::validateToken($token)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Token de sécurité invalide'], 403);
                return;
            }
            redirectWithMessage('/cart', 'Erreur de sécurité', 'error');
            return;
        }

        // 3️⃣ RÉCUPÉRATION ET VALIDATION DES DONNÉES
        $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);

        if (!$productId) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Produit invalide'], 400);
                return;
            }
            redirectWithMessage('/cart', 'Produit invalide', 'error');
            return;
        }

        // 4️⃣ SUPPRESSION DU PRODUIT
        $result = $this->cart->remove($productId);

        // 5️⃣ RÉPONSE
        if ($this->isAjax()) {
            $this->json($result);
            return;
        }

        redirectWithMessage('/cart', $result['message'], $result['success'] ? 'success' : 'error');
    }

    /**
     * ========================================================================
     * METTRE À JOUR LA QUANTITÉ D'UN PRODUIT
     * ========================================================================
     * 🔒 PROTÉGÉ PAR CSRF
     * Modifie la quantité d'un produit déjà dans le panier
     */
    public function update() {
        // 1️⃣ VÉRIFICATION DE LA MÉTHODE HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart');
            return;
        }

        // 2️⃣ VALIDATION DU TOKEN CSRF
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!CSRF::validateToken($token)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Token de sécurité invalide'], 403);
                return;
            }
            redirectWithMessage('/cart', 'Erreur de sécurité', 'error');
            return;
        }

        // 3️⃣ RÉCUPÉRATION ET VALIDATION DES DONNÉES
        $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
        $quantity = filter_var($_POST['quantity'] ?? 1, FILTER_VALIDATE_INT);

        if (!$productId || $quantity < 1) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Données invalides'], 400);
                return;
            }
            redirectWithMessage('/cart', 'Données invalides', 'error');
            return;
        }

        // 4️⃣ MISE À JOUR DE LA QUANTITÉ
        $result = $this->cart->updateQuantity($productId, $quantity);

        // 5️⃣ RÉPONSE
        if ($this->isAjax()) {
            $this->json($result);
            return;
        }

        redirectWithMessage('/cart', 'Panier mis à jour', 'success');
    }

    /**
     * ========================================================================
     * VIDER LE PANIER COMPLÈTEMENT
     * ========================================================================
     * 🔒 PROTÉGÉ PAR CSRF
     * Supprime tous les produits du panier d'un coup
     */
    public function clear() {
        // 1️⃣ VÉRIFICATION DE LA MÉTHODE HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart');
            return;
        }

        // 2️⃣ VALIDATION DU TOKEN CSRF
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!CSRF::validateToken($token)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Token de sécurité invalide'], 403);
                return;
            }
            redirectWithMessage('/cart', 'Erreur de sécurité', 'error');
            return;
        }

        // 3️⃣ VIDAGE DU PANIER
        $result = $this->cart->clear();

        // 4️⃣ RÉPONSE
        if ($this->isAjax()) {
            $this->json($result);
            return;
        }

        redirectWithMessage('/cart', 'Panier vidé avec succès', 'success');
    }

    /**
     * ========================================================================
     * APPLIQUER UN CODE PROMO
     * ========================================================================
     * 🔒 PROTÉGÉ PAR CSRF
     * Vérifie et applique un code de réduction au panier
     */
    public function applyPromo() {
        // 1️⃣ VÉRIFICATION DE LA MÉTHODE HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart');
            return;
        }

        // 2️⃣ VALIDATION DU TOKEN CSRF
        $token = $_POST['csrf_token'] ?? '';

        if (!CSRF::validateToken($token)) {
            redirectWithMessage('/cart', 'Erreur de sécurité', 'error');
            return;
        }

        // 3️⃣ RÉCUPÉRATION ET VALIDATION DU CODE
        // Nettoyer le code promo (supprimer espaces, mettre en majuscules)
        $code = strtoupper(trim($_POST['promo_code'] ?? ''));

        if (empty($code)) {
            redirectWithMessage('/cart', 'Veuillez entrer un code promo', 'error');
            return;
        }

        // 4️⃣ APPLICATION DU CODE PROMO
        // Le modèle va vérifier si le code existe et est valide
        $result = $this->cart->applyPromoCode($code);

        // 5️⃣ RÉPONSE
        redirectWithMessage('/cart', 
            $result['success'] ? $result['message'] : $result['error'],
            $result['success'] ? 'success' : 'error'
        );
    }

    /**
     * ========================================================================
     * RETIRER LE CODE PROMO
     * ========================================================================
     * 🔒 PROTÉGÉ PAR CSRF
     * Supprime le code promo appliqué au panier
     */
    public function removePromo() {
        // 1️⃣ VÉRIFICATION DE LA MÉTHODE HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart');
            return;
        }

        // 2️⃣ VALIDATION DU TOKEN CSRF
        $token = $_POST['csrf_token'] ?? '';

        if (!CSRF::validateToken($token)) {
            redirectWithMessage('/cart', 'Erreur de sécurité', 'error');
            return;
        }

        // 3️⃣ SUPPRESSION DU CODE PROMO
        $this->cart->removePromoCode();

        // 4️⃣ RÉPONSE
        redirectWithMessage('/cart', 'Code promo retiré', 'success');
    }

    /**
     * ========================================================================
     * PAGE DE CHECKOUT (PAIEMENT)
     * ========================================================================
     * Affiche la page de paiement avec récapitulatif et formulaire Stripe
     * Vérifie que l'utilisateur est connecté et que le panier est valide
     */
    public function checkout() {
        // 1️⃣ VÉRIFICATION DE L'AUTHENTIFICATION
        // Rediriger vers login si pas connecté, avec retour automatique après login
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = '/checkout';
            redirectWithMessage('/login', 'Veuillez vous connecter pour continuer', 'info');
            return;
        }

        // 2️⃣ VALIDATION DU PANIER
        // Vérifier que le panier n'est pas vide et que les produits sont disponibles
        $validation = $this->cart->validate();

        if (!$validation['valid']) {
            // Afficher toutes les erreurs de validation
            redirectWithMessage('/cart', implode('<br>', $validation['errors']), 'error');
            return;
        }

        // 3️⃣ RÉCUPÉRATION DES DONNÉES
        $cartData = $this->cart->getCheckoutData();
        $promo = $this->cart->getPromoCode();
        $user = $this->getCurrentUser();

        // 4️⃣ CALCUL DU TOTAL AVEC RÉDUCTION
        $subtotal = $cartData['total'];
        $discount = $promo ? $promo['discount'] : 0;
        $total = $subtotal - $discount;

        // 5️⃣ AFFICHAGE DE LA PAGE DE PAIEMENT
        $this->view('cart/checkout', [
            'title' => 'Paiement',
            'cart' => $cartData,
            'promo' => $promo,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'user' => $user,
            'stripe_public_key' => STRIPE_PUBLIC_KEY, // Clé publique Stripe pour le frontend
            'csrf_token' => CSRF::generateToken() // Token pour le formulaire de paiement
        ]);
    }

    /**
     * ========================================================================
     * TRAITER LE PAIEMENT STRIPE
     * ========================================================================
     * 🔒 PROTÉGÉ PAR CSRF
     * Crée la session de paiement Stripe et redirige vers la page de paiement
     * Cette méthode est appelée en AJAX depuis la page checkout
     */
    public function processCheckout() {
        // 1️⃣ VÉRIFICATION DE LA MÉTHODE HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/checkout');
            return;
        }

        // 2️⃣ VÉRIFICATION DE L'AUTHENTIFICATION
        if (!$this->isLoggedIn()) {
            $this->json(['success' => false, 'error' => 'Connexion requise'], 401);
            return;
        }

        // 3️⃣ VALIDATION DU TOKEN CSRF
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!CSRF::validateToken($token)) {
            $this->json(['success' => false, 'error' => 'Token de sécurité invalide'], 403);
            return;
        }

        // 4️⃣ VALIDATION DU PANIER
        $validation = $this->cart->validate();
        if (!$validation['valid']) {
            $this->json(['success' => false, 'error' => implode(', ', $validation['errors'])], 400);
            return;
        }

        // 5️⃣ RÉCUPÉRATION DES DONNÉES
        $cartData = $this->cart->getCheckoutData();
        $promo = $this->cart->getPromoCode();
        $total = $this->cart->getTotalWithPromo();

        // 6️⃣ CRÉATION DE LA COMMANDE EN BASE DE DONNÉES (statut: pending)
        // La commande sera marquée "paid" après confirmation Stripe
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
            return;
        }

        // 7️⃣ CRÉATION DE LA SESSION STRIPE
        try {
            // Initialiser Stripe avec la clé secrète
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

            // Construire les lignes de produits pour Stripe
            $lineItems = [];
            foreach ($cartData['items'] as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => strtolower(CURRENCY), // ex: 'eur', 'usd'
                        'product_data' => [
                            'name' => $item['title'],
                            'images' => [$item['thumbnail']], // Image du produit
                        ],
                        'unit_amount' => round($item['price'] * 100), // Stripe utilise les centimes (ex: 29.99€ = 2999)
                    ],
                    'quantity' => $item['quantity'],
                ];
            }

            // Créer la session de paiement Stripe
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'], // Accepter les cartes bancaires
                'line_items' => $lineItems,
                'mode' => 'payment', // Paiement unique (pas abonnement)
                'success_url' => APP_URL . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => APP_URL . '/payment/cancel',
                'client_reference_id' => $orderResult['order_id'], // Lier la commande à la session
                'customer_email' => $this->getCurrentUser()['email'],
                'metadata' => [
                    'order_id' => $orderResult['order_id'],
                    'user_id' => $_SESSION['user_id']
                ]
            ]);

            // 8️⃣ RÉPONSE AVEC L'ID DE SESSION
            // Le frontend utilisera cet ID pour rediriger vers Stripe
            $this->json([
                'success' => true,
                'session_id' => $session->id,
                'order_id' => $orderResult['order_id']
            ]);

        } catch (\Exception $e) {
            // 9️⃣ GESTION DES ERREURS STRIPE
            // Supprimer la commande si la session Stripe a échoué
            $orderModel->delete($orderResult['order_id']);

            // Log l'erreur pour debugging (dans un vrai projet)
            error_log('Stripe Error: ' . $e->getMessage());

            $this->json([
                'success' => false,
                'error' => 'Erreur lors de la création de la session de paiement'
            ], 500);
        }
    }

    /**
     * ========================================================================
     * RÉCUPÉRER LE PANIER (API AJAX)
     * ========================================================================
     * Retourne les données du panier en JSON pour les mises à jour dynamiques
     * Utilisé par le frontend pour afficher le compteur de panier, etc.
     */
    public function getCart() {
        // Récupérer toutes les données du panier
        $cartData = $this->cart->getCheckoutData();
        $promo = $this->cart->getPromoCode();

        // Réponse JSON avec les données
        $this->json([
            'success' => true,
            'cart' => $cartData,
            'promo' => $promo,
            'total' => $this->cart->getTotalWithPromo()
        ]);
    }

    /**
     * ========================================================================
     * VÉRIFIER SI LA REQUÊTE EST AJAX
     * ========================================================================
     * Méthode utilitaire pour détecter les requêtes AJAX (XMLHttpRequest)
     * Permet d'adapter la réponse (JSON vs HTML)
     */
    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}