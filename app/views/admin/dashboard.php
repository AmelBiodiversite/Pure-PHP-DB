<?php
/**
 * MARKETFLOW PRO - DASHBOARD ADMIN
 * Fichier : app/views/admin/dashboard.php
 */

// Données passées par le contrôleur
$stats = $stats ?? [];
$recentUsers = $recent_users ?? [];
$pendingProducts = $pending_products ?? [];
$recentOrders = $recent_orders ?? [];
?>

<div class="container mt-8 mb-16">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="mb-2">👑 Administration</h1>
        <p style="color: var(--text-secondary);">Tableau de bord administrateur</p>
    </div>

    <!-- Stats Cards -->
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Produits en attente -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="margin: 0;">⏳ Produits en attente</h2>
                <span class="badge badge-warning"><?= count($pendingProducts) ?></span>
            </div>

            <!-- Boutons Export CSV -->
            <div class="card mb-8">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <div>
                        <h2 style="margin: 0 0 0.5rem 0;">📊 Exports de données</h2>
                        <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">
                            Téléchargez vos données au format CSV compatible Excel
                        </p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <a href="/admin/export/users" class="btn btn-outline hover-lift" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <span style="font-size: 1.25rem;">👥</span>
                        <span>Utilisateurs</span>
                    </a>
                    <a href="/admin/export/products" class="btn btn-outline hover-lift" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <span style="font-size: 1.25rem;">📦</span>
                        <span>Produits</span>
                    </a>
                    <a href="/admin/export/orders" class="btn btn-outline hover-lift" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <span style="font-size: 1.25rem;">🛍️</span>
                        <span>Commandes</span>
                    </a>
                </div>
            </div>


<!-- Monitoring de Sécurité -->
    <div class="card mb-8">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 style="margin: 0 0 0.5rem 0;">🔒 Monitoring de Sécurité</h2>
                <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">
                    Surveillez les événements de sécurité en temps réel
                </p>
            </div>
            <?php
            // Récupérer le nombre d'événements critiques des 24 dernières heures
            // Utilise les helpers getSecurityStats() et getSecurityAlerts()
            $todayStats = getSecurityStats(1);
            $criticalToday = ($todayStats['LOGIN_BLOCKED'] ?? 0) + 
                             ($todayStats['CSRF_VIOLATION'] ?? 0) + 
                             ($todayStats['XSS_ATTEMPT'] ?? 0) + 
                             ($todayStats['SQLI_ATTEMPT'] ?? 0);
            ?>
            <?php if ($criticalToday > 0): ?>
                <span class="badge badge-danger" style="font-size: 1rem; padding: 0.5rem 1rem;">
                    <?= $criticalToday ?> alerte<?= $criticalToday > 1 ? 's' : '' ?>
                </span>
            <?php else: ?>
                <span class="badge" style="background: #43e97b; color: white; font-size: 1rem; padding: 0.5rem 1rem;">
                    ✓ Aucune alerte
                </span>
            <?php endif; ?>
        </div>

        <!-- Stats rapides de sécurité -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div style="padding: 1rem; background: var(--bg-secondary); border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 2rem;">📊</span>
                    <div>
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0 0 0.25rem 0;">
                            Événements (7j)
                        </p>
                        <h3 style="margin: 0; font-size: 1.5rem;">
                            <?= array_sum(getSecurityStats(7)) ?>
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
                            <?= $todayStats['LOGIN_FAILED'] ?? 0 ?>
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
                        <h3 style="margin: 0; font-size: 1.5rem; color: #f5576c;">
                            <?= $criticalToday ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bouton d'accès au dashboard sécurité -->
        <a href="/admin/security" class="btn btn-primary hover-lift" style="display: inline-flex; align-items: center; gap: 0.75rem; font-size: 1rem; padding: 1rem 2rem;">
            <span style="font-size: 1.5rem;">🔒</span>
            <span>Accéder au Monitoring Complet</span>
            <span style="margin-left: auto;">→</span>
        </a>
    </div>



            

            <?php if (empty($pendingProducts)): ?>
                <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                    <p>✅ Aucun produit en attente</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($pendingProducts as $product): ?>
                        <div class="hover-lift" style="padding: 1rem; background: var(--bg-secondary); border-radius: 8px; display: flex; gap: 1rem;">
                            <?php if ($product['thumbnail_url']): ?>
                                <img src="<?= htmlspecialchars($product['thumbnail_url']) ?>" 
                                     alt="<?= htmlspecialchars($product['title']) ?>"
                                     style="width: 64px; height: 64px; object-fit: cover; border-radius: 8px;">
                            <?php else: ?>
                                <div style="width: 64px; height: 64px; background: var(--primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                    📦
                                </div>
                            <?php endif; ?>
                            
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 0.5rem 0;"><?= htmlspecialchars($product['title']) ?></h4>
                                <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">
                                    Par <?= htmlspecialchars($product['seller_name']) ?>
                                </p>
                            </div>

                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <a href="/admin/products/approve/<?= e($product['id']) ?>" class="btn btn-success btn-sm">
                                    ✓ Approuver
                                </a>
                                <a href="/admin/products/reject/<?= e($product['id']) ?>" class="btn btn-danger btn-sm">
                                    ✗ Rejeter
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

        

        <!-- Monitoring de Sécurité -->
        <div class="card mb-8">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h2 style="margin: 0 0 0.5rem 0;">🔒 Monitoring de Sécurité</h2>
                    <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">
                        Surveillez les événements de sécurité en temps réel
                    </p>
                </div>
                <?php
                // Récupérer le nombre d'événements critiques des 24 dernières heures
                // Utilise les helpers getSecurityStats() et getSecurityAlerts()
                $todayStats = getSecurityStats(1);
                $criticalToday = ($todayStats['LOGIN_BLOCKED'] ?? 0) + 
                                 ($todayStats['CSRF_VIOLATION'] ?? 0) + 
                                 ($todayStats['XSS_ATTEMPT'] ?? 0) + 
                                 ($todayStats['SQLI_ATTEMPT'] ?? 0);
                ?>
                <?php if ($criticalToday > 0): ?>
                    <span class="badge badge-danger" style="font-size: 1rem; padding: 0.5rem 1rem;">
                        <?= $criticalToday ?> alerte<?= $criticalToday > 1 ? 's' : '' ?>
                    </span>
                <?php else: ?>
                    <span class="badge badge-success" style="background: #43e97b; color: white; font-size: 1rem; padding: 0.5rem 1rem;">
                        ✓ Aucune alerte
                    </span>
                <?php endif; ?>
            </div>

            <!-- Stats rapides de sécurité -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div style="padding: 1rem; background: var(--bg-secondary); border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 2rem;">📊</span>
                        <div>
                            <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0 0 0.25rem 0;">
                                Événements (7j)
                            </p>
                            <h3 style="margin: 0; font-size: 1.5rem;">
                                <?= array_sum(getSecurityStats(7)) ?>
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
                                <?= $todayStats['LOGIN_FAILED'] ?? 0 ?>
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
                            <h3 style="margin: 0; font-size: 1.5rem; color: #f5576c;">
                                <?= $criticalToday ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bouton d'accès au dashboard sécurité -->
            <a href="/admin/security" class="btn btn-primary hover-lift" style="display: inline-flex; align-items: center; gap: 0.75rem; font-size: 1rem; padding: 1rem 2rem;">
                <span style="font-size: 1.5rem;">🔒</span>
                <span>Accéder au Monitoring Complet</span>
                <span style="margin-left: auto;">→</span>
            </a>
        </div>


        
        
        <!-- Utilisateurs récents -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="margin: 0;">👥 Utilisateurs récents</h2>
                <span class="badge badge-primary"><?= count($recentUsers) ?></span>
            </div>

            <?php if (empty($recentUsers)): ?>
                <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                    <p>Aucun utilisateur récent</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($recentUsers as $user): ?>
                        <div class="hover-lift" style="padding: 1rem; background: var(--bg-secondary); border-radius: 8px; display: flex; gap: 1rem; align-items: center;">
                            <?php if ($user['avatar_url']): ?>
                                <img src="<?= htmlspecialchars($user['avatar_url']) ?>" 
                                     alt="<?= htmlspecialchars($user['full_name']) ?>"
                                     style="width: 48px; height: 48px; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                <div style="width: 48px; height: 48px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    <?= strtoupper(substr($user['full_name'] ?? $user['username'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 0.25rem 0;"><?= strtoupper(substr($user['full_name'] ?? $user['username'], 0, 1)) ?>
                                <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">
                                    @<?= htmlspecialchars($user['username']) ?>
                                </p>
                            </div>

                            <span class="badge badge-<?= $user['role'] === 'seller' ? 'primary' : 'secondary' ?>">
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

    <!-- Commandes récentes -->
    <div class="card mt-8">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="margin: 0;">🛍️ Commandes récentes</h2>
            <span class="badge badge-success"><?= count($recentOrders) ?></span>
        </div>

        <?php if (empty($recentOrders)): ?>
            <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                <p>Aucune commande récente</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <th style="padding: 1rem; text-align: left;">N° Commande</th>
                            <th style="padding: 1rem; text-align: left;">Client</th>
                            <th style="padding: 1rem; text-align: left;">Montant</th>
                            <th style="padding: 1rem; text-align: left;">Statut</th>
                            <th style="padding: 1rem; text-align: left;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr style="border-bottom: 1px solid var(--border);" class="hover-lift">
                                <td style="padding: 1rem;">
                                    <a href="/admin/orders/<?= e($order['order_number']) ?>" 
                                       style="color: var(--primary); text-decoration: none; font-weight: 600;">
                                        #<?= htmlspecialchars($order['order_number']) ?>
                                    </a>
                                </td>
                                <td style="padding: 1rem;"><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td style="padding: 1rem; font-weight: 600;"><?= number_format($order['total_amount'], 2) ?>€</td>
                                <td style="padding: 1rem;">
                                    <span class="badge badge-<?= $order['status'] === 'completed' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; color: var(--text-secondary);">
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

    <!-- Actions rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <a href="/admin/products" class="card hover-lift" style="text-decoration: none; color: inherit;">
            <div style="text-align: center; padding: 2rem;">
                <div style="font-size: 48px; margin-bottom: 1rem;">📦</div>
                <h3 style="margin: 0 0 0.5rem 0;">Gérer les produits</h3>
                <p style="color: var(--text-secondary); margin: 0;">Valider, rejeter ou modifier</p>
            </div>
        </a>

        <a href="/admin/users" class="card hover-lift" style="text-decoration: none; color: inherit;">
            <div style="text-align: center; padding: 2rem;">
                <div style="font-size: 48px; margin-bottom: 1rem;">👥</div>
                <h3 style="margin: 0 0 0.5rem 0;">Gérer les utilisateurs</h3>
                <p style="color: var(--text-secondary); margin: 0;">Activer, suspendre ou supprimer</p>
            </div>
        </a>

        <a href="/admin/settings" class="card hover-lift" style="text-decoration: none; color: inherit;">
            <div style="text-align: center; padding: 2rem;">
                <div style="font-size: 48px; margin-bottom: 1rem;">⚙️</div>
                <h3 style="margin: 0 0 0.5rem 0;">Paramètres</h3>
                <p style="color: var(--text-secondary); margin: 0;">Configuration du site</p>
            </div>
        </a>
    </div>
</div>

<style>
.hover-lift {
    transition: transform 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
}

table tr:hover {
    background: var(--bg-secondary);
}
</style>