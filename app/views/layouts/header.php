<?php
/**
 * MARKETFLOW PRO - HEADER LAYOUT
 * Fichier : app/views/layouts/header.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MarketFlow Pro - La marketplace pour créateurs digitaux">
    <title><?= isset($title) ? e($title) . ' - ' : '' ?>MarketFlow Pro</title>
    
    <!-- CSS et JS -->
    <link rel="stylesheet" href="/css/style.css">
    <script src="/public/js/app.js"></script>
    
    <!-- Favicon -->
    <link rel="icon" href="/public/favicon.ico">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="/" class="navbar-brand">MarketFlow Pro</a>
            
            <ul class="navbar-menu">
                <li><a href="/products" class="navbar-link">Produits</a></li>
                <li><a href="/sellers" class="navbar-link">Vendeurs</a></li>
                <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'seller'): ?>
                    <li><a href="/seller/dashboard" class="navbar-link">Dashboard</a></li>
                <?php endif; ?>
            </ul>


<?php if (isset($_SESSION['user_id'])): ?>
    <li><a href="/logout" class="navbar-link">Déconnexion</a></li>
<?php else: ?>
   
<?php endif; ?>

            
            <div class="flex gap-4">
                <!-- Search -->
                <div style="position: relative;">
                    <input 
                        type="search" 
                        placeholder="Rechercher..."
                        style="
                            padding: var(--space-2) var(--space-4);
                            border: 1px solid var(--border-color);
                            border-radius: var(--radius-full);
                            background: var(--bg-secondary);
                            width: 200px;
                        "
                    >
                </div>

                <!-- Cart -->
                <a href="/cart" class="btn btn-ghost btn-sm" style="position: relative;">
                    🛒
                    <?php if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                    <span style="
                        position: absolute;
                        top: -5px;
                        right: -5px;
                        background: var(--error);
                        color: white;
                        font-size: 0.75rem;
                        width: 20px;
                        height: 20px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-weight: 600;
                    ">
                        <?= $_SESSION['cart_count'] ?>
                    </span>
                    <?php endif; ?>
                </a>

                <!-- User Menu -->
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <div style="position: relative;">
                        <button class="btn btn-ghost btn-sm" id="userMenuBtn">
                            👤 <?= e($_SESSION['user_name']) ?>
                        </button>
                        <!-- Dropdown sera ajouté via JS -->
                    </div>
                <?php else: ?>
                    <a href="/login" class="btn btn-ghost btn-sm">Connexion</a>
                    <a href="/register" class="btn btn-primary btn-sm">S'inscrire</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
    <div class="container mt-4">
        <div style="
            background: <?= $flash['type'] === 'success' ? 'var(--success-light)' : ($flash['type'] === 'error' ? 'var(--error-light)' : 'var(--primary-50)') ?>;
            border: 1px solid <?= $flash['type'] === 'success' ? 'var(--success)' : ($flash['type'] === 'error' ? 'var(--error)' : 'var(--primary-600)') ?>;
            color: <?= $flash['type'] === 'success' ? '#065f46' : ($flash['type'] === 'error' ? '#991b1b' : 'var(--primary-700)') ?>;
            padding: var(--space-4);
            border-radius: var(--radius);
            animation: slideIn 0.3s ease-out;
        ">
            <?= e($flash['message']) ?>
        </div>
    </div>
    <?php endif; ?>

    


