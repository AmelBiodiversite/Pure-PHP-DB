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
                        <a href="/seller/products/<?= e($product['id']) ?>/edit" class="btn btn-sm btn-ghost">Modifier</a>
                        <a href="/products/<?= e($product['slug']) ?>" class="btn btn-sm btn-outline">Voir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<style>
/* === DESIGN MAQUETTE2 — PRODUITS VENDEUR === */
.container{background:#faf9f5;padding-top:32px!important}
h1,h2,h3{font-family:Georgia,serif;font-weight:400;color:#1e1208}
.card{background:#fff!important;border:0.5px solid #ede8df!important;border-radius:14px!important;box-shadow:none!important;padding:18px!important}
.badge{background:#ede9fe!important;color:#534ab7!important;border-radius:6px!important;font-size:11px!important;padding:3px 8px!important;font-family:'Manrope',sans-serif!important}
.btn-primary{background:#7c6cf0!important;color:#fff!important;border:none!important;border-radius:8px!important;font-family:'Manrope',sans-serif!important;font-size:12px!important;font-weight:500!important}
.btn-ghost.btn-sm{background:transparent!important;color:#6b5c4e!important;border:0.5px solid #ddd6c8!important;border-radius:7px!important;font-family:'Manrope',sans-serif!important;font-size:11px!important}
.btn-outline.btn-sm{background:transparent!important;color:#7c6cf0!important;border:0.5px solid #7c6cf0!important;border-radius:7px!important;font-family:'Manrope',sans-serif!important;font-size:11px!important}
</style>
