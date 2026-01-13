<div class="container mt-8">
    <h1><?= e($category['name']) ?></h1>
    <p><?= e($category['description'] ?? '') ?></p>
    
    <?php if (empty($products)): ?>
        <p>Aucun produit dans cette catégorie.</p>
    <?php else: ?>
        <div class="grid grid-4 mt-8">
            <?php foreach ($products as $product): ?>
                <div class="card">
                    <h3><?= e($product['title']) ?></h3>
                    <p><?= e($product['price']) ?>€</p>
                    <a href="/products/<?= e($product['slug']) ?>" class="btn btn-sm btn-primary">Voir</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>