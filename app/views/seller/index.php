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
                    Page <?= $pagination['current'] ?> sur <?= $pagination['total_pages'] ?>
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