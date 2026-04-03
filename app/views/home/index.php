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

<!-- HERO -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Produits digitaux pour votre <em class="hero-accent">autonomie</em></h1>
            <p class="hero-subtitle">Découvrez des ressources premium pour le développement personnel,<br>l'autonomie et un mode de vie plus conscient.</p>
        </div>
    </div>
</section>

<!-- CATÉGORIES -->
<section class="categories-section">
    <div class="cat-grid">

        <div class="cat-row cat-row--1-2">
            <a href="/category/developpement-personnel" class="cat-card cat-card--1">
                <span class="cat-num">01</span>
                <div class="cat-icon"><svg viewBox="0 0 32 32" fill="none"><path d="M16 26V10" stroke="#0f6e56" stroke-width="1.6" stroke-linecap="round"/><path d="M10 16L16 10L22 16" stroke="#0f6e56" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 22h12" stroke="#0f6e56" stroke-width="1.6" stroke-linecap="round"/></svg></div>
                <p class="cat-label">01 · Croissance</p>
                <h3 class="cat-title">Développement personnel</h3>
                <span class="cat-btn">— ressources</span>
            </a>
            <a href="/category/sante-alimentation" class="cat-card cat-card--2">
                <span class="cat-num">02</span>
                <div class="cat-icon"><svg viewBox="0 0 32 32" fill="none"><circle cx="16" cy="14" r="6" stroke="#3b6d11" stroke-width="1.6"/><path d="M13 14h6M16 11v6" stroke="#3b6d11" stroke-width="1.6" stroke-linecap="round"/><path d="M16 20v5M12 25h8" stroke="#3b6d11" stroke-width="1.6" stroke-linecap="round"/></svg></div>
                <p class="cat-label">02 · Nutrition</p>
                <h3 class="cat-title">Santé &amp; alimentation</h3>
                <span class="cat-btn">— ressources</span>
            </a>
        </div>

        <div class="cat-row cat-row--2-1">
            <a href="/category/jardin-autonomie" class="cat-card cat-card--3">
                <span class="cat-num">03</span>
                <div class="cat-icon"><svg viewBox="0 0 32 32" fill="none"><path d="M16 6C14 9 13 12 13 15a3 3 0 006 0C19 12 18 9 16 6z" stroke="#854f0b" stroke-width="1.6" stroke-linejoin="round"/><path d="M10 12C8 14 7 17 7 20a3 3 0 006 0C13 17 12 14 10 12z" stroke="#854f0b" stroke-width="1.6" stroke-linejoin="round"/><path d="M22 12C20 14 19 17 19 20a3 3 0 006 0C25 17 24 14 22 12z" stroke="#854f0b" stroke-width="1.6" stroke-linejoin="round"/></svg></div>
                <p class="cat-label">03 · Jardinage</p>
                <h3 class="cat-title">Jardin &amp; autonomie</h3>
                <span class="cat-btn">— ressources</span>
            </a>
            <a href="/category/maison-energie" class="cat-card cat-card--4">
                <span class="cat-num">04</span>
                <div class="cat-icon"><svg viewBox="0 0 32 32" fill="none"><path d="M4 28h24M8 28V18L16 10L24 18V28" stroke="#185fa5" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><rect x="13" y="20" width="6" height="8" rx="1" stroke="#185fa5" stroke-width="1.6"/></svg></div>
                <p class="cat-label">04 · Habitat</p>
                <h3 class="cat-title">Maison &amp; énergie</h3>
                <span class="cat-btn">— ressources</span>
            </a>
        </div>
        </div>

    </div>
</section>

<!-- PRODUITS POPULAIRES -->
<section class="products-section">
    <div class="container">
        <div class="section-header">
            <h2 class="sec-title-new">Produits populaires</h2>
            <a href="/products" class="sec-link-new">Voir tout →</a>
        </div>
        <div class="products-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="prod-card-new" data-product-id="<?= e($product['id']) ?>">
                        <div class="prod-img-new">
                            <?php if (isLoggedIn()): ?>
                                <button class="wishlist-btn <?= isInWishlist($product['id']) ? 'active' : '' ?>"
                                        data-product-id="<?= e($product['id']) ?>"
                                        title="<?= isInWishlist($product['id']) ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>"
                                        aria-label="Ajouter aux favoris">
                                    <span class="heart-icon">❤️</span>
                                </button>
                            <?php endif; ?>
                            <img src="<?= e($product['thumbnail_url'] ?? $product['thumbnail'] ?? '/public/img/placeholder.png') ?>"
                                 alt="<?= e($product['title']) ?>"
                                 class="product-image">
                            <span class="prod-badge-new"><?= e($product['category_name'] ?? 'Digital') ?></span>
                        </div>
                        <div class="prod-body-new">
                            <?php if (isset($product['rating_average']) && $product['rating_average'] > 0): ?>
                            <div class="prod-rating">
                                <span class="prod-stars"><?php $rating = round($product['rating_average'] ?? 0); for ($i = 1; $i <= 5; $i++) { echo $i <= $rating ? '★' : '☆'; } ?></span>
                                <span class="prod-rating-count">(<?= $product['rating_count'] ?? 0 ?>)</span>
                            </div>
                            <?php endif; ?>
                            <h3 class="prod-title-new">
                                <a href="/products/<?= e($product['slug']) ?>"><?= e(truncate($product['title'], 60)) ?></a>
                            </h3>
                            <?php if (isset($product['shop_name']) || isset($product['seller_name'])): ?>
                            <div class="prod-seller-new"><?= e($product['shop_name'] ?? $product['seller_name'] ?? 'Créateur') ?></div>
                            <?php endif; ?>
                            <div class="prod-foot-new">
                                <span class="prod-price-new"><?= formatPrice($product['price']) ?></span>
                                <a href="/products/<?= e($product['slug']) ?>" class="prod-btn-new">Voir</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products">
                    <div class="no-products-icon">📦</div>
                    Aucun produit disponible pour le moment.
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="features-section">
    <div class="container">
        <div class="section-header">
            <h2 class="sec-title-new">Pourquoi MarketFlow ?</h2>
        </div>
        <div class="feat-grid-new">
            <div class="feat-card-new">
                <div class="feat-ic-new" style="background:#ede9fe;">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke="#534ab7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="feat-title-new">Vente instantanée</h3>
                <p class="feat-desc-new">Téléchargement automatique après paiement. Vos clients reçoivent leurs fichiers en quelques secondes.</p>
            </div>
            <div class="feat-card-new">
                <div class="feat-ic-new" style="background:#d8f0e8;">
                    <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#0f6e56" stroke-width="1.5"/><path d="M8.5 14.5c.5 1.5 2 2.5 3.5 2.5s3-1 3-2.5-1.5-2-3-2.5-3-1-3-2.5S10.5 7 12 7s3 1 3.5 2.5" stroke="#0f6e56" stroke-width="1.5" stroke-linecap="round"/></svg>
                </div>
                <h3 class="feat-title-new">Commission réduite</h3>
                <p class="feat-desc-new">Gardez la majorité de vos revenus. Paiements automatiques et sécurisés via Stripe.</p>
            </div>
            <div class="feat-card-new">
                <div class="feat-ic-new" style="background:#fde8ee;">
                    <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7" rx="1" stroke="#993556" stroke-width="1.5"/><rect x="14" y="3" width="7" height="7" rx="1" stroke="#993556" stroke-width="1.5"/><rect x="3" y="14" width="7" height="7" rx="1" stroke="#993556" stroke-width="1.5"/><path d="M14 17.5h7M17.5 14v7" stroke="#993556" stroke-width="1.5" stroke-linecap="round"/></svg>
                </div>
                <h3 class="feat-title-new">Dashboard complet</h3>
                <p class="feat-desc-new">Suivez vos ventes, analysez vos performances et gérez vos produits facilement.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <div class="cta-inner-new">
            <h2 class="cta-title-new">Prêt à vendre vos créations ?</h2>
            <p class="cta-desc-new">Rejoignez MarketFlow et commencez à générer des revenus dès aujourd'hui.</p>
            <a href="/register" class="cta-btn-new">Créer mon compte vendeur →</a>
        </div>
    </div>
</section>
