<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MarketFlow Pro - La marketplace pour créateurs digitaux">
    <meta name="keywords" content="marketplace php, plateforme ecommerce, vente produits numériques, templates, ui kits, code source marketplace, stripe multi-vendeurs, marketplace digitale, vendre en ligne">
    <meta name="author" content="A. Devance - MarketFlow Pro">

    <!-- Open Graph -->
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

    <!-- SEO -->
    <link rel="canonical" href="<?= APP_URL . $_SERVER['REQUEST_URI'] ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

    <title><?= isset($title) ? e($title) . ' - ' : '' ?>MarketFlow Pro</title>

    <!-- Polices -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= CSS_URL ?>/style.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/dark-mode.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/notifications.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/animations.css">

    <!-- JS -->
    <script src="<?= JS_URL ?>/app.js" defer></script>
    <script src="<?= JS_URL ?>/notifications.js" defer></script>
    <script src="<?= JS_URL ?>/wishlist.js" defer></script>
    <script src="<?= JS_URL ?>/animations.js" defer></script>

    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="<?= APP_URL ?>/favicon.ico">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= APP_URL ?>/favicon-192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= APP_URL ?>/apple-touch-icon.png">
</head>
<body <?php if (isLoggedIn()): ?>data-user-logged-in="true"<?php endif; ?>>

<nav>
    <div class="container">
        <div class="nav-inner">

            <!-- Logo -->
            <a href="/" class="nav-logo">
                <div class="nav-logo-icon">M</div>
                <span class="nav-logo-text">MarketFlow</span>
            </a>

            <!-- Liens principaux -->
            <ul class="nav-links">
                <li>
                    <a href="/" <?= ($_SERVER['REQUEST_URI'] === '/') ? 'class="active"' : '' ?>>
                        Accueil
                    </a>
                </li>
                <li>
                    <a href="/products" <?= str_starts_with($_SERVER['REQUEST_URI'], '/products') ? 'class="active"' : '' ?>>
                        Produits
                    </a>
                </li>
                <li>
                    <a href="/category" <?= str_starts_with($_SERVER['REQUEST_URI'], '/category') ? 'class="active"' : '' ?>>
                        Catégories
                    </a>
                </li>
            </ul>

            <!-- Partie droite -->
            <div class="nav-right">

                <!-- Wishlist -->
                <?php
                $wishlistCount = 0;
                if (isLoggedIn()) {
                    $wishlistModel = new \App\Models\Wishlist();
                    $wishlistCount = $wishlistModel->getCount($_SESSION['user_id']);
                }
                ?>
                <a href="/wishlist" class="icon-btn icon-btn--wishlist" title="Ma liste de souhaits">
                    <svg viewBox="0 0 24 24">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    <?php if ($wishlistCount > 0): ?>
                        <span class="icon-badge wishlist-count"><?= e($wishlistCount) ?></span>
                    <?php endif; ?>
                </a>

                <!-- Panier -->
                <a href="/cart" class="icon-btn icon-btn--cart" title="Mon panier">
                    <svg viewBox="0 0 24 24">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    <?php $cartCount = $_SESSION['cart_count'] ?? 0; ?>
                    <?php if ($cartCount > 0): ?>
                        <span class="icon-badge"><?= e($cartCount) ?></span>
                    <?php endif; ?>
                </a>

                <div class="nav-sep"></div>

                <?php if (isLoggedIn()): ?>
                    <?php $currentUser = getCurrentUser(); ?>

                    <!-- Menu utilisateur connecté -->
                    <div class="nav-dropdown" data-dropdown>

                        <button class="nav-user-btn">
                            <div class="nav-avatar <?= $currentUser['role'] === 'admin' ? 'nav-avatar--admin' : '' ?>">
                                <?= strtoupper(substr($currentUser['username'], 0, 1)) ?>
                                <?php if ($currentUser['role'] === 'admin'): ?>
                                    <span class="nav-avatar-crown">👑</span>
                                <?php endif; ?>
                            </div>
                            <div class="nav-user-info">
                                <span class="nav-username"><?= e($currentUser['username']) ?></span>
                                <?php if ($currentUser['role'] === 'admin'): ?>
                                    <span class="nav-role-badge">ADMIN</span>
                                <?php endif; ?>
                            </div>
                            <svg class="nav-chevron" viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </button>

                        <div class="dropdown-menu">

                            <a href="/account" class="dropdown-item">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                                Mon compte
                            </a>

                            <a href="/orders" class="dropdown-item">
                                <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                Mes commandes
                            </a>

                            <a href="/wishlist" class="dropdown-item">
                                <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                Mes favoris
                            </a>

                            <?php if ($currentUser['role'] === 'seller'): ?>
                                <div class="dropdown-sep"></div>
                                <a href="/seller/dashboard" class="dropdown-item">
                                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 17.5h7M17.5 14v7"/></svg>
                                    Dashboard vendeur
                                </a>
                            <?php endif; ?>

                            <?php if ($currentUser['role'] === 'admin'): ?>
                                <div class="dropdown-sep"></div>
                                <a href="/admin" class="dropdown-item dropdown-item--admin">
                                    <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                                    Administration
                                </a>
                                <?php $criticalCount = getSecurityAlerts(); ?>
                                <a href="/admin/security" class="dropdown-item <?= $criticalCount > 0 ? 'dropdown-item--alert' : '' ?>">
                                    <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                    Monitoring Sécurité
                                    <?php if ($criticalCount > 0): ?>
                                        <span class="dropdown-item-badge"><?= $criticalCount ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>

                            <div class="dropdown-sep"></div>

                            <a href="/logout" class="dropdown-item dropdown-item--danger">
                                <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Déconnexion
                            </a>

                        </div>
                    </div>

                <?php else: ?>

                    <a href="/login" class="btn-login">Connexion</a>
                    <a href="/register" class="btn-register">S'inscrire</a>

                <?php endif; ?>

            </div><!-- /nav-right -->
        </div><!-- /nav-inner -->
    </div><!-- /container -->
</nav>

<!-- Flash message -->
<?php if ($flash = getFlashMessage()): ?>
    <div data-flash-message="<?= e($flash['message']) ?>"
         data-flash-type="<?= e($flash['type']) ?>"
         style="display:none;"></div>
<?php endif; ?>

<!-- CONTENU PRINCIPAL -->
