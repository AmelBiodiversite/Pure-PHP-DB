<?php
/**
 * MARKETFLOW PRO - LISTE DES VENDEURS
 * Fichier : app/views/seller/index.php
 */
?>

<div class="container mt-8">
    <!-- Header -->
    <div class="flex-between mb-8">
        <div>
            <h1>Nos Vendeurs</h1>
            <p style="color: var(--text-secondary); margin-top: var(--space-2);">
                Découvrez notre communauté de créateurs talentueux
            </p>
        </div>
        <a href="/register?role=seller" class="btn btn-primary">
            Devenir vendeur
        </a>
    </div>

    <!-- Filtres -->
    <div class="card mb-8" style="padding: var(--space-6);">
        <form method="GET" action="/sellers" class="flex gap-4">
            <input 
                type="text" 
                name="search" 
                placeholder="Rechercher un vendeur..."
                value="<?= $_GET['search'] ?? '' ?>"
                class="input"
                style="flex: 1;"
            >
            <select name="sort" class="input">
                <option value="newest">Plus récents</option>
                <option value="popular">Plus populaires</option>
                <option value="sales">Meilleures ventes</option>
            </select>
            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>
    </div>

    <!-- Liste des vendeurs -->
    <?php if (empty($sellers)): ?>
        <div class="card text-center" style="padding: var(--space-12);">
            <div style="font-size: 3rem; margin-bottom: var(--space-4);">🎨</div>
            <h2 style="margin-bottom: var(--space-4);">Aucun vendeur trouvé</h2>
            <p style="color: var(--text-secondary); margin-bottom: var(--space-6);">
                Soyez le premier à rejoindre notre marketplace !
            </p>
            <a href="/register?role=seller" class="btn btn-primary btn-lg">
                Devenir vendeur
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-3">
            <?php foreach ($sellers as $seller): ?>
                <div class="card">
                    <!-- Avatar -->
                    <div style="text-align: center; margin-bottom: var(--space-4);">
                        <?php if (!empty($seller['avatar_url'])): ?>
                            <img 
                                src="<?= e($seller['avatar_url']) ?>" 
                                alt="<?= e($seller['username']) ?>"
                                style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;"
                            >
                        <?php else: ?>
                            <div style="
                                width: 80px; 
                                height: 80px; 
                                border-radius: 50%; 
                                background: var(--primary-100);
                                color: var(--primary-600);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 2rem;
                                font-weight: 600;
                                margin: 0 auto;
                            ">
                                <?= strtoupper(substr($seller['username'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Info vendeur -->
                    <h3 class="card-title text-center" style="margin-bottom: var(--space-2);">
                        <?= e($seller['full_name'] ?? $seller['username']) ?>
                    </h3>
                    
                    <p style="text-align: center; color: var(--text-tertiary); font-size: 0.875rem; margin-bottom: var(--space-4);">
                        @<?= e($seller['username']) ?>
                    </p>

                    <!-- Stats -->
                    <div style="
                        display: grid; 
                        grid-template-columns: 1fr 1fr; 
                        gap: var(--space-4);
                        padding: var(--space-4);
                        background: var(--surface);
                        border-radius: var(--radius-md);
                        margin-bottom: var(--space-4);
                    ">
                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: 600; color: var(--primary-600);">
                                <?= $seller['products_count'] ?? 0 ?>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                Produits
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: 600; color: var(--success-600);">
                                <?= $seller['total_sales'] ?? 0 ?>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                Ventes
                            </div>
                        </div>
                    </div>

                    <!-- Note -->
                    <?php if (isset($seller['rating_average']) && $seller['rating_average'] > 0): ?>
                        <div style="text-align: center; margin-bottom: var(--space-4);">
                            <span style="color: var(--warning);">★</span>
                            <span style="font-weight: 600;"><?= number_format($seller['rating_average'], 1) ?></span>
                            <span style="color: var(--text-tertiary); font-size: 0.875rem;">
                                (<?= $seller['rating_count'] ?? 0 ?> avis)
                            </span>
                        </div>
                    <?php endif; ?>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="/seller/<?= e($seller['username']) ?>/products" class="btn btn-primary" style="flex: 1;">
                            Voir les produits
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
            <div class="flex-center gap-2 mt-8">
                <?php if ($pagination['current'] > 1): ?>
                    <a href="?page=<?= $pagination['current'] - 1 ?>" class="btn btn-ghost">
                        ← Précédent
                    </a>
                <?php endif; ?>

                <span style="padding: 0 var(--space-4); color: var(--text-secondary);">
                    Page <?= e($pagination['current']) ?> sur <?= e($pagination['total_pages']) ?>
                </span>

                <?php if ($pagination['current'] < $pagination['total_pages']): ?>
                    <a href="?page=<?= $pagination['current'] + 1 ?>" class="btn btn-ghost">
                        Suivant →
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- CTA Section -->
    <div class="card mt-12" style="background: var(--gradient-primary); color: white; text-align: center; padding: var(--space-12);">
        <h2 style="color: white; margin-bottom: var(--space-4);">
            Rejoignez notre communauté de créateurs
        </h2>
        <p style="color: rgba(255,255,255,0.9); font-size: 1.125rem; margin-bottom: var(--space-6); max-width: 600px; margin-left: auto; margin-right: auto;">
            Vendez vos créations digitales et générez des revenus passifs dès aujourd'hui
        </p>
        <div class="flex-center gap-4">
            <a href="/register?role=seller" class="btn btn-lg" style="background: white; color: var(--primary-600);">
                Devenir vendeur
            </a>
            <a href="/products" class="btn btn-outline btn-lg" style="border-color: white; color: white;">
                Voir les produits
            </a>
        </div>
    </div>
</div>
<style>
/* =====================================================
   SELLER INDEX — palette maquette2
   Fond crème #faf9f5, violet #7c6cf0, Georgia + Manrope
   ===================================================== */

/* Header page */
.container h1 {
    font-family: Georgia, serif;
    color: #1a1a2e;
    font-size: clamp(1.8rem, 3vw, 2.4rem);
    font-weight: 700;
}

/* Bouton principal → violet */
.btn.btn-primary {
    background: #7c6cf0;
    border-color: #7c6cf0;
    color: #fff;
    font-family: 'Manrope', sans-serif;
    font-weight: 600;
    border-radius: 8px;
    transition: background .2s, transform .15s;
}
.btn.btn-primary:hover {
    background: #6558d4;
    transform: translateY(-1px);
}

/* Card filtre */
.container > .card:first-of-type {
    background: #fff;
    border: 1px solid #e8e4f3;
    border-radius: 12px;
}

/* Input + select */
.input {
    border: 1px solid #e2ddf5;
    border-radius: 8px;
    background: #faf9f5;
    font-family: 'Manrope', sans-serif;
    transition: border-color .2s, box-shadow .2s;
}
.input:focus {
    border-color: #7c6cf0;
    box-shadow: 0 0 0 3px rgba(124,108,240,.12);
    outline: none;
}

/* Cards vendeurs */
.grid .card {
    background: #fff;
    border: 1px solid #eeeaf7;
    border-radius: 14px;
    padding: 1.75rem;
    transition: box-shadow .2s, transform .2s;
}
.grid .card:hover {
    box-shadow: 0 6px 24px rgba(124,108,240,.10);
    transform: translateY(-2px);
}

/* Avatar initiale */
.grid .card > div:first-child > div {
    background: #ede9fe !important;
    color: #7c6cf0 !important;
    font-family: 'Manrope', sans-serif;
}

/* Nom vendeur */
.grid .card .card-title {
    font-family: Georgia, serif;
    color: #1a1a2e;
    font-size: 1.05rem;
}

/* Stats internes : fond crème */
.grid .card > div[style*="grid-template-columns"] {
    background: #faf9f5 !important;
    border-radius: 8px;
}

/* Nombre produits → violet */
.grid .card > div[style*="grid-template-columns"] > div:first-child > div:first-child {
    color: #7c6cf0 !important;
}

/* Étoile note */
.grid .card span[style*="warning"] {
    color: #f59e0b !important;
}

/* Bouton ghost pagination */
.btn.btn-ghost {
    border: 1px solid #e2ddf5;
    color: #7c6cf0;
    border-radius: 8px;
    font-family: 'Manrope', sans-serif;
    font-weight: 600;
    transition: background .2s;
}
.btn.btn-ghost:hover {
    background: #f0eeff;
}

/* CTA lavande en bas */
.container > .card[style*="gradient"],
.container > .card[style*="gradient-primary"] {
    background: linear-gradient(135deg, #7c6cf0 0%, #9d91f5 100%) !important;
    border-radius: 16px;
    border: none;
}

/* État vide */
.card.text-center .btn.btn-primary.btn-lg {
    font-size: 1rem;
    padding: .875rem 2rem;
}
</style>
