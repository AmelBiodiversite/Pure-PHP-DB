/**
 * ============================================================================
 * MARKETFLOW PRO - SECURITY DASHBOARD JAVASCRIPT v3.0
 * ============================================================================
 *
 * 🔐 FONCTIONNALITÉS SÉCURISÉES :
 *   ✅ Protection CSRF sur toutes les requêtes AJAX
 *   ✅ Auto-refresh intelligent sans reload
 *   ✅ Validation des réponses API
 *   ✅ Gestion d'erreurs robuste
 *   ✅ Rate limiting des actions sensibles
 *   ✅ Système de toast pour feedback utilisateur
 *
 * @file public/js/security-dashboard.js
 * @version 3.0
 * @requires Chart.js 4.x
 */

'use strict'; // Mode strict pour éviter les erreurs silencieuses

// ============================================================================
// SECTION 1 : CONFIGURATION ET CONSTANTES
// ============================================================================

/**
 * Configuration globale (définie dans security-dashboard.php)
 * Contient le token CSRF et les URLs d'API
 */
const CONFIG = window.MARKETFLOW_CONFIG || {
    csrfToken: '',
    apiEndpoints: {}
};

/**
 * Constantes pour l'auto-refresh
 */
const REFRESH_INTERVAL = 60000; // 60 secondes (moins agressif que 30s)
const MAX_RETRIES = 3; // Nombre maximum de tentatives en cas d'erreur

/**
 * État global de l'application
 */
const APP_STATE = {
    isRefreshing: false, // Empêche les requêtes multiples simultanées
    retryCount: 0,
    lastRefresh: Date.now(),
    refreshTimer: null
};

// ============================================================================
// SECTION 2 : UTILITAIRES HTTP (avec protection CSRF)
// ============================================================================

/**
 * Effectue une requête AJAX sécurisée avec token CSRF
 * 
 * @param {string} url - URL de l'endpoint
 * @param {object} options - Options fetch (method, body, etc.)
 * @returns {Promise<object>} Réponse JSON parsée
 * @throws {Error} Si la requête échoue ou si le CSRF est invalide
 */
async function secureFetch(url, options = {}) {
    // Validation de l'URL
    if (!url || typeof url !== 'string') {
        throw new Error('URL invalide');
    }

    // Vérifier que le token CSRF existe
    if (!CONFIG.csrfToken) {
        throw new Error('Token CSRF manquant');
    }

    // Configuration par défaut
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest', // Permet au serveur de détecter AJAX
            'X-CSRF-Token': CONFIG.csrfToken // ✅ Token CSRF dans le header
        },
        credentials: 'same-origin' // Envoyer les cookies de session
    };

    // Fusionner les options
    const finalOptions = {
        ...defaultOptions,
        ...options,
        headers: {
            ...defaultOptions.headers,
            ...(options.headers || {})
        }
    };

    try {
        // Effectuer la requête
        const response = await fetch(url, finalOptions);

        // Vérifier le statut HTTP
        if (!response.ok) {
            // Cas spéciaux
            if (response.status === 403) {
                throw new Error('Token CSRF invalide ou session expirée');
            }
            if (response.status === 429) {
                throw new Error('Trop de requêtes, veuillez patienter');
            }
            if (response.status >= 500) {
                throw new Error('Erreur serveur, réessayez plus tard');
            }
            throw new Error(`Erreur HTTP ${response.status}`);
        }

        // Parser la réponse JSON
        const data = await response.json();

        // Valider la structure de la réponse
        if (typeof data !== 'object' || data === null) {
            throw new Error('Réponse invalide du serveur');
        }

        return data;

    } catch (error) {
        // Logger l'erreur pour debugging
        console.error('[SecureFetch] Erreur:', error);
        throw error;
    }
}

/**
 * Effectue une requête POST sécurisée
 * 
 * @param {string} url - URL de l'endpoint
 * @param {object} data - Données à envoyer
 * @returns {Promise<object>} Réponse JSON
 */
async function securePost(url, data = {}) {
    // Ajouter le token CSRF aux données
    const bodyData = {
        ...data,
        csrf_token: CONFIG.csrfToken // ✅ Token CSRF dans le body
    };

    // Encoder les données en x-www-form-urlencoded
    const body = Object.entries(bodyData)
        .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
        .join('&');

    return secureFetch(url, {
        method: 'POST',
        body: body
    });
}

// ============================================================================
// SECTION 3 : AUTO-REFRESH INTELLIGENT (AJAX)
// ============================================================================

/**
 * Rafraîchit les statistiques sans recharger la page
 * Utilise l'API AJAX pour récupérer les nouvelles données
 */
async function refreshStats() {
    // Éviter les appels multiples simultanés
    if (APP_STATE.isRefreshing) {
        console.log('[Refresh] Déjà en cours, skip');
        return;
    }

    // Vérifier que l'onglet est visible
    if (document.hidden) {
        console.log('[Refresh] Onglet caché, skip');
        scheduleNextRefresh();
        return;
    }

    APP_STATE.isRefreshing = true;

    try {
        console.log('[Refresh] Démarrage...');

        // Récupérer les nouvelles statistiques
        const stats = await secureFetch(CONFIG.apiEndpoints.stats + '?days=7');

        // Valider les données reçues
        if (!stats || typeof stats !== 'object') {
            throw new Error('Données invalides');
        }

        // Mettre à jour les cartes de stats
        updateStatsCards(stats);

        // Récupérer les IPs suspectes
        const ips = await secureFetch(CONFIG.apiEndpoints.suspiciousIPs + '?limit=10&days=7');
        
        if (Array.isArray(ips)) {
            updateSuspiciousIPs(ips);
        }

        // Mettre à jour l'horloge
        document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString('fr-FR');

        // Réinitialiser le compteur d'erreurs
        APP_STATE.retryCount = 0;
        APP_STATE.lastRefresh = Date.now();

        console.log('[Refresh] Succès');

    } catch (error) {
        console.error('[Refresh] Erreur:', error);
        
        // Incrémenter le compteur d'erreurs
        APP_STATE.retryCount++;

        // Afficher un toast d'erreur seulement après plusieurs échecs
        if (APP_STATE.retryCount >= MAX_RETRIES) {
            showToast('Erreur de rafraîchissement des données', 'error');
            APP_STATE.retryCount = 0; // Reset pour éviter spam
        }
    } finally {
        APP_STATE.isRefreshing = false;
        // Programmer le prochain refresh
        scheduleNextRefresh();
    }
}

/**
 * Met à jour les cartes de statistiques
 * 
 * @param {object} stats - Nouvelles statistiques
 */
function updateStatsCards(stats) {
    // Calculer les totaux
    let totalEvents = 0;
    let criticalEvents = 0;
    let warningEvents = 0;
    let infoEvents = 0;

    // Types critiques
    const criticalTypes = ['CSRF_VIOLATION', 'XSS_ATTEMPT', 'SQLI_ATTEMPT', 'UNAUTHORIZED_ACCESS'];
    // Types warning
    const warningTypes = ['LOGIN_FAILED', 'LOGIN_BLOCKED', 'RATE_LIMIT_EXCEEDED'];

    // Parcourir les stats
    for (const [type, count] of Object.entries(stats)) {
        const num = parseInt(count) || 0;
        totalEvents += num;

        if (criticalTypes.includes(type)) {
            criticalEvents += num;
        } else if (warningTypes.includes(type)) {
            warningEvents += num;
        } else {
            infoEvents += num;
        }
    }

    // Mettre à jour le DOM avec animation
    animateCounter('stat-total', totalEvents);
    animateCounter('stat-critical', criticalEvents);
    animateCounter('stat-warning', warningEvents);
    animateCounter('stat-info', infoEvents);
}

/**
 * Anime un compteur de nombre
 * 
 * @param {string} elementId - ID de l'élément à animer
 * @param {number} targetValue - Valeur cible
 */
function animateCounter(elementId, targetValue) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const currentValue = parseInt(element.textContent) || 0;
    const diff = targetValue - currentValue;

    // Pas d'animation si pas de changement
    if (diff === 0) return;

    // Animation simple
    const duration = 500; // 500ms
    const steps = 20;
    const stepValue = diff / steps;
    const stepDuration = duration / steps;

    let step = 0;
    const interval = setInterval(() => {
        step++;
        const newValue = Math.round(currentValue + (stepValue * step));
        element.textContent = newValue;

        if (step >= steps) {
            clearInterval(interval);
            element.textContent = targetValue; // Valeur finale exacte
        }
    }, stepDuration);
}

/**
 * Met à jour la liste des IPs suspectes
 * 
 * @param {Array} ips - Liste des IPs suspectes
 */
function updateSuspiciousIPs(ips) {
    const container = document.getElementById('suspicious-ips-container');
    if (!container) return;

    // Si pas d'IPs, afficher un message
    if (!Array.isArray(ips) || ips.length === 0) {
        container.innerHTML = '<p style="color:var(--text-secondary); font-size:13px;">Aucune IP suspecte détectée</p>';
        return;
    }

    // Générer le HTML
    let html = '';
    for (const ipData of ips) {
        // Valider les données
        const ip = String(ipData.ip || '').trim();
        const totalCount = parseInt(ipData.total_events) || 0;
        const criticalCnt = parseInt(ipData.critical_events) || 0;
        const severityScore = parseInt(ipData.severity_score) || 0;

        // Échapper l'IP pour éviter XSS (double sécurité)
        const escapedIP = escapeHtml(ip);

        html += `
            <div style="border-bottom:1px solid var(--border); padding:10px 0;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <code style="color:var(--orange); font-family:var(--font-mono);">${escapedIP}</code>
                    <span style="color:var(--red); font-weight:600;">${totalCount} events</span>
                </div>
                <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">
                    Critiques: ${criticalCnt} | Score: ${severityScore}
                </div>
                <div style="display:flex; gap:8px; margin-top:8px;">
                    <button onclick="filterByIP('${escapedIP}')" style="font-size:11px; padding:4px 8px; background:var(--blue-dim); border:1px solid var(--blue); color:var(--blue); border-radius:4px; cursor:pointer;">
                        Filtrer
                    </button>
                    <button onclick="openBlockModal('${escapedIP}')" style="font-size:11px; padding:4px 8px; background:var(--red-dim); border:1px solid var(--red); color:var(--red); border-radius:4px; cursor:pointer;">
                        Bloquer
                    </button>
                </div>
            </div>
        `;
    }

    container.innerHTML = html;
}

/**
 * Programme le prochain rafraîchissement
 */
function scheduleNextRefresh() {
    // Annuler le timer existant
    if (APP_STATE.refreshTimer) {
        clearTimeout(APP_STATE.refreshTimer);
    }

    // Programmer le prochain refresh
    APP_STATE.refreshTimer = setTimeout(() => {
        refreshStats();
    }, REFRESH_INTERVAL);
}

/**
 * Démarre l'auto-refresh
 */
function startAutoRefresh() {
    console.log('[AutoRefresh] Démarré (intervalle: ' + (REFRESH_INTERVAL / 1000) + 's)');
    scheduleNextRefresh();
}

/**
 * Arrête l'auto-refresh
 */
function stopAutoRefresh() {
    if (APP_STATE.refreshTimer) {
        clearTimeout(APP_STATE.refreshTimer);
        APP_STATE.refreshTimer = null;
        console.log('[AutoRefresh] Arrêté');
    }
}

// ============================================================================
// SECTION 4 : ACTIONS SUR LES IPs (blocage, whitelist)
// ============================================================================

/**
 * Variable globale pour stocker l'IP à bloquer
 */
let currentIPToBlock = '';

/**
 * Ouvre la modal de confirmation de blocage
 * 
 * @param {string} ip - Adresse IP à bloquer
 */
function openBlockModal(ip) {
    // Valider l'IP
    if (!ip || typeof ip !== 'string') {
        showToast('IP invalide', 'error');
        return;
    }

    currentIPToBlock = ip;
    document.getElementById('modalIPDisplay').textContent = ip;
    document.getElementById('blockReason').value = '';
    document.getElementById('blockModal').classList.add('open');

    // Focus sur le champ raison
    setTimeout(() => {
        document.getElementById('blockReason').focus();
    }, 100);
}

/**
 * Ferme la modal de confirmation
 */
function closeBlockModal() {
    document.getElementById('blockModal').classList.remove('open');
    currentIPToBlock = '';
}

/**
 * Confirme et exécute le blocage de l'IP
 */
async function confirmBlockIP() {
    if (!currentIPToBlock) {
        showToast('Aucune IP sélectionnée', 'error');
        return;
    }

    // Récupérer la raison
    const reason = document.getElementById('blockReason').value.trim() 
                || 'Bloquée manuellement par admin';

    // Fermer la modal
    closeBlockModal();

    // Afficher un toast de chargement
    showToast('Blocage en cours...', 'info');

    try {
        // Effectuer le blocage via API
        const result = await securePost(CONFIG.apiEndpoints.blockIP, {
            ip: currentIPToBlock,
            reason: reason
        });

        // Vérifier la réponse
        if (result.success) {
            showToast(result.message || 'IP bloquée avec succès', 'success');
            
            // Rafraîchir les données après 1 seconde
            setTimeout(() => {
                refreshStats();
            }, 1000);
        } else {
            showToast(result.message || 'Erreur lors du blocage', 'error');
        }

    } catch (error) {
        console.error('[BlockIP] Erreur:', error);
        showToast('Erreur: ' + error.message, 'error');
    }
}

/**
 * Ajoute une IP à la whitelist
 * 
 * @param {string} ip - Adresse IP à whitelister
 */
async function whitelistIP(ip) {
    // Validation
    if (!ip || typeof ip !== 'string') {
        showToast('IP invalide', 'error');
        return;
    }

    // Confirmation
    if (!confirm(`Ajouter ${ip} à la whitelist ?\n\nCette IP ne sera plus jamais bloquée automatiquement.`)) {
        return;
    }

    try {
        // Effectuer l'ajout via API
        const result = await securePost(CONFIG.apiEndpoints.whitelistIP, {
            ip: ip,
            description: 'Ajoutée manuellement depuis le dashboard'
        });

        if (result.success) {
            showToast(result.message || 'IP ajoutée à la whitelist', 'success');
            
            // Rafraîchir après 1 seconde
            setTimeout(() => {
                refreshStats();
            }, 1000);
        } else {
            showToast(result.message || 'Erreur lors de l\'ajout', 'error');
        }

    } catch (error) {
        console.error('[WhitelistIP] Erreur:', error);
        showToast('Erreur: ' + error.message, 'error');
    }
}

/**
 * Filtre les événements par IP
 * 
 * @param {string} ip - Adresse IP à filtrer
 */
function filterByIP(ip) {
    // Valider l'IP
    if (!ip || typeof ip !== 'string') {
        showToast('IP invalide', 'error');
        return;
    }

    // Rediriger vers la page avec le filtre IP
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('ip', ip);
    currentUrl.searchParams.delete('search'); // Supprimer le filtre search
    window.location.href = currentUrl.toString();
}

// ============================================================================
// SECTION 5 : SYSTÈME DE TOAST (notifications)
// ============================================================================

/**
 * Affiche une notification toast
 * 
 * @param {string} message - Message à afficher
 * @param {string} type - Type: 'success', 'error', 'info'
 */
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    // Créer l'élément toast
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    // Icône selon le type
    const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';
    
    toast.innerHTML = `
        <span>${icon}</span>
        <span>${escapeHtml(message)}</span>
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

// ============================================================================
// SECTION 6 : UTILITAIRES
// ============================================================================

/**
 * Échappe les caractères HTML pour éviter XSS
 * 
 * @param {string} text - Texte à échapper
 * @returns {string} Texte échappé
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Gère le changement de visibilité de l'onglet
 * Pause/Resume l'auto-refresh selon la visibilité
 */
function handleVisibilityChange() {
    if (document.hidden) {
        console.log('[Visibility] Onglet caché, pause refresh');
        stopAutoRefresh();
    } else {
        console.log('[Visibility] Onglet visible, resume refresh');
        // Rafraîchir immédiatement
        refreshStats();
    }
}

// ============================================================================
// SECTION 7 : INITIALISATION
// ============================================================================

/**
 * Initialise le dashboard au chargement de la page
 */
function initDashboard() {
    console.log('[Init] Initialisation du dashboard de sécurité');

    // Vérifier que la configuration existe
    if (!CONFIG.csrfToken) {
        console.error('[Init] Token CSRF manquant!');
        showToast('Erreur de configuration (CSRF manquant)', 'error');
        return;
    }

    // Démarrer l'auto-refresh
    startAutoRefresh();

    // Écouter le changement de visibilité de l'onglet
    document.addEventListener('visibilitychange', handleVisibilityChange);

    // Fermer la modal en cliquant sur l'overlay
    document.getElementById('blockModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeBlockModal();
        }
    });

    // Avertissement console si événements critiques
    const criticalCount = parseInt(document.getElementById('stat-critical')?.textContent) || 0;
    if (criticalCount > 0) {
        console.warn(
            '%c⚠ MarketFlow Security',
            'color:#ff3b3b;font-weight:bold;font-size:14px',
            `\n${criticalCount} événement(s) critique(s) détecté(s) sur les 7 derniers jours.`
        );
    }

    console.log('[Init] Dashboard initialisé avec succès');
}

// Initialiser au chargement du DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboard);
} else {
    // DOM déjà chargé
    initDashboard();
}

// Exposer les fonctions globalement (nécessaire pour onclick dans le HTML)
window.openBlockModal = openBlockModal;
window.closeBlockModal = closeBlockModal;
window.confirmBlockIP = confirmBlockIP;
window.whitelistIP = whitelistIP;
window.filterByIP = filterByIP;

