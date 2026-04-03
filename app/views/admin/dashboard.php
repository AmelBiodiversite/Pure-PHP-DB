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
<style>
/* === DESIGN MAQUETTE2 — DASHBOARD ADMIN === */

/* Fond général : beige chaud au lieu du gris froid #f8fafc */
body { background: #faf9f5 !important; color: #1e1208 !important; }

/* Header sticky : fond blanc, bordure fine au lieu de box-shadow prononcé */
.content-header {
    background: #fff !important;
    border-bottom: 0.5px solid #ede8df !important;
    box-shadow: none !important;
}

/* Titre de page : Georgia serif au lieu du dégradé bleu -webkit-clip */
.page-title {
    font-family: Georgia, serif !important;
    font-weight: 400 !important;
    font-size: 24px !important;
    background: none !important;
    -webkit-text-fill-color: unset !important;
    color: #1e1208 !important;
}

/* Boutons icônes du header */
.btn-icon {
    border: 0.5px solid #ede8df !important;
    border-radius: 10px !important;
    box-shadow: none !important;
}
.btn-icon:hover {
    border-color: #7c6cf0 !important;
    box-shadow: none !important;
    transform: none !important;
    background: #f5f3ff !important;
}

/* Fil d'ariane */
.breadcrumb { color: #a0907e !important; font-size: 11px !important; font-family: 'Manrope', sans-serif !important; }

/* Zone de contenu : pas de fond gris */
.main-content, .content-body { background: #faf9f5 !important; }

/* === STAT CARDS === */
.stat-card {
    background: #fff !important;
    border: 0.5px solid #ede8df !important;
    border-radius: 14px !important;
    box-shadow: none !important;
}
/* Barre couleur au survol : remplace le dégradé bleu */
.stat-card::before { background: #7c6cf0 !important; }
.stat-card:hover { transform: translateY(-2px) !important; box-shadow: none !important; }

/* Icônes de stats : dégradés vivifiants → pastels maquette2 */
.stat-icon { box-shadow: none !important; }
/* Icône bleue par défaut → violet pastel */
.stat-card .stat-icon { background: #ede9fe !important; }
/* Icône verte (success) → vert naturel */
.stat-card.success .stat-icon { background: #e4f1d8 !important; }
/* Icône rose (warning) → abricot doux */
.stat-card.warning .stat-icon { background: #fef3e0 !important; }
/* Icône cyan (info) → bleu lavande */
.stat-card.info .stat-icon { background: #dbeafe !important; }

/* Valeur principale de la stat */
.stat-value {
    font-family: Georgia, serif !important;
    font-weight: 400 !important;
    color: #1e1208 !important;
    font-size: 28px !important;
}

/* Indicateur tendance hausse */
.stat-trend.up { background: #e4f1d8 !important; color: #2d6a35 !important; }
.stat-trend.down { background: #fce5df !important; color: #993c1d !important; }

/* Pied de carte stat */
.stat-footer { border-top: 0.5px solid #ede8df !important; color: #a0907e !important; font-size: 11px !important; font-family: 'Manrope', sans-serif !important; }

/* Label de stat */
.stat-label { color: #6b5c4e !important; font-size: 11px !important; font-family: 'Manrope', sans-serif !important; }

/* === BANNIÈRE D'ALERTE === */
.alert-banner {
    background: #fef9e7 !important;
    border-left-color: #c99a27 !important;
    border-radius: 12px !important;
    box-shadow: none !important;
}
.alert-banner.danger {
    background: #fce5df !important;
    border-left-color: #993c1d !important;
}
.alert-title { font-family: Georgia, serif !important; font-weight: 400 !important; color: #1e1208 !important; }
.alert-description { color: #6b5c4e !important; font-size: 12px !important; font-family: 'Manrope', sans-serif !important; }

/* === CARDS GÉNÉRIQUES === */
.card {
    background: #fff !important;
    border: 0.5px solid #ede8df !important;
    border-radius: 14px !important;
    box-shadow: none !important;
}
.card-header { border-bottom: 0.5px solid #ede8df !important; }
.card-title { font-family: Georgia, serif !important; font-weight: 400 !important; color: #1e1208 !important; font-size: 16px !important; }

/* Badges carte */
.card-badge { background: #f5f1eb !important; color: #6b5c4e !important; font-size: 11px !important; font-family: 'Manrope', sans-serif !important; border-radius: 8px !important; }
.card-badge.warning { background: #fef9e7 !important; color: #7d5a00 !important; }
.card-badge.danger { background: #fce5df !important; color: #993c1d !important; }
.card-badge.success { background: #e4f1d8 !important; color: #2d6a35 !important; }

/* === ITEMS DE LISTE === */
.list-item { background: #faf9f5 !important; border-radius: 10px !important; }
.list-item:hover { background: #f0ece4 !important; transform: none !important; }

/* Avatar placeholder : remplace le dégradé bleu */
.list-item-avatar-placeholder { background: #ede9fe !important; color: #534ab7 !important; border-radius: 10px !important; font-weight: 500 !important; }

/* Sous-titre item liste */
.list-item-subtitle { color: #a0907e !important; font-size: 11px !important; font-family: 'Manrope', sans-serif !important; }

/* === BOUTONS === */
.btn-primary {
    background: #7c6cf0 !important;
    color: #fff !important;
    box-shadow: none !important;
    border-radius: 8px !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    border: none !important;
}
.btn-primary:hover { background: #6558d4 !important; box-shadow: none !important; transform: none !important; }

.btn-success { background: #3a7d44 !important; border-radius: 8px !important; font-family: 'Manrope', sans-serif !important; font-size: 12px !important; }
.btn-success:hover { background: #2d6235 !important; }

.btn-danger { background: #993c1d !important; border-radius: 8px !important; font-family: 'Manrope', sans-serif !important; font-size: 12px !important; }
.btn-danger:hover { background: #7a2e14 !important; }

.btn-outline {
    background: #fff !important;
    color: #6b5c4e !important;
    border: 0.5px solid #ddd6c8 !important;
    border-radius: 8px !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 12px !important;
    box-shadow: none !important;
}
.btn-outline:hover { border-color: #7c6cf0 !important; color: #7c6cf0 !important; }

/* === TABLEAU COMMANDES RÉCENTES === */
thead tr { border-bottom: 0.5px solid #ede8df !important; }
th { color: #a0907e !important; font-size: 11px !important; font-family: 'Manrope', sans-serif !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; font-weight: 500 !important; }
tbody tr { border-bottom: 0.5px solid #faf9f5 !important; }
tbody tr:hover { background: #faf9f5 !important; }
td { font-family: 'Manrope', sans-serif !important; font-size: 12px !important; }

/* Lien n° commande */
a[style*="color: var(--primary)"] { color: #7c6cf0 !important; }
/* Montant commande */
td[style*="font-weight: 600"] { font-family: Georgia, serif !important; font-weight: 400 !important; color: #1e1208 !important; }
/* Date commande */
td[style*="color: #64748b"] { color: #a0907e !important; }

/* === GRILLE CHART.JS === */
/* Les canvas Chart.js restent inchangés, juste le fond de la card */
.chart-container { background: transparent !important; }
</style>
