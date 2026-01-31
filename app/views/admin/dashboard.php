<?php
/**
 * MARKETFLOW PRO - DASHBOARD ADMIN
 * Fichier : app/views/admin/dashboard.php
 * Version optimisée UX - Navigation claire et organisation logique
 */

// Données passées par le contrôleur
$stats = $stats ?? [];
$recentUsers = $recent_users ?? [];
$pendingProducts = $pending_products ?? [];
$recentOrders = $recent_orders ?? [];

// Calcul des alertes de sécurité pour le badge de notification
$todayStats = getSecurityStats(1);
$criticalToday = ($todayStats['LOGIN_BLOCKED'] ?? 0) + 
                 ($todayStats['CSRF_VIOLATION'] ?? 0) + 
                 ($todayStats['XSS_ATTEMPT'] ?? 0) + 
                 ($todayStats['SQLI_ATTEMPT'] ?? 0);
?>

<div class="container mt-8 mb-16">
    
    <!-- ========== HEADER AVEC NAVIGATION RAPIDE ========== -->
    <div class="mb-8" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <h1 class="mb-2">👑 Administration</h1>
            <p style="color: var(--text-secondary);">Tableau de bord administrateur</p>
        </div>
        
        <!-- Navigation rapide sous forme de pills -->
        <nav style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="/admin" class="btn btn-primary btn-sm" style="border-radius: 20px;">
                📊 Dashboard
            </a>
            <a href="/admin/products" class="btn btn-outline btn-sm" style="border-radius: 20px;">
                📦 Produits
            </a>
            <a href="/admin/users" class="btn btn-outline btn-sm" style="border-radius: 20px;">
                👥 Utilisateurs
            </a>
            <a href="/admin/security" class="btn btn-outline btn-sm" style="border-radius: 20px; position: relative;">
                🔒 Sécurité
                <?php if ($criticalToday > 0): ?>
                    <span style="position: absolute; top: -8px; right: -8px; background: #f5576c; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 0.75rem; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                        <?= $criticalToday ?>
                    </span>
                <?php endif; ?>
            </a>
        </nav>
    </div>

    <!-- ========== STATISTIQUES PRINCIPALES ========== -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="card hover-lift">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    👥
                </div>
                <div style="flex: 1;">
                    <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.25rem;">Utilisateurs</p>
                    <h3 style="margin: 0; font-size: 1.75rem;"><?= number_format($stats['total_users'] ?? 0) ?></h3>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="card hover-lift">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    📦
                </div>
                <div style="flex: 1;">
                    <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.25rem;">Produits</p>
                    <h3 style="margin: 0; font-size: 1.75rem;"><?= number_format($stats['total_products'] ?? 0) ?></h3>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="card hover-lift">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    🛍️
                </div>
                <div style="flex: 1;">
                    <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.25rem;">Commandes</p>
                    <h3 style="margin: 0; font-size: 1.75rem;"><?= number_format($stats['total_orders'] ?? 0) ?></h3>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="card hover-lift">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    💰
                </div>
                <div style="flex: 1;">
                    <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.25rem;">Revenus Total</p>
                    <h3 style="margin: 0; font-size: 1.75rem;"><?= number_format($stats['total_revenue'] ?? 0, 2) ?>€</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== ALERTES ET ACTIONS PRIORITAIRES ========== -->
    <?php if (!empty($pendingProducts) || $criticalToday > 0): ?>
    <div class="mb-8" style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); border-left: 4px solid var(--primary); border-radius: 8px; padding: 1.5rem;">
        <h3 style="margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
            ⚠️ Actions requises
        </h3>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
            <?php if (!empty($pendingProducts)): ?>
                <a href="#pending-products" class="btn btn-warning" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    <span><?= count($pendingProducts) ?></span>
                    <span>produit<?= count($pendingProducts) > 1 ? 's' : '' ?> à valider</span>
                </a>
            <?php endif; ?>
            <?php if ($criticalToday > 0): ?>
                <a href="/admin/security" class="btn btn-danger" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    <span><?= $criticalToday ?></span>
                    <span>alerte<?= $criticalToday > 1 ? 's' : '' ?> de sécurité</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ========== GRILLE PRINCIPALE : CONTENU RÉCENT ========== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <!-- Produits en attente de validation -->
        <div class="card" id="pending-products">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="margin: 0;">⏳ Produits en attente</h2>
                <span class="badge badge-warning"><?= count($pendingProducts) ?></span>
            </div>

            <?php if (empty($pendingProducts)): ?>
                <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
                    <p style="margin: 0;">Aucun produit en attente</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($pendingProducts as $product): ?>
                        <div class="hover-lift" style="padding: 1rem; background: var(--bg-secondary); border-radius: 8px; display: flex; gap: 1rem;">
                            <!-- Thumbnail du produit -->
                            <?php if ($product['thumbnail_url']): ?>
                                <img src="<?= htmlspecialchars($product['thumbnail_url']) ?>" 
                                     alt="<?= htmlspecialchars($product['title']) ?>"
                                     style="width: 64px; height: 64px; object-fit: cover; border-radius: 8px; flex-shrink: 0;">
                            <?php else: ?>
                                <div style="width: 64px; height: 64px; background: var(--primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
                                    📦
                                </div>
                            <?php endif; ?>
                            
                            <!-- Informations du produit -->
                            <div style="flex: 1; min-width: 0;">
                                <h4 style="margin: 0 0 0.5rem 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars($product['title']) ?>
                                </h4>
                                <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">
                                    Par <?= htmlspecialchars($product['seller_name']) ?>
                                </p>
                            </div>

                            <!-- Actions de validation -->
                            <div style="display: flex; gap: 0.5rem; align-items: center; flex-shrink: 0;">
                                <a href="/admin/products/approve/<?= e($product['id']) ?>" 
                                   class="btn btn-success btn-sm" 
                                   title="Approuver ce produit">
                                    ✓
                                </a>
                                <a href="/admin/products/reject/<?= e($product['id']) ?>" 
                                   class="btn btn-danger btn-sm"
                                   title="Rejeter ce produit">
                                    ✗
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top: 1rem; text-align: center;">
                    <a href="/admin/products" class="btn btn-secondary">Voir tous les produits →</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Utilisateurs récents -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="margin: 0;">👥 Utilisateurs récents</h2>
                <span class="badge badge-primary"><?= count($recentUsers) ?></span>
            </div>

            <?php if (empty($recentUsers)): ?>
                <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">👤</div>
                    <p style="margin: 0;">Aucun utilisateur récent</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($recentUsers as $user): ?>
                        <div class="hover-lift" style="padding: 1rem; background: var(--bg-secondary); border-radius: 8px; display: flex; gap: 1rem; align-items: center;">
                            <!-- Avatar utilisateur -->
                            <?php if ($user['avatar_url']): ?>
                                <img src="<?= htmlspecialchars($user['avatar_url']) ?>" 
                                     alt="<?= htmlspecialchars($user['full_name'] ?? $user['username']) ?>"
                                     style="width: 48px; height: 48px; object-fit: cover; border-radius: 50%; flex-shrink: 0;">
                            <?php else: ?>
                                <div style="width: 48px; height: 48px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0;">
                                    <?= strtoupper(substr($user['full_name'] ?? $user['username'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Informations utilisateur -->
                            <div style="flex: 1; min-width: 0;">
                                <h4 style="margin: 0 0 0.25rem 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars($user['full_name'] ?? $user['username']) ?>
                                </h4>
                                <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">
                                    @<?= htmlspecialchars($user['username']) ?>
                                </p>
                            </div>

                            <!-- Badge rôle -->
                            <span class="badge badge-<?= $user['role'] === 'seller' ? 'primary' : ($user['role'] === 'admin' ? 'danger' : 'secondary') ?>" style="flex-shrink: 0;">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top: 1rem; text-align: center;">
                    <a href="/admin/users" class="btn btn-secondary">Voir tous les utilisateurs →</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ========== MONITORING DE SÉCURITÉ ========== -->
    <div class="card mb-8">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="margin: 0 0 0.5rem 0;">🔒 Monitoring de Sécurité</h2>
                <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">
                    Surveillez les événements de sécurité en temps réel
                </p>
            </div>
            
            <!-- Badge d'alerte -->
            <?php if ($criticalToday > 0): ?>
                <span class="badge badge-danger" style="font-size: 1rem; padding: 0.5rem 1rem;">
                    🚨 <?= $criticalToday ?> alerte<?= $criticalToday > 1 ? 's' : '' ?>
                </span>
            <?php else: ?>
                <span class="badge" style="background: #43e97b; color: white; font-size: 1rem; padding: 0.5rem 1rem;">
                    ✓ Système sécurisé
                </span>
            <?php endif; ?>
        </div>

        <!-- Statistiques de sécurité -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div style="padding: 1rem; background: var(--bg-secondary); border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 2rem;">📊</span>
                    <div>
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0 0 0.25rem 0;">
                            Événements (7j)
                        </p>
                        <h3 style="margin: 0; font-size: 1.5rem;">
                            <?= number_format(array_sum(getSecurityStats(7))) ?>
                        </h3>
                    </div>
                </div>
            </div>

            <div style="padding: 1rem; background: var(--bg-secondary); border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 2rem;">⚠️</span>
                    <div>
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0 0 0.25rem 0;">
                            Tentatives échouées
                        </p>
                        <h3 style="margin: 0; font-size: 1.5rem;">
                            <?= number_format($todayStats['LOGIN_FAILED'] ?? 0) ?>
                        </h3>
                    </div>
                </div>
            </div>

            <div style="padding: 1rem; background: var(--bg-secondary); border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 2rem;">🚨</span>
                    <div>
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0 0 0.25rem 0;">
                            Alertes critiques
                        </p>
                        <h3 style="margin: 0; font-size: 1.5rem; color: <?= $criticalToday > 0 ? '#f5576c' : '#43e97b' ?>;">
                            <?= number_format($criticalToday) ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bouton d'accès au dashboard sécurité complet -->
        <a href="/admin/security" class="btn btn-primary hover-lift" style="display: inline-flex; align-items: center; gap: 0.75rem; font-size: 1rem; padding: 1rem 2rem; width: 100%; justify-content: center;">
            <span style="font-size: 1.5rem;">🔒</span>
            <span>Accéder au Monitoring Complet</span>
            <span style="margin-left: auto;">→</span>
        </a>
    </div>

    <!-- ========== COMMANDES RÉCENTES ========== -->
    <div class="card mb-8">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="margin: 0;">🛍️ Commandes récentes</h2>
            <span class="badge badge-success"><?= count($recentOrders) ?></span>
        </div>

        <?php if (empty($recentOrders)): ?>
            <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📦</div>
                <p style="margin: 0;">Aucune commande récente</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border);">
                            <th style="padding: 1rem; text-align: left; font-weight: 600;">N° Commande</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600;">Client</th>
                            <th style="padding: 1rem; text-align: right; font-weight: 600;">Montant</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600;">Statut</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s;" class="hover-row">
                                <td style="padding: 1rem;">
                                    <a href="/admin/orders/<?= e($order['order_number']) ?>" 
                                       style="color: var(--primary); text-decoration: none; font-weight: 600;">
                                        #<?= htmlspecialchars($order['order_number']) ?>
                                    </a>
                                </td>
                                <td style="padding: 1rem;"><?= htmlspecialchars($order['customer_name'] ?? $order['buyer_name']) ?></td>
                                <td style="padding: 1rem; font-weight: 600; text-align: right;"><?= number_format($order['total_amount'], 2) ?>€</td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span class="badge badge-<?= $order['status'] === 'completed' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'secondary') ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; color: var(--text-secondary); font-size: 0.875rem;">
                                    <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1rem; text-align: center;">
                <a href="/admin/orders" class="btn btn-secondary">Voir toutes les commandes →</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- ========== EXPORTS ET OUTILS ========== -->
    <div class="card mb-8">
        <div style="margin-bottom: 1.5rem;">
            <h2 style="margin: 0 0 0.5rem 0;">📊 Exports de données</h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">
                Téléchargez vos données au format CSV compatible Excel
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <a href="/admin/export/users" class="btn btn-outline hover-lift" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 1rem;">
                <span style="font-size: 1.25rem;">👥</span>
                <span>Utilisateurs</span>
            </a>
            <a href="/admin/export/products" class="btn btn-outline hover-lift" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 1rem;">
                <span style="font-size: 1.25rem;">📦</span>
                <span>Produits</span>
            </a>
            <a href="/admin/export/orders" class="btn btn-outline hover-lift" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 1rem;">
                <span style="font-size: 1.25rem;">🛍️</span>
                <span>Commandes</span>
            </a>
        </div>
    </div>

    <!-- ========== ACTIONS RAPIDES ========== -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="/admin/products" class="card hover-lift" style="text-decoration: none; color: inherit; transition: all 0.3s;">
            <div style="text-align: center; padding: 2rem;">
                <div style="font-size: 48px; margin-bottom: 1rem;">📦</div>
                <h3 style="margin: 0 0 0.5rem 0;">Gérer les produits</h3>
                <p style="color: var(--text-secondary); margin: 0; font-size: 0.875rem;">Valider, rejeter ou modifier</p>
            </div>
        </a>

        <a href="/admin/users" class="card hover-lift" style="text-decoration: none; color: inherit; transition: all 0.3s;">
            <div style="text-align: center; padding: 2rem;">
                <div style="font-size: 48px; margin-bottom: 1rem;">👥</div>
                <h3 style="margin: 0 0 0.5rem 0;">Gérer les utilisateurs</h3>
                <p style="color: var(--text-secondary); margin: 0; font-size: 0.875rem;">Activer, suspendre ou supprimer</p>
            </div>
        </a>

        <a href="/admin/settings" class="card hover-lift" style="text-decoration: none; color: inherit; transition: all 0.3s;">
            <div style="text-align: center; padding: 2rem;">
                <div style="font-size: 48px; margin-bottom: 1rem;">⚙️</div>
                <h3 style="margin: 0 0 0.5rem 0;">Paramètres</h3>
                <p style="color: var(--text-secondary); margin: 0; font-size: 0.875rem;">Configuration du site</p>
            </div>
        </a>
    </div>
</div>

<!-- ========== STYLES PERSONNALISÉS ========== -->
<style>
/* Animation au survol des cartes */
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

/* Effet hover sur les lignes de tableau */
.hover-row:hover {
    background: var(--bg-secondary);
}

/* Smooth scroll pour les ancres */
html {
    scroll-behavior: smooth;
}

/* Responsive : ajustement de la navigation sur mobile */
@media (max-width: 768px) {
    nav {
        width: 100%;
    }
    
    nav a {
        flex: 1;
        justify-content: center;
    }
}
</style>
