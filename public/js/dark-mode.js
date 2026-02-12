/**
 * ================================================================
 * MARKETFLOW PRO - MODE SOMBRE JAVASCRIPT v1.0
 * ================================================================
 * Gestion du toggle avec sauvegarde localStorage
 * Fichier : public/js/dark-mode.js
 * ================================================================
 */

// ================================================================
// INITIALISATION MODE SOMBRE
// ================================================================

/**
 * Initialiser le mode sombre au chargement de la page
 * - Charge la préférence sauvegardée
 * - Détecte la préférence système si aucune sauvegarde
 * - Met à jour l'icône du bouton
 */
function initDarkMode() {
    const savedTheme = localStorage.getItem('theme');
    
    // Appliquer le thème sauvegardé ou détecter la préférence système
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
    } else if (savedTheme === 'light') {
        document.body.classList.remove('dark-mode');
    } else {
        // Pas de préférence : détecter la préférence système
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (prefersDark) {
            document.body.classList.add('dark-mode');
        }
    }
    
    // Mettre à jour l'icône
    updateIcon();
    
    console.log('🌓 Mode sombre initialisé');
}

/**
 * Basculer entre mode clair et mode sombre
 */
function toggleDarkMode() {
    const btn = document.getElementById('dark-mode-toggle');
    
    // Ajouter animation de rotation
    if (btn) {
        btn.classList.add('rotating');
        setTimeout(() => btn.classList.remove('rotating'), 500);
    }
    
    // Basculer le mode
    document.body.classList.toggle('dark-mode');
    
    // Sauvegarder la préférence
    if (document.body.classList.contains('dark-mode')) {
        localStorage.setItem('theme', 'dark');
        console.log('🌙 Mode sombre activé');
    } else {
        localStorage.setItem('theme', 'light');
        console.log('☀️ Mode clair activé');
    }
    
    // Mettre à jour l'icône
    updateIcon();
}

/**
 * Mettre à jour l'icône soleil/lune selon le mode actuel
 */
function updateIcon() {
    const isDark = document.body.classList.contains('dark-mode');
    const sunIcon = document.getElementById('sun-icon');
    const moonIcon = document.getElementById('moon-icon');
    
    if (!sunIcon || !moonIcon) return;
    
    if (isDark) {
        // Mode sombre : afficher le soleil (pour revenir au clair)
        sunIcon.style.opacity = '1';
        sunIcon.style.transform = 'rotate(0deg) scale(1)';
        moonIcon.style.opacity = '0';
        moonIcon.style.transform = 'rotate(-180deg) scale(0.5)';
    } else {
        // Mode clair : afficher la lune (pour passer au sombre)
        sunIcon.style.opacity = '0';
        sunIcon.style.transform = 'rotate(180deg) scale(0.5)';
        moonIcon.style.opacity = '1';
        moonIcon.style.transform = 'rotate(0deg) scale(1)';
    }
}

/**
 * Écouter les changements de préférence système (optionnel)
 */
function watchSystemPreference() {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        // Uniquement si l'utilisateur n'a pas de préférence manuelle
        if (!localStorage.getItem('theme')) {
            if (e.matches) {
                document.body.classList.add('dark-mode');
            } else {
                document.body.classList.remove('dark-mode');
            }
            updateIcon();
        }
    });
}

// ================================================================
// INITIALISATION AU CHARGEMENT
// ================================================================
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le mode sombre
    initDarkMode();
    
    // Écouter les changements système
    watchSystemPreference();
    
    // Attacher l'événement au bouton
    const toggleBtn = document.getElementById('dark-mode-toggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleDarkMode);
        console.log('✅ Bouton mode sombre initialisé');
    }
});
