<?php
namespace App\Controllers;

use Core\Controller;

class StripeController extends Controller {
    
    /**
     * Créer une session de paiement Stripe
     */
    public function createCheckoutSession() {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $this->json(['error' => 'Non authentifié'], 401);
            return;
        }
        
        // Récupérer le panier
        $cartModel = new \App\Models\Cart();
        $cartData = $cartModel->getCheckoutData();
        
        if (empty($cartData['items'])) {
            $this->json(['error' => 'Panier vide'], 400);
            return;
        }
        
        // Préparer les line items pour Stripe
        $lineItems = [];
        foreach ($cartData['items'] as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['title'],
                        'description' => 'Produit digital',
                        'images' => [$item['thumbnail_url'] ?? ''],
                    ],
                    'unit_amount' => (int)($item['price'] * 100), // Centimes
                ],
                'quantity' => 1,
            ];
        }
        
        // Clés Stripe (à configurer dans config.php)
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY ?? 'sk_test_VOTRE_CLE');
        
        try {
            // Créer la session Stripe
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => APP_URL . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => APP_URL . '/cart',
                'customer_email' => $_SESSION['user_email'] ?? '',
                'metadata' => [
                    'user_id' => $_SESSION['user_id'],
                    'cart_items' => json_encode(array_column($cartData['items'], 'product_id'))
                ],
            ]);
            
            // Retourner l'URL de checkout
            $this->json([
                'success' => true,
                'checkout_url' => $session->url
            ]);
            
        } catch (\Exception $e) {
            $this->json([
                'error' => 'Erreur Stripe: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Page de succès après paiement
     */
    public function success() {
        $sessionId = $_GET['session_id'] ?? '';
        
        if (!$sessionId) {
            redirectWithMessage('/', 'Session invalide', 'error');
            return;
        }
        
        // Vider le panier
        $cartModel = new \App\Models\Cart();
        $cartModel->clear();
        
        $this->view('checkout/success', [
            'title' => 'Paiement réussi !',
            'session_id' => $sessionId
        ]);
    }
}