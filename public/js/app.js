/**
 * ================================================================
 * MARKETFLOW PRO - JAVASCRIPT v3.0
 * ================================================================
 * Interactions et animations modernes
 * ================================================================
 */

console.log('🚀 MarketFlow Pro v3.0 - Chargé');

// ================================================================
// INITIALISATION AU CHARGEMENT DU DOM
// ================================================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Initialisation des composants...');
    
    initDropdowns();
    initScrollAnimations();
    initLazyLoading();
    initTooltips();
    initSmoothScroll();
    initProductCardAnimations();
    
    console.log('✅ Tous les composants sont initialisés');
});

// ================================================================
// DROPDOWNS - Menus déroulants
// ================================================================
function initDropdowns() {
    const dropdowns = document.querySelectorAll('[data-dropdown]');
    
    dropdowns.forEach(dropdown => {
        const button = dropdown.querySelector('button');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (!button || !menu) return;
        
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            
            // Fermer les autres dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                if (m !== menu) m.classList.add('hidden');
            });
            
            menu.classList.toggle('hidden');
        });
        
        document.addEventListener('click', () => {
            menu.classList.add('hidden');
        });
        
        menu.addEventListener('click', (e) => e.stopPropagation());
    });
    
    console.log(`   ✅ ${dropdowns.length} dropdown(s) initialisé(s)`);
}

// ================================================================
// ANIMATIONS AU SCROLL - Intersection Observer
// ================================================================
function initScrollAnimations() {
    const elements = document.querySelectorAll('.animate-fade-in');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    entry.target.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
                
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    elements.forEach(el => observer.observe(el));
    console.log(`   ✅ ${elements.length} animation(s) au scroll`);
}

// ================================================================
// LAZY LOADING - Chargement différé des images
// ================================================================
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
    console.log(`   ✅ ${images.length} image(s) en lazy loading`);
}

// ================================================================
// TOOLTIPS - Infobulles au survol
// ================================================================
function initTooltips() {
    const elements = document.querySelectorAll('[data-tooltip]');
    
    elements.forEach(el => {
        el.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.textContent = this.dataset.tooltip;
            tooltip.style.cssText = `
                position: absolute;
                background: #1f2937;
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                font-size: 0.875rem;
                z-index: 9999;
                pointer-events: none;
                white-space: nowrap;
                box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
                animation: tooltipFadeIn 0.2s ease-out;
            `;
            
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            
            this.tooltipElement = tooltip;
        });
        
        el.addEventListener('mouseleave', function() {
            if (this.tooltipElement) {
                this.tooltipElement.remove();
            }
        });
    });
    
    console.log(`   ✅ ${elements.length} tooltip(s) initialisé(s)`);
}

// ================================================================
// SMOOTH SCROLL - Défilement fluide
// ================================================================
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// ================================================================
// ANIMATIONS PRODUCT CARDS
// ================================================================
function initProductCardAnimations() {
    const cards = document.querySelectorAll('.product-card');
    
    cards.forEach((card, index) => {
        // Animation d'apparition en cascade
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    console.log(`   ✅ ${cards.length} product card(s) animée(s)`);
}


// ================================================================
// STRIPE CHECKOUT - À AJOUTER DANS public/js/app.js
// ================================================================

/**
 * Gérer le bouton "Passer commande"
 */
document.addEventListener('DOMContentLoaded', function() {
    const checkoutBtn = document.getElementById('checkout-btn');

    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', handleCheckout);
        console.log('✅ Bouton checkout initialisé');
    }
});

/**
 * Fonction pour gérer le checkout
 */
async function handleCheckout(e) {
    e.preventDefault();

    const btn = e.target;
    const originalText = btn.textContent;

    // Désactiver le bouton
    btn.disabled = true;
    btn.textContent = '⏳ Préparation...';

    try {
        console.log('📤 Création de la session Stripe...');

        // Appeler l'API pour créer la session Stripe
        const response = await fetch('/stripe/create-checkout-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        console.log('📥 Réponse reçue:', data);

        if (!response.ok) {
            throw new Error(data.error || 'Erreur lors de la création du paiement');
        }

        if (data.success && data.checkout_url) {
            console.log('✅ Redirection vers Stripe:', data.checkout_url);

            // Afficher un message
            showToast('Redirection vers le paiement sécurisé...', 'info');

            // Rediriger vers Stripe Checkout
            window.location.href = data.checkout_url;
        } else {
            throw new Error('URL de checkout manquante');
        }

    } catch (error) {
        console.error('❌ Erreur checkout:', error);

        // Afficher l'erreur
        showToast(error.message, 'error');

        // Réactiver le bouton
        btn.disabled = false;
        btn.textContent = originalText;
    }
}

/**
 * Alternative: Si vous utilisez Stripe.js (méthode recommandée)
 */
async function handleCheckoutWithStripeJS(e) {
    e.preventDefault();

    const stripe = Stripe('<?php echo STRIPE_PUBLIC_KEY; ?>'); // Clé publique
    const btn = e.target;
    const originalText = btn.textContent;

    btn.disabled = true;
    btn.textContent = '⏳ Préparation...';

    try {
        // Créer la session
        const response = await fetch('/stripe/create-checkout-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        // Rediriger avec Stripe.js
        const { error } = await stripe.redirectToCheckout({
            sessionId: data.session_id // Si vous retournez session_id au lieu de checkout_url
        });

        if (error) {
            throw new Error(error.message);
        }

    } catch (error) {
        console.error('Erreur:', error);
        showToast(error.message, 'error');
        btn.disabled = false;
        btn.textContent = originalText;
    }
}


// ================================================================
// HELPER: Toast Notifications
// ================================================================
window.showToast = function(message, type = 'info') {
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };
    
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${colors[type] || colors.info};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
        font-weight: 500;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

// Styles pour animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }
    @keyframes tooltipFadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);
