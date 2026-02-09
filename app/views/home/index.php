<?php
/**
 * ================================================================
 * MARKETFLOW PRO - PAGE D'ACCUEIL COMPLÈTE
 * ================================================================
 * 
 * Fichier : app/views/home/index.php
 *
 * 
 * SECTIONS :
 * 1. Hero - Section d'accueil avec titre et CTA
 * 2. Catégories - 6 catégories en Minimal Luxe
 * 3. Produits populaires - Avec boutons wishlist
 * 4. Features - Pourquoi MarketFlow
 * 5. CTA Final - Appel vendeur
 * 
 * DESIGN :
 * - Camaïeu de bleus
 * - Soft Blue background
 * - Glassmorphism cards
 * - Animations fluides
 * 
 * ================================================================
 */
?>

<!-- ============================================================
     HERO SECTION - Section d'accueil
     ============================================================ -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <!-- Titre principal avec gradient -->
            <h1 class="hero-title animate-fade-in">
                La marketplace pour les 
                <span class="gradient-text">créateurs digitaux</span>
            </h1>
            
            <!-- Sous-titre descriptif -->
            <p class="hero-subtitle animate-fade-in">
                Vendez et achetez des templates, designs, codes et ressources premium. 
                Rejoignez des milliers de créateurs qui génèrent des revenus passifs.
            </p>
            
            <!-- Call-to-actions -->
            <div class="hero-cta animate-fade-in">
                <a href="/products" class="btn-hero-primary">
                    Explorer les produits
                    <span>→</span>
                </a>
                <a href="/register" class="btn-hero-secondary">
                    Devenir vendeur
                    <span>🚀</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     SECTION CATÉGORIES - Minimal Luxe
     ============================================================ -->
<section class="categories-section">
    <div class="container">
        
        <!-- En-tête de section -->
        <h2 class="section-title">Explorez nos catégories</h2>
        <p class="section-subtitle">
            Des milliers de produits digitaux premium dans 6 catégories principales
        </p>

        <!-- Grille de catégories -->
        <div class="categories-grid">
            
            <!-- Catégorie 1 : Cours & Formations -->
            <a href="/category/courses" class="category-card cat-1">
                <div class="category-content">
                    <div class="category-number">01</div>
                    <div class="category-icon">🎓</div>
                    <h3 class="category-title">Cours & Formations</h3>
                    <p class="category-description">
                        Masterclasses et programmes complets pour développer votre expertise et accélérer votre carrière professionnelle.
                    </p>
                    <div class="category-meta">
                        <span class="category-stats"><?= number_format($categoryCounts['courses']) ?> produits</span>
                        <div class="category-arrow">→</div>
                    </div>
                </div>
            </a>

            <!-- Catégorie 2 : Design Assets -->
            <a href="/category/design" class="category-card cat-2">
                <div class="category-content">
                    <div class="category-number">02</div>
                    <div class="category-icon">🎨</div>
                    <h3 class="category-title">Design Assets</h3>
                    <p class="category-description">
                        UI Kits professionnels et design systems pour créer des produits digitaux de classe mondiale rapidement.
                    </p>
                    <div class="category-meta">
                        <span class="category-stats">1,923 produits</span>
                        <div class="category-arrow">→</div>
                    </div>
                </div>
            </a>

            <!-- Catégorie 3 : Templates Productivité -->
            <a href="/category/templates" class="category-card cat-3">
                <div class="category-content">
                    <div class="category-number">03</div>
                    <div class="category-icon">📋</div>
                    <h3 class="category-title">Templates Productivité</h3>
                    <p class="category-description">
                        Systèmes d'organisation Notion et templates pour optimiser votre workflow quotidien et gagner du temps.
                    </p>
                    <div class="category-meta">
                        <span class="category-stats">3,156 produits</span>
                        <div class="category-arrow">→</div>
                    </div>
                </div>
            </a>

            <!-- Catégorie 4 : Code & Scripts -->
            <a href="/category/code" class="category-card cat-4">
                <div class="category-content">
                    <div class="category-number">04</div>
                    <div class="category-icon">💻</div>
                    <h3 class="category-title">Code & Scripts</h3>
                    <p class="category-description">
                        Composants React, starters et plugins pour accélérer votre développement web moderne et efficace.
                    </p>
                    <div class="category-meta">
                        <span class="category-stats">1,548 produits</span>
                        <div class="category-arrow">→</div>
                    </div>
                </div>
            </a>

            <!-- Catégorie 5 : Audio & Musique -->
            <a href="/category/audio" class="category-card cat-5">
                <div class="category-content">
                    <div class="category-number">05</div>
                    <div class="category-icon">🎵</div>
                    <h3 class="category-title">Audio & Musique</h3>
                    <p class="category-description">
                        Bibliothèques audio premium et presets sonores pour vos projets multimédias et créations audiovisuelles.
                    </p>
                    <div class="category-meta">
                        <span class="category-stats">892 produits</span>
                        <div class="category-arrow">→</div>
                    </div>
                </div>
            </a>

            <!-- Catégorie 6 : Ressources Visuelles -->
            <a href="/category/visual" class="category-card cat-6">
                <div class="category-content">
                    <div class="category-number">06</div>
                    <div class="category-icon">✨</div>
                    <h3 class="category-title">Ressources Visuelles</h3>
                    <p class="category-description">
                        Collections premium de fonts, icônes et illustrations vectorielles haute qualité pour vos designs.
                    </p>
                    <div class="category-meta">
                        <span class="category-stats">2,441 produits</span>
                        <div class="category-arrow">→</div>
                    </div>
                </div>
            </a>

        </div>
    </div>
</section>

<!-- ============================================================
     SECTION PRODUITS POPULAIRES - Avec wishlist
     ============================================================ -->
<section class="products-section">
    <div class="container">
        
        <!-- En-tête avec lien "Voir tout" -->
        <div class="section-header">
            <h2 class="section-title">Produits populaires</h2>
            <a href="/products" class="view-all-link">
                Voir tout
                <span class="arrow">→</span>
            </a>
        </div>

        <!-- Grille de produits -->
        <div class="products-grid">
            
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    
                    <!-- Carte produit -->
                    <div class="product-card" data-product-id="<?= e($product['id']) ?>">
                        
                        <!-- Container image avec wishlist et badge -->
                        <div class="product-image-container">
                            
                            <!-- Bouton wishlist (cœur) - Uniquement si connecté -->
                            <?php if (isLoggedIn()): ?>
                                <button class="wishlist-btn <?= isInWishlist($product['id']) ? 'active' : '' ?>" 
                                        data-product-id="<?= e($product['id']) ?>" 
                                        title="<?= isInWishlist($product['id']) ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>"
                                        aria-label="Ajouter aux favoris">
                                    <span class="heart-icon">❤️</span>
                                </button>
                            <?php endif; ?>
                            
                            <!-- Image du produit -->
                            <img src="<?= e($product['thumbnail_url'] ?? $product['thumbnail'] ?? '/public/img/placeholder.png') ?>" 
                                 alt="<?= e($product['title']) ?>"
                                 class="product-image">
                            
                            <!-- Badge catégorie -->
                            <span class="product-badge">
                                <?= e($product['category_name'] ?? 'Digital') ?>
                            </span>
                        </div>

                        <!-- Contenu de la carte -->
                        <div class="product-content">
                            
                            <!-- En-tête avec note (si disponible) -->
                            <?php if (isset($product['rating_average']) && $product['rating_average'] > 0): ?>
                            <div class="product-header">
                                <div class="product-rating">
                                    <span class="product-stars">
                                        <?php 
                                        $rating = round($product['rating_average'] ?? 0);
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $rating ? '★' : '☆';
                                        }
                                        ?>
                                    </span>
                                    <span class="product-rating-count">(<?= $product['rating_count'] ?? 0 ?>)</span>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Titre du produit -->
                            <h3 class="product-title">
                                <a href="/products/<?= e($product['slug']) ?>">
                                    <?= e(truncate($product['title'], 60)) ?>
                                </a>
                            </h3>

                            <!-- Nom du vendeur (si disponible) -->
                            <?php if (isset($product['shop_name']) || isset($product['seller_name'])): ?>
                            <div class="product-seller">
                                <?= e($product['shop_name'] ?? $product['seller_name'] ?? 'Créateur') ?>
                            </div>
                            <?php endif; ?>

                            <!-- Footer : prix + bouton -->
                            <div class="product-footer">
                                <span class="product-price">
                                    <?= formatPrice($product['price']) ?>
                                </span>
                                <a href="/products/<?= e($product['slug']) ?>" class="product-btn">
                                    Voir
                                </a>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <!-- Message si aucun produit -->
                <div class="no-products">
                    <div class="no-products-icon">📦</div>
                    Aucun produit disponible pour le moment.
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- ============================================================
     SECTION FEATURES - Pourquoi MarketFlow
     ============================================================ -->
<section class="features-section">
    <div class="container">
        
        <!-- En-tête -->
        <h2 class="section-title">Pourquoi MarketFlow Pro ?</h2>
        <p class="section-subtitle">
            La plateforme qui place les créateurs au centre
        </p>

        <!-- Grille de features -->
        <div class="features-grid">
            
            <!-- Feature 1 : Vente instantanée -->
            <div class="feature-card">
                <div class="feature-icon">🚀</div>
                <h3 class="feature-title">Vente instantanée</h3>
                <p class="feature-description">
                    Téléchargement automatique après paiement. Vos clients reçoivent leurs fichiers en quelques secondes.
                </p>
            </div>

            <!-- Feature 2 : Commission réduite -->
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3 class="feature-title">Commission réduite</h3>
                <p class="feature-description">
                    Gardez la majorité de vos revenus. Les paiements sont automatiques et sécurisés via Stripe.
                </p>
            </div>

            <!-- Feature 3 : Dashboard complet -->
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3 class="feature-title">Dashboard complet</h3>
                <p class="feature-description">
                    Suivez vos ventes, analysez vos performances et gérez vos produits facilement.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     CTA FINAL - Appel à l'action vendeur
     ============================================================ -->
<section class="cta-section">
    <div class="container">
        <div class="cta-card">
            <!-- Titre avec appel -->
            <h2 class="cta-title">Prêt à vendre vos créations ?</h2>
            
            <!-- Description -->
            <p class="cta-description">
                Rejoignez MarketFlow Pro et commencez à générer des revenus dès aujourd'hui
            </p>
            
            <!-- Bouton CTA -->
            <a href="/register" class="cta-button">
                Créer mon compte vendeur →
            </a>
        </div>
    </div>
</section>
