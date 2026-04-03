<?php
/**
 * MARKETFLOW PRO - ADMIN : MODÉRATION DES AVIS
 * Fichier : app/views/admin/reviews/index.php
 */
?>

<div class="container mt-8 mb-16">

    <div class="flex-between mb-6">
        <h1>Modération des avis</h1>
        <a href="/admin/dashboard" class="btn btn-ghost">← Retour admin</a>
    </div>

    <?php if (empty($pending_reviews)): ?>
        <div class="card text-center" style="padding: var(--space-12); color: var(--text-tertiary);">
            <div style="font-size: 3rem; margin-bottom: var(--space-4);">✅</div>
            <p>Aucun avis en attente de modération.</p>
        </div>

    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: var(--space-4);">
            <?php foreach ($pending_reviews as $review): ?>
            <div class="card" style="padding: var(--space-6);">

                <!-- En-tête : produit + auteur + date -->
                <div class="flex-between mb-4">
                    <div>
                        <a href="/products/<?= e($review['slug']) ?>" style="font-weight: 600;">
                            <?= e($review['product_title']) ?>
                        </a>
                        <div style="font-size: 0.875rem; color: var(--text-tertiary); margin-top: var(--space-1);">
                            Par <strong><?= e($review['username']) ?></strong>
                            · <?= date('d/m/Y à H:i', strtotime($review['created_at'])) ?>
                        </div>
                    </div>

                    <!-- Note étoiles -->
                    <div style="display: flex; gap: 2px; font-size: 1.25rem;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span style="color: <?= $i <= $review['rating'] ? 'var(--warning)' : 'var(--border-color)' ?>;">★</span>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Titre + commentaire -->
                <?php if ($review['title']): ?>
                    <p style="font-weight: 600; margin-bottom: var(--space-2);"><?= e($review['title']) ?></p>
                <?php endif; ?>
                <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: var(--space-6);">
                    <?= nl2br(e($review['comment'])) ?>
                </p>

                <!-- Boutons approuver / rejeter -->
                <div style="display: flex; gap: var(--space-3);">
                    <form method="POST" action="/admin/reviews/<?= e($review['id']) ?>/approve">
                        <button type="submit" class="btn btn-primary btn-sm">✅ Approuver</button>
                    </form>
                    <form method="POST" action="/admin/reviews/<?= e($review['id']) ?>/reject">
                        <button type="submit" class="btn btn-danger btn-sm">❌ Rejeter</button>
                    </form>
                </div>

            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
<style>
/* === DESIGN MAQUETTE2 — MODÉRATION DES AVIS === */

/* Titre */
h1 { font-family: Georgia, serif !important; font-weight: 400 !important; color: #1e1208 !important; font-size: 24px !important; }

/* Bouton retour */
.btn.btn-ghost {
    background: transparent !important;
    color: #6b5c4e !important;
    border: 0.5px solid #ddd6c8 !important;
    border-radius: 8px !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 12px !important;
    box-shadow: none !important;
}
.btn.btn-ghost:hover { border-color: #7c6cf0 !important; color: #7c6cf0 !important; background: #f5f3ff !important; }

/* Card état vide */
.card.text-center p { font-family: 'Manrope', sans-serif !important; color: #a0907e !important; font-size: 13px !important; }

/* Cards avis */
.card {
    background: #fff !important;
    border: 0.5px solid #ede8df !important;
    border-radius: 14px !important;
    box-shadow: none !important;
}

/* Lien titre produit */
a[style*="font-weight: 600"] {
    color: #7c6cf0 !important;
    font-family: Georgia, serif !important;
    font-weight: 400 !important;
    font-size: 15px !important;
    text-decoration: none !important;
}
a[style*="font-weight: 600"]:hover { text-decoration: underline !important; }

/* Méta auteur + date */
div[style*="color: var(--text-tertiary)"][style*="margin-top"] {
    font-family: 'Manrope', sans-serif !important;
    font-size: 11px !important;
    color: #a0907e !important;
}

/* Étoiles — remplies */
span[style*="color: var(--warning)"] { color: #c99a27 !important; font-size: 1.1rem !important; }
/* Étoiles — vides */
span[style*="color: var(--border-color)"] { color: #ddd6c8 !important; font-size: 1.1rem !important; }

/* Titre de l'avis */
p[style*="font-weight: 600"] {
    font-family: Georgia, serif !important;
    font-weight: 400 !important;
    color: #1e1208 !important;
    font-size: 14px !important;
}

/* Corps de l'avis */
p[style*="color: var(--text-secondary)"][style*="line-height"] {
    font-family: 'Manrope', sans-serif !important;
    font-size: 13px !important;
    color: #6b5c4e !important;
    line-height: 1.7 !important;
}

/* Bouton approuver */
.btn.btn-primary.btn-sm {
    background: #3a7d44 !important;
    color: #fff !important;
    border: none !important;
    border-radius: 7px !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    box-shadow: none !important;
    padding: 6px 14px !important;
}
.btn.btn-primary.btn-sm:hover { background: #2d6235 !important; }

/* Bouton rejeter */
.btn.btn-danger.btn-sm {
    background: #fce5df !important;
    color: #993c1d !important;
    border: 0.5px solid #e5b8a8 !important;
    border-radius: 7px !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    box-shadow: none !important;
    padding: 6px 14px !important;
}
.btn.btn-danger.btn-sm:hover { background: #f8d4cb !important; }
</style>
