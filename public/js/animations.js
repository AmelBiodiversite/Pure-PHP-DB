/**
 * ================================================================
 * MARKETFLOW PRO - ANIMATIONS & MICRO-INTERACTIONS v1.0
 * ================================================================
 * 
 * Extension du système JavaScript existant (app.js)
 * Compatible avec le design system CSS actuel
 * 
 * DÉPENDANCES :
 * - app.js (fonctions showToast, initDropdowns, etc.)
 * - style.css (variables CSS, classes utilitaires)
 * 
 * FONCTIONNALITÉS :
 * - Animations au scroll révélées progressivement
 * - Compteur animé du panier
 * - Effet ripple sur les boutons
 * - Animations des product cards avancées
 * - Micro-interactions sur les formulaires
 * - Skeleton loading pour les images
 * 
 * @author MarketFlow Team
 * @version 1.0
 * @date 2025-01-31
 * ================================================================
 */

(function() {
    'use strict';

    console.log('🎨 Système d\'animations MarketFlow - Chargement...');

    // ================================================================
    // CONFIGURATION GLOBALE
    // ================================================================
    const AnimConfig = {
        scrollReveal: {
            threshold: 0.15,
            rootMargin: '0px 0px -100px 0px'
        },
        ripple: {
            duration: 600,
            color: 'rgba(255, 255, 255, 0.5)'
        },
        counter: {
            duration: 800,
            easing: 'ease-out'
        }
    };

    // ================================================================
    // INITIALISATION AU CHARGEMENT DU DOM
    // ================================================================
    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ Initialisation des animations...');

        initScrollReveal();
        initRippleEffect();
        initProductCardEnhancement();
        initFormAnimations();
        initSkeletonLoading();
        // initParallaxEffects(); // DÉSACTIVÉ - causait la superposition du hero
        // initNavScrollEffect(); // DÉSACTIVÉ - causait un défilement lent

        console.log('✅ Animations initialisées avec succès');
    });

    // ================================================================
    // SCROLL REVEAL - Révélation progressive au scroll
    // ================================================================
    /**
     * Révèle les éléments avec la classe .scroll-reveal ou .animate-fade-in
     * au fur et à mesure du défilement - VERSION SIMPLIFIÉE
     */
    function initScrollReveal() {
        const elements = document.querySelectorAll('.scroll-reveal, .product-card, .card, .category-card');

        if (elements.length === 0) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    // Animation simple et rapide
                    entry.target.classList.add('revealed');
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';

                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1, // Déclencher plus tôt
            rootMargin: '0px' // Pas de marge
        });

        elements.forEach(el => {
            // Préparer l'élément pour l'animation (plus subtil)
            if (!el.style.opacity) {
                el.style.opacity = '0';
                el.style.transform = 'translateY(15px)'; // Moins de déplacement
                el.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out'; // Plus rapide
            }
            observer.observe(el);
        });

        console.log(`   ✅ ${elements.length} élément(s) en scroll reveal`);
    }

    // ================================================================
    // RIPPLE EFFECT - Effet d'ondulation sur les boutons
    // ================================================================
    /**
     * Ajoute un effet ripple (ondulation) sur tous les boutons
     */
    function initRippleEffect() {
        const buttons = document.querySelectorAll('.btn, button:not(.notification-close)');

        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Ne pas ajouter de ripple si le bouton est désactivé
                if (this.disabled) return;

                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    border-radius: 50%;
                    background: ${AnimConfig.ripple.color};
                    top: ${y}px;
                    left: ${x}px;
                    pointer-events: none;
                    transform: scale(0);
                    animation: ripple ${AnimConfig.ripple.duration}ms ease-out;
                `;

                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);

                setTimeout(() => ripple.remove(), AnimConfig.ripple.duration);
            });
        });

        console.log(`   ✅ Effet ripple sur ${buttons.length} bouton(s)`);
    }

    // ================================================================
    // PRODUCT CARD ENHANCEMENT - Améliorations des cartes produits
    // ================================================================
    /**
     * Ajoute des animations avancées aux cartes produits
     */
    function initProductCardEnhancement() {
        const productCards = document.querySelectorAll('.product-card');

        productCards.forEach((card, index) => {
            // Animation d'apparition en cascade
            card.style.animationDelay = `${index * 0.1}s`;

            // Parallax subtil sur l'image au mouvement de la souris
            card.addEventListener('mousemove', function(e) {
                const image = this.querySelector('.product-image-container img');
                if (!image) return;

                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const percentX = (x - centerX) / centerX;
                const percentY = (y - centerY) / centerY;

                image.style.transform = `
                    scale(1.15) 
                    rotate(2deg) 
                    translate(${percentX * 5}px, ${percentY * 5}px)
                `;
            });

            card.addEventListener('mouseleave', function() {
                const image = this.querySelector('.product-image-container img');
                if (image) {
                    image.style.transform = 'scale(1) rotate(0deg) translate(0, 0)';
                }
            });
        });

        console.log(`   ✅ ${productCards.length} product card(s) améliorée(s)`);
    }

    // ================================================================
    // FORM ANIMATIONS - Animations sur les formulaires
    // ================================================================
    /**
     * Ajoute des micro-interactions sur les champs de formulaire
     */
    function initFormAnimations() {
        const inputs = document.querySelectorAll('input, textarea, select');

        inputs.forEach(input => {
            // Animation au focus
            input.addEventListener('focus', function() {
                this.parentElement?.classList.add('focused');

                // Animation de l'élément
                this.style.transform = 'scale(1.01)';
                this.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.1)';
            });

            // Animation au blur
            input.addEventListener('blur', function() {
                this.parentElement?.classList.remove('focused');
                this.style.transform = 'scale(1)';
                this.style.boxShadow = 'none';
            });

            // Animation lors de la saisie
            input.addEventListener('input', function() {
                if (this.value.length > 0) {
                    this.classList.add('has-value');
                } else {
                    this.classList.remove('has-value');
                }
            });
        });

        console.log(`   ✅ Animations sur ${inputs.length} champ(s) de formulaire`);
    }

    // ================================================================
    // SKELETON LOADING - Chargement avec effet skeleton
    // ================================================================
    /**
     * Ajoute un effet de chargement skeleton sur les images
     */
    function initSkeletonLoading() {
        const images = document.querySelectorAll('img[data-src], img:not([src])');

        images.forEach(img => {
            // Créer le skeleton
            const skeleton = document.createElement('div');
            skeleton.className = 'skeleton-loader';
            skeleton.style.cssText = `
                position: absolute;
                inset: 0;
                background: linear-gradient(
                    90deg,
                    #f0f0f0 0%,
                    #e0e0e0 50%,
                    #f0f0f0 100%
                );
                background-size: 200% 100%;
                animation: skeleton-loading 1.5s ease-in-out infinite;
                border-radius: inherit;
            `;

            // Insérer le skeleton
            img.parentElement.style.position = 'relative';
            img.parentElement.appendChild(skeleton);

            // Supprimer le skeleton quand l'image est chargée
            img.addEventListener('load', function() {
                skeleton.style.opacity = '0';
                skeleton.style.transition = 'opacity 0.3s ease-out';
                setTimeout(() => skeleton.remove(), 300);
            });

            // Charger l'image si data-src est présent
            if (img.dataset.src) {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
            }
        });

        console.log(`   ✅ Skeleton loading sur ${images.length} image(s)`);
    }

    // ================================================================
    // PARALLAX EFFECTS - Effets parallaxe subtils
    // ================================================================
    /**
     * Ajoute des effets parallaxe subtils aux éléments hero
     */
    function initParallaxEffects() {
        const heroElements = document.querySelectorAll('.hero, .hero::before, .hero::after');

        if (heroElements.length === 0) return;

        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;

            heroElements.forEach(element => {
                if (element instanceof Element) {
                    element.style.transform = `translateY(${scrolled * 0.5}px)`;
                }
            });
        });

        console.log(`   ✅ Parallax sur ${heroElements.length} élément(s)`);
    }

    // ================================================================
    // NAV SCROLL EFFECT - Effet au scroll sur la navigation
    // ================================================================
    /**
     * Ajoute un effet de défilement sur la barre de navigation
     */
    function initNavScrollEffect() {
        const nav = document.querySelector('nav');
        if (!nav) return;

        let lastScroll = 0;

        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;

            // Ajouter classe scrolled après 50px
            if (currentScroll > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }

            // Masquer/afficher la nav au scroll
            if (currentScroll > lastScroll && currentScroll > 100) {
                // Scroll vers le bas - masquer
                nav.style.transform = 'translateY(-100%)';
            } else {
                // Scroll vers le haut - afficher
                nav.style.transform = 'translateY(0)';
            }

            lastScroll = currentScroll;
        });

        console.log('   ✅ Effet scroll sur la navigation');
    }

    // ================================================================
    // CART COUNTER ANIMATION - Animation du compteur panier
    // ================================================================
    /**
     * Anime le compteur du panier lors des changements
     * Fonction globale exportée pour être utilisée ailleurs
     */
    window.animateCartCounter = function(newCount) {
        const badge = document.querySelector('a[href="/cart"] span');
        if (!badge) return;

        const oldCount = parseInt(badge.textContent) || 0;

        // Animation de pop
        badge.style.animation = 'none';
        setTimeout(() => {
            badge.style.animation = 'cartBadgePop 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        }, 10);

        // Compteur animé
        animateValue(badge, oldCount, newCount, AnimConfig.counter.duration);

        // Afficher/masquer le badge
        if (newCount > 0) {
            badge.style.display = 'flex';
        } else {
            setTimeout(() => {
                badge.style.display = 'none';
            }, AnimConfig.counter.duration);
        }
    };

    // ================================================================
    // ANIMATE VALUE - Animation de compteur
    // ================================================================
    /**
     * Anime un nombre de start à end
     */
    function animateValue(element, start, end, duration) {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;

            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                current = end;
                clearInterval(timer);
            }

            element.textContent = Math.round(current);
        }, 16);
    }

    // ================================================================
    // CSS ANIMATIONS - Ajouter les animations CSS manquantes
    // ================================================================
    const style = document.createElement('style');
    style.textContent = `
        /* Animation ripple */
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* Animation skeleton loading */
        @keyframes skeleton-loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }

        /* Animation pop du badge panier */
        @keyframes cartBadgePop {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.3);
            }
            100% {
                transform: scale(1);
            }
        }

        /* Animation pulse pour wishlist */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.6;
            }
        }

        /* État focused sur les inputs */
        .focused input,
        .focused textarea,
        .focused select {
            border-color: var(--primary-500) !important;
        }

        /* État has-value sur les inputs */
        .has-value {
            font-weight: 500;
        }

        /* Amélioration des transitions */
        input, textarea, select, button {
            transition: all 0.2s ease-out;
        }

        /* Navigation transitions */
        nav {
            transition: transform 0.3s ease-out, background 0.3s ease-out;
        }

        /* Skeleton loader responsive */
        @media (prefers-reduced-motion: reduce) {
            .skeleton-loader {
                animation: none !important;
                background: #e0e0e0 !important;
            }
        }
    `;
    document.head.appendChild(style);

    // ================================================================
    // EXPORT GLOBAL
    // ================================================================
    window.MarketFlowAnimations = {
        animateCartCounter: window.animateCartCounter,
        animateValue: animateValue
    };

    console.log('✅ Système d\'animations MarketFlow - Prêt');

})();