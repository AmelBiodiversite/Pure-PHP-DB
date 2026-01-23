<?php
/**
 * MARKETFLOW PRO - LISTE DES CATÉGORIES
 */
?>

<div class="container mt-8 mb-16">
    <div class="mb-8">
        <h1 class="mb-4">📂 Toutes les catégories</h1>
        <p class="text-text-secondary">Explorez nos produits par catégorie</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($categories as $category): ?>
            <a href="/category/<?= e($category['slug']) ?>" class="card hover-lift p-6 text-center">
                <div class="text-5xl mb-4">
                    <?php
                    $icons = [
                        'templates' => '📄',
                        'ui-kits' => '🎨',
                        'icones' => '🎯',
                        'illustrations' => '🖼️',
                        'photos' => '📸',
                        'videos' => '🎬',
                        'audio' => '🎵',
                        'fonts' => '🔤',
                        'code' => '💻',
                        'ebooks' => '📚',
                        'formations' => '🎓'
                    ];
                    echo $icons[$category['slug']] ?? '📦';
                    ?>
                </div>
                
                <h3 class="font-bold text-lg mb-2"><?= e($category['name']) ?></h3>
                
                <?php if (!empty($category['description'])): ?>
                    <p class="text-text-secondary text-sm mb-3">
                        <?= e(substr($category['description'], 0, 80)) ?>...
                    </p>
                <?php endif; ?>
                
                <div class="text-primary-600 font-semibold text-sm">
                    <?= $category['product_count'] ?> produit<?= $category['product_count'] > 1 ? 's' : '' ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>