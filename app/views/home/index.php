<?php
/**
 * PAGE D'ACCUEIL TEMPORAIRE - PREVIEW
 * Fichier : app/views/home/index.php
 */
?>

<!-- Hero Section -->
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

<!-- Catégories Featured -->
<section class="container mt-16">
    <h2 class="text-center mb-8">Catégories populaires</h2>
    <div class="grid grid-4">
        
        <div class="card text-center">
            <div style="font-size: 3rem; margin-bottom: var(--space-4);">🎨</div>
            <h3 class="card-title">UI Kits</h3>
            <p style="color: var(--text-secondary);">
                Kits d'interface utilisateur prêts à l'emploi
            </p>
            <a href="/category/ui-kits" class="btn btn-ghost" style="margin-top: var(--space-4);">
                Découvrir →
            </a>
        </div>

        <div class="card text-center">
            <div style="font-size: 3rem; margin-bottom: var(--space-4);">💻</div>
            <h3 class="card-title">Templates Web</h3>
            <p style="color: var(--text-secondary);">
                Templates de sites web et landing pages
            </p>
            <a href="/category/templates" class="btn btn-ghost" style="margin-top: var(--space-4);">
                Découvrir →
            </a>
        </div>

        <div class="card text-center">
            <div style="font-size: 3rem; margin-bottom: var(--space-4);">✨</div>
            <h3 class="card-title">Icônes</h3>
            <p style="color: var(--text-secondary);">
                Packs d'icônes et pictogrammes vectoriels
            </p>
            <a href="/category/icones" class="btn btn-ghost" style="margin-top: var(--space-4);">
                Découvrir →
            </a>
        </div>

        <div class="card text-center">
            <div style="font-size: 3rem; margin-bottom: var(--space-4);">🎭</div>
            <h3 class="card-title">Illustrations</h3>
            <p style="color: var(--text-secondary);">
                Illustrations vectorielles et images premium
            </p>
            <a href="/category/illustrations" class="btn btn-ghost" style="margin-top: var(--space-4);">
                Découvrir →
            </a>
        </div>

    </div>
</section>

<!-- Produits Exemple (statiques pour la démo) -->
<section class="container mt-16">
    <div class="flex-between mb-8">
        <h2>Produits populaires</h2>
        <a href="/products" class="btn btn-ghost">Voir tout →</a>
    </div>
    
    <div class="grid grid-4">
        
        <!-- Produit exemple 1 -->
        <div class="product-card">
            <div class="product-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                🎨
            </div>
            <div class="product-content">
                <div class="flex-between mb-3">
                    <span class="badge badge-primary">UI Kit</span>
                    <div class="flex gap-2" style="align-items: center;">
                        <span style="color: var(--warning); font-size: 0.875rem;">★</span>
                        <span style="font-size: 0.875rem; font-weight: 600;">4.9</span>
                    </div>
                </div>
                <h3 class="product-title">Dashboard UI Kit Pro</h3>
                <p style="font-size: 0.875rem; color: var(--text-tertiary); margin-bottom: var(--space-4);">
                    Par CreativeStudio
                </p>
                <div class="flex-between">
                    <span class="product-price">49€</span>
                    <button class="btn btn-primary btn-sm">Voir</button>
                </div>
            </div>
        </div>

        <!-- Produit exemple 2 -->
        <div class="product-card">
            <div class="product-image" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                💻
            </div>
            <div class="product-content">
                <div class="flex-between mb-3">
                    <span class="badge badge-success">Template</span>
                    <div class="flex gap-2" style="align-items: center;">
                        <span style="color: var(--warning); font-size: 0.875rem;">★</span>
                        <span style="font-size: 0.875rem; font-weight: 600;">5.0</span>
                    </div>
                </div>
                <h3 class="product-title">Landing Page Pack</h3>
                <p style="font-size: 0.875rem; color: var(--text-tertiary); margin-bottom: var(--space-4);">
                    Par DesignMasters
                </p>
                <div class="flex-between">
                    <span class="product-price">79€</span>
                    <button class="btn btn-primary btn-sm">Voir</button>
                </div>
            </div>
        </div>

        <!-- Produit exemple 3 -->
        <div class="product-card">
            <div class="product-image" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                ✨
            </div>
            <div class="product-content">
                <div class="flex-between mb-3">
                    <span class="badge badge-warning">Icônes</span>
                    <div class="flex gap-2" style="align-items: center;">
                        <span style="color: var(--warning); font-size: 0.875rem;">★</span>
                        <span style="font-size: 0.875rem; font-weight: 600;">4.8</span>
                    </div>
                </div>
                <h3 class="product-title">Icon Set Premium</h3>
                <p style="font-size: 0.875rem; color: var(--text-tertiary); margin-bottom: var(--space-4);">
                    Par IconLab
                </p>
                <div class="flex-between">
                    <span class="product-price">29€</span>
                    <button class="btn btn-primary btn-sm">Voir</button>
                </div>
            </div>
        </div>

        <!-- Produit exemple 4 -->
        <div class="product-card">
            <div class="product-image" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                🎭
            </div>
            <div class="product-content">
                <div class="flex-between mb-3">
                    <span class="badge badge-error">Code</span>
                    <div class="flex gap-2" style="align-items: center;">
                        <span style="color: var(--warning); font-size: 0.875rem;">★</span>
                        <span style="font-size: 0.875rem; font-weight: 600;">4.7</span>
                    </div>
                </div>
                <h3 class="product-title">React Components</h3>
                <p style="font-size: 0.875rem; color: var(--text-tertiary); margin-bottom: var(--space-4);">
                    Par CodeWizards
                </p>
                <div class="flex-between">
                    <span class="product-price">99€</span>
                    <button class="btn btn-primary btn-sm">Voir</button>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Features -->
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
            <h3 class="card-title">Commission 15% seulement</h3>
            <p>Gardez 85% de vos ventes. Les paiements sont automatiques et sécurisés via Stripe.</p>
        </div>
        <div class="card">
            <div style="font-size: 2.5rem; margin-bottom: var(--space-4);">📊</div>
            <h3 class="card-title">Dashboard complet</h3>
            <p>Suivez vos ventes, analysez vos performances et gérez vos produits facilement.</p>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="container mb-16">
    <div class="card" style="background: var(--gradient-primary); color: white; text-align: center; padding: var(--space-12);">
        <h2 style="color: white; margin-bottom: var(--space-4);">Prêt à vendre vos créations ?</h2>
        <p style="color: rgba(255,255,255,0.9); font-size: 1.25rem; margin-bottom: var(--space-8);">
            Rejoignez MarketFlow Pro et commencez à générer des revenus dès aujourd'hui
        </p>
        <a href="/register" class="btn btn-lg" style="background: white; color: var(--primary-600);">
            Créer mon compte vendeur
        </a>
    </div>
</section>