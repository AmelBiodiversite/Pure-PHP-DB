<?php
/**
 * Page de succès après paiement
 */
?>

<div class="container mt-8 mb-16">
    <div class="card text-center" style="max-width: 600px; margin: 0 auto; padding: var(--space-12);">
        <div style="font-size: 4rem; margin-bottom: var(--space-6);">🎉</div>
        
        <h1 style="color: var(--success); margin-bottom: var(--space-4);">
            Paiement Réussi !
        </h1>
        
        <p style="font-size: 1.125rem; margin-bottom: var(--space-8); color: var(--text-secondary);">
            Merci pour votre achat ! Vos produits sont maintenant disponibles.
        </p>
        
        <div style="background: var(--bg-secondary); padding: var(--space-6); border-radius: var(--radius); margin-bottom: var(--space-8);">
            <p style="margin-bottom: var(--space-2);"><strong>Session ID :</strong></p>
            <p style="font-family: monospace; font-size: 0.875rem; color: var(--text-tertiary);">
                <?= e($session_id) ?>
            </p>
        </div>
        
        <div style="display: flex; gap: var(--space-4); justify-content: center; flex-wrap: wrap;">
            <a href="/account/downloads" class="btn btn-primary btn-lg">
                📥 Mes Téléchargements
            </a>
            <a href="/products" class="btn btn-outline btn-lg">
                🛍️ Continuer mes Achats
            </a>
        </div>
        
        <div style="margin-top: var(--space-8); padding-top: var(--space-8); border-top: 1px solid var(--border-color);">
            <p style="font-size: 0.875rem; color: var(--text-tertiary);">
                📧 Un email de confirmation vous a été envoyé.<br>
                En cas de problème, contactez-nous : <a href="/contact">support@marketflow.com</a>
            </p>
        </div>
    </div>
</div>