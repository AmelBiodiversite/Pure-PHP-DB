<?php
namespace App\Models;

use Core\Model;
use PDO;
use Exception;

/**
 * MARKETFLOW PRO - ORDER MODEL (POSTGRESQL)
 * Gestion des commandes : création, lecture, téléchargements, statistiques
 * Fichier : app/models/Order.php
 */

class Order extends Model {
    protected $table = 'orders';
    protected $primaryKey = 'id';

    // ================================================================
    // CRÉATION DE COMMANDES
    // ================================================================

    /**
     * Créer une commande
     * RETURNING récupère l'id et l'order_number généré automatiquement par le trigger
     */
    public function createOrder($data) {
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
     * Créer une commande complète avec ses articles (transaction atomique)
     * Si un article échoue, toute la commande est annulée (rollback)
     */
    public function createOrderWithItems($orderData, $items) {
        $this->db->beginTransaction();
        try {
            $order = $this->createOrder($orderData);
            if (!$order) {
                throw new Exception("Erreur création commande");
            }
            foreach ($items as $item) {
                $item['order_id'] = $order['id'];
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
     * Créer un article de commande (usage interne)
     * Mappe les champs du panier vers les colonnes de order_items
     */
    private function createOrderItem($data) {
        $itemData = [
            'order_id'      => $data['order_id'],
            'product_id'    => $data['product_id'],
            'seller_id'     => $data['seller_id'],
            'product_title' => $data['title'],
            'product_price' => $data['price'],
            'quantity'      => $data['quantity'] ?? 1,
            'seller_amount' => $data['seller_amount'],
            'platform_fee'  => $data['commission_amount'],
            'license_key'   => $data['license_key']
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

    // ================================================================
    // LECTURE DE COMMANDES
    // ================================================================

    /**
     * Trouver une commande par son numéro (ex: ORD-20260328-000007)
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
     * Détails complets d'une commande avec ses articles
     * Vérifie que la commande appartient bien à l'acheteur connecté (sécurité)
     */
    public function getOrderDetails($orderNumber, $userId) {
        $stmt = $this->db->prepare("
            SELECT o.*,
                   u.email as buyer_email,
                   u.full_name as buyer_name
            FROM orders o
            LEFT JOIN users u ON o.buyer_id = u.id
            WHERE o.order_number = :order_number
            AND o.buyer_id = :buyer_id
            LIMIT 1
        ");
        $stmt->execute([
            'order_number' => $orderNumber,
            'buyer_id'     => $userId
        ]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) return null;

        $stmt = $this->db->prepare("
            SELECT oi.*,
                   oi.product_price  as price,
                   oi.max_downloads  as download_limit,
                   p.slug,
                   p.thumbnail_url   as thumbnail,
                   p.file_url        as file_path,
                   u.username        as seller_username,
                   u.shop_name
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            LEFT JOIN users u ON oi.seller_id = u.id
            WHERE oi.order_id = :order_id
            ORDER BY oi.id
        ");
        $stmt->execute(['order_id' => $order['id']]);
        $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $order;
    }

    /**
     * Articles d'une commande (usage interne)
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
     * Commandes d'un acheteur (ses achats)
     * Filtre sur buyer_id — identifie l'acheteur dans la table orders
     */
    public function getUserOrders($userId, $limit = 100) {
        $stmt = $this->db->prepare("
            SELECT o.*,
                   COUNT(oi.id) as items_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.buyer_id = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Ventes d'un vendeur (ses produits vendus)
     * Le seller_id est dans order_items car une commande peut avoir plusieurs vendeurs
     */
    public function getSellerOrders($sellerId, $limit = 100) {
        $stmt = $this->db->prepare("
            SELECT DISTINCT o.*,
                   u.username as buyer_username,
                   u.email as buyer_email,
                   COUNT(oi.id) as items_count
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN users u ON o.buyer_id = u.id
            WHERE oi.seller_id = ?
            GROUP BY o.id, u.username, u.email
            ORDER BY o.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$sellerId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Commandes d'un acheteur avec pagination (version avancée)
     */
    public function getBuyerOrders($buyerId, $limit = null, $offset = null) {
        $sql = "SELECT o.*,
                       COUNT(oi.id) as items_count
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.buyer_id = :buyer_id
                GROUP BY o.id
                ORDER BY o.created_at DESC";

        if ($limit)  $sql .= " LIMIT :limit";
        if ($offset) $sql .= " OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':buyer_id', $buyerId, PDO::PARAM_INT);
        if ($limit)  $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        if ($offset) $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ventes d'un vendeur avec pagination (version avancée)
     */
    public function getSellerSales($sellerId, $limit = null, $offset = null) {
        $sql = "SELECT DISTINCT o.*,
                       u.full_name as buyer_name
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                LEFT JOIN users u ON o.buyer_id = u.id
                WHERE oi.seller_id = :seller_id
                ORDER BY o.created_at DESC";

        if ($limit)  $sql .= " LIMIT :limit";
        if ($offset) $sql .= " OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':seller_id', $sellerId, PDO::PARAM_INT);
        if ($limit)  $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        if ($offset) $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================================================
    // PAIEMENT & STATUT
    // ================================================================

    /**
     * Marquer une commande comme payée (appelé après confirmation Stripe)
     */
    public function markAsPaid($orderId, $paymentId = null) {
        $data = [
            'payment_status' => 'completed',
            'status'         => 'completed',
            'paid_at'        => date('Y-m-d H:i:s'),
            'completed_at'   => date('Y-m-d H:i:s')
        ];
        if ($paymentId) {
            $data['stripe_payment_id'] = $paymentId;
        }
        return $this->update($orderId, $data);
    }

    // ================================================================
    // TÉLÉCHARGEMENTS
    // ================================================================

    /**
     * Enregistrer un téléchargement
     * Vérifie l'appartenance à l'acheteur et la limite de téléchargements
     */
    public function recordDownload($itemId, $userId) {
        $stmt = $this->db->prepare("
            SELECT oi.download_count, oi.max_downloads
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE oi.id = :item_id AND o.buyer_id = :user_id
            LIMIT 1
        ");
        $stmt->execute(['item_id' => $itemId, 'user_id' => $userId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            return ['success' => false, 'error' => 'Produit introuvable dans vos commandes'];
        }
        if ($item['download_count'] >= $item['max_downloads']) {
            return ['success' => false, 'error' => 'Limite de ' . $item['max_downloads'] . ' téléchargements atteinte'];
        }

        $stmt = $this->db->prepare("
            UPDATE order_items SET download_count = download_count + 1 WHERE id = :id
        ");
        $stmt->execute(['id' => $itemId]);
        return ['success' => true];
    }

    /**
     * Incrémenter le compteur de téléchargement (version simple sans vérification)
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
     * Vérifier si un téléchargement est encore autorisé
     */
    public function canDownload($orderItemId) {
        $sql = "SELECT download_count, max_downloads
                FROM order_items
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $orderItemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) return false;
        return $item['download_count'] < $item['max_downloads'];
    }

    // ================================================================
    // STATISTIQUES
    // ================================================================

    /**
     * Statistiques globales des commandes (filtrable par acheteur et dates)
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
     * Revenus par jour sur les N derniers jours
     * PostgreSQL : INTERVAL pour le calcul de dates
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
     * Produits les plus vendus (filtrable par vendeur)
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

    // ================================================================
    // CLÉS DE LICENCE
    // ================================================================

    /**
     * Générer une clé de licence unique au format XXXX-XXXX-XXXX-XXXX
     * Récursif jusqu'à trouver une clé non existante en base
     */
    private function generateLicenseKey() {
        $key = strtoupper(bin2hex(random_bytes(8)));
        $formatted = substr($key, 0, 4) . '-' .
                     substr($key, 4, 4) . '-' .
                     substr($key, 8, 4) . '-' .
                     substr($key, 12, 4);

        if ($this->licenseKeyExists($formatted)) {
            return $this->generateLicenseKey();
        }
        return $formatted;
    }

    /**
     * Vérifier qu'une clé de licence n'existe pas déjà en base
     */
    private function licenseKeyExists($key) {
        $sql = "SELECT COUNT(*) as count FROM order_items WHERE license_key = :key";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['key' => $key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}
