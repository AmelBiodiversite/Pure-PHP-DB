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
<style>
/* Layout principal */
.catalog-grid {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: var(--space-8);
}

/* Sidebar */
.filters-sidebar {
    position: sticky;
    top: 100px;
    height: fit-content;
    transition: all 0.3s ease;
}

.filters-card {
    padding: var(--space-6);
}

.filter-section {
    margin-bottom: var(--space-6);
    padding-top: var(--space-6);
    border-top: 1px solid var(--border-color);
}

.filter-section:first-child {
    padding-top: 0;
    border-top: none;
}

.filter-title {
    font-size: 1rem;
    margin-bottom: var(--space-4);
    font-weight: 600;
}

/* Catégories */
.categories-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.category-item {
    margin-bottom: var(--space-2);
}

.category-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-2);
    border-radius: var(--radius-sm);
    color: var(--text-secondary);
    text-decoration: none;
    transition: all 0.2s;
}

.category-link:hover {
    background: var(--bg-secondary);
}

.category-link.active {
    color: var(--primary-600);
    font-weight: 600;
}

.category-icon {
    margin-right: var(--space-2);
}

.category-link .count {
    color: var(--text-tertiary);
    font-size: 0.875rem;
}

/* Filtres de prix */
.price-filter {
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
}

.price-inputs {
    display: flex;
    flex-direction: column;
    gap: var(--space-2);
}

.price-range {
    width: 100%;
    cursor: pointer;
    accent-color: var(--primary-600);
    height: 6px;
    border-radius: 3px;
}

.price-labels {
    display: flex;
    gap: var(--space-2);
    font-size: 0.875rem;
    color: var(--text-secondary);
    justify-content: space-between;
}

/* Tags */
.tags-container {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-2);
}

.badge-secondary {
    background: var(--bg-secondary);
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s;
}

.badge-secondary:hover {
    background: var(--primary-100);
    color: var(--primary-600);
}

/* Toolbar */
.catalog-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: var(--space-4);
    margin-bottom: var(--space-6);
    padding: var(--space-3);
    background: var(--bg-primary);
    border-radius: var(--radius-md);
}

.results-count {
    color: var(--text-secondary);
    font-size: 0.95rem;
}

.sort-container {
    display: flex;
    gap: var(--space-3);
    align-items: center;
}

.sort-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
    white-space: nowrap;
}

.form-select {
    min-width: 180px;
    padding: var(--space-2) var(--space-3);
}

/* Grille de produits */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--space-6);
}

/* Card produit - Marges intérieures améliorées */
.product-card {
    display: flex;
    flex-direction: column;
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    animation: fadeIn 0.5s ease-out;
}

.product-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.product-link {
    text-decoration: none;
    color: inherit;
}

.product-image-container {
    position: relative;
    width: 100%;
    padding-bottom: 66.67%;
    overflow: hidden;
    background: var(--bg-secondary);
}

.product-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-content {
    padding: var(--space-5);
    display: flex;
    flex-direction: column;
    flex: 1;
    gap: var(--space-3);
}

.product-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: var(--space-2);
    flex-wrap: wrap;
}

.product-rating {
    display: flex;
    gap: var(--space-1);
    align-items: center;
}

.rating-star {
    color: var(--warning);
    font-size: 0.875rem;
}

.rating-score {
    font-size: 0.875rem;
    font-weight: 600;
}

.rating-count {
    font-size: 0.75rem;
    color: var(--text-tertiary);
}

.product-title {
    font-size: 1.05rem;
    margin: 0;
    line-height: 1.4;
    min-height: 2.8em;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s;
}

.product-title a:hover {
    color: var(--primary-600);
}

.product-seller {
    font-size: 0.875rem;
    color: var(--text-tertiary);
    margin: 0;
}

.seller-link {
    color: var(--primary-600);
    text-decoration: none;
}

.seller-link:hover {
    text-decoration: underline;
}

.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    padding-top: var(--space-3);
    border-top: 1px solid var(--border-color);
    gap: var(--space-3);
}

.price-container {
    display: flex;
    flex-direction: column;
    gap: var(--space-1);
}

.product-price {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--primary-600);
    line-height: 1;
}

.product-price-original {
    font-size: 0.85rem;
    color: var(--text-tertiary);
    text-decoration: line-through;
}

.product-actions {
    display: flex;
    gap: var(--space-2);
    align-items: center;
    flex-shrink: 0;
}

.btn-wishlist {
    padding: 0.5rem 0.75rem;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 42px;
    height: 38px;
}

.btn-wishlist:hover {
    border-color: var(--primary-600);
    background: var(--primary-50);
}

.btn-wishlist.in-wishlist {
    background: var(--error-50);
    border-color: var(--error-600);
}

.wishlist-icon {
    font-size: 1.25rem;
    line-height: 1;
}

.product-stats {
    display: flex;
    gap: var(--space-4);
    font-size: 0.8rem;
    color: var(--text-tertiary);
    padding-top: var(--space-2);
}

/* État vide */
.empty-state {
    padding: var(--space-16) var(--space-8);
    text-align: center;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: var(--space-4);
}

.empty-title {
    margin-bottom: var(--space-3);
}

.empty-description {
    color: var(--text-secondary);
    margin-bottom: var(--space-6);
}

/* Pagination */
.pagination-container {
    display: flex;
    justify-content: center;
    gap: var(--space-2);
    margin-top: var(--space-12);
    flex-wrap: wrap;
}

/* Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
.toggle-filters {
    display: none;
}

/* Tablette - 1024px et moins */
@media (max-width: 1024px) {
    .catalog-grid {
        grid-template-columns: 1fr;
        gap: var(--space-6);
    }
    
    .filters-sidebar {
        position: fixed;
        top: 0;
        left: -100%;
        width: 320px;
        height: 100vh;
        background: var(--bg-primary);
        z-index: 1000;
        overflow-y: auto;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        transition: left 0.3s ease;
    }
    
    .filters-sidebar.active {
        left: 0;
    }
    
    .toggle-filters {
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: var(--space-5);
    }
}

/* Tablette moyenne - 768px et moins */
@media (max-width: 768px) {
    .container {
        padding-left: var(--space-4);
        padding-right: var(--space-4);
    }
    
    .catalog-toolbar {
        flex-direction: row;
        justify-content: space-between;
        padding: var(--space-3);
        gap: var(--space-3);
    }
    
    .results-count {
        order: 2;
        flex: 1;
        text-align: center;
        font-size: 0.875rem;
    }
    
    .toggle-filters {
        order: 1;
    }
    
    .sort-container {
        order: 3;
        width: 100%;
        justify-content: space-between;
    }
    
    .form-select {
        flex: 1;
        min-width: 0;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: var(--space-4);
    }
    
    .product-content {
        padding: var(--space-4);
        gap: var(--space-2);
    }
    
    .product-title {
        font-size: 0.95rem;
        min-height: 2.6em;
    }
    
    .product-price {
        font-size: 1.15rem;
    }
    
    .product-stats {
        gap: var(--space-3);
        font-size: 0.75rem;
    }
    
    .filters-sidebar {
        width: 280px;
    }
}

/* Mobile - 480px et moins */
@media (max-width: 480px) {
    .container {
        padding-left: var(--space-3);
        padding-right: var(--space-3);
    }
    
    .mb-8 h1 {
        font-size: 1.5rem;
    }
    
    .catalog-toolbar {
        padding: var(--space-2);
        gap: var(--space-2);
    }
    
    .results-count {
        font-size: 0.8rem;
    }
    
    .products-grid {
        grid-template-columns: 1fr;
        gap: var(--space-4);
    }
    
    .product-card {
        max-width: 100%;
    }
    
    .product-content {
        padding: var(--space-3);
    }
    
    .product-header {
        flex-direction: row;
        justify-content: space-between;
        align-items: flex-start;
    }
    
    .product-title {
        font-size: 0.9rem;
    }
    
    .product-footer {
        flex-direction: column;
        align-items: stretch;
        gap: var(--space-3);
    }
    
    .price-container {
        flex-direction: row;
        align-items: center;
        gap: var(--space-2);
    }
    
    .product-actions {
        width: 100%;
        justify-content: space-between;
    }
    
    .btn-wishlist {
        flex: 0 0 auto;
    }
    
    .product-actions .btn {
        flex: 1;
    }
    
    .pagination-container {
        gap: var(--space-1);
        margin-top: var(--space-8);
    }
    
    .pagination-container .btn {
        padding: var(--space-2) var(--space-3);
        font-size: 0.875rem;
        min-width: 38px;
    }
    
    .filters-sidebar {
        width: 100%;
        max-width: 300px;
    }
    
    .filters-card {
        padding: var(--space-4);
    }
}

/* Très petit mobile - 360px et moins */
@media (max-width: 360px) {
    .product-price {
        font-size: 1rem;
    }
    
    .product-stats {
        font-size: 0.7rem;
        gap: var(--space-2);
    }
    
    .pagination-container .btn {
        padding: var(--space-1) var(--space-2);
        font-size: 0.8rem;
    }
}

/* Overlay pour le menu mobile */
.filters-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
}

@media (max-width: 1024px) {
    .filters-overlay.active {
        display: block;
    }
}
</style>
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