<?php
/**
 * MARKETFLOW PRO - PAGE CHECKOUT
 * Fichier : app/views/cart/checkout.php
 */
?>

<!-- Script Stripe -->
<script src="https://js.stripe.com/v3/"></script>

<div class="container mt-8 mb-16">
    
    <!-- Header -->
    <div class="mb-8">
        <h1>Paiement</h1>
        <p style="color: var(--text-secondary); margin-top: var(--space-2);">
            Finalisez votre commande en toute sécurité
        </p>
    </div>

    <!-- Breadcrumb steps -->
    <div style="
        display: flex;
        justify-content: center;
        gap: var(--space-8);
        margin-bottom: var(--space-12);
    ">
        <div style="display: flex; align-items: center; gap: var(--space-2); color: var(--success);">
            <div style="
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: var(--success);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
            ">✓</div>
            <span style="font-weight: 600;">Panier</span>
        </div>
        <div style="width: 60px; height: 2px; background: var(--primary-600); align-self: center;"></div>
        <div style="display: flex; align-items: center; gap: var(--space-2); color: var(--primary-600);">
            <div style="
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: var(--primary-600);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
            ">2</div>
            <span style="font-weight: 600;">Paiement</span>
        </div>
        <div style="width: 60px; height: 2px; background: var(--border-color); align-self: center;"></div>
        <div style="display: flex; align-items: center; gap: var(--space-2); color: var(--text-tertiary);">
            <div style="
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: var(--bg-tertiary);
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
            ">3</div>
            <span>Confirmation</span>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 450px; gap: var(--space-8);">
        
        <!-- Colonne gauche - Informations -->
        <div>
            
            <!-- Informations client -->
            <div class="card" style="padding: var(--space-8); margin-bottom: var(--space-6);">
                <h2 style="font-size: 1.5rem; margin-bottom: var(--space-6);">
                    📋 Vos informations
                </h2>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-6);">
                    <div>
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-tertiary); display: block; margin-bottom: var(--space-2);">
                            Nom complet
                        </label>
                        <div style="font-size: 1rem; font-weight: 600;">
                            <?= e($user['full_name'] ?? $user['username']) ?>
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 0.875rem; font-weight: 600; color: var(--text-tertiary); display: block; margin-bottom: var(--space-2);">
                            Email
                        </label>
                        <div style="font-size: 1rem; font-weight: 600;">
                            <?= e($user['email']) ?>
                        </div>
                    </div>
                </div>

                <a href="/profile" style="
                    display: inline-block;
                    margin-top: var(--space-4);
                    font-size: 0.875rem;
                    color: var(--primary-600);
                ">
                    Modifier mes informations →
                </a>
            </div>

            <!-- Récapitulatif commande -->
            <div class="card" style="padding: var(--space-8);">
                <h2 style="font-size: 1.5rem; margin-bottom: var(--space-6);">
                    🛍️ Récapitulatif de la commande
                </h2>

                <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                    
                    <?php foreach ($cart['items'] as $item): ?>
                    <div style="display: grid; grid-template-columns: 60px 1fr auto; gap: var(--space-4); align-items: center;">
                        <img 
                            src="<?= e($item['thumbnail']) ?>" 
                            alt="<?= e($item['title']) ?>"
                            style="width: 60px; height: 40px; object-fit: cover; border-radius: var(--radius-sm);"
                        >
                        <div>
                            <div style="font-weight: 600; margin-bottom: var(--space-1);">
                                <?= e($item['title']) ?>
                            </div>
                            <div style="font-size: 0.875rem; color: var(--text-tertiary);">
                                Par <?= e($item['shop_name'] ?? $item['seller_name']) ?>
                            </div>
                        </div>
                        <div style="font-weight: 700; color: var(--primary-600);">
                            <?= formatPrice($item['price']) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </div>
            </div>

        </div>

        <!-- Colonne droite - Paiement -->
        <aside>
            
            <!-- Résumé montants -->
            <div class="card" style="padding: var(--space-6); margin-bottom: var(--space-6);">
                
                <h2 style="font-size: 1.25rem; margin-bottom: var(--space-6);">
                    Montant à payer
                </h2>

                <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                    
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);">Sous-total</span>
                        <span style="font-weight: 600;"><?= formatPrice($subtotal) ?></span>
                    </div>

                    <?php if ($discount > 0): ?>
                    <div style="display: flex; justify-content: space-between; color: var(--success);">
                        <span>Réduction <?= $promo ? '(' . e($promo['code']) . ')' : '' ?></span>
                        <span style="font-weight: 600;">-<?= formatPrice($discount) ?></span>
                    </div>
                    <?php endif; ?>

                    <div style="
                        padding-top: var(--space-4);
                        border-top: 2px solid var(--border-color);
                        display: flex;
                        justify-content: space-between;
                        font-size: 1.5rem;
                    ">
                        <span style="font-weight: 700;">Total</span>
                        <span style="font-weight: 700; color: var(--primary-600);">
                            <?= formatPrice($total) ?>
                        </span>
                    </div>

                </div>

            </div>

            <!-- Formulaire de paiement Stripe -->
            <div class="card" style="padding: var(--space-6);">
                
                <h2 style="font-size: 1.25rem; margin-bottom: var(--space-4);">
                    💳 Paiement sécurisé
                </h2>

                <form id="payment-form">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
                    
                    <!-- Element Stripe Card -->
                    <div id="card-element" style="
                        padding: var(--space-4);
                        border: 1px solid var(--border-color);
                        border-radius: var(--radius);
                        background: var(--bg-secondary);
                        margin-bottom: var(--space-4);
                    "></div>

                    <!-- Erreurs de carte -->
                    <div id="card-errors" style="
                        color: var(--error);
                        font-size: 0.875rem;
                        margin-bottom: var(--space-4);
                        min-height: 20px;
                    "></div>

                    <!-- Bouton payer -->
                    <button 
                        type="submit" 
                        id="submit-button"
                        class="btn btn-primary" 
                        style="width: 100%; font-size: 1.125rem;"
                    >
                        <span id="button-text">Payer <?= formatPrice($total) ?></span>
                        <span id="spinner" style="display: none;">
                            ⏳ Traitement en cours...
                        </span>
                    </button>

                    <!-- Logos paiement -->
                    <div style="
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        gap: var(--space-3);
                        margin-top: var(--space-4);
                        font-size: 0.75rem;
                        color: var(--text-tertiary);
                    ">
                        <span>Propulsé par</span>
                        <svg height="20" viewBox="0 0 60 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M59.64 14.28h-8.06c.19 1.93 1.6 2.55 3.2 2.55 1.64 0 2.96-.37 4.05-.95v3.32a8.33 8.33 0 0 1-4.56 1.1c-4.01 0-6.83-2.5-6.83-7.48 0-4.19 2.39-7.52 6.3-7.52 3.92 0 5.96 3.28 5.96 7.5 0 .4-.04 1.26-.06 1.48zm-5.92-5.62c-1.03 0-2.17.73-2.17 2.58h4.25c0-1.85-1.07-2.58-2.08-2.58zM40.95 20.3c-1.44 0-2.32-.6-2.9-1.04l-.02 4.63-4.12.87V5.57h3.76l.08 1.02a4.7 4.7 0 0 1 3.23-1.29c2.9 0 5.62 2.6 5.62 7.4 0 5.23-2.7 7.6-5.65 7.6zM40 8.95c-.95 0-1.54.34-1.97.81l.02 6.12c.4.44.98.78 1.95.78 1.52 0 2.54-1.65 2.54-3.87 0-2.15-1.04-3.84-2.54-3.84zM28.24 5.57h4.13v14.44h-4.13V5.57zm0-4.7L32.37 0v3.36l-4.13.88V.88zm-4.32 9.35v9.79H19.8V5.57h3.7l.12 1.22c1-1.77 3.07-1.41 3.62-1.22v3.79c-.52-.17-2.29-.43-3.32.86zm-8.55 4.72c0 2.43 2.6 1.68 3.12 1.46v3.36c-.55.3-1.54.54-2.89.54a4.15 4.15 0 0 1-4.27-4.24l.01-13.17 4.02-.86v3.54h3.14V9.1h-3.13v5.85zm-4.91.7c0 2.97-2.31 4.66-5.73 4.66a11.2 11.2 0 0 1-4.46-.93v-3.93c1.38.75 3.1 1.31 4.46 1.31.92 0 1.53-.24 1.53-1C6.26 13.77 0 14.51 0 9.95 0 7.04 2.28 5.3 5.62 5.3c1.36 0 2.72.2 4.09.75v3.88a9.23 9.23 0 0 0-4.1-1.06c-.86 0-1.44.25-1.44.9 0 1.85 6.29.97 6.29 5.88z" fill="#635BFF"/>
                        </svg>
                    </div>

                </form>

            </div>

            <!-- Infos sécurité -->
            <div style="
                margin-top: var(--space-4);
                padding: var(--space-4);
                background: var(--success-light);
                border-radius: var(--radius);
                font-size: 0.875rem;
            ">
                <div style="display: flex; flex-direction: column; gap: var(--space-2); color: #065f46;">
                    <div style="font-weight: 600; margin-bottom: var(--space-2);">
                        🔒 Paiement 100% sécurisé
                    </div>
                    <div>✓ Cryptage SSL 256 bits</div>
                    <div>✓ Conformité PCI-DSS</div>
                    <div>✓ Vos données ne sont jamais stockées</div>
                </div>
            </div>

        </aside>

    </div>

</div>

<!-- JavaScript Stripe -->
<script>
// Initialiser Stripe
const stripe = Stripe('<?= e($stripe_public_key) ?>');
const elements = stripe.elements();

// Créer l'élément Card
const cardElement = elements.create('card', {
    style: {
        base: {
            fontSize: '16px',
            color: 'var(--text-primary)',
            fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
            '::placeholder': {
                color: 'var(--text-tertiary)',
            },
        },
        invalid: {
            color: 'var(--error)',
            iconColor: 'var(--error)',
        },
    },
});

cardElement.mount('#card-element');

// Gérer les erreurs de validation en temps réel
cardElement.on('change', function(event) {
    const displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});

// Gérer la soumission du formulaire
const form = document.getElementById('payment-form');
const submitButton = document.getElementById('submit-button');
const buttonText = document.getElementById('button-text');
const spinner = document.getElementById('spinner');

form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Désactiver le bouton
    submitButton.disabled = true;
    buttonText.style.display = 'none';
    spinner.style.display = 'inline';

    try {
        // Créer la session Stripe via notre API
        const response = await fetch('/checkout/create-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                csrf_token: '<?= e($csrf_token) ?>'
            })
        });

        const result = await response.json();

        if (result.success) {
            // Rediriger vers Stripe Checkout
            const { error } = await stripe.redirectToCheckout({
                sessionId: result.session_id
            });

            if (error) {
                showError(error.message);
            }
        } else {
            showError(result.error || 'Une erreur est survenue');
        }
    } catch (error) {
        showError('Erreur de connexion au serveur');
    }
});

function showError(message) {
    const displayError = document.getElementById('card-errors');
    displayError.textContent = message;
    
    // Réactiver le bouton
    submitButton.disabled = false;
    buttonText.style.display = 'inline';
    spinner.style.display = 'none';
}

// Animation au chargement
document.querySelectorAll('.card').forEach((card, index) => {
    card.style.animation = `fadeIn 0.5s ease-out ${index * 0.1}s both`;
});
</script>
<style>
/* === DESIGN MAQUETTE2 — CHECKOUT === */
h1 { font-family: Georgia, serif !important; font-weight: 400 !important; color: #1e1208 !important; font-size: 26px !important; }
p[style*="color: var(--text-secondary)"] { font-family: 'Manrope', sans-serif !important; font-size: 13px !important; color: #6b5c4e !important; }

/* Breadcrumb steps */
div[style*="background: var(--success)"] { background: #3a7d44 !important; }
span[style*="font-weight: 600"][style*="color: var(--success)"] { color: #3a7d44 !important; }
div[style*="background: var(--primary-600)"] { background: #7c6cf0 !important; }
span[style*="color: var(--primary-600)"] { color: #7c6cf0 !important; font-family: 'Manrope', sans-serif !important; font-weight: 500 !important; }
div[style*="width: 60px; height: 2px; background: var(--primary-600)"] { background: #7c6cf0 !important; }
div[style*="background: var(--border-color)"][style*="height: 2px"] { background: #ddd6c8 !important; }
div[style*="background: var(--bg-tertiary)"] { background: #f5f1eb !important; }
span[style*="color: var(--text-tertiary)"] { color: #a0907e !important; font-family: 'Manrope', sans-serif !important; }

/* Cards */
.card { background: #fff !important; border: 0.5px solid #ede8df !important; border-radius: 14px !important; box-shadow: none !important; }

/* Infos client */
h2[style*="font-size: 1.5rem"] { font-family: Georgia, serif !important; font-weight: 400 !important; color: #1e1208 !important; font-size: 18px !important; }
label[style*="font-size: 0.875rem"] { font-family: 'Manrope', sans-serif !important; font-size: 11px !important; color: #a0907e !important; font-weight: 500 !important; }
div[style*="font-size: 1rem; font-weight: 600"] { font-family: 'Manrope', sans-serif !important; font-size: 13px !important; color: #1e1208 !important; }
a[style*="color: var(--primary-600)"][style*="margin-top"] { color: #7c6cf0 !important; font-family: 'Manrope', sans-serif !important; font-size: 12px !important; }

/* Items commande */
div[style*="font-weight: 600; margin-bottom"] { font-family: 'Manrope', sans-serif !important; font-size: 13px !important; color: #1e1208 !important; }
div[style*="color: var(--text-tertiary)"][style*="0.875rem"] { color: #a0907e !important; font-size: 11px !important; font-family: 'Manrope', sans-serif !important; }
div[style*="font-weight: 700; color: var(--primary-600)"] { color: #7c6cf0 !important; font-family: Georgia, serif !important; font-weight: 400 !important; font-size: 15px !important; }

/* Résumé montants */
h2[style*="font-size: 1.25rem"] { font-family: Georgia, serif !important; font-weight: 400 !important; color: #1e1208 !important; font-size: 16px !important; }
span[style*="color: var(--text-secondary)"] { color: #6b5c4e !important; font-family: 'Manrope', sans-serif !important; font-size: 13px !important; }
span[style*="font-weight: 600"] { font-family: 'Manrope', sans-serif !important; color: #1e1208 !important; }
div[style*="border-top: 2px solid var(--border-color)"] { border-top: 0.5px solid #ede8df !important; }
span[style*="font-weight: 700; color: var(--primary-600)"] { color: #7c6cf0 !important; font-family: Georgia, serif !important; font-weight: 400 !important; font-size: 22px !important; }
span[style*="font-weight: 700"][style*="font-size: 1.5rem"] { font-family: Georgia, serif !important; font-weight: 400 !important; color: #1e1208 !important; }
div[style*="color: var(--success)"] span { color: #3a7d44 !important; font-size: 13px !important; }

/* Stripe element */
div#card-element { border: 0.5px solid #ddd6c8 !important; border-radius: 10px !important; background: #faf9f5 !important; }
div#card-errors { color: #993c1d !important; font-family: 'Manrope', sans-serif !important; font-size: 12px !important; }

/* Bouton payer */
.btn.btn-primary {
    background: #7c6cf0 !important;
    color: #fff !important;
    border: none !important;
    border-radius: 10px !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 15px !important;
    font-weight: 600 !important;
    box-shadow: none !important;
}
.btn.btn-primary:hover { background: #6558d4 !important; }

/* Logos stripe */
div[style*="font-size: 0.75rem"][style*="color: var(--text-tertiary)"] span { color: #a0907e !important; font-family: 'Manrope', sans-serif !important; }

/* Bloc sécurité */
div[style*="background: var(--success-light)"] {
    background: #e4f1d8 !important;
    border-radius: 12px !important;
}
div[style*="color: #065f46"] { color: #2d6235 !important; font-family: 'Manrope', sans-serif !important; font-size: 12px !important; }
div[style*="font-weight: 600; margin-bottom: var(--space-2)"] { font-family: 'Manrope', sans-serif !important; font-size: 12px !important; font-weight: 600 !important; }

/* Responsive */
@media (max-width: 1024px) {
    [style*="grid-template-columns: 1fr 450px"] { grid-template-columns: 1fr !important; }
}
@media (max-width: 768px) {
    [style*="grid-template-columns: 1fr 1fr"] { grid-template-columns: 1fr !important; }
}
</style>
