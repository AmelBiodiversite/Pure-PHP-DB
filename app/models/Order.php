<?php
namespace App\Models;

use Core\Model;
use PDO;
use Exception;


/**
 * MARKETFLOW PRO - ORDER MODEL (POSTGRESQL)
 * Fichier : app/models/Order.php
 */

class Order extends Model {
    protected $table = 'orders';
    protected $primaryKey = 'id';

    /**
     * Créer une commande
     */
    public function createOrder($data) {
        // PostgreSQL : RETURNING pour obtenir l'ID et order_number généré par trigger
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $fieldList = implode(', ', $fields);

        $sql = "INSERT INTO orders ({$fieldList}) 
                VALUES ({$placeholders}) 
                RETURNING id, order_number";

        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    /**
     * Créer une commande complète avec items
     */
    public function createOrderWithItems($orderData, $items) {
        // Démarrer transaction PostgreSQL
        $this->db->beginTransaction();

        try {
            // Créer la commande
            $order = $this->createOrder($orderData);

            if (!$order) {
                throw new Exception("Erreur création commande");
            }

            // Créer les items
            foreach ($items as $item) {
                $item['order_id'] = $order['id'];

                // Générer une clé de licence unique
                $item['license_key'] = $this->generateLicenseKey();

                $this->createOrderItem($item);
            }

            $this->db->commit();
            return $order;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error creating order: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer un item de commande
     */
    private function createOrderItem($data) {
        // Mapper les champs du panier vers les colonnes de order_items
        $itemData = [
            'order_id' => $data['order_id'],
            'product_id' => $data['product_id'],
            'seller_id' => $data['seller_id'],
            'product_title' => $data['title'],
            'product_price' => $data['price'],
            'quantity' => $data['quantity'] ?? 1,
            'seller_amount' => $data['seller_amount'],
            'platform_fee' => $data['commission_amount'],
            'license_key' => $data['license_key']
        ];

        $fields = array_keys($itemData);
        $placeholders = ':' . implode(', :', $fields);
        $fieldList = implode(', ', $fields);

        $sql = "INSERT INTO order_items ({$fieldList}) 
                VALUES ({$placeholders}) 
                RETURNING id";

        $stmt = $this->db->prepare($sql);

        foreach ($itemData as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }

    /**
     * Obtenir une commande par numéro
     */
    public function findByOrderNumber($orderNumber) {
        $sql = "SELECT o.*, 
                u.full_name as buyer_name,
                u.email as buyer_email
                FROM orders o
                LEFT JOIN users u ON o.buyer_id = u.id
                WHERE o.order_number = :order_number
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['order_number' => $orderNumber]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir les items d'une commande
     */
    public function getOrderItems($orderId) {
        $sql = "SELECT oi.*, 
                p.title as product_title,
                p.thumbnail_url,
                p.file_url,
                u.shop_name as seller_shop_name,
                u.username as seller_username
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                LEFT JOIN users u ON oi.seller_id = u.id
                WHERE oi.order_id = :order_id
                ORDER BY oi.id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir les commandes d'un acheteur
     */
    public function getBuyerOrders($buyerId, $limit = null, $offset = null) {
        $sql = "SELECT o.*,
                COUNT(oi.id) as items_count
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.buyer_id = :buyer_id
                GROUP BY o.id
                ORDER BY o.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit";
        }

        if ($offset) {
            $sql .= " OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':buyer_id', $buyerId, PDO::PARAM_INT);

        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        if ($offset) {
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir les ventes d'un vendeur
     */
    public function getSellerSales($sellerId, $limit = null, $offset = null) {
        $sql = "SELECT DISTINCT o.*,
                u.full_name as buyer_name
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                LEFT JOIN users u ON o.buyer_id = u.id
                WHERE oi.seller_id = :seller_id
                ORDER BY o.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit";
        }

        if ($offset) {
            $sql .= " OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':seller_id', $sellerId, PDO::PARAM_INT);

        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        if ($offset) {
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    /**
     * Marquer comme payée
     */
    public function markAsPaid($orderId, $paymentId = null) {
        $data = [
            'payment_status' => 'completed',
            'status' => 'completed',
            'paid_at' => date('Y-m-d H:i:s'),
            'completed_at' => date('Y-m-d H:i:s')
        ];

        if ($paymentId) {
            $data['stripe_payment_id'] = $paymentId;
        }

        return $this->update($orderId, $data);
    }

    /**
     * Incrémenter le compteur de téléchargement
     */
    public function incrementDownloadCount($orderItemId) {
        $sql = "UPDATE order_items 
                SET download_count = download_count + 1 
                WHERE id = :id 
                AND download_count < max_downloads";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $orderItemId]);
    }

    /**
     * Vérifier si un téléchargement est autorisé
     */
    public function canDownload($orderItemId) {
        $sql = "SELECT download_count, max_downloads 
                FROM order_items 
                WHERE id = :id 
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $orderItemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            return false;
        }

        return $item['download_count'] < $item['max_downloads'];
    }

    /**
     * Générer une clé de licence unique
     */
    private function generateLicenseKey() {
        // Format: XXXX-XXXX-XXXX-XXXX
        $key = strtoupper(bin2hex(random_bytes(8)));
        $formatted = substr($key, 0, 4) . '-' . 
                     substr($key, 4, 4) . '-' . 
                     substr($key, 8, 4) . '-' . 
                     substr($key, 12, 4);

        // Vérifier unicité
        if ($this->licenseKeyExists($formatted)) {
            return $this->generateLicenseKey(); // Récursif
        }

        return $formatted;
    }

    /**
     * Vérifier si une clé de licence existe
     */
    private function licenseKeyExists($key) {
        $sql = "SELECT COUNT(*) as count FROM order_items WHERE license_key = :key";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['key' => $key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Obtenir les statistiques de commandes
     */
    public function getOrderStats($filters = []) {
        $sql = "SELECT 
                COUNT(*) as total_orders,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_orders,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
                COALESCE(SUM(total_amount), 0) as total_revenue,
                COALESCE(SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END), 0) as completed_revenue,
                COALESCE(AVG(total_amount), 0) as average_order_value
                FROM orders
                WHERE 1=1";

        $params = [];

        if (isset($filters['buyer_id'])) {
            $sql .= " AND buyer_id = :buyer_id";
            $params['buyer_id'] = $filters['buyer_id'];
        }

        if (isset($filters['start_date'])) {
            $sql .= " AND created_at >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }

        if (isset($filters['end_date'])) {
            $sql .= " AND created_at <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir les revenus par jour (derniers 30 jours)
     * PostgreSQL : INTERVAL pour les dates
     */
    public function getRevenueByDay($sellerId = null, $days = 30) {
        $sql = "SELECT 
                DATE(o.created_at) as date,
                COUNT(o.id) as orders_count,
                COALESCE(SUM(o.total_amount), 0) as revenue";

        if ($sellerId) {
            $sql .= ", COALESCE(SUM(oi.seller_amount), 0) as seller_revenue
                    FROM orders o
                    JOIN order_items oi ON o.id = oi.order_id
                    WHERE oi.seller_id = :seller_id 
                    AND o.status = 'completed'
                    AND o.created_at >= CURRENT_DATE - INTERVAL '{$days} days'
                    GROUP BY DATE(o.created_at)";
        } else {
            $sql .= " FROM orders o
                    WHERE o.status = 'completed'
                    AND o.created_at >= CURRENT_DATE - INTERVAL '{$days} days'
                    GROUP BY DATE(o.created_at)";
        }

        $sql .= " ORDER BY date DESC";

        $stmt = $this->db->prepare($sql);

        if ($sellerId) {
            $stmt->execute(['seller_id' => $sellerId]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir les produits les plus vendus
     */
    public function getTopSellingProducts($sellerId = null, $limit = 10) {
        $sql = "SELECT 
                p.id,
                p.title,
                p.thumbnail_url,
                p.price,
                COUNT(oi.id) as sales_count,
                SUM(oi.seller_amount) as total_revenue
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                JOIN orders o ON oi.order_id = o.id
                WHERE o.status = 'completed'";

        $params = [];

        if ($sellerId) {
            $sql .= " AND oi.seller_id = :seller_id";
            $params['seller_id'] = $sellerId;
        }

        $sql .= " GROUP BY p.id, p.title, p.thumbnail_url, p.price
                 ORDER BY sales_count DESC
                 LIMIT :limit";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

/**
     * Récupérer les commandes d'un vendeur (ses ventes)
     */
    public function getSellerOrders($sellerId, $limit = 100) {
        $stmt = $this->db->prepare("
            SELECT o.*, 
                   u.username as buyer_username,
                   u.email as buyer_email,
                   COUNT(oi.id) as items_count
            FROM orders o
            JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.seller_id = ?
            GROUP BY o.id, u.username, u.email
            ORDER BY o.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$sellerId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Récupérer les commandes d'un utilisateur (ses achats)
     */
    public function getUserOrders($userId, $limit = 100) {
        $stmt = $this->db->prepare("
            SELECT o.*, 
                   COUNT(oi.id) as items_count,
                   u.username as seller_username
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN users u ON o.seller_id = u.id
            WHERE o.user_id = ?
            GROUP BY o.id, u.username
            ORDER BY o.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }


}
