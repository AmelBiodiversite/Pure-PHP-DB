/**
 * =====================================================
 * MARKETFLOW PRO - SYSTÈME DE NOTIFICATIONS TOAST
 * =====================================================
 * 
 * Système de notifications modernes type "toast" avec animations
 * Compatible avec les messages flash PHP existants
 * 
 * Usage:
 *   NotificationSystem.show('Message', 'success');
 *   NotificationSystem.success('Opération réussie !');
 *   NotificationSystem.error('Une erreur est survenue');
 * 
 * Types disponibles: success, error, warning, info
 * 
 * @author MarketFlow Team
 * @version 1.0
 */

const NotificationSystem = (function() {
    'use strict';

    // =====================================================
    // CONFIGURATION
    // =====================================================
    const config = {
        duration: 5000,        // Durée d'affichage (ms)
        position: 'top-right', // Position: top-right, top-left, bottom-right, bottom-left
        maxNotifications: 3,   // Nombre max de notifications simultanées
        animation: 'slide'     // Type d'animation: slide, fade
    };

    // Container pour les notifications
    let container = null;

    // =====================================================
    // INITIALISATION
    // =====================================================
    function init() {
        // Créer le container s'il n'existe pas
        if (!container) {
            container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'notification-container ' + config.position;
            document.body.appendChild(container);
        }
    }

    // =====================================================
    // AFFICHER UNE NOTIFICATION
    // =====================================================
    /**
     * Affiche une notification toast
     * @param {string} message - Le message à afficher
     * @param {string} type - Type de notification (success, error, warning, info)
     * @param {number} duration - Durée d'affichage optionnelle
     */
    function show(message, type = 'info', duration = config.duration) {
        init();

        // Limiter le nombre de notifications
        const notifications = container.querySelectorAll('.notification-toast');
        if (notifications.length >= config.maxNotifications) {
            notifications[0].remove();
        }

        // Créer la notification
        const toast = createToast(message, type);
        container.appendChild(toast);

        // Animation d'entrée
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);

        // Auto-suppression
        const timeout = setTimeout(() => {
            removeToast(toast);
        }, duration);

        // Bouton de fermeture
        toast.querySelector('.notification-close').addEventListener('click', () => {
            clearTimeout(timeout);
            removeToast(toast);
        });

        return toast;
    }

    // =====================================================
    // CRÉER UNE NOTIFICATION
    // =====================================================
    /**
     * Crée l'élément HTML d'une notification
     * @param {string} message - Le message
     * @param {string} type - Le type (success, error, warning, info)
     * @returns {HTMLElement} L'élément toast
     */
    function createToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `notification-toast notification-${type}`;

        // Icônes selon le type
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };

        toast.innerHTML = `
            <div class="notification-icon">${icons[type] || icons.info}</div>
            <div class="notification-message">${escapeHtml(message)}</div>
            <button class="notification-close" aria-label="Fermer">×</button>
        `;

        return toast;
    }

    // =====================================================
    // SUPPRIMER UNE NOTIFICATION
    // =====================================================
    /**
     * Supprime une notification avec animation
     * @param {HTMLElement} toast - L'élément à supprimer
     */
    function removeToast(toast) {
        toast.classList.remove('show');
        toast.classList.add('hide');

        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 300);
    }

    // =====================================================
    // MÉTHODES RACCOURCIES
    // =====================================================
    /**
     * Notification de succès
     */
    function success(message, duration) {
        return show(message, 'success', duration);
    }

    /**
     * Notification d'erreur
     */
    function error(message, duration) {
        return show(message, 'error', duration);
    }

    /**
     * Notification d'avertissement
     */
    function warning(message, duration) {
        return show(message, 'warning', duration);
    }

    /**
     * Notification d'information
     */
    function info(message, duration) {
        return show(message, 'info', duration);
    }

    // =====================================================
    // UTILITAIRES
    // =====================================================
    /**
     * Échappe le HTML pour éviter les injections XSS
     * @param {string} text - Le texte à échapper
     * @returns {string} Texte échappé
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // =====================================================
    // API PUBLIQUE
    // =====================================================
    return {
        show,
        success,
        error,
        warning,
        info,
        config
    };
})();

// =====================================================
// AUTO-INITIALISATION
// Convertit les messages flash PHP en notifications toast
// =====================================================
document.addEventListener('DOMContentLoaded', function() {
    // Chercher les messages flash existants dans le header
    const flashMessage = document.querySelector('[data-flash-message]');
    
    if (flashMessage) {
        const message = flashMessage.dataset.flashMessage;
        const type = flashMessage.dataset.flashType || 'info';
        
        // Afficher comme toast
        NotificationSystem.show(message, type);
        
        // Cacher le message flash statique
        flashMessage.style.display = 'none';
    }
});

// =====================================================
// EXPORT GLOBAL
// =====================================================
window.NotificationSystem = NotificationSystem;