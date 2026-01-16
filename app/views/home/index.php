<?php
/**
 * MARKETFLOW PRO - PAGE D'ACCUEIL
 * Fichier : app/views/home/index.php
 */
?>

<section class="hero">
    <div class="container">
        <h1 class="hero-title animate-fade-in">
            La marketplace pour les 
            <span class="text-gradient">créateurs digitaux</span>
        </h1>
        <p class="hero-subtitle animate-fade-in">
            Vendez et achetez des templates, designs, codes et ressources premium. 
            Rejoignez des milliers de créateurs qui génèrent des revenus passifs.
        </p>
        <div class="flex-center gap-4 animate-fade-in">
            <a href="/products" class="btn btn-primary btn-lg">Explorer les produits</a>
            <a href="/register" class="btn btn-outline btn-lg">Devenir vendeur</a>
        </div>
    </div>
</section>

<section class="container mt-16">
    <h2 class="text-center mb-8">Catégories populaires</h2>
    <div class="grid grid-4">
        
        <div class="card text-center">
            <div style="font-size: 3rem; margin-bottom: var(--space-4);">🎨</div>
            <h3 class="card-title">UI Kits</h3>
            <p style="color: var(--text-secondary);">Kits d'interface utilisateur prêts à l'emploi</p>
            <a href="/category/ui-kits" class="btn btn-ghost" style="margin-top: var(--space-4);">Découvrir →</a>
        </div>

        <div class="card text-center">
            <div style="font-size: 3rem; margin-bottom: var(--space-4);">💻</div>
            <h3 class="card-title">Templates Web</h3>
            <p style="color: var(--text-secondary);">Templates de sites web et landing pages</p>
            <a href="/category/templates" class="btn btn-ghost" style="margin-top: var(--space-4);">Découvrir →</a>
        </div>

        <div class="card text-center">
            <div style="font-size: 3rem; margin-bottom: var(--space-4);">✨</div>
            <h3 class="card-title">Icônes</h3>
            <p style="color: var(--text-secondary);">Packs d'icônes et pictogrammes vectoriels</p>
            <a href="/category/icones" class="btn btn-ghost" style="margin-top: var(--space-4);">Découvrir →</a>
        </div>

        <div class="card text-center">
            <div style="font-size: 3rem; margin-bottom: var(--space-4);">🎭</div>
            <h3 class="card-title">Illustrations</h3>
            <p style="color: var(--text-secondary);">Illustrations vectorielles et images premium</p>
            <a href="/category/illustrations" class="btn btn-ghost" style="margin-top: var(--space-4);">Découvrir →</a>
        </div>

    </div>
</section>

<section class="container mt-16">
    <div class="flex-between mb-8">
        <h2>Produits populaires</h2>
        <a href="/products" class="btn btn-ghost">Voir tout →</a>
    </div>
    
    <div class="grid grid-4">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <a href="/products/<?= e($product['slug']) ?>">
                        <div class="product-image-container" style="aspect-ratio: 16/10; overflow: hidden; background: var(--bg-secondary);">
                            <img src="<?= e($product['thumbnail_url'] ?? $product['thumbnail'] ?? '/public/img/placeholder.png') ?>" 
                                 alt="<?= e($product['title']) ?>"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </a>
                    
                    <div class="product-content">
                        <div class="flex-between mb-2">
                            <span class="badge badge-primary" style="font-size: 0.7rem;">
                                <?= e($product['category_name'] ?? 'Digital') ?>
                            </span>
                            
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <div style="color: #fbbf24; font-size: 0.875rem;">
                                    <?php 
                                    $rating = round($product['rating_average'] ?? 0);
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rating ? '★' : '☆';
                                    }
                                    ?>
                                </div>
                                <span style="font-size: 0.75rem; color: var(--text-tertiary);">
                                    (<?= $product['rating_count'] ?? 0 ?>)
                                </span>
                            </div>
                        </div>

                        <h3 class="product-title">
                            <a href="/products/<?= e($product['slug']) ?>" style="color: inherit; text-decoration: none;">
                                <?= e(truncate($product['title'], 45)) ?>
                            </a>
                        </h3>
                        
                        <p style="font-size: 0.875rem; color: var(--text-tertiary); margin-bottom: var(--space-4);">
                            Par <?= e($product['shop_name'] ?? $product['seller_name'] ?? 'Créateur') ?>
                        </p>

                        <div class="flex-between">
                            <span class="product-price" style="font-weight: 700; color: var(--primary-600);">
                                <?= formatPrice($product['price']) ?>
                            </span>
                            <a href="/products/<?= e($product['slug']) ?>" class="btn btn-primary btn-sm">Voir</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="grid-column: span 4; text-align: center; padding: var(--space-12); color: var(--text-tertiary);">
                Aucun produit disponible pour le moment.
            </p>
        <?php endif; ?>
    </div>
</section>

<section class="container mt-16 mb-16">
    <h2 class="text-center mb-8">Pourquoi MarketFlow Pro ?</h2>
    <div class="grid grid-3">
        <div class="card">
            <div style="font-size: 2.5rem; margin-bottom: var(--space-4);">🚀</div>
            <h3 class="card-title">Vente instantanée</h3>
            <p>Téléchargement automatique après paiement. Vos clients reçoivent leurs fichiers en quelques secondes.</p>
        </div>
        <div class="card">
            <div style="font-size: 2.5rem; margin-bottom: var(--space-4);">💰</div>
            <h3 class="card-title">Commission réduite</h3>
            <p>Gardez la majorité de vos revenus. Les paiements sont automatiques et sécurisés via Stripe.</p>
        </div>
        <div class="card">
            <div style="font-size: 2.5rem; margin-bottom: var(--space-4);">📊</div>
            <h3 class="card-title">Dashboard complet</h3>
            <p>Suivez vos ventes, analysez vos performances et gérez vos produits facilement.</p>
        </div>
    </div>
</section>

<section class="container mb-16">
    <div class="card" style="background: var(--gradient-primary); color: white; text-align: center; padding: var(--space-12); border-radius: var(--radius-lg);">
        <h2 style="color: white; margin-bottom: var(--space-4);">Prêt à vendre vos créations ?</h2>
        <p style="color: rgba(255,255,255,0.9); font-size: 1.25rem; margin-bottom: var(--space-8);">
            Rejoignez MarketFlow Pro et commencez à générer des revenus dès aujourd'hui
        </p>
        <a href="/register" class="btn btn-lg" style="background: white; color: var(--primary-600); font-weight: 600;">
            Créer mon compte vendeur
        </a>
    </div>
</section>