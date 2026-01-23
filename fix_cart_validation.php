<?php
// Lire le fichier
$content = file_get_contents('app/models/Cart.php');

// Remplacer la ligne problématique
$old = "SELECT status, is_active 
                FROM products 
                WHERE id = ?";

$new = "SELECT status 
                FROM products 
                WHERE id = ?";

$content = str_replace($old, $new, $content);

// Aussi corriger la condition
$old2 = "if (!$product || $product['status'] !== 'approved' || !$product['is_active'])";
$new2 = "if (!$product || $product['status'] !== 'approved')";

$content = str_replace($old2, $new2, $content);

// Sauvegarder
file_put_contents('app/models/Cart.php', $content);

echo "✅ Cart.php corrigé !\n";
