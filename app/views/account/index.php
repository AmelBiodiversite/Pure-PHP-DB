<?php
/**
 * Dashboard compte utilisateur
 */
$isBuyer = $user['role'] === 'buyer';
?>

<div class="container mt-8 mb-16">
    <div class="row" style="gap: var(--space-8);">
        
        <!-- Sidebar -->
        <div class="col-12 col-md-3">
            <div class="card" style="position: sticky; top: 100px;">
                <div style="text-align: center; padding: var(--space-6); border-bottom: 1px solid var(--border-color);">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto var(--space-4);">
                        <?= strtoupper(substr($user['username'], 0, 2)) ?>
                    </div>
                    <h3 style="margin: 0 0 var(--space-2);"><?= e($user['username']) ?></h3>
                    <p style="color: var(--text-tertiary); font-size: 0.875rem; margin: 0;">
                        <?= $isBuyer ? '🛍️ Acheteur' : '💼 Vendeur' ?>
                    </p>
                </div>
                
                <nav style="padding: var(--space-4);">
                    <a href="/account" class="menu-item active">
                        📊 Tableau de bord
                    </a>
                    <?php if ($isBuyer): ?>
                    <a href="/account/downloads" class="menu-item">
                        📥 Mes téléchargements
                    </a>
                    <?php else: ?>
                    <a href="/seller/dashboard" class="menu-item">
                        💼 Espace vendeur
                    </a>
                    <?php endif; ?>
                    <a href="/account/settings" class="menu-item">
                        ⚙️ Paramètres
                    </a>
                    <a href="/logout" class="menu-item" style="color: var(--danger);">
                        🚪 Déconnexion
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Contenu principal -->
        <div class="col-12 col-md-9">
            <h1 style="margin-bottom: var(--space-6);">
                Bienvenue, <?= e($user['username']) ?> ! 👋
            </h1>
            
            <?php if ($isBuyer): ?>
            <!-- Stats acheteur -->
            <div class="row" style="gap: var(--space-6); margin-bottom: var(--space-8);">
                <div class="col-12 col-md-6">
                    <div class="card" style="text-align: center; padding: var(--space-8);">
                        <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: var(--space-4);">
                            <?= $stats['total_orders'] ?? 0 ?>
                        </div>
                        <h3 style="margin: 0; font-size: 1.125rem; color: var(--text-secondary);">
                            Commandes
                        </h3>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card" style="text-align: center; padding: var(--space-8);">
                        <div style="font-size: 2.5rem; color: var(--success); margin-bottom: var(--space-4);">
                            <?= formatPrice($stats['total_spent'] ?? 0) ?>
                        </div>
                        <h3 style="margin: 0; font-size: 1.125rem; color: var(--text-secondary);">
                            Total dépensé
                        </h3>
                    </div>
                </div>
            </div>
            
            <!-- Dernières commandes -->
            <div class="card">
                <div style="padding: var(--space-6); border-bottom: 1px solid var(--border-color);">
                    <h2 style="margin: 0;">Dernières commandes</h2>
                </div>
                
                <?php if (empty($orders)): ?>
                <div style="padding: var(--space-12); text-align: center;">
                    <p style="font-size: 3rem; margin-bottom: var(--space-4);">🛍️</p>
                    <p style="color: var(--text-secondary); margin-bottom: var(--space-6);">
                        Vous n'avez pas encore passé de commande.
                    </p>
                    <a href="/products" class="btn btn-primary">
                        Découvrir les produits
                    </a>
                </div>
                <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <th style="padding: var(--space-4); text-align: left;">Date</th>
                                <th style="padding: var(--space-4); text-align: left;">Référence</th>
                                <th style="padding: var(--space-4); text-align: center;">Articles</th>
                                <th style="padding: var(--space-4); text-align: right;">Montant</th>
                                <th style="padding: var(--space-4); text-align: center;">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: var(--space-4);">
                                    <?= date('d/m/Y', strtotime($order['created_at'])) ?>
                                </td>
                                <td style="padding: var(--space-4); font-family: monospace; font-size: 0.875rem;">
                                    #<?= substr($order['id'], 0, 8) ?>
                                </td>
                                <td style="padding: var(--space-4); text-align: center;">
                                    <?= $order['items_count'] ?>
                                </td>
                                <td style="padding: var(--space-4); text-align: right; font-weight: 600;">
                                    <?= formatPrice($order['total_amount']) ?>
                                </td>
                                <td style="padding: var(--space-4); text-align: center;">
                                    <span class="badge badge-<?= $order['payment_status'] === 'completed' ? 'success' : 'warning' ?>">
                                        <?= $order['payment_status'] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            
            <?php else: ?>
            <!-- Vendeur -->
            <div class="card" style="padding: var(--space-12); text-align: center;">
                <p style="font-size: 3rem; margin-bottom: var(--space-4);">💼</p>
                <h2 style="margin-bottom: var(--space-4);">Espace Vendeur</h2>
                <p style="color: var(--text-secondary); margin-bottom: var(--space-6);">
                    Gérez vos produits, suivez vos ventes et analysez vos performances.
                </p>
                <a href="/seller/dashboard" class="btn btn-primary btn-lg">
                    Accéder au dashboard vendeur
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.menu-item {
    display: block;
    padding: var(--space-3) var(--space-4);
    color: var(--text-primary);
    text-decoration: none;
    border-radius: var(--radius);
    transition: all 0.2s;
    margin-bottom: var(--space-2);
}

.menu-item:hover {
    background: var(--bg-secondary);
}

.menu-item.active {
    background: var(--primary);
    color: white;
}
</style>