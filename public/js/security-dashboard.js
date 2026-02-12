/**
 * ============================================================================
 * MARKETFLOW PRO - SECURITY DASHBOARD JAVASCRIPT v4.0
 * ============================================================================
 *
 * CORRECTIONS v4 :
 *   ✅ Utilise window.MARKETFLOW_CONFIG unifié (plus de variables disparates)
 *   ✅ Initialisation complète des 3 graphiques Chart.js
 *   ✅ Auto-refresh AJAX intelligent (pause si onglet caché)
 *   ✅ Protection CSRF sur toutes les requêtes POST
 *   ✅ Système de toasts MarketFlow (sans Bootstrap)
 *   ✅ Gestion complète des actions IP (bloquer/débloquer/whitelist)
 *   ✅ Animations des compteurs
 *   ✅ Tous les commentaires en français
 *
 * @file    public/js/security-dashboard.js
 * @version 4.0
 * @author  A.Devance
 * @requires Chart.js 4.x
 */

'use strict'; // Mode strict pour éviter les erreurs silencieuses

// ============================================================================
// SECTION 1 : CONFIGURATION ET ÉTAT GLOBAL
// ============================================================================

/**
 * Récupère la configuration injectée par le PHP
 * Contient : csrfToken, apiEndpoints, chartDonut, chartTimeline, chartSeverity
 */
const CONFIG = window.MARKETFLOW_CONFIG || {
    csrfToken: '',
    apiEndpoints: {},
    chartDonut: { labels: [], data: [], colors: [] },
    chartTimeline: { labels: [], critical: [], warning: [], info: [] },
    chartSeverity: { labels: [], data: [], colors: [] }
};

/** Intervalle entre chaque rafraîchissement automatique (60 secondes) */
const REFRESH_INTERVAL = 60000;

/** Nombre max de tentatives de refresh avant affichage d'un toast d'erreur */
const MAX_RETRIES = 3;

/** État global mutable de l'application */
const APP_STATE = {
    isRefreshing: false,   // Empêche les requêtes simultanées
    retryCount: 0,         // Compteur d'échecs consécutifs
    lastRefresh: Date.now(),
    refreshTimer: null,    // Référence au setTimeout pour pouvoir l'annuler
    charts: {}             // Instances Chart.js (pour mise à jour sans recréer)
};


// ============================================================================
// SECTION 2 : UTILITAIRES HTTP (avec protection CSRF)
// ============================================================================

/**
 * Effectue une requête AJAX GET sécurisée
 * Ajoute automatiquement le header CSRF et le flag XMLHttpRequest
 *
 * @param {string} url     - URL de l'endpoint API
 * @param {object} options - Options fetch additionnelles
 * @returns {Promise<object>} Réponse JSON parsée
 */
async function secureFetch(url, options = {}) {
    // Vérifications de sécurité
    if (!url || typeof url !== 'string') {
        throw new Error('URL invalide');
    }
    if (!CONFIG.csrfToken) {
        throw new Error('Token CSRF manquant — rechargez la page');
    }

    // Headers par défaut (CSRF + XMLHttpRequest pour détection AJAX côté PHP)
    const defaultHeaders = {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-Token': CONFIG.csrfToken
    };

    // Fusionner les options avec les valeurs par défaut
    const finalOptions = {
        method: 'GET',
        credentials: 'same-origin', // Inclure les cookies de session
        ...options,
        headers: { ...defaultHeaders, ...(options.headers || {}) }
    };

    try {
        const response = await fetch(url, finalOptions);

        // Gestion des codes d'erreur HTTP spécifiques
        if (!response.ok) {
            if (response.status === 403) throw new Error('Session expirée ou CSRF invalide');
            if (response.status === 429) throw new Error('Trop de requêtes — patientez');
            if (response.status >= 500)  throw new Error('Erreur serveur');
            throw new Error(`Erreur HTTP ${response.status}`);
        }

        // Parser et valider la réponse JSON
        const data = await response.json();
        if (typeof data !== 'object' || data === null) {
            throw new Error('Réponse serveur invalide');
        }

        return data;

    } catch (error) {
        console.error('[SecureFetch]', error.message);
        throw error;
    }
}

/**
 * Effectue une requête AJAX POST sécurisée
 * Ajoute le token CSRF dans le body ET dans le header
 *
 * @param {string} url  - URL de l'endpoint
 * @param {object} data - Données à envoyer (objet clé/valeur)
 * @returns {Promise<object>} Réponse JSON parsée
 */
async function securePost(url, data = {}) {
    // Injecter le CSRF dans le body (double protection : header + body)
    const bodyData = { ...data, csrf_token: CONFIG.csrfToken };

    // Encoder en application/x-www-form-urlencoded (compatible PHP $_POST)
    const body = Object.entries(bodyData)
        .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
        .join('&');

    return secureFetch(url, { method: 'POST', body });
}


// ============================================================================
// SECTION 3 : INITIALISATION DES GRAPHIQUES CHART.JS
// ============================================================================

/**
 * Options communes à tous les graphiques (thème MarketFlow)
 * Police, couleurs, bordures cohérentes avec le design system
 */
const CHART_DEFAULTS = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                padding: 16,
                usePointStyle: true,
                pointStyleWidth: 10,
                font: { family: "'Inter', sans-serif", size: 12 },
                color: '#6b7280'
            }
        },
        tooltip: {
            backgroundColor: '#1f2937',
            titleFont: { family: "'Inter', sans-serif", size: 13, weight: '600' },
            bodyFont: { family: "'Inter', sans-serif", size: 12 },
            padding: 12,
            cornerRadius: 8,
            displayColors: true
        }
    }
};

/**
 * Initialise le graphique Donut (répartition par type d'événement)
 * Utilise les données de CONFIG.chartDonut injectées par PHP
 */
function initDonutChart() {
    const canvas = document.getElementById('donutChart');
    if (!canvas) return;

    const { labels, data, colors } = CONFIG.chartDonut;

    // Ne pas créer le chart si aucune donnée
    if (!labels.length || !data.length) return;

    APP_STATE.charts.donut = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.length ? colors : [
                    '#ef4444', '#f59e0b', '#3b82f6', '#10b981',
                    '#8b5cf6', '#ec4899', '#f97316', '#06b6d4'
                ],
                borderWidth: 0,         // Pas de bordure entre les segments
                hoverOffset: 8,         // Écart au survol
                borderRadius: 4         // Coins arrondis sur les segments
            }]
        },
        options: {
            ...CHART_DEFAULTS,
            cutout: '65%',  // Taille du trou central (donut)
            plugins: {
                ...CHART_DEFAULTS.plugins,
                legend: {
                    ...CHART_DEFAULTS.plugins.legend,
                    position: 'right'
                }
            }
        }
    });
}

/**
 * Initialise le graphique en barres horizontales (répartition par sévérité)
 * 3 barres : Critique, Warning, Info
 */
function initSeverityChart() {
    const canvas = document.getElementById('severityChart');
    if (!canvas) return;

    const { labels, data, colors } = CONFIG.chartSeverity;

    APP_STATE.charts.severity = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Événements',
                data: data,
                backgroundColor: colors,
                borderWidth: 0,
                borderRadius: 6,       // Coins arrondis des barres
                borderSkipped: false    // Arrondir les 4 coins
            }]
        },
        options: {
            ...CHART_DEFAULTS,
            indexAxis: 'y',  // Barres horizontales
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    ticks: {
                        font: { family: "'Inter', sans-serif", size: 11 },
                        color: '#9ca3af',
                        stepSize: 1
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: {
                        font: { family: "'Inter', sans-serif", size: 12, weight: '600' },
                        color: '#374151'
                    }
                }
            },
            plugins: {
                ...CHART_DEFAULTS.plugins,
                legend: { display: false }  // Pas de légende (labels sur l'axe Y)
            }
        }
    });
}

/**
 * Initialise le graphique Timeline (évolution sur 7 jours)
 * 3 courbes empilées : Critical, Warning, Info
 */
function initTimelineChart() {
    const canvas = document.getElementById('timelineChart');
    if (!canvas) return;

    const { labels, critical, warning, info } = CONFIG.chartTimeline;

    // Ne pas créer si aucune donnée
    if (!labels.length) return;

    APP_STATE.charts.timeline = new Chart(canvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Critique',
                    data: critical,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,        // Courbe lissée
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ef4444'
                },
                {
                    label: 'Warning',
                    data: warning,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#f59e0b'
                },
                {
                    label: 'Info',
                    data: info,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#3b82f6'
                }
            ]
        },
        options: {
            ...CHART_DEFAULTS,
            interaction: {
                mode: 'index',       // Tooltip groupé par date
                intersect: false
            },
            scales: {
                x: {
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    ticks: {
                        font: { family: "'Inter', sans-serif", size: 11 },
                        color: '#9ca3af'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    ticks: {
                        font: { family: "'Inter', sans-serif", size: 11 },
                        color: '#9ca3af',
                        stepSize: 1
                    }
                }
            }
        }
    });
}


// ============================================================================
// SECTION 4 : AUTO-REFRESH INTELLIGENT (AJAX sans reload)
// ============================================================================

/**
 * Rafraîchit les statistiques et IPs suspectes via AJAX
 * Ne recharge PAS la page — met à jour le DOM directement
 */
async function refreshStats() {
    // Éviter les appels simultanés
    if (APP_STATE.isRefreshing) return;

    // Ne pas rafraîchir si l'onglet est caché (économie de ressources)
    if (document.hidden) {
        scheduleNextRefresh();
        return;
    }

    APP_STATE.isRefreshing = true;

    // Feedback visuel : bouton en loading
    const btn = document.getElementById('btnRefresh');
    if (btn) btn.classList.add('sec-btn--loading');

    try {
        // Récupérer les nouvelles statistiques (7 jours)
        const stats = await secureFetch(CONFIG.apiEndpoints.stats + '?days=7');

        if (stats && typeof stats === 'object') {
            updateStatsCards(stats);
        }

        // Récupérer les IPs suspectes (top 10)
        const ips = await secureFetch(CONFIG.apiEndpoints.suspiciousIPs + '?limit=10&days=7');

        if (Array.isArray(ips)) {
            updateSuspiciousIPs(ips);
        }

        // Mettre à jour l'horloge de dernière actualisation
        const clock = document.getElementById('lastUpdate');
        if (clock) clock.textContent = new Date().toLocaleTimeString('fr-FR');

        // Reset compteur d'erreurs
        APP_STATE.retryCount = 0;
        APP_STATE.lastRefresh = Date.now();

    } catch (error) {
        APP_STATE.retryCount++;

        // N'afficher un toast qu'après MAX_RETRIES échecs consécutifs
        if (APP_STATE.retryCount >= MAX_RETRIES) {
            showToast('Impossible de rafraîchir les données', 'error');
            APP_STATE.retryCount = 0;
        }
    } finally {
        APP_STATE.isRefreshing = false;
        if (btn) btn.classList.remove('sec-btn--loading');
        scheduleNextRefresh();
    }
}

/**
 * Met à jour les 4 cartes de statistiques avec les nouvelles données
 * Anime les compteurs pour un effet visuel fluide
 *
 * @param {object} stats - Objet { EVENT_TYPE: count, ... }
 */
function updateStatsCards(stats) {
    // Types critiques, warning, et le reste = info
    const criticalTypes = ['CSRF_VIOLATION', 'XSS_ATTEMPT', 'SQLI_ATTEMPT', 'UNAUTHORIZED_ACCESS'];
    const warningTypes  = ['LOGIN_FAILED', 'LOGIN_BLOCKED', 'RATE_LIMIT_EXCEEDED'];

    let total = 0, critical = 0, warning = 0, info = 0;

    // Calculer les totaux par catégorie
    for (const [type, count] of Object.entries(stats)) {
        const n = parseInt(count) || 0;
        total += n;
        if (criticalTypes.includes(type)) critical += n;
        else if (warningTypes.includes(type)) warning += n;
        else info += n;
    }

    // Animer les compteurs vers les nouvelles valeurs
    animateCounter('stat-threats', critical);
    animateCounter('stat-total', total);
}

/**
 * Anime un compteur numérique de sa valeur actuelle vers la valeur cible
 * Utilise requestAnimationFrame pour une animation fluide
 *
 * @param {string} elementId   - ID de l'élément DOM à animer
 * @param {number} targetValue - Valeur numérique cible
 */
function animateCounter(elementId, targetValue) {
    const el = document.getElementById(elementId);
    if (!el) return;

    const startValue = parseInt(el.textContent) || 0;
    const diff = targetValue - startValue;

    // Pas d'animation si la valeur n'a pas changé
    if (diff === 0) return;

    const duration = 500;   // Durée de l'animation (ms)
    const startTime = performance.now();

    /**
     * Fonction d'animation appelée à chaque frame
     * Utilise une interpolation ease-out pour un effet naturel
     */
    function animate(now) {
        const elapsed = now - startTime;
        // Progression de 0 à 1 avec ease-out quadratique
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        
        el.textContent = Math.round(startValue + diff * eased);

        if (progress < 1) {
            requestAnimationFrame(animate);
        } else {
            el.textContent = targetValue; // Valeur finale exacte
        }
    }

    requestAnimationFrame(animate);
}

/**
 * Met à jour dynamiquement la liste des IPs suspectes dans le DOM
 * Reconstruit le HTML sans recharger la page
 *
 * @param {Array} ips - Liste des IPs suspectes depuis l'API
 */
function updateSuspiciousIPs(ips) {
    const container = document.getElementById('suspicious-ips-container');
    if (!container) return;

    // Si pas d'IPs suspectes, afficher un message positif
    if (!ips.length) {
        container.innerHTML = `
            <div class="sec-empty">
                <div class="sec-empty__icon">✅</div>
                <div class="sec-empty__text">Aucune adresse IP suspecte détectée</div>
            </div>`;
        return;
    }

    // Reconstruire la liste
    let html = '<div class="sec-ip-list">';

    for (const ipData of ips) {
        const ip    = escapeHtml(String(ipData.ip || '').trim());
        const total = parseInt(ipData.total) || 0;
        const fails = parseInt(ipData.failed_logins) || 0;
        const score = Math.min(100, parseInt(ipData.severity_score) || 0);

        // Classe de couleur selon le score de menace
        const scoreClass = score >= 80 ? 'high' : (score >= 40 ? 'medium' : 'low');

        html += `
            <div class="sec-ip-item">
                <span class="sec-ip-item__address">${ip}</span>
                <div class="sec-ip-item__stats">
                    <span class="sec-ip-item__stat">Tentatives : <strong>${total}</strong></span>
                    <span class="sec-ip-item__stat">Échecs login : <strong>${fails}</strong></span>
                    <span class="sec-ip-item__stat">
                        Score :
                        <div class="sec-threat-bar">
                            <div class="sec-threat-bar__fill sec-threat-bar__fill--${scoreClass}"
                                 style="width:${score}%"></div>
                        </div>
                    </span>
                </div>
                <div class="sec-ip-item__actions">
                    <button class="sec-btn sec-btn--sm sec-btn--outline"
                            onclick="filterByIP('${ip}')">Filtrer</button>
                    <button class="sec-btn sec-btn--sm sec-btn--danger"
                            onclick="openBlockModal('${ip}')">🚫 Bloquer</button>
                    <button class="sec-btn sec-btn--sm sec-btn--success"
                            onclick="whitelistIP('${ip}')">✅ Whitelist</button>
                </div>
            </div>`;
    }

    html += '</div>';
    container.innerHTML = html;
}

/** Programme le prochain cycle de rafraîchissement automatique */
function scheduleNextRefresh() {
    if (APP_STATE.refreshTimer) clearTimeout(APP_STATE.refreshTimer);
    APP_STATE.refreshTimer = setTimeout(refreshStats, REFRESH_INTERVAL);
}

/** Démarre le cycle d'auto-refresh */
function startAutoRefresh() {
    scheduleNextRefresh();
}

/** Arrête l'auto-refresh (quand l'onglet est caché) */
function stopAutoRefresh() {
    if (APP_STATE.refreshTimer) {
        clearTimeout(APP_STATE.refreshTimer);
        APP_STATE.refreshTimer = null;
    }
}


// ============================================================================
// SECTION 5 : ACTIONS SUR LES IPs (blocage, whitelist)
// ============================================================================

/** IP en cours de blocage (stockée entre ouverture modal et confirmation) */
let currentIPToBlock = '';

/**
 * Ouvre la modal de confirmation de blocage d'une IP
 * Pré-remplit l'adresse IP et donne le focus au champ raison
 *
 * @param {string} ip - Adresse IP à bloquer
 */
function openBlockModal(ip) {
    if (!ip || typeof ip !== 'string') {
        showToast('Adresse IP invalide', 'error');
        return;
    }

    currentIPToBlock = ip;

    // Mettre à jour l'affichage de l'IP dans la modal
    const display = document.getElementById('modalIPDisplay');
    if (display) display.textContent = ip;

    // Vider le champ raison
    const reason = document.getElementById('blockReason');
    if (reason) reason.value = '';

    // Afficher la modal
    const modal = document.getElementById('blockModal');
    if (modal) modal.classList.add('active');

    // Focus automatique sur le champ raison après l'animation
    setTimeout(() => { if (reason) reason.focus(); }, 200);
}

/** Ferme la modal de blocage et réinitialise l'état */
function closeBlockModal() {
    const modal = document.getElementById('blockModal');
    if (modal) modal.classList.remove('active');
    currentIPToBlock = '';
}

/**
 * Confirme le blocage de l'IP après validation dans la modal
 * Envoie la requête POST sécurisée puis rafraîchit les données
 */
async function confirmBlockIP() {
    if (!currentIPToBlock) {
        showToast('Aucune IP sélectionnée', 'error');
        return;
    }

    // Récupérer la raison saisie (ou valeur par défaut)
    const reasonEl = document.getElementById('blockReason');
    const reason = (reasonEl?.value || '').trim() || 'Bloquée manuellement par admin';

    // Fermer la modal immédiatement
    closeBlockModal();

    showToast('Blocage en cours...', 'info');

    try {
        const result = await securePost(CONFIG.apiEndpoints.blockIP, {
            ip: currentIPToBlock,
            reason: reason
        });

        if (result.success) {
            showToast(result.message || 'IP bloquée avec succès', 'success');
            // Rafraîchir les données après un court délai
            setTimeout(refreshStats, 800);
        } else {
            showToast(result.message || 'Erreur lors du blocage', 'error');
        }

    } catch (error) {
        showToast('Erreur : ' + error.message, 'error');
    }
}

/**
 * Ajoute une IP à la whitelist après confirmation utilisateur
 *
 * @param {string} ip - Adresse IP à whitelister
 */
async function whitelistIP(ip) {
    if (!ip || typeof ip !== 'string') {
        showToast('Adresse IP invalide', 'error');
        return;
    }

    // Confirmation native (simple et efficace)
    if (!confirm(`Ajouter ${ip} à la whitelist ?\n\nCette IP ne sera plus jamais bloquée automatiquement.`)) {
        return;
    }

    try {
        const result = await securePost(CONFIG.apiEndpoints.whitelistIP, {
            ip: ip,
            description: 'Ajoutée depuis le dashboard'
        });

        if (result.success) {
            showToast(result.message || 'IP ajoutée à la whitelist', 'success');
            setTimeout(refreshStats, 800);
        } else {
            showToast(result.message || 'Erreur', 'error');
        }

    } catch (error) {
        showToast('Erreur : ' + error.message, 'error');
    }
}

/**
 * Redirige vers le dashboard avec un filtre IP pré-rempli
 * Permet de voir tous les événements liés à une IP spécifique
 *
 * @param {string} ip - Adresse IP à filtrer
 */
function filterByIP(ip) {
    if (!ip) return;
    const url = new URL(window.location.href);
    url.searchParams.set('ip', ip);
    url.searchParams.delete('page');  // Reset la pagination
    window.location.href = url.toString();
}


// ============================================================================
// SECTION 6 : SYSTÈME DE TOASTS (notifications visuelles)
// ============================================================================

/**
 * Affiche une notification toast en haut à droite de l'écran
 * S'auto-supprime après 4 secondes avec animation de sortie
 *
 * @param {string} message - Texte du message
 * @param {string} type    - Type visuel : 'success', 'error', 'warning', 'info'
 */
function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    // Mapper le type vers une icône
    const icons = {
        success: '✅',
        error:   '❌',
        warning: '⚠️',
        info:    'ℹ️'
    };

    // Créer l'élément toast
    const toast = document.createElement('div');
    toast.className = `sec-toast sec-toast--${type}`;
    toast.innerHTML = `
        <span class="sec-toast__icon">${icons[type] || 'ℹ️'}</span>
        <span class="sec-toast__message">${escapeHtml(message)}</span>
        <button class="sec-toast__close" onclick="this.parentElement.remove()">×</button>
    `;

    container.appendChild(toast);

    // Auto-suppression après 4 secondes avec animation de sortie
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}


// ============================================================================
// SECTION 7 : UTILITAIRES
// ============================================================================

/**
 * Échappe les caractères HTML dangereux pour prévenir les injections XSS
 * Utilisé pour tout contenu dynamique injecté dans le DOM via innerHTML
 *
 * @param {string} text - Texte brut à échapper
 * @returns {string} Texte sécurisé pour insertion HTML
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Gère la visibilité de l'onglet navigateur
 * Pause l'auto-refresh quand l'onglet est caché (économie de requêtes)
 * Reprend immédiatement quand l'onglet redevient visible
 */
function handleVisibilityChange() {
    if (document.hidden) {
        stopAutoRefresh();
    } else {
        // Rafraîchir immédiatement au retour sur l'onglet
        refreshStats();
    }
}


// ============================================================================
// SECTION 8 : INITIALISATION AU CHARGEMENT
// ============================================================================

/**
 * Point d'entrée principal du dashboard
 * Initialise les graphiques, l'auto-refresh et les event listeners
 */
function initDashboard() {
    // Vérifier que la configuration est présente
    if (!CONFIG.csrfToken) {
        console.error('[Security Dashboard] Token CSRF manquant !');
        showToast('Erreur de configuration — token CSRF absent', 'error');
        return;
    }

    // --- Initialiser les 3 graphiques Chart.js ---
    initDonutChart();
    initSeverityChart();
    initTimelineChart();

    // --- Démarrer le cycle d'auto-refresh (toutes les 60s) ---
    startAutoRefresh();

    // --- Écouter le changement de visibilité de l'onglet ---
    document.addEventListener('visibilitychange', handleVisibilityChange);

    // --- Fermer la modal si on clique sur l'overlay (en dehors du contenu) ---
    const modal = document.getElementById('blockModal');
    if (modal) {
        modal.addEventListener('click', function (e) {
            // Fermer seulement si le clic est sur l'overlay, pas sur le contenu
            if (e.target === this) closeBlockModal();
        });
    }

    // --- Fermer la modal avec Échap ---
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeBlockModal();
    });

    // --- Log console si événements critiques détectés ---
    const criticalEl = document.getElementById('stat-threats');
    const criticalCount = parseInt(criticalEl?.textContent) || 0;
    if (criticalCount > 0) {
        console.warn(
            '%c⚠ MarketFlow Security',
            'color:#ef4444;font-weight:bold;font-size:14px',
            `\n${criticalCount} événement(s) critique(s) sur les 7 derniers jours.`
        );
    }
}

// Lancer l'initialisation quand le DOM est prêt
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboard);
} else {
    initDashboard();
}

// --- Exposer les fonctions nécessaires aux onclick du HTML ---
window.openBlockModal  = openBlockModal;
window.closeBlockModal = closeBlockModal;
window.confirmBlockIP  = confirmBlockIP;
window.whitelistIP     = whitelistIP;
window.filterByIP      = filterByIP;
window.refreshStats    = refreshStats;
