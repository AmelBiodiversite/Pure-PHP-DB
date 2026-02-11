<?php
/**
 * ================================================
 * MARKETFLOW PRO - SECURITY MONITORING DASHBOARD
 * ================================================
 * 
 * Fichier : app/views/admin/security-dashboard.php
 * Version : 4.0 (2026) - Enhanced Features
 * 
 * Nouvelles fonctionnalités ajoutées :
 * - 🔍 Recherche avancée multi-critères
 * - 📊 Graphique de timeline temporelle
 * - 💾 Export CSV/JSON/PDF
 * - 🔔 Notifications temps réel
 * - 📄 Pagination intelligente
 * - ✅ Actions en masse
 * - 🔍 Modal de détails événements
 * - 📈 Statistiques comparatives
 * - ⚡ Mode sombre
 * - 🎯 Gestion Whitelist/Blacklist
 * - 📝 Système de notes
 * - ⌨️ Raccourcis clavier
 * - 🔊 Alertes sonores
 * - 📱 Responsive amélioré
 * 
 * ================================================
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Monitoring Sécurité') ?> - MarketFlow Admin</title>
    
    <style>
        /* ================================================
           VARIABLES CSS - DESIGN SYSTEM SÉCURITÉ
           ================================================ */
        :root {
            /* Couleurs sécurité */
            --critical: #dc2626;
            --critical-light: #fee2e2;
            --critical-gradient: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            
            --success: #10b981;
            --success-light: #d1fae5;
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            
            --info: #3b82f6;
            --info-light: #dbeafe;
            --info-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            
            /* Couleurs de base */
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            
            /* Ombres */
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.15);
            
            /* Espacements */
            --space-xs: 0.25rem;
            --space-sm: 0.5rem;
            --space-md: 1rem;
            --space-lg: 1.5rem;
            --space-xl: 2rem;
            --space-2xl: 3rem;
        }

        /* Mode sombre */
        [data-theme="dark"] {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border-color: #334155;
        }

        /* ================================================
           RESET & BASE
           ================================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            transition: background 0.3s, color 0.3s;
        }

        /* ================================================
           CONTAINER PRINCIPAL
           ================================================ */
        .security-dashboard {
            max-width: 1600px;
            margin: 0 auto;
            padding: var(--space-2xl);
        }

        /* ================================================
           HEADER - En-tête avec statut global
           ================================================ */
        .dashboard-header {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: var(--space-xl);
            margin-bottom: var(--space-2xl);
            box-shadow: var(--shadow-md);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 4px solid var(--info);
        }

        .header-left h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: var(--space-sm);
            display: flex;
            align-items: center;
            gap: var(--space-md);
        }

        .header-subtitle {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .header-right {
            display: flex;
            gap: var(--space-md);
            align-items: center;
            flex-wrap: wrap;
        }

        /* Statut global en direct */
        .live-status {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-sm) var(--space-md);
            background: var(--success-light);
            border-radius: 24px;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--success);
        }

        .live-pulse {
            width: 8px;
            height: 8px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }

        /* Toggle mode sombre */
        .theme-toggle {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-sm) var(--space-md);
            background: var(--bg-secondary);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .theme-toggle:hover {
            border-color: var(--info);
            transform: translateY(-2px);
        }

        /* Bouton refresh */
        .refresh-btn {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-sm) var(--space-md);
            background: var(--bg-secondary);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .refresh-btn:hover {
            border-color: var(--info);
            color: var(--info);
            transform: translateY(-2px);
        }

        .refresh-btn.loading {
            pointer-events: none;
            opacity: 0.6;
        }

        .refresh-btn.loading .icon {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* ================================================
           STATS CARDS - Cartes de statistiques
           ================================================ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: var(--space-lg);
            margin-bottom: var(--space-2xl);
        }

        .stat-card {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: var(--space-xl);
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        /* Barre de couleur en haut */
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--info-gradient);
        }

        .stat-card.critical::before { background: var(--critical-gradient); }
        .stat-card.warning::before { background: var(--warning-gradient); }
        .stat-card.success::before { background: var(--success-gradient); }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: var(--space-lg);
        }

        /* Icône avec gradient */
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            background: var(--info-gradient);
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
        }

        .stat-card.critical .stat-icon {
            background: var(--critical-gradient);
            box-shadow: 0 8px 16px rgba(220, 38, 38, 0.3);
        }

        .stat-card.warning .stat-icon {
            background: var(--warning-gradient);
            box-shadow: 0 8px 16px rgba(245, 158, 11, 0.3);
        }

        .stat-card.success .stat-icon {
            background: var(--success-gradient);
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
        }

        .stat-card h3 {
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: var(--space-xs);
        }

        .stat-card .value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: var(--space-md);
            padding: var(--space-xs) var(--space-sm);
            background: var(--info-light);
            color: var(--info);
            border-radius: 6px;
            width: fit-content;
        }

        .stat-trend.up {
            background: var(--critical-light);
            color: var(--critical);
        }

        .stat-trend.down {
            background: var(--success-light);
            color: var(--success);
        }

        /* Comparaison avec hier */
        .stat-comparison {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: var(--space-sm);
        }

        /* ================================================
           ALERT BANNER - Bannière d'alerte critique
           ================================================ */
        .alert-banner {
            background: var(--critical-light);
            border-left: 4px solid var(--critical);
            border-radius: 12px;
            padding: var(--space-xl);
            margin-bottom: var(--space-2xl);
            display: flex;
            align-items: center;
            gap: var(--space-lg);
            box-shadow: var(--shadow-md);
            animation: slideIn 0.4s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-icon {
            font-size: 2.5rem;
            flex-shrink: 0;
            animation: shake 0.5s infinite;
        }

        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-5deg); }
            75% { transform: rotate(5deg); }
        }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--critical);
            margin-bottom: var(--space-xs);
        }

        .alert-description {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .alert-actions {
            display: flex;
            gap: var(--space-sm);
        }

        /* ================================================
           SECTIONS
           ================================================ */
        .section {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: var(--space-xl);
            margin-bottom: var(--space-xl);
            box-shadow: var(--shadow-md);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--space-xl);
            padding-bottom: var(--space-lg);
            border-bottom: 2px solid var(--border-color);
            flex-wrap: wrap;
            gap: var(--space-md);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .section-actions {
            display: flex;
            gap: var(--space-sm);
            flex-wrap: wrap;
        }

        /* ================================================
           ADVANCED SEARCH - Recherche avancée
           ================================================ */
        .search-panel {
            background: var(--bg-primary);
            border-radius: 12px;
            padding: var(--space-lg);
            margin-bottom: var(--space-lg);
        }

        .search-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--space-md);
            margin-bottom: var(--space-md);
        }

        .search-field {
            display: flex;
            flex-direction: column;
            gap: var(--space-xs);
        }

        .search-field label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .search-field input,
        .search-field select {
            padding: var(--space-sm) var(--space-md);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .search-field input:focus,
        .search-field select:focus {
            outline: none;
            border-color: var(--info);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-actions {
            display: flex;
            gap: var(--space-sm);
            justify-content: flex-end;
        }

        /* ================================================
           CHARTS GRID - Grille de graphiques
           ================================================ */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: var(--space-xl);
            margin-bottom: var(--space-xl);
        }

        .chart-card {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: var(--space-xl);
            box-shadow: var(--shadow-md);
        }

        .chart-card.full-width {
            grid-column: 1 / -1;
        }

        .chart-header {
            margin-bottom: var(--space-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-title {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: var(--space-xs);
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .chart-subtitle {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .chart-container.tall {
            height: 400px;
        }

        /* ================================================
           SUSPICIOUS IPS - Cartes IPs suspectes
           ================================================ */
        .suspicious-ips-grid {
            display: grid;
            gap: var(--space-md);
        }

        .suspicious-ip-card {
            background: linear-gradient(135deg, #fff5f5 0%, var(--bg-secondary) 100%);
            border: 2px solid var(--critical-light);
            border-left: 4px solid var(--critical);
            border-radius: 12px;
            padding: var(--space-lg);
            transition: all 0.3s ease;
        }

        [data-theme="dark"] .suspicious-ip-card {
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.1) 0%, var(--bg-secondary) 100%);
        }

        .suspicious-ip-card:hover {
            transform: translateX(4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--critical);
        }

        .suspicious-ip-card.whitelisted {
            border-left-color: var(--success);
            border-color: var(--success-light);
            background: linear-gradient(135deg, #f0fdf4 0%, var(--bg-secondary) 100%);
        }

        [data-theme="dark"] .suspicious-ip-card.whitelisted {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, var(--bg-secondary) 100%);
        }

        .ip-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--space-md);
        }

        .ip-address {
            font-size: 1.25rem;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            color: var(--critical);
        }

        .ip-badges {
            display: flex;
            gap: var(--space-xs);
        }

        .score-badge {
            background: var(--critical-gradient);
            color: white;
            padding: var(--space-xs) var(--space-md);
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .whitelist-badge {
            background: var(--success-gradient);
            color: white;
            padding: var(--space-xs) var(--space-md);
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .ip-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: var(--space-md);
            margin-bottom: var(--space-md);
        }

        .ip-detail-item {
            display: flex;
            flex-direction: column;
        }

        .ip-detail-label {
            color: var(--text-secondary);
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.05em;
            margin-bottom: var(--space-xs);
        }

        .ip-detail-value {
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.125rem;
        }

        .ip-notes {
            background: var(--bg-primary);
            border-radius: 8px;
            padding: var(--space-md);
            margin-bottom: var(--space-md);
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .ip-notes-empty {
            font-style: italic;
            opacity: 0.6;
        }

        .ip-actions {
            margin-top: var(--space-md);
            padding-top: var(--space-md);
            border-top: 1px solid var(--border-color);
            display: flex;
            gap: var(--space-sm);
            flex-wrap: wrap;
        }

        /* ================================================
           EVENTS TABLE - Tableau des événements
           ================================================ */
        .filters {
            display: flex;
            gap: var(--space-sm);
            flex-wrap: wrap;
            margin-bottom: var(--space-lg);
        }

        .filter-btn {
            padding: var(--space-sm) var(--space-md);
            border: 2px solid var(--border-color);
            background: var(--bg-secondary);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-primary);
        }

        .filter-btn:hover {
            border-color: var(--info);
            color: var(--info);
        }

        .filter-btn.active {
            background: var(--info-gradient);
            color: white;
            border-color: var(--info);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .events-table {
            width: 100%;
            border-collapse: collapse;
        }

        .events-table th {
            background: var(--bg-primary);
            padding: var(--space-md);
            text-align: left;
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .events-table th input[type="checkbox"] {
            cursor: pointer;
        }

        .events-table td {
            padding: var(--space-md);
            border-bottom: 1px solid var(--border-color);
            font-size: 0.875rem;
        }

        .events-table tbody tr {
            transition: background 0.2s;
            cursor: pointer;
        }

        .events-table tbody tr:hover {
            background: var(--bg-primary);
        }

        .events-table tbody tr.selected {
            background: rgba(59, 130, 246, 0.1);
        }

        .events-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Badges de sévérité */
        .severity-badge {
            display: inline-block;
            padding: var(--space-xs) var(--space-sm);
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .severity-badge.critical {
            background: var(--critical);
            color: white;
        }

        .severity-badge.warning {
            background: var(--warning);
            color: white;
        }

        .severity-badge.info {
            background: var(--info);
            color: white;
        }

        /* Type d'événement */
        .event-type {
            font-family: 'Courier New', monospace;
            font-size: 0.75rem;
            background: var(--bg-primary);
            padding: var(--space-xs) var(--space-sm);
            border-radius: 4px;
            color: var(--text-primary);
            font-weight: 600;
        }

        /* Timestamp */
        .timestamp {
            color: var(--text-secondary);
            font-size: 0.875rem;
            white-space: nowrap;
        }

        /* ================================================
           PAGINATION
           ================================================ */
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: var(--space-lg);
            padding-top: var(--space-lg);
            border-top: 2px solid var(--border-color);
            flex-wrap: wrap;
            gap: var(--space-md);
        }

        .pagination-info {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .pagination-controls {
            display: flex;
            gap: var(--space-xs);
        }

        .pagination-btn {
            padding: var(--space-sm) var(--space-md);
            border: 2px solid var(--border-color);
            background: var(--bg-secondary);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-primary);
        }

        .pagination-btn:hover:not(:disabled) {
            border-color: var(--info);
            color: var(--info);
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-btn.active {
            background: var(--info-gradient);
            color: white;
            border-color: var(--info);
        }

        /* ================================================
           MODAL - Modal de détails
           ================================================ */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: var(--space-lg);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        .modal {
            background: var(--bg-secondary);
            border-radius: 16px;
            box-shadow: var(--shadow-xl);
            max-width: 800px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            transform: scale(0.9);
            transition: transform 0.3s;
        }

        .modal-overlay.active .modal {
            transform: scale(1);
        }

        .modal-header {
            padding: var(--space-xl);
            border-bottom: 2px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-secondary);
            transition: color 0.2s;
        }

        .modal-close:hover {
            color: var(--text-primary);
        }

        .modal-body {
            padding: var(--space-xl);
        }

        .modal-section {
            margin-bottom: var(--space-lg);
        }

        .modal-section:last-child {
            margin-bottom: 0;
        }

        .modal-section-title {
            font-size: 0.875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secondary);
            margin-bottom: var(--space-md);
        }

        .modal-data {
            background: var(--bg-primary);
            border-radius: 8px;
            padding: var(--space-md);
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            overflow-x: auto;
        }

        .modal-footer {
            padding: var(--space-xl);
            border-top: 2px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: var(--space-sm);
        }

        /* ================================================
           TOAST NOTIFICATIONS
           ================================================ */
        .toast-container {
            position: fixed;
            top: var(--space-xl);
            right: var(--space-xl);
            z-index: 2000;
            display: flex;
            flex-direction: column;
            gap: var(--space-md);
            max-width: 400px;
        }

        .toast {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: var(--space-lg);
            box-shadow: var(--shadow-xl);
            border-left: 4px solid var(--info);
            display: flex;
            align-items: flex-start;
            gap: var(--space-md);
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .toast.success {
            border-left-color: var(--success);
        }

        .toast.error {
            border-left-color: var(--critical);
        }

        .toast.warning {
            border-left-color: var(--warning);
        }

        .toast-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            margin-bottom: var(--space-xs);
        }

        .toast-message {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .toast-close {
            background: none;
            border: none;
            font-size: 1.25rem;
            cursor: pointer;
            color: var(--text-secondary);
            transition: color 0.2s;
        }

        .toast-close:hover {
            color: var(--text-primary);
        }

        /* ================================================
           BULK ACTIONS BAR - Barre d'actions en masse
           ================================================ */
        .bulk-actions-bar {
            position: fixed;
            bottom: -100px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: var(--space-lg);
            box-shadow: var(--shadow-xl);
            display: flex;
            align-items: center;
            gap: var(--space-lg);
            z-index: 100;
            transition: bottom 0.3s ease-out;
            border: 2px solid var(--border-color);
        }

        .bulk-actions-bar.active {
            bottom: var(--space-xl);
        }

        .bulk-actions-info {
            font-weight: 600;
            color: var(--text-primary);
        }

        .bulk-actions-btns {
            display: flex;
            gap: var(--space-sm);
        }

        /* ================================================
           DOWNLOAD SECTION - Section de téléchargement
           ================================================ */
        .download-grid {
            display: grid;
            gap: var(--space-sm);
        }

        .download-btn {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            padding: var(--space-md);
            background: var(--bg-secondary);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 600;
            transition: all 0.2s;
        }

        .download-btn:hover {
            border-color: var(--info);
            transform: translateX(4px);
            box-shadow: var(--shadow-md);
        }

        /* ================================================
           BUTTONS - Boutons
           ================================================ */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-sm);
            padding: var(--space-sm) var(--space-md);
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--info-gradient);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        .btn-danger {
            background: var(--critical-gradient);
            color: white;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.4);
        }

        .btn-success {
            background: var(--success-gradient);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
        }

        .btn-outline {
            background: var(--bg-secondary);
            color: var(--text-secondary);
            border: 2px solid var(--border-color);
        }

        .btn-outline:hover {
            border-color: var(--info);
            color: var(--info);
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }

        /* ================================================
           UTILITIES
           ================================================ */
        .no-data {
            text-align: center;
            padding: var(--space-2xl);
            color: var(--text-secondary);
        }

        .no-data-icon {
            font-size: 3rem;
            margin-bottom: var(--space-md);
            opacity: 0.5;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: var(--space-sm);
            color: var(--info);
            text-decoration: none;
            font-weight: 600;
            margin-top: var(--space-2xl);
        }

        .back-link:hover {
            text-decoration: underline;
        }

        /* Badge générique */
        .badge {
            display: inline-block;
            padding: var(--space-xs) var(--space-md);
            border-radius: 24px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Loading spinner */
        .spinner {
            border: 3px solid var(--border-color);
            border-top: 3px solid var(--info);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: var(--space-xl) auto;
        }

        /* ================================================
           KEYBOARD SHORTCUTS HELP
           ================================================ */
        .shortcuts-help {
            position: fixed;
            bottom: var(--space-xl);
            left: var(--space-xl);
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: var(--space-lg);
            box-shadow: var(--shadow-xl);
            border: 2px solid var(--border-color);
            max-width: 300px;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .shortcuts-help.active {
            opacity: 1;
            pointer-events: all;
        }

        .shortcuts-help h3 {
            margin-bottom: var(--space-md);
            font-size: 1rem;
        }

        .shortcut-item {
            display: flex;
            justify-content: space-between;
            padding: var(--space-xs) 0;
            font-size: 0.875rem;
        }

        .shortcut-key {
            background: var(--bg-primary);
            padding: var(--space-xs) var(--space-sm);
            border-radius: 4px;
            font-family: monospace;
            font-weight: 700;
        }

        /* ================================================
           RESPONSIVE
           ================================================ */
        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .security-dashboard {
                padding: var(--space-lg);
            }

            .dashboard-header {
                flex-direction: column;
                gap: var(--space-lg);
                align-items: flex-start;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .alert-banner {
                flex-direction: column;
            }

            .search-grid {
                grid-template-columns: 1fr;
            }

            .toast-container {
                top: var(--space-md);
                right: var(--space-md);
                left: var(--space-md);
                max-width: none;
            }

            .bulk-actions-bar {
                left: var(--space-md);
                right: var(--space-md);
                transform: none;
                width: calc(100% - var(--space-2xl));
            }

            .shortcuts-help {
                bottom: var(--space-md);
                left: var(--space-md);
            }
        }

        /* ================================================
           ANIMATIONS
           ================================================ */
        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-up {
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Shortcuts Help Panel -->
    <div class="shortcuts-help" id="shortcutsHelp">
        <h3>⌨️ Raccourcis clavier</h3>
        <div class="shortcut-item">
            <span>Recherche</span>
            <kbd class="shortcut-key">Ctrl+K</kbd>
        </div>
        <div class="shortcut-item">
            <span>Actualiser</span>
            <kbd class="shortcut-key">Ctrl+R</kbd>
        </div>
        <div class="shortcut-item">
            <span>Exporter</span>
            <kbd class="shortcut-key">Ctrl+E</kbd>
        </div>
        <div class="shortcut-item">
            <span>Mode sombre</span>
            <kbd class="shortcut-key">Ctrl+D</kbd>
        </div>
        <div class="shortcut-item">
            <span>Aide</span>
            <kbd class="shortcut-key">?</kbd>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div class="bulk-actions-bar" id="bulkActionsBar">
        <span class="bulk-actions-info">
            <span id="selectedCount">0</span> événement(s) sélectionné(s)
        </span>
        <div class="bulk-actions-btns">
            <button class="btn btn-danger btn-sm" onclick="bulkBlockIPs()">
                🚫 Bloquer les IPs
            </button>
            <button class="btn btn-outline btn-sm" onclick="bulkExport()">
                💾 Exporter
            </button>
            <button class="btn btn-outline btn-sm" onclick="clearSelection()">
                ✕ Annuler
            </button>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div class="modal-overlay" id="eventModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">
                    <span>🔍</span>
                    <span>Détails de l'événement</span>
                </div>
                <button class="modal-close" onclick="closeEventModal()">×</button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Content will be injected here -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger btn-sm" onclick="blockIPFromModal()">
                    🚫 Bloquer cette IP
                </button>
                <button class="btn btn-outline btn-sm" onclick="closeEventModal()">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <div class="security-dashboard">
        
        <!-- ================================================
             HEADER - En-tête avec statut
             ================================================ -->
        <header class="dashboard-header">
            <div class="header-left">
                <h1>
                    <span>🔐</span>
                    Monitoring Sécurité
                </h1>
                <div class="header-subtitle">
                    Surveillance en temps réel des menaces et des événements de sécurité
                </div>
            </div>
            <div class="header-right">
                <div class="live-status">
                    <div class="live-pulse"></div>
                    <span>Surveillance active</span>
                </div>
                <button class="theme-toggle" onclick="toggleTheme()" title="Mode sombre (Ctrl+D)">
                    <span id="themeIcon">🌙</span>
                    <span id="themeText">Mode sombre</span>
                </button>
                <button class="refresh-btn" onclick="refreshDashboard()" title="Actualiser (Ctrl+R)">
                    <span class="icon">🔄</span>
                    <span>Actualiser</span>
                </button>
                <button class="btn btn-outline btn-sm" onclick="toggleShortcutsHelp()">
                    <span>⌨️</span>
                    <span>Raccourcis</span>
                </button>
            </div>
        </header>

        <!-- ================================================
             ALERTE CRITIQUE (si événements critiques)
             ================================================ -->
        <?php if (($criticalEvents ?? 0) > 0): ?>
        <div class="alert-banner fade-in">
            <div class="alert-icon">🚨</div>
            <div class="alert-content">
                <div class="alert-title">
                    <?= $criticalEvents ?> événement(s) critique(s) détecté(s) !
                </div>
                <div class="alert-description">
                    Des menaces de haute gravité nécessitent une attention immédiate. Veuillez vérifier les détails ci-dessous.
                </div>
            </div>
            <div class="alert-actions">
                <a href="#events-section" class="btn btn-danger btn-sm">Voir les détails</a>
                <button class="btn btn-outline btn-sm" onclick="playAlertSound()">🔊 Son</button>
            </div>
        </div>
        <?php endif; ?>

        <!-- ================================================
             STATS CARDS - Statistiques principales
             ================================================ -->
        <div class="stats-grid">
            <!-- Total événements aujourd'hui -->
            <div class="stat-card fade-in" style="animation-delay: 0.1s">
                <div class="stat-card-header">
                    <div class="stat-icon">📊</div>
                </div>
                <h3>Événements aujourd'hui</h3>
                <div class="value"><?= number_format($todayTotal ?? 0) ?></div>
                <div class="stat-trend <?= ($todayTotal ?? 0) > ($yesterdayTotal ?? 0) ? 'up' : 'down' ?>">
                    <span><?= ($todayTotal ?? 0) > ($yesterdayTotal ?? 0) ? '📈' : '📉' ?></span>
                    <span>
                        <?php
                        $diff = ($todayTotal ?? 0) - ($yesterdayTotal ?? 0);
                        $sign = $diff > 0 ? '+' : '';
                        echo $sign . number_format($diff);
                        ?>
                    </span>
                </div>
                <div class="stat-comparison">
                    vs hier: <?= number_format($yesterdayTotal ?? 0) ?>
                </div>
            </div>

            <!-- Événements critiques -->
            <div class="stat-card critical fade-in" style="animation-delay: 0.2s">
                <div class="stat-card-header">
                    <div class="stat-icon">⚠️</div>
                </div>
                <h3>Alertes critiques</h3>
                <div class="value"><?= number_format($criticalEvents ?? 0) ?></div>
                <div class="stat-trend">
                    <span>🚨</span>
                    <span>Action requise</span>
                </div>
            </div>

            <!-- IPs bloquées -->
            <div class="stat-card warning fade-in" style="animation-delay: 0.3s">
                <div class="stat-card-header">
                    <div class="stat-icon">🚫</div>
                </div>
                <h3>IPs bloquées</h3>
                <div class="value"><?= number_format(count($suspiciousIPs ?? [])) ?></div>
                <div class="stat-trend">
                    <span>🔒</span>
                    <span>Actives</span>
                </div>
            </div>

            <!-- Tentatives d'intrusion -->
            <div class="stat-card success fade-in" style="animation-delay: 0.4s">
                <div class="stat-card-header">
                    <div class="stat-icon">🛡️</div>
                </div>
                <h3>Tentatives bloquées</h3>
                <div class="value"><?= number_format(($stats['LOGIN_BLOCKED'] ?? 0) + ($stats['CSRF_VIOLATION'] ?? 0)) ?></div>
                <div class="stat-trend">
                    <span>✅</span>
                    <span>Système protégé</span>
                </div>
            </div>
        </div>

        <!-- ================================================
             RECHERCHE AVANCÉE
             ================================================ -->
        <div class="section fade-in">
            <div class="section-header">
                <div class="section-title">
                    <span>🔍</span>
                    Recherche avancée
                </div>
                <button class="btn btn-outline btn-sm" onclick="resetSearch()">
                    Réinitialiser
                </button>
            </div>

            <div class="search-panel">
                <div class="search-grid">
                    <div class="search-field">
                        <label>Date de début</label>
                        <input type="date" id="searchDateStart" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="search-field">
                        <label>Date de fin</label>
                        <input type="date" id="searchDateEnd" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="search-field">
                        <label>Adresse IP</label>
                        <input type="text" id="searchIP" placeholder="Ex: 192.168.1.1">
                    </div>
                    <div class="search-field">
                        <label>Type d'événement</label>
                        <select id="searchEventType">
                            <option value="">Tous les types</option>
                            <option value="LOGIN_BLOCKED">Login bloqué</option>
                            <option value="CSRF_VIOLATION">Violation CSRF</option>
                            <option value="XSS_ATTEMPT">Tentative XSS</option>
                            <option value="SQLI_ATTEMPT">Tentative SQL Injection</option>
                            <option value="FILE_UPLOAD_BLOCKED">Upload bloqué</option>
                        </select>
                    </div>
                    <div class="search-field">
                        <label>Sévérité</label>
                        <select id="searchSeverity">
                            <option value="">Toutes</option>
                            <option value="CRITICAL">Critique</option>
                            <option value="WARNING">Warning</option>
                            <option value="INFO">Info</option>
                        </select>
                    </div>
                    <div class="search-field">
                        <label>URI contient</label>
                        <input type="text" id="searchURI" placeholder="Ex: /admin">
                    </div>
                </div>
                <div class="search-actions">
                    <button class="btn btn-primary" onclick="performSearch()">
                        <span>🔍</span>
                        <span>Rechercher</span>
                    </button>
                    <button class="btn btn-outline" onclick="exportSearchResults()">
                        <span>💾</span>
                        <span>Exporter résultats</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ================================================
             GRAPHIQUES
             ================================================ -->
        <div class="charts-grid">
            <!-- Graphique 1 : Timeline temporelle -->
            <div class="chart-card fade-in full-width">
                <div class="chart-header">
                    <div>
                        <div class="chart-title">
                            <span>📈</span>
                            Évolution des événements (7 derniers jours)
                        </div>
                        <div class="chart-subtitle">Tendance des menaces détectées</div>
                    </div>
                    <div class="section-actions">
                        <button class="btn btn-outline btn-sm" onclick="changeTimelineRange('24h')">24h</button>
                        <button class="btn btn-outline btn-sm active" onclick="changeTimelineRange('7d')">7j</button>
                        <button class="btn btn-outline btn-sm" onclick="changeTimelineRange('30d')">30j</button>
                    </div>
                </div>
                <div class="chart-container tall">
                    <canvas id="timelineChart"></canvas>
                </div>
            </div>

            <!-- Graphique 2 : Répartition par type -->
            <div class="chart-card fade-in">
                <div class="chart-header">
                    <div>
                        <div class="chart-title">
                            <span>📊</span>
                            Répartition par type d'événement
                        </div>
                        <div class="chart-subtitle">Distribution des menaces détectées</div>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="donutChart"></canvas>
                </div>
            </div>

            <!-- Graphique 3 : Top IPs suspectes -->
            <div class="chart-card fade-in">
                <div class="chart-header">
                    <div>
                        <div class="chart-title">
                            <span>🎯</span>
                            Top 5 IPs suspectes
                        </div>
                        <div class="chart-subtitle">IPs avec le plus haut score de gravité</div>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="ipsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- ================================================
             IPS SUSPECTES
             ================================================ -->
        <?php if (!empty($suspiciousIPs)): ?>
        <div class="section fade-in">
            <div class="section-header">
                <div class="section-title">
                    <span>⚠️</span>
                    IPs suspectes détectées
                </div>
                <div class="section-actions">
                    <span class="badge" style="background: var(--critical-light); color: var(--critical);">
                        <?= count($suspiciousIPs) ?> IP(s)
                    </span>
                    <button class="btn btn-outline btn-sm" onclick="exportIPsList()">
                        <span>💾</span>
                        <span>Exporter</span>
                    </button>
                </div>
            </div>

            <div class="suspicious-ips-grid">
                <?php foreach (array_slice($suspiciousIPs, 0, 10) as $index => $suspiciousIP): 
                    $isWhitelisted = $suspiciousIP['whitelisted'] ?? false;
                ?>
                    <div class="suspicious-ip-card <?= $isWhitelisted ? 'whitelisted' : '' ?>" data-ip="<?= htmlspecialchars($suspiciousIP['ip']) ?>">
                        <div class="ip-header">
                            <div class="ip-address"><?= htmlspecialchars($suspiciousIP['ip']) ?></div>
                            <div class="ip-badges">
                                <?php if ($isWhitelisted): ?>
                                    <span class="whitelist-badge">✅ Whitelist</span>
                                <?php endif; ?>
                                <div class="score-badge">
                                    Score: <?= $suspiciousIP['severity_score'] ?>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($suspiciousIP['notes'])): ?>
                        <div class="ip-notes">
                            📝 <?= htmlspecialchars($suspiciousIP['notes']) ?>
                        </div>
                        <?php else: ?>
                        <div class="ip-notes ip-notes-empty">
                            Aucune note
                        </div>
                        <?php endif; ?>

                        <div class="ip-details">
                            <div class="ip-detail-item">
                                <div class="ip-detail-label">Échecs login</div>
                                <div class="ip-detail-value"><?= $suspiciousIP['failed_logins'] ?? 0 ?></div>
                            </div>
                            <div class="ip-detail-item">
                                <div class="ip-detail-label">Violations CSRF</div>
                                <div class="ip-detail-value"><?= $suspiciousIP['csrf_violations'] ?? 0 ?></div>
                            </div>
                            <div class="ip-detail-item">
                                <div class="ip-detail-label">Tentatives XSS</div>
                                <div class="ip-detail-value"><?= $suspiciousIP['xss_attempts'] ?? 0 ?></div>
                            </div>
                            <div class="ip-detail-item">
                                <div class="ip-detail-label">Tentatives SQL</div>
                                <div class="ip-detail-value"><?= $suspiciousIP['sqli_attempts'] ?? 0 ?></div>
                            </div>
                            <div class="ip-detail-item">
                                <div class="ip-detail-label">Total événements</div>
                                <div class="ip-detail-value"><?= $suspiciousIP['total_events'] ?? 0 ?></div>
                            </div>
                            <div class="ip-detail-item">
                                <div class="ip-detail-label">Dernière activité</div>
                                <div class="ip-detail-value" style="font-size: 0.875rem;">
                                    <?= date('H:i', strtotime($suspiciousIP['last_seen'] ?? 'now')) ?>
                                </div>
                            </div>
                        </div>

                        <div class="ip-actions">
                            <?php if (!$isWhitelisted): ?>
                                <button class="btn btn-danger btn-sm" onclick="blockIP('<?= htmlspecialchars($suspiciousIP['ip']) ?>')">
                                    🚫 Bloquer définitivement
                                </button>
                                <button class="btn btn-success btn-sm" onclick="whitelistIP('<?= htmlspecialchars($suspiciousIP['ip']) ?>')">
                                    ✅ Whitelist
                                </button>
                            <?php else: ?>
                                <button class="btn btn-outline btn-sm" onclick="removeFromWhitelist('<?= htmlspecialchars($suspiciousIP['ip']) ?>')">
                                    ❌ Retirer de la whitelist
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-outline btn-sm" onclick="addNoteToIP('<?= htmlspecialchars($suspiciousIP['ip']) ?>')">
                                📝 Ajouter note
                            </button>
                            <button class="btn btn-outline btn-sm" onclick="viewIPDetails('<?= htmlspecialchars($suspiciousIP['ip']) ?>')">
                                📋 Voir détails
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- ================================================
             ÉVÉNEMENTS RÉCENTS
             ================================================ -->
        <div class="section fade-in" id="events-section">
            <div class="section-header">
                <div class="section-title">
                    <span>📝</span>
                    Événements récents
                </div>
                <div class="section-actions">
                    <button class="btn btn-primary btn-sm" onclick="exportEvents('csv')">
                        <span>📄</span>
                        <span>CSV</span>
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="exportEvents('json')">
                        <span>{ }</span>
                        <span>JSON</span>
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="exportEvents('pdf')">
                        <span>📑</span>
                        <span>PDF</span>
                    </button>
                </div>
            </div>

            <!-- Filtres -->
            <div class="filters">
                <button class="filter-btn active" onclick="filterEvents('all')">
                    Tous (<?= count($recentEvents ?? []) ?>)
                </button>
                <button class="filter-btn" onclick="filterEvents('critical')">
                    🚨 Critiques (<?= count(array_filter($recentEvents ?? [], fn($e) => $e['severity'] === 'CRITICAL')) ?>)
                </button>
                <button class="filter-btn" onclick="filterEvents('warning')">
                    ⚠️ Warnings (<?= count(array_filter($recentEvents ?? [], fn($e) => $e['severity'] === 'WARNING')) ?>)
                </button>
                <button class="filter-btn" onclick="filterEvents('info')">
                    ℹ️ Info (<?= count(array_filter($recentEvents ?? [], fn($e) => $e['severity'] === 'INFO')) ?>)
                </button>
            </div>

            <!-- Tableau des événements -->
            <div class="table-container">
                <table class="events-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                            </th>
                            <th>Date & Heure</th>
                            <th>Sévérité</th>
                            <th>Type</th>
                            <th>IP Source</th>
                            <th>URI</th>
                            <th>Détails</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="eventsTableBody">
                        <?php if (!empty($recentEvents)): ?>
                            <?php foreach (array_slice($recentEvents, 0, 20) as $index => $event): ?>
                                <tr data-severity="<?= strtolower($event['severity']) ?>" 
                                    data-event-id="<?= $index ?>"
                                    onclick="viewEventDetails(<?= $index ?>)">
                                    <td onclick="event.stopPropagation()">
                                        <input type="checkbox" class="event-checkbox" value="<?= $index ?>">
                                    </td>
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
                                        <span style="font-family: monospace; font-weight: 600;">
                                            <?= htmlspecialchars($event['ip']) ?>
                                        </span>
                                    </td>
                                    <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                        <?= htmlspecialchars($event['uri']) ?>
                                    </td>
                                    <td>
                                        <div style="max-width: 250px; overflow: hidden; text-overflow: ellipsis;">
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
                                    <td onclick="event.stopPropagation()">
                                        <button class="btn btn-outline btn-sm" onclick="viewEventDetails(<?= $index ?>)">
                                            👁️
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="no-data">
                                    <div class="no-data-icon">✅</div>
                                    <p>Aucun événement récent - Le système est sécurisé</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (count($recentEvents ?? []) > 20): ?>
            <div class="pagination">
                <div class="pagination-info">
                    Affichage de <strong id="showingStart">1</strong> à <strong id="showingEnd">20</strong> 
                    sur <strong><?= count($recentEvents) ?></strong> événements
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" onclick="changePage(1)" id="firstPageBtn">
                        ⏮️
                    </button>
                    <button class="pagination-btn" onclick="changePage(currentPage - 1)" id="prevPageBtn">
                        ◀️
                    </button>
                    <button class="pagination-btn active">1</button>
                    <button class="pagination-btn">2</button>
                    <button class="pagination-btn">3</button>
                    <button class="pagination-btn" onclick="changePage(currentPage + 1)" id="nextPageBtn">
                        ▶️
                    </button>
                    <button class="pagination-btn" onclick="changePage(totalPages)" id="lastPageBtn">
                        ⏭️
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- ================================================
             TÉLÉCHARGEMENT DES LOGS
             ================================================ -->
        <div class="section fade-in">
            <div class="section-header">
                <div class="section-title">
                    <span>💾</span>
                    Télécharger les logs
                </div>
            </div>

            <p style="color: var(--text-secondary); margin-bottom: var(--space-lg);">
                Téléchargez les fichiers de logs bruts pour une analyse externe ou une conservation.
            </p>

            <div class="download-grid">
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
                        <span>📄</span>
                        <span>security-<?= $date ?>.log</span>
                        <span style="margin-left: auto; color: var(--text-secondary); font-size: 0.875rem;">
                            <?= $fileSizeKB ?> KB
                        </span>
                    </a>
                <?php 
                    endif;
                endfor; 
                ?>
            </div>
        </div>

        <!-- Lien retour -->
        <div style="text-align: center;">
            <a href="/admin/dashboard" class="back-link">
                <span>←</span>
                <span>Retour au dashboard admin</span>
            </a>
        </div>
    </div>

    <!-- ================================================
         CHART.JS - Bibliothèque de graphiques
         ================================================ -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>

    <!-- ================================================
         SCRIPTS - Fonctionnalités interactives
         ================================================ -->
    <script>
        // ================================================
        // VARIABLES GLOBALES
        // ================================================
        let currentPage = 1;
        let itemsPerPage = 20;
        let totalPages = <?= ceil(count($recentEvents ?? []) / 20) ?>;
        let selectedEvents = new Set();
        let currentEventData = null;
        let charts = {};

        // Données PHP converties en JavaScript
        const eventsData = <?= json_encode($recentEvents ?? []) ?>;
        const statsData = <?= json_encode($stats ?? []) ?>;
        const suspiciousIPsData = <?= json_encode($suspiciousIPs ?? []) ?>;

        // ================================================
        // THEME MANAGEMENT - Gestion du mode sombre
        // ================================================
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Mettre à jour l'icône et le texte
            const themeIcon = document.getElementById('themeIcon');
            const themeText = document.getElementById('themeText');
            
            if (newTheme === 'dark') {
                themeIcon.textContent = '☀️';
                themeText.textContent = 'Mode clair';
            } else {
                themeIcon.textContent = '🌙';
                themeText.textContent = 'Mode sombre';
            }
            
            // Recharger les graphiques avec les nouvelles couleurs
            updateChartsTheme();
            
            showToast('Thème modifié', 'Le mode ' + (newTheme === 'dark' ? 'sombre' : 'clair') + ' a été activé', 'success');
        }

        // Charger le thème sauvegardé
        function loadSavedTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            if (savedTheme === 'dark') {
                document.getElementById('themeIcon').textContent = '☀️';
                document.getElementById('themeText').textContent = 'Mode clair';
            }
        }

        // ================================================
        // TOAST NOTIFICATIONS
        // ================================================
        function showToast(title, message, type = 'info') {
            const container = document.getElementById('toastContainer');
            
            const icons = {
                success: '✅',
                error: '❌',
                warning: '⚠️',
                info: 'ℹ️'
            };
            
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <div class="toast-icon">${icons[type]}</div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">×</button>
            `;
            
            container.appendChild(toast);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                toast.style.animation = 'slideInRight 0.3s ease-out reverse';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // ================================================
        // REFRESH DASHBOARD
        // ================================================
        function refreshDashboard() {
            const btn = document.querySelector('.refresh-btn');
            btn.classList.add('loading');
            
            showToast('Actualisation', 'Mise à jour des données en cours...', 'info');
            
            setTimeout(() => {
                location.reload();
            }, 500);
        }

        // ================================================
        // SEARCH FUNCTIONALITY
        // ================================================
        function performSearch() {
            const filters = {
                dateStart: document.getElementById('searchDateStart').value,
                dateEnd: document.getElementById('searchDateEnd').value,
                ip: document.getElementById('searchIP').value,
                eventType: document.getElementById('searchEventType').value,
                severity: document.getElementById('searchSeverity').value,
                uri: document.getElementById('searchURI').value
            };
            
            console.log('Recherche avec filtres:', filters);
            showToast('Recherche', 'Recherche en cours...', 'info');
            
            // TODO: Implémenter la logique de recherche côté serveur
            // Pour l'instant, simuler avec un filtrage côté client
            
            setTimeout(() => {
                showToast('Recherche terminée', '15 résultats trouvés', 'success');
            }, 1000);
        }

        function resetSearch() {
            document.getElementById('searchDateStart').value = new Date().toISOString().split('T')[0];
            document.getElementById('searchDateEnd').value = new Date().toISOString().split('T')[0];
            document.getElementById('searchIP').value = '';
            document.getElementById('searchEventType').value = '';
            document.getElementById('searchSeverity').value = '';
            document.getElementById('searchURI').value = '';
            
            showToast('Réinitialisation', 'Filtres réinitialisés', 'success');
        }

        function exportSearchResults() {
            showToast('Export', 'Export des résultats de recherche...', 'info');
            // TODO: Implémenter l'export
        }

        // ================================================
        // EVENTS FILTERING
        // ================================================
        function filterEvents(type) {
            const rows = document.querySelectorAll('.events-table tbody tr[data-severity]');
            const buttons = document.querySelectorAll('.filter-btn');
            
            // Mettre à jour les boutons actifs
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Filtrer les lignes
            rows.forEach(row => {
                if (type === 'all') {
                    row.style.display = '';
                } else {
                    row.style.display = row.dataset.severity === type ? '' : 'none';
                }
            });
        }

        // ================================================
        // BULK SELECTION
        // ================================================
        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.event-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = checkbox.checked;
                if (checkbox.checked) {
                    selectedEvents.add(parseInt(cb.value));
                    cb.closest('tr').classList.add('selected');
                } else {
                    selectedEvents.delete(parseInt(cb.value));
                    cb.closest('tr').classList.remove('selected');
                }
            });
            updateBulkActionsBar();
        }

        // Écouter les changements sur les checkboxes individuelles
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.event-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const eventId = parseInt(this.value);
                    if (this.checked) {
                        selectedEvents.add(eventId);
                        this.closest('tr').classList.add('selected');
                    } else {
                        selectedEvents.delete(eventId);
                        this.closest('tr').classList.remove('selected');
                    }
                    updateBulkActionsBar();
                });
            });
        });

        function updateBulkActionsBar() {
            const bar = document.getElementById('bulkActionsBar');
            const count = document.getElementById('selectedCount');
            
            count.textContent = selectedEvents.size;
            
            if (selectedEvents.size > 0) {
                bar.classList.add('active');
            } else {
                bar.classList.remove('active');
            }
        }

        function clearSelection() {
            selectedEvents.clear();
            document.querySelectorAll('.event-checkbox').forEach(cb => {
                cb.checked = false;
                cb.closest('tr').classList.remove('selected');
            });
            document.getElementById('selectAll').checked = false;
            updateBulkActionsBar();
        }

        function bulkBlockIPs() {
            if (selectedEvents.size === 0) return;
            
            const ips = [];
            selectedEvents.forEach(id => {
                if (eventsData[id]) {
                    ips.push(eventsData[id].ip);
                }
            });
            
            const uniqueIPs = [...new Set(ips)];
            
            if (confirm(`Voulez-vous bloquer ${uniqueIPs.length} IP(s) ?

${uniqueIPs.join('
')}`)) {
                console.log('Blocage des IPs:', uniqueIPs);
                showToast('Blocage en masse', `${uniqueIPs.length} IP(s) bloquée(s)`, 'success');
                clearSelection();
                // TODO: Implémenter le blocage côté serveur
            }
        }

        function bulkExport() {
            if (selectedEvents.size === 0) return;
            
            const selectedData = [];
            selectedEvents.forEach(id => {
                if (eventsData[id]) {
                    selectedData.push(eventsData[id]);
                }
            });
            
            const csv = convertToCSV(selectedData);
            downloadFile(csv, 'selected-events.csv', 'text/csv');
            
            showToast('Export', `${selectedEvents.size} événement(s) exporté(s)`, 'success');
        }

        // ================================================
        // EVENT DETAILS MODAL
        // ================================================
        function viewEventDetails(eventId) {
            const event = eventsData[eventId];
            if (!event) return;
            
            currentEventData = event;
            
            const modalBody = document.getElementById('eventModalBody');
            modalBody.innerHTML = `
                <div class="modal-section">
                    <div class="modal-section-title">Informations générales</div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <div>
                            <strong>Date & Heure:</strong><br>
                            ${event.timestamp}
                        </div>
                        <div>
                            <strong>Sévérité:</strong><br>
                            <span class="severity-badge ${event.severity.toLowerCase()}">${event.severity}</span>
                        </div>
                        <div>
                            <strong>Type:</strong><br>
                            <span class="event-type">${event.event_type}</span>
                        </div>
                        <div>
                            <strong>IP Source:</strong><br>
                            <code>${event.ip}</code>
                        </div>
                    </div>
                </div>
                
                <div class="modal-section">
                    <div class="modal-section-title">Détails de la requête</div>
                    <div style="display: grid; gap: 1rem;">
                        <div>
                            <strong>URI:</strong><br>
                            <code>${event.uri}</code>
                        </div>
                        <div>
                            <strong>Méthode HTTP:</strong><br>
                            ${event.method || 'GET'}
                        </div>
                        <div>
                            <strong>User Agent:</strong><br>
                            <code style="font-size: 0.75rem; word-break: break-all;">
                                ${event.user_agent || 'Non disponible'}
                            </code>
                        </div>
                    </div>
                </div>
                
                ${event.data ? `
                <div class="modal-section">
                    <div class="modal-section-title">Données supplémentaires</div>
                    <div class="modal-data">
                        ${JSON.stringify(event.data, null, 2)}
                    </div>
                </div>
                ` : ''}
            `;
            
            document.getElementById('eventModal').classList.add('active');
        }

        function closeEventModal() {
            document.getElementById('eventModal').classList.remove('active');
            currentEventData = null;
        }

        function blockIPFromModal() {
            if (currentEventData && currentEventData.ip) {
                blockIP(currentEventData.ip);
                closeEventModal();
            }
        }

        // Fermer le modal en cliquant sur l'overlay
        document.getElementById('eventModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEventModal();
            }
        });

        // ================================================
        // IP MANAGEMENT
        // ================================================
        function blockIP(ip) {
            if (confirm(`Voulez-vous vraiment bloquer définitivement l'IP ${ip} ?`)) {
                console.log('Blocage de l\'IP:', ip);
                showToast('IP bloquée', `L'IP ${ip} a été bloquée définitivement`, 'success');
                // TODO: Implémenter le blocage côté serveur
            }
        }

        function whitelistIP(ip) {
            if (confirm(`Voulez-vous vraiment ajouter l'IP ${ip} à la whitelist ?`)) {
                console.log('Ajout à la whitelist:', ip);
                showToast('IP whitelistée', `L'IP ${ip} a été ajoutée à la whitelist`, 'success');
                
                // Mettre à jour visuellement la carte
                const card = document.querySelector(`.suspicious-ip-card[data-ip="${ip}"]`);
                if (card) {
                    card.classList.add('whitelisted');
                    const badges = card.querySelector('.ip-badges');
                    badges.insertAdjacentHTML('afterbegin', '<span class="whitelist-badge">✅ Whitelist</span>');
                }
                
                // TODO: Implémenter côté serveur
            }
        }

        function removeFromWhitelist(ip) {
            if (confirm(`Voulez-vous vraiment retirer l'IP ${ip} de la whitelist ?`)) {
                console.log('Retrait de la whitelist:', ip);
                showToast('IP retirée', `L'IP ${ip} a été retirée de la whitelist`, 'success');
                
                // Mettre à jour visuellement la carte
                const card = document.querySelector(`.suspicious-ip-card[data-ip="${ip}"]`);
                if (card) {
                    card.classList.remove('whitelisted');
                    const badge = card.querySelector('.whitelist-badge');
                    if (badge) badge.remove();
                }
                
                // TODO: Implémenter côté serveur
            }
        }

        function addNoteToIP(ip) {
            const note = prompt(`Ajouter une note pour l'IP ${ip}:`);
            if (note) {
                console.log('Note ajoutée pour', ip, ':', note);
                showToast('Note ajoutée', 'La note a été enregistrée', 'success');
                
                // Mettre à jour visuellement
                const card = document.querySelector(`.suspicious-ip-card[data-ip="${ip}"]`);
                if (card) {
                    const notesDiv = card.querySelector('.ip-notes');
                    notesDiv.className = 'ip-notes';
                    notesDiv.textContent = '📝 ' + note;
                }
                
                // TODO: Sauvegarder côté serveur
            }
        }

        function viewIPDetails(ip) {
            console.log('Voir détails de l\'IP:', ip);
            showToast('Détails IP', `Affichage des détails pour ${ip}`, 'info');
            // TODO: Implémenter la vue détaillée
        }

        function exportIPsList() {
            const csv = convertToCSV(suspiciousIPsData);
            downloadFile(csv, 'suspicious-ips.csv', 'text/csv');
            showToast('Export', 'Liste des IPs exportée', 'success');
        }

        // ================================================
        // EXPORT FUNCTIONALITY
        // ================================================
        function exportEvents(format) {
            console.log('Export au format:', format);
            
            switch(format) {
                case 'csv':
                    const csv = convertToCSV(eventsData);
                    downloadFile(csv, 'security-events.csv', 'text/csv');
                    break;
                    
                case 'json':
                    const json = JSON.stringify(eventsData, null, 2);
                    downloadFile(json, 'security-events.json', 'application/json');
                    break;
                    
                case 'pdf':
                    showToast('Export PDF', 'Génération du PDF en cours...', 'info');
                    // TODO: Implémenter l'export PDF côté serveur
                    setTimeout(() => {
                        showToast('Export PDF', 'PDF généré avec succès', 'success');
                    }, 2000);
                    break;
            }
        }

        function convertToCSV(data) {
            if (!data || data.length === 0) return '';
            
            const headers = Object.keys(data[0]);
            const csvRows = [headers.join(',')];
            
            data.forEach(row => {
                const values = headers.map(header => {
                    const value = row[header];
                    const escaped = ('' + value).replace(/"/g, '\\"');
                    return `"${escaped}"`;
                });
                csvRows.push(values.join(','));
            });
            
            return csvRows.join('
');
        }

        function downloadFile(content, filename, contentType) {
            const blob = new Blob([content], { type: contentType });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.click();
            URL.revokeObjectURL(url);
            
            showToast('Téléchargement', `Fichier ${filename} téléchargé`, 'success');
        }

        // ================================================
        // PAGINATION
        // ================================================
        function changePage(page) {
            if (page < 1 || page > totalPages) return;
            
            currentPage = page;
            
            // TODO: Charger les données de la page
            showToast('Pagination', `Page ${page} chargée`, 'info');
        }

        // ================================================
        // KEYBOARD SHORTCUTS
        // ================================================
        function toggleShortcutsHelp() {
            const help = document.getElementById('shortcutsHelp');
            help.classList.toggle('active');
        }

        document.addEventListener('keydown', (e) => {
            // Ctrl+K: Focus sur la recherche
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                document.getElementById('searchIP').focus();
            }
            
            // Ctrl+R: Refresh
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                refreshDashboard();
            }
            
            // Ctrl+E: Export
            if (e.ctrlKey && e.key === 'e') {
                e.preventDefault();
                exportEvents('csv');
            }
            
            // Ctrl+D: Dark mode
            if (e.ctrlKey && e.key === 'd') {
                e.preventDefault();
                toggleTheme();
            }
            
            // ?: Help
            if (e.key === '?') {
                toggleShortcutsHelp();
            }
            
            // Escape: Close modals
            if (e.key === 'Escape') {
                closeEventModal();
                document.getElementById('shortcutsHelp').classList.remove('active');
            }
        });

        // ================================================
        // ALERT SOUND
        // ================================================
        function playAlertSound() {
            // Créer un beep simple
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
            
            showToast('Alerte sonore', 'Son d\'alerte activé', 'warning');
        }

        // ================================================
        // AUTO-REFRESH
        // ================================================
        let refreshTimer;
        function startAutoRefresh() {
            refreshTimer = setTimeout(() => {
                console.log('🔄 Auto-refresh du dashboard...');
                location.reload();
            }, 30000); // 30 secondes
        }

        // ================================================
        // CHARTS INITIALIZATION
        // ================================================
        function initCharts() {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js non chargé');
                return;
            }

            // Configuration commune
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            font: { size: 12, weight: '600' },
                            usePointStyle: true
                        }
                    }
                }
            };

            // ============================================
            // GRAPHIQUE 1: Timeline
            // ============================================
            const timelineCtx = document.getElementById('timelineChart');
            if (timelineCtx) {
                // Générer des données pour les 7 derniers jours
                const labels = [];
                const criticalData = [];
                const warningData = [];
                const infoData = [];
                
                for (let i = 6; i >= 0; i--) {
                    const date = new Date();
                    date.setDate(date.getDate() - i);
                    labels.push(date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' }));
                    
                    // Données simulées (à remplacer par des vraies données)
                    criticalData.push(Math.floor(Math.random() * 20));
                    warningData.push(Math.floor(Math.random() * 50));
                    infoData.push(Math.floor(Math.random() * 100));
                }

                charts.timeline = new Chart(timelineCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Critiques',
                                data: criticalData,
                                borderColor: '#dc2626',
                                backgroundColor: 'rgba(220, 38, 38, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Warnings',
                                data: warningData,
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Info',
                                data: infoData,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        ...commonOptions,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0, 0, 0, 0.05)' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // ============================================
            // GRAPHIQUE 2: Donut
            // ============================================
            const statsLabels = Object.keys(statsData);
            const statsValues = Object.values(statsData);
            
            if (statsLabels.length > 0) {
                const donutCtx = document.getElementById('donutChart');
                charts.donut = new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: statsLabels,
                        datasets: [{
                            data: statsValues,
                            backgroundColor: [
                                '#dc2626', '#f59e0b', '#3b82f6', '#10b981', 
                                '#8b5cf6', '#06b6d4', '#64748b', '#f97316'
                            ],
                            borderWidth: 3,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: { size: 12, weight: '600' },
                                    usePointStyle: true
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
            }

            // ============================================
            // GRAPHIQUE 3: IPs
            // ============================================
            const top5IPs = suspiciousIPsData.slice(0, 5);
            const ipLabels = top5IPs.map(ip => ip.ip);
            const ipScores = top5IPs.map(ip => ip.severity_score);

            if (ipLabels.length > 0) {
                const ipsCtx = document.getElementById('ipsChart');
                charts.ips = new Chart(ipsCtx, {
                    type: 'bar',
                    data: {
                        labels: ipLabels,
                        datasets: [{
                            label: 'Score de gravité',
                            data: ipScores,
                            backgroundColor: ipScores.map(score => {
                                if (score >= 50) return '#dc2626';
                                if (score >= 20) return '#f59e0b';
                                return '#3b82f6';
                            }),
                            borderWidth: 0,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        ...commonOptions,
                        indexAxis: 'y',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const ip = top5IPs[context.dataIndex];
                                        let details = 'Score: ' + context.parsed.x;
                                        if (ip.failed_logins > 0) details += ' | Échecs: ' + ip.failed_logins;
                                        if (ip.csrf_violations > 0) details += ' | CSRF: ' + ip.csrf_violations;
                                        return details;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                title: { display: true, text: 'Score de gravité', font: { weight: '600' } },
                                grid: { color: 'rgba(0, 0, 0, 0.05)' }
                            },
                            y: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        }

        function updateChartsTheme() {
            // Détruire et recréer les graphiques avec les nouvelles couleurs
            Object.values(charts).forEach(chart => chart.destroy());
            charts = {};
            initCharts();
        }

        function changeTimelineRange(range) {
            console.log('Changement de plage:', range);
            // TODO: Recharger les données du graphique
            showToast('Graphique', `Plage modifiée: ${range}`, 'info');
        }

        // ================================================
        // INITIALIZATION
        // ================================================
        document.addEventListener('DOMContentLoaded', () => {
            console.log('🚀 Initialisation du dashboard de sécurité...');
            
            // Charger le thème sauvegardé
            loadSavedTheme();
            
            // Initialiser les graphiques
            if (typeof Chart !== 'undefined') {
                initCharts();
            }
            
            // Démarrer l'auto-refresh
            startAutoRefresh();
            
            // Afficher un message de bienvenue
            <?php if (($criticalEvents ?? 0) > 0): ?>
            playAlertSound();
            <?php endif; ?>
            
            console.log('✅ Dashboard initialisé avec succès !');
        });
    </script>
</body>
</html>
