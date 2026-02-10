<?php
/**
 * Fichier : app/views/admin/dashboard.php
<?php
/**
 * ================================================
 * MARKETFLOW PRO - DASHBOARD ADMIN MODERNE
 * ================================================
 * 
 * Version : 2.0 (2026)
 * Améliorations UX/UI :
 * - Design pleine largeur sans sidebar
 * - Graphiques Chart.js interactifs
 * - Animations smoothes et micro-interactions
 * - Design system moderne avec gradients
 * - Cards avec profondeur et shadows
 * - Responsive optimisé
 * - Quick actions accessibles
 * 
 * ================================================
 */

// ================================================
// RÉCUPÉRATION DES DONNÉES
// ================================================
// Données passées par le contrôleur
$stats = $stats ?? [];
$recentUsers = $recent_users ?? [];
$pendingProducts = $pending_products ?? [];
$recentOrders = $recent_orders ?? [];

// Calcul des alertes de sécurité
$todayStats = getSecurityStats(1);
$criticalToday = ($todayStats['LOGIN_BLOCKED'] ?? 0) + 
                 ($todayStats['CSRF_VIOLATION'] ?? 0) + 
                 ($todayStats['XSS_ATTEMPT'] ?? 0) + 
                 ($todayStats['SQLI_ATTEMPT'] ?? 0);

// ================================================
// DONNÉES POUR LES GRAPHIQUES (7 derniers jours)
// ================================================
$last7Days = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $last7Days[] = [
        'date' => date('d/m', strtotime($date)),
        'orders' => rand(5, 25), // TODO: Remplacer par vraies données
        'revenue' => rand(200, 1500), // TODO: Remplacer par vraies données
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - MarketFlow Pro</title>
    
    <style>
        /* ================================================
           VARIABLES CSS - DESIGN SYSTEM
           ================================================ */
        :root {
            /* Hauteur du header */
            --header-height: 70px;
            
            /* Couleurs principales */
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            
            /* Gradients modernes */
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-success: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --gradient-warning: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --gradient-info: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            
            /* Ombres */
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.15);
            
            /* Espacements */
            --space-xs: 0.25rem;
            --space-sm: 0.5rem;
            --space-md: 1rem;
            --space-lg: 1.5rem;
            --space-xl: 2rem;
            --space-2xl: 3rem;
        }

        /* ================================================
           RESET & BASE
           ================================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }

        /* ================================================
           LAYOUT - MAIN CONTENT PLEINE LARGEUR
           ================================================ */
        .admin-layout {
            min-height: 100vh;
            width: 100%;
        }

        /* MAIN CONTENT AREA - Pleine largeur */
        .main-content {
            width: 100%;
            min-height: 100vh;
            background: #f8fafc;
        }

        /* ================================================
           HEADER - En-tête de la page
           ================================================ */
        .content-header {
            background: white;
            padding: var(--space-xl) var(--space-2xl);
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }

        .content-header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--space-lg);
        }

        /* Titre de la page avec gradient */
        .page-title {
            font-size: 2rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: var(--space-md);
        }

        /* Boutons d'actions rapides */
        .quick-actions {
            display: flex;
            gap: var(--space-sm);
        }

        .btn-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 1.25rem;
        }

        .btn-icon:hover {
            background: #f8fafc;
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* ================================================
           BREADCRUMB - Fil d'ariane
           ================================================ */
        .breadcrumb {
            display: flex;
            gap: var(--space-sm);
            color: #64748b;
            font-size: 0.875rem;
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .breadcrumb-separator {
            color: #cbd5e1;
        }

        /* ================================================
           CONTENT BODY - Zone de contenu principale
           ================================================ */
        .content-body {
            padding: var(--space-2xl);
        }

        /* ================================================
           COMPONENTS - STATS CARDS
           ================================================ */
        /* Grille responsive pour les cartes de stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-lg);
            margin-bottom: var(--space-2xl);
        }

        /* Carte de statistique individuelle */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: var(--space-xl);
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        /* Barre de couleur en haut au survol */
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        /* Animation au survol */
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        /* En-tête de la carte de stat */
        .stat-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: var(--space-lg);
        }

        /* Icône avec gradient */
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            background: var(--gradient-primary);
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.2);
        }

        /* Variantes de couleur pour les icônes */
        .stat-card.success .stat-icon {
            background: var(--gradient-success);
        }

        .stat-card.warning .stat-icon {
            background: var(--gradient-warning);
        }

        .stat-card.info .stat-icon {
            background: var(--gradient-info);
        }

        /* Indicateur de tendance (hausse/baisse) */
        .stat-trend {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
        }

        .stat-trend.up {
            background: #d1fae5;
            color: #065f46;
        }

        .stat-trend.down {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Label de la statistique */
        .stat-label {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: var(--space-xs);
        }

        /* Valeur principale de la stat */
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
        }

        /* Footer de la carte avec info supplémentaire */
        .stat-footer {
            margin-top: var(--space-md);
            padding-top: var(--space-md);
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            color: #64748b;
            font-size: 0.875rem;
        }

        /* ================================================
           COMPONENTS - ALERT BANNER
           ================================================ */
        /* Bannière d'alerte */
        .alert-banner {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
            padding: var(--space-xl);
            margin-bottom: var(--space-2xl);
            display: flex;
            align-items: center;
            gap: var(--space-lg);
            box-shadow: var(--shadow-md);
        }

        /* Variante danger (rouge) */
        .alert-banner.danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-left-color: var(--danger);
        }

        /* Icône de l'alerte */
        .alert-icon {
            font-size: 2rem;
            flex-shrink: 0;
        }

        /* Contenu textuel de l'alerte */
        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 700;
            font-size: 1.125rem;
            margin-bottom: var(--space-xs);
        }

        .alert-description {
            color: #64748b;
            font-size: 0.875rem;
        }

        /* Boutons d'action de l'alerte */
        .alert-actions {
            display: flex;
            gap: var(--space-sm);
            flex-shrink: 0;
        }

        /* ================================================
           COMPONENTS - CARDS
           ================================================ */
        /* Carte générique */
        .card {
            background: white;
            border-radius: 16px;
            padding: var(--space-xl);
            box-shadow: var(--shadow-md);
            margin-bottom: var(--space-xl);
        }

        /* En-tête de carte */
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--space-lg);
            padding-bottom: var(--space-lg);
            border-bottom: 1px solid #f1f5f9;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        /* Badge pour compteurs ou statuts */
        .card-badge {
            background: #f1f5f9;
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
        }

        /* Variantes de couleur pour badges */
        .card-badge.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .card-badge.danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .card-badge.success {
            background: #d1fae5;
            color: #065f46;
        }

        /* ================================================
           COMPONENTS - CHART CONTAINER
           ================================================ */
        /* Conteneur pour les graphiques Chart.js */
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: var(--space-lg);
        }

        /* ================================================
           COMPONENTS - LIST ITEMS
           ================================================ */
        /* Item de liste (utilisateurs, produits, etc.) */
        .list-item {
            padding: var(--space-md);
            background: #f8fafc;
            border-radius: 12px;
            display: flex;
            gap: var(--space-md);
            align-items: center;
            margin-bottom: var(--space-sm);
            transition: all 0.2s ease;
        }

        .list-item:hover {
            background: #f1f5f9;
            transform: translateX(4px);
        }

        /* Avatar image */
        .list-item-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            object-fit: cover;
            flex-shrink: 0;
        }

        /* Avatar placeholder avec initiale */
        .list-item-avatar-placeholder {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* Contenu textuel de l'item */
        .list-item-content {
            flex: 1;
            min-width: 0;
        }

        .list-item-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .list-item-subtitle {
            color: #64748b;
            font-size: 0.875rem;
        }

        /* Boutons d'action de l'item */
        .list-item-actions {
            display: flex;
            gap: var(--space-xs);
            flex-shrink: 0;
        }

        /* ================================================
           COMPONENTS - BUTTONS
           ================================================ */
        /* Bouton générique */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-sm);
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        /* Variantes de boutons */
        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-outline {
            background: white;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Taille réduite de bouton */
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }

        /* ================================================
           COMPONENTS - TABLE
           ================================================ */
        /* Conteneur responsive pour tableau */
        .table-container {
            overflow-x: auto;
            margin-top: var(--space-lg);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* En-tête de tableau */
        thead tr {
            border-bottom: 2px solid #e2e8f0;
        }

        th {
            padding: var(--space-md);
            text-align: left;
            font-weight: 600;
            color: #64748b;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Lignes du tableau */
        tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s ease;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        td {
            padding: var(--space-md);
        }

        /* ================================================
           UTILITIES
           ================================================ */
        /* Grille 2 colonnes responsive */
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: var(--space-xl);
        }

        /* État vide (no data) */
        .empty-state {
            text-align: center;
            padding: var(--space-2xl);
            color: #64748b;
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: var(--space-md);
            opacity: 0.5;
        }

        /* ================================================
           RESPONSIVE
           ================================================ */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }

        @media (max-width: 768px) {
            /* Espacement réduit sur mobile */
            .content-header {
                padding: var(--space-lg);
            }

            .content-body {
                padding: var(--space-lg);
            }

            .page-title {
                font-size: 1.5rem;
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        /* ================================================
           ANIMATIONS
           ================================================ */
        /* Animation d'entrée par le bas */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animation de pulsation */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        /* Classes utilitaires d'animation */
        .animate-slide-in {
            animation: slideInUp 0.4s ease-out;
        }

        .animate-pulse {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        
        <!-- ================================================
             MAIN CONTENT AREA - Zone de contenu principale
             ================================================ -->
        <main class="main-content">
            
            <!-- ================================================
                 HEADER - En-tête avec titre et actions rapides
                 ================================================ -->
            <header class="content-header">
                <div class="content-header-top">
                    <!-- Titre et fil d'ariane -->
                    <div>
                        <h1 class="page-title">
                            <span>👑</span>
                            Dashboard Admin
                        </h1>
                        <div class="breadcrumb">
                            <span class="breadcrumb-item">MarketFlow</span>
                            <span class="breadcrumb-separator">›</span>
                            <span class="breadcrumb-item">Administration</span>
                            <span class="breadcrumb-separator">›</span>
                            <span class="breadcrumb-item">Dashboard</span>
                        </div>
                    </div>

                    <!-- Boutons d'actions rapides -->
                    <div class="quick-actions">
                        <button class="btn-icon" title="Notifications">🔔</button>
                        <button class="btn-icon" title="Recherche">🔍</button>
                        <button class="btn-icon" title="Paramètres">⚙️</button>
                    </div>
                </div>
            </header>

            <!-- ================================================
                 BODY - Contenu principal du dashboard
                 ================================================ -->
            <div class="content-body">
                
                <!-- ================================================
                     ALERTES CRITIQUES
                     Affichées si produits en attente ou alertes sécurité
                     ================================================ -->
                <?php if (!empty($pendingProducts) || $criticalToday > 0): ?>
                <div class="alert-banner <?= $criticalToday > 0 ? 'danger' : '' ?> animate-slide-in">
                    <div class="alert-icon"><?= $criticalToday > 0 ? '🚨' : '⚠️' ?></div>
                    <div class="alert-content">
                        <div class="alert-title">Actions requises</div>
                        <div class="alert-description">
                            Vous avez <?= count($pendingProducts) ?> produit(s) en attente de validation
                            <?php if ($criticalToday > 0): ?>
                                et <?= $criticalToday ?> alerte(s) de sécurité critique
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="alert-actions">
                        <?php if (!empty($pendingProducts)): ?>
                            <a href="#pending-products" class="btn btn-outline">Voir produits</a>
                        <?php endif; ?>
                        <?php if ($criticalToday > 0): ?>
                            <a href="/admin/security" class="btn btn-danger">Sécurité</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ================================================
                     STATS PRINCIPALES
                     4 cartes : Utilisateurs, Produits, Commandes, Revenus
                     ================================================ -->
                <div class="stats-grid">
                    <!-- Total Users -->
                    <div class="stat-card animate-slide-in" style="animation-delay: 0.1s">
                        <div class="stat-card-header">
                            <div class="stat-icon">👥</div>
                            <div class="stat-trend up">
                                <span>↗</span>
                                <span>+12%</span>
                            </div>
                        </div>
                        <div class="stat-label">Total Utilisateurs</div>
                        <div class="stat-value"><?= number_format($stats['total_users'] ?? 0) ?></div>
                        <div class="stat-footer">
                            <span>📈</span>
                            <span>+24 ce mois-ci</span>
                        </div>
                    </div>

                    <!-- Total Products -->
                    <div class="stat-card success animate-slide-in" style="animation-delay: 0.2s">
                        <div class="stat-card-header">
                            <div class="stat-icon">📦</div>
                            <div class="stat-trend up">
                                <span>↗</span>
                                <span>+8%</span>
                            </div>
                        </div>
                        <div class="stat-label">Total Produits</div>
                        <div class="stat-value"><?= number_format($stats['total_products'] ?? 0) ?></div>
                        <div class="stat-footer">
                            <span>✨</span>
                            <span><?= count($pendingProducts) ?> en attente</span>
                        </div>
                    </div>

                    <!-- Total Orders -->
                    <div class="stat-card info animate-slide-in" style="animation-delay: 0.3s">
                        <div class="stat-card-header">
                            <div class="stat-icon">🛍️</div>
                            <div class="stat-trend up">
                                <span>↗</span>
                                <span>+15%</span>
                            </div>
                        </div>
                        <div class="stat-label">Total Commandes</div>
                        <div class="stat-value"><?= number_format($stats['total_orders'] ?? 0) ?></div>
                        <div class="stat-footer">
                            <span>🎯</span>
                            <span>+45 cette semaine</span>
                        </div>
                    </div>

                    <!-- Total Revenue -->
                    <div class="stat-card warning animate-slide-in" style="animation-delay: 0.4s">
                        <div class="stat-card-header">
                            <div class="stat-icon">💰</div>
                            <div class="stat-trend up">
                                <span>↗</span>
                                <span>+23%</span>
                            </div>
                        </div>
                        <div class="stat-label">Revenus Total</div>
                        <div class="stat-value"><?= number_format($stats['total_revenue'] ?? 0, 0) ?>€</div>
                        <div class="stat-footer">
                            <span>💵</span>
                            <span>+3 450€ ce mois</span>
                        </div>
                    </div>
                </div>

                <!-- ================================================
                     GRAPHIQUES
                     Commandes et Revenus sur 7 jours
                     ================================================ -->
                <div class="grid-2">
                    <!-- Graphique Commandes -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <span>📊</span>
                                Commandes (7 derniers jours)
                            </h2>
                        </div>
                        <div class="chart-container">
                            <canvas id="ordersChart"></canvas>
                        </div>
                    </div>

                    <!-- Graphique Revenus -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <span>💰</span>
                                Revenus (7 derniers jours)
                            </h2>
                        </div>
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- ================================================
                     GRILLE DE CONTENU
                     Produits en attente & Utilisateurs récents
                     ================================================ -->
                <div class="grid-2">
                    
                    <!-- ================================================
                         PRODUITS EN ATTENTE DE VALIDATION
                         ================================================ -->
                    <div class="card" id="pending-products">
                        <div class="card-header">
                            <h2 class="card-title">
                                <span>⏳</span>
                                Produits en attente
                            </h2>
                            <span class="card-badge warning"><?= count($pendingProducts) ?></span>
                        </div>

                        <?php if (empty($pendingProducts)): ?>
                            <!-- État vide -->
                            <div class="empty-state">
                                <div class="empty-state-icon">✅</div>
                                <p>Aucun produit en attente</p>
                            </div>
                        <?php else: ?>
                            <!-- Liste des produits (max 5) -->
                            <?php foreach (array_slice($pendingProducts, 0, 5) as $product): ?>
                                <div class="list-item">
                                    <!-- Avatar ou placeholder -->
                                    <?php if ($product['thumbnail_url']): ?>
                                        <img src="<?= e($product['thumbnail_url']) ?>" 
                                             alt="<?= e($product['title']) ?>"
                                             class="list-item-avatar">
                                    <?php else: ?>
                                        <div class="list-item-avatar-placeholder">📦</div>
                                    <?php endif; ?>
                                    
                                    <!-- Informations produit -->
                                    <div class="list-item-content">
                                        <div class="list-item-title"><?= e($product['title']) ?></div>
                                        <div class="list-item-subtitle">Par <?= e($product['seller_name']) ?></div>
                                    </div>

                                    <!-- Actions (Approuver/Rejeter) -->
                                    <div class="list-item-actions">
                                        <a href="/admin/products/approve/<?= e($product['id']) ?>" 
                                           class="btn btn-success btn-sm" title="Approuver">✓</a>
                                        <a href="/admin/products/reject/<?= e($product['id']) ?>" 
                                           class="btn btn-danger btn-sm" title="Rejeter">✗</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <!-- Lien vers la liste complète -->
                            <div style="margin-top: var(--space-lg); text-align: center;">
                                <a href="/admin/products" class="btn btn-outline">Voir tous les produits →</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ================================================
                         UTILISATEURS RÉCENTS
                         ================================================ -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <span>👥</span>
                                Utilisateurs récents
                            </h2>
                            <span class="card-badge"><?= count($recentUsers) ?></span>
                        </div>

                        <?php if (empty($recentUsers)): ?>
                            <!-- État vide -->
                            <div class="empty-state">
                                <div class="empty-state-icon">👤</div>
                                <p>Aucun utilisateur récent</p>
                            </div>
                        <?php else: ?>
                            <!-- Liste des utilisateurs (max 5) -->
                            <?php foreach (array_slice($recentUsers, 0, 5) as $user): ?>
                                <div class="list-item">
                                    <!-- Avatar ou initiale -->
                                    <?php if ($user['avatar_url'] ?? false): ?>
                                        <img src="<?= e($user['avatar_url']) ?>" 
                                             alt="<?= e($user['username']) ?>"
                                             class="list-item-avatar">
                                    <?php else: ?>
                                        <div class="list-item-avatar-placeholder">
                                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Informations utilisateur -->
                                    <div class="list-item-content">
                                        <div class="list-item-title"><?= e($user['full_name'] ?? $user['username']) ?></div>
                                        <div class="list-item-subtitle">@<?= e($user['username']) ?></div>
                                    </div>

                                    <!-- Badge de rôle -->
                                    <span class="card-badge <?= $user['role'] === 'seller' ? 'success' : '' ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>

                            <!-- Lien vers la liste complète -->
                            <div style="margin-top: var(--space-lg); text-align: center;">
                                <a href="/admin/users" class="btn btn-outline">Voir tous les utilisateurs →</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ================================================
                     COMMANDES RÉCENTES
                     Tableau avec détails des commandes
                     ================================================ -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <span>🛍️</span>
                            Commandes récentes
                        </h2>
                        <span class="card-badge success"><?= count($recentOrders) ?></span>
                    </div>

                    <?php if (empty($recentOrders)): ?>
                        <!-- État vide -->
                        <div class="empty-state">
                            <div class="empty-state-icon">📦</div>
                            <p>Aucune commande récente</p>
                        </div>
                    <?php else: ?>
                        <!-- Tableau des commandes (max 10) -->
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>N° Commande</th>
                                        <th>Client</th>
                                        <th style="text-align: right;">Montant</th>
                                        <th style="text-align: center;">Statut</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($recentOrders, 0, 10) as $order): ?>
                                        <tr>
                                            <!-- Numéro de commande (lien cliquable) -->
                                            <td>
                                                <a href="/admin/orders/<?= e($order['order_number']) ?>" 
                                                   style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                                    #<?= e($order['order_number']) ?>
                                                </a>
                                            </td>
                                            <!-- Nom du client -->
                                            <td><?= e($order['customer_name'] ?? $order['buyer_name']) ?></td>
                                            <!-- Montant total -->
                                            <td style="text-align: right; font-weight: 600;">
                                                <?= number_format($order['total_amount'], 2) ?>€
                                            </td>
                                            <!-- Statut avec badge coloré -->
                                            <td style="text-align: center;">
                                                <span class="card-badge <?= $order['status'] === 'completed' ? 'success' : 'warning' ?>">
                                                    <?= ucfirst($order['status']) ?>
                                                </span>
                                            </td>
                                            <!-- Date de création -->
                                            <td style="color: #64748b; font-size: 0.875rem;">
                                                <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Lien vers la liste complète -->
                        <div style="margin-top: var(--space-lg); text-align: center;">
                            <a href="/admin/orders" class="btn btn-outline">Voir toutes les commandes →</a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>

    <!-- ================================================
         CHART.JS - Bibliothèque de graphiques
         ================================================ -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- ================================================
         INITIALISATION DES GRAPHIQUES
         ================================================ -->
    <script>
        // ================================================
        // Configuration commune des graphiques
        // ================================================
        const chartConfig = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // Masquer la légende
                }
            },
            scales: {
                y: {
                    beginAtZero: true, // Commencer à 0
                    grid: {
                        color: '#f1f5f9' // Couleur de la grille
                    }
                },
                x: {
                    grid: {
                        display: false // Masquer la grille verticale
                    }
                }
            }
        };

        // ================================================
        // Données des graphiques (injectées depuis PHP)
        // ================================================
        const chartData = <?= json_encode($last7Days) ?>;
        const labels = chartData.map(d => d.date); // Labels des dates
        const ordersData = chartData.map(d => d.orders); // Nombre de commandes
        const revenueData = chartData.map(d => d.revenue); // Montant des revenus

        // ================================================
        // GRAPHIQUE 1 : Commandes (courbe)
        // ================================================
        new Chart(document.getElementById('ordersChart'), {
            type: 'line', // Type : courbe
            data: {
                labels: labels,
                datasets: [{
                    label: 'Commandes',
                    data: ordersData,
                    borderColor: '#3b82f6', // Couleur de la ligne
                    backgroundColor: 'rgba(59, 130, 246, 0.1)', // Remplissage sous la courbe
                    fill: true,
                    tension: 0.4, // Courbure de la ligne
                    borderWidth: 3 // Épaisseur de la ligne
                }]
            },
            options: chartConfig
        });

        // ================================================
        // GRAPHIQUE 2 : Revenus (barres)
        // ================================================
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar', // Type : barres
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenus (€)',
                    data: revenueData,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)', // Couleur des barres
                    borderRadius: 8 // Coins arrondis
                }]
            },
            options: chartConfig
        });
    </script>
</body>
</html>
