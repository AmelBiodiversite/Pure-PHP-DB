<?php
/**
 * MARKETFLOW PRO - VALIDATION PRODUITS ADMIN
 * Fichier : app/views/admin/products.php
 */
$products = $data['products'] ?? [];
$stats = $data['stats'] ?? [];
$csrf_token = $data['csrf_token'] ?? '';
$current_page = $_GET['page'] ?? 1;
$per_page = 20;
$total_pages = ceil(($stats['total'] ?? 0) / $per_page);
?>

<div class="container mt-8 mb-16">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="mb-2">📦 Gestion des Produits</h1>
            <p class="text-text-secondary">Valider, rejeter ou modérer les produits</p>
        </div>
        <a href="/admin" class="btn btn-secondary">← Retour Dashboard</a>
    </div>

    <!-- Stats rapides -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card text-center p-4">
            <p class="text-text-secondary text-sm mb-2">Total Produits</p>
            <h2 class="text-3xl font-bold"><?= number_format($stats['total'] ?? 0) ?></h2>
        </div>
        <div class="card text-center p-4">
            <p class="text-text-secondary text-sm mb-2">En Attente</p>
            <h2 class="text-3xl font-bold text-warning"><?= number_format($stats['pending'] ?? 0) ?></h2>
        </div>
        <div class="card text-center p-4">
            <p class="text-text-secondary text-sm mb-2">Approuvés</p>
            <h2 class="text-3xl font-bold text-success"><?= number_format($stats['approved'] ?? 0) ?></h2>
        </div>
        <div class="card text-center p-4">
            <p class="text-text-secondary text-sm mb-2">Rejetés</p>
            <h2 class="text-3xl font-bold text-error"><?= number_format($stats['rejected'] ?? 0) ?></h2>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-8 p-6">
        <form method="GET" action="/admin/products" class="flex flex-wrap gap-4 items-center">
            <input type="text" name="search" placeholder="🔍 Rechercher produit, vendeur..." class="input flex-1 min-w-[250px]" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <select name="status" class="input w-48">
                <option value="">Tous les statuts</option>
                <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>⏳ En attente</option>
                <option value="approved" <?= ($_GET['status'] ?? '') === 'approved' ? 'selected' : '' ?>>✅ Approuvés</option>
                <option value="rejected" <?= ($_GET['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>❌ Rejetés</option>
            </select>
            <select name="category" class="input w-48">
                <option value="">Toutes catégories</option>
                <option value="1">Templates</option>
                <option value="2">Graphiques</option>
                <option value="3">Codes</option>
                <option value="4">Formations</option>
                <option value="5">Photos</option>
            </select>
            <select name="sort" class="input w-48">
                <option value="recent" <?= ($_GET['sort'] ?? 'recent') === 'recent' ? 'selected' : '' ?>>Plus récents</option>
                <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Plus anciens</option>
                <option value="price_high" <?= ($_GET['sort'] ?? '') === 'price_high' ? 'selected' : '' ?>>Prix décroissant</option>
                <option value="price_low" <?= ($_GET['sort'] ?? '') === 'price_low' ? 'selected' : '' ?>>Prix croissant</option>
            </select>
            <button type="submit" class="btn btn-primary">Filtrer</button>
            <a href="/admin/products" class="btn btn-ghost">Réinitialiser</a>
        </form>
    </div>

    <!-- Liste produits -->
    <div class="card overflow-hidden">
        <?php if (empty($products)): ?>
            <div class="text-center p-16 text-text-secondary">
                <p class="text-lg">Aucun produit trouvé</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-bg-tertiary sticky top-0 z-10">
                        <tr class="border-b-2 border-gray-200">
                            <th class="p-3 text-left" style="width: 40px;">
                                <input type="checkbox" id="select-all" aria-label="Sélectionner tout">
                            </th>
                            <th class="p-3 text-left" style="min-width: 280px;">Produit</th>
                            <th class="p-3 text-left" style="min-width: 160px;">Vendeur</th>
                            <th class="p-3 text-left" style="min-width: 100px;">Prix</th>
                            <th class="p-3 text-left" style="min-width: 120px;">Catégorie</th>
                            <th class="p-3 text-left" style="min-width: 110px;">Date</th>
                            <th class="p-3 text-left" style="min-width: 120px;">Statut</th>
                            <th class="p-3 text-center" style="min-width: 220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr class="border-b border-gray-100 hover:bg-bg-secondary transition-all duration-200">
                                <!-- Checkbox -->
                                <td class="p-3">
                                    <input type="checkbox" name="bulk[]" value="<?= $product['id'] ?>" aria-label="Sélectionner produit <?= $product['id'] ?>">
                                </td>
                                
                                <!-- Produit -->
                                <td class="p-3">
                                    <div class="flex items-center gap-3">
                                        <?php if ($product['thumbnail_url']): ?>
                                            <img src="<?= htmlspecialchars($product['thumbnail_url']) ?>" 
                                                 alt="<?= htmlspecialchars($product['title']) ?>" 
                                                 class="w-12 h-12 object-cover rounded-lg shadow-sm flex-shrink-0"
                                                 style="min-width: 48px; min-height: 48px;">
                                        <?php else: ?>
                                            <div class="w-12 h-12 bg-primary-500 rounded-lg flex items-center justify-center text-xl text-white flex-shrink-0" 
                                                 style="min-width: 48px; min-height: 48px;">
                                                📦
                                            </div>
                                        <?php endif; ?>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold truncate" style="max-width: 220px;" title="<?= htmlspecialchars($product['title']) ?>">
                                                <?= htmlspecialchars($product['title']) ?>
                                            </p>
                                            <p class="text-text-secondary text-xs">ID: #<?= $product['id'] ?></p>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Vendeur -->
                                <td class="p-3">
                                    <div class="min-w-0">
                                        <p class="font-medium truncate text-sm" style="max-width: 140px;" title="<?= htmlspecialchars($product['seller_name']) ?>">
                                            <?= htmlspecialchars($product['seller_name']) ?>
                                        </p>
                                        <p class="text-text-secondary text-xs truncate" style="max-width: 140px;">
                                            @<?= htmlspecialchars($product['seller_username']) ?>
                                        </p>
                                    </div>
                                </td>
                                
                                <!-- Prix -->
                                <td class="p-3">
                                    <span class="font-semibold text-primary-600 whitespace-nowrap">
                                        <?= number_format($product['price'], 2) ?> €
                                    </span>
                                </td>
                                
                                <!-- Catégorie -->
                                <td class="p-3">
                                    <span class="text-text-secondary text-sm">
                                        <?= htmlspecialchars($product['category_name'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                
                                <!-- Date -->
                                <td class="p-3">
                                    <span class="text-text-secondary text-xs whitespace-nowrap">
                                        <?= date('d/m/Y', strtotime($product['created_at'])) ?>
                                    </span>
                                </td>
                                
                                <!-- Statut -->
                                <td class="p-3">
                                    <?php
                                    $statusColors = [
                                        'pending' => 'warning', 
                                        'approved' => 'success', 
                                        'rejected' => 'error', 
                                        'suspended' => 'secondary'
                                    ];
                                    $statusLabels = [
                                        'pending' => '⏳ En attente', 
                                        'approved' => '✅ Approuvé', 
                                        'rejected' => '❌ Rejeté', 
                                        'suspended' => '⏸️ Suspendu'
                                    ];
                                    $color = $statusColors[$product['status']] ?? 'secondary';
                                    $label = $statusLabels[$product['status']] ?? ucfirst($product['status']);
                                    ?>
                                    <span class="badge badge-<?= $color ?> text-xs whitespace-nowrap"><?= $label ?></span>
                                </td>
                                
                                <!-- Actions -->
                                <td class="p-2">
                                    <div class="flex gap-1 justify-center flex-wrap">
                                        <!-- Voir détails -->
                                        <button onclick="viewProduct(<?= $product['id'] ?>)" 
                                                class="btn btn-sm btn-ghost px-2 py-1" 
                                                aria-label="Voir détails" 
                                                title="Voir détails">
                                            👁️
                                        </button>
                                        
                                        <!-- Si en attente -->
                                        <?php if ($product['status'] === 'pending'): ?>
                                            <form method="POST" action="/admin/products/<?= $product['id'] ?>/approve" class="inline">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-success px-2 py-1" 
                                                        aria-label="Approuver" 
                                                        title="Approuver" 
                                                        onclick="return confirm('Approuver <?= htmlspecialchars($product['title']) ?> ?')">
                                                    ✓
                                                </button>
                                            </form>
                                            <button onclick="showRejectModal(<?= $product['id'] ?>)" 
                                                    class="btn btn-sm btn-error px-2 py-1" 
                                                    aria-label="Rejeter" 
                                                    title="Rejeter">
                                                ✗
                                            </button>
                                        <?php endif; ?>
                                        
                                        <!-- Si approuvé -->
                                        <?php if ($product['status'] === 'approved'): ?>
                                            <form method="POST" action="/admin/products/suspend" class="inline">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-warning px-2 py-1" 
                                                        aria-label="Suspendre" 
                                                        title="Suspendre" 
                                                        onclick="return confirm('Suspendre <?= htmlspecialchars($product['title']) ?> ?')">
                                                    ⏸️
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <!-- Si suspendu -->
                                        <?php if ($product['status'] === 'suspended'): ?>
                                            <form method="POST" action="/admin/products/approve" class="inline">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-success px-2 py-1" 
                                                        aria-label="Réactiver" 
                                                        title="Réactiver" 
                                                        onclick="return confirm('Réactiver <?= htmlspecialchars($product['title']) ?> ?')">
                                                    ▶️
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <!-- Supprimer -->
                                        <form method="POST" action="/admin/products/delete" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <button type="submit" 
                                                    class="btn btn-sm btn-error px-2 py-1" 
                                                    aria-label="Supprimer" 
                                                    title="Supprimer" 
                                                    onclick="return confirm('⚠️ Supprimer définitivement <?= htmlspecialchars($product['title']) ?> ?')">
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
            
            <!-- Pagination -->
            <div class="flex justify-between items-center mt-6 p-4 border-t border-gray-200">
                <a href="?page=<?= max(1, $current_page - 1) ?>&<?= http_build_query(array_diff_key($_GET, ['page' => ''])) ?>" 
                   class="btn btn-ghost <?= $current_page <= 1 ? 'opacity-50 cursor-not-allowed' : '' ?>">
                    ← Précédent
                </a>
                <p class="text-text-secondary text-sm">Page <?= $current_page ?> sur <?= max(1, $total_pages) ?></p>
                <a href="?page=<?= min($total_pages, $current_page + 1) ?>&<?= http_build_query(array_diff_key($_GET, ['page' => ''])) ?>" 
                   class="btn btn-ghost <?= $current_page >= $total_pages ? 'opacity-50 cursor-not-allowed' : '' ?>">
                    Suivant →
                </a>
            </div>
            
            <!-- Bulk actions -->
            <div class="flex gap-4 mt-4 p-4 bg-bg-tertiary rounded-b-lg">
                <select id="bulk-action" class="input w-48">
                    <option value="">Actions groupées</option>
                    <option value="approve">Approuver</option>
                    <option value="reject">Rejeter</option>
                    <option value="suspend">Suspendre</option>
                    <option value="delete">Supprimer</option>
                </select>
                <button onclick="applyBulkAction()" class="btn btn-primary">Appliquer</button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal détail produit -->
<div id="productModal" class="modal fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300 opacity-0 pointer-events-none" aria-hidden="true">
    <div class="modal-content card max-w-4xl w-full m-4 overflow-auto max-h-[90vh] transform scale-95 transition-transform duration-300 bg-white rounded-xl shadow-2xl">
        <div class="flex justify-between items-center mb-6 p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold">📦 Détails du produit</h2>
            <button onclick="closeProductModal()" class="btn btn-ghost text-2xl" aria-label="Fermer">✕</button>
        </div>
        <div id="productDetails" class="p-6"></div>
    </div>
</div>

<!-- Modal rejet -->
<div id="rejectModal" class="modal fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300 opacity-0 pointer-events-none" aria-hidden="true">
    <div class="modal-content card max-w-lg w-full m-4 overflow-auto max-h-[90vh] transform scale-95 transition-transform duration-300 bg-white rounded-xl shadow-2xl">
        <div class="flex justify-between items-center mb-6 p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold">❌ Rejeter le produit</h2>
            <button onclick="closeRejectModal()" class="btn btn-ghost text-2xl" aria-label="Fermer">✕</button>
        </div>
        <form method="POST" action="/admin/products/reject" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="product_id" id="reject_product_id">
            
            <div class="mb-6">
                <label class="block mb-2 font-semibold text-sm">Raison du rejet *</label>
                <select name="reason" class="input w-full" required>
                    <option value="">Choisir une raison</option>
                    <option value="quality">Qualité insuffisante</option>
                    <option value="content">Contenu inapproprié</option>
                    <option value="copyright">Violation copyright</option>
                    <option value="duplicate">Produit dupliqué</option>
                    <option value="incomplete">Information incomplète</option>
                    <option value="other">Autre</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label class="block mb-2 font-semibold text-sm">Message au vendeur (optionnel)</label>
                <textarea name="message" class="input w-full" rows="4" placeholder="Expliquez pourquoi le produit est rejeté..."></textarea>
            </div>
            
            <div class="flex gap-4 justify-end">
                <button type="button" onclick="closeRejectModal()" class="btn btn-secondary">Annuler</button>
                <button type="submit" class="btn btn-error">Rejeter le produit</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Modals */
.modal { 
    transition: opacity 300ms ease-in-out; 
}
.modal.open { 
    opacity: 1; 
    pointer-events: auto; 
}
.modal.open .modal-content { 
    transform: scale(1); 
}

/* Spinner de chargement */
.spinner { 
    border: 4px solid #e5e7eb; 
    border-top: 4px solid var(--primary-500, #3b82f6); 
    border-radius: 50%; 
    width: 40px; 
    height: 40px; 
    animation: spin 1s linear infinite; 
    margin: 2rem auto; 
}

@keyframes spin { 
    0% { transform: rotate(0deg); } 
    100% { transform: rotate(360deg); } 
}

/* Tableau optimisé */
table { 
    table-layout: auto; 
}

th, td { 
    white-space: nowrap; 
    vertical-align: middle;
}

/* Amélioration des boutons compacts */
.btn-sm { 
    padding: 0.375rem 0.75rem; 
    font-size: 0.875rem; 
    line-height: 1.25rem;
}

/* Hover sur les lignes du tableau */
tbody tr:hover {
    background: var(--bg-secondary, #f8fafc);
    cursor: pointer;
}

/* Amélioration mobile */
@media (max-width: 768px) {
    .overflow-x-auto { 
        -webkit-overflow-scrolling: touch; 
    }
    
    td, th { 
        padding: 0.75rem 0.5rem !important; 
        font-size: 0.875rem; 
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    h1 {
        font-size: 1.5rem;
    }
}

/* Badges plus compacts */
.badge {
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    border-radius: 9999px;
    font-weight: 600;
    display: inline-block;
}

.badge-warning { background: #fef3c7; color: #92400e; }
.badge-success { background: #d1fae5; color: #065f46; }
.badge-error { background: #fee2e2; color: #991b1b; }
.badge-secondary { background: #e5e7eb; color: #1f2937; }
</style>

<script>
// Gestion des modals
const modals = document.querySelectorAll('.modal');

modals.forEach(modal => {
    modal.addEventListener('click', e => { 
        if (e.target === modal) closeModal(modal.id); 
    });
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        modals.forEach(modal => { 
            if (modal.classList.contains('open')) closeModal(modal.id); 
        });
    }
});

function openModal(id) {
    const modal = document.getElementById(id);
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
    modal.querySelector('.modal-content').focus();
}

function closeModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
}

// Voir détails du produit
async function viewProduct(productId) {
    const details = document.getElementById('productDetails');
    details.innerHTML = '<div class="spinner"></div><p class="text-center mt-4 text-text-secondary">Chargement des détails...</p>';
    openModal('productModal');

    try {
        const response = await fetch(`/admin/products/${productId}/details`);
        if (!response.ok) throw new Error('Erreur réseau');
        const data = await response.json();
        
        details.innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <img src="${data.thumbnail_url || '/placeholder.jpg'}" 
                         alt="${data.title}" 
                         class="w-full rounded-xl shadow-md object-cover"
                         style="max-height: 400px;">
                </div>
                <div>
                    <h3 class="text-2xl font-bold mb-4">${data.title}</h3>
                    <p class="text-text-secondary mb-6 leading-relaxed">${data.description}</p>
                    
                    <div class="space-y-3">
                        <p class="flex justify-between">
                            <strong>Prix :</strong> 
                            <span class="text-primary-600 font-bold">${parseFloat(data.price).toFixed(2)}€</span>
                        </p>
                        <p class="flex justify-between">
                            <strong>Vendeur :</strong> 
                            <span>${data.seller_name} (@${data.seller_username})</span>
                        </p>
                        <p class="flex justify-between">
                            <strong>Téléchargements :</strong> 
                            <span>${data.downloads || 0}</span>
                        </p>
                        <p class="flex justify-between">
                            <strong>Note :</strong> 
                            <span>⭐ ${parseFloat(data.rating || 0).toFixed(1)}/5 (${data.reviews_count || 0} avis)</span>
                        </p>
                        <p class="flex justify-between">
                            <strong>Catégorie :</strong> 
                            <span>${data.category_name || 'N/A'}</span>
                        </p>
                        <p class="flex justify-between">
                            <strong>Date :</strong> 
                            <span>${new Date(data.created_at).toLocaleString('fr-FR')}</span>
                        </p>
                    </div>
                </div>
            </div>
        `;
    } catch (error) {
        details.innerHTML = `
            <div class="text-center p-8">
                <p class="text-error text-lg">❌ Erreur de chargement</p>
                <p class="text-text-secondary mt-2">${error.message}</p>
            </div>
        `;
    }
}

// Modal de rejet
function showRejectModal(productId) {
    document.getElementById('reject_product_id').value = productId;
    openModal('rejectModal');
}

function closeProductModal() { closeModal('productModal'); }
function closeRejectModal() { closeModal('rejectModal'); }

// Sélection globale
document.getElementById('select-all').addEventListener('change', e => {
    document.querySelectorAll('input[name="bulk[]"]').forEach(checkbox => {
        checkbox.checked = e.target.checked;
    });
});

// Actions groupées
async function applyBulkAction() {
    const action = document.getElementById('bulk-action').value;
    if (!action) {
        alert('⚠️ Veuillez sélectionner une action');
        return;
    }
    
    const selected = Array.from(document.querySelectorAll('input[name="bulk[]"]:checked'))
        .map(cb => cb.value);
    
    if (!selected.length) {
        alert('⚠️ Veuillez sélectionner au moins un produit');
        return;
    }

    if (!confirm(`Appliquer "${action}" sur ${selected.length} produit(s) sélectionné(s) ?`)) {
        return;
    }

    try {
        const response = await fetch('/admin/products/bulk', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ 
                action, 
                product_ids: selected, 
                csrf_token: '<?= $csrf_token ?>' 
            })
        });
        
        if (!response.ok) throw new Error('Erreur serveur');
        
        const result = await response.json();
        
        if (result.success) {
            alert(`✅ ${result.message || 'Action appliquée avec succès'}`);
            location.reload();
        } else {
            alert(`❌ ${result.error || 'Une erreur est survenue'}`);
        }
    } catch (error) {
        alert(`❌ Erreur : ${error.message}`);
    }
}
</script>