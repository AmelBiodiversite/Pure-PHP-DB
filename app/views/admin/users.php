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
<style>
/* === DESIGN MAQUETTE2 — GESTION UTILISATEURS === */

/* En-tête de page */
h1 { font-family: Georgia, serif !important; font-weight: 400 !important; color: #1e1208 !important; }
p[style*="color: var(--text-secondary)"] { font-family: 'Manrope', sans-serif !important; font-size: 12px !important; color: #6b5c4e !important; }

/* Bouton retour dashboard */
a.btn.btn-secondary { 
    background: #f5f1eb !important; 
    color: #6b5c4e !important; 
    border: 0.5px solid #ddd6c8 !important; 
    border-radius: 8px !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 12px !important;
    box-shadow: none !important;
}

/* Stats cards */
.card { 
    background: #fff !important; 
    border: 0.5px solid #ede8df !important; 
    border-radius: 14px !important; 
    box-shadow: none !important; 
}
.card h2 { font-family: Georgia, serif !important; font-weight: 400 !important; color: #1e1208 !important; font-size: 26px !important; }
/* Chiffre vendeurs (violet) */
h2[style*="color: var(--primary)"] { color: #7c6cf0 !important; }
/* Chiffre acheteurs (vert) */
h2[style*="color: var(--success)"] { color: #3a7d44 !important; }

/* Formulaire de filtres */
input[type="text"].input, select.input {
    border: 0.5px solid #ddd6c8 !important;
    border-radius: 10px !important;
    background: #faf9f5 !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 13px !important;
    color: #1e1208 !important;
}
input[type="text"].input:focus, select.input:focus {
    border-color: #7c6cf0 !important;
    box-shadow: 0 0 0 3px rgba(124,108,240,.1) !important;
    background: #fff !important;
}

/* Bouton filtrer */
button.btn.btn-primary, a.btn.btn-primary {
    background: #7c6cf0 !important;
    color: #fff !important;
    border: none !important;
    border-radius: 8px !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    box-shadow: none !important;
}
/* Bouton réinitialiser */
a.btn.btn-secondary {
    background: #f5f1eb !important;
    color: #6b5c4e !important;
    border: 0.5px solid #ddd6c8 !important;
    border-radius: 8px !important;
    font-size: 12px !important;
}

/* En-tête tableau */
thead tr { border-bottom: 0.5px solid #ede8df !important; }
th[style*="padding: 1rem"] {
    font-family: 'Manrope', sans-serif !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    color: #a0907e !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
}
/* Lignes tableau */
tr[style*="border-bottom: 1px solid var(--border)"] { border-bottom: 0.5px solid #f5f1eb !important; }
table tr:hover { background: #faf9f5 !important; }
td[style*="padding: 1rem"] { font-family: 'Manrope', sans-serif !important; font-size: 12px !important; }

/* Avatar placeholder */
div[style*="background: var(--primary)"][style*="border-radius: 50%"] {
    background: #ede9fe !important;
    color: #534ab7 !important;
}
/* Nom utilisateur */
div[style*="font-weight: 600"] { color: #1e1208 !important; }
/* @username / email */
div[style*="color: var(--text-secondary)"], td[style*="color: var(--text-secondary)"] {
    color: #a0907e !important;
    font-size: 11px !important;
}

/* Badges rôle et statut */
.badge.badge-primary { background: #ede9fe !important; color: #534ab7 !important; border-radius: 6px !important; font-size: 10px !important; padding: 2px 8px !important; }
.badge.badge-warning { background: #fef9e7 !important; color: #7d5a00 !important; border-radius: 6px !important; font-size: 10px !important; padding: 2px 8px !important; }
.badge.badge-secondary { background: #f5f1eb !important; color: #6b5c4e !important; border-radius: 6px !important; font-size: 10px !important; padding: 2px 8px !important; }
.badge.badge-success { background: #e4f1d8 !important; color: #2d6a35 !important; border-radius: 6px !important; font-size: 10px !important; padding: 2px 8px !important; }
.badge.badge-danger { background: #fce5df !important; color: #993c1d !important; border-radius: 6px !important; font-size: 10px !important; padding: 2px 8px !important; }

/* Boutons d'action (suspendre, activer, supprimer) */
.btn.btn-sm.btn-warning { background: #fef9e7 !important; color: #7d5a00 !important; border: 0.5px solid #e8d89a !important; border-radius: 7px !important; font-size: 11px !important; }
.btn.btn-sm.btn-success { background: #e4f1d8 !important; color: #2d6a35 !important; border: 0.5px solid #aed49b !important; border-radius: 7px !important; font-size: 11px !important; }
.btn.btn-sm.btn-danger { background: #fce5df !important; color: #993c1d !important; border: 0.5px solid #e5b8a8 !important; border-radius: 7px !important; font-size: 11px !important; }
</style>
