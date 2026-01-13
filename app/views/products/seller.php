<?php
/**
 * Produits d'un vendeur
 */
?>

<div class="container mt-8">
    <h1>Produits de <?= e($seller['full_name'] ?? $seller['username']) ?></h1>
    
    <?php if (empty($products)): ?>
        <div class="card text-center" style="padding: var(--space-12);">
            <p>Ce vendeur n'a pas encore de produits.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-4 mt-8">
            <?php foreach ($products as $product): ?>
                <div class="card">
                    <h3><?= e($product['title']) ?></h3>
                    <p><?= e($product['price']) ?>€</p>
                    <a href="/products/<?= e($product['slug']) ?>" class="btn btn-primary btn-sm">Voir</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>