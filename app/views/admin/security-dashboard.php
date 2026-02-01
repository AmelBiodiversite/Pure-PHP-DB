<?php
/**
 * MARKETFLOW PRO - SECURITY DASHBOARD VIEW
 * Vue du monitoring de sécurité pour les administrateurs
 * Fichier : app/views/admin/security-dashboard.php
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Monitoring Sécurité') ?> - MarketFlow Admin</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .security-dashboard {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #3498db;
        }

        .stat-card.critical {
            border-left-color: #e74c3c;
        }

        .stat-card.warning {
            border-left-color: #f39c12;
        }

        .stat-card.info {
            border-left-color: #2ecc71;
        }

        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #7f8c8d;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
        }

        .section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2c3e50;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }

        .events-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .events-table th {
            background: #ecf0f1;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #bdc3c7;
        }

        .events-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #ecf0f1;
        }

        .events-table tr:hover {
            background: #f8f9fa;
        }

        .severity-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .severity-badge.critical {
            background: #e74c3c;
            color: white;
        }

        .severity-badge.warning {
            background: #f39c12;
            color: white;
        }

        .severity-badge.info {
            background: #2ecc71;
            color: white;
        }

        .event-type {
            font-family: monospace;
            font-size: 12px;
            background: #ecf0f1;
            padding: 2px 6px;
            border-radius: 3px;
        }

        .ip-address {
            font-family: monospace;
            color: #7f8c8d;
        }

        .suspicious-ip-card {
            background: #fff5f5;
            border: 1px solid #e74c3c;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .suspicious-ip-card .ip {
            font-size: 18px;
            font-weight: bold;
            color: #e74c3c;
            font-family: monospace;
            margin-bottom: 10px;
        }

        .suspicious-ip-card .details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            font-size: 13px;
        }

        .suspicious-ip-card .detail-item {
            display: flex;
            flex-direction: column;
        }

        .suspicious-ip-card .detail-label {
            color: #7f8c8d;
            font-size: 11px;
            text-transform: uppercase;
        }

        .suspicious-ip-card .detail-value {
            color: #2c3e50;
            font-weight: 600;
            margin-top: 2px;
        }

        .score-badge {
            background: #e74c3c;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            float: right;
        }

        .filters {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 1px solid #bdc3c7;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-btn:hover {
            background: #ecf0f1;
        }

        .filter-btn.active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }

        .timestamp {
            color: #7f8c8d;
            font-size: 12px;
        }

        .data-preview {
            font-size: 12px;
            color: #95a5a6;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .download-btn {
            background: #3498db;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            font-size: 14px;
        }

        .download-btn:hover {
            background: #2980b9;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .events-table {
                font-size: 12px;
            }

            .events-table th,
            .events-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="security-dashboard">
        <h1 style="margin-bottom: 30px; color: #2c3e50;">
            🔒 Monitoring de Sécurité
        </h1>

        <!-- Statistiques globales -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total des événements</h3>
                <div class="value"><?= number_format($totalEvents ?? 0) ?></div>
                <div style="margin-top: 10px; color: #7f8c8d; font-size: 13px;">
                    Sur les 7 derniers jours
                </div>
            </div>

            <div class="stat-card critical">
                <h3>Événements critiques</h3>
                <div class="value"><?= number_format($criticalEvents ?? 0) ?></div>
                <div style="margin-top: 10px; color: #e74c3c; font-size: 13px;">
                    Blocages, CSRF, XSS, SQLi
                </div>
            </div>

            <div class="stat-card warning">
                <h3>Avertissements</h3>
                <div class="value"><?= number_format($warningEvents ?? 0) ?></div>
                <div style="margin-top: 10px; color: #f39c12; font-size: 13px;">
                    Tentatives échouées
                </div>
            </div>

            <div class="stat-card info">
                <h3>Événements normaux</h3>
                <div class="value"><?= number_format($infoEvents ?? 0) ?></div>
                <div style="margin-top: 10px; color: #2ecc71; font-size: 13px;">
                    Connexions réussies
                </div>
            </div>
        </div>

        <!-- IPs suspectes -->
        <?php if (!empty($suspiciousIPs)): ?>
        <div class="section">
            <div class="section-title">
                ⚠️ IPs Suspectes (Top 10)
            </div>

            <?php foreach ($suspiciousIPs as $ipData): ?>
            <div class="suspicious-ip-card">
                <div class="ip">
                    <?= htmlspecialchars($ipData['ip']) ?>
                    <span class="score-badge">Score: <?= $ipData['severity_score'] ?></span>
                </div>
                
                <div class="details">
                    <div class="detail-item">
                        <span class="detail-label">Total événements</span>
                        <span class="detail-value"><?= $ipData['total'] ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Connexions échouées</span>
                        <span class="detail-value"><?= $ipData['failed_logins'] ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Blocages</span>
                        <span class="detail-value"><?= $ipData['blocks'] ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Violations CSRF</span>
                        <span class="detail-value"><?= $ipData['csrf_violations'] ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Tentatives XSS</span>
                        <span class="detail-value"><?= $ipData['xss_attempts'] ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Tentatives SQLi</span>
                        <span class="detail-value"><?= $ipData['sqli_attempts'] ?></span>
                    </div>
                </div>
                
                <div style="margin-top: 10px; font-size: 12px; color: #7f8c8d;">
                    Dernier événement : <?= htmlspecialchars($ipData['last_event']) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Statistiques par type d'événement -->
        <div class="section">
            <div class="section-title">
                📊 Statistiques par type d'événement (7 derniers jours)
            </div>

        <!-- Section Graphiques -->
        <div class="section">
            <div class="section-title">
                📊 Graphiques de visualisation
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <!-- Graphique Donut -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h3 style="margin: 0 0 15px 0; color: #2c3e50; font-size: 16px;">
                        🍩 Répartition par type d'événement
                    </h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="donutChart"></canvas>
                    </div>
                </div>

                <!-- Graphique IPs -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h3 style="margin: 0 0 15px 0; color: #2c3e50; font-size: 16px;">
                        📊 Top 5 IPs suspectes
                    </h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="ipsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

            <table class="events-table">
                <thead>
                    <tr>
                        <th>Type d'événement</th>
                        <th style="text-align: right;">Nombre</th>
                        <th style="text-align: right;">Pourcentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($stats)): ?>
                        <?php 
                        // Trier par nombre décroissant
                        arsort($stats);
                        foreach ($stats as $eventType => $count): 
                            $percentage = $totalEvents > 0 ? ($count / $totalEvents) * 100 : 0;
                        ?>
                        <tr>
                            <td>
                                <span class="event-type"><?= htmlspecialchars($eventType) ?></span>
                            </td>
                            <td style="text-align: right; font-weight: 600;">
                                <?= number_format($count) ?>
                            </td>
                            <td style="text-align: right; color: #7f8c8d;">
                                <?= number_format($percentage, 1) ?>%
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="no-data">
                                Aucune statistique disponible
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Événements récents -->
        <div class="section">
            <div class="section-title">
                📝 Événements récents (50 derniers)
            </div>

            <div style="overflow-x: auto;">
                <table class="events-table">
                    <thead>
                        <tr>
                            <th>Date/Heure</th>
                            <th>Sévérité</th>
                            <th>Type</th>
                            <th>IP</th>
                            <th>URI</th>
                            <th>Données</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentEvents)): ?>
                            <?php foreach ($recentEvents as $event): ?>
                            <tr>
                                <td class="timestamp">
                                    <?= htmlspecialchars($event['timestamp']) ?>
                                </td>
                                <td>
                                    <span class="severity-badge <?= strtolower($event['severity']) ?>">
                                        <?= htmlspecialchars($event['severity']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="event-type"><?= htmlspecialchars($event['event_type']) ?></span>
                                </td>
                                <td>
                                    <span class="ip-address"><?= htmlspecialchars($event['ip']) ?></span>
                                </td>
                                <td style="font-size: 12px; max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                    <?= htmlspecialchars($event['uri']) ?>
                                </td>
                                <td>
                                    <div class="data-preview" title="<?= htmlspecialchars(json_encode($event['data'])) ?>">
                                        <?php 
                                        if (!empty($event['data'])) {
                                            $dataStr = json_encode($event['data']);
                                            echo htmlspecialchars(strlen($dataStr) > 50 ? substr($dataStr, 0, 50) . '...' : $dataStr);
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="no-data">
                                    Aucun événement récent
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Téléchargement des logs -->
        <div class="section">
            <div class="section-title">
                💾 Télécharger les logs
            </div>
            
            <p style="color: #7f8c8d; margin-bottom: 15px;">
                Téléchargez les fichiers de logs bruts pour une analyse externe ou une conservation.
            </p>

            <?php
            // Générer les liens pour les 7 derniers jours
            for ($i = 0; $i < 7; $i++):
                $date = date('Y-m-d', strtotime("-{$i} days"));
                $logFile = __DIR__ . '/../../../logs/security-' . $date . '.log';
                
                if (file_exists($logFile)):
                    $fileSize = filesize($logFile);
                    $fileSizeKB = round($fileSize / 1024, 2);
            ?>
                <a href="/admin/security/download/<?= $date ?>" class="download-btn">
                    📄 security-<?= $date ?>.log (<?= $fileSizeKB ?> KB)
                </a>
            <?php 
                endif;
            endfor; 
            ?>
        </div>

        <!-- Retour au dashboard -->
        <div style="margin-top: 30px; text-align: center;">
            <a href="/admin/dashboard" style="color: #3498db; text-decoration: none; font-weight: 600;">
                ← Retour au dashboard admin
            </a>
        </div>
    </div>

    <script>
        // Auto-refresh toutes les 30 secondes
        setTimeout(function() {
            location.reload();
        }, 30000);

        // Confirmation avant de quitter si des événements critiques
        <?php if ($criticalEvents > 0): ?>
        console.warn('⚠️ <?= $criticalEvents ?> événement(s) critique(s) détecté(s) !');
        <?php endif; ?>
    </script>
</body>

    <!-- Chart.js Library -->
    <script src="/js/libs/chart.min.js"></script>

    <!-- Initialisation des graphiques -->
    <script>
        // Attendre que Chart.js soit chargé
        if (typeof Chart !== 'undefined') {
            console.log('✅ Chart.js chargé !');
            
            try {
                // ============================================================
                // GRAPHIQUE 1 : Donut - Répartition par type d'événement
                // ============================================================
                const statsLabels = <?= json_encode(array_keys($stats ?? [])) ?>;
                const statsData = <?= json_encode(array_values($stats ?? [])) ?>;
                
                if (statsLabels.length > 0) {
                    const donutCtx = document.getElementById('donutChart').getContext('2d');
                    new Chart(donutCtx, {
                        type: 'doughnut',
                        data: {
                            labels: statsLabels,
                            datasets: [{
                                data: statsData,
                                backgroundColor: [
                                    '#3498db', '#e74c3c', '#f39c12', '#2ecc71', 
                                    '#9b59b6', '#1abc9c', '#34495e', '#e67e22'
                                ],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 10,
                                        font: { size: 11 }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                            return label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                    console.log('✅ Graphique Donut créé');
                }

                // ============================================================
                // GRAPHIQUE 2 : Barres - Top 5 IPs suspectes
                // ============================================================
                const suspiciousIPs = <?= json_encode($suspiciousIPs ?? []) ?>;
                const top5IPs = suspiciousIPs.slice(0, 5);
                const ipLabels = top5IPs.map(ip => ip.ip);
                const ipScores = top5IPs.map(ip => ip.severity_score);

                if (ipLabels.length > 0) {
                    const ipsCtx = document.getElementById('ipsChart').getContext('2d');
                    new Chart(ipsCtx, {
                        type: 'bar',
                        data: {
                            labels: ipLabels,
                            datasets: [{
                                label: 'Score de gravité',
                                data: ipScores,
                                backgroundColor: ipScores.map(score => {
                                    if (score >= 50) return '#e74c3c';  // Rouge
                                    if (score >= 20) return '#f39c12';  // Orange
                                    return '#3498db';                    // Bleu
                                }),
                                borderWidth: 0,
                                borderRadius: 4
                            }]
                        },
                        options: {
                            indexAxis: 'y',  // Barres horizontales
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const ip = top5IPs[context.dataIndex];
                                            let details = 'Score: ' + context.parsed.x;
                                            if (ip.failed_logins > 0) details += ' | Échecs: ' + ip.failed_logins;
                                            if (ip.csrf_violations > 0) details += ' | CSRF: ' + ip.csrf_violations;
                                            if (ip.xss_attempts > 0) details += ' | XSS: ' + ip.xss_attempts;
                                            return details;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    title: { display: true, text: 'Score de gravité' }
                                }
                            }
                        }
                    });
                    console.log('✅ Graphique IPs créé');
                } else {
                    document.getElementById('ipsChart').parentElement.innerHTML = 
                        '<p style="text-align: center; color: #7f8c8d; padding: 40px;">Aucune IP suspecte détectée</p>';
                }

                console.log('📊 Dashboard de sécurité avec graphiques chargé !');
                
            } catch (error) {
                console.error('❌ Erreur lors de la création des graphiques:', error);
            }
        } else {
            console.error('❌ Chart.js n\'est pas chargé');
        }
    </script>
</html>