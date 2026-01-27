<?php
/**
 * MARKETFLOW PRO - GESTION UTILISATEURS ADMIN
 * Fichier : app/views/admin/users.php
 */

$users = $data['users'] ?? [];
$stats = $data['stats'] ?? [];
?>

<div class="container mt-8 mb-16">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 class="mb-2">👥 Gestion des utilisateurs</h1>
            <p style="color: var(--text-secondary);">Gérer tous les comptes utilisateurs</p>
        </div>
        <a href="/admin" class="btn btn-secondary">← Retour Dashboard</a>
    </div>

    <!-- Stats rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card">
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;">Total Utilisateurs</p>
            <h2 style="margin: 0; font-size: 2rem;"><?= number_format($stats['total'] ?? 0) ?></h2>
        </div>
        <div class="card">
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;">Vendeurs</p>
            <h2 style="margin: 0; font-size: 2rem; color: var(--primary);"><?= number_format($stats['sellers'] ?? 0) ?></h2>
        </div>
        <div class="card">
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;">Acheteurs</p>
            <h2 style="margin: 0; font-size: 2rem; color: var(--success);"><?= number_format($stats['buyers'] ?? 0) ?></h2>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-8">
        <form method="GET" action="/admin/users" style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <input type="text" 
                   name="search" 
                   placeholder="🔍 Rechercher par nom, email, username..." 
                   class="input"
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                   style="flex: 1; min-width: 300px;">
            
            <select name="role" class="input" style="width: 150px;">
                <option value="">Tous les rôles</option>
                <option value="buyer" <?= ($_GET['role'] ?? '') === 'buyer' ? 'selected' : '' ?>>Acheteurs</option>
                <option value="seller" <?= ($_GET['role'] ?? '') === 'seller' ? 'selected' : '' ?>>Vendeurs</option>
                <option value="admin" <?= ($_GET['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>

            <select name="status" class="input" style="width: 150px;">
                <option value="">Tous les statuts</option>
                <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Actifs</option>
                <option value="suspended" <?= ($_GET['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspendus</option>
            </select>

            <button type="submit" class="btn btn-primary">Filtrer</button>
            <a href="/admin/users" class="btn btn-secondary">Réinitialiser</a>
        </form>
    </div>

    <!-- Liste utilisateurs -->
    <div class="card">
        <?php if (empty($users)): ?>
            <div style="text-align: center; padding: 4rem; color: var(--text-secondary);">
                <p>Aucun utilisateur trouvé</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border);">
                            <th style="padding: 1rem; text-align: left;">Utilisateur</th>
                            <th style="padding: 1rem; text-align: left;">Email</th>
                            <th style="padding: 1rem; text-align: left;">Rôle</th>
                            <th style="padding: 1rem; text-align: left;">Inscription</th>
                            <th style="padding: 1rem; text-align: left;">Statut</th>
                            <th style="padding: 1rem; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr style="border-bottom: 1px solid var(--border);" class="hover-lift">
                                <td style="padding: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <?php if ($user['avatar_url']): ?>
                                            <img src="<?= htmlspecialchars($user['avatar_url']) ?>" 
                                                 alt="<?= htmlspecialchars($user['full_name']) ?>"
                                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                        <?php else: ?>
                                            <div style="width: 40px; height: 40px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                <?= strtoupper(substr($user['full_name'] ?? $user['username'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($user['full_name'] ?? $user['username']) ?></div>
                                            <div style="color: var(--text-secondary); font-size: 0.875rem;">
                                                @<?= htmlspecialchars($user['username']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem; color: var(--text-secondary);">
                                    <?= htmlspecialchars($user['email']) ?>
                                </td>
                                <td style="padding: 1rem;">
                                    <span class="badge badge-<?= $user['role'] === 'seller' ? 'primary' : ($user['role'] === 'admin' ? 'warning' : 'secondary') ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; color: var(--text-secondary);">
                                    <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                                </td>
                                <td style="padding: 1rem;">
                                    <span class="badge badge-<?= $user['is_active'] ? 'success' : 'danger' ?>">
                                        <?= $user['is_active'] ? 'Actif' : 'Suspendu' ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                       <!-- <button onclick="viewUser(<?= e($user['id']) ?>)" 
                                                class="btn btn-sm btn-secondary" 
                                                title="Voir détails">
                                            👁️
                                        </button>-->
                                        
                                        <?php if ($user['is_active']): ?>
    <form method="POST" action="/admin/users/<?= e($user['id']) ?>/suspend" style="display: inline;">
        <button type="submit" 
                class="btn btn-sm btn-warning" 
                title="Suspendre"
                onclick="return confirm('Suspendre cet utilisateur ?')">
            ⏸️
        </button>
    </form>
<?php else: ?>
    <form method="POST" action="/admin/users/<?= e($user['id']) ?>/activate" style="display: inline;">
        <button type="submit" 
                class="btn btn-sm btn-success" 
                title="Activer"
                onclick="return confirm('Réactiver cet utilisateur ?')">
            ▶️
        </button>
    </form>
<?php endif; ?>

<form method="POST" action="/admin/users/<?= e($user['id']) ?>/delete" style="display: inline;">
    <button type="submit" 
            class="btn btn-sm btn-danger" 
            title="Supprimer"
            onclick="return confirm('⚠️ ATTENTION ! Supprimer définitivement cet utilisateur ?')">
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
function viewUser(userId) {
    // Ouvrir modal et charger les détails
    const modal = document.getElementById('userModal');
    const details = document.getElementById('userDetails');
    
    modal.style.display = 'flex';
    details.innerHTML = '<p style="text-align: center; padding: 2rem;">Chargement...</p>';
    
    // Simuler chargement (à remplacer par vraie requête AJAX)
    setTimeout(() => {
        details.innerHTML = `
            <div style="text-align: center; padding: 2rem;">
                <p>Fonctionnalité à implémenter avec AJAX</p>
                <p style="color: var(--text-secondary); margin-top: 1rem;">
                    User ID: ${userId}
                </p>
            </div>
        `;
    }, 500);
}

function closeUserModal() {
    document.getElementById('userModal').style.display = 'none';
}

// Fermer modal en cliquant à l'extérieur
window.onclick = function(event) {
    const modal = document.getElementById('userModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>