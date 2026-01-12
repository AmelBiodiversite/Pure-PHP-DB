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
.card {
    animation: fadeIn 0.6s ease-out;
}
</style>