<?php
/**
 * ============================================================================
 * MARKETFLOW PRO - SECURITY DASHBOARD v3.1 FINAL COMPLETE
 * ============================================================================
 * 
 * Vue sécurisée du tableau de bord de sécurité avec :
 *   ✅ Validation stricte des données PHP
 *   ✅ Échappement XSS systématique avec e()
 *   ✅ Token CSRF exposé pour JavaScript
 *   ✅ JavaScript séparé dans security-dashboard.js
 *   ✅ Auto-refresh AJAX (sans reload brutal)
 *   ✅ Timeline des 7 derniers jours
 *   ✅ Tableau des événements récents
 * 
 * @package    MarketFlow
 * @subpackage Views\Admin
 * @version    3.1-COMPLETE
 * @author     A.Devance
 * @date       2026-02-12
 */

// ============================================================================
// VALIDATION STRICTE DES DONNÉES REÇUES DU CONTRÔLEUR
// ============================================================================

/**
 * Fonction de validation et sanitisation des données
 * 
 * @param mixed  $value   Valeur à valider
 * @param string $type    Type attendu (string, int, array, etc.)
 * @param mixed  $default Valeur par défaut si validation échoue
 * @return mixed Valeur validée ou valeur par défaut
 */
function validateAndSanitize($value, $type = 'string', $default = null) {
    // Si la valeur est null ou non définie, retourner la valeur par défaut
    if ($value === null || !isset($value)) {
        return $default;
    }
    
    // Validation selon le type
    switch ($type) {
        case 'int':
            return filter_var($value, FILTER_VALIDATE_INT) !== false ? (int)$value : $default;
        case 'float':
            return filter_var($value, FILTER_VALIDATE_FLOAT) !== false ? (float)$value : $default;
        case 'bool':
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
        case 'email':
            return filter_var($value, FILTER_VALIDATE_EMAIL) ?: $default;
        case 'url':
            return filter_var($value, FILTER_VALIDATE_URL) ?: $default;
        case 'array':
            return is_array($value) ? $value : $default;
        case 'string':
        default:
            return is_string($value) ? trim($value) : $default;
    }
}

// Variables principales (passées par le contrôleur SecurityController::index())
$title           = validateAndSanitize($title ?? 'Sécurité', 'string', 'Sécurité');
$stats           = validateAndSanitize($stats ?? [], 'array', []);
$suspiciousIPs   = validateAndSanitize($suspiciousIPs ?? [], 'array', []);
$recentEvents    = validateAndSanitize($recentEvents ?? [], 'array', []);
$pagination      = validateAndSanitize($pagination ?? [], 'array', []);
$filters         = validateAndSanitize($filters ?? [], 'array', []);
$eventTypes      = validateAndSanitize($eventTypes ?? [], 'array', []);
$severityLevels  = validateAndSanitize($severityLevels ?? [], 'array', []);
$chartData       = validateAndSanitize($chartData ?? [], 'array', []);
$criticalEvents  = validateAndSanitize($criticalEvents ?? 0, 'int', 0);
$timelineData    = validateAndSanitize($timelineData ?? [], 'array', []);

// Validation des statistiques individuelles (avec valeurs par défaut sécurisées)
$totalThreats    = validateAndSanitize($stats['total_threats'] ?? 0, 'int', 0);
$blockedIPs      = validateAndSanitize($stats['blocked_ips'] ?? 0, 'int', 0);
$suspiciousCount = validateAndSanitize($stats['suspicious_count'] ?? 0, 'int', 0);
$last24h         = validateAndSanitize($stats['last_24h'] ?? 0, 'int', 0);

// ============================================================================
// EXPOSITION SÉCURISÉE DU TOKEN CSRF POUR JAVASCRIPT
// ============================================================================

// Récupérer le token CSRF de la session (généré par le middleware ou helpers)
$csrfToken = $_SESSION['csrf_token'] ?? '';

// Si pas de token en session, en générer un nouveau (fallback)
if (empty($csrfToken)) {
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrfToken;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e($csrfToken) ?>">
    <title><?= e($title) ?> - MarketFlow Admin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Styles inline pour le dashboard de sécurité -->
    <style>
        /* ==================== VARIABLES CSS ==================== */
        :root {
            --color-danger: #dc3545;
            --color-warning: #ffc107;
            --color-success: #28a745;
            --color-info: #17a2b8;
            --color-primary: #007bff;
            --color-dark: #343a40;
            --transition-speed: 0.3s;
        }
        
        /* ==================== LAYOUT ==================== */
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* ==================== CARTES DE STATS ==================== */
        .stat-card {
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, var(--card-color), transparent);
        }
        
        .stat-card.danger { --card-color: var(--color-danger); }
        .stat-card.warning { --card-color: var(--color-warning); }
        .stat-card.success { --card-color: var(--color-success); }
        .stat-card.info { --card-color: var(--color-info); }
        
        .stat-card .stat-icon {
            font-size: 3rem;
            opacity: 0.15;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            color: var(--color-dark);
        }
        
        .stat-card p {
            margin: 5px 0 0;
            color: #6c757d;
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        /* ==================== TABLEAUX ==================== */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
            cursor: pointer;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            color: #495057;
            border-top: none;
        }
        
        /* ==================== BADGES ==================== */
        .badge {
            padding: 6px 12px;
            font-weight: 600;
            border-radius: 6px;
        }
        
        /* ==================== BOUTONS D'ACTION ==================== */
        .action-btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all var(--transition-speed);
            border: none;
            cursor: pointer;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .btn-block-ip {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }
        
        .btn-unblock-ip {
            background: linear-gradient(135deg, #28a745, #218838);
            color: white;
        }
        
        .btn-whitelist-ip {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }
        
        /* ==================== MODAL ==================== */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        
        .modal-overlay.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            animation: modalSlideIn 0.3s ease-out;
        }
        
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* ==================== TOAST NOTIFICATIONS ==================== */
        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
        }
        
        .toast-notification {
            background: white;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            animation: toastSlideIn 0.3s ease-out;
            border-left: 4px solid var(--toast-color);
        }
        
        @keyframes toastSlideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .toast-notification.success { --toast-color: var(--color-success); }
        .toast-notification.error { --toast-color: var(--color-danger); }
        .toast-notification.warning { --toast-color: var(--color-warning); }
        .toast-notification.info { --toast-color: var(--color-info); }
        
        /* ==================== GRAPHIQUES ==================== */
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 20px;
        }
        
        /* ==================== ANIMATIONS DES COMPTEURS ==================== */
        .counter {
            display: inline-block;
            transition: all 0.5s ease;
        }
        
        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 768px) {
            .stat-card h3 {
                font-size: 2rem;
            }
            
            .stat-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- ==================== HEADER ==================== -->
    <div class="admin-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">🛡️ <?= e($title) ?></h1>
                <p class="text-muted mb-0">Surveillance et gestion de la sécurité en temps réel</p>
            </div>
            <div>
                <button class="btn btn-outline-primary" onclick="location.reload()">
                    🔄 Actualiser
                </button>
                <a href="/admin/security/export/csv" class="btn btn-outline-success">
                    📥 Exporter CSV
                </a>
            </div>
        </div>
        
        <!-- ==================== STATISTIQUES PRINCIPALES ==================== -->
        <div class="row mb-4">
            <!-- Total des menaces -->
            <div class="col-lg-3 col-md-6">
                <div class="stat-card danger">
                    <div class="stat-icon">⚠️</div>
                    <h3 class="counter" data-target="<?= e($totalThreats) ?>">0</h3>
                    <p>Menaces détectées (total)</p>
                </div>
            </div>
            
            <!-- IPs bloquées -->
            <div class="col-lg-3 col-md-6">
                <div class="stat-card warning">
                    <div class="stat-icon">🚫</div>
                    <h3 class="counter" data-target="<?= e($blockedIPs) ?>">0</h3>
                    <p>Adresses IP bloquées</p>
                </div>
            </div>
            
            <!-- IPs suspectes -->
            <div class="col-lg-3 col-md-6">
                <div class="stat-card info">
                    <div class="stat-icon">👁️</div>
                    <h3 class="counter" data-target="<?= e($suspiciousCount) ?>">0</h3>
                    <p>IPs sous surveillance</p>
                </div>
            </div>
            
            <!-- Dernières 24h -->
            <div class="col-lg-3 col-md-6">
                <div class="stat-card success">
                    <div class="stat-icon">🕐</div>
                    <h3 class="counter" data-target="<?= e($last24h) ?>">0</h3>
                    <p>Événements (24h)</p>
                </div>
            </div>
        </div>
        
        <!-- ==================== IPs SUSPECTES ==================== -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">🔍 Adresses IP suspectes</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($suspiciousIPs)): ?>
                            <p class="text-muted text-center py-4">
                                ✅ Aucune adresse IP suspecte détectée actuellement
                            </p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Adresse IP</th>
                                            <th>Tentatives</th>
                                            <th>Dernière activité</th>
                                            <th>Score de menace</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($suspiciousIPs as $ip): ?>
                                        <tr>
                                            <td>
                                                <code class="text-dark"><?= e($ip['ip'] ?? 'N/A') ?></code>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning text-dark">
                                                    <?= e($ip['attempts'] ?? 0) ?> tentatives
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= e($ip['last_seen'] ?? 'Inconnue') ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php
                                                $threatScore = $ip['threat_score'] ?? 0;
                                                $badgeClass = $threatScore >= 80 ? 'danger' : ($threatScore >= 50 ? 'warning' : 'info');
                                                ?>
                                                <span class="badge bg-<?= $badgeClass ?>">
                                                    <?= e($threatScore) ?>/100
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <button 
                                                    class="action-btn btn-block-ip btn-sm" 
                                                    onclick="showBlockModal('<?= e($ip['ip'] ?? '') ?>')"
                                                >
                                                    🚫 Bloquer
                                                </button>
                                                <button 
                                                    class="action-btn btn-whitelist-ip btn-sm" 
                                                    onclick="whitelistIP('<?= e($ip['ip'] ?? '') ?>')"
                                                >
                                                    ✅ Whitelist
                                                </button>
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
        </div>
        
        <!-- ==================== GRAPHIQUES ==================== -->
        <div class="row mb-4">
            <!-- Répartition par type d'événement -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">📊 Répartition par type</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="donutChart" style="max-height:300px;"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Répartition par sévérité -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">⚡ Répartition par sévérité</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="severityChart" style="max-height:300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ==================== TIMELINE DES 7 DERNIERS JOURS ==================== -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">📈 Évolution des menaces (7 derniers jours)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="timelineChart" width="400" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ==================== ÉVÉNEMENTS RÉCENTS ==================== -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">📋 Événements de sécurité récents</h5>
                        <span class="badge bg-info"><?= count($recentEvents) ?> événements</span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentEvents)): ?>
                            <p class="text-muted text-center py-4">
                                ℹ️ Aucun événement de sécurité récent
                            </p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Date & Heure</th>
                                            <th>Type d'événement</th>
                                            <th>Sévérité</th>
                                            <th>Adresse IP</th>
                                            <th>Détails</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentEvents as $event): ?>
                                        <tr>
                                            <td>
                                                <small class="text-muted">
                                                    <?= e($event['timestamp'] ?? '') ?>
                                                </small>
                                            </td>
                                            <td>
                                                <code class="text-primary">
                                                    <?= e($event['event_type'] ?? '') ?>
                                                </code>
                                            </td>
                                            <td>
                                                <?php
                                                $severity = $event['severity'] ?? 'INFO';
                                                $badgeClass = match($severity) {
                                                    'CRITICAL' => 'danger',
                                                    'WARNING' => 'warning',
                                                    'INFO' => 'info',
                                                    default => 'secondary'
                                                };
                                                ?>
                                                <span class="badge bg-<?= $badgeClass ?>">
                                                    <?= e($severity) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <code class="text-dark">
                                                    <?= e($event['ip'] ?? 'N/A') ?>
                                                </code>
                                            </td>
                                            <td class="text-truncate" style="max-width: 400px;">
                                                <small class="text-muted">
                                                    <?= e($event['data'] ?? '') ?>
                                                </small>
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
        </div>
        
    </div><!-- Fin admin-container -->
    
    <!-- ==================== CONTAINER POUR LES TOASTS ==================== -->
    <div id="toast-container"></div>
    
    <!-- ==================== MODAL BLOCAGE IP ==================== -->
    <div id="blockModal" class="modal-overlay">
        <div class="modal-content">
            <h4 class="mb-3">🚫 Bloquer une adresse IP</h4>
            <p class="text-muted mb-3">
                Vous êtes sur le point de bloquer l'IP : <code id="ipToBlock" class="text-danger"></code>
            </p>
            <div class="mb-3">
                <label for="blockReason" class="form-label">Raison du blocage (optionnel) :</label>
                <textarea 
                    id="blockReason" 
                    class="form-control" 
                    rows="3" 
                    placeholder="Ex: Tentatives de brute force sur /admin/login"
                ></textarea>
            </div>
            <div class="d-flex gap-2 justify-content-end">
                <button class="btn btn-secondary" onclick="closeBlockModal()">
                    Annuler
                </button>
                <button class="btn btn-danger" onclick="confirmBlockIP()">
                    🚫 Confirmer le blocage
                </button>
            </div>
        </div>
    </div>
    
    <!-- ==================== SCRIPTS ==================== -->
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Exposition des données PHP pour JavaScript -->
    <script>
        // Exposition sécurisée du token CSRF pour toutes les requêtes AJAX
        window.CSRF_TOKEN = <?= json_encode($csrfToken) ?>;
        
        // Exposition des données pour les graphiques Chart.js
        window.CHART_DATA = <?= json_encode($chartData) ?>;
        window.TIMELINE_DATA = <?= json_encode($timelineData) ?>;
        window.CRITICAL_EVENTS = <?= $criticalEvents ?>;
    </script>
    
    <!-- 
        ============================================================
        JAVASCRIPT EXTERNE (security-dashboard.js)
        ============================================================
        
        Ce fichier contient toute la logique JavaScript :
        - Auto-refresh AJAX toutes les 60 secondes
        - Protection CSRF sur toutes les requêtes
        - Gestion des modales et toasts
        - Animations des compteurs
        - Initialisation des graphiques Chart.js
        - Gestion des actions (bloquer/débloquer/whitelist)
    -->
    <script src="/js/security-dashboard.js"></script>
    
</body>
</html>
