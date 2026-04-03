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
/* === DESIGN MAQUETTE2 — PAGES PRODUITS === */
.container{background:#faf9f5}
h1,h2,h3{font-family:Georgia,serif;font-weight:400;color:#1e1208}
.catalog-grid{display:grid;grid-template-columns:250px 1fr;gap:20px;align-items:start}
.filters-sidebar{position:sticky;top:80px;height:fit-content}
.filters-card,.card{background:#fff;border:0.5px solid #ede8df;border-radius:14px;padding:22px;box-shadow:none}
.filter-section{margin-bottom:20px;padding-top:20px;border-top:0.5px solid #ede8df}
.filter-section:first-child{padding-top:0;border-top:none}
.filter-title{font-family:Georgia,serif;font-size:15px;font-weight:400;color:#1e1208;margin-bottom:10px}
.categories-list{list-style:none;padding:0;margin:0}
.category-item{margin-bottom:2px}
.category-link{display:flex;justify-content:space-between;align-items:center;padding:7px 10px;border-radius:8px;font-family:'Manrope',sans-serif;font-size:13px;color:#6b5c4e;text-decoration:none;transition:all 0.15s}
.category-link:hover{background:#faf9f5;color:#1e1208}
.category-link.active{color:#534ab7;background:#ede9fe;font-weight:500}
.category-link .count{font-size:11px;color:#a0907e}
.price-range{width:100%;accent-color:#7c6cf0;height:4px}
.price-labels{display:flex;justify-content:space-between;font-family:'Manrope',sans-serif;font-size:12px;color:#a0907e;margin-top:8px}
.tags-container{display:flex;flex-wrap:wrap;gap:6px}
.badge-secondary{background:#f5f1eb;color:#6b5c4e;border-radius:6px;font-family:'Manrope',sans-serif;font-size:11px;padding:4px 10px;text-decoration:none;transition:all 0.15s;cursor:pointer;border:none}
.badge-secondary:hover{background:#ede9fe;color:#534ab7}
.badge-primary{background:#ede9fe!important;color:#534ab7!important;border-radius:6px;font-family:'Manrope',sans-serif;font-size:11px;padding:3px 9px;text-decoration:none;border:none}
.catalog-toolbar{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:18px;padding:11px 14px;background:#fff;border:0.5px solid #ede8df;border-radius:11px}
.results-count{font-family:'Manrope',sans-serif;font-size:13px;color:#8a7060}
.sort-label{font-family:'Manrope',sans-serif;font-size:12px;color:#8a7060;white-space:nowrap}
.form-select{padding:7px 12px;border:0.5px solid #ddd6c8;border-radius:8px;background:#faf9f5;font-family:'Manrope',sans-serif;font-size:12px;color:#1e1208;outline:none;cursor:pointer;min-width:155px}
.form-select:focus{border-color:#7c6cf0}
.products-grid,.grid.grid-4{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:14px!important}
.product-card{background:#fff!important;border:0.5px solid #ede8df!important;border-radius:14px!important;overflow:hidden;display:flex;flex-direction:column;box-shadow:none!important;transition:transform 0.2s ease}
.product-card:hover{transform:translateY(-3px);box-shadow:none!important}
.product-image-container{position:relative;width:100%;padding-bottom:62%;overflow:hidden;background:#f5f1eb;display:block;text-decoration:none}
.product-image{position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;transition:transform 0.3s}
.product-card:hover .product-image{transform:scale(1.04)}
.product-content{padding:14px;display:flex;flex-direction:column;flex:1;gap:7px}
.product-header{display:flex;justify-content:space-between;align-items:center;gap:6px;flex-wrap:wrap}
.product-rating{display:flex;gap:4px;align-items:center}
.rating-star{color:#ba7517;font-size:12px}
.rating-score{font-family:'Manrope',sans-serif;font-size:12px;font-weight:500;color:#1e1208}
.rating-count{font-family:'Manrope',sans-serif;font-size:11px;color:#a0907e}
.product-title{font-family:Georgia,serif;font-size:15px;font-weight:400;color:#1e1208;line-height:1.4;margin:0;min-height:2.4em;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.product-title a{color:inherit;text-decoration:none}
.product-title a:hover{color:#7c6cf0}
.product-seller{font-family:'Manrope',sans-serif;font-size:11px;color:#a0907e;margin:0}
.seller-link{color:#7c6cf0;text-decoration:none}
.product-footer{display:flex;justify-content:space-between;align-items:center;margin-top:auto;padding-top:10px;border-top:0.5px solid #ede8df;gap:8px}
.price-container{display:flex;flex-direction:column;gap:2px}
.product-price,.price-current{font-family:Georgia,serif;font-size:18px;font-weight:400;color:#1e1208;line-height:1}
.product-price-original,.price-old{font-family:'Manrope',sans-serif;font-size:11px;color:#a0907e;text-decoration:line-through}
.product-actions{display:flex;gap:6px;align-items:center;flex-shrink:0}
.btn-wishlist{width:32px;height:32px;background:#fff;border:0.5px solid #ede8df;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.15s;font-size:13px;padding:0}
.btn-wishlist:hover{border-color:#d4537e;background:#fbeaf0}
.btn-wishlist.in-wishlist{background:#fbeaf0;border-color:#d4537e}
.product-stats{display:flex;gap:12px;font-family:'Manrope',sans-serif;font-size:11px;color:#a0907e}
.product-badge,.product-category{background:#ede9fe;color:#534ab7;border-radius:6px;font-family:'Manrope',sans-serif;font-size:10px;padding:3px 8px;position:absolute;top:10px;left:10px}
.product-description{font-family:'Manrope',sans-serif;font-size:12px;color:#8a7060;line-height:1.6;margin:0}
.empty-state{padding:64px 32px;text-align:center;background:#fff;border:0.5px solid #ede8df;border-radius:14px}
.empty-icon{font-size:48px;margin-bottom:16px}
.empty-title{font-family:Georgia,serif;font-size:20px;font-weight:400;color:#1e1208;margin-bottom:10px}
.empty-description{font-family:'Manrope',sans-serif;font-size:13px;color:#8a7060;margin-bottom:20px}
.btn,.btn-primary,.btn.btn-primary{background:#7c6cf0!important;color:#fff!important;border:none!important;border-radius:8px;font-family:'Manrope',sans-serif;font-size:12px;font-weight:500;padding:7px 14px;text-decoration:none;transition:background 0.15s;cursor:pointer;display:inline-block}
.btn:hover,.btn-primary:hover,.btn.btn-primary:hover{background:#6558d4!important;color:#fff!important}
.btn-secondary,.btn.btn-secondary{background:#f5f1eb!important;color:#6b5c4e!important;border:0.5px solid #ddd6c8!important;border-radius:8px;font-family:'Manrope',sans-serif;font-size:12px;padding:7px 14px;text-decoration:none;transition:all 0.15s;cursor:pointer;display:inline-block}
.btn-secondary:hover,.btn.btn-secondary:hover{background:#ede8df!important;color:#1e1208!important}
.btn-sm{padding:5px 10px!important;font-size:11px!important}
.btn-ghost{background:transparent!important;color:#6b5c4e!important;border:0.5px solid #ddd6c8!important}
.btn-ghost:hover{background:#faf9f5!important;color:#1e1208!important}
.btn-outline{background:transparent!important;color:#7c6cf0!important;border:0.5px solid #7c6cf0!important}
.btn-outline:hover{background:#ede9fe!important}
.btn-icon{background:#ede9fe;color:#534ab7;border:none;border-radius:8px;cursor:pointer;transition:background 0.15s}
.btn-icon:hover{background:#c9c4f5}
.pagination-container{display:flex;justify-content:center;gap:6px;margin-top:40px;flex-wrap:wrap}
.search-header{border-bottom:0.5px solid #ede8df;padding-bottom:20px;margin-bottom:24px}
.search-title{font-family:Georgia,serif;font-size:26px;font-weight:400;color:#1e1208}
.search-query{color:#7c6cf0}
.search-form-inner{display:flex;gap:10px;max-width:520px}
.search-form-input{flex:1;padding:9px 14px;border:0.5px solid #ddd6c8;border-radius:10px;font-family:'Manrope',sans-serif;font-size:13px;background:#faf9f5;color:#1e1208;outline:none;transition:border-color 0.15s}
.search-form-input:focus{border-color:#7c6cf0;background:#fff}
.toggle-filters{display:none}
.filters-overlay{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.4);z-index:999}
@keyframes fadeIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
@media(max-width:1024px){
  .catalog-grid{grid-template-columns:1fr}
  .filters-sidebar{position:fixed;top:0;left:-100%;width:300px;height:100vh;background:#faf9f5;z-index:1000;overflow-y:auto;transition:left 0.3s;padding:20px}
  .filters-sidebar.active{left:0}
  .toggle-filters{display:flex;align-items:center;gap:6px}
  .filters-overlay.active{display:block}
}
@media(max-width:768px){.products-grid,.grid.grid-4{grid-template-columns:repeat(2,1fr)!important}}
@media(max-width:480px){.products-grid,.grid.grid-4{grid-template-columns:1fr!important}.search-form-inner{flex-direction:column}}
</style>
