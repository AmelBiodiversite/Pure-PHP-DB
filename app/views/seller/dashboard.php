<?php
/**
 * MARKETFLOW PRO - DASHBOARD VENDEUR
 * Fichier : app/views/seller/dashboard.php
 */
?>

<!-- Chart.js pour les graphiques -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="container mt-8 mb-16">
    
    <!-- Header -->
    <div class="mb-8">
        <h1>Dashboard Vendeur</h1>
        <p style="color: var(--text-secondary); margin-top: var(--space-2);">
            Vue d'ensemble de vos performances
        </p>
    </div>

    <div class="flex-between mb-8">
        <h1>Dashboard Vendeur</h1>
        <a href="/seller/products/create" class="btn btn-primary">
            + Ajouter un produit
        </a>
    </div>

    <!-- Stats principales -->
    <div class="grid grid-4 mb-8">
        
        <!-- Revenus totaux -->
        <div class="card" style="padding: var(--space-6); background: var(--gradient-primary); color: white;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--space-4);">
                <div style="
                    width: 50px;
                    height: 50px;
                    background: rgba(255,255,255,0.2);
                    border-radius: var(--radius-lg);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.5rem;
                ">
                    💰
                </div>
                <span style="font-size: 0.875rem; opacity: 0.9;">Total</span>
            </div>
            <div style="font-size: 2rem; font-weight: 700; margin-bottom: var(--space-1);">
                <?= formatPrice($stats['total_sales'] ?? 0) ?>
            </div>
            <div style="font-size: 0.875rem; opacity: 0.8;">
                Revenus générés
            </div>
        </div>

        <!-- Ventes -->
        <div class="card" style="padding: var(--space-6);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--space-4);">
                <div style="
                    width: 50px;
                    height: 50px;
                    background: var(--success-light);
                    border-radius: var(--radius-lg);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.5rem;
                ">
                    📦
                </div>
                <span style="font-size: 0.875rem; color: var(--text-tertiary);">30j</span>
            </div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--success); margin-bottom: var(--space-1);">
                <?= number_format(count($recent_sales)) ?>
            </div>
            <div style="font-size: 0.875rem; color: var(--text-secondary);">
                Ventes récentes
            </div>
        </div>

        <!-- Produits -->
        <div class="card" style="padding: var(--space-6);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--space-4);">
                <div style="
                    width: 50px;
                    height: 50px;
                    background: var(--primary-100);
                    border-radius: var(--radius-lg);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.5rem;
                ">
                    🎨
                </div>
                <a href="/seller/products" style="font-size: 0.875rem; color: var(--primary-600);">Voir →</a>
            </div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--primary-600); margin-bottom: var(--space-1);">
                <?= $stats['total_products'] ?? 0 ?>
            </div>
            <div style="font-size: 0.875rem; color: var(--text-secondary);">
                Produits actifs
            </div>
        </div>

        <!-- Note moyenne -->
        <div class="card" style="padding: var(--space-6);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--space-4);">
                <div style="
                    width: 50px;
                    height: 50px;
                    background: var(--warning-light);
                    border-radius: var(--radius-lg);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.5rem;
                ">
                    ⭐
                </div>
                <span style="font-size: 0.875rem; color: var(--text-tertiary);">
                    <?= $stats['rating_count'] ?? 0 ?> avis
                </span>
            </div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--warning); margin-bottom: var(--space-1);">
                <?= number_format($stats['rating_average'] ?? 0, 1) ?>/5
            </div>
            <div style="font-size: 0.875rem; color: var(--text-secondary);">
                Note moyenne
            </div>
        </div>

    </div>

    <!-- Graphiques et données -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-8); margin-bottom: var(--space-8);">
        
        <!-- Graphique des ventes -->
        <div class="card" style="padding: var(--space-8);">
            <div class="flex-between mb-6">
                <h2 style="font-size: 1.5rem;">📈 Ventes des 30 derniers jours</h2>
                <div style="display: flex; gap: var(--space-2);">
                    <button class="btn btn-ghost btn-sm" onclick="changeChartPeriod(7)">7j</button>
                    <button class="btn btn-ghost btn-sm" onclick="changeChartPeriod(30)" style="background: var(--primary-100);">30j</button>
                    <button class="btn btn-ghost btn-sm" onclick="changeChartPeriod(90)">90j</button>
                </div>
            </div>
            <canvas id="salesChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Produits en attente -->
        <div class="card" style="padding: var(--space-6);">
            <h3 style="font-size: 1.25rem; margin-bottom: var(--space-6);">
                ⏳ En attente de validation
            </h3>
            
            <?php if (empty($pending_products)): ?>
                <div style="text-align: center; padding: var(--space-8); color: var(--text-tertiary);">
                    <div style="font-size: 3rem; margin-bottom: var(--space-3);">✓</div>
                    <p>Tous vos produits sont validés</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                    <?php foreach (array_slice($pending_products, 0, 3) as $product): ?>
                    <div style="
                        padding: var(--space-3);
                        background: var(--bg-secondary);
                        border-radius: var(--radius);
                        font-size: 0.875rem;
                    ">
                        <div style="font-weight: 600; margin-bottom: var(--space-1);">
                            <?= e(truncate($product['title'], 40)) ?>
                        </div>
                        <div style="color: var(--text-tertiary); font-size: 0.75rem;">
                            Soumis le <?= date('d/m/Y', strtotime($product['created_at'])) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (count($pending_products) > 3): ?>
                    <a href="/seller/products?status=pending" style="font-size: 0.875rem; color: var(--primary-600);">
                        Voir les <?= count($pending_products) - 3 ?> autres →
                    </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- Ventes récentes et Top produits -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-8);">
        
        <!-- Ventes récentes -->
        <div class="card" style="padding: var(--space-8);">
            <h2 style="font-size: 1.5rem; margin-bottom: var(--space-6);">
                💳 Ventes récentes
            </h2>
            
            <?php if (empty($recent_sales)): ?>
                <div style="text-align: center; padding: var(--space-12); color: var(--text-tertiary);">
                    <div style="font-size: 3rem; margin-bottom: var(--space-3);">📦</div>
                    <p>Aucune vente pour le moment</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                    <?php foreach (array_slice($recent_sales, 0, 5) as $sale): ?>
                    <div style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: var(--space-4);
                        background: var(--bg-secondary);
                        border-radius: var(--radius);
                    ">
                        <div>
                            <div style="font-weight: 600; margin-bottom: var(--space-1);">
                                <?= e(truncate($sale['product_title'], 35)) ?>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-tertiary);">
                                <?= timeAgo($sale['order_date']) ?> • <?= e($sale['buyer_name']) ?>
                            </div>
                        </div>
                        <div style="font-weight: 700; color: var(--success);">
                            +<?= formatPrice($sale['seller_amount']) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <a href="/seller/sales" class="btn btn-ghost" style="margin-top: var(--space-2);">
                        Voir toutes les ventes →
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Top produits -->
        <div class="card" style="padding: var(--space-8);">
            <h2 style="font-size: 1.5rem; margin-bottom: var(--space-6);">
                🏆 Produits les plus vendus
            </h2>
            
            <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                <?php 
                // Récupérer les top produits
                $stmt = $db->prepare("
                    SELECT p.id, p.title, p.thumbnail_url, p.sales, p.price
                    FROM products p
                    WHERE p.seller_id = :seller_id AND p.sales > 0
                    ORDER BY p.sales DESC
                    LIMIT 5
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $topProducts = $stmt->fetchAll();
                ?>
                
                <?php if (empty($topProducts)): ?>
                    <div style="text-align: center; padding: var(--space-12); color: var(--text-tertiary);">
                        <div style="font-size: 3rem; margin-bottom: var(--space-3);">🎯</div>
                        <p>Vos meilleures ventes apparaîtront ici</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($topProducts as $index => $product): ?>
                    <div style="
                        display: grid;
                        grid-template-columns: 40px 60px 1fr auto;
                        gap: var(--space-3);
                        align-items: center;
                    ">
                        <!-- Rang -->
                        <div style="
                            width: 32px;
                            height: 32px;
                            background: <?= $index === 0 ? 'var(--warning)' : ($index === 1 ? 'var(--text-tertiary)' : 'var(--border-color)') ?>;
                            color: white;
                            border-radius: var(--radius-full);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-weight: 700;
                            font-size: 0.875rem;
                        ">
                            <?= $index + 1 ?>
                        </div>
                        
                        <!-- Image -->
                        <img 
                            src="<?= e($product['thumbnail']) ?>" 
                            alt="<?= e($product['title']) ?>"
                            style="width: 60px; height: 40px; object-fit: cover; border-radius: var(--radius-sm);"
                        >
                        
                        <!-- Info -->
                        <div>
                            <div style="font-weight: 600; font-size: 0.875rem; margin-bottom: var(--space-1);">
                                <?= e(truncate($product['title'], 30)) ?>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-tertiary);">
                                <?= $product['sales_count'] ?> vente<?= $product['sales_count'] > 1 ? 's' : '' ?>
                            </div>
                        </div>
                        
                        <!-- Prix -->
                        <div style="font-weight: 700; color: var(--primary-600); font-size: 0.875rem;">
                            <?= formatPrice($product['price']) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<!-- JavaScript -->
<script>
// Données pour le graphique (30 derniers jours)
const salesData = <?= json_encode($sales_chart) ?>;

// Préparer les données pour Chart.js
const labels = salesData.map(item => {
    const date = new Date(item.date);
    return date.getDate() + '/' + (date.getMonth() + 1);
});

const revenues = salesData.map(item => parseFloat(item.revenue || 0));
const orders = salesData.map(item => parseInt(item.orders || 0));

// Configuration du graphique
const ctx = document.getElementById('salesChart');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Revenus (€)',
                data: revenues,
                borderColor: '#0ea5e9',
                backgroundColor: 'rgba(14, 165, 233, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y'
            },
            {
                label: 'Commandes',
                data: orders,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                padding: 12,
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#334155',
                borderWidth: 1,
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.datasetIndex === 0) {
                            label += context.parsed.y.toFixed(2) + ' €';
                        } else {
                            label += context.parsed.y;
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Revenus (€)'
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Commandes'
                },
                grid: {
                    drawOnChartArea: false
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Fonction pour changer la période (à implémenter)
function changeChartPeriod(days) {
    console.log('Charger les données pour ' + days + ' jours');
    // TODO: Recharger les données via AJAX
}

// Animation des cards au chargement
document.querySelectorAll('.card').forEach((card, index) => {
    card.style.animation = `fadeIn 0.6s ease-out ${index * 0.05}s both`;
});
</script>

<style>
/* Responsive */
@media (max-width: 1024px) {
    .grid-4 {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    
    [style*="grid-template-columns: 2fr 1fr"],
    [style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 768px) {
    .grid-4 {
        grid-template-columns: 1fr !important;
    }
}
</style>