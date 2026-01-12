<?php
/**
 * MARKETFLOW PRO - PAGE DÉTAIL PRODUIT
 * Fichier : app/views/products/show.php
 */
?>

<div class="container mt-8 mb-16">

    <!-- Breadcrumb -->
    <nav style="margin-bottom: var(--space-6); font-size: 0.875rem; color: var(--text-tertiary);">
        <a href="/" style="color: var(--text-tertiary);">Accueil</a>
        <span style="margin: 0 var(--space-2);">/</span>
        <a href="/products" style="color: var(--text-tertiary);">Produits</a>
        <span style="margin: 0 var(--space-2);">/</span>
        <a href="/category/<?= e($product['category_slug']) ?>" style="color: var(--text-tertiary);">
            <?= e($product['category_name']) ?>
        </a>
        <span style="margin: 0 var(--space-2);">/</span>
        <span style="color: var(--text-primary);"><?= e($product['title']) ?></span>
    </nav>

    <!-- Contenu principal -->
    <div style="display: grid; grid-template-columns: 1fr 400px; gap: var(--space-12); margin-bottom: var(--space-12);">
        
        <!-- Colonne gauche - Images et description -->
        <div>
            
            <!-- Galerie d'images -->
            <div class="card" style="padding: var(--space-8); margin-bottom: var(--space-8);">
                
                <!-- Image principale -->
                <div style="margin-bottom: var(--space-6);">
                    <img 
                        id="mainImage"
                        src="<?= e($product['thumbnail']) ?>" 
                        alt="<?= e($product['title']) ?>"
                        style="
                            width: 100%;
                            border-radius: var(--radius-lg);
                            box-shadow: var(--shadow-lg);
                        "
                    >
                </div>

                <!-- Miniatures -->
                <?php if (!empty($product['images'])): ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: var(--space-3);">
                    <div class="thumbnail-item active" onclick="changeMainImage('<?= e($product['thumbnail']) ?>', this)">
                        <img src="<?= e($product['thumbnail']) ?>" alt="Miniature">
                    </div>
                    <?php foreach ($product['images'] as $image): ?>
                    <div class="thumbnail-item" onclick="changeMainImage('<?= e($image['image_url']) ?>', this)">
                        <img src="<?= e($image['image_url']) ?>" alt="Miniature">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            </div>

            <!-- Tabs Description / Avis -->
            <div class="card" style="padding: 0; overflow: hidden;">
                
                <!-- Tabs Headers -->
                <div style="display: flex; border-bottom: 1px solid var(--border-color);">
                    <button 
                        class="tab-btn active" 
                        data-tab="description"
                        style="
                            flex: 1;
                            padding: var(--space-4);
                            border: none;
                            background: none;
                            cursor: pointer;
                            font-weight: 600;
                            border-bottom: 2px solid var(--primary-600);
                            color: var(--primary-600);
                        ">
                        Description
                    </button>
                    <button 
                        class="tab-btn" 
                        data-tab="reviews"
                        style="
                            flex: 1;
                            padding: var(--space-4);
                            border: none;
                            background: none;
                            cursor: pointer;
                            color: var(--text-secondary);
                        ">
                        Avis (<?= count($reviews) ?>)
                    </button>
                </div>

                <!-- Tab Content - Description -->
                <div class="tab-content active" data-tab-content="description" style="padding: var(--space-8);">
                    <div style="line-height: 1.8; color: var(--text-secondary);">
                        <?= nl2br(e($product['description'])) ?>
                    </div>

                    <?php if (!empty($product['tags'])): ?>
                    <div style="margin-top: var(--space-8); padding-top: var(--space-6); border-top: 1px solid var(--border-color);">
                        <h4 style="margin-bottom: var(--space-3); font-size: 1rem;">Tags</h4>
                        <div style="display: flex; flex-wrap: wrap; gap: var(--space-2);">
                            <?php foreach ($product['tags'] as $tag): ?>
                            <a href="/products?tag=<?= e($tag['slug']) ?>" class="badge badge-primary">
                                <?= e($tag['name']) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($product['demo_url']): ?>
                    <div style="margin-top: var(--space-6);">
                        <a href="<?= e($product['demo_url']) ?>" target="_blank" class="btn btn-outline">
                            🔗 Voir la démo
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Tab Content - Avis -->
                <div class="tab-content" data-tab-content="reviews" style="padding: var(--space-8); display: none;">
                    
                    <?php if (empty($reviews)): ?>
                        <div class="text-center" style="padding: var(--space-12); color: var(--text-tertiary);">
                            <div style="font-size: 3rem; margin-bottom: var(--space-4);">💬</div>
                            <p>Aucun avis pour le moment. Soyez le premier à donner votre avis !</p>
                        </div>
                    <?php else: ?>
                        
                        <!-- Liste des avis -->
                        <div style="display: flex; flex-direction: column; gap: var(--space-6);">
                            <?php foreach ($reviews as $review): ?>
                            <div class="card" style="padding: var(--space-6);">
                                
                                <!-- Header avis -->
                                <div class="flex-between mb-3">
                                    <div class="flex gap-3" style="align-items: center;">
                                        <div style="
                                            width: 40px;
                                            height: 40px;
                                            border-radius: 50%;
                                            background: var(--gradient-primary);
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            color: white;
                                            font-weight: 600;
                                        ">
                                            <?= strtoupper(substr($review['username'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600;"><?= e($review['username']) ?></div>
                                            <div style="font-size: 0.75rem; color: var(--text-tertiary);">
                                                <?= $review['review_date'] ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Note -->
                                    <div style="display: flex; gap: var(--space-1);">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span style="color: <?= $i <= $review['rating'] ? 'var(--warning)' : 'var(--border-color)' ?>;">★</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>

                                <!-- Commentaire -->
                                <?php if ($review['comment']): ?>
                                <p style="color: var(--text-secondary); line-height: 1.6;">
                                    <?= nl2br(e($review['comment'])) ?>
                                </p>
                                <?php endif; ?>

                                <!-- Réponse du vendeur -->
                                <?php if ($review['seller_response']): ?>
                                <div style="
                                    margin-top: var(--space-4);
                                    padding: var(--space-4);
                                    background: var(--bg-secondary);
                                    border-left: 3px solid var(--primary-600);
                                    border-radius: var(--radius);
                                ">
                                    <div style="font-weight: 600; margin-bottom: var(--space-2); font-size: 0.875rem;">
                                        Réponse du vendeur
                                    </div>
                                    <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0;">
                                        <?= nl2br(e($review['seller_response'])) ?>
                                    </p>
                                </div>
                                <?php endif; ?>

                            </div>
                            <?php endforeach; ?>
                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

        <!-- Colonne droite - Sidebar achat -->
        <aside style="position: sticky; top: 100px; height: fit-content;">
            
            <div class="card" style="padding: var(--space-8);">
                
                <!-- Prix -->
                <div style="margin-bottom: var(--space-6);">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-600);">
                        <?= formatPrice($product['price']) ?>
                    </div>
                    <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                    <div style="display: flex; align-items: center; gap: var(--space-3); margin-top: var(--space-2);">
                        <span style="
                            font-size: 1.125rem;
                            color: var(--text-tertiary);
                            text-decoration: line-through;
                        ">
                            <?= formatPrice($product['original_price']) ?>
                        </span>
                        <span class="badge badge-error" style="font-size: 0.875rem;">
                            -<?= round((1 - $product['price'] / $product['original_price']) * 100) ?>%
                        </span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Note -->
                <?php if ($product['rating_count'] > 0): ?>
                <div style="
                    display: flex;
                    align-items: center;
                    gap: var(--space-3);
                    padding: var(--space-3);
                    background: var(--bg-secondary);
                    border-radius: var(--radius);
                    margin-bottom: var(--space-6);
                ">
                    <div style="display: flex; gap: var(--space-1);">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span style="color: <?= $i <= round($product['rating_average']) ? 'var(--warning)' : 'var(--border-color)' ?>; font-size: 1.25rem;">★</span>
                        <?php endfor; ?>
                    </div>
                    <div>
                        <div style="font-weight: 600;"><?= number_format($product['rating_average'], 1) ?>/5</div>
                        <div style="font-size: 0.75rem; color: var(--text-tertiary);">
                            <?= $product['rating_count'] ?> avis
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Boutons d'action -->
                <form method="POST" action="/cart/add" style="margin-bottom: var(--space-4);">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 1.125rem;">
                        🛒 Ajouter au panier
                    </button>
                </form>

                <button 
                    onclick="toggleWishlist(<?= $product['id'] ?>)"
                    class="btn btn-outline" 
                    id="wishlistBtn"
                    style="width: 100%;">
                    <?= $in_wishlist ? '💖' : '🤍' ?> 
                    <?= $in_wishlist ? 'Dans les favoris' : 'Ajouter aux favoris' ?>
                </button>

                <!-- Infos -->
                <div style="
                    margin-top: var(--space-6);
                    padding-top: var(--space-6);
                    border-top: 1px solid var(--border-color);
                ">
                    <div style="display: flex; flex-direction: column; gap: var(--space-4); font-size: 0.875rem;">
                        <div class="flex-between">
                            <span style="color: var(--text-tertiary);">Type de fichier</span>
                            <span style="font-weight: 600;"><?= strtoupper($product['file_type']) ?></span>
                        </div>
                        <?php if ($product['file_size']): ?>
                        <div class="flex-between">
                            <span style="color: var(--text-tertiary);">Taille</span>
                            <span style="font-weight: 600;"><?= number_format($product['file_size'] / 1024, 1) ?> MB</span>
                        </div>
                        <?php endif; ?>
                        <div class="flex-between">
                            <span style="color: var(--text-tertiary);">Téléchargements</span>
                            <span style="font-weight: 600;"><?= number_format($product['downloads_count']) ?></span>
                        </div>
                        <div class="flex-between">
                            <span style="color: var(--text-tertiary);">Ventes</span>
                            <span style="font-weight: 600;"><?= number_format($product['sales_count']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Garanties -->
                <div style="
                    margin-top: var(--space-6);
                    padding: var(--space-4);
                    background: var(--success-light);
                    border-radius: var(--radius);
                    font-size: 0.875rem;
                ">
                    <div style="display: flex; flex-direction: column; gap: var(--space-2); color: #065f46;">
                        <div>✓ Téléchargement instantané</div>
                        <div>✓ Mises à jour gratuites</div>
                        <div>✓ Support client inclus</div>
                    </div>
                </div>

            </div>

            <!-- Info vendeur -->
            <div class="card" style="padding: var(--space-6); margin-top: var(--space-6);">
                <h4 style="margin-bottom: var(--space-4); font-size: 1rem;">Vendu par</h4>
                
                <div class="flex gap-3 mb-4" style="align-items: center;">
                    <div style="
                        width: 50px;
                        height: 50px;
                        border-radius: 50%;
                        background: var(--gradient-primary);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-weight: 600;
                        font-size: 1.25rem;
                    ">
                        <?= strtoupper(substr($product['shop_name'] ?? $product['seller_name'], 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-weight: 600; margin-bottom: var(--space-1);">
                            <?= e($product['shop_name'] ?? $product['seller_name']) ?>
                        </div>
                        <?php if ($product['seller_rating'] > 0): ?>
                        <div style="font-size: 0.875rem; color: var(--text-tertiary);">
                            ⭐ <?= number_format($product['seller_rating'], 1) ?> 
                            (<?= number_format($product['seller_total_sales']) ?> ventes)
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($product['shop_description']): ?>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: var(--space-4); line-height: 1.6;">
                    <?= e(truncate($product['shop_description'], 100)) ?>
                </p>
                <?php endif; ?>

                <a href="/seller/<?= e($product['seller_name']) ?>" class="btn btn-ghost" style="width: 100%;">
                    Voir la boutique →
                </a>
            </div>

        </aside>

    </div>

    <!-- Produits similaires -->
    <?php if (!empty($related_products)): ?>
    <section style="margin-top: var(--space-16);">
        <h2 style="margin-bottom: var(--space-8);">Produits similaires</h2>
        <div class="grid grid-4">
            <?php foreach ($related_products as $rp): ?>
            <div class="product-card">
                <a href="/products/<?= e($rp['slug']) ?>">
                    <img src="<?= e($rp['thumbnail']) ?>" alt="<?= e($rp['title']) ?>" class="product-image">
                </a>
                <div class="product-content">
                    <h3 class="product-title">
                        <a href="/products/<?= e($rp['slug']) ?>"><?= e(truncate($rp['title'], 50)) ?></a>
                    </h3>
                    <div class="flex-between mt-4">
                        <span class="product-price"><?= formatPrice($rp['price']) ?></span>
                        <a href="/products/<?= e($rp['slug']) ?>" class="btn btn-primary btn-sm">Voir</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

</div>

<!-- JavaScript -->
<script>
// Changement d'image principale
function changeMainImage(imageUrl, thumbnail) {
    document.getElementById('mainImage').src = imageUrl;
    
    // Retirer la classe active de toutes les miniatures
    document.querySelectorAll('.thumbnail-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Ajouter la classe active à la miniature cliquée
    thumbnail.classList.add('active');
}

// Système de tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabName = this.dataset.tab;
        
        // Désactiver tous les tabs
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.style.borderBottom = 'none';
            b.style.color = 'var(--text-secondary)';
        });
        
        // Activer le tab cliqué
        this.style.borderBottom = '2px solid var(--primary-600)';
        this.style.color = 'var(--primary-600)';
        
        // Masquer tous les contenus
        document.querySelectorAll('.tab-content').forEach(content => {
            content.style.display = 'none';
        });
        
        // Afficher le contenu correspondant
        document.querySelector(`[data-tab-content="${tabName}"]`).style.display = 'block';
    });
});

// Toggle wishlist
async function toggleWishlist(productId) {
    try {
        const response = await fetch('/api/products/wishlist', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            const btn = document.getElementById('wishlistBtn');
            if (data.action === 'added') {
                btn.innerHTML = '💖 Dans les favoris';
            } else {
                btn.innerHTML = '🤍 Ajouter aux favoris';
            }
            
            MarketFlow.Toast.show(data.message, 'success');
        } else {
            if (response.status === 401) {
                window.location.href = '/login';
            } else {
                MarketFlow.Toast.show(data.error, 'error');
            }
        }
    } catch (error) {
        console.error('Erreur:', error);
        MarketFlow.Toast.show('Une erreur est survenue', 'error');
    }
}
</script>

<style>
.thumbnail-item {
    cursor: pointer;
    border: 2px solid transparent;
    border-radius: var(--radius);
    overflow: hidden;
    transition: all var(--transition);
    opacity: 0.6;
}

.thumbnail-item:hover,
.thumbnail-item.active {
    opacity: 1;
    border-color: var(--primary-600);
    transform: scale(1.05);
}

.thumbnail-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* Responsive */
@media (max-width: 1024px) {
    [style*="grid-template-columns: 1fr 400px"] {
        grid-template-columns: 1fr !important;
    }
    
    aside {
        position: relative !important;
        top: 0 !important;
    }
}
</style>