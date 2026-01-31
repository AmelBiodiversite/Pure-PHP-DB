<?php
/**
 * MARKETFLOW PRO - LISTE DES CATÉGORIES
 * Page principale affichant toutes les catégories disponibles
 * Version UX optimisée avec design moderne
 */
?>

<!-- Hero Section avec gradient moderne -->
<section class="hero" style="padding: 4rem 0; position: relative; overflow: hidden;">
    <!-- Gradient mesh animé en background -->
    <div style="position: absolute; inset: 0; background: var(--gradient-mesh); opacity: 0.4; z-index: 0;"></div>
    
    <div class="container hero-content" style="position: relative; z-index: 1;">
        <!-- Badge supérieur -->
        <div class="text-center mb-4">
            <span class="badge badge-gradient" style="font-size: 0.875rem; padding: 0.625rem 1.5rem;">
                ✨ Marketplace Premium
            </span>
        </div>

        <!-- Titre principal avec gradient -->
        <h1 class="text-center mb-4 text-gradient" style="font-size: clamp(2.5rem, 5vw, 3.5rem);">
            Explorez nos catégories
        </h1>
        
        <!-- Sous-titre -->
        <p class="text-center text-lg mb-8" style="max-width: 650px; margin-left: auto; margin-right: auto; color: var(--text-secondary);">
            Découvrez des milliers de produits numériques de qualité, créés par des vendeurs talentueux du monde entier
        </p>

        <!-- Barre de recherche rapide (optionnel) -->
        <div style="max-width: 600px; margin: 0 auto;">
            <form action="/products" method="GET" class="flex gap-2">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="🔍 Rechercher un produit, une catégorie..." 
                    style="flex: 1; padding: 1rem 1.5rem; border: 2px solid var(--gray-200); border-radius: var(--radius-xl); font-size: 1rem; transition: all var(--transition-base);"
                    onfocus="this.style.borderColor='var(--primary-500)'; this.style.boxShadow='var(--shadow-primary)'"
                    onblur="this.style.borderColor='var(--gray-200)'; this.style.boxShadow='none'"
                />
                <button type="submit" class="btn btn-primary btn-lg">
                    Rechercher
                </button>
            </form>
        </div>
    </div>

    <!-- Cercles décoratifs flottants -->
    <div style="position: absolute; top: 10%; right: 5%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%); border-radius: 50%; filter: blur(60px); animation: float 20s ease-in-out infinite;"></div>
    <div style="position: absolute; bottom: 10%; left: 5%; width: 250px; height: 250px; background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, transparent 70%); border-radius: 50%; filter: blur(60px); animation: float 25s ease-in-out infinite reverse;"></div>
</section>

<!-- Section statistiques (optionnel mais impactant) -->
<section class="container" style="margin-top: -2rem; margin-bottom: 4rem;">
    <div class="grid grid-3 gap-6">
        <!-- Stat 1 : Nombre de catégories -->
        <div class="card text-center p-6" style="border: 2px solid var(--primary-100); background: linear-gradient(135deg, var(--bg-primary) 0%, var(--primary-50) 100%);">
            <div class="stat-value" style="font-size: 2.5rem; margin-bottom: 0.5rem;">
                <?= count($categories) ?>
            </div>
            <div class="stat-label" style="color: var(--text-secondary); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.1em;">
                Catégories
            </div>
        </div>

        <!-- Stat 2 : Total de produits -->
        <div class="card text-center p-6" style="border: 2px solid var(--secondary-100); background: linear-gradient(135deg, var(--bg-primary) 0%, #f5f3ff 100%);">
            <div class="stat-value" style="font-size: 2.5rem; margin-bottom: 0.5rem;">
                <?php 
                $totalProducts = array_sum(array_column($categories, 'product_count'));
                echo number_format($totalProducts, 0, ',', ' ');
                ?>
            </div>
            <div class="stat-label" style="color: var(--text-secondary); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.1em;">
                Produits disponibles
            </div>
        </div>

        <!-- Stat 3 : Vendeurs actifs (estimation) -->
        <div class="card text-center p-6" style="border: 2px solid #d1fae5; background: linear-gradient(135deg, var(--bg-primary) 0%, var(--success-light) 100%);">
            <div class="stat-value" style="font-size: 2.5rem; margin-bottom: 0.5rem;">
                100+
            </div>
            <div class="stat-label" style="color: var(--text-secondary); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.1em;">
                Vendeurs vérifiés
            </div>
        </div>
    </div>
</section>

<!-- Grille des catégories -->
<section class="container mb-16">
    <!-- En-tête de section -->
    <div class="section-header text-center mb-12">
        <h2 class="section-title">Parcourir par catégorie</h2>
        <p class="section-description">
            Trouvez exactement ce que vous cherchez parmi notre sélection organisée
        </p>
    </div>

    <!-- Grille responsive des catégories -->
    <div class="grid grid-4 gap-6" style="grid-template-columns: repeat(auto-fill, minmax(min(280px, 100%), 1fr));">
        <?php foreach ($categories as $index => $category): ?>
            <!-- Card catégorie avec animations -->
            <a 
                href="/category/<?= e($category['slug']) ?>" 
                class="card category-card" 
                style="
                    text-decoration: none; 
                    transition: all var(--transition-base);
                    animation: fadeInUp 0.6s ease-out backwards;
                    animation-delay: <?= $index * 0.05 ?>s;
                    border: 2px solid transparent;
                    position: relative;
                    overflow: hidden;
                "
                onmouseover="this.style.borderColor='var(--primary-400)'; this.style.transform='translateY(-8px) scale(1.02)'"
                onmouseout="this.style.borderColor='transparent'; this.style.transform='translateY(0) scale(1)'"
            >
                <!-- Gradient overlay subtil au hover -->
                <div style="
                    position: absolute;
                    inset: 0;
                    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
                    opacity: 0;
                    transition: opacity var(--transition-base);
                    pointer-events: none;
                    z-index: 0;
                " class="category-overlay"></div>

                <!-- Contenu de la card -->
                <div style="position: relative; z-index: 1; text-align: center;">
                    <!-- Icône emoji grande taille avec animation -->
                    <div style="
                        font-size: 4rem; 
                        margin-bottom: 1.5rem;
                        transition: transform var(--transition-bounce);
                        display: inline-block;
                    " class="category-icon">
                        <?php
                        // Mapping icônes par slug de catégorie
                        $icons = [
                            'templates' => '📄',
                            'ui-kits' => '🎨',
                            'icones' => '🎯',
                            'icons' => '🎯',
                            'illustrations' => '🖼️',
                            'photos' => '📸',
                            'videos' => '🎬',
                            'audio' => '🎵',
                            'music' => '🎵',
                            'fonts' => '🔤',
                            'code' => '💻',
                            'scripts' => '💻',
                            'ebooks' => '📚',
                            'books' => '📚',
                            'formations' => '🎓',
                            'courses' => '🎓',
                            'plugins' => '🔌',
                            'themes' => '🎨',
                            'graphics' => '✨',
                            'design' => '🎨',
                            'marketing' => '📊',
                            'business' => '💼',
                            'productivity' => '⚡',
                            '3d' => '🎲',
                            'animation' => '🎬'
                        ];
                        echo $icons[$category['slug']] ?? '📦';
                        ?>
                    </div>
                    
                    <!-- Nom de la catégorie -->
                    <h3 style="
                        font-size: 1.25rem; 
                        font-weight: 700; 
                        margin-bottom: 0.75rem;
                        color: var(--text-primary);
                        transition: color var(--transition-fast);
                    " class="category-title">
                        <?= e($category['name']) ?>
                    </h3>
                    
                    <!-- Description si disponible -->
                    <?php if (!empty($category['description'])): ?>
                        <p style="
                            font-size: 0.875rem; 
                            color: var(--text-secondary); 
                            line-height: 1.6;
                            margin-bottom: 1rem;
                            display: -webkit-box;
                            -webkit-line-clamp: 2;
                            -webkit-box-orient: vertical;
                            overflow: hidden;
                        ">
                            <?= e(substr($category['description'], 0, 100)) ?><?= strlen($category['description']) > 100 ? '...' : '' ?>
                        </p>
                    <?php endif; ?>
                    
                    <!-- Badge compteur de produits -->
                    <div style="
                        display: inline-flex;
                        align-items: center;
                        gap: 0.5rem;
                        padding: 0.5rem 1rem;
                        background: var(--primary-100);
                        color: var(--primary-700);
                        border-radius: var(--radius-full);
                        font-size: 0.875rem;
                        font-weight: 600;
                        margin-top: 0.5rem;
                    ">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                            <!-- Icône paquet -->
                            <path d="M20 6h-4V4c0-1.1-.9-2-2-2h-4c-1.1 0-2 .9-2 2v2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM10 4h4v2h-4V4zm10 16H4V8h16v12z"/>
                        </svg>
                        <span><?= number_format($category['product_count'], 0, ',', ' ') ?> produit<?= $category['product_count'] > 1 ? 's' : '' ?></span>
                    </div>
                </div>

                <!-- Script inline pour animation hover de l'icône -->
                <script>
                    document.currentScript.parentElement.addEventListener('mouseenter', function() {
                        this.querySelector('.category-icon').style.transform = 'scale(1.15) rotate(5deg)';
                        this.querySelector('.category-overlay').style.opacity = '1';
                        this.querySelector('.category-title').style.color = 'var(--primary-600)';
                    });
                    document.currentScript.parentElement.addEventListener('mouseleave', function() {
                        this.querySelector('.category-icon').style.transform = 'scale(1) rotate(0deg)';
                        this.querySelector('.category-overlay').style.opacity = '0';
                        this.querySelector('.category-title').style.color = 'var(--text-primary)';
                    });
                </script>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Section CTA finale -->
<section class="container mb-16">
    <div class="card text-center p-12" style="
        background: var(--gradient-primary);
        color: white;
        position: relative;
        overflow: hidden;
    ">
        <!-- Pattern décoratif en background -->
        <div style="
            position: absolute;
            inset: 0;
            opacity: 0.1;
            background-image: repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,.1) 35px, rgba(255,255,255,.1) 70px);
        "></div>

        <!-- Contenu -->
        <div style="position: relative; z-index: 1;">
            <h2 style="color: white; margin-bottom: 1rem; font-size: 2rem;">
                Vous êtes créateur ?
            </h2>
            <p style="font-size: 1.125rem; margin-bottom: 2rem; opacity: 0.95;">
                Rejoignez notre communauté de vendeurs et partagez vos créations avec le monde
            </p>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
                <!-- Si non connecté -->
                <a href="/register?role=seller" class="btn btn-lg" style="
                    background: white;
                    color: var(--primary-600);
                    box-shadow: var(--shadow-xl);
                    font-weight: 700;
                ">
                    Devenir vendeur →
                </a>
            <?php elseif ($_SESSION['user_role'] === 'seller'): ?>
                <!-- Si déjà vendeur -->
                <a href="/seller/products/create" class="btn btn-lg" style="
                    background: white;
                    color: var(--primary-600);
                    box-shadow: var(--shadow-xl);
                    font-weight: 700;
                ">
                    Ajouter un produit →
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CSS inline pour les animations -->
<style>
/* Animation keyframes déjà définie dans style.css mais on s'assure qu'elle existe */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px) translateX(0px);
    }
    33% {
        transform: translateY(-20px) translateX(10px);
    }
    66% {
        transform: translateY(10px) translateX(-10px);
    }
}

/* Amélioration du hover sur les cards catégories */
.category-card:hover {
    box-shadow: var(--shadow-2xl), var(--shadow-primary) !important;
}
</style>
