<?php
/**
 * ================================================
 * MARKETFLOW PRO - PAGE MES FAVORIS
 * ================================================
 * 
 * Fichier : app/views/wishlist/index.php
 * Version : 1.0
 * Date : 16 janvier 2025
 * 
 * DESCRIPTION :
 * Page affichant tous les produits favoris de l'utilisateur
 * avec possibilité de les supprimer ou de les ajouter au panier.
 * 
 * VARIABLES DISPONIBLES :
 * - $wishlistItems : Tableau des produits en favoris
 * - $wishlistCount : Nombre total de favoris
 * - $title : Titre de la page
 * 
 * ================================================
 */
?>

<div class="container" style="padding: 2rem 1rem; max-width: 1200px; margin: 0 auto;">
    
    <!-- ========================================
         HEADER DE LA PAGE
         ======================================== -->
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.5rem;">
            ❤️ Mes Favoris
        </h1>
        <p style="color: #666; font-size: 1rem;">
            <?php if (count($wishlist_items ?? []) > 0): ?>
                Vous avez <strong><?= e($wishlistCount) ?></strong> produit<?= $wishlistCount > 1 ? 's' : '' ?> en favoris
            <?php else: ?>
                Vous n'avez pas encore de produits en favoris
            <?php endif; ?>
        </p>
    </div>

    <?php if (empty($wishlist_items)): ?>
        
        <!-- ========================================
             ÉTAT VIDE (Aucun favori)
             ======================================== -->
        <div style="text-align: center; padding: 4rem 2rem; background: #f9fafb; border-radius: 12px; border: 2px dashed #e5e7eb;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">💔</div>
            <h2 style="font-size: 1.5rem; font-weight: 600; color: #374151; margin-bottom: 1rem;">
                Votre liste de favoris est vide
            </h2>
            <p style="color: #6b7280; margin-bottom: 2rem; max-width: 500px; margin-left: auto; margin-right: auto;">
                Parcourez notre catalogue et cliquez sur le ❤️ pour ajouter vos produits préférés ici.
            </p>
            <a href="/products" 
               class="btn btn-primary" 
               style="display: inline-block; padding: 0.75rem 2rem; background: #2563eb; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s;">
                Découvrir les produits
            </a>
        </div>

    <?php else: ?>
        
        <!-- ========================================
             GRILLE DES PRODUITS FAVORIS
             ======================================== -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
            
            <?php foreach ($wishlist_items as $item): ?>
                
                <!-- ========================================
                     CARTE PRODUIT FAVORI
                     ======================================== -->
                <div class="wishlist-product-card" 
                     data-product-id="<?= e($item['product_id']) ?>" 
                     style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s; position: relative;">
                    
                    <!-- Bouton Supprimer (coin supérieur droit) -->
                    <button class="btn-remove-wishlist" 
                            data-product-id="<?= e($item['product_id']) ?>" 
                            style="position: absolute; top: 12px; right: 12px; z-index: 10; background: rgba(255,255,255,0.95); border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: all 0.3s;"
                            title="Retirer des favoris">
                        <span style="font-size: 1.25rem;">❤️</span>
                    </button>

                    <!-- Image du produit -->
                    <a href="/products/<?= e($item['slug']) ?>" style="display: block; text-decoration: none;">
                        <div style="width: 100%; height: 200px; overflow: hidden; background: #f3f4f6;">
                            <?php if (!empty($item['thumbnail_url'])): ?>
                                <img src="<?= e($item['thumbnail_url']) ?>" 
                                     alt="<?= e($item['title']) ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;"
                                     onmouseover="this.style.transform='scale(1.05)'"
                                     onmouseout="this.style.transform='scale(1)'">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                    <span style="font-size: 3rem;">📦</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>

                    <!-- Informations du produit -->
                    <div style="padding: 1.25rem;">
                        
                        <!-- Titre -->
                        <h3 style="margin: 0 0 0.75rem 0; font-size: 1.125rem; font-weight: 600; color: #1a1a1a;">
                            <a href="/products/<?= e($item['slug']) ?>" 
                               style="color: inherit; text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= e($item['title']) ?>
                            </a>
                        </h3>

                        <!-- Vendeur -->
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <span style="color: #6b7280; font-size: 0.875rem;">par</span>
                            <a href="/seller/<?= e($item['seller_username']) ?>/products" 
                               style="color: #2563eb; text-decoration: none; font-weight: 500; font-size: 0.875rem;">
                                <?= e($item['seller_shop_name'] ?: $item['seller_name']) ?>
                            </a>
                        </div>

                        <!-- Note et ventes -->
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; font-size: 0.875rem; color: #6b7280;">
                            <?php if ($item['rating_count'] > 0): ?>
                                <div style="display: flex; align-items: center; gap: 0.25rem;">
                                    <span style="color: #fbbf24;">⭐</span>
                                    <strong style="color: #1a1a1a;"><?= number_format($item['rating_average'], 1) ?></strong>
                                    <span>(<?= e($item['rating_count']) ?>)</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($item['sales'] > 0): ?>
                                <div>
                                    <span>🛒</span> <?= e($item['sales']) ?> vente<?= $item['sales'] > 1 ? 's' : '' ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Prix et bouton -->
                        <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                            <div>
                                <?php if ($item['original_price'] && $item['original_price'] > $item['price']): ?>
                                    <div style="text-decoration: line-through; color: #9ca3af; font-size: 0.875rem;">
                                        <?= formatPrice($item['original_price']) ?>
                                    </div>
                                <?php endif; ?>
                                <div style="font-size: 1.5rem; font-weight: 700; color: #2563eb;">
                                    <?= formatPrice($item['price']) ?>
                                </div>
                            </div>

                            <!-- Bouton Ajouter au panier -->
                            <form action="/cart/add" method="POST" style="margin: 0;">
                                <input type="hidden" name="product_id" value="<?= e($item['product_id']) ?>">
                                <button type="submit" 
                                        style="padding: 0.5rem 1.25rem; background: #10b981; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s; white-space: nowrap;"
                                        onmouseover="this.style.background='#059669'"
                                        onmouseout="this.style.background='#10b981'">
                                    🛒 Ajouter
                                </button>
                            </form>
                        </div>

                        <!-- Date d'ajout -->
                        <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #f3f4f6; font-size: 0.75rem; color: #9ca3af; text-align: center;">
                            Ajouté <?= timeAgo($item['added_at']) ?>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
            
        </div>

    <?php endif; ?>

</div>

<!-- ========================================
     JAVASCRIPT POUR SUPPRESSION AJAX
     ======================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gérer les clics sur les boutons "Retirer des favoris"
    document.querySelectorAll('.btn-remove-wishlist').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.dataset.productId;
            const card = this.closest('.wishlist-product-card');
            
            // Confirmation visuelle
            if (!confirm('Retirer ce produit de vos favoris ?')) {
                return;
            }
            
            // Animation de suppression
            card.style.opacity = '0.5';
            card.style.pointerEvents = 'none';
            
            // Requête AJAX
            fetch('/wishlist/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Animation de disparition
                    card.style.transition = 'all 0.5s ease';
                    card.style.transform = 'scale(0.8)';
                    card.style.opacity = '0';
                    
                    setTimeout(() => {
                        card.remove();
                        
                        // Recharger si plus de produits
                        const remainingCards = document.querySelectorAll('.wishlist-product-card');
                        if (remainingCards.length === 0) {
                            location.reload();
                        }
                        
                        // Mettre à jour le compteur header
                        const badge = document.querySelector('.wishlist-count');
                        if (badge && data.count !== undefined) {
                            badge.textContent = data.count;
                            if (data.count === 0) {
                                badge.style.display = 'none';
                            }
                        }
                    }, 500);
                    
                    // Notification
                    if (window.showNotification) {
                        window.showNotification(data.message, 'success');
                    }
                } else {
                    card.style.opacity = '1';
                    card.style.pointerEvents = 'auto';
                    alert(data.message || 'Erreur lors de la suppression');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                card.style.opacity = '1';
                card.style.pointerEvents = 'auto';
                alert('Erreur lors de la suppression du favori');
            });
        });
    });
});
</script>
<style>
/* === DESIGN MAQUETTE2 — FAVORIS === */
.container{background:#faf9f5;padding-top:32px!important;padding-bottom:40px!important}
h1,h2,h3{font-family:Georgia,serif;font-weight:400;color:#1e1208}
h1[style]{font-size:28px!important;font-weight:400!important;color:#1e1208!important}
p[style*="color: #666"]{font-family:'Manrope',sans-serif;font-size:13px;color:#8a7060!important}
.wishlist-product-card{background:#fff!important;border:0.5px solid #ede8df!important;border-radius:14px!important;overflow:hidden;box-shadow:none!important;transition:transform 0.2s ease}
.wishlist-product-card:hover{transform:translateY(-3px);box-shadow:none!important}
div[style*="height: 200px"]{background:#f5f1eb!important}
div[style*="font-size: 1.5rem"][style*="font-weight: 7"]{font-family:Georgia,serif;font-size:20px!important;font-weight:400!important;color:#1e1208!important}
a[style*="color: #2563eb"]{color:#7c6cf0!important}
button[style*="background: #10b981"]{background:#7c6cf0!important;border-radius:8px!important;font-family:'Manrope',sans-serif;font-size:12px!important;font-weight:500!important;border:none!important;padding:6px 14px!important;transition:background 0.15s!important}
button[style*="background: #10b981"]:hover{background:#6558d4!important}
.btn-remove-wishlist{background:rgba(255,255,255,0.95)!important;border:0.5px solid #ede8df!important;border-radius:50%;box-shadow:none!important;transition:all 0.15s!important}
.btn-remove-wishlist:hover{background:#fbeaf0!important;border-color:#d4537e!important}
div[style*="text-align: center"][style*="background: #f9fafb"]{background:#f5f1eb!important;border:0.5px dashed #ddd6c8!important;border-radius:14px!important}
a[style*="background: #2563eb"]{background:#7c6cf0!important;color:#fff!important;border-radius:8px!important;padding:9px 20px!important;font-family:'Manrope',sans-serif!important;font-size:13px!important}
div[style*="border-top: 1px solid #e5e7eb"]{border-top:0.5px solid #ede8df!important}
div[style*="font-size: 0.75rem"][style*="text-align: center"]{font-family:'Manrope',sans-serif;font-size:11px;color:#a0907e!important;border-top:0.5px solid #ede8df!important}
span[style*="color: #fbbf24"]{color:#ba7517!important}
strong[style*="color: #1a1a1a"]{color:#1e1208!important;font-family:Georgia,serif;font-weight:400}
</style>
