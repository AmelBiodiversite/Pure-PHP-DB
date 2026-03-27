<?php
/**
 * MARKETFLOW PRO - PAGE RÉSULTATS DE RECHERCHE
 * Fichier : app/views/products/search.php
 * 
 * Affiche les résultats de la recherche plein texte avec :
 * - Résumé de la requête et nombre de résultats
 * - Grille de produits identique au catalogue
 * - Pagination
 * - Possibilité de relancer une nouvelle recherche
 */
?>
<div class="container mt-8 mb-16">

    <!-- ======================================================
         EN-TÊTE : Résumé de la recherche
         ====================================================== -->
    <div class="search-header mb-8">

        <!-- Titre avec le mot recherché -->
        <h1 class="search-title">
            Résultats pour :
            <span class="search-query">« <?= e($query) ?> »</span>
        </h1>

        <!-- Sous-titre : nombre de résultats -->
        <p class="text-secondary" style="font-size: 1.1rem; margin-top: 0.5rem;">
            <?php if ($pagination['total_items'] > 0): ?>
                <?= number_format($pagination['total_items']) ?>
                produit<?= $pagination['total_items'] > 1 ? 's' : '' ?> trouvé<?= $pagination['total_items'] > 1 ? 's' : '' ?>
            <?php else: ?>
                Aucun produit ne correspond à votre recherche.
            <?php endif; ?>
        </p>

        <!-- Formulaire pour relancer une nouvelle recherche -->
        <form action="/products/search" method="GET" class="search-form mt-4">
            <div class="search-form-inner">
                <input
                    type="text"
                    name="q"
                    value="<?= e($query) ?>"
                    placeholder="Rechercher un autre produit..."
                    class="search-form-input"
                    aria-label="Modifier la recherche">
                <button type="submit" class="btn btn-primary">
                    Rechercher
                </button>
            </div>
        </form>
    </div>

    <!-- ======================================================
         GRILLE DE RÉSULTATS ou ÉTAT VIDE
         ====================================================== -->
    <?php if (empty($products)): ?>

        <!-- État vide : aucun résultat -->
        <div class="card text-center empty-state">
            <div class="empty-icon">🔍</div>
            <h3 class="empty-title">Aucun résultat trouvé</h3>
            <p class="empty-description">
                Essayez avec des mots-clés différents ou explorez le catalogue.
            </p>
            <a href="/products" class="btn btn-primary">
                Voir tous les produits
            </a>
        </div>

    <?php else: ?>

        <!-- Grille de produits (même structure que products/index.php) -->
        <div class="products-grid">
            <?php foreach ($products as $product): ?>

                <article class="product-card">

                    <!-- Lien image -->
                    <a href="/products/<?= e($product['slug']) ?>" class="product-link">
                        <div class="product-image-container">
                            <img
                                src="<?= e($product['thumbnail_url'] ?? '/public/img/placeholder.png') ?>"
                                alt="<?= e($product['title']) ?>"
                                class="product-image"
                                loading="lazy"
                                width="300"
                                height="200">
                        </div>
                    </a>

                    <!-- Contenu de la card -->
                    <div class="product-content">

                        <div class="product-header">
                            <span class="badge badge-primary">
                                <?= e($product['category_name']) ?>
                            </span>
                            <?php if (!empty($product['rating_count']) && $product['rating_count'] > 0): ?>
                                <div class="product-rating">
                                    <span class="rating-star">★</span>
                                    <span class="rating-score">
                                        <?= number_format($product['rating_average'], 1) ?>
                                    </span>
                                    <span class="rating-count">
                                        (<?= e($product['rating_count']) ?>)
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <h3 class="product-title">
                            <a href="/products/<?= e($product['slug']) ?>">
                                <?= e($product['title']) ?>
                            </a>
                        </h3>

                        <p class="product-seller">
                            Par
                            <a href="/seller/<?= urlencode($product['seller_name']) ?>" class="seller-link">
                                <?= e($product['seller_name']) ?>
                            </a>
                        </p>

                        <div class="product-footer">
                            <div class="price-container">
                                <span class="product-price">
                                    <?= formatPrice($product['price']) ?>
                                </span>
                                <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                    <span class="product-price-original">
                                        <?= formatPrice($product['original_price']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <a href="/products/<?= e($product['slug']) ?>" class="btn btn-primary btn-sm">
                                Voir
                            </a>
                        </div>

                    </div>
                </article>

            <?php endforeach; ?>
        </div>

        <!-- ======================================================
             PAGINATION
             ====================================================== -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <nav class="pagination-container mt-12" aria-label="Pagination des résultats">

                <?php if ($pagination['current'] > 1): ?>
                    <a href="/products/search?q=<?= urlencode($query) ?>&page=<?= $pagination['current'] - 1 ?>"
                       class="btn btn-secondary btn-sm"
                       rel="prev">
                        ← Précédent
                    </a>
                <?php endif; ?>

                <?php
                    // Afficher au maximum 5 numéros de page autour de la page courante
                    $start = max(1, $pagination['current'] - 2);
                    $end   = min($pagination['total_pages'], $pagination['current'] + 2);
                    for ($i = $start; $i <= $end; $i++):
                ?>
                    <a href="/products/search?q=<?= urlencode($query) ?>&page=<?= $i ?>"
                       class="btn btn-sm <?= $i === $pagination['current'] ? 'btn-primary' : 'btn-secondary' ?>"
                       <?= $i === $pagination['current'] ? 'aria-current="page"' : '' ?>>
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagination['current'] < $pagination['total_pages']): ?>
                    <a href="/products/search?q=<?= urlencode($query) ?>&page=<?= $pagination['current'] + 1 ?>"
                       class="btn btn-secondary btn-sm"
                       rel="next">
                        Suivant →
                    </a>
                <?php endif; ?>

            </nav>
        <?php endif; ?>

    <?php endif; ?>

</div>

<style>
/* ============================================================
   STYLES SPÉCIFIQUES À LA PAGE RECHERCHE
   ============================================================ */

/* En-tête de recherche */
.search-header {
    border-bottom: 1px solid var(--border-color);
    padding-bottom: var(--space-6);
}

.search-title {
    font-size: 1.75rem;
    font-weight: 700;
}

/* Mot recherché mis en valeur */
.search-query {
    color: var(--primary-600);
}

/* Formulaire de nouvelle recherche */
.search-form-inner {
    display: flex;
    gap: var(--space-3);
    max-width: 560px;
}

.search-form-input {
    flex: 1;
    padding: 0.65rem 1rem;
    border: 1.5px solid var(--border-color);
    border-radius: var(--radius-md);
    font-size: 1rem;
    font-family: inherit;
    background: var(--bg-secondary);
    color: var(--text-primary);
    outline: none;
    transition: border-color 0.2s;
}

.search-form-input:focus {
    border-color: var(--primary-600);
}

/* Réutilisation de la grille catalogue */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--space-6);
}

/* États vides */
.empty-state {
    padding: var(--space-16) var(--space-8);
    text-align: center;
}
.empty-icon  { font-size: 4rem; margin-bottom: var(--space-4); }
.empty-title { margin-bottom: var(--space-3); }
.empty-description { color: var(--text-secondary); margin-bottom: var(--space-6); }

/* Pagination */
.pagination-container {
    display: flex;
    justify-content: center;
    gap: var(--space-2);
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .search-title    { font-size: 1.35rem; }
    .search-form-inner { flex-direction: column; }
    .products-grid   { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
}

@media (max-width: 480px) {
    .products-grid { grid-template-columns: 1fr; }
}
</style>
