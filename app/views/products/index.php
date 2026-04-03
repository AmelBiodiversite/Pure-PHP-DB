<?php
/**
 * MARKETFLOW PRO - PAGE CATALOGUE PRODUITS
 * Fichier : app/views/products/index.php
 */
// Récupérer les IDs des produits en favoris de l'utilisateur
$wishlistIds = [];
if (isset($_SESSION['user_id'])) {
    $wishlistModel = new \App\Models\Wishlist();
    $wishlistIds = $wishlistModel->getUserWishlistIds($_SESSION['user_id']);
}
// Helper function pour les query params (améliorée pour supporter la suppression)
function updateQueryParam($key, $value = null) {
    $params = $_GET;
    if ($value === null) {
        unset($params[$key]);
    } else {
        $params[$key] = $value;
    }
    $query = http_build_query($params);
    return '/products' . ($query ? '?' . $query : '');
}
?>
<div class="container mt-8 mb-16">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="mb-4">Catalogue de Produits</h1>
        <p class="text-secondary" style="font-size: 1.125rem;">
            Découvrez <?= number_format($pagination['total_items']) ?> produits digitaux premium
        </p>
    </div>
    <div class="catalog-grid">
        <!-- SIDEBAR FILTRES -->
        <aside class="filters-sidebar">
            <div class="card filters-card">
                <!-- Catégories -->
                <div class="filter-section">
                    <h3 class="filter-title">Catégories</h3>
                    <ul class="categories-list">
                        <li class="category-item">
                            <a href="<?= updateQueryParam('category', null) ?>"
                               class="category-link <?= empty($active_filters['category_id']) ? 'active' : '' ?>">
                                <span>Toutes</span>
                                <span class="count"><?= number_format($pagination['total_items']) ?></span>
                            </a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                        <li class="category-item">
                            <a href="<?= updateQueryParam('category', $cat['id']) ?>"
                               class="category-link <?= ($active_filters['category_id'] ?? null) == $cat['id'] ? 'active' : '' ?>">
                                <span>
                                    <?php if (!empty($cat['icon'])): ?>
                                        <span class="category-icon"><?= e($cat['icon']) ?></span>
                                    <?php endif; ?>
                                    <?= e($cat['name']) ?>
                                </span>
                                <span class="badge badge-primary"><?= e($cat['product_count']) ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <!-- Prix -->
                <div class="filter-section">
                    <h3 class="filter-title">Prix</h3>
                    <div class="price-filter">
                        <div class="price-inputs">
                            <input type="range"
                                   id="priceMin"
                                   class="price-range"
                                   min="<?= $price_range['min_price'] ?? 0 ?>"
                                   max="<?= $price_range['max_price'] ?? 1000 ?>"
                                   value="<?= $active_filters['min_price'] ?? $price_range['min_price'] ?? 0 ?>"
                                   aria-label="Prix minimum">
                            <input type="range"
                                   id="priceMax"
                                   class="price-range"
                                   min="<?= $price_range['min_price'] ?? 0 ?>"
                                   max="<?= $price_range['max_price'] ?? 1000 ?>"
                                   value="<?= $active_filters['max_price'] ?? $price_range['max_price'] ?? 1000 ?>"
                                   aria-label="Prix maximum">
                        </div>
                        <div class="price-labels">
                            <span id="priceMinLabel">
                                <?= formatPrice($active_filters['min_price'] ?? $price_range['min_price'] ?? 0) ?>
                            </span>
                            <span>-</span>
                            <span id="priceMaxLabel">
                                <?= formatPrice($active_filters['max_price'] ?? $price_range['max_price'] ?? 1000) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Tags populaires -->
                <div class="filter-section">
                    <h3 class="filter-title">Tags populaires</h3>
                    <div class="tags-container">
                        <?php foreach (array_slice($popular_tags, 0, 10) as $tag): ?>
                        <a href="<?= updateQueryParam('tag', urlencode($tag['slug'])) ?>"
                           class="badge <?= ($active_filters['tag'] ?? null) == $tag['slug'] ? 'badge-primary' : 'badge-secondary' ?>">
                            <?= e($tag['name']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </aside>
        <!-- CONTENU PRINCIPAL -->
        <main class="catalog-main">
            <!-- Barre de tri et recherche -->
            <div class="catalog-toolbar">
                <!-- Bouton toggle filtres (mobile) -->
                <button id="toggleFilters" class="btn btn-secondary btn-sm toggle-filters" aria-label="Ouvrir les filtres">
                    Filtres
                </button>
                <!-- Résultats -->
                <div class="results-count">
                    <?= number_format($pagination['total_items']) ?>
                    produit<?= $pagination['total_items'] > 1 ? 's' : '' ?>
                    trouvé<?= $pagination['total_items'] > 1 ? 's' : '' ?>
                </div>
                <!-- Tri -->
                <div class="sort-container">
                    <label for="sortSelect" class="sort-label">Trier par :</label>
                    <select
                        id="sortSelect"
                        class="form-select"
                        onchange="window.location.href = this.value">
                        <option value="<?= updateQueryParam('sort', 'newest') ?>" <?= ($active_filters['sort'] ?? 'newest') == 'newest' ? 'selected' : '' ?>>
                            Plus récents
                        </option>
                        <option value="<?= updateQueryParam('sort', 'popular') ?>" <?= ($active_filters['sort'] ?? '') == 'popular' ? 'selected' : '' ?>>
                            Plus populaires
                        </option>
                        <option value="<?= updateQueryParam('sort', 'price_asc') ?>" <?= ($active_filters['sort'] ?? '') == 'price_asc' ? 'selected' : '' ?>>
                            Prix croissant
                        </option>
                        <option value="<?= updateQueryParam('sort', 'price_desc') ?>" <?= ($active_filters['sort'] ?? '') == 'price_desc' ? 'selected' : '' ?>>
                            Prix décroissant
                        </option>
                        <option value="<?= updateQueryParam('sort', 'rating') ?>" <?= ($active_filters['sort'] ?? '') == 'rating' ? 'selected' : '' ?>>
                            Meilleures notes
                        </option>
                    </select>
                </div>
            </div>
            <!-- Grille de produits -->
            <?php if (empty($products)): ?>
                <div class="card text-center empty-state">
                    <div class="empty-icon">🔍</div>
                    <h3 class="empty-title">Aucun produit trouvé</h3>
                    <p class="empty-description">
                        Essayez de modifier vos critères de recherche
                    </p>
                    <a href="<?= updateQueryParam('reset', null) ?>" class="btn btn-primary">Voir tous les produits</a>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product):
                        $isInWishlist = in_array($product['id'], $wishlistIds);
                    ?>
                    <article class="product-card">
                        <!-- Image -->
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
                        <!-- Contenu -->
                        <div class="product-content">
                            <!-- Header -->
                            <div class="product-header">
                                <span class="badge badge-primary">
                                    <?= e($product['category_name']) ?>
                                </span>
                                <?php if ($product['rating_count'] > 0): ?>
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
                            <!-- Titre -->
                            <h3 class="product-title">
                                <a href="/products/<?= e($product['slug']) ?>">
                                    <?= e($product['title']) ?>
                                </a>
                            </h3>
                            <!-- Vendeur -->
                            <p class="product-seller">
                                Par
                                <a href="/seller/<?= urlencode($product['seller_name']) ?>" class="seller-link">
                                    <?= e($product['shop_name'] ?? $product['seller_name']) ?>
                                </a>
                            </p>
                            <!-- Prix et actions -->
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
                                <!-- Boutons d'action -->
                                <div class="product-actions">
                                    <!-- Bouton Wishlist -->
                                    <button
                                        type="button"
                                        class="btn-wishlist <?= $isInWishlist ? 'in-wishlist' : '' ?>"
                                        data-product-id="<?= e($product['id']) ?>"
                                        title="<?= $isInWishlist ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>"
                                        aria-label="<?= $isInWishlist ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>">
                                        <span class="wishlist-icon">
                                            <?= $isInWishlist ? '❤️' : '🤍' ?>
                                        </span>
                                    </button>
                                    <!-- Bouton Voir -->
                                    <a href="/products/<?= e($product['slug']) ?>" class="btn btn-primary btn-sm">
                                        Voir
                                    </a>
                                </div>
                            </div>
                            <!-- Stats -->
                            <div class="product-stats">
                                <span>👁️ <?= number_format($product['views_count'] ?? 0) ?></span>
                                <span>💰 <?= number_format($product['sales_count'] ?? 0) ?> ventes</span>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                <nav class="pagination-container" aria-label="Pagination">
                    <?php if ($pagination['current'] > 1): ?>
                    <a href="<?= updateQueryParam('page', $pagination['current'] - 1) ?>"
                       class="btn btn-secondary btn-sm"
                       rel="prev">
                        ← Précédent
                    </a>
                    <?php endif; ?>
                    <?php
                    $start = max(1, $pagination['current'] - 2);
                    $end = min($pagination['total_pages'], $pagination['current'] + 2);
                    for ($i = $start; $i <= $end; $i++):
                    ?>
                    <a href="<?= updateQueryParam('page', $i) ?>"
                       class="btn btn-sm <?= $i == $pagination['current'] ? 'btn-primary' : 'btn-secondary' ?>"
                       <?= $i == $pagination['current'] ? 'aria-current="page"' : '' ?>>
                        <?= e($i) ?>
                    </a>
                    <?php endfor; ?>
                    <?php if ($pagination['current'] < $pagination['total_pages']): ?>
                    <a href="<?= updateQueryParam('page', $pagination['current'] + 1) ?>"
                       class="btn btn-secondary btn-sm"
                       rel="next">
                        Suivant →
                    </a>
                    <?php endif; ?>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
// Filtres de prix avec debouncing et validation min/max
(function() {
    const priceMin = document.getElementById('priceMin');
    const priceMax = document.getElementById('priceMax');
    const priceMinLabel = document.getElementById('priceMinLabel');
    const priceMaxLabel = document.getElementById('priceMaxLabel');
    if (!priceMin || !priceMax) return;
    let priceTimeout;
    function formatPrice(price) {
        return parseFloat(price).toFixed(2).replace('.', ',') + ' €';
    }
    function applyPriceFilter() {
        const url = new URL(window.location.href);
        url.searchParams.set('min_price', priceMin.value);
        url.searchParams.set('max_price', priceMax.value);
        window.location.href = url.toString();
    }
    function updateLabels() {
        priceMinLabel.textContent = formatPrice(priceMin.value);
        priceMaxLabel.textContent = formatPrice(priceMax.value);
    }
    priceMin.addEventListener('input', function() {
        if (parseFloat(this.value) > parseFloat(priceMax.value)) {
            this.value = priceMax.value;
        }
        updateLabels();
        clearTimeout(priceTimeout);
        priceTimeout = setTimeout(applyPriceFilter, 500);
    });
    priceMax.addEventListener('input', function() {
        if (parseFloat(this.value) < parseFloat(priceMin.value)) {
            this.value = priceMin.value;
        }
        updateLabels();
        clearTimeout(priceTimeout);
        priceTimeout = setTimeout(applyPriceFilter, 500);
    });
})();

// Animation des cards au scroll
(function() {
    if (!('IntersectionObserver' in window)) return;
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '50px'
    });
    document.querySelectorAll('.product-card').forEach(card => {
        observer.observe(card);
    });
})();

// Gestion des boutons wishlist
document.addEventListener('click', async function(e) {
    if (e.target.closest('.btn-wishlist')) {
        const btn = e.target.closest('.btn-wishlist');
        const productId = btn.dataset.productId;
        const isAdding = !btn.classList.contains('in-wishlist');
       
        try {
            const url = isAdding ? '/wishlist/add' : '/wishlist/remove';
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ product_id: productId, add: isAdding })
            });
           
            if (response.ok) {
                btn.classList.toggle('in-wishlist');
                const icon = btn.querySelector('.wishlist-icon');
                icon.textContent = btn.classList.contains('in-wishlist') ? '❤️' : '🤍';
                btn.title = btn.classList.contains('in-wishlist') ? 'Retirer des favoris' : 'Ajouter aux favoris';
                btn.setAttribute('aria-label', btn.title);
            } else {
                console.error('Erreur lors de la mise à jour de la wishlist');
            }
        } catch (error) {
            console.error('Erreur réseau:', error);
        }
    }
});

// Toggle filtres sur mobile avec overlay
(function() {
    const toggleBtn = document.getElementById('toggleFilters');
    const sidebar = document.querySelector('.filters-sidebar');
    if (!toggleBtn || !sidebar) return;

    // Créer l'overlay
    const overlay = document.createElement('div');
    overlay.className = 'filters-overlay';
    document.body.appendChild(overlay);

    function openFilters() {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        toggleBtn.textContent = 'Fermer filtres';
        toggleBtn.setAttribute('aria-label', 'Fermer les filtres');
        document.body.style.overflow = 'hidden'; // Empêcher le scroll
    }

    function closeFilters() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        toggleBtn.textContent = 'Filtres';
        toggleBtn.setAttribute('aria-label', 'Ouvrir les filtres');
        document.body.style.overflow = ''; // Restaurer le scroll
    }

    toggleBtn.addEventListener('click', function() {
        if (sidebar.classList.contains('active')) {
            closeFilters();
        } else {
            openFilters();
        }
    });

    // Fermer les filtres si clic sur l'overlay
    overlay.addEventListener('click', closeFilters);

    // Fermer avec la touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('active')) {
            closeFilters();
        }
    });
})();
</script>
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
