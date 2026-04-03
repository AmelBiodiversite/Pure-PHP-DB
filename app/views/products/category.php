<?php
/**
 * VUE CATÉGORIE - UX OPTIMISÉ
 * Affiche les produits d'une catégorie avec filtres et tri
 */
?>

<!-- Fil d'Ariane pour navigation contextuelle -->
<nav class="container" style="margin-top: 2rem; position: relative; z-index: 1;" aria-label="Breadcrumb">
    <ol style="
        display: flex; 
        align-items: center;
        gap: 0.5rem; 
        list-style: none; 
        padding: 0; 
        margin: 0; 
        font-size: 0.875rem; 
        color: var(--text-secondary);
        flex-wrap: wrap;
    ">
        <!-- Lien vers accueil -->
        <li style="display: flex; align-items: center;">
            <a href="/" style="color: var(--text-secondary); text-decoration: none; transition: color var(--transition-fast);" 
               onmouseover="this.style.color='var(--primary-600)'" 
               onmouseout="this.style.color='var(--text-secondary)'">
                Accueil
            </a>
        </li>
        
        <!-- Séparateur -->
        <li aria-hidden="true" style="color: var(--text-tertiary); display: flex; align-items: center; user-select: none;">
            /
        </li>
        
        <!-- Lien vers toutes les catégories -->
        <li style="display: flex; align-items: center;">
            <a href="/category" style="color: var(--text-secondary); text-decoration: none; transition: color var(--transition-fast);"
               onmouseover="this.style.color='var(--primary-600)'" 
               onmouseout="this.style.color='var(--text-secondary)'">
                Catégories
            </a>
        </li>
        
        <!-- Séparateur -->
        <li aria-hidden="true" style="color: var(--text-tertiary); display: flex; align-items: center; user-select: none;">
            /
        </li>
        
        <!-- Catégorie actuelle -->
        <li style="font-weight: 600; color: var(--primary-600); display: flex; align-items: center;" aria-current="page">
            <?= e($category['name']) ?>
        </li>
    </ol>
</nav>

<!-- En-tête de catégorie avec description -->
<section class="container mt-8">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Titre et description -->
        <div style="flex: 1; min-width: 300px;">
            <h1 style="margin-bottom: 0.75rem;"><?= e($category['name']) ?></h1>
            
            <?php if (!empty($category['description'])): ?>
                <!-- Description de la catégorie -->
                <p style="font-size: 1.125rem; color: var(--text-secondary); max-width: 600px; line-height: 1.7; margin-bottom: 0;">
                    <?= e($category['description']) ?>
                </p>
            <?php endif; ?>
            
            <!-- Compteur de produits -->
            <p style="margin-top: 1rem; font-size: 0.875rem; color: var(--text-tertiary); margin-bottom: 0;">
                <strong><?= count($products) ?></strong> produit<?= count($products) > 1 ? 's' : '' ?> disponible<?= count($products) > 1 ? 's' : '' ?>
            </p>
        </div>

        <!-- Filtres et tri (zone droite) -->
        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
            <!-- Tri par prix -->
            <select 
                id="sortFilter" 
                style="
                    min-width: 180px;
                    padding: 0.75rem 1rem;
                    border: 1px solid var(--gray-300);
                    border-radius: var(--radius-lg);
                    font-size: 0.9375rem;
                    cursor: pointer;
                    transition: all var(--transition-base);
                    background: white;
                "
                onchange="applySorting(this.value)"
                onfocus="this.style.borderColor='var(--primary-500)'; this.style.outline='2px solid var(--primary-200)'; this.style.outlineOffset='0'"
                onblur="this.style.borderColor='var(--gray-300)'; this.style.outline='none'"
            >
                <option value="newest">Plus récents</option>
                <option value="price_asc">Prix croissant</option>
                <option value="price_desc">Prix décroissant</option>
                <option value="popular">Populaires</option>
            </select>

            <!-- Bouton vue grille (actif par défaut) -->
            <button 
                id="gridView" 
                class="btn-icon btn-primary"
                onclick="switchView('grid')"
                aria-label="Vue grille"
                title="Vue grille"
                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"
            >
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                    <!-- Icône grille (4 carrés) -->
                    <rect x="3" y="3" width="8" height="8" rx="1"/>
                    <rect x="13" y="3" width="8" height="8" rx="1"/>
                    <rect x="3" y="13" width="8" height="8" rx="1"/>
                    <rect x="13" y="13" width="8" height="8" rx="1"/>
                </svg>
            </button>
        </div>
    </div>
</section>

<!-- Liste des produits -->
<section class="container mb-16">
    <?php if (empty($products)): ?>
        <!-- État vide amélioré avec CTA -->
        <div class="card text-center p-12" style="max-width: 600px; margin: 4rem auto;">
            <!-- Icône illustrative -->
            <div style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.3;">📦</div>
            
            <h3 style="margin-bottom: 1rem; color: var(--text-secondary);">
                Aucun produit dans cette catégorie
            </h3>
            
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                Soyez le premier à ajouter un produit dans <strong><?= e($category['name']) ?></strong> !
            </p>
            
            <!-- Boutons d'action -->
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="/category" class="btn btn-outline">
                    Voir toutes les catégories
                </a>
                
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'seller'): ?>
                    <a href="/seller/products/create" class="btn btn-primary">
                        Ajouter un produit
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
    <?php else: ?>
        <!-- Grille de produits avec animations -->
        <div class="grid grid-4" id="productsGrid">
            <?php foreach ($products as $product): ?>
                <!-- Card produit premium avec hover effects -->
                <article class="product-card">
                    <!-- Container image avec placeholder si pas d'image -->
                    <a href="/products/<?= e($product['slug']) ?>" class="product-image-container">
                        <?php if (!empty($product['thumbnail_url'])): ?>
                            <!-- Image du produit avec lazy loading -->
                            <img 
                                src="<?= e($product['thumbnail_url']) ?>" 
                                alt="<?= e($product['title']) ?>"
                                loading="lazy"
                                onerror="this.src='/public/images/placeholder-product.jpg'"
                            />
                        <?php else: ?>
                            <!-- Placeholder si pas d'image -->
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);">
                                <svg width="64" height="64" fill="currentColor" style="opacity: 0.3;">
                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Badge "Nouveau" si produit récent (moins de 7 jours) -->
                        <?php 
                        $isNew = (time() - strtotime($product['created_at'])) < (7 * 24 * 3600);
                        if ($isNew): 
                        ?>
                            <span class="product-badge">Nouveau</span>
                        <?php endif; ?>
                    </a>

                    <!-- Contenu de la card -->
                    <div class="product-content">
                        <!-- Catégorie (tag supérieur) -->
                        <span class="product-category">
                            <?= e($category['name']) ?>
                        </span>

                        <!-- Titre du produit avec lien -->
                        <h3 class="product-title">
                            <a href="/products/<?= e($product['slug']) ?>">
                                <?= e($product['title']) ?>
                            </a>
                        </h3>

                        <!-- Description courte (2 lignes max) -->
                        <?php if (!empty($product['description'])): ?>
                            <p class="product-description">
                                <?= e(substr($product['description'], 0, 120)) ?>...
                            </p>
                        <?php endif; ?>

                        <!-- Footer avec prix et rating -->
                        <div class="product-footer">
                            <!-- Prix -->
                            <div class="product-price">
                                <span class="price-current"><?= number_format($product['price'], 2, ',', ' ') ?> €</span>
                                
                                <!-- Ancien prix si promo -->
                                <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                    <span class="price-old"><?= number_format($product['original_price'], 2, ',', ' ') ?> €</span>
                                <?php endif; ?>
                            </div>

                            <!-- Note et vendeur -->
                            <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-end;">
                                <!-- Rating étoiles -->
                                <?php if (!empty($product['rating_average'])): ?>
                                    <div class="product-rating">
                                        <?php 
                                        // Affichage des étoiles (5 max)
                                        $rating = round($product['rating_average'] * 2) / 2; // Arrondi à 0.5
                                        for ($i = 1; $i <= 5; $i++): 
                                        ?>
                                            <span class="star">
                                                <?php if ($i <= $rating): ?>
                                                    ★
                                                <?php elseif ($i - 0.5 <= $rating): ?>
                                                    ⯨
                                                <?php else: ?>
                                                    ☆
                                                <?php endif; ?>
                                            </span>
                                        <?php endfor; ?>
                                        <span style="font-size: 0.875rem; color: var(--text-tertiary);">
                                            (<?= $product['rating_count'] ?? 0 ?>)
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <!-- Nom du vendeur -->
                                <p style="font-size: 0.75rem; color: var(--text-tertiary); margin: 0;">
                                    par <strong><?= e($product['seller_name']) ?></strong>
                                </p>
                            </div>
                        </div>

                        <!-- Bouton d'action principal -->
                        <a href="/products/<?= e($product['slug']) ?>" class="btn btn-primary w-full mt-4">
                            Voir les détails
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Pagination si nécessaire -->
<?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
    <section class="container mb-16">
        <nav aria-label="Pagination" style="display: flex; justify-content: center; gap: 0.5rem; flex-wrap: wrap;">
            <!-- Bouton précédent -->
            <?php if ($pagination['current'] > 1): ?>
                <a 
                    href="?page=<?= $pagination['current'] - 1 ?>" 
                    class="btn btn-outline btn-sm"
                    aria-label="Page précédente"
                >
                    ← Précédent
                </a>
            <?php endif; ?>

            <!-- Numéros de pages -->
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <?php if ($i === $pagination['current']): ?>
                    <!-- Page actuelle -->
                    <span class="btn btn-primary btn-sm" aria-current="page">
                        <?= $i ?>
                    </span>
                <?php else: ?>
                    <!-- Autres pages -->
                    <a href="?page=<?= $i ?>" class="btn btn-outline btn-sm">
                        <?= $i ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            <!-- Bouton suivant -->
            <?php if ($pagination['current'] < $pagination['total_pages']): ?>
                <a 
                    href="?page=<?= $pagination['current'] + 1 ?>" 
                    class="btn btn-outline btn-sm"
                    aria-label="Page suivante"
                >
                    Suivant →
                </a>
            <?php endif; ?>
        </nav>
    </section>
<?php endif; ?>

<!-- Script pour le tri dynamique -->
<script>
/**
 * Applique le tri sélectionné en rechargeant la page avec le paramètre
 */
function applySorting(sortValue) {
    // Récupère l'URL actuelle
    const url = new URL(window.location.href);
    
    // Ajoute ou modifie le paramètre 'sort'
    url.searchParams.set('sort', sortValue);
    
    // Recharge la page avec le nouveau paramètre
    window.location.href = url.toString();
}

/**
 * Change le mode d'affichage (grille/liste)
 * Note: actuellement seule la grille est implémentée
 */
function switchView(viewType) {
    const grid = document.getElementById('productsGrid');
    
    if (viewType === 'grid') {
        // Active la vue grille (par défaut)
        grid.className = 'grid grid-4';
    } else if (viewType === 'list') {
        // Vue liste (à implémenter si besoin)
        grid.className = 'grid grid-1';
    }
}

/**
 * Préserve le tri sélectionné au chargement de la page
 */
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const sortValue = urlParams.get('sort');
    
    if (sortValue) {
        // Restaure la sélection du tri
        document.getElementById('sortFilter').value = sortValue;
    }
});
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
