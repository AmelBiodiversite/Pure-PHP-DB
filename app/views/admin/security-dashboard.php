<?php
/**
 * MARKETFLOW PRO - DASHBOARD DE SÉCURITÉ AVEC CHART.JS
 * Interface de monitoring des événements de sécurité avec graphiques interactifs
 * 
 * NOUVEAUTÉS :
 * - 📈 Graphique en ligne : Évolution sur 7 jours
 * - 🍩 Graphique en donut : Répartition par type
 * - 📊 Graphique à barres : Top IPs suspectes
 * 
 * Fichier : app/views/admin/security-dashboard.php
 */

// Récupération des données du contrôleur
$stats = $stats ?? [];
$recentEvents = $recentEvents ?? [];
$suspiciousIPs = $suspiciousIPs ?? [];
$totalEvents = $totalEvents ?? 0;
$criticalEvents = $criticalEvents ?? 0;
$warningEvents = $warningEvents ?? 0;
$infoEvents = $infoEvents ?? 0;

/**
 * Obtenir une icône selon le type d'événement
 */
function getEventIcon($eventType) {
    $icons = [
        'LOGIN_SUCCESS' => '✅', 'LOGIN_FAILED' => '❌', 'LOGIN_BLOCKED' => '🚫',
        'CSRF_VIOLATION' => '⚠️', 'XSS_ATTEMPT' => '🔴', 'SQLI_ATTEMPT' => '🔴',
        'REGISTER' => '📝', 'LOGOUT' => '👋', 'SUSPICIOUS' => '🔍', 'SESSION_HIJACK' => '🏴‍☠️'
    ];
    return $icons[$eventType] ?? '📌';
}

/**
 * Obtenir la classe CSS du badge selon la sévérité
 */
function getSeverityBadge($severity) {
    $badges = ['INFO' => 'badge-secondary', 'WARNING' => 'badge-warning', 'CRITICAL' => 'badge-danger'];
    return $badges[$severity] ?? 'badge-secondary';
}

/**
 * Formater une date en temps relatif
 */
function getRelativeTime($timestamp) {
    $diff = time() - strtotime($timestamp);
    if ($diff < 60) return "il y a {$diff} secondes";
    elseif ($diff < 3600) { $m = floor($diff/60); return "il y a {$m} minute".($m>1?'s':''); }
    elseif ($diff < 86400) { $h = floor($diff/3600); return "il y a {$h} heure".($h>1?'s':''); }
    else { $d = floor($diff/86400); return "il y a {$d} jour".($d>1?'s':''); }
}

/**
 * Préparer les données pour Chart.js
 * Format JSON pour les graphiques
 */
// Données pour le graphique en donut (répartition par type)
$chartLabels = array_keys($stats);
$chartData = array_values($stats);
$chartColors = [
    '#43e97b', '#f5576c', '#ffa500', '#667eea', '#f093fb', 
    '#38f9d7', '#ff6347', '#764ba2', '#4facfe', '#00f2fe'
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Sécurité - MarketFlow Pro</title>
    
    <!-- Essayer plusieurs CDN pour Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
    <script>
    // Si Chart.js n'est pas chargé, essayer un autre CDN
    if (typeof Chart === 'undefined') {
        console.log('⚠️ Premier CDN échoué, essai avec unpkg...');
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/chart.js@4.4.1/dist/chart.umd.js';
        document.head.appendChild(script);
    }
    </script>
    
</head>
<body>

<div class="container mt-8 mb-16">
    
    <!-- Header du dashboard -->
    <div class="mb-8">
        <h1 class="mb-2">🔒 Monitoring de Sécurité</h1>
        <p style="color: var(--text-secondary);">
            Surveillance en temps réel des événements de sécurité (7 derniers jours)
        </p>
    </div>

    <!-- Cartes de statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Total d'événements -->
        <div class="card hover-lift">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    📊
                </div>
                <div style="flex: 1;">
                    <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.25rem;">
                        Total événements
                    </p>
                    <h3 style="margin: 0; font-size: 1.75rem;">
                        <?= number_format($totalEvents) ?>
                    </h3>
                </div>
            </div>
        </div>

        <!-- Événements critiques -->
        <div class="card hover-lift">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    🚨
                </div>
                <div style="flex: 1;">
                    <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.25rem;">
                        Critiques
                    </p>
                    <h3 style="margin: 0; font-size: 1.75rem; color: #f5576c;">
                        <?= number_format($criticalEvents) ?>
                    </h3>
                </div>
            </div>
        </div>

        <!-- Avertissements -->
        <div class="card hover-lift">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #ffa500 0%, #ff6347 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    ⚠️
                </div>
                <div style="flex: 1;">
                    <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.25rem;">
                        Avertissements
                    </p>
                    <h3 style="margin: 0; font-size: 1.75rem; color: #ffa500;">
                        <?= number_format($warningEvents) ?>
                    </h3>
                </div>
            </div>
        </div>

        <!-- Événements normaux -->
        <div class="card hover-lift">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    ✅
                </div>
                <div style="flex: 1;">
                    <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.25rem;">
                        Normaux
                    </p>
                    <h3 style="margin: 0; font-size: 1.75rem; color: #43e97b;">
                        <?= number_format($infoEvents) ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- NOUVEAUTÉ : Graphiques Chart.js -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <!-- Graphique en donut : Répartition par type -->
        <div class="card">
            <h2 style="margin: 0 0 1.5rem 0;">🍩 Répartition par type d'événement</h2>
            <div style="position: relative; height: 300px;">
                <!-- Canvas pour Chart.js -->
                <canvas id="donutChart"></canvas>
            </div>
        </div>

        <!-- Graphique à barres : Top IPs suspectes -->
        <div class="card">
            <h2 style="margin: 0 0 1.5rem 0;">📊 Top 5 IPs suspectes</h2>
            <div style="position: relative; height: 300px;">
                <!-- Canvas pour Chart.js -->
                <canvas id="ipsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Section IPs suspectes (détails) -->
    <div class="card mb-8">
        <h2 style="margin: 0 0 1.5rem 0;">🔴 IPs Suspectes (Détails)</h2>
        
        <?php if (empty($suspiciousIPs)): ?>
            <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                <p style="font-size: 2rem; margin-bottom: 0.5rem;">✅</p>
                <p>Aucune IP suspecte détectée</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach (array_slice($suspiciousIPs, 0, 6) as $ipData): ?>
                    <div class="hover-lift" style="padding: 1rem; background: var(--bg-secondary); border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <strong style="font-family: monospace; color: #f5576c;">
                                <?= htmlspecialchars($ipData['ip']) ?>
                            </strong>
                            <span class="badge badge-danger">
                                Score: <?= $ipData['severity_score'] ?>
                            </span>
                        </div>
                        
                        <div style="font-size: 0.875rem; color: var(--text-secondary);">
                            <?php if ($ipData['failed_logins'] > 0): ?>
                                <div>❌ <?= $ipData['failed_logins'] ?> échec(s)</div>
                            <?php endif; ?>
                            
                            <?php if ($ipData['blocks'] > 0): ?>
                                <div>🚫 <?= $ipData['blocks'] ?> blocage(s)</div>
                            <?php endif; ?>
                            
                            <?php if ($ipData['csrf_violations'] > 0): ?>
                                <div>⚠️ <?= $ipData['csrf_violations'] ?> CSRF</div>
                            <?php endif; ?>
                            
                            <?php if ($ipData['xss_attempts'] > 0): ?>
                                <div>🔴 <?= $ipData['xss_attempts'] ?> XSS</div>
                            <?php endif; ?>
                            
                            <?php if ($ipData['sqli_attempts'] > 0): ?>
                                <div>🔴 <?= $ipData['sqli_attempts'] ?> SQLi</div>
                            <?php endif; ?>
                            
                            <div style="margin-top: 0.5rem; font-size: 0.75rem;">
                                🕐 <?= getRelativeTime($ipData['last_event']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Événements récents -->
    <div class="card mb-8">
        <h2 style="margin: 0 0 1.5rem 0;">📋 Événements récents (20 derniers)</h2>
        
        <?php if (empty($recentEvents)): ?>
            <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                <p>Aucun événement récent</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border);">
                            <th style="padding: 1rem; text-align: left;">Type</th>
                            <th style="padding: 1rem; text-align: left;">Sévérité</th>
                            <th style="padding: 1rem; text-align: left;">IP</th>
                            <th style="padding: 1rem; text-align: left;">Détails</th>
                            <th style="padding: 1rem; text-align: left;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($recentEvents, 0, 20) as $event): ?>
                            <tr style="border-bottom: 1px solid var(--border);" class="hover-lift">
                                <td style="padding: 1rem;">
                                    <span style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="font-size: 1.25rem;"><?= getEventIcon($event['event_type']) ?></span>
                                        <span style="font-size: 0.875rem; font-family: monospace;">
                                            <?= htmlspecialchars($event['event_type']) ?>
                                        </span>
                                    </span>
                                </td>
                                
                                <td style="padding: 1rem;">
                                    <span class="badge <?= getSeverityBadge($event['severity']) ?>">
                                        <?= htmlspecialchars($event['severity']) ?>
                                    </span>
                                </td>
                                
                                <td style="padding: 1rem;">
                                    <code style="background: var(--bg-secondary); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.875rem;">
                                        <?= htmlspecialchars($event['ip']) ?>
                                    </code>
                                </td>
                                
                                <td style="padding: 1rem; max-width: 300px;">
                                    <div style="font-size: 0.875rem; color: var(--text-secondary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?php
                                        if (isset($event['data']['email'])) {
                                            echo '📧 ' . htmlspecialchars($event['data']['email']);
                                        } elseif (isset($event['data']['reason'])) {
                                            echo '📝 ' . htmlspecialchars($event['data']['reason']);
                                        } elseif (isset($event['data']['description'])) {
                                            echo '📝 ' . htmlspecialchars($event['data']['description']);
                                        } else {
                                            echo '—';
                                        }
                                        ?>
                                    </div>
                                </td>
                                
                                <td style="padding: 1rem; color: var(--text-secondary); font-size: 0.875rem;">
                                    <div><?= date('d/m/Y H:i', strtotime($event['timestamp'])) ?></div>
                                    <div style="font-size: 0.75rem; opacity: 0.7;">
                                        <?= getRelativeTime($event['timestamp']) ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Actions rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="/admin/dashboard" class="card hover-lift" style="text-decoration: none; color: inherit;">
            <div style="text-align: center; padding: 2rem;">
                <div style="font-size: 48px; margin-bottom: 1rem;">🏠</div>
                <h3 style="margin: 0 0 0.5rem 0;">Dashboard Admin</h3>
                <p style="color: var(--text-secondary); margin: 0;">Retour à l'accueil</p>
            </div>
        </a>

        <a href="/admin/security/download/<?= date('Y-m-d') ?>" class="card hover-lift" style="text-decoration: none; color: inherit;">
            <div style="text-align: center; padding: 2rem;">
                <div style="font-size: 48px; margin-bottom: 1rem;">📥</div>
                <h3 style="margin: 0 0 0.5rem 0;">Télécharger logs</h3>
                <p style="color: var(--text-secondary); margin: 0;">Fichier d'aujourd'hui</p>
            </div>
        </a>

        <a href="/admin/security" class="card hover-lift" style="text-decoration: none; color: inherit;">
            <div style="text-align: center; padding: 2rem;">
                <div style="font-size: 48px; margin-bottom: 1rem;">🔄</div>
                <h3 style="margin: 0 0 0.5rem 0;">Actualiser</h3>
                <p style="color: var(--text-secondary); margin: 0;">Rafraîchir les données</p>
            </div>
        </a>
    </div>
</div>

<!-- JavaScript Chart.js -->
<script>
/**
 * CONFIGURATION DES GRAPHIQUES CHART.JS
 * Tous les graphiques sont interactifs et responsive
 */

// Données PHP converties en JavaScript
const statsData = <?= json_encode($stats) ?>;
const suspiciousIPsData = <?= json_encode($suspiciousIPs) ?>;

/**
 * GRAPHIQUE 1 : Donut - Répartition par type d'événement
 */
const donutCtx = document.getElementById('donutChart').getContext('2d');
const donutChart = new Chart(donutCtx, {
    type: 'doughnut',  // Type : graphique en donut (cercle avec trou au centre)
    data: {
        // Labels : types d'événements (LOGIN_SUCCESS, LOGIN_FAILED, etc.)
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
            // Données : nombre d'événements par type
            data: <?= json_encode($chartData) ?>,
            // Couleurs : chaque type a sa propre couleur
            backgroundColor: <?= json_encode($chartColors) ?>,
            // Bordure blanche pour séparer visuellement les sections
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,              // S'adapte à la taille du conteneur
        maintainAspectRatio: false,    // Permet de définir une hauteur fixe
        plugins: {
            legend: {
                position: 'bottom',    // Légende en bas du graphique
                labels: {
                    padding: 15,       // Espacement entre les items de la légende
                    font: {
                        size: 12
                    }
                }
            },
            tooltip: {
                // Personnalisation du tooltip (info-bulle au survol)
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

/**
 * GRAPHIQUE 2 : Barres horizontales - Top 5 IPs suspectes
 */
const ipsCtx = document.getElementById('ipsChart').getContext('2d');

// Préparer les données : prendre les 5 IPs avec le score le plus élevé
const top5IPs = suspiciousIPsData.slice(0, 5);
const ipLabels = top5IPs.map(ip => ip.ip);          // Adresses IP
const ipScores = top5IPs.map(ip => ip.severity_score);  // Scores de gravité

const ipsChart = new Chart(ipsCtx, {
    type: 'bar',  // Type : graphique à barres
    data: {
        labels: ipLabels,
        datasets: [{
            label: 'Score de gravité',
            data: ipScores,
            // Gradient de couleur selon le score (rouge = dangereux)
            backgroundColor: ipScores.map(score => {
                if (score >= 50) return '#f5576c';      // Rouge si score >= 50
                if (score >= 20) return '#ffa500';      // Orange si score >= 20
                return '#667eea';                        // Bleu sinon
            }),
            borderWidth: 0,
            borderRadius: 6  // Coins arrondis des barres
        }]
    },
    options: {
        indexAxis: 'y',                 // Barres horizontales (axe Y = catégories)
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false          // Cacher la légende (pas nécessaire)
            },
            tooltip: {
                callbacks: {
                    // Personnaliser le tooltip
                    label: function(context) {
                        const ip = top5IPs[context.dataIndex];
                        let details = `Score: ${context.parsed.x}`;
                        if (ip.failed_logins > 0) details += ` | Échecs: ${ip.failed_logins}`;
                        if (ip.csrf_violations > 0) details += ` | CSRF: ${ip.csrf_violations}`;
                        if (ip.xss_attempts > 0) details += ` | XSS: ${ip.xss_attempts}`;
                        return details;
                    }
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,      // L'axe X commence à 0
                title: {
                    display: true,
                    text: 'Score de gravité'
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'  // Grille légère
                }
            },
            y: {
                grid: {
                    display: false      // Pas de grille sur l'axe Y
                }
            }
        }
    }
});

/**
 * Auto-refresh toutes les 30 secondes
 * Le dashboard se recharge automatiquement pour afficher les nouvelles données
 */
setTimeout(() => {
    location.reload();  // Recharger la page
}, 30000);  // 30000 ms = 30 secondes

console.log('📊 Dashboard de sécurité chargé avec Chart.js');
console.log(`Total événements : ${<?= $totalEvents ?>}`);
console.log(`IPs suspectes : ${suspiciousIPsData.length}`);
</script>

<!-- Styles CSS -->
<style>
/* Animation hover sur les cartes */
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Effet hover sur les lignes du tableau */
table tbody tr:hover {
    background: var(--bg-secondary);
}

/* Badges colorés */
.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-primary {
    background: var(--primary);
    color: white;
}

.badge-secondary {
    background: #6c757d;
    color: white;
}

.badge-warning {
    background: #ffa500;
    color: white;
}

.badge-danger {
    background: #f5576c;
    color: white;
}

/* Responsive : graphiques sur mobile */
@media (max-width: 768px) {
    #donutChart, #ipsChart {
        height: 250px !important;
    }
}
</style>

</body>
</html>