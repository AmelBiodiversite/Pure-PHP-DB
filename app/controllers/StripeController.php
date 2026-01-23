<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Cart;
use App\Models\Order;

class StripeController extends Controller {

    /**
     * Créer une session de paiement Stripe
     */
    public function createCheckoutSession() {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['error' => 'Non authentifié'], 401);
            return;
        }

        // Récupérer le panier
        $cartModel = new Cart();
        $cartData = $cartModel->getCheckoutData();

        if (empty($cartData['items'])) {
            $this->jsonResponse(['error' => 'Panier vide'], 400);
            return;
        }

        // Valider le panier
        $validation = $cartModel->validate();
        if (!$validation['valid']) {
            $this->jsonResponse(['error' => implode(', ', $validation['errors'])], 400);
            return;
        }

        // Récupérer le code promo si présent
        $promo = $cartModel->getPromoCode();
        $total = $cartModel->getTotalWithPromo();

        try {
            // 🔥 ÉTAPE CRITIQUE : CRÉER LA COMMANDE EN BASE D'ABORD
            $orderModel = new Order();

            // Préparer les données de la commande
            $orderData = [
                'buyer_id' => $_SESSION['user_id'],
                'subtotal' => $cartData['subtotal'],
                'discount' => $promo ? $promo['discount'] : 0,
                'total_amount' => $total,
                'promo_code_id' => null,
                'promo_discount' => $promo ? $promo['discount'] : 0,
                'payment_status' => 'pending',
                'status' => 'pending'
            ];

            // Créer la commande avec ses items
            $order = $orderModel->createOrderWithItems($orderData, $cartData['items']);

            if (!$order) {
                throw new \Exception('Impossible de créer la commande');
            }

            // Logger pour debug
            error_log("✅ Commande créée : ID={$order['id']}, Number={$order['order_number']}");

            // Créer la session Stripe
            require_once __DIR__ . '/../../vendor/stripe/stripe-php/init.php';
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

            // Préparer les line items pour Stripe
            $lineItems = [];
            foreach ($cartData['items'] as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $item['title'],
                            'description' => 'Produit digital',
                        ],
                        'unit_amount' => (int)($item['price'] * 100),
                    ],
                    'quantity' => 1,
                ];
            }

            // Créer la session Stripe avec l'order_id dans metadata
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => APP_URL . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => APP_URL . '/cart',
                'customer_email' => $_SESSION['user_email'] ?? '',
                'metadata' => [
                    'user_id' => $_SESSION['user_id'],
                    'order_id' => $order['id'],
                    'order_number' => $order['order_number']
                ],
            ]);

            // Lier la session Stripe à la commande
            $stmt = $this->db->prepare("
                UPDATE orders 
                SET stripe_session_id = :session_id
                WHERE id = :order_id
            ");
            $stmt->execute([
                'session_id' => $session->id,
                'order_id' => $order['id']
            ]);

            error_log("✅ Session Stripe créée : {$session->id}");

            // Retourner l'URL de checkout
            $this->jsonResponse([
                'success' => true,
                'checkout_url' => $session->url,
                'order_id' => $order['id']
            ]);

        } catch (\Exception $e) {
            error_log('❌ Erreur checkout: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());

            $this->jsonResponse([
                'error' => 'Erreur lors de la création du paiement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Page de succès - Redirige vers PaymentController
     */
    public function success() {
        $sessionId = $_GET['session_id'] ?? '';
        if ($sessionId) {
            $this->redirect('/payment/success?session_id=' . $sessionId);
        } else {
            $this->redirect('/');
        }
    }
}