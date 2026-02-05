<?php
/**
 * MARKETFLOW PRO - PAYMENT CONTROLLER
 * Gestion des paiements Stripe + Webhooks
 * Fichier : app/controllers/PaymentController.php
 */

namespace App\Controllers;

use Core\Controller;
use App\Models\Order;
use App\Models\Cart;

class PaymentController extends Controller {
    private $orderModel;
    private $cart;

    public function __construct() {
        parent::__construct();
        $this->orderModel = new Order();
        $this->cart = new Cart();
        
        // Charger la librairie Stripe
        require_once __DIR__ . '/../../vendor/stripe/stripe-php/init.php';
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
    }

    /**
     * Page de succès après paiement
     */
    public function success() {
        $sessionId = $_GET['session_id'] ?? null;

        if (!$sessionId) {
            redirectWithMessage('/', 'Session invalide', 'error');
            return;
        }

        try {
            // Récupérer la session Stripe
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            // Récupérer la commande
            $orderId = $session->metadata->order_id ?? null;
            
            if (!$orderId) {
                redirectWithMessage('/', 'Commande introuvable', 'error');
                return;
            }

            $order = $this->orderModel->find($orderId);

            if (!$order) {
                redirectWithMessage('/', 'Commande introuvable', 'error');
                return;
            }

            // Si le paiement est déjà confirmé, afficher la page
            if ($order['payment_status'] === 'completed') {
                // Vider le panier
                $this->cart->clear();

                // Récupérer les détails complets
                $orderDetails = $this->orderModel->getOrderDetails($order['order_number']);

                $this->view('payment/success', [
                    'title' => 'Paiement réussi !',
                    'order' => $orderDetails,
                    'session' => $session
                ]);
                return;
            }

            // Si pas encore confirmé, confirmer maintenant
            if ($session->payment_status === 'paid') {
                $this->orderModel->confirmPayment(
                    $orderId,
                    $session->payment_intent
                );

                // Vider le panier
                $this->cart->clear();

                $orderDetails = $this->orderModel->getOrderDetails($order['order_number']);

                $this->view('payment/success', [
                    'title' => 'Paiement réussi !',
                    'order' => $orderDetails,
                    'session' => $session
                ]);
            } else {
                redirectWithMessage('/cart', 'Le paiement n\'a pas été complété', 'error');
            }

        } catch (\Exception $e) {
            error_log('Erreur payment success: ' . $e->getMessage());
            redirectWithMessage('/', 'Erreur lors de la récupération de la commande', 'error');
        }
    }

    /**
     * Page d'annulation de paiement
     */
    public function cancel() {
        $this->view('payment/cancel', [
            'title' => 'Paiement annulé'
        ]);
    }

    /**
     * Webhook Stripe (CRITIQUE pour la production)
     */
    public function stripeWebhook() {
        // Récupérer le payload
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        try {
            // Vérifier la signature
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                STRIPE_WEBHOOK_SECRET
            );

        } catch (\UnexpectedValueException $e) {
            // Payload invalide
            http_response_code(400);
            echo json_encode(['error' => 'Invalid payload']);
            exit;

        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Signature invalide
            http_response_code(400);
            echo json_encode(['error' => 'Invalid signature']);
            exit;
        }

        // Logger l'événement
        error_log('Stripe webhook received: ' . $event->type);

        // Gérer l'événement selon son type
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event->data->object);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;

            case 'charge.refunded':
                $this->handleRefund($event->data->object);
                break;

            default:
                error_log('Unhandled event type: ' . $event->type);
        }

        // Retourner 200 OK
        http_response_code(200);
        echo json_encode(['status' => 'success']);
        exit;
    }

    /**
     * Gérer la completion du checkout
     */
    private function handleCheckoutCompleted($session) {
        $orderId = $session->metadata->order_id ?? null;

        if (!$orderId) {
            error_log('Order ID not found in session metadata');
            return;
        }

        // Vérifier si déjà traité
        $order = $this->orderModel->find($orderId);
        
        if (!$order) {
            error_log('Order not found: ' . $orderId);
            return;
        }

        if ($order['payment_status'] === 'completed') {
            error_log('Order already processed: ' . $orderId);
            return;
        }

        // Confirmer le paiement
        $result = $this->orderModel->confirmPayment(
            $orderId,
            $session->payment_intent
        );

        if ($result['success']) {
            error_log('Order confirmed successfully: ' . $orderId);
            
            // Logger l'activité
            $this->logActivity(
                $order['buyer_id'],
                'order_completed',
                'order',
                $orderId,
                ['order_number' => $order['order_number']]
            );
        } else {
            error_log('Failed to confirm order: ' . $orderId . ' - ' . ($result['error'] ?? 'Unknown error'));
        }
    }

    /**
     * Gérer le succès du paiement
     */
    private function handlePaymentSucceeded($paymentIntent) {
        error_log('Payment succeeded: ' . $paymentIntent->id);
        
        // Trouver la commande par payment_intent
        $stmt = $this->db->prepare("
            SELECT id FROM orders WHERE payment_id = ? AND payment_status = 'pending'
        ");
        $stmt->execute([$paymentIntent->id]);
        $order = $stmt->fetch();

        if ($order) {
            $this->orderModel->confirmPayment($order['id'], $paymentIntent->id);
        }
    }

    /**
     * Gérer l'échec du paiement
     */
    private function handlePaymentFailed($paymentIntent) {
        error_log('Payment failed: ' . $paymentIntent->id);

        // Marquer la commande comme échouée
        $stmt = $this->db->prepare("
            UPDATE orders 
            SET payment_status = 'failed'
            WHERE payment_id = ?
        ");
        $stmt->execute([$paymentIntent->id]);
    }

    /**
     * Gérer un remboursement
     */
    private function handleRefund($charge) {
        error_log('Refund processed: ' . $charge->id);

        // Marquer la commande comme remboursée
        $stmt = $this->db->prepare("
            UPDATE orders 
            SET payment_status = 'refunded'
            WHERE payment_id = ?
        ");
        $stmt->execute([$charge->payment_intent]);

        // TODO: Notifier le vendeur, révoquer les licences, etc.
    }

    /**
     * Créer une intention de paiement (alternative à Checkout)
     */
    public function createPaymentIntent() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
        }

        if (!$this->isLoggedIn()) {
            $this->json(['error' => 'Authentication required'], 401);
        }

        // Valider le panier
        $validation = $this->cart->validate();
        if (!$validation['valid']) {
            $this->json(['error' => implode(', ', $validation['errors'])], 400);
        }

        $total = $this->cart->getTotalWithPromo();

        try {
            // Créer l'intention de paiement
            $intent = \Stripe\PaymentIntent::create([
                'amount' => round($total * 100), // En centimes
                'currency' => strtolower(CURRENCY),
                'metadata' => [
                    'user_id' => $_SESSION['user_id']
                ]
            ]);

            $this->json([
                'success' => true,
                'clientSecret' => $intent->client_secret
            ]);

        } catch (\Exception $e) {
            error_log('Error creating payment intent: ' . $e->getMessage());
            $this->json(['error' => 'Payment creation failed'], 500);
        }
    }

    /**
     * Vérifier le statut d'un paiement
     */
    public function checkPaymentStatus() {
        $orderId = $_GET['order_id'] ?? null;

        if (!$orderId) {
            $this->json(['error' => 'Order ID required'], 400);
        }

        $order = $this->orderModel->find($orderId);

        if (!$order) {
            $this->json(['error' => 'Order not found'], 404);
        }

        // Vérifier que c'est bien la commande de l'utilisateur
        if ($this->isLoggedIn() && $order['buyer_id'] != $_SESSION['user_id']) {
            $this->json(['error' => 'Unauthorized'], 403);
        }

        $this->json([
            'success' => true,
            'status' => $order['payment_status'],
            'order_number' => $order['order_number']
        ]);
    }

    /**
     * Demander un remboursement (admin only)
     */
    public function refund() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/orders');
        }

        $orderId = $_POST['order_id'] ?? null;
        $reason = $_POST['reason'] ?? '';

        if (!$orderId) {
            redirectWithMessage('/admin/orders', 'Commande invalide', 'error');
            return;
        }

        $order = $this->orderModel->find($orderId);

        if (!$order || !$order['payment_id']) {
            redirectWithMessage('/admin/orders', 'Commande introuvable', 'error');
            return;
        }

        try {
            // Créer le remboursement Stripe
            $refund = \Stripe\Refund::create([
                'payment_intent' => $order['payment_id'],
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'order_id' => $orderId,
                    'admin_reason' => $reason
                ]
            ]);

            // Mettre à jour la commande
            $stmt = $this->db->prepare("
                UPDATE orders 
                SET payment_status = 'refunded'
                WHERE id = ?
            ");
            $stmt->execute([$orderId]);

            // Logger
            $this->logActivity(
                $_SESSION['user_id'],
                'order_refunded',
                'order',
                $orderId,
                ['reason' => $reason]
            );

            redirectWithMessage(
                '/admin/orders',
                'Remboursement effectué avec succès',
                'success'
            );

        } catch (\Exception $e) {
            error_log('Refund error: ' . $e->getMessage());
            redirectWithMessage(
                '/admin/orders',
                'Erreur lors du remboursement: ' . $e->getMessage(),
                'error'
            );
        }
    }

    /**
     * Logger une activité
     */
    private function logActivity($userId, $action, $entityType, $entityId, $details = null) {
        $stmt = $this->db->prepare("
            INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, ip_address, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $userId,
            $action,
            $entityType,
            $entityId,
            $details ? json_encode($details) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            date('Y-m-d H:i:s')
        ]);
    }
}