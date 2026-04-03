<?php
/**
 * MARKETFLOW PRO - DASHBOARD VENDEUR
 * Fichier : app/views/seller/dashboard.php
 *
 * Variables reçues depuis SellerController@dashboard :
 *   $stats          → total_products, approved_products, pending_products,
 *                     total_downloads, total_revenue
 *   $revenue_by_day → tableau [{date, revenue, orders}, ...]  (30 derniers jours)
 *   $top_products   → tableau [{id, title, price, downloads, sales_count, revenue}, ...]
 *   $recent_products→ tableau des 5 derniers produits créés
 *   $recent_sales   → tableau des 10 dernières commandes (30 jours)
 *
 * Corrections apportées :
 *   - Chart.js chargé en tête de fichier (était absent du header global)
 *   - Code dupliqué supprimé (deux dashboards étaient concaténés)
 *   - Variable $sales_chart remplacée par $revenue_by_day (correcte)
 *   - Variable $pending_products remplacée par $stats['pending_products']
 *   - seller_amount absent de recent_sales → remplacé par total_amount
 *   - Double axe Y (revenus + commandes) sur le graphique principal
 *   - Boutons 7j / 30j / 90j branchés via AJAX sur /seller/analytics
 */

// ── Chargement de Chart.js (local, déjà présent dans public/js/libs/) ──────────
// On le charge ici et non dans le header car seules les pages vendeur en ont besoin
echo '<script src="' . JS_URL . '/libs/chart.min.js"></script>' . "\n";
?>

<div class="container mt-8 mb-16">

    <!-- ═══════════════════════════════════════════════
         HEADER PAGE
    ═══════════════════════════════════════════════ -->
    <div class="flex-between mb-8">
        <div>
            <h1 class="mb-2">📊 Dashboard Vendeur</h1>
            <p style="color: var(--text-secondary);">Suivez vos performances en temps réel</p>
        </div>
        <a href="/seller/products/create" class="btn btn-primary">
            ➕ Nouveau produit
        </a>
    </div>

    <!-- ═══════════════════════════════════════════════
         CARTES STATS (4 colonnes)
         Source : $stats retourné par SellerController
    ═══════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Revenus totaux -->
        <div class="card hover-lift">
            <div style="display:flex; align-items:center; gap:1rem;">
                <div style="width:48px; height:48px; background:linear-gradient(135deg,#43e97b,#38f9d7);
                            border-radius:12px; display:flex; align-items:center;
                            justify-content:center; font-size:24px;">💰</div>
                <div style="flex:1;">
                    <p style="color:var(--text-secondary); font-size:.875rem; margin-bottom:.25rem;">Revenus totaux</p>
                    <h3 style="margin:0; font-size:1.75rem;"><?= formatPrice($stats['total_revenue'] ?? 0) ?></h3>
                </div>
            </div>
        </div>

        <!-- Produits -->
        <div class="card hover-lift">
            <div style="display:flex; align-items:center; gap:1rem;">
                <div style="width:48px; height:48px; background:linear-gradient(135deg,#667eea,#764ba2);
                            border-radius:12px; display:flex; align-items:center;
                            justify-content:center; font-size:24px;">📦</div>
                <div style="flex:1;">
                    <p style="color:var(--text-secondary); font-size:.875rem; margin-bottom:.25rem;">Produits</p>
                    <h3 style="margin:0; font-size:1.75rem;"><?= e($stats['total_products'] ?? 0) ?></h3>
                    <p style="font-size:.75rem; color:var(--success); margin:0;">
                        <?= e($stats['approved_products'] ?? 0) ?> approuvés
                        <?php if (($stats['pending_products'] ?? 0) > 0): ?>
                            · <span style="color:var(--warning);"><?= e($stats['pending_products']) ?> en attente</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Téléchargements -->
        <div class="card hover-lift">
            <div style="display:flex; align-items:center; gap:1rem;">
                <div style="width:48px; height:48px; background:linear-gradient(135deg,#f093fb,#f5576c);
                            border-radius:12px; display:flex; align-items:center;
                            justify-content:center; font-size:24px;">📥</div>
                <div style="flex:1;">
                    <p style="color:var(--text-secondary); font-size:.875rem; margin-bottom:.25rem;">Téléchargements</p>
                    <h3 style="margin:0; font-size:1.75rem;"><?= number_format($stats['total_downloads'] ?? 0) ?></h3>
                </div>
            </div>
        </div>

        <!-- Ventes récentes (30j) -->
        <div class="card hover-lift">
            <div style="display:flex; align-items:center; gap:1rem;">
                <div style="width:48px; height:48px; background:linear-gradient(135deg,#fa709a,#fee140);
                            border-radius:12px; display:flex; align-items:center;
                            justify-content:center; font-size:24px;">🛍️</div>
                <div style="flex:1;">
                    <p style="color:var(--text-secondary); font-size:.875rem; margin-bottom:.25rem;">Ventes (30j)</p>
                    <h3 style="margin:0; font-size:1.75rem;"><?= number_format(count($recent_sales ?? [])) ?></h3>
                </div>
            </div>
        </div>

    </div>

    <!-- ═══════════════════════════════════════════════
         GRAPHIQUES
         - Gauche  : courbe revenus + commandes (30j)
         - Droite  : barres top 5 produits
    ═══════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

        <!-- ── Graphique revenus ── -->
        <div class="card">
            <div style="padding:1.5rem; border-bottom:1px solid var(--border-color);
                        display:flex; justify-content:space-between; align-items:center;">
                <h2 style="margin:0;">📈 Revenus (30 derniers jours)</h2>
                <!-- Boutons période : appellent changeChartPeriod() défini en JS ci-dessous -->
                <div style="display:flex; gap:.5rem;">
                    <button class="btn btn-sm btn-ghost period-btn" data-days="7"  onclick="changeChartPeriod(7)">7j</button>
                    <button class="btn btn-sm btn-ghost period-btn active-period" data-days="30" onclick="changeChartPeriod(30)">30j</button>
                    <button class="btn btn-sm btn-ghost period-btn" data-days="90" onclick="changeChartPeriod(90)">90j</button>
                </div>
            </div>
            <div style="padding:1.5rem;">
                <!-- height fixe pour que Chart.js respecte la hauteur max -->
                <div style="position:relative; height:280px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- ── Top 5 produits ── -->
        <div class="card">
            <div style="padding:1.5rem; border-bottom:1px solid var(--border-color);">
                <h2 style="margin:0;">🏆 Top 5 produits</h2>
            </div>
            <div style="padding:1.5rem;">
                <?php if (empty($top_products)): ?>
                    <div style="text-align:center; padding:3rem; color:var(--text-secondary);">
                        <p style="font-size:3rem; margin-bottom:1rem;">🎯</p>
                        <p>Vos meilleures ventes apparaîtront ici</p>
                    </div>
                <?php else: ?>
                    <div style="position:relative; height:280px;">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- ═══════════════════════════════════════════════
         VENTES RÉCENTES + PRODUITS RÉCENTS
    ═══════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

        <!-- Ventes récentes -->
        <div class="card">
            <div style="padding:1.5rem; border-bottom:1px solid var(--border-color);">
                <h2 style="margin:0;">💳 Ventes récentes</h2>
            </div>
            <div style="padding:1.5rem;">
                <?php if (empty($recent_sales)): ?>
                    <div style="text-align:center; padding:3rem; color:var(--text-secondary);">
                        <p style="font-size:3rem; margin-bottom:1rem;">📭</p>
                        <p>Aucune vente sur les 30 derniers jours</p>
                    </div>
                <?php else: ?>
                    <div style="display:flex; flex-direction:column; gap:.75rem;">
                        <?php foreach (array_slice($recent_sales, 0, 5) as $sale): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center;
                                    padding:.75rem; background:var(--bg-secondary); border-radius:8px;">
                            <div>
                                <div style="font-weight:600; font-size:.875rem; margin-bottom:.25rem;">
                                    <!-- Numéro de commande cliquable -->
                                    <a href="/orders/<?= e($sale['order_number']) ?>"
                                       style="color:var(--primary-600); text-decoration:none;">
                                        <?= e($sale['order_number']) ?>
                                    </a>
                                </div>
                                <div style="font-size:.75rem; color:var(--text-secondary);">
                                    <?= date('d/m/Y', strtotime($sale['created_at'])) ?>
                                    · <?= e($sale['buyer_name']) ?>
                                    · <?= e($sale['items_count']) ?> article(s)
                                </div>
                            </div>
                            <!-- Montant total de la commande -->
                            <div style="font-weight:700; color:var(--success); white-space:nowrap;">
                                +<?= formatPrice($sale['total_amount']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <?php if (count($recent_sales) > 5): ?>
                            <a href="/seller/sales" class="btn btn-ghost btn-sm" style="margin-top:.5rem;">
                                Voir toutes les ventes →
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Produits récents -->
        <div class="card">
            <div style="padding:1.5rem; border-bottom:1px solid var(--border-color);
                        display:flex; justify-content:space-between; align-items:center;">
                <h2 style="margin:0;">🕒 Produits récents</h2>
                <a href="/seller/products" style="font-size:.875rem; color:var(--primary-600);">Voir tout →</a>
            </div>
            <div style="padding:1.5rem;">
                <?php if (empty($recent_products)): ?>
                    <div style="text-align:center; padding:3rem; color:var(--text-secondary);">
                        <p style="font-size:3rem; margin-bottom:1rem;">📦</p>
                        <p>Aucun produit pour le moment</p>
                        <a href="/seller/products/create" class="btn btn-primary" style="margin-top:1rem;">
                            Créer mon premier produit
                        </a>
                    </div>
                <?php else: ?>
                    <div style="display:flex; flex-direction:column; gap:.75rem;">
                        <?php foreach ($recent_products as $product): ?>
                        <div style="display:flex; align-items:center; gap:1rem;
                                    padding:.75rem; background:var(--bg-secondary); border-radius:8px;">
                            <!-- Miniature -->
                            <?php if ($product['thumbnail_url']): ?>
                                <img src="<?= e($product['thumbnail_url']) ?>"
                                     style="width:40px; height:40px; object-fit:cover;
                                            border-radius:6px; flex-shrink:0;">
                            <?php endif; ?>
                            <!-- Titre + statut -->
                            <div style="flex:1; min-width:0;">
                                <div style="font-weight:600; font-size:.875rem;
                                            white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    <?= e($product['title']) ?>
                                </div>
                                <div style="font-size:.75rem; color:var(--text-secondary); margin-top:.2rem;">
                                    <?= formatPrice($product['price']) ?>
                                    · <span class="badge badge-<?= $product['status'] === 'approved' ? 'success' : 'warning' ?>">
                                        <?= e($product['status']) ?>
                                    </span>
                                </div>
                            </div>
                            <!-- Lien modifier -->
                            <a href="/seller/products/<?= e($product['id']) ?>/edit"
                               class="btn btn-sm btn-outline" style="flex-shrink:0;">✏️</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- ═══════════════════════════════════════════════
         ACTIONS RAPIDES
    ═══════════════════════════════════════════════ -->
    <div class="card">
        <div style="padding:1.5rem; border-bottom:1px solid var(--border-color);">
            <h2 style="margin:0;">⚡ Actions rapides</h2>
        </div>
        <div style="padding:1.5rem; display:grid;
                    grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:1rem;">
            <a href="/seller/products/create" class="btn btn-primary hover-lift">➕ Nouveau produit</a>
            <a href="/seller/products"         class="btn btn-outline hover-lift">📦 Mes produits</a>
            <a href="/seller/sales"            class="btn btn-outline hover-lift">🛍️ Mes ventes</a>
            <a href="/seller/analytics"        class="btn btn-outline hover-lift">📊 Analytics</a>
        </div>
    </div>

</div><!-- /container -->

<!-- ═══════════════════════════════════════════════
     JAVASCRIPT — Graphiques Chart.js
     Chart.js est chargé en haut de ce fichier via JS_URL/libs/chart.min.js
═══════════════════════════════════════════════ -->
<script>
(function () {
    'use strict';

    // ── 1. Données PHP → JS ─────────────────────────────────────────────────
    // $revenue_by_day : [{date:"2025-01-10", revenue:"49.99", orders:"2"}, ...]
    // $top_products   : [{title:"...", revenue:"149.97", sales_count:3}, ...]

    const rawRevenue  = <?= json_encode($revenue_by_day  ?? []) ?>;
    const rawProducts = <?= json_encode($top_products    ?? []) ?>;

    // ── 2. Couleurs adaptées au thème (CSS vars → valeurs hex/rgb) ──────────
    // On lit la valeur réelle des variables CSS du thème courant
    const style       = getComputedStyle(document.documentElement);
    const colorBlue   = '#0ea5e9';   // Revenus
    const colorGreen  = '#10b981';   // Commandes
    const colorPurple = '#6366f1';   // Barres top produits
    const gridColor   = 'rgba(0,0,0,0.06)';

    // ── 3. Graphique 1 : Revenus + Commandes (double axe Y) ─────────────────
    const revenueCtx = document.getElementById('revenueChart');

    if (revenueCtx) {
        // Formatage des labels de date : "10 jan", "11 jan"...
        const labels   = rawRevenue.map(d =>
            new Date(d.date).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' })
        );
        const revenues = rawRevenue.map(d => parseFloat(d.revenue  || 0));
        const orders   = rawRevenue.map(d => parseInt(d.orders     || 0, 10));

        window.revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label:           'Revenus (€)',
                        data:            revenues,
                        borderColor:     colorBlue,
                        backgroundColor: 'rgba(14, 165, 233, 0.08)',
                        tension:         0.4,   // courbe lisse
                        fill:            true,
                        yAxisID:         'yRevenue',
                        pointRadius:     3,
                        pointHoverRadius:6
                    },
                    {
                        label:           'Commandes',
                        data:            orders,
                        borderColor:     colorGreen,
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        tension:         0.4,
                        fill:            true,
                        yAxisID:         'yOrders',
                        pointRadius:     3,
                        pointHoverRadius:6
                    }
                ]
            },
            options: {
                responsive:          true,
                maintainAspectRatio: false,     // respecte le div parent height:280px
                interaction: {
                    mode:      'index',          // tooltip groupé par colonne
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels:   { usePointStyle: true, padding: 16 }
                    },
                    tooltip: {
                        callbacks: {
                            // Formater le label selon le dataset
                            label: function (ctx) {
                                const v = ctx.parsed.y;
                                return ctx.datasetIndex === 0
                                    ? ' Revenus : ' + v.toFixed(2) + ' €'
                                    : ' Commandes : ' + v;
                            }
                        }
                    }
                },
                scales: {
                    yRevenue: {
                        type:     'linear',
                        display:  true,
                        position: 'left',
                        beginAtZero: true,
                        grid:     { color: gridColor },
                        ticks:    { callback: v => v + ' €' }
                    },
                    yOrders: {
                        type:     'linear',
                        display:  true,
                        position: 'right',
                        beginAtZero: true,
                        grid:     { drawOnChartArea: false }, // évite double grille
                        ticks:    { precision: 0 }            // entiers seulement
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ── 4. Graphique 2 : Top 5 produits (barres horizontales) ───────────────
    const topCtx = document.getElementById('topProductsChart');

    if (topCtx && rawProducts.length > 0) {
        // Tronquer les titres longs pour qu'ils tiennent sur l'axe
        const truncate = (str, n) => str.length > n ? str.slice(0, n) + '…' : str;
        const labels   = rawProducts.map(p => truncate(p.title, 22));
        const revenues = rawProducts.map(p => parseFloat(p.revenue || 0));

        // Dégradé de transparence sur les barres (la 1ère est la plus opaque)
        const bgColors = rawProducts.map((_, i) =>
            `rgba(99, 102, 241, ${1 - i * 0.15})`  // violet dégradé
        );

        new Chart(topCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label:           'Revenus (€)',
                    data:            revenues,
                    backgroundColor: bgColors,
                    borderRadius:    6,     // coins arrondis sur les barres
                    borderSkipped:   false
                }]
            },
            options: {
                indexAxis:           'y',   // barres HORIZONTALES (meilleur affichage titres)
                responsive:          true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + ctx.parsed.x.toFixed(2) + ' €'
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid:  { color: gridColor },
                        ticks: { callback: v => v + ' €' }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ── 5. Boutons de période (7j / 30j / 90j) ───────────────────────────────
    // Appelle /seller/analytics?period=N via AJAX et met à jour le graphique revenus
    window.changeChartPeriod = function (days) {
        // Mettre en évidence le bouton actif
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.classList.toggle('active-period', parseInt(btn.dataset.days, 10) === days);
        });

        // Requête AJAX vers le contrôleur
        fetch('/seller/analytics?period=' + days + '&format=json', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (!window.revenueChart || !Array.isArray(data.revenue_by_day)) return;

            const chart   = window.revenueChart;
            const newData = data.revenue_by_day;

            // Mettre à jour les labels et les deux datasets
            chart.data.labels                    = newData.map(d =>
                new Date(d.date).toLocaleDateString('fr-FR', { day:'2-digit', month:'short' })
            );
            chart.data.datasets[0].data = newData.map(d => parseFloat(d.revenue || 0));
            chart.data.datasets[1].data = newData.map(d => parseInt(d.orders   || 0, 10));
            chart.update(); // re-render avec animation
        })
        .catch(err => console.warn('[Dashboard] Erreur chargement période :', err));
    };

})();
</script>
<style>
/* === DESIGN MAQUETTE2 — DASHBOARD VENDEUR === */
.container{background:#faf9f5}
h1,h2,h3{font-family:Georgia,serif;font-weight:400;color:#1e1208}
/* Stats cards */
.card.hover-lift,.card{background:#fff!important;border:0.5px solid #ede8df!important;border-radius:14px!important;box-shadow:none!important}
.hover-lift:hover{transform:translateY(-2px)!important;box-shadow:none!important}
/* Icônes stats */
div[style*="background:linear-gradient(135deg,#43e97b"]{background:#e4f1d8!important;border-radius:10px!important}
div[style*="background:linear-gradient(135deg,#667eea"]{background:#ede9fe!important;border-radius:10px!important}
div[style*="background:linear-gradient(135deg,#f093fb"]{background:#fce5df!important;border-radius:10px!important}
div[style*="background:linear-gradient(135deg,#fa709a"]{background:#fef3e0!important;border-radius:10px!important}
/* Chiffres h3 dans stats */
.card h3[style*="font-size:1.75rem"]{font-family:Georgia,serif!important;font-weight:400!important;color:#1e1208!important}
/* Couleur succès → vert naturel */
[style*="color:var(--success)"]{color:#3a7d44!important}
[style*="color:var(--warning)"]{color:#7d5a00!important}
/* Séparateurs border */
[style*="border-bottom:1px solid var(--border-color)"]{border-bottom:0.5px solid #ede8df!important}
/* Lien commande */
a[style*="color:var(--primary-600)"]{color:#7c6cf0!important}
/* Montant vente */
div[style*="color:var(--success)"][style*="white-space:nowrap"]{color:#3a7d44!important;font-family:Georgia,serif!important;font-size:15px!important;font-weight:400!important}
/* Rows ventes/produits */
div[style*="background:var(--bg-secondary)"][style*="border-radius:8px"]{background:#faf9f5!important;border:0.5px solid #ede8df!important;border-radius:10px!important}
/* Badges produit */
.badge-success{background:#e4f1d8!important;color:#2d6a35!important;border-radius:6px!important;font-size:10px!important;padding:2px 7px!important}
.badge-warning{background:#fef9e7!important;color:#7d5a00!important;border-radius:6px!important;font-size:10px!important;padding:2px 7px!important}
/* Boutons */
.btn-primary{background:#7c6cf0!important;color:#fff!important;border:none!important;border-radius:8px!important;font-family:'Manrope',sans-serif!important;font-size:12px!important;font-weight:500!important}
.btn-primary:hover{background:#6558d4!important}
.btn-outline{background:transparent!important;color:#7c6cf0!important;border:0.5px solid #7c6cf0!important;border-radius:8px!important;font-family:'Manrope',sans-serif!important;font-size:11px!important}
.btn-ghost{background:transparent!important;color:#6b5c4e!important;border:0.5px solid #ddd6c8!important;border-radius:8px!important;font-family:'Manrope',sans-serif!important;font-size:11px!important}
.btn-sm{padding:5px 10px!important;font-size:11px!important}
/* Bouton période actif */
.active-period{background:#ede9fe!important;color:#534ab7!important;font-weight:600!important;border:0.5px solid #c9c4f5!important}
@media(max-width:1024px){[class*="lg:grid-cols-2"]{grid-template-columns:1fr!important}[class*="lg:grid-cols-4"]{grid-template-columns:repeat(2,1fr)!important}}
@media(max-width:640px){[class*="md:grid-cols-2"],[class*="lg:grid-cols-4"]{grid-template-columns:1fr!important}}
</style>
