<?php
/**
 * MARKETFLOW PRO - PAGE SUCCÈS PAIEMENT
 * Fichier : app/views/payment/success.php
 */
?>

<div class="container mt-8 mb-16">
    
    <!-- Animation succès -->
    <div style="text-align: center; margin-bottom: var(--space-12);">
        <div style="
            width: 120px;
            height: 120px;
            background: var(--gradient-success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--space-6);
            animation: successPulse 0.6s ease-out;
        ">
            <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M50 15L22.5 42.5L10 30" stroke="white" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        
        <h1 style="font-size: 2.5rem; margin-bottom: var(--space-4); animation: fadeIn 0.8s ease-out;">
            Paiement réussi ! 🎉
        </h1>
        
        <p style="font-size: 1.25rem; color: var(--text-secondary); animation: fadeIn 1s ease-out;">
            Votre commande <strong style="color: var(--primary-600);">#<?= e($order['order_number']) ?></strong> a été confirmée
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 400px; gap: var(--space-8); max-width: 1200px; margin: 0 auto;">
        
        <!-- Produits commandés -->
        <div>
            
            <div class="card" style="padding: var(--space-8); margin-bottom: var(--space-6);">
                
                <h2 style="font-size: 1.5rem; margin-bottom: var(--space-6); display: flex; align-items: center; gap: var(--space-3);">
                    📦 Vos produits
                </h2>

                <div style="display: flex; flex-direction: column; gap: var(--space-6);">
                    
                    <?php foreach ($order['items'] as $item): ?>
                    <div style="
                        display: grid;
                        grid-template-columns: 100px 1fr auto;
                        gap: var(--space-4);
                        align-items: center;
                        padding-bottom: var(--space-6);
                        border-bottom: 1px solid var(--border-color);
                    ">
                        
                        <!-- Thumbnail -->
                        <img 
                            src="<?= e($item['thumbnail']) ?>" 
                            alt="<?= e($item['product_title']) ?>"
                            style="width: 100px; height: 70px; object-fit: cover; border-radius: var(--radius);"
                        >

                        <!-- Info -->
                        <div>
                            <h3 style="font-size: 1.125rem; margin-bottom: var(--space-2);">
                                <?= e($item['product_title']) ?>
                            </h3>
                            <p style="font-size: 0.875rem; color: var(--text-tertiary); margin-bottom: var(--space-2);">
                                Par <?= e($item['shop_name'] ?? $item['seller_username']) ?>
                            </p>
                            <div style="
                                display: inline-block;
                                padding: var(--space-1) var(--space-3);
                                background: var(--success-light);
                                color: #065f46;
                                font-size: 0.75rem;
                                font-weight: 600;
                                border-radius: var(--radius-full);
                            ">
                                ✓ Disponible
                            </div>
                        </div>

                        <!-- Téléchargement -->
                        <div style="text-align: right;">
                            <a 
                                href="/orders/<?= e($order['order_number']) ?>/download/<?= e($item['id']) ?>" 
                                class="btn btn-primary btn-sm"
                            >
                                📥 Télécharger
                            </a>
                            <div style="font-size: 0.75rem; color: var(--text-tertiary); margin-top: var(--space-2);">
                                Clé: <?= e(substr($item['license_key'], 0, 16)) ?>...
                            </div>
                        </div>

                    </div>
                    <?php endforeach; ?>

                </div>

            </div>

            <!-- Informations importantes -->
            <div class="card" style="padding: var(--space-6); background: var(--primary-50);">
                <h3 style="margin-bottom: var(--space-4); display: flex; align-items: center; gap: var(--space-2);">
                    ℹ️ Informations importantes
                </h3>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: var(--space-3); color: var(--primary-700);">
                    <li>• Un email de confirmation a été envoyé à <strong><?= e($order['buyer_email']) ?></strong></li>
                    <li>• Vos produits sont disponibles immédiatement dans <a href="/orders" style="color: var(--primary-700); text-decoration: underline;">Mes commandes</a></li>
                    <li>• Vous pouvez télécharger chaque produit jusqu'à <strong>3 fois</strong></li>
                    <li>• Conservez vos clés de licence pour toute utilisation future</li>
                    <li>• En cas de problème, contactez le support ou le vendeur</li>
                </ul>
            </div>

        </div>

        <!-- Sidebar récapitulatif -->
        <aside>
            
            <!-- Résumé commande -->
            <div class="card" style="padding: var(--space-6); margin-bottom: var(--space-6);">
                
                <h3 style="margin-bottom: var(--space-6); font-size: 1.25rem;">
                    Récapitulatif
                </h3>

                <div style="display: flex; flex-direction: column; gap: var(--space-4); margin-bottom: var(--space-6);">
                    
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                        <span style="color: var(--text-tertiary);">N° de commande</span>
                        <span style="font-weight: 600; font-family: var(--font-mono);">
                            <?= e($order['order_number']) ?>
                        </span>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                        <span style="color: var(--text-tertiary);">Date</span>
                        <span style="font-weight: 600;">
                            <?= date('d/m/Y à H:i', strtotime($order['paid_at'])) ?>
                        </span>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                        <span style="color: var(--text-tertiary);">Articles</span>
                        <span style="font-weight: 600;">
                            <?= count($order['items']) ?>
                        </span>
                    </div>

                    <div style="
                        padding-top: var(--space-4);
                        border-top: 2px solid var(--border-color);
                        display: flex;
                        justify-content: space-between;
                        font-size: 1.25rem;
                    ">
                        <span style="font-weight: 700;">Total payé</span>
                        <span style="font-weight: 700; color: var(--primary-600);">
                            <?= formatPrice($order['total_amount']) ?>
                        </span>
                    </div>

                </div>

                <!-- Méthode de paiement -->
                <div style="
                    padding: var(--space-4);
                    background: var(--bg-secondary);
                    border-radius: var(--radius);
                ">
                    <div style="font-size: 0.75rem; color: var(--text-tertiary); margin-bottom: var(--space-1);">
                        Méthode de paiement
                    </div>
                    <div style="display: flex; align-items: center; gap: var(--space-2);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                            <path d="M2 10H22" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <span style="font-weight: 600;">Carte bancaire</span>
                        <span style="font-size: 0.75rem; color: var(--success); margin-left: auto;">
                            ✓ Sécurisé
                        </span>
                    </div>
                </div>

            </div>

            <!-- Actions -->
            <div style="display: flex; flex-direction: column; gap: var(--space-3);">
                <a href="/orders/<?= e($order['order_number']) ?>" class="btn btn-primary" style="width: 100%;">
                    📋 Voir ma commande
                </a>
                <a href="/products" class="btn btn-secondary" style="width: 100%;">
                    🛍️ Continuer mes achats
                </a>
            </div>

            <!-- Support -->
            <div style="
                margin-top: var(--space-6);
                padding: var(--space-4);
                background: var(--bg-secondary);
                border-radius: var(--radius);
                text-align: center;
                font-size: 0.875rem;
            ">
                <div style="margin-bottom: var(--space-2);">
                    Besoin d'aide ?
                </div>
                <a href="/contact" style="color: var(--primary-600); font-weight: 600;">
                    Contactez le support
                </a>
            </div>

        </aside>

    </div>

</div>

<!-- JavaScript -->
<script>
// Confettis au chargement (optionnel - nécessite une lib)
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎉 Commande confirmée !');
    
    // Animation des cards
    document.querySelectorAll('.card').forEach((card, index) => {
        card.style.animation = `fadeIn 0.6s ease-out ${index * 0.1}s both`;
    });
});

// Copier la clé de licence au clic
document.querySelectorAll('[data-license]').forEach(el => {
    el.addEventListener('click', function() {
        const license = this.dataset.license;
        navigator.clipboard.writeText(license).then(() => {
            MarketFlow.Toast.show('Clé copiée dans le presse-papier', 'success');
        });
    });
});
</script>
<style>
/* === DESIGN MAQUETTE2 — PAIEMENT SUCCESS === */
body { background: #faf9f5 !important; }

/* Cercle succès */
div[style*="background: var(--gradient-success)"] {
    background: #3a7d44 !important;
    box-shadow: none !important;
}

/* Titre principal */
h1[style*="font-size: 2.5rem"] {
    font-family: Georgia, serif !important;
    font-weight: 400 !important;
    color: #1e1208 !important;
    font-size: 28px !important;
}

/* N° commande inline */
p[style*="font-size: 1.25rem; color: var(--text-secondary)"] {
    font-family: 'Manrope', sans-serif !important;
    font-size: 14px !important;
    color: #6b5c4e !important;
}
strong[style*="color: var(--primary-600)"] { color: #7c6cf0 !important; }

/* Cards */
.card {
    background: #fff !important;
    border: 0.5px solid #ede8df !important;
    border-radius: 14px !important;
    box-shadow: none !important;
}

/* Titres h2 h3 dans cards */
h2[style*="font-size: 1.5rem"] { font-family: Georgia, serif !important; font-weight: 400 !important; color: #1e1208 !important; font-size: 18px !important; }
h3[style*="margin-bottom: var(--space-6)"] { font-family: Georgia, serif !important; font-weight: 400 !important; color: #1e1208 !important; font-size: 16px !important; }
h3[style*="margin-bottom: var(--space-4)"] { font-family: Georgia, serif !important; font-weight: 400 !important; color: #1e1208 !important; font-size: 15px !important; }

/* Produits — titre */
h3[style*="font-size: 1.125rem"] { font-family: Georgia, serif !important; font-weight: 400 !important; color: #1e1208 !important; font-size: 15px !important; }

/* Vendeur */
p[style*="font-size: 0.875rem; color: var(--text-tertiary)"] { font-family: 'Manrope', sans-serif !important; font-size: 11px !important; color: #a0907e !important; }

/* Badge disponible */
div[style*="background: var(--success-light)"][style*="color: #065f46"] {
    background: #e4f1d8 !important;
    color: #2d6235 !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 11px !important;
    border-radius: 6px !important;
}

/* Bouton télécharger */
.btn.btn-primary.btn-sm {
    background: #7c6cf0 !important;
    color: #fff !important;
    border: none !important;
    border-radius: 7px !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 12px !important;
    box-shadow: none !important;
}
.btn.btn-primary.btn-sm:hover { background: #6558d4 !important; }

/* Clé licence */
div[style*="font-size: 0.75rem; color: var(--text-tertiary)"][style*="margin-top"] {
    color: #a0907e !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 11px !important;
}

/* Séparateurs items */
div[style*="border-bottom: 1px solid var(--border-color)"] { border-bottom: 0.5px solid #f0ece4 !important; }

/* Card infos importantes */
.card[style*="background: var(--primary-50)"] { background: #f5f3ff !important; border: 0.5px solid #ddd6c8 !important; }
ul[style*="color: var(--primary-700)"] li { color: #534ab7 !important; font-family: 'Manrope', sans-serif !important; font-size: 12px !important; }
a[style*="color: var(--primary-700)"] { color: #7c6cf0 !important; }

/* Sidebar récap — labels */
span[style*="color: var(--text-tertiary)"][style*="font-size: 0.875rem"] { color: #a0907e !important; font-family: 'Manrope', sans-serif !important; font-size: 12px !important; }
span[style*="font-weight: 600; font-family: var(--font-mono)"] { font-family: 'Manrope', monospace, sans-serif !important; font-size: 12px !important; color: #1e1208 !important; }

/* Total payé */
div[style*="border-top: 2px solid var(--border-color)"] { border-top: 0.5px solid #ede8df !important; }
span[style*="font-weight: 700; color: var(--primary-600)"] { color: #7c6cf0 !important; font-family: Georgia, serif !important; font-weight: 400 !important; font-size: 20px !important; }
span[style*="font-weight: 700"][style*="font-size: 1.25rem"] { font-family: Georgia, serif !important; font-weight: 400 !important; color: #1e1208 !important; }

/* Méthode paiement */
div[style*="background: var(--bg-secondary)"][style*="border-radius: var(--radius)"] {
    background: #faf9f5 !important;
    border: 0.5px solid #ede8df !important;
    border-radius: 10px !important;
}
div[style*="font-size: 0.75rem; color: var(--text-tertiary); margin-bottom"] { color: #a0907e !important; font-family: 'Manrope', sans-serif !important; font-size: 11px !important; }
span[style*="color: var(--success)"][style*="margin-left: auto"] { color: #3a7d44 !important; font-size: 11px !important; }

/* Boutons actions */
.btn.btn-primary[style*="width: 100%"] { background: #7c6cf0 !important; border-radius: 10px !important; font-family: 'Manrope', sans-serif !important; box-shadow: none !important; }
.btn.btn-secondary[style*="width: 100%"] { background: #f5f1eb !important; color: #6b5c4e !important; border: 0.5px solid #ddd6c8 !important; border-radius: 10px !important; font-family: 'Manrope', sans-serif !important; box-shadow: none !important; }

/* Support sidebar */
div[style*="margin-top: var(--space-6)"][style*="text-align: center"] {
    background: #faf9f5 !important;
    border: 0.5px solid #ede8df !important;
    border-radius: 12px !important;
}
a[href="/contact"][style*="color: var(--primary-600)"] { color: #7c6cf0 !important; font-family: 'Manrope', sans-serif !important; }

/* Responsive */
@media (max-width: 1024px) {
    [style*="grid-template-columns: 1fr 400px"] { grid-template-columns: 1fr !important; }
}
@media (max-width: 768px) {
    [style*="grid-template-columns: 100px 1fr auto"] { grid-template-columns: 1fr !important; }
}
</style>
