<?php
/**
 * Liste des produits du vendeur
 */
?>

<div class="container mt-8">
    <div class="flex-between mb-8">
        <h1>Mes Produits</h1>
        <a href="/seller/products/create" class="btn btn-primary">+ Ajouter un produit</a>
    </div>

    <?php if (empty($products)): ?>
        <div class="card text-center" style="padding: var(--space-12);">
            <p style="font-size: 1.25rem; margin-bottom: var(--space-4);">Aucun produit pour le moment</p>
            <a href="/seller/products/create" class="btn btn-primary">Créer mon premier produit</a>
        </div>
    <?php else: ?>
        <div class="grid grid-3">
            <?php foreach ($products as $product): ?>
                <div class="card">
                    <h3><?= e($product['title']) ?></h3>
                    <p>Prix : <?= e($product['price']) ?>€</p>
                    <p>Statut : <span class="badge"><?= e($product['status']) ?></span></p>
                    <div class="flex gap-2 mt-4">
                        <a href="/seller/products/<?= $product['id'] ?>/edit" class="btn btn-sm btn-ghost">Modifier</a>
                        <a href="/products/<?= $product['slug'] ?>" class="btn btn-sm btn-outline">Voir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>