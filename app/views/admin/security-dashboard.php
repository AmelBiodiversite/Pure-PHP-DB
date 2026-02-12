<?php
/**
 * MARKETFLOW PRO - SECURITY DASHBOARD VIEW v2.0
 *
 * Vue principale du monitoring de sécurité admin
 * Thème : sombre industriel - adapté à un contexte de surveillance
 *
 * Données attendues depuis SecurityController::index() :
 *   $title, $stats, $totalEvents, $criticalEvents, $warningEvents, $infoEvents,
 *   $suspiciousIPs, $recentEvents, $pagination, $filters, $eventTypes,
 *   $chartLabels, $chartData, $chartColors,
 *   $timelineLabels, $timelineCritical, $timelineWarning, $timelineInfo
 *
 * @file app/views/admin/security-dashboard.php
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Monitoring Sécurité') ?> — MarketFlow Admin</title>

    <!-- Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- Police Geist Mono + JetBrains Mono pour les données techniques -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ====================================================================
           VARIABLES CSS & RESET
           ==================================================================== */
        :root {
            --bg-base:        #0a0c0f;
            --bg-panel:       #0f1318;
            --bg-card:        #141920;
            --bg-hover:       #1c2330;
            --border:         #1e2840;
            --border-light:   #252f42;

            --text-primary:   #e8edf5;
            --text-secondary: #8494b0;
            --text-muted:     #4a5568;

            --red:            #ff3b3b;
            --red-dim:        rgba(255,59,59,.12);
            --orange:         #ff8c42;
            --orange-dim:     rgba(255,140,66,.12);
            --green:          #2ecc71;
            --green-dim:      rgba(46,204,113,.12);
            --blue:           #4a9eff;
            --blue-dim:       rgba(74,158,255,.12);
            --purple:         #a78bfa;

            --font-ui:        'Syne', sans-serif;
            --font-mono:      'JetBrains Mono', monospace;

            --radius:         8px;
            --radius-sm:      4px;
            --transition:     0.2s ease;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--bg-base);
            color: var(--text-primary);
            font-family: var(--font-ui);
            font-size: 14px;
            line-height: 1.6;
            /* Texture subtile pour l'ambiance industrielle */
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0h60v60H0z' fill='none'/%3E%3Cpath d='M0 30h60M30 0v60' stroke='%23ffffff04' stroke-width='1'/%3E%3C/svg%3E");
        }

        /* ====================================================================
           LAYOUT PRINCIPAL
           ==================================================================== */
        .sec-wrap {
            max-width: 1440px;
            margin: 0 auto;
            padding: 32px 24px 80px;
        }

        /* ====================================================================
           HEADER
           ==================================================================== */
        .sec-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 36px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .sec-header-left h1 {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Point clignotant "live" */
        .live-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--green);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(46,204,113,.4); }
            50%       { opacity: .7; box-shadow: 0 0 0 6px rgba(46,204,113,0); }
        }

        .sec-header-left p {
            color: var(--text-secondary);
            font-size: 13px;
            margin-top: 4px;
            font-family: var(--font-mono);
        }

        /* Bouton retour admin */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13px;
            padding: 8px 14px;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-sm);
            transition: all var(--transition);
        }

        .btn-back:hover {
            color: var(--text-primary);
            background: var(--bg-hover);
            border-color: var(--blue);
        }

        /* ====================================================================
           ALERTES CRITIQUES (si events critiques détectés)
           ==================================================================== */
        .alert-critical {
            background: var(--red-dim);
            border: 1px solid rgba(255,59,59,.3);
            border-left: 3px solid var(--red);
            border-radius: var(--radius);
            padding: 14px 18px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: #ffaaaa;
            animation: fadeIn .4s ease;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: none; } }

        /* ====================================================================
           GRILLE STATS (4 cartes)
           ==================================================================== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: border-color var(--transition);
        }

        /* Ligne colorée en haut */
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
        }

        .stat-card.total::before   { background: var(--blue); }
        .stat-card.critical::before { background: var(--red); }
        .stat-card.warning::before  { background: var(--orange); }
        .stat-card.info::before     { background: var(--green); }

        .stat-card:hover { border-color: var(--border-light); }

        .stat-card .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-family: var(--font-mono);
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 36px;
            font-weight: 800;
            line-height: 1;
            font-family: var(--font-mono);
        }

        .stat-card.total .value   { color: var(--blue); }
        .stat-card.critical .value { color: var(--red); }
        .stat-card.warning .value  { color: var(--orange); }
        .stat-card.info .value     { color: var(--green); }

        .stat-card .sub {
            margin-top: 8px;
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ====================================================================
           SECTION GÉNÉRIQUE
           ==================================================================== */
        .sec-panel {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }

        .panel-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .panel-body { padding: 20px; }

        /* ====================================================================
           FORMULAIRE DE FILTRES
           ==================================================================== */
        .filters-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 2fr auto;
            gap: 10px;
            align-items: end;
        }

        .filter-group { display: flex; flex-direction: column; gap: 5px; }

        .filter-group label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--text-muted);
            font-family: var(--font-mono);
        }

        /* Inputs et selects */
        .filter-group input,
        .filter-group select {
            background: var(--bg-base);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            padding: 8px 12px;
            font-family: var(--font-mono);
            font-size: 12px;
            outline: none;
            transition: border-color var(--transition);
        }

        .filter-group input:focus,
        .filter-group select:focus { border-color: var(--blue); }

        .filter-group select option { background: var(--bg-card); }

        /* Boutons filtres */
        .btn-filter {
            padding: 8px 18px;
            border-radius: var(--radius-sm);
            font-family: var(--font-ui);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition);
            border: none;
            white-space: nowrap;
        }

        .btn-apply {
            background: var(--blue);
            color: #fff;
        }

        .btn-apply:hover { background: #3a8ef0; }

        .btn-reset {
            background: transparent;
            border: 1px solid var(--border-light) !important;
            color: var(--text-secondary);
            border: none;
            margin-left: 4px;
        }

        .btn-reset:hover { background: var(--bg-hover); color: var(--text-primary); }

        /* Boutons export */
        .export-btns {
            display: flex;
            gap: 8px;
        }

        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid var(--border-light);
            background: transparent;
            color: var(--text-secondary);
            transition: all var(--transition);
            font-family: var(--font-ui);
        }

        .btn-export:hover { background: var(--bg-hover); color: var(--text-primary); }
        .btn-export.csv:hover { border-color: var(--green); color: var(--green); }
        .btn-export.json:hover { border-color: var(--orange); color: var(--orange); }

        /* ====================================================================
           GRILLE GRAPHIQUES
           ==================================================================== */
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        .chart-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
        }

        .chart-box.wide { grid-column: span 2; }

        .chart-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-family: var(--font-mono);
            margin-bottom: 14px;
        }

        .chart-wrap {
            position: relative;
            height: 220px;
        }

        /* ====================================================================
           CARTES IPs SUSPECTES
           ==================================================================== */
        .ips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 12px;
        }

        .ip-card {
            background: var(--bg-panel);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px;
            transition: border-color var(--transition);
        }

        .ip-card:hover { border-color: var(--border-light); }

        .ip-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .ip-addr {
            font-family: var(--font-mono);
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .score-badge {
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .score-badge.high   { background: var(--red-dim);    color: var(--red);    border: 1px solid rgba(255,59,59,.25); }
        .score-badge.medium { background: var(--orange-dim); color: var(--orange); border: 1px solid rgba(255,140,66,.25); }
        .score-badge.low    { background: var(--blue-dim);   color: var(--blue);   border: 1px solid rgba(74,158,255,.25); }

        /* Métriques de l'IP */
        .ip-metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }

        .ip-metric {
            background: var(--bg-base);
            border-radius: var(--radius-sm);
            padding: 8px;
            text-align: center;
        }

        .ip-metric .m-val {
            font-family: var(--font-mono);
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .ip-metric .m-lbl {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-top: 2px;
        }

        .ip-last {
            font-size: 11px;
            color: var(--text-muted);
            font-family: var(--font-mono);
            margin-bottom: 10px;
        }

        /* Boutons actions IP */
        .ip-actions { display: flex; gap: 6px; }

        .btn-ip {
            flex: 1;
            padding: 6px 10px;
            border-radius: var(--radius-sm);
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition);
            border: 1px solid var(--border-light);
            background: transparent;
            color: var(--text-secondary);
            font-family: var(--font-ui);
        }

        .btn-ip:hover            { background: var(--bg-hover); color: var(--text-primary); }
        .btn-ip.block:hover      { border-color: var(--red);    color: var(--red); }
        .btn-ip.whitelist:hover  { border-color: var(--green);  color: var(--green); }
        .btn-ip.filter:hover     { border-color: var(--blue);   color: var(--blue); }

        /* ====================================================================
           TABLEAU DES ÉVÉNEMENTS
           ==================================================================== */
        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead th {
            padding: 10px 14px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-family: var(--font-mono);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
            background: var(--bg-panel);
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background var(--transition);
        }

        tbody tr:hover { background: var(--bg-hover); }

        tbody td {
            padding: 10px 14px;
            color: var(--text-secondary);
            vertical-align: middle;
        }

        /* Cellule timestamp */
        .td-time {
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        /* Badges de sévérité */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
            font-family: var(--font-mono);
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .badge.CRITICAL { background: var(--red-dim);    color: var(--red);    border: 1px solid rgba(255,59,59,.25); }
        .badge.WARNING  { background: var(--orange-dim); color: var(--orange); border: 1px solid rgba(255,140,66,.25); }
        .badge.INFO     { background: var(--green-dim);  color: var(--green);  border: 1px solid rgba(46,204,113,.25); }

        /* Type d'événement */
        .td-type {
            font-family: var(--font-mono);
            font-size: 11px;
            background: var(--bg-panel);
            color: var(--text-primary);
            padding: 3px 8px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-light);
            white-space: nowrap;
        }

        /* IP cliquable */
        .td-ip {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--blue);
            cursor: pointer;
            text-decoration: none;
        }

        .td-ip:hover { text-decoration: underline; }

        /* URI tronquée */
        .td-uri {
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 12px;
            font-family: var(--font-mono);
            color: var(--text-muted);
        }

        /* Data JSON */
        .td-data {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 11px;
            font-family: var(--font-mono);
            color: var(--text-muted);
            cursor: help;
        }

        /* Message vide */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
            font-family: var(--font-mono);
            font-size: 13px;
        }

        .empty-state .icon { font-size: 32px; margin-bottom: 12px; display: block; opacity: .4; }

        /* ====================================================================
           PAGINATION
           ==================================================================== */
        .pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-top: 1px solid var(--border);
        }

        .pagination-info {
            font-size: 12px;
            color: var(--text-muted);
            font-family: var(--font-mono);
        }

        .pagination-btns { display: flex; gap: 4px; }

        .page-btn {
            min-width: 32px;
            height: 32px;
            padding: 0 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            font-size: 13px;
            text-decoration: none;
            color: var(--text-secondary);
            border: 1px solid var(--border-light);
            background: transparent;
            transition: all var(--transition);
            font-family: var(--font-mono);
        }

        .page-btn:hover    { background: var(--bg-hover); color: var(--text-primary); }
        .page-btn.active   { background: var(--blue); color: #fff; border-color: var(--blue); }
        .page-btn.disabled { opacity: .3; pointer-events: none; }

        /* ====================================================================
           TOAST NOTIFICATIONS
           ==================================================================== */
        #toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .toast {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: var(--radius);
            padding: 12px 16px;
            font-size: 13px;
            min-width: 260px;
            animation: slideIn .3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 32px rgba(0,0,0,.5);
        }

        .toast.success { border-left: 3px solid var(--green); }
        .toast.error   { border-left: 3px solid var(--red);   }

        @keyframes slideIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: none; } }

        /* ====================================================================
           MODAL CONFIRMATION BLOCAGE IP
           ==================================================================== */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.open { display: flex; }

        .modal {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: var(--radius);
            padding: 28px;
            width: 400px;
            max-width: 90vw;
            animation: fadeIn .2s ease;
        }

        .modal h3 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .modal p {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 16px;
        }

        .modal-ip-display {
            font-family: var(--font-mono);
            font-size: 16px;
            font-weight: 700;
            color: var(--red);
            background: var(--red-dim);
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            margin-bottom: 16px;
            border: 1px solid rgba(255,59,59,.2);
        }

        .modal input[type="text"] {
            width: 100%;
            background: var(--bg-base);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            padding: 10px 12px;
            font-family: var(--font-mono);
            font-size: 13px;
            outline: none;
            margin-bottom: 16px;
        }

        .modal input:focus { border-color: var(--red); }

        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; }

        .btn-confirm-block {
            padding: 9px 20px;
            background: var(--red);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 700;
            cursor: pointer;
            font-family: var(--font-ui);
            font-size: 13px;
            transition: background var(--transition);
        }

        .btn-confirm-block:hover { background: #e02020; }

        .btn-cancel {
            padding: 9px 20px;
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-sm);
            font-weight: 600;
            cursor: pointer;
            font-family: var(--font-ui);
            font-size: 13px;
            transition: all var(--transition);
        }

        .btn-cancel:hover { background: var(--bg-hover); color: var(--text-primary); }

        /* ====================================================================
           RESPONSIVE
           ==================================================================== */
        @media (max-width: 1100px) {
            .charts-grid { grid-template-columns: 1fr 1fr; }
            .chart-box.wide { grid-column: span 2; }
        }

        @media (max-width: 768px) {
            .stats-grid      { grid-template-columns: 1fr 1fr; }
            .charts-grid     { grid-template-columns: 1fr; }
            .chart-box.wide  { grid-column: span 1; }
            .filters-grid    { grid-template-columns: 1fr 1fr; }
            .sec-header      { flex-direction: column; align-items: flex-start; gap: 12px; }
            .ips-grid        { grid-template-columns: 1fr; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="sec-wrap">

    <!-- ===================================================================
         HEADER
         =================================================================== -->
    <header class="sec-header">
        <div class="sec-header-left">
            <h1>
                <span class="live-dot"></span>
                Security Monitor
            </h1>
            <p>
                <?= date('d/m/Y H:i') ?> &nbsp;·&nbsp;
                Fenêtre active : <?= htmlspecialchars($filters['date_from']) ?> → <?= htmlspecialchars($filters['date_to']) ?>
            </p>
        </div>

        <div style="display:flex;align-items:center;gap:10px;">
            <!-- Boutons export CSV / JSON (filtres actuels) -->
            <div class="export-btns">
                <a href="/admin/security/export/csv?<?= http_build_query($filters) ?>"
                   class="btn-export csv">
                    ↓ CSV
                </a>
                <a href="/admin/security/export/json?<?= http_build_query($filters) ?>"
                   class="btn-export json">
                    ↓ JSON
                </a>
            </div>
            <a href="/admin/dashboard" class="btn-back">← Admin</a>
        </div>
    </header>

    <!-- ===================================================================
         ALERTE CRITIQUE (si événements critiques présents)
         =================================================================== -->
    <?php if (!empty($criticalEvents) && $criticalEvents > 0): ?>
    <div class="alert-critical">
        <span>⚠</span>
        <span>
            <strong><?= number_format($criticalEvents) ?> événement(s) critique(s)</strong>
            détecté(s) sur les 7 derniers jours — vérifiez les IPs suspectes ci-dessous.
        </span>
    </div>
    <?php endif; ?>

    <!-- ===================================================================
         STATISTIQUES GLOBALES (4 cartes)
         =================================================================== -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="label">Total événements</div>
            <div class="value"><?= number_format($totalEvents ?? 0) ?></div>
            <div class="sub">7 derniers jours</div>
        </div>
        <div class="stat-card critical">
            <div class="label">Critiques</div>
            <div class="value"><?= number_format($criticalEvents ?? 0) ?></div>
            <div class="sub">CSRF · XSS · SQLi</div>
        </div>
        <div class="stat-card warning">
            <div class="label">Avertissements</div>
            <div class="value"><?= number_format($warningEvents ?? 0) ?></div>
            <div class="sub">Tentatives échouées</div>
        </div>
        <div class="stat-card info">
            <div class="label">Normaux</div>
            <div class="value"><?= number_format($infoEvents ?? 0) ?></div>
            <div class="sub">Connexions · Inscriptions</div>
        </div>
    </div>

    <!-- ===================================================================
         GRAPHIQUES (3 colonnes)
         =================================================================== -->
    <div class="charts-grid">

        <!-- Graphique Timeline (occupe 2 colonnes) -->
        <div class="chart-box wide">
            <div class="chart-label">📈 Évolution sur 7 jours</div>
            <div class="chart-wrap">
                <canvas id="chartTimeline"></canvas>
            </div>
        </div>

        <!-- Graphique Donut répartition par type -->
        <div class="chart-box">
            <div class="chart-label">🍩 Répartition par type</div>
            <div class="chart-wrap">
                <canvas id="chartDonut"></canvas>
            </div>
        </div>

    </div>

    <!-- ===================================================================
         IPs SUSPECTES
         =================================================================== -->
    <?php if (!empty($suspiciousIPs)): ?>
    <div class="sec-panel" style="margin-bottom:20px;">
        <div class="panel-header">
            <span class="panel-title">⚠ IPs Suspectes (top 10 · 7 jours)</span>
            <span style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);">
                <?= count($suspiciousIPs) ?> IP(s) détectée(s)
            </span>
        </div>
        <div class="panel-body">
            <div class="ips-grid">
                <?php foreach ($suspiciousIPs as $ipData):
                    // Déterminer la classe du score selon sa valeur
                    $scoreClass = $ipData['severity_score'] >= 50 ? 'high'
                                : ($ipData['severity_score'] >= 20 ? 'medium' : 'low');
                ?>
                <div class="ip-card">
                    <div class="ip-card-header">
                        <!-- IP cliquable pour filtrer les événements -->
                        <span class="ip-addr"
                              onclick="filterByIP('<?= htmlspecialchars($ipData['ip']) ?>')"
                              style="cursor:pointer;"
                              title="Filtrer les événements de cette IP">
                            <?= htmlspecialchars($ipData['ip']) ?>
                        </span>
                        <span class="score-badge <?= $scoreClass ?>">
                            Score <?= $ipData['severity_score'] ?>
                        </span>
                    </div>

                    <!-- 6 métriques de l'IP -->
                    <div class="ip-metrics">
                        <div class="ip-metric">
                            <div class="m-val"><?= $ipData['total'] ?></div>
                            <div class="m-lbl">Total</div>
                        </div>
                        <div class="ip-metric">
                            <div class="m-val" style="<?= $ipData['failed_logins'] > 0 ? 'color:var(--orange)' : '' ?>">
                                <?= $ipData['failed_logins'] ?>
                            </div>
                            <div class="m-lbl">Échecs</div>
                        </div>
                        <div class="ip-metric">
                            <div class="m-val" style="<?= $ipData['csrf_violations'] > 0 ? 'color:var(--red)' : '' ?>">
                                <?= $ipData['csrf_violations'] ?>
                            </div>
                            <div class="m-lbl">CSRF</div>
                        </div>
                        <div class="ip-metric">
                            <div class="m-val" style="<?= $ipData['xss_attempts'] > 0 ? 'color:var(--red)' : '' ?>">
                                <?= $ipData['xss_attempts'] ?>
                            </div>
                            <div class="m-lbl">XSS</div>
                        </div>
                        <div class="ip-metric">
                            <div class="m-val" style="<?= $ipData['sqli_attempts'] > 0 ? 'color:var(--red)' : '' ?>">
                                <?= $ipData['sqli_attempts'] ?>
                            </div>
                            <div class="m-lbl">SQLi</div>
                        </div>
                        <div class="ip-metric">
                            <div class="m-val" style="<?= $ipData['blocks'] > 0 ? 'color:var(--orange)' : '' ?>">
                                <?= $ipData['blocks'] ?>
                            </div>
                            <div class="m-lbl">Bloquée</div>
                        </div>
                    </div>

                    <!-- Dernier événement -->
                    <div class="ip-last">
                        Dernier événement : <?= htmlspecialchars($ipData['last_event']) ?>
                    </div>

                    <!-- Actions -->
                    <div class="ip-actions">
                        <!-- Filtrer les événements de cette IP -->
                        <button class="btn-ip filter"
                                onclick="filterByIP('<?= htmlspecialchars($ipData['ip']) ?>')">
                            🔍 Voir
                        </button>
                        <!-- Bloquer l'IP (ouvre la modal) -->
                        <button class="btn-ip block"
                                onclick="openBlockModal('<?= htmlspecialchars($ipData['ip']) ?>')">
                            🚫 Bloquer
                        </button>
                        <!-- Whitelist -->
                        <button class="btn-ip whitelist"
                                onclick="whitelistIP('<?= htmlspecialchars($ipData['ip']) ?>')">
                            ✅ Whitelist
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ===================================================================
         FILTRES + TABLEAU DES ÉVÉNEMENTS
         =================================================================== -->
    <div class="sec-panel">
        <div class="panel-header">
            <span class="panel-title">📋 Événements récents</span>
            <span style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);">
                <?= number_format($pagination['total']) ?> résultat(s)
            </span>
        </div>

        <!-- Formulaire de filtres -->
        <div class="panel-body" style="padding-bottom:0;border-bottom:1px solid var(--border);">
            <form method="GET" action="/admin/security" id="filterForm">
                <div class="filters-grid">

                    <!-- Date début -->
                    <div class="filter-group">
                        <label>Du</label>
                        <input type="date"
                               name="date_from"
                               value="<?= htmlspecialchars($filters['date_from']) ?>"
                               max="<?= date('Y-m-d') ?>">
                    </div>

                    <!-- Date fin -->
                    <div class="filter-group">
                        <label>Au</label>
                        <input type="date"
                               name="date_to"
                               value="<?= htmlspecialchars($filters['date_to']) ?>"
                               max="<?= date('Y-m-d') ?>">
                    </div>

                    <!-- Type d'événement -->
                    <div class="filter-group">
                        <label>Type</label>
                        <select name="event_type">
                            <option value="">Tous les types</option>
                            <?php foreach ($eventTypes as $type): ?>
                            <option value="<?= htmlspecialchars($type) ?>"
                                <?= $filters['event_type'] === $type ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Sévérité -->
                    <div class="filter-group">
                        <label>Sévérité</label>
                        <select name="severity">
                            <option value="">Toutes</option>
                            <option value="CRITICAL" <?= $filters['severity'] === 'CRITICAL' ? 'selected' : '' ?>>CRITICAL</option>
                            <option value="WARNING"  <?= $filters['severity'] === 'WARNING'  ? 'selected' : '' ?>>WARNING</option>
                            <option value="INFO"     <?= $filters['severity'] === 'INFO'     ? 'selected' : '' ?>>INFO</option>
                        </select>
                    </div>

                    <!-- Recherche (IP, URI, email) -->
                    <div class="filter-group">
                        <label>Recherche (IP / URI / email)</label>
                        <input type="text"
                               name="search"
                               placeholder="ex: 192.168.1.100 ou /login"
                               value="<?= htmlspecialchars($filters['search']) ?>">
                        <!-- Champ IP caché (rempli par les boutons "Voir" des IP cards) -->
                        <input type="hidden" name="ip" id="ipHidden" value="<?= htmlspecialchars($filters['ip']) ?>">
                    </div>

                    <!-- Boutons -->
                    <div class="filter-group" style="flex-direction:row;gap:6px;">
                        <button type="submit" class="btn-filter btn-apply">Filtrer</button>
                        <a href="/admin/security" class="btn-filter btn-reset">✕</a>
                    </div>

                </div>
            </form>
        </div>

        <!-- Tableau des événements -->
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date / Heure</th>
                        <th>Sévérité</th>
                        <th>Type</th>
                        <th>IP</th>
                        <th>URI</th>
                        <th>Email</th>
                        <th>Données</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentEvents)): ?>
                        <?php foreach ($recentEvents as $event): ?>
                        <tr>
                            <!-- Timestamp -->
                            <td class="td-time">
                                <?= htmlspecialchars($event['timestamp']) ?>
                            </td>

                            <!-- Badge sévérité -->
                            <td>
                                <span class="badge <?= htmlspecialchars($event['severity']) ?>">
                                    <?= htmlspecialchars($event['severity']) ?>
                                </span>
                            </td>

                            <!-- Type d'événement -->
                            <td>
                                <span class="td-type"><?= htmlspecialchars($event['event_type']) ?></span>
                            </td>

                            <!-- IP cliquable pour filtrer -->
                            <td>
                                <?php if ($event['ip']): ?>
                                <span class="td-ip"
                                      onclick="filterByIP('<?= htmlspecialchars($event['ip']) ?>')"
                                      title="Filtrer par cette IP">
                                    <?= htmlspecialchars($event['ip']) ?>
                                </span>
                                <?php else: ?>
                                <span style="color:var(--text-muted)">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- URI tronquée -->
                            <td>
                                <span class="td-uri" title="<?= htmlspecialchars($event['uri'] ?? '') ?>">
                                    <?= htmlspecialchars($event['uri'] ?? '—') ?>
                                </span>
                            </td>

                            <!-- Email utilisateur -->
                            <td style="font-size:12px;font-family:var(--font-mono);color:var(--text-muted);">
                                <?= htmlspecialchars($event['user_email'] ?? '—') ?>
                            </td>

                            <!-- Données JSON (tooltip au survol) -->
                            <td>
                                <?php
                                $dataStr = '';
                                if (!empty($event['data'])) {
                                    $dataStr = is_array($event['data'])
                                        ? json_encode($event['data'], JSON_UNESCAPED_UNICODE)
                                        : $event['data'];
                                }
                                ?>
                                <?php if ($dataStr): ?>
                                <span class="td-data"
                                      title="<?= htmlspecialchars($dataStr) ?>">
                                    <?= htmlspecialchars(
                                        strlen($dataStr) > 40
                                            ? substr($dataStr, 0, 40) . '…'
                                            : $dataStr
                                    ) ?>
                                </span>
                                <?php else: ?>
                                <span style="color:var(--text-muted);">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <span class="icon">🛡</span>
                                    Aucun événement pour ces critères
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- ================================================================
             PAGINATION
             ================================================================ -->
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="pagination">
            <!-- Info résultats -->
            <span class="pagination-info">
                <?php
                $from = (($pagination['page'] - 1) * $pagination['per_page']) + 1;
                $to   = min($pagination['page'] * $pagination['per_page'], $pagination['total']);
                ?>
                Affichage de <?= $from ?> à <?= $to ?> sur <?= number_format($pagination['total']) ?>
            </span>

            <!-- Boutons de navigation -->
            <div class="pagination-btns">
                <?php
                // Construire les paramètres de la requête actuelle sans 'page'
                $queryParams = array_merge($filters, ['per_page' => $pagination['per_page']]);

                // Bouton précédent
                $prevPage = $pagination['page'] - 1;
                $prevClass = $pagination['page'] <= 1 ? 'page-btn disabled' : 'page-btn';
                $prevUrl = '/admin/security?' . http_build_query(array_merge($queryParams, ['page' => $prevPage]));
                ?>
                <a href="<?= $prevUrl ?>" class="<?= $prevClass ?>">←</a>

                <?php
                // Afficher au max 5 pages autour de la page courante
                $currentPage  = $pagination['page'];
                $totalPages   = $pagination['total_pages'];
                $startPage    = max(1, $currentPage - 2);
                $endPage      = min($totalPages, $currentPage + 2);

                // Toujours afficher la première page si on est loin
                if ($startPage > 1): ?>
                    <a href="<?= '/admin/security?' . http_build_query(array_merge($queryParams, ['page' => 1])) ?>"
                       class="page-btn">1</a>
                    <?php if ($startPage > 2): ?><span class="page-btn disabled">…</span><?php endif; ?>
                <?php endif; ?>

                <?php for ($p = $startPage; $p <= $endPage; $p++):
                    $pageClass = ($p === $currentPage) ? 'page-btn active' : 'page-btn';
                    $pageUrl   = '/admin/security?' . http_build_query(array_merge($queryParams, ['page' => $p]));
                ?>
                <a href="<?= $pageUrl ?>" class="<?= $pageClass ?>"><?= $p ?></a>
                <?php endfor; ?>

                <?php
                // Toujours afficher la dernière page si on est loin
                if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?><span class="page-btn disabled">…</span><?php endif; ?>
                    <a href="<?= '/admin/security?' . http_build_query(array_merge($queryParams, ['page' => $totalPages])) ?>"
                       class="page-btn"><?= $totalPages ?></a>
                <?php endif; ?>

                <?php
                // Bouton suivant
                $nextPage = $pagination['page'] + 1;
                $nextClass = $pagination['page'] >= $totalPages ? 'page-btn disabled' : 'page-btn';
                $nextUrl = '/admin/security?' . http_build_query(array_merge($queryParams, ['page' => $nextPage]));
                ?>
                <a href="<?= $nextUrl ?>" class="<?= $nextClass ?>">→</a>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /.sec-panel -->

</div><!-- /.sec-wrap -->

<!-- =========================================================================
     MODAL CONFIRMATION BLOCAGE IP
     ========================================================================= -->
<div class="modal-overlay" id="blockModal">
    <div class="modal">
        <h3>🚫 Bloquer cette IP</h3>
        <p>Cette IP sera immédiatement bloquée. Toutes ses requêtes seront rejetées.</p>
        <div class="modal-ip-display" id="modalIPDisplay">—</div>
        <label style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;display:block;">
            Raison du blocage
        </label>
        <input type="text" id="blockReason" placeholder="ex: Tentatives XSS répétées" value="">
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeBlockModal()">Annuler</button>
            <button class="btn-confirm-block" onclick="confirmBlockIP()">🚫 Confirmer le blocage</button>
        </div>
    </div>
</div>

<!-- =========================================================================
     CONTENEUR TOAST
     ========================================================================= -->
<div id="toast-container"></div>

<!-- =========================================================================
     SCRIPTS
     ========================================================================= -->
<script>
    // =========================================================================
    // SECTION 1 : DONNÉES PHP → JAVASCRIPT (pour les graphiques)
    // =========================================================================

    // Données timeline (graphique d'évolution sur 7 jours)
    const timelineLabels   = <?= json_encode($timelineLabels   ?? []) ?>;
    const timelineCritical = <?= json_encode($timelineCritical ?? []) ?>;
    const timelineWarning  = <?= json_encode($timelineWarning  ?? []) ?>;
    const timelineInfo     = <?= json_encode($timelineInfo     ?? []) ?>;

    // Données donut (répartition par type d'événement)
    const donutLabels = <?= json_encode($chartLabels ?? []) ?>;
    const donutData   = <?= json_encode($chartData   ?? []) ?>;
    const donutColors = <?= json_encode($chartColors ?? []) ?>;

    // =========================================================================
    // SECTION 2 : CONFIGURATION CHART.JS (defaults communes)
    // =========================================================================

    // Appliquer un thème sombre global à tous les graphiques
    Chart.defaults.color            = '#8494b0'; // Couleur texte par défaut
    Chart.defaults.font.family      = "'JetBrains Mono', monospace";
    Chart.defaults.font.size        = 11;
    Chart.defaults.borderColor      = '#1e2840'; // Couleur des grilles

    // =========================================================================
    // SECTION 3 : GRAPHIQUE TIMELINE (barres empilées)
    // =========================================================================
    (function() {
        const ctx = document.getElementById('chartTimeline');
        if (!ctx) return;

        // Si pas de données timeline, afficher un message
        if (timelineLabels.length === 0) {
            ctx.parentElement.innerHTML = '<p style="text-align:center;color:var(--text-muted);padding:80px 0;font-family:var(--font-mono);">Aucune donnée disponible</p>';
            return;
        }

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: timelineLabels,
                datasets: [
                    {
                        label: 'CRITICAL',
                        data: timelineCritical,
                        backgroundColor: 'rgba(255,59,59,.8)',
                        borderRadius: 2,
                        borderSkipped: false,
                    },
                    {
                        label: 'WARNING',
                        data: timelineWarning,
                        backgroundColor: 'rgba(255,140,66,.8)',
                        borderRadius: 2,
                        borderSkipped: false,
                    },
                    {
                        label: 'INFO',
                        data: timelineInfo,
                        backgroundColor: 'rgba(74,158,255,.5)',
                        borderRadius: 2,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 16, boxWidth: 10, boxHeight: 10 }
                    },
                    tooltip: {
                        backgroundColor: '#141920',
                        borderColor: '#1e2840',
                        borderWidth: 1,
                        padding: 10,
                    }
                },
                scales: {
                    x: {
                        stacked: true, // Barres empilées
                        grid: { display: false },
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: { precision: 0 }, // Entiers uniquement
                        grid: { color: '#1e2840' }
                    }
                }
            }
        });
    })();

    // =========================================================================
    // SECTION 4 : GRAPHIQUE DONUT (répartition par type)
    // =========================================================================
    (function() {
        const ctx = document.getElementById('chartDonut');
        if (!ctx) return;

        if (donutLabels.length === 0) {
            ctx.parentElement.innerHTML = '<p style="text-align:center;color:var(--text-muted);padding:80px 0;font-family:var(--font-mono);">Aucune donnée</p>';
            return;
        }

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: donutLabels,
                datasets: [{
                    data: donutData,
                    backgroundColor: donutColors,
                    borderWidth: 2,
                    borderColor: '#141920', // Séparation entre segments
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%', // Trou central plus grand pour un look moderne
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            boxWidth: 10,
                            boxHeight: 10,
                            // Tronquer les labels trop longs
                            generateLabels: function(chart) {
                                const data = chart.data;
                                return data.labels.map((label, i) => ({
                                    text: label.length > 16 ? label.substring(0, 16) + '…' : label,
                                    fillStyle: data.datasets[0].backgroundColor[i],
                                    hidden: false,
                                    index: i
                                }));
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#141920',
                        borderColor: '#1e2840',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            // Afficher le pourcentage dans le tooltip
                            label: function(ctx) {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct   = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                                return ` ${ctx.label} : ${ctx.parsed} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    })();

    // =========================================================================
    // SECTION 5 : FILTRE PAR IP (depuis les cartes ou le tableau)
    // =========================================================================

    /**
     * Remplit le champ IP caché et soumet le formulaire de filtres
     * @param {string} ip - Adresse IP à filtrer
     */
    function filterByIP(ip) {
        // Remplir le champ caché IP
        document.getElementById('ipHidden').value = ip;
        // Vider le champ search pour éviter conflit
        document.querySelector('input[name="search"]').value = '';
        // Soumettre le formulaire
        document.getElementById('filterForm').submit();
    }

    // =========================================================================
    // SECTION 6 : MODAL BLOCAGE IP
    // =========================================================================

    let currentIPToBlock = ''; // IP stockée en attente de confirmation

    /**
     * Ouvre la modal de confirmation de blocage
     * @param {string} ip - Adresse IP à bloquer
     */
    function openBlockModal(ip) {
        currentIPToBlock = ip;
        document.getElementById('modalIPDisplay').textContent = ip;
        document.getElementById('blockReason').value = '';
        document.getElementById('blockModal').classList.add('open');
        // Focus sur le champ raison
        setTimeout(() => document.getElementById('blockReason').focus(), 100);
    }

    /** Ferme la modal de confirmation */
    function closeBlockModal() {
        document.getElementById('blockModal').classList.remove('open');
        currentIPToBlock = '';
    }

    // Fermer la modal en cliquant sur l'overlay
    document.getElementById('blockModal').addEventListener('click', function(e) {
        if (e.target === this) closeBlockModal();
    });

    // =========================================================================
    // SECTION 7 : ACTIONS AJAX SUR LES IPs
    // =========================================================================

    /**
     * Envoie une requête POST AJAX pour une action sur une IP
     * @param {string} url     - URL de l'action (/admin/security/block-ip, etc.)
     * @param {object} data    - Données à envoyer (ip, reason, etc.)
     * @param {function} onSuccess - Callback en cas de succès
     */
    async function ipAction(url, data, onSuccess) {
        try {
            // Construire le body en form-urlencoded (compatible $_POST PHP)
            const body = Object.entries(data)
                .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
                .join('&');

            const res  = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body
            });

            const json = await res.json();

            if (json.success) {
                showToast(json.message, 'success');
                if (onSuccess) onSuccess();
            } else {
                showToast(json.message || 'Erreur serveur', 'error');
            }

        } catch (err) {
            showToast('Erreur de connexion', 'error');
            console.error('ipAction error:', err);
        }
    }

    /** Confirme le blocage après validation dans la modal */
    function confirmBlockIP() {
        if (!currentIPToBlock) return;

        const reason = document.getElementById('blockReason').value.trim()
                    || 'Bloquée manuellement par admin';

        closeBlockModal();

        ipAction('/admin/security/block-ip', {
            ip: currentIPToBlock,
            reason
        }, () => {
            // Recharger la page après blocage pour actualiser les données
            setTimeout(() => location.reload(), 1200);
        });
    }

    /**
     * Ajoute une IP à la whitelist
     * @param {string} ip - Adresse IP à whitelister
     */
    function whitelistIP(ip) {
        if (!confirm(`Ajouter ${ip} à la whitelist ?\n\nCette IP ne sera plus jamais bloquée automatiquement.`)) {
            return;
        }

        ipAction('/admin/security/whitelist-ip', {
            ip,
            description: 'Ajoutée manuellement depuis le dashboard'
        }, () => {
            setTimeout(() => location.reload(), 1200);
        });
    }

    // =========================================================================
    // SECTION 8 : SYSTÈME DE TOAST (notifications)
    // =========================================================================

    /**
     * Affiche une notification toast temporaire
     * @param {string} message - Message à afficher
     * @param {string} type    - 'success' ou 'error'
     */
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');

        const toast     = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <span>${type === 'success' ? '✅' : '❌'}</span>
            <span>${message}</span>
        `;

        container.appendChild(toast);

        // Auto-supprimer après 3 secondes
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(20px)';
            toast.style.transition = 'all .3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // =========================================================================
    // SECTION 9 : AUTO-REFRESH INTELLIGENT (toutes les 60s si onglet actif)
    // =========================================================================

    let refreshTimer = null;

    /**
     * Lance l'auto-refresh uniquement si l'onglet est visible
     * Évite de recharger inutilement en arrière-plan
     */
    function startAutoRefresh() {
        // Recharger toutes les 60 secondes (moins agressif que l'ancien 30s)
        refreshTimer = setTimeout(() => {
            if (!document.hidden) {
                location.reload();
            } else {
                // Si onglet caché, reprogrammer plus tard
                startAutoRefresh();
            }
        }, 60000);
    }

    // Démarrer l'auto-refresh
    startAutoRefresh();

    // Réinitialiser le timer quand l'onglet redevient visible
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            clearTimeout(refreshTimer);
            startAutoRefresh();
        }
    });

    // =========================================================================
    // SECTION 10 : AVERTISSEMENT CONSOLE pour événements critiques
    // =========================================================================
    <?php if (!empty($criticalEvents) && $criticalEvents > 0): ?>
    console.warn(
        '%c⚠ MarketFlow Security',
        'color:#ff3b3b;font-weight:bold;font-size:14px',
        '\n<?= $criticalEvents ?> événement(s) critique(s) détecté(s) sur les 7 derniers jours.'
    );
    <?php endif; ?>

</script>

</body>
</html>
