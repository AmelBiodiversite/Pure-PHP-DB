<?php
/**
 * MARKETFLOW PRO - PAGE CATALOGUE PRODUITS
 * Fichier : app/views/products/index.php
 */
?>

<div class="container mt-8 mb-16">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="mb-4">Catalogue de Produits</h1>
        <p style="color: var(--text-secondary); font-size: 1.125rem;">
            Découvrez <?= number_format($pagination['total_items']) ?> produits digitaux premium
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 280px 1fr; gap: var(--space-8);">
        
        <!-- SIDEBAR FILTRES -->
        <aside style="position: sticky; top: 100px; height: fit-content;">
            
            <div class="card" style="padding: var(--space-6);">
                
                <!-- Catégories -->
                <div style="margin-bottom: var(--space-6);">
                    <h3 style="font-size: 1rem; margin-bottom: var(--space-4);">Catégories</h3>
                    <ul style="list-style: none;">
                        <li style="margin-bottom: var(--space-2);">
                            <a href="/products" style="
                                display: flex;
                                justify-content: space-between;
                                padding: var(--space-2);
                                border-radius: var(--radius-sm);
                                color: <?= empty($active_filters['category_id']) ? 'var(--primary-600)' : 'var(--text-secondary)' ?>;
                                font-weight: <?= empty($active_filters['category_id']) ? '600' : 'normal' ?>;
                            " onmouseover="this.style.background='var(--bg-secondary)'" 
                               onmouseout="this.style.background='transparent'">
                                <span>Toutes</span>
                                <span style="color: var(--text-tertiary);"><?= number_format($pagination['total_items']) ?></span>
                            </a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                        <li style="margin-bottom: var(--space-2);">
                            <a href="/products?category=<?= $cat['id'] ?>" style="
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                padding: var(--space-2);
                                border-radius: var(--radius-sm);
                                color: <?= $active_filters['category_id'] == $cat['id'] ? 'var(--primary-600)' : 'var(--text-secondary)' ?>;
                                font-weight: <?= $active_filters['category_id'] == $cat['id'] ? '600' : 'normal' ?>;
                            " onmouseover="this.style.background='var(--bg-secondary)'" 
                               onmouseout="this.style.background='transparent'">
                                <span>
                                    <?php if ($cat['icon']): ?>
                                        <span style="margin-right: var(--space-2);"><?= $cat['icon'] ?></span>
                                    <?php endif; ?>
                                    <?= e($cat['name']) ?>
                                </span>
                                <span class="badge badge-primary" style="font-size: 0.7rem;">
                                    <?= $cat['product_count'] ?>
                                </span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Prix -->
                <div style="margin-bottom: var(--space-6); padding-top: var(--space-6); border-top: 1px solid var(--border-color);">
                    <h3 style="font-size: 1rem; margin-bottom: var(--space-4);">Prix</h3>
                    <div style="margin-bottom: var(--space-3);">
                        <input type="range" 
                               id="priceMin" 
                               min="<?= $price_range['min_price'] ?? 0 ?>"
                               max="<?= $price_range['max_price'] ?? 1000 ?>"
                               value="<?= $active_filters['min_price'] ?? $price_range['min_price'] ?? 0 ?>"
                            style="width: 100%;">
                        <input type="range" 
                               id="priceMax" 
                               min="<?= $price_range['min_price'] ?>" 
                               max="<?= $price_range['max_price'] ?>"
                               value="<?= $active_filters['max_price'] ?? $price_range['max_price'] ?>"
                               style="width: 100%;">
                    </div>
                    <div style="display: flex; gap: var(--space-2); font-size: 0.875rem; color: var(--text-secondary);">
                        
                        <span id="priceMinLabel"><?= formatPrice($active_filters['min_price'] ?? $price_range['min_price'] ?? 0) ?></span>
                        <span>-</span>
                        <span id="priceMaxLabel"><?= formatPrice($active_filters['max_price'] ?? $price_range['max_price'] ?? 1000) ?></span>
                        
                    
                    </div>
                </div>

                <!-- Tags populaires -->
                <div style="padding-top: var(--space-6); border-top: 1px solid var(--border-color);">
                    <h3 style="font-size: 1rem; margin-bottom: var(--space-4);">Tags populaires</h3>
                    <div style="display: flex; flex-wrap: wrap; gap: var(--space-2);">
                        <?php foreach (array_slice($popular_tags, 0, 10) as $tag): ?>
                        <a href="/products?tag=<?= e($tag['slug']) ?>" 
                           class="badge <?= $active_filters['tag'] == $tag['slug'] ? 'badge-primary' : '' ?>"
                           style="
                               <?= $active_filters['tag'] != $tag['slug'] ? 'background: var(--bg-secondary); color: var(--text-secondary);' : '' ?>
                               cursor: pointer;
                           ">
                            <?= e($tag['name']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

        </aside>

        <!-- CONTENU PRINCIPAL -->
        <main>
            
            <!-- Barre de tri et recherche -->
            <div class="flex-between mb-6" style="flex-wrap: wrap; gap: var(--space-4);">
                
                <!-- Résultats -->
                <div style="color: var(--text-secondary);">
                    <?= number_format($pagination['total_items']) ?> produit<?= $pagination['total_items'] > 1 ? 's' : '' ?> trouvé<?= $pagination['total_items'] > 1 ? 's' : '' ?>
                </div>

                <!-- Tri -->
                <div class="flex gap-4" style="align-items: center;">
                    <label style="font-size: 0.875rem; color: var(--text-secondary);">Trier par :</label>
                    <select 
                        id="sortSelect" 
                        class="form-select" 
                        style="width: 200px; padding: var(--space-2) var(--space-3);"
                        onchange="window.location.href = updateQueryParam('sort', this.value)">
                        <option value="newest" <?= ($active_filters['sort'] ?? 'newest') == 'newest' ? 'selected' : '' ?>>
                            Plus récents
                        </option>
                        <option value="popular" <?= $active_filters['sort'] == 'popular' ? 'selected' : '' ?>>
                            Plus populaires
                        </option>
                        <option value="price_asc" <?= $active_filters['sort'] == 'price_asc' ? 'selected' : '' ?>>
                            Prix croissant
                        </option>
                        <option value="price_desc" <?= $active_filters['sort'] == 'price_desc' ? 'selected' : '' ?>>
                            Prix décroissant
                        </option>
                        <option value="rating" <?= $active_filters['sort'] == 'rating' ? 'selected' : '' ?>>
                            Meilleures notes
                        </option>
                    </select>
                </div>

            </div>

            <!-- Grille de produits -->
            <?php if (empty($products)): ?>
                
                <div class="card text-center" style="padding: var(--space-16);">
                    <div style="font-size: 4rem; margin-bottom: var(--space-4);">🔍</div>
                    <h3 style="margin-bottom: var(--space-3);">Aucun produit trouvé</h3>
                    <p style="color: var(--text-secondary); margin-bottom: var(--space-6);">
                        Essayez de modifier vos critères de recherche
                    </p>
                    <a href="/products" class="btn btn-primary">Voir tous les produits</a>
                </div>

            <?php else: ?>

                <div class="grid grid-4" style="gap: var(--space-6);">
                    <?php foreach ($products as $product): ?>
                    
                    <div class="product-card">
                        
                        <!-- Image -->
                        <a href="/products/<?= e($product['slug']) ?>">
                            <img 
                                src="<?= e($product['thumbnail_url'] ?? '/public/img/placeholder.png') ?>" 
                                alt="<?= e($product['title']) ?>"
                                class="product-image"
                                loading="lazy"
                            >
                        </a>

                        <!-- Contenu -->
                        <div class="product-content">
                            
                            <!-- Header -->
                            <div class="flex-between mb-3">
                                <span class="badge badge-primary">
                                    <?= e($product['category_name']) ?>
                                </span>
                                <?php if ($product['rating_count'] > 0): ?>
                                <div class="flex gap-2" style="align-items: center;">
                                    <span style="color: var(--warning); font-size: 0.875rem;">★</span>
                                    <span style="font-size: 0.875rem; font-weight: 600;">
                                        <?= number_format($product['rating_average'], 1) ?>
                                    </span>
                                    <span style="font-size: 0.75rem; color: var(--text-tertiary);">
                                        (<?= $product['rating_count'] ?>)
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Titre -->
                            <h3 class="product-title">
                                <a href="/products/<?= e($product['slug']) ?>" style="color: inherit;">
                                    <?= e($product['title']) ?>
                                </a>
                            </h3>

                            <!-- Vendeur -->
                            <p style="font-size: 0.875rem; color: var(--text-tertiary); margin-bottom: var(--space-4);">
                                Par 
                                <a href="/seller/<?= e($product['seller_name']) ?>" style="color: var(--primary-600);">
                                    <?= e($product['shop_name'] ?? $product['seller_name']) ?>
                                </a>
                            </p>

                            <!-- Prix et actions -->
                            <div class="flex-between" style="align-items: center;">
                                <div>
                                    <span class="product-price">
                                        <?= formatPrice($product['price']) ?>
                                    </span>
                                    <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                    <span style="
                                        font-size: 0.875rem; 
                                        color: var(--text-tertiary); 
                                        text-decoration: line-through;
                                        margin-left: var(--space-2);
                                    ">
                                        <?= formatPrice($product['original_price']) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                
                                <button 
                                    class="btn btn-primary btn-sm"
                                    onclick="window.location.href='/products/<?= e($product['slug']) ?>'"
                                >
                                    Voir
                                </button>
                            </div>

                            <!-- Stats -->
                            <div class="flex gap-4 mt-3" style="font-size: 0.75rem; color: var(--text-tertiary);">
                                <span>👁️ <?= number_format($product['views_count'] ?? 0) ?></span>
                                <span>💰 <?= number_format($product['sales_count'] ?? 0) ?> ventes</span>
                            </div>

                        </div>
                    </div>

                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                <div class="flex-center mt-12" style="gap: var(--space-2);">
                    
                    <?php if ($pagination['current'] > 1): ?>
                    <a href="<?= updateQueryParam('page', $pagination['current'] - 1) ?>" 
                       class="btn btn-secondary btn-sm">
                        ← Précédent
                    </a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $pagination['current'] - 2); $i <= min($pagination['total_pages'], $pagination['current'] + 2); $i++): ?>
                    <a href="<?= updateQueryParam('page', $i) ?>" 
                       class="btn btn-sm <?= $i == $pagination['current'] ? 'btn-primary' : 'btn-secondary' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>

                    <?php if ($pagination['current'] < $pagination['total_pages']): ?>
                    <a href="<?= updateQueryParam('page', $pagination['current'] + 1) ?>" 
                       class="btn btn-secondary btn-sm">
                        Suivant →
                    </a>
                    <?php endif; ?>

                </div>
                <?php endif; ?>

            <?php endif; ?>

        </main>

    </div>

</div>

<!-- JavaScript -->
<script>
// Fonction helper pour mettre à jour les query params
function updateQueryParam(key, value) {
    const url = new URL(window.location.href);
    url.searchParams.set(key, value);
    return url.toString();
}

// Filtres de prix avec mise à jour en temps réel
const priceMin = document.getElementById('priceMin');
const priceMax = document.getElementById('priceMax');
const priceMinLabel = document.getElementById('priceMinLabel');
const priceMaxLabel = document.getElementById('priceMaxLabel');

if (priceMin && priceMax) {
    let priceTimeout;
    
    priceMin.addEventListener('input', function() {
        priceMinLabel.textContent = formatPrice(this.value);
        
        clearTimeout(priceTimeout);
        priceTimeout = setTimeout(() => {
            applyPriceFilter();
        }, 500);
    });
    
    priceMax.addEventListener('input', function() {
        priceMaxLabel.textContent = formatPrice(this.value);
        
        clearTimeout(priceTimeout);
        priceTimeout = setTimeout(() => {
            applyPriceFilter();
        }, 500);
    });
}

function applyPriceFilter() {
    const url = new URL(window.location.href);
    url.searchParams.set('min_price', priceMin.value);
    url.searchParams.set('max_price', priceMax.value);
    window.location.href = url.toString();
}

function formatPrice(price) {
    return parseFloat(price).toFixed(2).replace('.', ',') + ' €';
}

// Animation des cards au scroll
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animation = 'fadeIn 0.5s ease-out';
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.product-card').forEach(card => {
    observer.observe(card);
});
</script>

<style>
/* Responsive */
@media (max-width: 1024px) {
    [style*="grid-template-columns: 280px 1fr"] {
        grid-template-columns: 1fr !important;
    }
    
    aside {
        position: relative !important;
        top: 0 !important;
    }
}

@media (max-width: 768px) {
    .grid-4 {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (max-width: 480px) {
    .grid-4 {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php
// Helper function pour les query params
function updateQueryParam($key, $value) {
    $params = $_GET;
    $params[$key] = $value;
    return '/products?' . http_build_query($params);
}
?>