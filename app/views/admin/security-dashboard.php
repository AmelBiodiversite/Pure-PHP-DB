<?php
/**
 * ============================================================================
 * MARKETFLOW PRO - SECURITY DASHBOARD v4.0
 * ============================================================================
 * 
 * Vue sécurisée du tableau de bord de sécurité.
 * 
 * NOUVEAUTÉS v4 :
 *   ✅ Zéro Bootstrap — Design System MarketFlow pur
 *   ✅ Score de santé global avec indicateur visuel
 *   ✅ Barre de filtres (date, type, sévérité, IP, recherche)
 *   ✅ Pagination complète des événements
 *   ✅ Graphiques Chart.js correctement initialisés
 *   ✅ Config JS unifiée (window.MARKETFLOW_CONFIG)
 *   ✅ Échappement XSS systématique avec e()
 *   ✅ Token CSRF exposé de manière sécurisée
 *
 * @package    MarketFlow
 * @subpackage Views\Admin
 * @version    4.0
 * @author     A.Devance
 */

// ============================================================================
// VALIDATION DES DONNÉES REÇUES DU CONTRÔLEUR
// ============================================================================

/**
 * Valide et sanitise une valeur selon le type attendu
 * Protège contre les données corrompues ou manquantes
 *
 * @param mixed  $value   Valeur brute à valider
 * @param string $type    Type attendu (string, int, float, bool, array, email, url)
 * @param mixed  $default Valeur de repli si validation échoue
 * @return mixed Valeur validée ou valeur par défaut
 */
function validateAndSanitize($value, $type = 'string', $default = null) {
    if ($value === null || !isset($value)) {
        return $default;
    }
    
    switch ($type) {
        case 'int':
            return filter_var($value, FILTER_VALIDATE_INT) !== false ? (int)$value : $default;
        case 'float':
            return filter_var($value, FILTER_VALIDATE_FLOAT) !== false ? (float)$value : $default;
        case 'bool':
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
        case 'array':
            return is_array($value) ? $value : $default;
        case 'string':
        default:
            return is_string($value) ? trim($value) : $default;
    }
}

// --- Variables principales (passées par SecurityController::index()) ---
$title           = validateAndSanitize($title ?? 'Monitoring Sécurité', 'string', 'Monitoring Sécurité');
$stats           = validateAndSanitize($stats ?? [], 'array', []);
$suspiciousIPs   = validateAndSanitize($suspiciousIPs ?? [], 'array', []);
$recentEvents    = validateAndSanitize($recentEvents ?? [], 'array', []);
$pagination      = validateAndSanitize($pagination ?? [], 'array', []);
$filters         = validateAndSanitize($filters ?? [], 'array', []);
$eventTypes      = validateAndSanitize($eventTypes ?? [], 'array', []);
$chartLabels     = validateAndSanitize($chartLabels ?? [], 'array', []);
$chartData       = validateAndSanitize($chartData ?? [], 'array', []);
$chartColors     = validateAndSanitize($chartColors ?? [], 'array', []);
$timelineLabels  = validateAndSanitize($timelineLabels ?? [], 'array', []);
$timelineCritical = validateAndSanitize($timelineCritical ?? [], 'array', []);
$timelineWarning = validateAndSanitize($timelineWarning ?? [], 'array', []);
$timelineInfo    = validateAndSanitize($timelineInfo ?? [], 'array', []);

// --- Totaux par sévérité ---
$totalEvents    = validateAndSanitize($totalEvents ?? 0, 'int', 0);
$criticalEvents = validateAndSanitize($criticalEvents ?? 0, 'int', 0);
$warningEvents  = validateAndSanitize($warningEvents ?? 0, 'int', 0);
$infoEvents     = validateAndSanitize($infoEvents ?? 0, 'int', 0);

// --- Pagination ---
$currentPage = validateAndSanitize($pagination['page'] ?? 1, 'int', 1);
$totalPages  = validateAndSanitize($pagination['total_pages'] ?? 1, 'int', 1);
$totalItems  = validateAndSanitize($pagination['total'] ?? 0, 'int', 0);
$perPage     = validateAndSanitize($pagination['per_page'] ?? 50, 'int', 50);

// --- Score de santé (0-100, calculé selon les événements critiques) ---
// Plus il y a d'événements critiques, plus le score baisse
$healthScore = max(0, min(100, 100 - ($criticalEvents * 10) - ($warningEvents * 2)));
if ($healthScore >= 80) {
    $healthClass  = 'excellent';
    $healthLabel  = 'Excellent';
    $healthDesc   = 'Aucune menace significative détectée. Le système est bien protégé.';
} elseif ($healthScore >= 60) {
    $healthClass  = 'good';
    $healthLabel  = 'Bon';
    $healthDesc   = 'Quelques alertes mineures, mais la sécurité est globalement solide.';
} elseif ($healthScore >= 30) {
    $healthClass  = 'warning';
    $healthLabel  = 'Attention requise';
    $healthDesc   = 'Des menaces ont été détectées. Vérifiez les événements critiques.';
} else {
    $healthClass  = 'critical';
    $healthLabel  = 'Alerte critique';
    $healthDesc   = 'Menaces actives détectées ! Action immédiate recommandée.';
}

// Calcul du cercle SVG pour le score (circumference = 2πr = 2 × π × 42 ≈ 264)
$circumference = 264;
$dashOffset = $circumference - ($circumference * $healthScore / 100);

// ============================================================================
// TOKEN CSRF POUR JAVASCRIPT
// ============================================================================

$csrfToken = $_SESSION['csrf_token'] ?? '';
if (empty($csrfToken)) {
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrfToken;
}

// --- Compteur d'IPs bloquées (récupéré du contrôleur ou calculé) ---
$blockedIPsCount = validateAndSanitize($stats['blocked_ips'] ?? 0, 'int', 0);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e($csrfToken) ?>">
    <title><?= e($title) ?> — MarketFlow Admin</title>
    
    <!-- Chart.js pour les graphiques (seule dépendance externe) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Design System MarketFlow (tes propres fichiers) -->
    <link rel="stylesheet" href="<?= CSS_URL ?>/style.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/animations.css">
    
    <!-- CSS dédié au Security Dashboard -->
    <link rel="stylesheet" href="<?= CSS_URL ?>/security-dashboard.css">
</head>
<body>

<!-- ================================================================
     CONTAINER PRINCIPAL DU DASHBOARD
     ================================================================ -->
<div class="security-dashboard">

    <!-- ==================== HEADER ==================== -->
    <header class="sec-header">
        <div>
            <h1 class="sec-header__title">🛡️ <?= e($title) ?></h1>
            <p class="sec-header__subtitle">
                <span class="sec-header__live-dot"></span>
                Surveillance en temps réel
                — Dernière mise à jour : <span id="lastUpdate"><?= date('H:i:s') ?></span>
            </p>
        </div>
        <div class="sec-header__actions">
            <!-- Bouton actualiser manuellement -->
            <button class="sec-btn sec-btn--outline" onclick="refreshStats()" id="btnRefresh">
                🔄 Actualiser
            </button>
            <!-- Export CSV -->
            <a href="/admin/security/export/csv" class="sec-btn sec-btn--outline">
                📥 Export CSV
            </a>
            <!-- Export JSON -->
            <a href="/admin/security/export/json" class="sec-btn sec-btn--outline">
                📄 Export JSON
            </a>
        </div>
    </header>

    <!-- ==================== SCORE DE SANTÉ GLOBAL ==================== -->
    <div class="sec-health sec-health--<?= $healthClass ?>">
        <!-- Cercle SVG du score -->
        <div class="sec-health__score">
            <svg viewBox="0 0 100 100">
                <!-- Cercle de fond -->
                <circle class="sec-health__score-bg" cx="50" cy="50" r="42"/>
                <!-- Cercle de progression (animé via stroke-dashoffset) -->
                <circle 
                    class="sec-health__score-fill" 
                    cx="50" cy="50" r="42"
                    stroke-dasharray="<?= $circumference ?>"
                    stroke-dashoffset="<?= $dashOffset ?>"
                />
            </svg>
            <!-- Valeur numérique au centre -->
            <span class="sec-health__score-text" id="healthScoreValue"><?= $healthScore ?></span>
        </div>
        
        <!-- Détails du score -->
        <div class="sec-health__details">
            <div class="sec-health__label">Score de sécurité</div>
            <div class="sec-health__status"><?= e($healthLabel) ?></div>
            <div class="sec-health__summary"><?= e($healthDesc) ?></div>
        </div>

        <!-- Mini métriques compactes -->
        <div class="sec-health__metrics">
            <div class="sec-health__metric">
                <div class="sec-health__metric-value" style="color:var(--sec-critical)"><?= $criticalEvents ?></div>
                <div class="sec-health__metric-label">Critiques</div>
            </div>
            <div class="sec-health__metric">
                <div class="sec-health__metric-value" style="color:var(--sec-warning)"><?= $warningEvents ?></div>
                <div class="sec-health__metric-label">Warnings</div>
            </div>
            <div class="sec-health__metric">
                <div class="sec-health__metric-value" style="color:var(--sec-info)"><?= $infoEvents ?></div>
                <div class="sec-health__metric-label">Info</div>
            </div>
            <div class="sec-health__metric">
                <div class="sec-health__metric-value" style="color:var(--sec-success)"><?= $totalEvents ?></div>
                <div class="sec-health__metric-label">Total (7j)</div>
            </div>
        </div>
    </div>

    <!-- ==================== 4 CARTES DE STATISTIQUES ==================== -->
    <div class="sec-stats">
        <!-- Menaces détectées -->
        <div class="sec-stat-card sec-stat-card--critical">
            <span class="sec-stat-card__icon">⚠️</span>
            <div class="sec-stat-card__value" id="stat-threats"><?= $criticalEvents ?></div>
            <div class="sec-stat-card__label">Menaces critiques</div>
        </div>

        <!-- IPs bloquées -->
        <div class="sec-stat-card sec-stat-card--warning">
            <span class="sec-stat-card__icon">🚫</span>
            <div class="sec-stat-card__value" id="stat-blocked"><?= $blockedIPsCount ?></div>
            <div class="sec-stat-card__label">IPs bloquées</div>
        </div>

        <!-- IPs suspectes sous surveillance -->
        <div class="sec-stat-card sec-stat-card--info">
            <span class="sec-stat-card__icon">👁️</span>
            <div class="sec-stat-card__value" id="stat-suspicious"><?= count($suspiciousIPs) ?></div>
            <div class="sec-stat-card__label">IPs sous surveillance</div>
        </div>

        <!-- Total événements 7 jours -->
        <div class="sec-stat-card sec-stat-card--success">
            <span class="sec-stat-card__icon">📊</span>
            <div class="sec-stat-card__value" id="stat-total"><?= $totalEvents ?></div>
            <div class="sec-stat-card__label">Événements (7 jours)</div>
        </div>
    </div>

    <!-- ==================== BARRE DE FILTRES ==================== -->
    <form class="sec-filters" method="GET" action="/admin/security" id="filtersForm">
        <!-- Filtre par date de début -->
        <div class="sec-filter-group">
            <label for="filter-date-from">Date début</label>
            <input type="date" id="filter-date-from" name="date_from" 
                   value="<?= e($filters['date_from'] ?? '') ?>">
        </div>

        <!-- Filtre par date de fin -->
        <div class="sec-filter-group">
            <label for="filter-date-to">Date fin</label>
            <input type="date" id="filter-date-to" name="date_to" 
                   value="<?= e($filters['date_to'] ?? '') ?>">
        </div>

        <!-- Filtre par type d'événement -->
        <div class="sec-filter-group">
            <label for="filter-type">Type</label>
            <select id="filter-type" name="event_type">
                <option value="">Tous les types</option>
                <?php foreach ($eventTypes as $type): ?>
                    <option value="<?= e($type) ?>" 
                        <?= ($filters['event_type'] ?? '') === $type ? 'selected' : '' ?>>
                        <?= e($type) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Filtre par sévérité -->
        <div class="sec-filter-group">
            <label for="filter-severity">Sévérité</label>
            <select id="filter-severity" name="severity">
                <option value="">Toutes</option>
                <option value="CRITICAL" <?= ($filters['severity'] ?? '') === 'CRITICAL' ? 'selected' : '' ?>>
                    🔴 Critique
                </option>
                <option value="WARNING" <?= ($filters['severity'] ?? '') === 'WARNING' ? 'selected' : '' ?>>
                    🟡 Warning
                </option>
                <option value="INFO" <?= ($filters['severity'] ?? '') === 'INFO' ? 'selected' : '' ?>>
                    🔵 Info
                </option>
            </select>
        </div>

        <!-- Filtre par IP -->
        <div class="sec-filter-group">
            <label for="filter-ip">Adresse IP</label>
            <input type="text" id="filter-ip" name="ip" placeholder="Ex: 192.168.1.1"
                   value="<?= e($filters['ip'] ?? '') ?>">
        </div>

        <!-- Recherche textuelle -->
        <div class="sec-filter-group">
            <label for="filter-search">Recherche</label>
            <input type="text" id="filter-search" name="search" placeholder="URI, email, IP..."
                   value="<?= e($filters['search'] ?? '') ?>">
        </div>

        <!-- Boutons d'action des filtres -->
        <div class="sec-filter-actions">
            <button type="submit" class="sec-btn sec-btn--primary">🔍 Filtrer</button>
            <a href="/admin/security" class="sec-btn sec-btn--outline">✕ Reset</a>
        </div>
    </form>

    <!-- ==================== IPs SUSPECTES ==================== -->
    <div class="sec-panel">
        <div class="sec-panel__header">
            <h2 class="sec-panel__title">🔍 Adresses IP suspectes</h2>
            <span class="sec-panel__badge"><?= count($suspiciousIPs) ?> IP(s)</span>
        </div>
        <div class="sec-panel__body sec-panel__body--flush" id="suspicious-ips-container">
            <?php if (empty($suspiciousIPs)): ?>
                <div class="sec-empty">
                    <div class="sec-empty__icon">✅</div>
                    <div class="sec-empty__text">Aucune adresse IP suspecte détectée</div>
                </div>
            <?php else: ?>
                <div class="sec-ip-list">
                    <?php foreach ($suspiciousIPs as $ip): ?>
                        <?php 
                            // Score de menace (0-100) depuis la requête SQL
                            $score = (int)($ip['severity_score'] ?? 0);
                            $scoreClass = $score >= 80 ? 'high' : ($score >= 40 ? 'medium' : 'low');
                            // Pourcentage pour la barre de progression
                            $scorePercent = min(100, $score);
                        ?>
                        <div class="sec-ip-item">
                            <!-- Adresse IP (monospace) -->
                            <span class="sec-ip-item__address"><?= e($ip['ip'] ?? 'N/A') ?></span>
                            
                            <!-- Statistiques compactes -->
                            <div class="sec-ip-item__stats">
                                <span class="sec-ip-item__stat">
                                    Tentatives : <strong><?= (int)($ip['total'] ?? 0) ?></strong>
                                </span>
                                <span class="sec-ip-item__stat">
                                    Échecs login : <strong><?= (int)($ip['failed_logins'] ?? 0) ?></strong>
                                </span>
                                <span class="sec-ip-item__stat">
                                    Score :
                                    <div class="sec-threat-bar" title="Score de menace : <?= $score ?>">
                                        <div class="sec-threat-bar__fill sec-threat-bar__fill--<?= $scoreClass ?>"
                                             style="width: <?= $scorePercent ?>%"></div>
                                    </div>
                                </span>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="sec-ip-item__actions">
                                <button class="sec-btn sec-btn--sm sec-btn--outline"
                                        onclick="filterByIP('<?= e($ip['ip'] ?? '') ?>')">
                                    Filtrer
                                </button>
                                <button class="sec-btn sec-btn--sm sec-btn--danger"
                                        onclick="openBlockModal('<?= e($ip['ip'] ?? '') ?>')">
                                    🚫 Bloquer
                                </button>
                                <button class="sec-btn sec-btn--sm sec-btn--success"
                                        onclick="whitelistIP('<?= e($ip['ip'] ?? '') ?>')">
                                    ✅ Whitelist
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ==================== GRAPHIQUES (2 colonnes) ==================== -->
    <div class="sec-charts-grid">
        <!-- Donut : répartition par type d'événement -->
        <div class="sec-panel">
            <div class="sec-panel__header">
                <h2 class="sec-panel__title">📊 Répartition par type</h2>
            </div>
            <div class="sec-panel__body">
                <div class="sec-chart-container">
                    <canvas id="donutChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Barres horizontales : répartition par sévérité -->
        <div class="sec-panel">
            <div class="sec-panel__header">
                <h2 class="sec-panel__title">⚡ Répartition par sévérité</h2>
            </div>
            <div class="sec-panel__body">
                <div class="sec-chart-container">
                    <canvas id="severityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== TIMELINE (7 derniers jours) ==================== -->
    <div class="sec-panel">
        <div class="sec-panel__header">
            <h2 class="sec-panel__title">📈 Évolution des menaces (7 derniers jours)</h2>
        </div>
        <div class="sec-panel__body">
            <div class="sec-chart-container" style="height: 220px;">
                <canvas id="timelineChart"></canvas>
            </div>
        </div>
    </div>

    <!-- ==================== ÉVÉNEMENTS RÉCENTS (avec pagination) ==================== -->
    <div class="sec-panel">
        <div class="sec-panel__header">
            <h2 class="sec-panel__title">📋 Événements de sécurité</h2>
            <span class="sec-panel__badge"><?= $totalItems ?> événement(s)</span>
        </div>
        <div class="sec-panel__body sec-panel__body--flush">
            <?php if (empty($recentEvents)): ?>
                <div class="sec-empty">
                    <div class="sec-empty__icon">ℹ️</div>
                    <div class="sec-empty__text">Aucun événement ne correspond aux filtres sélectionnés</div>
                </div>
            <?php else: ?>
                <!-- Tableau scrollable horizontalement -->
                <div style="overflow-x: auto;">
                    <table class="sec-table">
                        <thead>
                            <tr>
                                <th>Date & Heure</th>
                                <th>Type</th>
                                <th>Sévérité</th>
                                <th>IP</th>
                                <th>URI</th>
                                <th>Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentEvents as $event): ?>
                            <tr>
                                <!-- Horodatage formaté -->
                                <td>
                                    <span style="color:var(--text-secondary); font-size:0.8rem;">
                                        <?= e($event['timestamp'] ?? '') ?>
                                    </span>
                                </td>

                                <!-- Type d'événement (code monospace) -->
                                <td>
                                    <span class="cell-code"><?= e($event['event_type'] ?? '') ?></span>
                                </td>

                                <!-- Badge de sévérité coloré -->
                                <td>
                                    <?php
                                    $sev = $event['severity'] ?? 'INFO';
                                    $sevClass = match($sev) {
                                        'CRITICAL' => 'critical',
                                        'WARNING'  => 'warning',
                                        'INFO'     => 'info',
                                        default    => 'info'
                                    };
                                    ?>
                                    <span class="sec-badge sec-badge--<?= $sevClass ?>">
                                        <?= e($sev) ?>
                                    </span>
                                </td>

                                <!-- Adresse IP (cliquable pour filtrer) -->
                                <td>
                                    <?php if (!empty($event['ip'])): ?>
                                        <a href="javascript:void(0)" 
                                           onclick="filterByIP('<?= e($event['ip']) ?>')"
                                           class="cell-code" style="cursor:pointer; text-decoration:none;">
                                            <?= e($event['ip']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span style="color:var(--text-tertiary)">—</span>
                                    <?php endif; ?>
                                </td>

                                <!-- URI ciblée -->
                                <td>
                                    <span class="cell-truncate"><?= e($event['uri'] ?? '') ?></span>
                                </td>

                                <!-- Détails JSON (tronqués) -->
                                <td>
                                    <span class="cell-truncate">
                                        <?php
                                        $data = $event['data'] ?? [];
                                        if (is_array($data)) {
                                            echo e(json_encode($data, JSON_UNESCAPED_UNICODE));
                                        } else {
                                            echo e((string)$data);
                                        }
                                        ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="sec-pagination">
                    <span class="sec-pagination__info">
                        Page <?= $currentPage ?> sur <?= $totalPages ?>
                        (<?= $totalItems ?> événements)
                    </span>
                    <div class="sec-pagination__controls">
                        <?php
                        // Construire l'URL de base avec les filtres actuels
                        $baseUrl = '/admin/security?' . http_build_query(array_filter($filters));
                        ?>

                        <!-- Bouton précédent -->
                        <a href="<?= $baseUrl ?>&page=<?= max(1, $currentPage - 1) ?>"
                           class="sec-pagination__btn <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                            ‹
                        </a>

                        <!-- Numéros de pages (max 7 visibles) -->
                        <?php
                        $startPage = max(1, $currentPage - 3);
                        $endPage   = min($totalPages, $currentPage + 3);
                        for ($p = $startPage; $p <= $endPage; $p++):
                        ?>
                            <a href="<?= $baseUrl ?>&page=<?= $p ?>"
                               class="sec-pagination__btn <?= $p === $currentPage ? 'sec-pagination__btn--active' : '' ?>">
                                <?= $p ?>
                            </a>
                        <?php endfor; ?>

                        <!-- Bouton suivant -->
                        <a href="<?= $baseUrl ?>&page=<?= min($totalPages, $currentPage + 1) ?>"
                           class="sec-pagination__btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                            ›
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

</div><!-- Fin .security-dashboard -->

<!-- ==================== TOAST CONTAINER ==================== -->
<div class="sec-toast-container" id="toast-container"></div>

<!-- ==================== MODAL BLOCAGE IP ==================== -->
<div class="sec-modal-overlay" id="blockModal">
    <div class="sec-modal">
        <h3 class="sec-modal__title">🚫 Bloquer une adresse IP</h3>
        <p class="sec-modal__desc">
            L'IP <code id="modalIPDisplay"></code> sera bloquée immédiatement.
            Toutes les requêtes futures de cette IP seront rejetées.
        </p>
        <label class="sec-modal__label" for="blockReason">Raison du blocage (optionnel)</label>
        <textarea class="sec-modal__textarea" id="blockReason" 
                  placeholder="Ex : Tentatives de brute force sur /admin/login"></textarea>
        <div class="sec-modal__actions">
            <button class="sec-btn sec-btn--outline" onclick="closeBlockModal()">Annuler</button>
            <button class="sec-btn sec-btn--danger" onclick="confirmBlockIP()">🚫 Bloquer</button>
        </div>
    </div>
</div>

<!-- ==================== CONFIGURATION JAVASCRIPT ==================== -->
<script>
/**
 * Configuration globale unifiée pour le JavaScript du dashboard
 * Contient le token CSRF, les endpoints API, et les données des graphiques
 */
window.MARKETFLOW_CONFIG = {
    // Token CSRF pour sécuriser toutes les requêtes AJAX POST
    csrfToken: <?= json_encode($csrfToken, JSON_HEX_TAG | JSON_HEX_APOS) ?>,

    // Endpoints API utilisés par le JS
    apiEndpoints: {
        stats:         '/admin/security/api/stats',
        events:        '/admin/security/api/events',
        suspiciousIPs: '/admin/security/api/suspicious-ips',
        blockIP:       '/admin/security/block-ip',
        unblockIP:     '/admin/security/unblock-ip',
        whitelistIP:   '/admin/security/whitelist-ip'
    },

    // Données pour le graphique donut (répartition par type)
    chartDonut: {
        labels: <?= json_encode($chartLabels) ?>,
        data:   <?= json_encode($chartData) ?>,
        colors: <?= json_encode($chartColors) ?>
    },

    // Données pour le graphique timeline (7 jours)
    chartTimeline: {
        labels:   <?= json_encode($timelineLabels) ?>,
        critical: <?= json_encode($timelineCritical) ?>,
        warning:  <?= json_encode($timelineWarning) ?>,
        info:     <?= json_encode($timelineInfo) ?>
    },

    // Données pour le graphique sévérité
    chartSeverity: {
        labels: ['Critique', 'Warning', 'Info'],
        data:   [<?= $criticalEvents ?>, <?= $warningEvents ?>, <?= $infoEvents ?>],
        colors: ['#ef4444', '#f59e0b', '#3b82f6']
    }
};
</script>

<!-- JavaScript externe du dashboard (toute la logique) -->
<script src="<?= JS_URL ?>/security-dashboard.js"></script>

</body>
</html>
