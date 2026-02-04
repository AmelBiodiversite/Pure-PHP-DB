<?php
/**
 * TEST DE SÉCURITÉ: Vérification anti-manipulation prix
 */

// Autoloader Composer
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

// Charger les fichiers core manuellement
require_once 'core/Database.php';
require_once 'app/models/Cart.php';

// Charger .env si Dotenv disponible
if (class_exists('Dotenv\Dotenv') && file_exists('.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Démarrer session
session_start();

use Core\Database;
use App\Models\Cart;

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║   TEST SÉCURITÉ: Anti-Manipulation Prix Panier            ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

try {
    // Connexion DB
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    echo "✓ Connexion base de données OK\n\n";
    
    // Récupérer un produit existant pour le test
    $stmt = $pdo->query("SELECT id, title, price FROM products WHERE status = 'approved' LIMIT 1");
    $product = $stmt->fetch();
    
    if (!$product) {
        echo "❌ Aucun produit disponible pour le test\n";
        exit(1);
    }
    
    $productId = $product['id'];
    $originalPrice = (float)$product['price'];
    
    echo "📦 Produit de test:\n";
    echo "   ID: {$productId}\n";
    echo "   Titre: {$product['title']}\n";
    echo "   Prix original: {$originalPrice} €\n\n";
    
    // SIMULATION ATTAQUE
    echo "═══════════════════════════════════════════════════════════\n";
    echo "🔴 SIMULATION D'ATTAQUE: Manipulation prix session\n";
    echo "═══════════════════════════════════════════════════════════\n\n";
    
    // Vider le panier
    $_SESSION['cart'] = null;
    $cart = new Cart();
    
    // Ajouter le produit au panier (prix = original)
    echo "1️⃣  Ajout produit au panier (prix: {$originalPrice} €)\n";
    $result = $cart->add($productId, 1);
    
    if (!$result['success']) {
        echo "❌ Erreur ajout: {$result['error']}\n";
        exit(1);
    }
    
    // Simuler manipulation: changer le prix dans la session
    $fakePrice = round($originalPrice * 0.5, 2); // 50% de réduction frauduleuse
    $_SESSION['cart']['items'][$productId]['price'] = $fakePrice;
    
    echo "2️⃣  💀 ATTAQUE: Prix session modifié à {$fakePrice} € (-50%)\n\n";
    
    // Modifier aussi le prix en BDD (simuler changement admin)
    $newDbPrice = round($originalPrice * 1.2, 2); // +20%
    $stmt = $pdo->prepare("UPDATE products SET price = ? WHERE id = ?");
    $stmt->execute([$newDbPrice, $productId]);
    
    echo "3️⃣  Admin change le prix BDD à {$newDbPrice} € (+20%)\n\n";
    
    // TEST: getCheckoutData() doit utiliser le prix BDD
    echo "═══════════════════════════════════════════════════════════\n";
    echo "✅ TEST: Vérification sécurité getCheckoutData()\n";
    echo "═══════════════════════════════════════════════════════════\n\n";
    
    $checkoutData = $cart->getCheckoutData();
    
    if (empty($checkoutData['items'])) {
        echo "❌ Aucun item dans checkout data\n";
        exit(1);
    }
    
    $usedPrice = (float)$checkoutData['items'][0]['price'];
    
    echo "Prix en session (FRAUDULEUX): {$fakePrice} €\n";
    echo "Prix en BDD (LÉGITIME):       {$newDbPrice} €\n";
    echo "Prix utilisé au checkout:     {$usedPrice} €\n\n";
    
    // VÉRIFICATION
    if (abs($usedPrice - $newDbPrice) < 0.01) {
        echo "✅ ✅ ✅ SÉCURITÉ OK ✅ ✅ ✅\n";
        echo "Le prix BDD est utilisé (pas celui de la session)\n";
        echo "L'attaque est BLOQUÉE !\n\n";
        
        // Vérifier le log
        if (!empty($checkoutData['warnings'])) {
            echo "📊 Alertes détectées:\n";
            foreach ($checkoutData['warnings'] as $warning) {
                echo "   ⚠️  {$warning}\n";
            }
            echo "\n";
        }
        
        echo "💰 Impact financier:\n";
        echo "   Perte évitée: " . number_format($newDbPrice - $fakePrice, 2) . " € par transaction\n";
        
        $result = "SUCCÈS";
    } else {
        echo "❌ ❌ ❌ VULNÉRABILITÉ DÉTECTÉE ❌ ❌ ❌\n";
        echo "Le prix SESSION est utilisé (DANGEREUX !)\n";
        echo "Perte financière potentielle: " . number_format($newDbPrice - $usedPrice, 2) . " €\n\n";
        $result = "ÉCHEC";
    }
    
    // Remettre le prix original
    $stmt = $pdo->prepare("UPDATE products SET price = ? WHERE id = ?");
    $stmt->execute([$originalPrice, $productId]);
    
    echo "\n4️⃣  Prix BDD restauré à {$originalPrice} €\n";
    
    // Nettoyer
    $cart->clear();
    
    echo "\n═══════════════════════════════════════════════════════════\n";
    echo "RÉSULTAT FINAL: {$result}\n";
    echo "═══════════════════════════════════════════════════════════\n";
    
    exit($result === "SUCCÈS" ? 0 : 1);
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
