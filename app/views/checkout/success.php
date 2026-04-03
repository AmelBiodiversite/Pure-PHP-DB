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
<style>
/* === DESIGN MAQUETTE2 — CHECKOUT SUCCESS === */
body { background: #faf9f5 !important; }

.card {
    background: #fff !important;
    border: 0.5px solid #ede8df !important;
    border-radius: 16px !important;
    box-shadow: none !important;
}

h1[style*="color: var(--success)"] {
    font-family: Georgia, serif !important;
    font-weight: 400 !important;
    color: #3a7d44 !important;
    font-size: 26px !important;
}

p[style*="font-size: 1.125rem"] {
    font-family: 'Manrope', sans-serif !important;
    font-size: 14px !important;
    color: #6b5c4e !important;
}

/* Bloc session ID */
div[style*="background: var(--bg-secondary)"][style*="border-radius: var(--radius)"] {
    background: #faf9f5 !important;
    border: 0.5px solid #ede8df !important;
    border-radius: 10px !important;
}
p[style*="font-family: monospace"] {
    font-size: 12px !important;
    color: #a0907e !important;
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
.btn.btn-primary.btn-lg:hover { background: #6558d4 !important; }

.btn.btn-outline.btn-lg {
    background: #fff !important;
    color: #6b5c4e !important;
    border: 0.5px solid #ddd6c8 !important;
    border-radius: 10px !important;
    font-family: 'Manrope', sans-serif !important;
    font-weight: 500 !important;
    box-shadow: none !important;
}
.btn.btn-outline.btn-lg:hover { border-color: #7c6cf0 !important; color: #7c6cf0 !important; }

/* Pied de card */
div[style*="border-top: 1px solid var(--border-color)"] { border-top: 0.5px solid #ede8df !important; }
p[style*="font-size: 0.875rem; color: var(--text-tertiary)"] {
    font-family: 'Manrope', sans-serif !important;
    font-size: 12px !important;
    color: #a0907e !important;
}
a[href="/contact"] { color: #7c6cf0 !important; }
</style>
