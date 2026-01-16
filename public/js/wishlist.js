/**
 * ================================================
 * MARKETFLOW PRO - SYSTÈME WISHLIST (FAVORIS)
 * ================================================
 * 
 * Fichier : public/js/wishlist.js
 * Version : 1.0
 * Date : 16 janvier 2025
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
 * ================================================
 */

(function() {
    'use strict';

    /**
     * ============================================
     * INITIALISATION AU CHARGEMENT DE LA PAGE
     * ============================================
     */
    document.addEventListener('DOMContentLoaded', function() {
        initWishlistButtons();
        updateWishlistCount();
    });

    /**
     * ============================================
     * INITIALISER LES BOUTONS WISHLIST
     * ============================================
     * 
     * Attache les événements de clic à tous les boutons wishlist
     * (boutons avec la classe .btn-wishlist)
     */
    function initWishlistButtons() {
        const wishlistButtons = document.querySelectorAll('.btn-wishlist');
        
        wishlistButtons.forEach(button => {
            button.addEventListener('click', handleWishlistClick);
        });
    }

    /**
     * ============================================
     * GÉRER LE CLIC SUR UN BOUTON WISHLIST
     * ============================================
     * 
     * @param {Event} e - Événement de clic
     */
    function handleWishlistClick(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const button = e.currentTarget;
        const productId = button.dataset.productId;
        const isInWishlist = button.classList.contains('in-wishlist');
        
        // Vérifier l'authentification
        if (!isUserLoggedIn()) {
            showLoginPrompt();
            return;
        }
        
        // Désactiver le bouton pendant la requête
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
     * @param {number} productId - ID du produit
     * @param {HTMLElement} button - Bouton cliqué
     */
    function addToWishlist(productId, button) {
        fetch('/wishlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mettre à jour l'apparence du bouton
                button.classList.add('in-wishlist');
                button.innerHTML = '❤️'; // Cœur plein
                button.title = 'Retirer des favoris';
                
                // Animation de pulsation
                animateHeartbeat(button);
                
                // Mettre à jour le compteur
                if (data.count !== undefined) {
                    updateWishlistCountValue(data.count);
                }
                
                // Notification de succès
                if (window.showNotification) {
                    window.showNotification(data.message || 'Produit ajouté aux favoris !', 'success');
                }
            } else {
                // Erreur
                if (window.showNotification) {
                    window.showNotification(data.message || 'Erreur lors de l\'ajout', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            if (window.showNotification) {
                window.showNotification('Erreur de connexion', 'error');
            }
        })
        .finally(() => {
            // Réactiver le bouton
            button.disabled = false;
            button.style.pointerEvents = 'auto';
        });
    }

    /**
     * ============================================
     * RETIRER UN PRODUIT DES FAVORIS
     * ============================================
     * 
     * @param {number} productId - ID du produit
     * @param {HTMLElement} button - Bouton cliqué
     */
    function removeFromWishlist(productId, button) {
        fetch('/wishlist/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mettre à jour l'apparence du bouton
                button.classList.remove('in-wishlist');
                button.innerHTML = '🤍'; // Cœur vide
                button.title = 'Ajouter aux favoris';
                
                // Animation de disparition
                animateHeartBreak(button);
                
                // Mettre à jour le compteur
                if (data.count !== undefined) {
                    updateWishlistCountValue(data.count);
                }
                
                // Notification
                if (window.showNotification) {
                    window.showNotification(data.message || 'Produit retiré des favoris', 'info');
                }
            } else {
                if (window.showNotification) {
                    window.showNotification(data.message || 'Erreur lors de la suppression', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            if (window.showNotification) {
                window.showNotification('Erreur de connexion', 'error');
            }
        })
        .finally(() => {
            // Réactiver le bouton
            button.disabled = false;
            button.style.pointerEvents = 'auto';
        });
    }

    /**
     * ============================================
     * ANIMATION CŒUR QUI BAT (Ajout)
     * ============================================
     * 
     * @param {HTMLElement} button - Bouton à animer
     */
    function animateHeartbeat(button) {
        button.style.animation = 'heartbeat 0.6s ease';
        
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
     */
    function animateHeartBreak(button) {
        button.style.animation = 'heartbreak 0.4s ease';
        
        setTimeout(() => {
            button.style.animation = '';
        }, 400);
    }

    /**
     * ============================================
     * METTRE À JOUR LE COMPTEUR WISHLIST
     * ============================================
     * 
     * Récupère le nombre actuel de favoris via AJAX
     * et met à jour le badge dans le header
     */
    function updateWishlistCount() {
        if (!isUserLoggedIn()) {
            return;
        }

        fetch('/wishlist/count')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.count !== undefined) {
                    updateWishlistCountValue(data.count);
                }
            })
            .catch(error => {
                console.error('Erreur lors de la récupération du compteur:', error);
            });
    }

    /**
     * ============================================
     * METTRE À JOUR LA VALEUR DU COMPTEUR
     * ============================================
     * 
     * @param {number} count - Nouveau nombre de favoris
     */
    function updateWishlistCountValue(count) {
        const badge = document.querySelector('.wishlist-count');
        
        if (badge) {
            badge.textContent = count;
            
            // Afficher/masquer le badge
            if (count > 0) {
                badge.style.display = 'inline-block';
                
                // Animation de mise à jour
                badge.style.animation = 'bounce 0.5s ease';
                setTimeout(() => {
                    badge.style.animation = '';
                }, 500);
            } else {
                badge.style.display = 'none';
            }
        }
    }

    /**
     * ============================================
     * VÉRIFIER SI L'UTILISATEUR EST CONNECTÉ
     * ============================================
     * 
     * @returns {boolean} TRUE si connecté, FALSE sinon
     */
    function isUserLoggedIn() {
        // Vérifier si un élément user-only est présent dans le DOM
        // (ajusté selon votre structure HTML)
        return document.querySelector('.user-menu') !== null || 
               document.body.dataset.userLoggedIn === 'true';
    }

    /**
     * ============================================
     * AFFICHER UNE INVITE DE CONNEXION
     * ============================================
     */
    function showLoginPrompt() {
        if (window.showNotification) {
            window.showNotification('Connectez-vous pour ajouter des favoris', 'info');
        } else {
            alert('Vous devez être connecté pour ajouter des favoris');
        }
        
        // Rediriger vers la page de connexion après 1 seconde
        setTimeout(() => {
            window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
        }, 1000);
    }

})();

/**
 * ================================================
 * ANIMATIONS CSS (À ajouter dans style.css ou ici)
 * ================================================
 */
const style = document.createElement('style');
style.textContent = `
    /* Animation battement de cœur (ajout) */
    @keyframes heartbeat {
        0%, 100% { transform: scale(1); }
        25% { transform: scale(1.3); }
        50% { transform: scale(1.1); }
        75% { transform: scale(1.2); }
    }

    /* Animation cœur qui se brise (suppression) */
    @keyframes heartbreak {
        0% { transform: scale(1); }
        50% { transform: scale(0.8) rotate(-10deg); }
        100% { transform: scale(1) rotate(0deg); }
    }

    /* Animation rebond du compteur */
    @keyframes bounce {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }

    /* Style des boutons wishlist */
    .btn-wishlist {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.5rem;
        transition: all 0.3s ease;
        line-height: 1;
    }

    .btn-wishlist:hover {
        transform: scale(1.15);
    }

    .btn-wishlist:active {
        transform: scale(0.95);
    }

    .btn-wishlist.in-wishlist {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
`;
document.head.appendChild(style);

/**
 * ================================================
 * FIN DU FICHIER wishlist.js
 * ================================================
 * 
 * NOTES POUR LA MAINTENANCE :
 * 
 * 1. UTILISATION :
 *    - Ajouter data-product-id="X" sur les boutons wishlist
 *    - Utiliser la classe .btn-wishlist
 *    - Ajouter .in-wishlist si déjà en favoris
 * 
 * 2. EXEMPLE HTML :
 *    <button class="btn-wishlist" data-product-id="42">
 *        🤍
 *    </button>
 * 
 * 3. PERSONNALISATION :
 *    - Modifier les emojis (❤️ / 🤍)
 *    - Ajuster les animations CSS
 *    - Changer les durées d'animation
 * 
 * ================================================
 */
