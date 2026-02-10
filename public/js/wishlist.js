/**
 * ================================================
 * MARKETFLOW PRO - SYSTÈME WISHLIST (FAVORIS)
 * ================================================
 * 
 * Fichier : public/js/wishlist.js
 * Version : 1.1 (Corrigée)
 * Date : 10 février 2026
 * 
 * DESCRIPTION :
 * Gère toutes les interactions avec la wishlist (favoris) :
 * - Ajout/suppression de produits
 * - Animations des boutons cœur
 * - Mise à jour du compteur dans le header
 * - Notifications toast
 * 
 * DÉPENDANCES :
 * - public/js/notifications.js (système de notifications)
 * 
 * UTILISATION :
 * Inclure ce fichier dans le header :
 * <script src="/public/js/wishlist.js"></script>
 * 
 * CORRECTIONS v1.1 :
 * ✅ Variable 'style' déplacée dans l'IIFE (évite conflit global)
 * ✅ Injection des styles via fonction injectStyles()
 * ✅ Meilleure isolation du code (scope propre)
 * 
 * ================================================
 */

(function() {
    'use strict';

    /**
     * ============================================
     * INITIALISATION AU CHARGEMENT DE LA PAGE
     * ============================================
     * 
     * Cette fonction s'exécute quand le DOM est prêt.
     * Elle initialise tous les composants de la wishlist.
     */
    document.addEventListener('DOMContentLoaded', function() {
        injectStyles();          // ✅ Injecter les styles CSS
        initWishlistButtons();   // ✅ Initialiser les boutons
        updateWishlistCount();   // ✅ Mettre à jour le compteur
    });

    /**
     * ============================================
     * INJECTER LES STYLES CSS DYNAMIQUEMENT
     * ============================================
     * 
     * Cette fonction crée une balise <style> et l'ajoute au <head>.
     * Permet d'éviter un fichier CSS séparé pour ces animations.
     * 
     * POURQUOI ICI ?
     * - Garde tout le code wishlist dans un seul fichier
     * - Évite les conflits de noms dans le scope global
     * - Facilite la maintenance
     */
    function injectStyles() {
        // Vérifier si les styles ne sont pas déjà injectés
        if (document.getElementById('wishlist-styles')) {
            return; // Déjà injecté, on sort
        }

        const styleElement = document.createElement('style');
        styleElement.id = 'wishlist-styles'; // ID unique pour éviter les doublons
        styleElement.textContent = `
            /* ========================================
               ANIMATIONS WISHLIST
               ======================================== */

            /* Animation battement de cœur (ajout aux favoris) */
            @keyframes heartbeat {
                0%, 100% { 
                    transform: scale(1); 
                }
                25% { 
                    transform: scale(1.3); 
                }
                50% { 
                    transform: scale(1.1); 
                }
                75% { 
                    transform: scale(1.2); 
                }
            }

            /* Animation cœur qui se brise (suppression des favoris) */
            @keyframes heartbreak {
                0% { 
                    transform: scale(1); 
                }
                50% { 
                    transform: scale(0.8) rotate(-10deg); 
                }
                100% { 
                    transform: scale(1) rotate(0deg); 
                }
            }

            /* Animation rebond du compteur (badge) */
            @keyframes bounce {
                0%, 100% { 
                    transform: scale(1); 
                }
                50% { 
                    transform: scale(1.2); 
                }
            }

            /* Animation pulse pour les favoris actifs */
            @keyframes pulse {
                0%, 100% { 
                    opacity: 1; 
                }
                50% { 
                    opacity: 0.7; 
                }
            }

            /* ========================================
               STYLES DES BOUTONS WISHLIST
               ======================================== */

            .btn-wishlist {
                background: none;
                border: none;
                font-size: 1.5rem;
                cursor: pointer;
                padding: 0.5rem;
                transition: all 0.3s ease;
                line-height: 1;
                position: relative;
                outline: none;
            }

            /* État hover (survol) */
            .btn-wishlist:hover {
                transform: scale(1.15);
            }

            /* État active (clic) */
            .btn-wishlist:active {
                transform: scale(0.95);
            }

            /* État disabled (pendant requête AJAX) */
            .btn-wishlist:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }

            /* Produit déjà en favoris (pulse subtil) */
            .btn-wishlist.in-wishlist {
                animation: pulse 2s infinite;
            }

            /* Focus pour accessibilité */
            .btn-wishlist:focus {
                outline: 2px solid var(--primary-color, #FF6B6B);
                outline-offset: 2px;
                border-radius: 4px;
            }
        `;
        
        // Ajouter la balise <style> au <head>
        document.head.appendChild(styleElement);
    }

    /**
     * ============================================
     * INITIALISER LES BOUTONS WISHLIST
     * ============================================
     * 
     * Cherche tous les boutons avec la classe .btn-wishlist
     * et leur attache un gestionnaire d'événements.
     * 
     * STRUCTURE HTML ATTENDUE :
     * <button class="btn-wishlist" data-product-id="123">
     *     🤍 ou ❤️
     * </button>
     */
    function initWishlistButtons() {
        const wishlistButtons = document.querySelectorAll('.btn-wishlist');
        
        if (wishlistButtons.length === 0) {
            console.log('ℹ️ Aucun bouton wishlist trouvé sur cette page');
            return;
        }

        wishlistButtons.forEach(button => {
            // Ajouter l'événement de clic
            button.addEventListener('click', handleWishlistClick);
            
            // Ajouter l'accessibilité clavier (Entrée/Espace)
            button.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    handleWishlistClick.call(this, e);
                }
            });
        });

        console.log(`✅ ${wishlistButtons.length} bouton(s) wishlist initialisé(s)`);
    }

    /**
     * ============================================
     * GÉRER LE CLIC SUR UN BOUTON WISHLIST
     * ============================================
     * 
     * @param {Event} e - Événement de clic ou keydown
     * 
     * WORKFLOW :
     * 1. Vérifier si l'utilisateur est connecté
     * 2. Récupérer l'ID du produit
     * 3. Déterminer l'action (ajouter ou retirer)
     * 4. Appeler la fonction appropriée
     */
    function handleWishlistClick(e) {
        e.preventDefault();
        e.stopPropagation(); // Empêcher la propagation (important sur les cards)
        
        const button = e.currentTarget;
        const productId = button.dataset.productId;
        
        // Vérification 1 : Product ID présent ?
        if (!productId) {
            console.error('❌ Erreur : data-product-id manquant sur le bouton');
            if (window.showNotification) {
                window.showNotification('Erreur : ID produit manquant', 'error');
            }
            return;
        }
        
        // Vérification 2 : Utilisateur connecté ?
        if (!isUserLoggedIn()) {
            showLoginPrompt();
            return;
        }
        
        // Récupérer l'état actuel
        const isInWishlist = button.classList.contains('in-wishlist');
        
        // Désactiver le bouton pendant la requête AJAX
        button.disabled = true;
        button.style.pointerEvents = 'none';
        
        // Ajouter ou retirer selon l'état actuel
        if (isInWishlist) {
            removeFromWishlist(productId, button);
        } else {
            addToWishlist(productId, button);
        }
    }

    /**
     * ============================================
     * AJOUTER UN PRODUIT AUX FAVORIS
     * ============================================
     * 
     * @param {number} productId - ID du produit à ajouter
     * @param {HTMLElement} button - Bouton cliqué (pour mise à jour UI)
     * 
     * REQUÊTE AJAX : POST /wishlist/add
     * RÉPONSE ATTENDUE : { success: true, message: "...", count: X }
     */
    function addToWishlist(productId, button) {
        fetch('/wishlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + encodeURIComponent(productId)
        })
        .then(response => {
            // Vérifier le statut HTTP
            if (!response.ok) {
                throw new Error('Erreur HTTP ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // ✅ SUCCÈS : Mettre à jour l'interface
                
                // 1. Changer l'apparence du bouton
                button.classList.add('in-wishlist');
                button.innerHTML = '❤️'; // Cœur plein
                button.title = 'Retirer des favoris';
                button.setAttribute('aria-label', 'Retirer des favoris');
                
                // 2. Animation de pulsation
                animateHeartbeat(button);
                
                // 3. Mettre à jour le compteur dans le header
                if (data.count !== undefined) {
                    updateWishlistCountValue(data.count);
                }
                
                // 4. Afficher une notification de succès
                if (window.showNotification) {
                    window.showNotification(
                        data.message || 'Produit ajouté aux favoris ! ❤️', 
                        'success'
                    );
                }
            } else {
                // ❌ ÉCHEC : Afficher l'erreur
                if (window.showNotification) {
                    window.showNotification(
                        data.message || 'Erreur lors de l\'ajout', 
                        'error'
                    );
                }
            }
        })
        .catch(error => {
            // ❌ ERREUR RÉSEAU ou PARSING JSON
            console.error('❌ Erreur wishlist/add:', error);
            if (window.showNotification) {
                window.showNotification('Erreur de connexion au serveur', 'error');
            }
        })
        .finally(() => {
            // 🔓 Toujours réactiver le bouton
            button.disabled = false;
            button.style.pointerEvents = 'auto';
        });
    }

    /**
     * ============================================
     * RETIRER UN PRODUIT DES FAVORIS
     * ============================================
     * 
     * @param {number} productId - ID du produit à retirer
     * @param {HTMLElement} button - Bouton cliqué (pour mise à jour UI)
     * 
     * REQUÊTE AJAX : POST /wishlist/remove
     * RÉPONSE ATTENDUE : { success: true, message: "...", count: X }
     */
    function removeFromWishlist(productId, button) {
        fetch('/wishlist/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + encodeURIComponent(productId)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur HTTP ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // ✅ SUCCÈS : Mettre à jour l'interface
                
                // 1. Changer l'apparence du bouton
                button.classList.remove('in-wishlist');
                button.innerHTML = '🤍'; // Cœur vide
                button.title = 'Ajouter aux favoris';
                button.setAttribute('aria-label', 'Ajouter aux favoris');
                
                // 2. Animation de "cœur brisé"
                animateHeartBreak(button);
                
                // 3. Mettre à jour le compteur
                if (data.count !== undefined) {
                    updateWishlistCountValue(data.count);
                }
                
                // 4. Notification
                if (window.showNotification) {
                    window.showNotification(
                        data.message || 'Produit retiré des favoris', 
                        'info'
                    );
                }
            } else {
                // ❌ ÉCHEC
                if (window.showNotification) {
                    window.showNotification(
                        data.message || 'Erreur lors de la suppression', 
                        'error'
                    );
                }
            }
        })
        .catch(error => {
            console.error('❌ Erreur wishlist/remove:', error);
            if (window.showNotification) {
                window.showNotification('Erreur de connexion au serveur', 'error');
            }
        })
        .finally(() => {
            // 🔓 Réactiver le bouton
            button.disabled = false;
            button.style.pointerEvents = 'auto';
        });
    }

    /**
     * ============================================
     * ANIMATION CŒUR QUI BAT (Ajout aux favoris)
     * ============================================
     * 
     * @param {HTMLElement} button - Bouton à animer
     * 
     * Applique l'animation CSS 'heartbeat' définie plus haut.
     * Durée : 600ms
     */
    function animateHeartbeat(button) {
        button.style.animation = 'heartbeat 0.6s ease';
        
        // Retirer l'animation après son exécution
        setTimeout(() => {
            button.style.animation = '';
        }, 600);
    }

    /**
     * ============================================
     * ANIMATION CŒUR QUI SE BRISE (Suppression)
     * ============================================
     * 
     * @param {HTMLElement} button - Bouton à animer
     * 
     * Applique l'animation CSS 'heartbreak'.
     * Durée : 400ms
     */
    function animateHeartBreak(button) {
        button.style.animation = 'heartbreak 0.4s ease';
        
        setTimeout(() => {
            button.style.animation = '';
        }, 400);
    }

    /**
     * ============================================
     * METTRE À JOUR LE COMPTEUR WISHLIST (Badge)
     * ============================================
     * 
     * Récupère le nombre actuel de favoris via AJAX
     * et met à jour le badge dans le header.
     * 
     * REQUÊTE : GET /wishlist/count
     * RÉPONSE : { success: true, count: X }
     * 
     * Cette fonction est appelée au chargement de la page.
     */
    function updateWishlistCount() {
        // Ne faire la requête que si l'utilisateur est connecté
        if (!isUserLoggedIn()) {
            return;
        }

        fetch('/wishlist/count')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur HTTP ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.count !== undefined) {
                    updateWishlistCountValue(data.count);
                }
            })
            .catch(error => {
                // Erreur silencieuse (pas grave si le compteur ne se met pas à jour)
                console.warn('⚠️ Impossible de récupérer le compteur wishlist:', error);
            });
    }

    /**
     * ============================================
     * METTRE À JOUR LA VALEUR DU COMPTEUR (UI)
     * ============================================
     * 
     * @param {number} count - Nouveau nombre de favoris
     * 
     * Cherche le badge .wishlist-count dans le DOM
     * et met à jour sa valeur avec animation.
     * 
     * STRUCTURE HTML ATTENDUE :
     * <span class="wishlist-count">3</span>
     */
    function updateWishlistCountValue(count) {
        const badge = document.querySelector('.wishlist-count');
        
        if (!badge) {
            console.warn('⚠️ Badge .wishlist-count non trouvé dans le DOM');
            return;
        }

        // Mettre à jour le texte
        badge.textContent = count;
        
        // Afficher/masquer selon la valeur
        if (count > 0) {
            badge.style.display = 'inline-block';
            
            // Animation de rebond
            badge.style.animation = 'bounce 0.5s ease';
            setTimeout(() => {
                badge.style.animation = '';
            }, 500);
        } else {
            // Masquer si 0
            badge.style.display = 'none';
        }
    }

    /**
     * ============================================
     * VÉRIFIER SI L'UTILISATEUR EST CONNECTÉ
     * ============================================
     * 
     * @returns {boolean} TRUE si connecté, FALSE sinon
     * 
     * MÉTHODES DE DÉTECTION :
     * 1. Présence du menu utilisateur (.user-menu)
     * 2. Attribut data-user-logged-in sur <body>
     * 
     * Ajustez selon votre structure HTML.
     */
    function isUserLoggedIn() {
        // Méthode 1 : Vérifier si .user-menu existe
        if (document.querySelector('.user-menu')) {
            return true;
        }
        
        // Méthode 2 : Vérifier data-attribute sur body
        if (document.body.dataset.userLoggedIn === 'true') {
            return true;
        }
        
        // Méthode 3 : Vérifier si un élément avec classe .user-only existe
        if (document.querySelector('.user-only')) {
            return true;
        }
        
        return false;
    }

    /**
     * ============================================
     * AFFICHER UNE INVITE DE CONNEXION
     * ============================================
     * 
     * Appelée quand un utilisateur non-connecté
     * tente d'ajouter un produit aux favoris.
     * 
     * WORKFLOW :
     * 1. Afficher une notification
     * 2. Rediriger vers /login après 1.5 secondes
     * 3. Inclure l'URL actuelle comme paramètre redirect
     */
    function showLoginPrompt() {
        // Afficher une notification
        if (window.showNotification) {
            window.showNotification(
                'Connectez-vous pour ajouter des favoris ❤️', 
                'info'
            );
        } else {
            // Fallback si le système de notifications n'est pas disponible
            alert('Vous devez être connecté pour ajouter des favoris');
        }
        
        // Redirection vers la page de connexion
        // avec l'URL actuelle en paramètre pour revenir après login
        setTimeout(() => {
            const currentUrl = window.location.pathname + window.location.search;
            const redirectUrl = '/login?redirect=' + encodeURIComponent(currentUrl);
            window.location.href = redirectUrl;
        }, 1500); // 1.5 secondes pour laisser lire la notification
    }

})(); // ✅ FIN DE L'IIFE (Immediately Invoked Function Expression)

/**
 * ================================================
 * FIN DU FICHIER wishlist.js
 * ================================================
 * 
 * NOTES POUR LA MAINTENANCE :
 * 
 * ✅ STRUCTURE PROPRE :
 *    - Tout le code est dans une IIFE (pas de pollution globale)
 *    - Les styles CSS sont injectés dynamiquement
 *    - Aucune variable globale exposée
 * 
 * ✅ UTILISATION :
 *    1. Ajouter data-product-id="X" sur les boutons
 *    2. Utiliser la classe .btn-wishlist
 *    3. Ajouter .in-wishlist si déjà en favoris
 * 
 * ✅ EXEMPLE HTML :
 *    <button class="btn-wishlist" data-product-id="42">
 *        🤍
 *    </button>
 * 
 *    <!-- Après ajout aux favoris : -->
 *    <button class="btn-wishlist in-wishlist" data-product-id="42">
 *        ❤️
 *    </button>
 * 
 * ✅ DÉPENDANCES :
 *    - notifications.js (optionnel, fallback sur alert())
 *    - Backend endpoints : /wishlist/add, /wishlist/remove, /wishlist/count
 * 
 * ✅ PERSONNALISATION :
 *    - Modifier les emojis (❤️ / 🤍) lignes 234 et 296
 *    - Ajuster les animations CSS (lignes 68-119)
 *    - Changer les durées d'animation (lignes 372-385)
 *    - Modifier le délai de redirection (ligne 497)
 * 
 * ✅ ACCESSIBILITÉ :
 *    - Support clavier (Entrée/Espace)
 *    - Attributs aria-label
 *    - Focus visible
 * 
 * ✅ PERFORMANCES :
 *    - Injection CSS unique (vérification anti-doublon)
 *    - Désactivation des boutons pendant requêtes
 *    - Gestion d'erreurs robuste
 * 
 * ================================================
 */
