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
<style>
/* === DESIGN MAQUETTE2 — PAGE PUBLIQUE VENDEUR === */

/* Titre */
h1 {
    font-family: Georgia, serif !important;
    font-weight: 400 !important;
    color: #1e1208 !important;
    font-size: 24px !important;
}

/* Card état vide */
.card.text-center p {
    font-family: 'Manrope', sans-serif !important;
    color: #a0907e !important;
    font-size: 13px !important;
}

/* Grille de produits */
.grid.grid-4 {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)) !important;
    gap: 16px !important;
}

/* Cards produit */
.card {
    background: #fff !important;
    border: 0.5px solid #ede8df !important;
    border-radius: 14px !important;
    box-shadow: none !important;
    padding: 20px !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
    transition: border-color .15s ease !important;
}
.card:hover { border-color: #c4bdf8 !important; }

/* Titre produit */
.card h3 {
    font-family: Georgia, serif !important;
    font-weight: 400 !important;
    color: #1e1208 !important;
    font-size: 15px !important;
    line-height: 1.4 !important;
    margin: 0 !important;
}

/* Prix */
.card p {
    font-family: Georgia, serif !important;
    font-weight: 400 !important;
    color: #7c6cf0 !important;
    font-size: 16px !important;
    margin: 0 !important;
}

/* Bouton voir */
.btn.btn-primary.btn-sm {
    background: #7c6cf0 !important;
    color: #fff !important;
    border: none !important;
    border-radius: 8px !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    box-shadow: none !important;
    padding: 7px 16px !important;
    margin-top: auto !important;
    align-self: flex-start !important;
}
.btn.btn-primary.btn-sm:hover { background: #6558d4 !important; }
</style>
