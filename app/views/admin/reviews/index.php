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
