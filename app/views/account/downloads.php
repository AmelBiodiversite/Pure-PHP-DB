<?php
/**
 * Page des téléchargements
 */
?>

<div class="container mt-8 mb-16">
    <div style="margin-bottom: var(--space-8);">
        <a href="/account" style="color: var(--text-tertiary); text-decoration: none;">
            ← Retour au compte
        </a>
    </div>
    
    <h1 style="margin-bottom: var(--space-8);">
        📥 Mes Téléchargements
    </h1>
    
    <?php if (empty($products)): ?>
    <div class="card" style="padding: var(--space-12); text-align: center;">
        <p style="font-size: 3rem; margin-bottom: var(--space-4);">📦</p>
        <h2 style="margin-bottom: var(--space-4);">Aucun produit acheté</h2>
        <p style="color: var(--text-secondary); margin-bottom: var(--space-6);">
            Commencez par acheter des produits pour pouvoir les télécharger ici.
        </p>
        <a href="/products" class="btn btn-primary">
            Découvrir les produits
        </a>
    </div>
    <?php else: ?>
    <div class="row">
        <?php foreach ($products as $product): ?>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card" style="height: 100%;">
                <img src="<?= e($product['thumbnail_url'] ?? '/public/img/placeholder.png') ?>" 
                     alt="<?= e($product['title']) ?>"
                     style="width: 100%; height: 200px; object-fit: cover; border-radius: var(--radius) var(--radius) 0 0;">
                
                <div style="padding: var(--space-6);">
                    <h3 style="margin-bottom: var(--space-3); font-size: 1.125rem;">
                        <?= e($product['title']) ?>
                    </h3>
                    
                    <p style="font-size: 0.875rem; color: var(--text-tertiary); margin-bottom: var(--space-4);">
                        Acheté le <?= date('d/m/Y', strtotime($product['purchased_at'])) ?>
                    </p>
                    
                    <a href="/download/<?= e($product['id']) ?>" class="btn btn-primary" style="width: 100%;">
                        📥 Télécharger
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>