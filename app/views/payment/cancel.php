<?php
/**
 * MARKETFLOW PRO - PAGE PAIEMENT ANNULÉ
 * Fichier : app/views/payment/cancel.php
 */
?>

<div class="container mt-16 mb-16">
    
    <div style="max-width: 600px; margin: 0 auto; text-align: center;">
        
        <!-- Icône -->
        <div style="
            width: 100px;
            height: 100px;
            background: var(--error-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--space-6);
            font-size: 3rem;
        ">
            ⚠️
        </div>

        <!-- Message -->
        <h1 style="font-size: 2rem; margin-bottom: var(--space-4);">
            Paiement annulé
        </h1>

        <p style="font-size: 1.125rem; color: var(--text-secondary); margin-bottom: var(--space-8);">
            Votre paiement a été annulé. Aucun montant n'a été débité de votre compte.
        </p>

        <!-- Card -->
        <div class="card" style="padding: var(--space-8); margin-bottom: var(--space-6);">
            
            <h3 style="margin-bottom: var(--space-4);">
                Que souhaitez-vous faire ?
            </h3>

            <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                <a href="/cart" class="btn btn-primary btn-lg">
                    ← Retour au panier
                </a>
                <a href="/products" class="btn btn-secondary">
                    Continuer mes achats
                </a>
            </div>

        </div>

        <!-- Message rassurant -->
        <div style="
            padding: var(--space-4);
            background: var(--primary-50);
            border-radius: var(--radius);
            font-size: 0.875rem;
            color: var(--primary-700);
        ">
            💡 Vos articles sont toujours dans votre panier et vous attendent !
        </div>

        <!-- Support -->
        <div style="margin-top: var(--space-8); font-size: 0.875rem; color: var(--text-tertiary);">
            Besoin d'aide ? 
            <a href="/contact" style="color: var(--primary-600);">Contactez notre support</a>
        </div>

    </div>

</div>
<style>
/* === DESIGN MAQUETTE2 — PAIEMENT ANNULÉ === */
body { background: #faf9f5 !important; }

/* Icône cercle */
div[style*="background: var(--error-light)"] {
    background: #fce5df !important;
    border-radius: 50% !important;
}

/* Titre */
h1[style*="font-size: 2rem"] {
    font-family: Georgia, serif !important;
    font-weight: 400 !important;
    color: #1e1208 !important;
    font-size: 26px !important;
}

/* Sous-titre */
p[style*="font-size: 1.125rem; color: var(--text-secondary)"] {
    font-family: 'Manrope', sans-serif !important;
    font-size: 14px !important;
    color: #6b5c4e !important;
}

/* Card */
.card {
    background: #fff !important;
    border: 0.5px solid #ede8df !important;
    border-radius: 14px !important;
    box-shadow: none !important;
}
h3[style*="margin-bottom"] {
    font-family: Georgia, serif !important;
    font-weight: 400 !important;
    color: #1e1208 !important;
    font-size: 17px !important;
}

/* Boutons */
.btn.btn-primary.btn-lg {
    background: #7c6cf0 !important;
    color: #fff !important;
    border: none !important;
    border-radius: 10px !important;
    font-family: 'Manrope', sans-serif !important;
    font-weight: 500 !important;
    box-shadow: none !important;
}
.btn.btn-secondary {
    background: #f5f1eb !important;
    color: #6b5c4e !important;
    border: 0.5px solid #ddd6c8 !important;
    border-radius: 10px !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 13px !important;
    box-shadow: none !important;
}

/* Bloc info panier */
div[style*="background: var(--primary-50)"] {
    background: #f5f3ff !important;
    border-radius: 12px !important;
    color: #534ab7 !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 13px !important;
}

/* Support */
div[style*="font-size: 0.875rem; color: var(--text-tertiary)"] {
    font-family: 'Manrope', sans-serif !important;
    font-size: 12px !important;
    color: #a0907e !important;
}
a[href="/contact"] { color: #7c6cf0 !important; }
</style>
