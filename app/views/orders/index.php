<?php
/**
 * MARKETFLOW PRO - LISTE DES COMMANDES
 * Fichier : app/views/orders/index.php
 */
?>

<div class="container mt-8 mb-16">
    
    <!-- Header -->
    <div class="mb-8">
        <h1>Mes Commandes</h1>
        <p style="color: var(--text-secondary); margin-top: var(--space-2);">
            Retrouvez toutes vos commandes et téléchargez vos produits
        </p>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-3 mb-8">
        
        <div class="card" style="padding: var(--space-6);">
            <div style="display: flex; align-items: center; gap: var(--space-4);">
                <div style="
                    width: 60px;
                    height: 60px;
                    background: var(--primary-100);
                    border-radius: var(--radius-lg);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.75rem;
                ">
                    📦
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-tertiary); margin-bottom: var(--space-1);">
                        Total commandes
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--primary-600);">
                        <?= number_format($stats['total_orders']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="padding: var(--space-6);">
            <div style="display: flex; align-items: center; gap: var(--space-4);">
                <div style="
                    width: 60px;
                    height: 60px;
                    background: var(--success-light);
                    border-radius: var(--radius-lg);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.75rem;
                ">
                    💰
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-tertiary); margin-bottom: var(--space-1);">
                        Total dépensé
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--success);">
                        <?= formatPrice($stats['total_spent']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="padding: var(--space-6);">
            <div style="display: flex; align-items: center; gap: var(--space-4);">
                <div style="
                    width: 60px;
                    height: 60px;
                    background: var(--secondary-100);
                    border-radius: var(--radius-lg);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.75rem;
                ">
                    🎨
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-tertiary); margin-bottom: var(--space-1);">
                        Produits achetés
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--secondary-600);">
                        <?= number_format($stats['total_products']) ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php if (empty($orders)): ?>
        
        <!-- Aucune commande -->
        <div class="card text-center" style="padding: var(--space-16);">
            <div style="font-size: 5rem; margin-bottom: var(--space-6);">📦</div>
            <h2 style="margin-bottom: var(--space-4);">Aucune commande</h2>
            <p style="color: var(--text-secondary); margin-bottom: var(--space-8); font-size: 1.125rem;">
                Vous n'avez pas encore passé de commande
            </p>
            <a href="/products" class="btn btn-primary btn-lg">
                Découvrir les produits
            </a>
        </div>

    <?php else: ?>

        <!-- Liste des commandes -->
        <div style="display: flex; flex-direction: column; gap: var(--space-4);">
            
            <?php foreach ($orders as $order): ?>
            <div class="card" style="padding: var(--space-6); transition: all var(--transition);">
                
                <div style="display: grid; grid-template-columns: auto 1fr auto auto; gap: var(--space-6); align-items: center;">
                    
                    <!-- Icône statut -->
                    <div style="
                        width: 50px;
                        height: 50px;
                        border-radius: var(--radius-lg);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 1.5rem;
                        <?php if ($order['payment_status'] === 'completed'): ?>
                            background: var(--success-light);
                        <?php elseif ($order['payment_status'] === 'pending'): ?>
                            background: var(--warning-light);
                        <?php else: ?>
                            background: var(--error-light);
                        <?php endif; ?>
                    ">
                        <?php if ($order['payment_status'] === 'completed'): ?>
                            ✓
                        <?php elseif ($order['payment_status'] === 'pending'): ?>
                            ⏳
                        <?php else: ?>
                            ✕
                        <?php endif; ?>
                    </div>

                    <!-- Infos commande -->
                    <div>
                        <div style="display: flex; align-items: center; gap: var(--space-3); margin-bottom: var(--space-2);">
                            <h3 style="font-size: 1.125rem; margin: 0;">
                                Commande #<?= e($order['order_number']) ?>
                            </h3>
                            
                            <?php if ($order['payment_status'] === 'completed'): ?>
                                <span class="badge badge-success">Payée</span>
                            <?php elseif ($order['payment_status'] === 'pending'): ?>
                                <span class="badge badge-warning">En attente</span>
                            <?php elseif ($order['payment_status'] === 'failed'): ?>
                                <span class="badge badge-error">Échouée</span>
                            <?php elseif ($order['payment_status'] === 'refunded'): ?>
                                <span class="badge" style="background: var(--text-tertiary); color: white;">Remboursée</span>
                            <?php endif; ?>
                        </div>

                        <div style="display: flex; gap: var(--space-6); font-size: 0.875rem; color: var(--text-tertiary);">
                            <div>
                                📅 <?= date('d/m/Y', strtotime($order['created_at'])) ?>
                            </div>
                            <div>
                                📦 <?= $order['items_count'] ?> article<?= $order['items_count'] > 1 ? 's' : '' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Montant -->
                    <div style="text-align: right;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary-600);">
                            <?= formatPrice($order['total_amount']) ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div>
                        <a 
                            href="/orders/<?= e($order['order_number']) ?>" 
                            class="btn btn-primary btn-sm"
                        >
                            Voir détails →
                        </a>
                    </div>

                </div>

            </div>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<!-- JavaScript -->
<script>
// Animation au chargement
document.querySelectorAll('.card').forEach((card, index) => {
    card.style.animation = `fadeIn 0.5s ease-out ${index * 0.05}s both`;
});

// Hover effect sur les commandes
document.querySelectorAll('.card[style*="transition"]').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateX(4px)';
        this.style.boxShadow = 'var(--shadow-lg)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateX(0)';
        this.style.boxShadow = 'var(--shadow)';
    });
});
</script>

<style>
/* Responsive */
@media (max-width: 1024px) {
    .grid-3 {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 768px) {
    [style*="grid-template-columns: auto 1fr auto auto"] {
        grid-template-columns: 1fr !important;
        gap: var(--space-4) !important;
    }
    
    [style*="text-align: right"] {
        text-align: left !important;
    }
}
</style>