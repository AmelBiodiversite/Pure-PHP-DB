<?php
/**
 * MARKETFLOW PRO - VALIDATION PRODUITS ADMIN
 * Fichier : app/views/admin/products.php
 */

$products = $data['products'] ?? [];
$stats = $data['stats'] ?? [];
?>

<div class="container mt-8 mb-16">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 class="mb-2">📦 Gestion des Produits</h1>
            <p style="color: var(--text-secondary);">Valider, rejeter ou modérer les produits</p>
        </div>
        <a href="/admin" class="btn btn-secondary">← Retour Dashboard</a>
    </div>

    <!-- Stats rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card">
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;">Total Produits</p>
            <h2 style="margin: 0; font-size: 2rem;"><?= number_format($stats['total'] ?? 0) ?></h2>
        </div>
        <div class="card">
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;">En Attente</p>
            <h2 style="margin: 0; font-size: 2rem; color: var(--warning);"><?= number_format($stats['pending'] ?? 0) ?></h2>
        </div>
        <div class="card">
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;">Approuvés</p>
            <h2 style="margin: 0; font-size: 2rem; color: var(--success);"><?= number_format($stats['approved'] ?? 0) ?></h2>
        </div>
        <div class="card">
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;">Rejetés</p>
            <h2 style="margin: 0; font-size: 2rem; color: var(--danger);"><?= number_format($stats['rejected'] ?? 0) ?></h2>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-8">
        <form method="GET" action="/admin/products" style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <input type="text" 
                   name="search" 
                   placeholder="🔍 Rechercher produit, vendeur..." 
                   class="input"
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                   style="flex: 1; min-width: 300px;">
            
            <select name="status" class="input" style="width: 180px;">
                <option value="">Tous les statuts</option>
                <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>⏳ En attente</option>
                <option value="approved" <?= ($_GET['status'] ?? '') === 'approved' ? 'selected' : '' ?>>✅ Approuvés</option>
                <option value="rejected" <?= ($_GET['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>❌ Rejetés</option>
            </select>

            <select name="category" class="input" style="width: 180px;">
                <option value="">Toutes catégories</option>
                <option value="1">Templates</option>
                <option value="2">Graphiques</option>
                <option value="3">Codes</option>
                <option value="4">Formations</option>
                <option value="5">Photos</option>
            </select>

            <select name="sort" class="input" style="width: 180px;">
                <option value="recent">Plus récents</option>
                <option value="oldest">Plus anciens</option>
                <option value="price_high">Prix décroissant</option>
                <option value="price_low">Prix croissant</option>
            </select>

            <button type="submit" class="btn btn-primary">Filtrer</button>
            <a href="/admin/products" class="btn btn-secondary">Réinitialiser</a>
        </form>
    </div>

    <!-- Liste produits -->
    <div class="card">
        <?php if (empty($products)): ?>
            <div style="text-align: center; padding: 4rem; color: var(--text-secondary);">
                <p>Aucun produit trouvé</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border);">
                            <th style="padding: 1rem; text-align: left;">Produit</th>
                            <th style="padding: 1rem; text-align: left;">Vendeur</th>
                            <th style="padding: 1rem; text-align: left;">Prix</th>
                            <th style="padding: 1rem; text-align: left;">Catégorie</th>
                            <th style="padding: 1rem; text-align: left;">Date</th>
                            <th style="padding: 1rem; text-align: left;">Statut</th>
                            <th style="padding: 1rem; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr style="border-bottom: 1px solid var(--border);" class="hover-lift">
                                <td style="padding: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <?php if ($product['thumbnail_url']): ?>
                                            <img src="<?= htmlspecialchars($product['thumbnail_url']) ?>" 
                                                 alt="<?= htmlspecialchars($product['title']) ?>"
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                        <?php else: ?>
                                            <div style="width: 60px; height: 60px; background: var(--primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                                📦
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div style="font-weight: 600; margin-bottom: 0.25rem;">
                                                <?= htmlspecialchars($product['title']) ?>
                                            </div>
                                            <div style="color: var(--text-secondary); font-size: 0.875rem;">
                                                ID: #<?= $product['id'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="font-weight: 500;"><?= htmlspecialchars($product['seller_name']) ?></div>
                                    <div style="color: var(--text-secondary); font-size: 0.875rem;">
                                        @<?= htmlspecialchars($product['seller_username']) ?>
                                    </div>
                                </td>
                                <td style="padding: 1rem; font-weight: 600;">
                                    <?= number_format($product['price'], 2) ?>€
                                </td>
                                <td style="padding: 1rem; color: var(--text-secondary);">
                                    <?= htmlspecialchars($product['category_name'] ?? 'N/A') ?>
                                </td>
                                <td style="padding: 1rem; color: var(--text-secondary);">
                                    <?= date('d/m/Y', strtotime($product['created_at'])) ?>
                                </td>
                                <td style="padding: 1rem;">
                                    <?php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger'
                                    ];
                                    $statusLabels = [
                                        'pending' => '⏳ En attente',
                                        'approved' => '✅ Approuvé',
                                        'rejected' => '❌ Rejeté'
                                    ];
                                    $color = $statusColors[$product['status']] ?? 'secondary';
                                    $label = $statusLabels[$product['status']] ?? $product['status'];
                                    ?>
                                    <span class="badge badge-<?= $color ?>">
                                        <?= $label ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                                        <button onclick="viewProduct(<?= $product['id'] ?>)" 
                                                class="btn btn-sm btn-secondary" 
                                                title="Voir détails">
                                            👁️
                                        </button>
                                        
                                        <?php if ($product['status'] === 'pending'): ?>
                                            <form method="POST" action="/admin/products/approve" style="display: inline;">
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-success" 
                                                        title="Approuver"
                                                        onclick="return confirm('Approuver ce produit ?')">
                                                    ✓
                                                </button>
                                            </form>
                                            
                                            <button onclick="showRejectModal(<?= $product['id'] ?>)" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Rejeter">
                                                ✗
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($product['status'] === 'approved'): ?>
                                            <form method="POST" action="/admin/products/suspend" style="display: inline;">
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-warning" 
                                                        title="Suspendre"
                                                        onclick="return confirm('Suspendre ce produit ?')">
                                                    ⏸️
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <form method="POST" action="/admin/products/delete" style="display: inline;">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Supprimer"
                                                    onclick="return confirm('⚠️ Supprimer définitivement ce produit ?')">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal détail produit -->
<div id="productModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="margin: 0;">Détails du produit</h2>
            <button onclick="closeProductModal()" class="btn btn-secondary btn-sm">✕</button>
        </div>
        <div id="productDetails">
            <!-- Contenu chargé dynamiquement -->
        </div>
    </div>
</div>

<!-- Modal rejet -->
<div id="rejectModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="margin: 0;">❌ Rejeter le produit</h2>
            <button onclick="closeRejectModal()" class="btn btn-secondary btn-sm">✕</button>
        </div>
        <form method="POST" action="/admin/products/reject">
            <input type="hidden" name="product_id" id="reject_product_id">
            
            <div class="form-group">
                <label>Raison du rejet *</label>
                <select name="reason" class="input" required>
                    <option value="">Choisir une raison</option>
                    <option value="quality">Qualité insuffisante</option>
                    <option value="content">Contenu inapproprié</option>
                    <option value="copyright">Violation copyright</option>
                    <option value="duplicate">Produit dupliqué</option>
                    <option value="incomplete">Information incomplète</option>
                    <option value="other">Autre</option>
                </select>
            </div>

            <div class="form-group">
                <label>Message au vendeur (optionnel)</label>
                <textarea name="message" 
                          class="input" 
                          rows="4" 
                          placeholder="Expliquez pourquoi le produit est rejeté..."></textarea>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" onclick="closeRejectModal()" class="btn btn-secondary">
                    Annuler
                </button>
                <button type="submit" class="btn btn-danger">
                    Rejeter le produit
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.hover-lift {
    transition: all 0.2s ease;
}

table tr:hover {
    background: var(--bg-secondary);
}
</style>

<script>
function viewProduct(productId) {
    const modal = document.getElementById('productModal');
    const details = document.getElementById('productDetails');
    
    modal.style.display = 'flex';
    details.innerHTML = '<p style="text-align: center; padding: 2rem;">Chargement...</p>';
    
    // Charger détails via AJAX
    fetch(`/admin/products/${productId}/details`)
        .then(response => response.json())
        .then(data => {
            details.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <img src="${data.thumbnail_url}" alt="${data.title}" style="width: 100%; border-radius: 8px;">
                    </div>
                    <div>
                        <h3>${data.title}</h3>
                        <p>${data.description}</p>
                        <p><strong>Prix:</strong> ${data.price}€</p>
                        <p><strong>Vendeur:</strong> ${data.seller_name}</p>
                        <p><strong>Téléchargements:</strong> ${data.downloads}</p>
                        <p><strong>Note:</strong> ⭐ ${data.rating}/5</p>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            details.innerHTML = '<p style="color: var(--danger); text-align: center;">Erreur de chargement</p>';
        });
}

function closeProductModal() {
    document.getElementById('productModal').style.display = 'none';
}

function showRejectModal(productId) {
    document.getElementById('reject_product_id').value = productId;
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

// Fermer modals en cliquant à l'extérieur
window.onclick = function(event) {
    const productModal = document.getElementById('productModal');
    const rejectModal = document.getElementById('rejectModal');
    
    if (event.target === productModal) {
        productModal.style.display = 'none';
    }
    if (event.target === rejectModal) {
        rejectModal.style.display = 'none';
    }
}
</script>