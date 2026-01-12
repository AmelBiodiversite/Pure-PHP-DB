/**
 * MARKETFLOW PRO - JAVASCRIPT COMPONENTS
 * Composants interactifs modernes
 */

// ============================================
// DARK MODE TOGGLE
// ============================================
class DarkModeToggle {
    constructor() {
        this.darkMode = localStorage.getItem('darkMode') === 'true';
        this.init();
    }

    init() {
        // Appliquer le mode sauvegardé
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        }

        // Créer le bouton toggle
        this.createToggleButton();
    }

    createToggleButton() {
        const btn = document.createElement('button');
        btn.className = 'btn btn-ghost btn-sm dark-mode-toggle';
        btn.innerHTML = this.darkMode ? '☀️' : '🌙';
        btn.style.cssText = 'position: fixed; bottom: 20px; right: 20px; z-index: 999; border-radius: 50%; width: 50px; height: 50px;';
        
        btn.onclick = () => this.toggle();
        document.body.appendChild(btn);
    }

    toggle() {
        this.darkMode = !this.darkMode;
        document.documentElement.classList.toggle('dark');
        localStorage.setItem('darkMode', this.darkMode);
        
        const btn = document.querySelector('.dark-mode-toggle');
        btn.innerHTML = this.darkMode ? '☀️' : '🌙';
    }
}

// ============================================
// MODAL SYSTEM
// ============================================
class Modal {
    constructor(id) {
        this.id = id;
        this.modal = null;
    }

    create(content, options = {}) {
        const {
            title = 'Modal',
            size = 'md', // sm, md, lg
            closeOnOverlay = true
        } = options;

        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            animation: fadeIn 0.2s ease-out;
        `;

        const maxWidths = { sm: '400px', md: '600px', lg: '900px' };
        
        const modalEl = document.createElement('div');
        modalEl.className = 'modal';
        modalEl.style.cssText = `
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            max-width: ${maxWidths[size]};
            width: 90%;
            max-height: 90vh;
            overflow: auto;
            animation: slideIn 0.3s ease-out;
        `;

        modalEl.innerHTML = `
            <div style="padding: var(--space-6); border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0;">${title}</h3>
                <button class="modal-close btn btn-ghost btn-sm">✕</button>
            </div>
            <div style="padding: var(--space-6);">
                ${content}
            </div>
        `;

        overlay.appendChild(modalEl);
        document.body.appendChild(overlay);
        this.modal = overlay;

        // Events
        if (closeOnOverlay) {
            overlay.onclick = (e) => {
                if (e.target === overlay) this.close();
            };
        }

        modalEl.querySelector('.modal-close').onclick = () => this.close();
    }

    close() {
        if (this.modal) {
            this.modal.style.opacity = '0';
            setTimeout(() => this.modal.remove(), 200);
        }
    }
}

// ============================================
// NOTIFICATION TOAST
// ============================================
class Toast {
    static show(message, type = 'info', duration = 3000) {
        const colors = {
            success: 'var(--success)',
            error: 'var(--error)',
            warning: 'var(--warning)',
            info: 'var(--primary-600)'
        };

        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };

        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-left: 4px solid ${colors[type]};
            border-radius: var(--radius);
            padding: var(--space-4);
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: var(--space-3);
            min-width: 300px;
            z-index: 2000;
            animation: slideIn 0.3s ease-out;
        `;

        toast.innerHTML = `
            <span style="font-size: 1.25rem; color: ${colors[type]};">${icons[type]}</span>
            <span style="flex: 1;">${message}</span>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; font-size: 1.25rem; color: var(--text-tertiary);">✕</button>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(400px)';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
}

// ============================================
// SEARCH BAR WITH AUTOCOMPLETE
// ============================================
class SearchBar {
    constructor(inputSelector, onSearch, suggestions = []) {
        this.input = document.querySelector(inputSelector);
        this.onSearch = onSearch;
        this.suggestions = suggestions;
        this.init();
    }

    init() {
        if (!this.input) return;

        // Créer container de suggestions
        const suggestionsEl = document.createElement('div');
        suggestionsEl.className = 'search-suggestions';
        suggestionsEl.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-top: none;
            border-radius: 0 0 var(--radius) var(--radius);
            box-shadow: var(--shadow-lg);
            max-height: 300px;
            overflow-y: auto;
            display: none;
            z-index: 100;
        `;

        this.input.parentElement.style.position = 'relative';
        this.input.parentElement.appendChild(suggestionsEl);
        this.suggestionsEl = suggestionsEl;

        // Events
        this.input.addEventListener('input', (e) => this.handleInput(e));
        this.input.addEventListener('focus', () => this.showSuggestions());
        document.addEventListener('click', (e) => {
            if (!this.input.contains(e.target)) {
                this.hideSuggestions();
            }
        });
    }

    handleInput(e) {
        const query = e.target.value.toLowerCase();
        
        if (query.length < 2) {
            this.hideSuggestions();
            return;
        }

        const filtered = this.suggestions.filter(item => 
            item.toLowerCase().includes(query)
        );

        this.renderSuggestions(filtered);
    }

    renderSuggestions(items) {
        if (items.length === 0) {
            this.hideSuggestions();
            return;
        }

        this.suggestionsEl.innerHTML = items.map(item => `
            <div class="suggestion-item" style="
                padding: var(--space-3) var(--space-4);
                cursor: pointer;
                transition: background var(--transition);
            " onmouseover="this.style.background='var(--bg-secondary)'" 
               onmouseout="this.style.background='transparent'"
               onclick="document.querySelector('${this.input.tagName.toLowerCase()}').value='${item}'; this.parentElement.style.display='none';">
                ${item}
            </div>
        `).join('');

        this.showSuggestions();
    }

    showSuggestions() {
        this.suggestionsEl.style.display = 'block';
    }

    hideSuggestions() {
        this.suggestionsEl.style.display = 'none';
    }
}

// ============================================
// DROPDOWN MENU
// ============================================
class Dropdown {
    constructor(triggerSelector) {
        this.trigger = document.querySelector(triggerSelector);
        this.init();
    }

    init() {
        if (!this.trigger) return;

        const menu = document.createElement('div');
        menu.className = 'dropdown-menu';
        menu.style.cssText = `
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            min-width: 200px;
            padding: var(--space-2);
            display: none;
            z-index: 100;
            margin-top: var(--space-2);
        `;

        this.trigger.parentElement.style.position = 'relative';
        this.trigger.parentElement.appendChild(menu);
        this.menu = menu;

        this.trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggle();
        });

        document.addEventListener('click', () => this.close());
    }

    addItem(text, onClick) {
        const item = document.createElement('button');
        item.className = 'dropdown-item';
        item.textContent = text;
        item.style.cssText = `
            width: 100%;
            padding: var(--space-2) var(--space-3);
            text-align: left;
            background: none;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: background var(--transition);
            color: var(--text-primary);
        `;
        
        item.onmouseover = () => item.style.background = 'var(--bg-secondary)';
        item.onmouseout = () => item.style.background = 'none';
        item.onclick = () => {
            onClick();
            this.close();
        };

        this.menu.appendChild(item);
    }

    toggle() {
        this.menu.style.display = this.menu.style.display === 'block' ? 'none' : 'block';
    }

    close() {
        this.menu.style.display = 'none';
    }
}

// ============================================
// TABS SYSTEM
// ============================================
class Tabs {
    constructor(containerSelector) {
        this.container = document.querySelector(containerSelector);
        this.init();
    }

    init() {
        if (!this.container) return;

        const tabs = this.container.querySelectorAll('[data-tab]');
        const panels = this.container.querySelectorAll('[data-tab-panel]');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;

                // Désactiver tous les tabs
                tabs.forEach(t => {
                    t.classList.remove('active');
                    t.style.borderBottom = 'none';
                    t.style.color = 'var(--text-secondary)';
                });

                // Activer le tab cliqué
                tab.classList.add('active');
                tab.style.borderBottom = '2px solid var(--primary-600)';
                tab.style.color = 'var(--primary-600)';

                // Cacher tous les panels
                panels.forEach(p => p.style.display = 'none');

                // Afficher le panel correspondant
                const panel = this.container.querySelector(`[data-tab-panel="${target}"]`);
                if (panel) {
                    panel.style.display = 'block';
                    panel.style.animation = 'fadeIn 0.3s ease-out';
                }
            });
        });

        // Activer le premier tab par défaut
        if (tabs.length > 0) {
            tabs[0].click();
        }
    }
}

// ============================================
// IMAGE UPLOAD PREVIEW
// ============================================
class ImageUploadPreview {
    constructor(inputSelector, previewSelector) {
        this.input = document.querySelector(inputSelector);
        this.preview = document.querySelector(previewSelector);
        this.init();
    }

    init() {
        if (!this.input || !this.preview) return;

        this.input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = (e) => {
                    this.preview.innerHTML = `
                        <img src="${e.target.result}" style="
                            max-width: 100%;
                            height: auto;
                            border-radius: var(--radius);
                        " alt="Preview">
                    `;
                };
                
                reader.readAsDataURL(file);
            }
        });
    }
}

// ============================================
// LOADING SPINNER
// ============================================
class LoadingSpinner {
    static show(targetSelector = 'body') {
        const target = document.querySelector(targetSelector);
        if (!target) return;

        const spinner = document.createElement('div');
        spinner.className = 'loading-spinner';
        spinner.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        `;

        spinner.innerHTML = `
            <div style="
                width: 50px;
                height: 50px;
                border: 4px solid rgba(255, 255, 255, 0.3);
                border-top-color: white;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            "></div>
            <style>
                @keyframes spin {
                    to { transform: rotate(360deg); }
                }
            </style>
        `;

        document.body.appendChild(spinner);
    }

    static hide() {
        const spinner = document.querySelector('.loading-spinner');
        if (spinner) spinner.remove();
    }
}

// ============================================
// FORM VALIDATION
// ============================================
class FormValidator {
    constructor(formSelector) {
        this.form = document.querySelector(formSelector);
        this.init();
    }

    init() {
        if (!this.form) return;

        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            if (this.validate()) {
                Toast.show('Formulaire valide !', 'success');
                // Soumettre le formulaire ici
            }
        });
    }

    validate() {
        let isValid = true;
        const inputs = this.form.querySelectorAll('input[required], textarea[required]');

        inputs.forEach(input => {
            const errorEl = input.parentElement.querySelector('.form-error');
            
            if (!input.value.trim()) {
                isValid = false;
                this.showError(input, 'Ce champ est requis');
            } else if (input.type === 'email' && !this.isValidEmail(input.value)) {
                isValid = false;
                this.showError(input, 'Email invalide');
            } else {
                this.clearError(input);
            }
        });

        return isValid;
    }

    showError(input, message) {
        input.style.borderColor = 'var(--error)';
        
        let errorEl = input.parentElement.querySelector('.form-error');
        if (!errorEl) {
            errorEl = document.createElement('div');
            errorEl.className = 'form-error';
            input.parentElement.appendChild(errorEl);
        }
        errorEl.textContent = message;
    }

    clearError(input) {
        input.style.borderColor = 'var(--border-color)';
        const errorEl = input.parentElement.querySelector('.form-error');
        if (errorEl) errorEl.remove();
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
}

// ============================================
// INITIALISATION AUTOMATIQUE
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    // Dark mode
    new DarkModeToggle();

    // Exemple d'utilisation
    console.log('MarketFlow Pro - Components chargés ✓');
});

// Export pour utilisation globale
window.MarketFlow = {
    Modal,
    Toast,
    SearchBar,
    Dropdown,
    Tabs,
    ImageUploadPreview,
    LoadingSpinner,
    FormValidator
};