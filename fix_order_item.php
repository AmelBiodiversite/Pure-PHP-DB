<?php
$file = 'app/models/Order.php';
$content = file_get_contents($file);

// Trouver et remplacer la méthode createOrderItem
$oldMethod = <<<'OLD'
    /**
     * Créer un item de commande
     */
    private function createOrderItem($data) {
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $fieldList = implode(', ', $fields);

        $sql = "INSERT INTO order_items ({$fieldList}) 
                VALUES ({$placeholders}) 
                RETURNING id";

        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }
OLD;

$newMethod = <<<'NEW'
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
NEW;

$content = str_replace($oldMethod, $newMethod, $content);
file_put_contents($file, $content);
echo "✅ Order.php corrigé\n";
