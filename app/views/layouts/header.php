<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MarketFlow Pro - La marketplace pour créateurs digitaux">

<title><?= isset($title) ? e($title) . ' - ' : '' ?>MarketFlow Pro</title>

<link rel="stylesheet" href="<?= CSS_URL ?>/style.css">
<link rel="stylesheet" href="<?= CSS_URL ?>/notifications.css">

<script src="<?= JS_URL ?>/app.js" defer></script>
<script src="<?= JS_URL ?>/wishlist.js" defer></script>

<link rel="icon" href="<?= IMG_URL ?>/favicon.ico">

<style>
    .wishlist-count {
        animation: pulse-badge 2s infinite;
    }
    @keyframes pulse-badge {
        0%,100% { box-shadow:0 0 0 0 rgba(239,68,68,.6); }
        50% { box-shadow:0 0 0 6px rgba(239,68,68,0); }
    }

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
    transition-delay: 150ms; /* Délai avant de disparaître */
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
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #8b5cf6 100%);
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
                        <?= $wishlistCount ?>
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
                    <?= $cartCount ?>
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
            background:<?= $currentUser['role']==='admin' ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : 'var(--gradient-primary)' ?>;
            display:flex;
            align-items:center;
            justify-content:center;
            color:white;
            font-weight:800;
            font-size: 1.1rem;
            box-shadow: 0 4px 12px <?= $currentUser['role']==='admin' ? 'rgba(102, 126, 234, 0.4)' : 'rgba(59, 130, 246, 0.3)' ?>;
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
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            text-decoration: none;
                            transition: all 0.2s;
                            font-weight: 600;
                            font-size: 0.95rem;
                            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
                        " onmouseover="this.style.transform='translateX(4px)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.4)'" onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='0 2px 8px rgba(102, 126, 234, 0.3)'">
                            👑 Administration
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
     data-flash-type="<?= $flash['type'] ?>" style="display:none;"></div>
<?php endif; ?>

<!-- CONTENU PRINCIPAL -->
