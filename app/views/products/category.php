<?php
/**
 * VUE CATÉGORIE - UX OPTIMISÉ
 * Affiche les produits d'une catégorie avec filtres et tri
 */
?>

<!-- Fil d'Ariane pour navigation contextuelle -->
<nav class="container mt-4" aria-label="Breadcrumb">
    <ol class="flex gap-2 text-sm text-gray-600">
        <!-- Lien vers accueil -->
        <li><a href="/" class="hover:text-primary-600 transition-colors">Accueil</a></li>
        <li aria-hidden="true">/</li>
        
        <!-- Lien vers toutes les catégories -->
        <li><a href="/products/categories" class="hover:text-primary-600 transition-colors">Catégories</a></li>
        <li aria-hidden="true">/</li>
        
        <!-- Catégorie actuelle -->
        <li class="font-semibold text-primary-600" aria-current="page"><?= e($category['name']) ?></li>
    </ol>
</nav>

<!-- En-tête de catégorie avec description -->
<section class="container mt-8">
    <div class="flex flex-between flex-wrap gap-6 mb-8">
        <!-- Titre et description -->
        <div class="flex-1" style="min-width: 300px;">
            <h1 class="mb-3"><?= e($category['name']) ?></h1>
            
            <?php if (!empty($category['description'])): ?>
                <!-- Description de la catégorie -->
                <p class="text-lg text-secondary" style="max-width: 600px; line-height: 1.7;">
                    <?= e($category['description']) ?>
                </p>
            <?php endif; ?>
            
            <!-- Compteur de produits -->
            <p class="mt-4 text-sm text-tertiary">
                <strong><?= count($products) ?></strong> produit<?= count($products) > 1 ? 's' : '' ?> disponible<?= count($products) > 1 ? 's' : '' ?>
            </p>
        </div>

        <!-- Filtres et tri (zone droite) -->
        <div class="flex gap-3 items-center">
            <!-- Tri par prix -->
            <select 
                id="sortFilter" 
                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all cursor-pointer"
                style="min-width: 180px;"
                onchange="applySorting(this.value)"
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
            
            <h3 class="mb-4" style="color: var(--text-secondary);">
                Aucun produit dans cette catégorie
            </h3>
            
            <p class="text-secondary mb-6">
                Soyez le premier à ajouter un produit dans <strong><?= e($category['name']) ?></strong> !
            </p>
            
            <!-- Boutons d'action -->
            <div class="flex gap-4 justify-center flex-wrap">
                <a href="/products/categories" class="btn btn-outline">
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
                                <p style="font-size: 0.75rem; color: var(--text-tertiary);">
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
        <nav aria-label="Pagination" class="flex justify-center gap-2">
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
