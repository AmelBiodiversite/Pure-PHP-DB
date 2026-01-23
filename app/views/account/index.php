<?php
/**
 * Dashboard compte utilisateur - VERSION AMÉLIORÉE
 */
$isBuyer = $user['role'] === 'buyer';
?>

<div style="min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
    <div class="container" style="padding-top: var(--space-8); padding-bottom: var(--space-16);">
        
        <!-- Header avec avatar -->
        <div style="
            background: white;
            border-radius: 20px;
            padding: var(--space-8);
            margin-bottom: var(--space-8);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: var(--space-6);
            flex-wrap: wrap;
        ">
            <div style="
                width: 100px;
                height: 100px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2.5rem;
                font-weight: 800;
                box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
            ">
                <?= strtoupper(substr($user['username'], 0, 2)) ?>
            </div>
            <div style="flex: 1;">
                <h1 style="margin: 0 0 var(--space-2); font-size: 2rem;">
                    Bienvenue, <?= e($user['username']) ?> ! 👋
                </h1>
                <p style="color: var(--text-secondary); font-size: 1rem; margin: 0;">
                    <?php if ($isBuyer): ?>
                        🛍️ Compte Acheteur • Membre depuis <?= date('M Y', strtotime($user['created_at'])) ?>
                    <?php else: ?>
                        💼 Compte Vendeur • Membre depuis <?= date('M Y', strtotime($user['created_at'])) ?>
                    <?php endif; ?>
                </p>
            </div>
            <a href="/account/settings" style="
                padding: var(--space-3) var(--space-5);
                background: white;
                border: 2px solid var(--border-color);
                border-radius: 12px;
                text-decoration: none;
                color: var(--text-primary);
                font-weight: 600;
                transition: all 0.3s;
                display: flex;
                align-items: center;
                gap: var(--space-2);
            " onmouseover="this.style.borderColor='#667eea'; this.style.background='rgba(102, 126, 234, 0.05)'" onmouseout="this.style.borderColor='var(--border-color)'; this.style.background='white'">
                ⚙️ Paramètres
            </a>
        </div>

        <div style="display: grid; grid-template-columns: 280px 1fr; gap: var(--space-8);">
            
            <!-- Sidebar -->
            <aside>
                <div style="
                    background: white;
                    border-radius: 20px;
                    padding: var(--space-6);
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                    position: sticky;
                    top: 100px;
                ">
                    <h3 style="
                        font-size: 0.85rem;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        color: var(--text-tertiary);
                        margin: 0 0 var(--space-4);
                        font-weight: 700;
                    ">
                        Navigation
                    </h3>
                    
                    <nav style="display: flex; flex-direction: column; gap: var(--space-2);">
                        <a href="/account" style="
                            display: flex;
                            align-items: center;
                            gap: var(--space-3);
                            padding: var(--space-3) var(--space-4);
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            text-decoration: none;
                            border-radius: 12px;
                            font-weight: 600;
                            transition: all 0.3s;
                            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
                        ">
                            <span style="font-size: 1.2rem;">📊</span>
                            <span>Tableau de bord</span>
                        </a>
                        
                        <a href="/orders" style="
                            display: flex;
                            align-items: center;
                            gap: var(--space-3);
                            padding: var(--space-3) var(--space-4);
                            background: transparent;
                            color: var(--text-primary);
                            text-decoration: none;
                            border-radius: 12px;
                            font-weight: 500;
                            transition: all 0.3s;
                        " onmouseover="this.style.background='var(--bg-secondary)'" onmouseout="this.style.background='transparent'">
                            <span style="font-size: 1.2rem;">📦</span>
                            <span>Mes commandes</span>
                        </a>
                        
                        <?php if ($isBuyer): ?>
                        <a href="/wishlist" style="
                            display: flex;
                            align-items: center;
                            gap: var(--space-3);
                            padding: var(--space-3) var(--space-4);
                            background: transparent;
                            color: var(--text-primary);
                            text-decoration: none;
                            border-radius: 12px;
                            font-weight: 500;
                            transition: all 0.3s;
                        " onmouseover="this.style.background='var(--bg-secondary)'" onmouseout="this.style.background='transparent'">
                            <span style="font-size: 1.2rem;">❤️</span>
                            <span>Mes favoris</span>
                        </a>
                        
                        <a href="/account/downloads" style="
                            display: flex;
                            align-items: center;
                            gap: var(--space-3);
                            padding: var(--space-3) var(--space-4);
                            background: transparent;
                            color: var(--text-primary);
                            text-decoration: none;
                            border-radius: 12px;
                            font-weight: 500;
                            transition: all 0.3s;
                        " onmouseover="this.style.background='var(--bg-secondary)'" onmouseout="this.style.background='transparent'">
                            <span style="font-size: 1.2rem;">📥</span>
                            <span>Téléchargements</span>
                        </a>
                        <?php else: ?>
                        <a href="/seller/dashboard" style="
                            display: flex;
                            align-items: center;
                            gap: var(--space-3);
                            padding: var(--space-3) var(--space-4);
                            background: transparent;
                            color: var(--text-primary);
                            text-decoration: none;
                            border-radius: 12px;
                            font-weight: 500;
                            transition: all 0.3s;
                        " onmouseover="this.style.background='var(--bg-secondary)'" onmouseout="this.style.background='transparent'">
                            <span style="font-size: 1.2rem;">💼</span>
                            <span>Espace vendeur</span>
                        </a>
                        <?php endif; ?>
                        
                        <div style="height: 1px; background: var(--border-color); margin: var(--space-3) 0;"></div>
                        
                        <a href="/logout" style="
                            display: flex;
                            align-items: center;
                            gap: var(--space-3);
                            padding: var(--space-3) var(--space-4);
                            background: transparent;
                            color: #ef4444;
                            text-decoration: none;
                            border-radius: 12px;
                            font-weight: 500;
                            transition: all 0.3s;
                        " onmouseover="this.style.background='rgba(239, 68, 68, 0.1)'" onmouseout="this.style.background='transparent'">
                            <span style="font-size: 1.2rem;">🚪</span>
                            <span>Déconnexion</span>
                        </a>
                    </nav>
                </div>
            </aside>
            
            <!-- Contenu principal -->
            <main>
                <?php if ($isBuyer): ?>
                
                <!-- Stats acheteur -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--space-6); margin-bottom: var(--space-8);">
                    <div style="
                        background: white;
                        border-radius: 20px;
                        padding: var(--space-8);
                        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                        text-align: center;
                        position: relative;
                        overflow: hidden;
                    ">
                        <div style="
                            position: absolute;
                            top: -20px;
                            right: -20px;
                            width: 100px;
                            height: 100px;
                            background: rgba(102, 126, 234, 0.1);
                            border-radius: 50%;
                            filter: blur(30px);
                        "></div>
                        <div style="font-size: 3rem; margin-bottom: var(--space-3);">📦</div>
                        <div style="
                            font-size: 2.5rem;
                            font-weight: 800;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                            background-clip: text;
                            margin-bottom: var(--space-2);
                        ">
                            <?= $stats['total_orders'] ?? 0 ?>
                        </div>
                        <h3 style="margin: 0; font-size: 1rem; color: var(--text-secondary); font-weight: 600;">
                            Commandes
                        </h3>
                    </div>
                    
                    <div style="
                        background: white;
                        border-radius: 20px;
                        padding: var(--space-8);
                        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                        text-align: center;
                        position: relative;
                        overflow: hidden;
                    ">
                        <div style="
                            position: absolute;
                            top: -20px;
                            right: -20px;
                            width: 100px;
                            height: 100px;
                            background: rgba(16, 185, 129, 0.1);
                            border-radius: 50%;
                            filter: blur(30px);
                        "></div>
                        <div style="font-size: 3rem; margin-bottom: var(--space-3);">💰</div>
                        <div style="
                            font-size: 2rem;
                            font-weight: 800;
                            color: #10b981;
                            margin-bottom: var(--space-2);
                        ">
                            <?= formatPrice($stats['total_spent'] ?? 0) ?>
                        </div>
                        <h3 style="margin: 0; font-size: 1rem; color: var(--text-secondary); font-weight: 600;">
                            Total dépensé
                        </h3>
                    </div>
                </div>
                
                <!-- Dernières commandes -->
                <div style="
                    background: white;
                    border-radius: 20px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                    overflow: hidden;
                ">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                        <h2 style="margin: 0; font-size: 1.5rem;">📦 Dernières commandes</h2>
                        <a href="/orders" style="
                            color: #667eea;
                            text-decoration: none;
                            font-weight: 600;
                            font-size: 0.9rem;
                            transition: color 0.2s;
                        " onmouseover="this.style.color='#764ba2'" onmouseout="this.style.color='#667eea'">
                            Voir tout →
                        </a>
                    </div>
                    
                    <?php if (empty($orders)): ?>
                    <div style="padding: var(--space-16); text-align: center;">
                        <div style="
                            width: 120px;
                            height: 120px;
                            margin: 0 auto var(--space-6);
                            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 4rem;
                        ">
                            🛍️
                        </div>
                        <h3 style="margin: 0 0 var(--space-3); font-size: 1.5rem;">Aucune commande</h3>
                        <p style="color: var(--text-secondary); margin-bottom: var(--space-6); font-size: 1rem;">
                            Vous n'avez pas encore passé de commande.
                        </p>
                        <a href="/products" style="
                            display: inline-block;
                            padding: var(--space-3) var(--space-6);
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            text-decoration: none;
                            border-radius: 12px;
                            font-weight: 600;
                            transition: all 0.3s;
                            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
                        " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(102, 126, 234, 0.5)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.4)'">
                            🛍️ Découvrir les produits
                        </a>
                    </div>
                    <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: var(--bg-secondary);">
                                    <th style="padding: var(--space-4); text-align: left; font-weight: 600; font-size: 0.875rem; color: var(--text-tertiary);">Date</th>
                                    <th style="padding: var(--space-4); text-align: left; font-weight: 600; font-size: 0.875rem; color: var(--text-tertiary);">Référence</th>
                                    <th style="padding: var(--space-4); text-align: center; font-weight: 600; font-size: 0.875rem; color: var(--text-tertiary);">Articles</th>
                                    <th style="padding: var(--space-4); text-align: right; font-weight: 600; font-size: 0.875rem; color: var(--text-tertiary);">Montant</th>
                                    <th style="padding: var(--space-4); text-align: center; font-weight: 600; font-size: 0.875rem; color: var(--text-tertiary);">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;" onmouseover="this.style.background='var(--bg-secondary)'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: var(--space-4);">
                                        <div style="font-weight: 500;"><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                                        <div style="font-size: 0.8rem; color: var(--text-tertiary);"><?= date('H:i', strtotime($order['created_at'])) ?></div>
                                    </td>
                                    <td style="padding: var(--space-4); font-family: monospace; font-size: 0.875rem; color: var(--text-secondary);">
                                        #<?= substr($order['id'], 0, 8) ?>
                                    </td>
                                    <td style="padding: var(--space-4); text-align: center;">
                                        <span style="
                                            display: inline-block;
                                            padding: 0.25rem 0.75rem;
                                            background: var(--bg-secondary);
                                            border-radius: 8px;
                                            font-weight: 600;
                                            font-size: 0.875rem;
                                        ">
                                            <?= $order['items_count'] ?>
                                        </span>
                                    </td>
                                    <td style="padding: var(--space-4); text-align: right; font-weight: 700; font-size: 1.05rem; color: var(--text-primary);">
                                        <?= formatPrice($order['total_amount']) ?>
                                    </td>
                                    <td style="padding: var(--space-4); text-align: center;">
                                        <span style="
                                            display: inline-block;
                                            padding: 0.4rem 1rem;
                                            background: <?= $order['payment_status'] === 'completed' ? '#d1fae5' : '#fef3c7' ?>;
                                            color: <?= $order['payment_status'] === 'completed' ? '#065f46' : '#92400e' ?>;
                                            border-radius: 12px;
                                            font-weight: 600;
                                            font-size: 0.8rem;
                                            text-transform: uppercase;
                                        ">
                                            <?= $order['payment_status'] === 'completed' ? '✓ Payé' : '⏳ En attente' ?>
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
                <div style="
                    background: white;
                    border-radius: 20px;
                    padding: var(--space-16);
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                    text-align: center;
                    position: relative;
                    overflow: hidden;
                ">
                    <div style="
                        position: absolute;
                        top: -50px;
                        right: -50px;
                        width: 200px;
                        height: 200px;
                        background: rgba(102, 126, 234, 0.1);
                        border-radius: 50%;
                        filter: blur(60px);
                    "></div>
                    <div style="font-size: 5rem; margin-bottom: var(--space-4);">💼</div>
                    <h2 style="margin-bottom: var(--space-4); font-size: 2rem;">Espace Vendeur</h2>
                    <p style="color: var(--text-secondary); margin-bottom: var(--space-8); font-size: 1.05rem; max-width: 500px; margin-left: auto; margin-right: auto;">
                        Gérez vos produits, suivez vos ventes et analysez vos performances depuis votre dashboard vendeur.
                    </p>
                    <a href="/seller/dashboard" style="
                        display: inline-block;
                        padding: var(--space-4) var(--space-8);
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        text-decoration: none;
                        border-radius: 12px;
                        font-weight: 600;
                        font-size: 1.1rem;
                        transition: all 0.3s;
                        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
                    " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(102, 126, 234, 0.5)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.4)'">
                        🚀 Accéder au dashboard vendeur
                    </a>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</div>

<style>
/* Responsive */
@media (max-width: 1024px) {
    div[style*="grid-template-columns: 280px 1fr"] {
        grid-template-columns: 1fr !important;
    }
    
    aside div[style*="position: sticky"] {
        position: relative !important;
        top: 0 !important;
    }
}

@media (max-width: 768px) {
    div[style*="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
}
</style>