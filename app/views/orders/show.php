<?php
/**
 * MARKETFLOW PRO - DÉTAIL COMMANDE
 * Fichier : app/views/orders/show.php
 */
?>

<div class="container mt-8 mb-16">
    
    <!-- Header -->
    <div class="mb-8">
        <a href="/orders" style="color: var(--text-secondary); font-size: 0.875rem; display: inline-flex; align-items: center; gap: var(--space-2); margin-bottom: var(--space-4);">
            ← Retour à mes commandes
        </a>
        <h1>Commande #<?= e($order['order_number']) ?></h1>
        <div style="display: flex; align-items: center; gap: var(--space-4); margin-top: var(--space-3);">
            <span style="color: var(--text-secondary);">
                Passée le <?= date('d/m/Y à H:i', strtotime($order['created_at'])) ?>
            </span>
            
            <?php if ($order['payment_status'] === 'completed'): ?>
                <span class="badge badge-success" style="font-size: 0.875rem;">✓ Payée</span>
            <?php elseif ($order['payment_status'] === 'pending'): ?>
                <span class="badge badge-warning" style="font-size: 0.875rem;">⏳ En attente</span>
            <?php elseif ($order['payment_status'] === 'failed'): ?>
                <span class="badge badge-error" style="font-size: 0.875rem;">✕ Échouée</span>
            <?php elseif ($order['payment_status'] === 'refunded'): ?>
                <span class="badge" style="background: var(--text-tertiary); color: white; font-size: 0.875rem;">↩ Remboursée</span>
            <?php endif; ?>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 400px; gap: var(--space-8);">
        
        <!-- Colonne principale - Produits -->
        <div>
            
            <!-- Produits commandés -->
            <div class="card" style="padding: var(--space-8); margin-bottom: var(--space-6);">
                
                <h2 style="font-size: 1.5rem; margin-bottom: var(--space-6);">
                    📦 Produits
                </h2>

                <div style="display: flex; flex-direction: column; gap: var(--space-6);">
                    
                    <?php foreach ($order['items'] as $item): ?>
                    <div style="
                        display: grid;
                        grid-template-columns: 120px 1fr auto;
                        gap: var(--space-6);
                        padding-bottom: var(--space-6);
                        border-bottom: 1px solid var(--border-color);
                    ">
                        
                        <!-- Image -->
                        <a href="/products/<?= e($item['slug']) ?>">
                            <img 
                                src="<?= e($item['thumbnail']) ?>" 
                                alt="<?= e($item['product_title']) ?>"
                                style="width: 120px; height: 80px; object-fit: cover; border-radius: var(--radius);"
                            >
                        </a>

                        <!-- Infos -->
                        <div>
                            <h3 style="font-size: 1.125rem; margin-bottom: var(--space-2);">
                                <a href="/products/<?= e($item['slug']) ?>" style="color: inherit;">
                                    <?= e($item['product_title']) ?>
                                </a>
                            </h3>
                            
                            <p style="font-size: 0.875rem; color: var(--text-tertiary); margin-bottom: var(--space-3);">
                                Par 
                                <a href="/seller/<?= e($item['seller_username']) ?>" style="color: var(--primary-600);">
                                    <?= e($item['shop_name'] ?? $item['seller_username']) ?>
                                </a>
                            </p>

                            <!-- Prix -->
                            <div style="font-size: 1.25rem; font-weight: 700; color: var(--primary-600); margin-bottom: var(--space-4);">
                                <?= formatPrice($item['price']) ?>
                            </div>

                            <!-- Clé de licence -->
                            <div style="
                                padding: var(--space-3);
                                background: var(--bg-secondary);
                                border-radius: var(--radius);
                                font-size: 0.875rem;
                            ">
                                <div style="font-weight: 600; margin-bottom: var(--space-1); color: var(--text-secondary);">
                                    🔑 Clé de licence
                                </div>
                                <div style="
                                    font-family: var(--font-mono);
                                    font-weight: 600;
                                    color: var(--text-primary);
                                    display: flex;
                                    align-items: center;
                                    gap: var(--space-2);
                                ">
                                    <span id="license-<?= e($item['id']) ?>"><?= e($item['license_key']) ?></span>
                                    <button 
                                        onclick="copyLicense('<?= e($item['license_key']) ?>', <?= e($item['id']) ?>)"
                                        class="btn btn-ghost btn-sm"
                                        style="padding: var(--space-1) var(--space-2);"
                                        title="Copier"
                                    >
                                        📋
                                    </button>
                                </div>
                            </div>

                        </div>

                        <!-- Actions téléchargement -->
                        <div style="text-align: right;">
                            
                            <?php if ($order['payment_status'] === 'completed'): ?>
                                
                                <a 
                                    href="/orders/<?= e($order['order_number']) ?>/download/<?= e($item['id']) ?>" 
                                    class="btn btn-primary"
                                    style="margin-bottom: var(--space-3);"
                                >
                                    📥 Télécharger
                                </a>

                                <div style="font-size: 0.75rem; color: var(--text-tertiary);">
                                    <?= e($item['download_count']) ?>/<?= e($item['download_limit']) ?> téléchargements
                                </div>

                                <!-- Laisser un avis -->
                                <?php
                                // Vérifier si un avis existe déjà
                                $stmt = $GLOBALS['db']->prepare("SELECT id FROM reviews WHERE order_item_id = ?");
                                $stmt->execute([$item['id']]);
                                $hasReview = $stmt->fetch();
                                ?>

                                <?php if (!$hasReview): ?>
                                <button 
                                    onclick="openReviewModal(<?= e($item['id']) ?>, <?= e($item['product_id']) ?>, '<?= e($item['product_title']) ?>')"
                                    class="btn btn-ghost btn-sm"
                                    style="margin-top: var(--space-2);"
                                >
                                    ⭐ Laisser un avis
                                </button>
                                <?php else: ?>
                                <div style="margin-top: var(--space-2); color: var(--success); font-size: 0.75rem;">
                                    ✓ Avis laissé
                                </div>
                                <?php endif; ?>

                            <?php else: ?>
                                <div style="
                                    padding: var(--space-3);
                                    background: var(--warning-light);
                                    border-radius: var(--radius);
                                    color: #92400e;
                                    font-size: 0.875rem;
                                ">
                                    En attente de paiement
                                </div>
                            <?php endif; ?>

                        </div>

                    </div>
                    <?php endforeach; ?>

                </div>

            </div>

            <!-- Actions supplémentaires -->
            <?php if ($order['payment_status'] === 'completed'): ?>
            <div class="card" style="padding: var(--space-6);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="font-size: 1rem; margin-bottom: var(--space-1);">
                            Besoin d'aide ?
                        </h3>
                        <p style="font-size: 0.875rem; color: var(--text-tertiary); margin: 0;">
                            Un problème avec votre commande ?
                        </p>
                    </div>
                    <div style="display: flex; gap: var(--space-3);">
                        <a href="/orders/<?= e($order['order_number']) ?>/invoice" class="btn btn-secondary">
                            📄 Télécharger la facture
                        </a>
                        <button onclick="openRefundModal()" class="btn btn-outline">
                            Demander un remboursement
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- Sidebar - Récapitulatif -->
        <aside>
            
            <!-- Résumé commande -->
            <div class="card" style="padding: var(--space-6); margin-bottom: var(--space-6);">
                
                <h3 style="margin-bottom: var(--space-6); font-size: 1.25rem;">
                    Résumé
                </h3>

                <div style="display: flex; flex-direction: column; gap: var(--space-4); margin-bottom: var(--space-6);">
                    
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                        <span style="color: var(--text-tertiary);">Articles</span>
                        <span style="font-weight: 600;">
                            <?= count($order['items']) ?>
                        </span>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                        <span style="color: var(--text-tertiary);">Commission plateforme</span>
                        <span style="font-weight: 600;">
                            <?= formatPrice($order['platform_fee']) ?>
                        </span>
                    </div>

                    <div style="
                        padding-top: var(--space-4);
                        border-top: 2px solid var(--border-color);
                        display: flex;
                        justify-content: space-between;
                        font-size: 1.5rem;
                    ">
                        <span style="font-weight: 700;">Total</span>
                        <span style="font-weight: 700; color: var(--primary-600);">
                            <?= formatPrice($order['total_amount']) ?>
                        </span>
                    </div>

                </div>

                <!-- Méthode de paiement -->
                <?php if ($order['payment_status'] === 'completed'): ?>
                <div style="
                    padding: var(--space-4);
                    background: var(--success-light);
                    border-radius: var(--radius);
                ">
                    <div style="font-size: 0.75rem; font-weight: 600; color: #065f46; margin-bottom: var(--space-2);">
                        💳 Paiement effectué
                    </div>
                    <div style="font-size: 0.875rem; color: #065f46;">
                        Le <?= date('d/m/Y à H:i', strtotime($order['paid_at'])) ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Informations -->
            <div class="card" style="padding: var(--space-6); background: var(--primary-50);">
                <h3 style="margin-bottom: var(--space-4); font-size: 1rem;">
                    ℹ️ Informations
                </h3>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: var(--space-3); font-size: 0.875rem; color: var(--primary-700);">
                    <li>• Vos produits sont téléchargeables immédiatement</li>
                    <li>• Limite de <?= $order['items'][0]['download_limit'] ?? 3 ?> téléchargements par produit</li>
                    <li>• Conservez vos clés de licence</li>
                    <li>• Support disponible 7j/7</li>
                </ul>
            </div>

        </aside>

    </div>

</div>

<!-- Modal Avis -->
<div id="reviewModal" style="display: none;">
    <!-- Le modal sera créé dynamiquement -->
</div>

<!-- Modal Remboursement -->
<div id="refundModal" style="display: none;">
    <!-- Le modal sera créé dynamiquement -->
</div>

<!-- JavaScript -->
<script>
// Copier la clé de licence
function copyLicense(key, itemId) {
    navigator.clipboard.writeText(key).then(() => {
        MarketFlow.Toast.show('Clé copiée dans le presse-papier', 'success');
    }).catch(() => {
        MarketFlow.Toast.show('Erreur lors de la copie', 'error');
    });
}

// Ouvrir modal d'avis
function openReviewModal(orderItemId, productId, productTitle) {
    const modal = new MarketFlow.Modal('review-modal');
    
    const content = `
        <form method="POST" action="/orders/review" id="reviewForm">
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
            <input type="hidden" name="order_item_id" value="${orderItemId}">
            <input type="hidden" name="product_id" value="${productId}">
            
            <h3 style="margin-bottom: var(--space-4);">Votre avis sur :</h3>
            <p style="font-weight: 600; margin-bottom: var(--space-6);">${productTitle}</p>
            
            <div class="form-group">
                <label class="form-label">Note *</label>
                <div style="display: flex; gap: var(--space-2); font-size: 2rem;">
                    ${[1,2,3,4,5].map(i => `
                        <span class="star" data-rating="${i}" onclick="setRating(${i})" style="cursor: pointer; color: var(--border-color);">★</span>
                    `).join('')}
                </div>
                <input type="hidden" name="rating" id="rating" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="comment">Votre commentaire</label>
                <textarea 
                    id="comment" 
                    name="comment" 
                    class="form-textarea" 
                    rows="5"
                    placeholder="Partagez votre expérience avec ce produit..."
                ></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Publier l'avis
            </button>
        </form>
    `;
    
    modal.create(content, { title: 'Laisser un avis', size: 'md' });
}

// Définir la note
function setRating(rating) {
    document.getElementById('rating').value = rating;
    document.querySelectorAll('.star').forEach((star, index) => {
        star.style.color = index < rating ? 'var(--warning)' : 'var(--border-color)';
    });
}

// Ouvrir modal de remboursement
function openRefundModal() {
    const modal = new MarketFlow.Modal('refund-modal');
    
    const content = `
        <form method="POST" action="/orders/refund" id="refundForm">
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
            <input type="hidden" name="order_number" value="<?= e($order['order_number']) ?>">
            
            <div style="padding: var(--space-4); background: var(--warning-light); border-radius: var(--radius); margin-bottom: var(--space-6);">
                <strong>⚠️ Attention</strong><br>
                <span style="font-size: 0.875rem;">Les demandes de remboursement sont traitées sous 48h. Assurez-vous d'avoir un motif valable.</span>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="reason">Motif du remboursement *</label>
                <textarea 
                    id="reason" 
                    name="reason" 
                    class="form-textarea" 
                    rows="4"
                    placeholder="Expliquez pourquoi vous souhaitez être remboursé..."
                    required
                ></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Envoyer la demande
            </button>
        </form>
    `;
    
    modal.create(content, { title: 'Demander un remboursement', size: 'md' });
}

// Animation au chargement
document.querySelectorAll('.card').forEach((card, index) => {
    card.style.animation = `fadeIn 0.5s ease-out ${index * 0.1}s both`;
});
</script>

<style>
/* Responsive */
@media (max-width: 1024px) {
    [style*="grid-template-columns: 1fr 400px"] {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 768px) {
    [style*="grid-template-columns: 120px 1fr auto"] {
        grid-template-columns: 1fr !important;
    }
    
    [style*="text-align: right"] {
        text-align: left !important;
    }
}
</style>