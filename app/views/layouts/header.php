<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MarketFlow Pro - La marketplace pour créateurs digitaux">

<!-- Meta Keywords -->
    <meta name="keywords" content="marketplace php, plateforme ecommerce, vente produits numériques, templates, ui kits, code source marketplace, stripe multi-vendeurs, marketplace digitale, vendre en ligne">

    <!-- Meta Author -->
    <meta name="author" content="A. Devancé - MarketFlow Pro">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= APP_URL ?>">
    <meta property="og:title" content="<?= isset($title) ? e($title) . ' - MarketFlow Pro' : 'MarketFlow Pro - Marketplace pour créateurs digitaux' ?>">
    <meta property="og:description" content="Vendez et achetez des templates, designs, codes et ressources premium. Rejoignez des milliers de créateurs qui génèrent des revenus passifs.">
    <meta property="og:image" content="<?= APP_URL ?>/img/og-image.jpg">
    <meta property="og:site_name" content="MarketFlow Pro">
    <meta property="og:locale" content="fr_FR">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= APP_URL ?>">
    <meta name="twitter:title" content="<?= isset($title) ? e($title) . ' - MarketFlow Pro' : 'MarketFlow Pro - Marketplace pour créateurs digitaux' ?>">
    <meta name="twitter:description" content="Vendez et achetez des templates, designs, codes et ressources premium. Rejoignez des milliers de créateurs.">
    <meta name="twitter:image" content="<?= APP_URL ?>/img/twitter-card.jpg">

    <!-- Canonical URL (pour éviter duplicate content) -->
    <link rel="canonical" href="<?= APP_URL . $_SERVER['REQUEST_URI'] ?>">

    <!-- Robots Meta -->
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

<title><?= isset($title) ? e($title) . ' - ' : '' ?>MarketFlow Pro</title>

<link rel="stylesheet" href="<?= CSS_URL ?>/style.css">
<link rel="stylesheet" href="<?= CSS_URL ?>/notifications.css">
<link rel="stylesheet" href="<?= CSS_URL ?>/animations.css">  

    <script src="<?= JS_URL ?>/app.js" defer></script>
    <script src="<?= JS_URL ?>/notifications.js" defer></script>
    <script src="<?= JS_URL ?>/wishlist.js" defer></script>
    <script src="<?= JS_URL ?>/animations.js" defer></script>  

<link rel="icon" href="<?= IMG_URL ?>/favicon.ico">

<style>
    /* Animation wishlist badge */
    .wishlist-count {
        animation: pulse-badge 2s infinite;
    }
    @keyframes pulse-badge {
        0%,100% { box-shadow:0 0 0 0 rgba(239,68,68,.6); }
        50% { box-shadow:0 0 0 6px rgba(239,68,68,0); }
    }

    /* Animation dropdown */
   .dropdown-menu {
    opacity:0;
    transform: translateY(10px);
    pointer-events:none;
    transition: all 180ms ease;
    transition-delay: 0s;
}

[data-dropdown]:hover .dropdown-menu,
.dropdown-menu:hover {
    opacity:1;
    transform: translateY(0);
    pointer-events:auto;
    transition-delay: 0s;
}

[data-dropdown]:not(:hover) .dropdown-menu:not(:hover) {
    transition-delay: 150ms;
}

/* NOUVEAU : Bouton Licence Fondateur animé */
@keyframes shimmer {
    0% { background-position: -200% center; }
    100% { background-position: 200% center; }
}

.btn-licence-fondateur {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.2rem;
    border-radius: 10px;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #fbbf24 100%);
    background-size: 200% auto;
    color: #1f2937;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4);
    transition: all 0.3s ease;
    animation: shimmer 3s linear infinite;
}

.btn-licence-fondateur:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(251, 191, 36, 0.6);
}

.btn-licence-fondateur .rocket {
    font-size: 1.1rem;
    animation: rocket-shake 0.5s ease-in-out infinite alternate;
}

@keyframes rocket-shake {
    0% { transform: rotate(-5deg); }
    100% { transform: rotate(5deg); }
}

.btn-licence-fondateur .badge-urgent {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ef4444;
    color: white;
    font-size: 0.65rem;
    font-weight: 800;
    padding: 0.15rem 0.4rem;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
}
</style>
</head>

<body <?php if (isLoggedIn()): ?>data-user-logged-in="true"<?php endif; ?>>

<nav>
    <div class="container">
        <div class="flex flex-between" style="height:70px;">
        <!-- LOGO -->
        <a href="/" class="flex gap-4" style="align-items:center;">
            <div style="
    width:42px;height:42px;
    border-radius:12px;
    background: linear-gradient(135deg, #3b82f6 0%, #0ea5e9 100%);
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-weight:900;
    box-shadow: var(--shadow-md);
">
    M
</div>
            <span style="font-size:1.2rem;font-weight:800;">
                MarketFlow
            </span>
        </a>

        <!-- NAV LINKS -->
        <div class="flex gap-4" style="align-items:center;">
            <a href="/">Accueil</a>
            <a href="/products">Produits</a>
            <a href="/category">Catégories</a>
            
            <!-- 🔥 NOUVEAU : Bouton Licence Fondateur -->
            <a href="/licence-fondateur" class="btn-licence-fondateur">
                <span class="rocket">🚀</span>
                Licence Fondateur
                <span class="badge-urgent">3</span>
            </a>
        </div>

        <!-- SEARCH -->
        <form action="/search" method="GET" style="position:relative;max-width:280px;width:100%;">
            <input type="text" name="q" placeholder="Rechercher..."
                   style="
                    width:100%;
                    padding:.6rem 2.2rem .6rem 1rem;
                    border-radius: var(--radius-lg);
                    border:1px solid var(--gray-200);
                    background: var(--bg-primary);
                   ">
            <button type="submit" style="
                position:absolute;
                right:.6rem;top:50%;
                transform:translateY(-50%);
                background:none;border:none;
                cursor:pointer;
                color:var(--text-secondary);
            ">
                🔍
            </button>
        </form>

        <!-- ACTIONS -->
        <div class="flex gap-4" style="align-items:center;">

            <?php if (isLoggedIn()): ?>
                <?php 
                $wishlistModel = new \App\Models\Wishlist();
                $wishlistCount = $wishlistModel->getCount($_SESSION["user_id"]);
                ?>

                <!-- Wishlist -->
                <a href="/wishlist" style="position:relative;">
                    ❤️
                    <?php if ($wishlistCount > 0): ?>
                    <span class="wishlist-count" style="
                        position:absolute;
                        top:-8px;right:-10px;
                        background:#ef4444;
                        color:white;
                        font-size:.65rem;
                        font-weight:700;
                        width:18px;height:18px;
                        border-radius:50%;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                    ">
                        <?= e($wishlistCount) ?>
                    </span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <!-- Cart -->
            <a href="/cart" style="position:relative;">🛒
                <?php $cartCount = $_SESSION['cart_count'] ?? 0; ?>
                <?php if ($cartCount > 0): ?>
                <span style="
                    position:absolute;
                    top:-8px;right:-10px;
                    background:#ef4444;
                    color:white;
                    font-size:.65rem;
                    font-weight:700;
                    width:18px;height:18px;
                    border-radius:50%;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                ">
                    <?= e($cartCount) ?>
                </span>
                <?php endif; ?>
            </a>

            <!-- USER -->
            <?php if (isLoggedIn()): ?>
                <?php $currentUser = getCurrentUser(); ?>

                <div class="relative" data-dropdown>
    <button class="flex gap-4" style="
        align-items:center;
        padding: 0.5rem 1rem;
        border-radius: 12px;
        transition: all 0.3s;
        background: transparent;
        border: none;
        cursor: pointer;
    " onmouseover="this.style.background='var(--bg-secondary)'" onmouseout="this.style.background='transparent'">
        <div style="
            width:40px;
            height:40px;
            border-radius:50%;
            background:<?= $currentUser['role']==='admin' ? 'linear-gradient(135deg, #3b82f6 0%, #6366f1 100%)' : 'linear-gradient(135deg, #3b82f6 0%, #0ea5e9 100%)' ?>;
            display:flex;
            align-items:center;
            justify-content:center;
            color:white;
            font-weight:800;
            font-size: 1.1rem;
            box-shadow: 0 4px 12px <?= $currentUser['role']==='admin' ? 'rgba(59, 130, 246, 0.4)' : 'rgba(59, 130, 246, 0.3)' ?>;
            position: relative;
        ">
            <?= strtoupper(substr($currentUser['username'],0,1)) ?>
            <?php if ($currentUser['role']==='admin'): ?>
                <span style="
                    position: absolute;
                    bottom: -2px;
                    right: -2px;
                    background: #fbbf24;
                    width: 16px;
                    height: 16px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 0.6rem;
                    border: 2px solid white;
                ">👑</span>
            <?php endif; ?>
        </div>
        <div style="display: flex; flex-direction: column; align-items: flex-start;">
            <span style="font-weight: 600; font-size: 0.95rem; color: var(--text-primary);">
                <?= e($currentUser['username']) ?>
            </span>
            <?php if ($currentUser['role']==='admin'): ?>
                <span style="
                    font-size: 0.7rem;
                    color: white;
                    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
                    padding: 0.1rem 0.5rem;
                    border-radius: 6px;
                    font-weight: 600;
                    margin-top: 2px;
                ">ADMIN</span>
            <?php endif; ?>
        </div>
        <span style="margin-left: 0.25rem; color: var(--text-tertiary); font-size: 0.8rem;">▼</span>
    </button>
                    <div class="dropdown-menu" style="
                    position: absolute;
                    right: 0;
                    top: 100%;
                    min-width: 220px;
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
                    padding: 0.5rem;
                    border: 1px solid rgba(0, 0, 0, 0.05);
                    
                ">
                    <a href="/account" style="
                        display: block;
                        padding: 0.75rem 1rem;
                        border-radius: 8px;
                        color: var(--text-primary);
                        text-decoration: none;
                        transition: all 0.2s;
                        font-size: 0.95rem;
                    " onmouseover="this.style.background='var(--bg-secondary)'" onmouseout="this.style.background='transparent'">
                        👤 Mon compte
                    </a>
                    
                    <a href="/orders" style="
                        display: block;
                        padding: 0.75rem 1rem;
                        border-radius: 8px;
                        color: var(--text-primary);
                        text-decoration: none;
                        transition: all 0.2s;
                        font-size: 0.95rem;
                    " onmouseover="this.style.background='var(--bg-secondary)'" onmouseout="this.style.background='transparent'">
                        📦 Mes commandes
                    </a>
                    
                    <a href="/wishlist" style="
                        display: block;
                        padding: 0.75rem 1rem;
                        border-radius: 8px;
                        color: var(--text-primary);
                        text-decoration: none;
                        transition: all 0.2s;
                        font-size: 0.95rem;
                    " onmouseover="this.style.background='var(--bg-secondary)'" onmouseout="this.style.background='transparent'">
                        ❤️ Mes favoris
                    </a>

                    <?php if ($currentUser['role']==='seller'): ?>
                        <div style="height: 1px; background: var(--border-color); margin: 0.5rem 0;"></div>
                        <a href="/seller/dashboard" style="
                            display: block;
                            padding: 0.75rem 1rem;
                            border-radius: 8px;
                            color: var(--text-primary);
                            text-decoration: none;
                            transition: all 0.2s;
                            font-size: 0.95rem;
                        " onmouseover="this.style.background='var(--bg-secondary)'" onmouseout="this.style.background='transparent'">
                            🏪 Dashboard vendeur
                        </a>
                    <?php endif; ?>

                    <?php if ($currentUser['role']==='admin'): ?>
    <div style="height: 1px; background: var(--border-color); margin: 0.5rem 0;"></div>
    
    <a href="/admin" style="
        display: block;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
        color: white;
        text-decoration: none;
        transition: all 0.2s;
        font-weight: 600;
        font-size: 0.95rem;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    " onmouseover="this.style.transform='translateX(4px)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.4)'" onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='0 2px 8px rgba(59, 130, 246, 0.3)'">
        👑 Administration
    </a>
    
    <?php $criticalCount = getSecurityAlerts(); ?>
    
    <a href="/admin/security" style="
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        <?= $criticalCount > 0 ? 'background: rgba(245, 87, 108, 0.1);' : '' ?>
        color: <?= $criticalCount > 0 ? '#f5576c' : 'var(--text-primary)' ?>;
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.95rem;
        <?= $criticalCount > 0 ? 'font-weight: 600;' : '' ?>
    " onmouseover="this.style.background='<?= $criticalCount > 0 ? 'rgba(245, 87, 108, 0.15)' : 'var(--bg-secondary)' ?>'" onmouseout="this.style.background='<?= $criticalCount > 0 ? 'rgba(245, 87, 108, 0.1)' : 'transparent' ?>'">
        <span>🔒 Monitoring Sécurité</span>
        <?php if ($criticalCount > 0): ?>
            <span style="
                background: #f5576c;
                color: white;
                font-size: 0.7rem;
                font-weight: 700;
                padding: 0.15rem 0.5rem;
                border-radius: 12px;
                margin-left: 0.5rem;
            "><?= $criticalCount ?></span>
        <?php endif; ?>
    </a>
<?php endif; ?>

                    <div style="height: 1px; background: var(--border-color); margin: 0.5rem 0;"></div>
                    
                    <a href="/logout" style="
                        display: block;
                        padding: 0.75rem 1rem;
                        border-radius: 8px;
                        color: #ef4444;
                        text-decoration: none;
                        transition: all 0.2s;
                        font-size: 0.95rem;
                        font-weight: 500;
                    " onmouseover="this.style.background='rgba(239, 68, 68, 0.1)'" onmouseout="this.style.background='transparent'">
                        🚪 Déconnexion
                    </a>
                </div>
            </div>

            <?php else: ?>
                <a href="/login">Connexion</a>
                <a href="/register" class="btn btn-primary">Inscription</a>
            <?php endif; ?>

        </div>
    </div>
</div>

</nav>

<!-- Flash message -->
<?php if ($flash = getFlashMessage()): ?>
<div data-flash-message="<?= e($flash['message']) ?>"
     data-flash-type="<?= e($flash['type']) ?>" style="display:none;"></div>
<?php endif; ?>

<!-- CONTENU PRINCIPAL -->
