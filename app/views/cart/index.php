<?php
/**
 * MARKETFLOW PRO - PAGE PANIER
 * Fichier : app/views/cart/index.php
 */
?>

<div class="container mt-8 mb-16">
    
    <!-- Header -->
    <div class="mb-8">
        <h1>Mon Panier</h1>
        <p style="color: var(--text-secondary); margin-top: var(--space-2);">
            <?= $cart['count'] ?> article<?= $cart['count'] > 1 ? 's' : '' ?> dans votre panier
        </p>
    </div>

    <?php if (empty($cart['items'])): ?>
        
        <!-- Panier vide -->
        <div class="card text-center" style="padding: var(--space-16);">
            <div style="font-size: 5rem; margin-bottom: var(--space-6);">🛒</div>
            <h2 style="margin-bottom: var(--space-4);">Votre panier est vide</h2>
            <p style="color: var(--text-secondary); margin-bottom: var(--space-8); font-size: 1.125rem;">
                Découvrez nos produits et commencez vos achats !
            </p>
            <a href="/products" class="btn btn-primary btn-lg">
                Découvrir les produits
            </a>
        </div>

    <?php else: ?>

        <div style="display: grid; grid-template-columns: 1fr 400px; gap: var(--space-8);">
            
            <!-- Liste des produits -->
            <div>
                
                <?php foreach ($cart['items'] as $item): ?>
                <div class="card" style="padding: var(--space-6); margin-bottom: var(--space-4);">
                    
                    <div style="display: grid; grid-template-columns: 120px 1fr auto; gap: var(--space-6); align-items: center;">
                        
                        <!-- Image -->
                        <a href="/products/<?= e($item['slug']) ?>">
                            <img 
                                src="<?= e($item['thumbnail']) ?>" 
                                alt="<?= e($item['title']) ?>"
                                style="width: 120px; height: 80px; object-fit: cover; border-radius: var(--radius);"
                            >
                        </a>

                        <!-- Infos produit -->
                        <div>
                            <h3 style="margin-bottom: var(--space-2);">
                                <a href="/products/<?= e($item['slug']) ?>" style="color: inherit;">
                                    <?= e($item['title']) ?>
                                </a>
                            </h3>
                            <p style="font-size: 0.875rem; color: var(--text-tertiary); margin-bottom: var(--space-3);">
                                Par 
                                <a href="/seller/<?= e($item['seller_name']) ?>" style="color: var(--primary-600);">
                                    <?= e($item['shop_name'] ?? $item['seller_name']) ?>
                                </a>
                            </p>
                            <p style="font-size: 1.25rem; font-weight: 700; color: var(--primary-600);">
                                <?= formatPrice($item['price']) ?>
                            </p>
                        </div>

                        <!-- Actions -->
                        <div style="text-align: right;">
                            <form method="POST" action="/cart/remove" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                <button 
                                    type="submit" 
                                    class="btn btn-ghost btn-sm"
                                    onclick="return confirm('Retirer ce produit du panier ?')"
                                    style="color: var(--error);"
                                >
                                    🗑️ Retirer
                                </button>
                            </form>
                        </div>

                    </div>

                </div>
                <?php endforeach; ?>

                <!-- Bouton vider le panier -->
                <div style="margin-top: var(--space-6);">
                    <form method="POST" action="/cart/clear" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <button 
                            type="submit" 
                            class="btn btn-ghost"
                            onclick="return confirm('Vider complètement le panier ?')"
                        >
                            🗑️ Vider le panier
                        </button>
                    </form>
                </div>

            </div>

            <!-- Sidebar résumé -->
            <aside style="position: sticky; top: 100px; height: fit-content;">
                
                <div class="card" style="padding: var(--space-6);">
                    
                    <h2 style="font-size: 1.5rem; margin-bottom: var(--space-6); padding-bottom: var(--space-4); border-bottom: 1px solid var(--border-color);">
                        Résumé
                    </h2>

                    <!-- Code promo -->
                    <?php if (!$promo): ?>
                    <div style="margin-bottom: var(--space-6);">
                        <form method="POST" action="/cart/apply-promo" id="promoForm">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <label class="form-label" for="promo_code">Code promo</label>
                            <div style="display: flex; gap: var(--space-2);">
                                <input 
                                    type="text" 
                                    name="promo_code" 
                                    id="promo_code"
                                    class="form-input"
                                    placeholder="PROMO2024"
                                    style="flex: 1;"
                                >
                                <button type="submit" class="btn btn-secondary">
                                    Appliquer
                                </button>
                            </div>
                        </form>
                    </div>
                    <?php else: ?>
                    <div style="
                        margin-bottom: var(--space-6);
                        padding: var(--space-4);
                        background: var(--success-light);
                        border: 1px solid var(--success);
                        border-radius: var(--radius);
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    ">
                        <div>
                            <div style="font-weight: 600; color: #065f46;">
                                ✓ Code promo appliqué
                            </div>
                            <div style="font-size: 0.875rem; color: #065f46;">
                                <?= e($promo['code']) ?>
                                <?php if ($promo['type'] === 'percentage'): ?>
                                    (-<?= $promo['value'] ?>%)
                                <?php else: ?>
                                    (-<?= formatPrice($promo['value']) ?>)
                                <?php endif; ?>
                            </div>
                        </div>
                        <a href="/cart/remove-promo" style="color: var(--error); text-decoration: none;">
                            ✕
                        </a>
                    </div>
                    <?php endif; ?>

                    <!-- Calculs -->
                    <div style="display: flex; flex-direction: column; gap: var(--space-4); margin-bottom: var(--space-6);">
                        
                        <div style="display: flex; justify-content: space-between; font-size: 0.9375rem;">
                            <span style="color: var(--text-secondary);">Sous-total</span>
                            <span style="font-weight: 600;"><?= formatPrice($cart['subtotal']) ?></span>
                        </div>

                        <?php if ($promo): ?>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9375rem; color: var(--success);">
                            <span>Réduction</span>
                            <span style="font-weight: 600;">-<?= formatPrice($promo['discount']) ?></span>
                        </div>
                        <?php endif; ?>

                        <div style="
                            padding-top: var(--space-4);
                            border-top: 1px solid var(--border-color);
                            display: flex;
                            justify-content: space-between;
                            font-size: 1.25rem;
                        ">
                            <span style="font-weight: 700;">Total</span>
                            <span style="font-weight: 700; color: var(--primary-600);">
                                <?php 
                                $total = $cart['subtotal'] - ($promo ? $promo['discount'] : 0);
                                echo formatPrice($total); 
                                ?>
                            </span>
                        </div>

                    </div>

                    <!-- Bouton commander -->
                    <button onclick="proceedToCheckout()" class="btn btn-primary btn-lg" id="checkoutBtn">
    Passer commande
</button>

<script>
async function proceedToCheckout() {
    const btn = document.getElementById('checkoutBtn');
    btn.disabled = true;
    btn.textContent = 'Redirection...';
    
    try {
        const response = await fetch('/checkout/create-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.checkout_url) {
            window.location.href = data.checkout_url;
        } else {
            alert('Erreur : ' + (data.error || 'Erreur inconnue'));
            btn.disabled = false;
            btn.textContent = 'Passer commande';
        }
    } catch (error) {
        alert('Erreur de connexion');
        btn.disabled = false;
        btn.textContent = 'Passer commande';
    }
}
</script>

                    <!-- Infos rassurantes -->
                    <div style="
                        margin-top: var(--space-6);
                        padding: var(--space-4);
                        background: var(--primary-50);
                        border-radius: var(--radius);
                        font-size: 0.875rem;
                    ">
                        <div style="display: flex; flex-direction: column; gap: var(--space-2); color: var(--primary-700);">
                            <div>✓ Paiement 100% sécurisé</div>
                            <div>✓ Téléchargement instantané</div>
                            <div>✓ Garantie satisfait ou remboursé</div>
                        </div>
                    </div>

                </div>

            </aside>

        </div>

    <?php endif; ?>

</div>

<!-- JavaScript -->
<script>
// Animation au chargement
document.querySelectorAll('.card').forEach((card, index) => {
    card.style.animation = `fadeIn 0.5s ease-out ${index * 0.1}s both`;
});

// Confirmation avant de vider le panier
document.querySelectorAll('form[action="/cart/clear"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir vider votre panier ?')) {
            e.preventDefault();
        }
    });
});
</script>

<style>
/* Responsive */
@media (max-width: 1024px) {
    [style*="grid-template-columns: 1fr 400px"] {
        grid-template-columns: 1fr !important;
    }
    
    aside {
        position: relative !important;
        top: 0 !important;
    }
}

@media (max-width: 768px) {
    [style*="grid-template-columns: 120px 1fr auto"] {
        grid-template-columns: 1fr !important;
    }
    
    .card > div > a {
        margin-bottom: var(--space-4);
    }
}
</style>