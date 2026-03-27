/**
 * ============================================================
 * MARKETFLOW PRO - AUTOCOMPLETE RECHERCHE
 * Fichier : public/js/search.js
 * 
 * Fonctionnement :
 * 1. L'utilisateur tape dans #searchInput
 * 2. Après 250ms de pause (debounce), appel AJAX vers /api/search/suggestions
 * 3. Le dropdown #searchDropdown est rempli avec les résultats
 * 4. Navigation clavier (↑ ↓ Entrée Escape) supportée
 * 5. Soumission → redirige vers /products/search?q=...
 * ============================================================
 */

(function () {
    'use strict';

    // -------------------------------------------------------
    // ÉLÉMENTS DOM
    // -------------------------------------------------------
    const input    = document.getElementById('searchInput');
    const dropdown = document.getElementById('searchDropdown');
    const submitBtn = document.getElementById('searchSubmitBtn');

    // Si les éléments sont absents (page sans nav search), on sort immédiatement
    if (!input || !dropdown) return;

    // -------------------------------------------------------
    // ÉTAT INTERNE
    // -------------------------------------------------------
    let debounceTimer   = null;   // Timer pour le debounce
    let currentQuery    = '';     // Dernière requête envoyée
    let highlightIndex  = -1;     // Index de l'item surligné au clavier (-1 = aucun)
    let allItems        = [];     // Tous les liens cliquables dans le dropdown

    // -------------------------------------------------------
    // CONSTANTES
    // -------------------------------------------------------
    const DEBOUNCE_DELAY  = 250;  // ms avant d'envoyer la requête
    const MIN_QUERY_LEN   = 2;    // Nb min de caractères pour déclencher la recherche

    // -------------------------------------------------------
    // DEBOUNCE : évite d'envoyer une requête à chaque frappe
    // -------------------------------------------------------
    function debounce(fn, delay) {
        return function (...args) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fn(...args), delay);
        };
    }

    // -------------------------------------------------------
    // AFFICHER LE SPINNER pendant le chargement
    // -------------------------------------------------------
    function showLoading() {
        dropdown.innerHTML = `
            <div class="search-loading">
                <div class="search-spinner"></div>
                Recherche en cours...
            </div>`;
        openDropdown();
    }

    // -------------------------------------------------------
    // OUVRIR / FERMER LE DROPDOWN
    // -------------------------------------------------------
    function openDropdown() {
        dropdown.classList.add('open');
        input.setAttribute('aria-expanded', 'true');
    }

    function closeDropdown() {
        dropdown.classList.remove('open');
        input.setAttribute('aria-expanded', 'false');
        highlightIndex = -1;
        allItems = [];
    }

    // -------------------------------------------------------
    // METTRE EN ÉVIDENCE le terme recherché dans le titre
    // Remplace "motif" par <mark>motif</mark> (insensible à la casse)
    // -------------------------------------------------------
    function highlight(text, query) {
        if (!query) return escapeHtml(text);
        const escaped = escapeHtml(text);
        const regex   = new RegExp('(' + escapeRegex(query) + ')', 'gi');
        return escaped.replace(regex, '<mark>$1</mark>');
    }

    // Échappe les caractères HTML dangereux
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    // Échappe les caractères spéciaux pour une RegExp
    function escapeRegex(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Formate un prix en euros
    function formatPrice(price) {
        return parseFloat(price).toFixed(2).replace('.', ',') + ' €';
    }

    // -------------------------------------------------------
    // CONSTRUCTION DU HTML DU DROPDOWN
    // -------------------------------------------------------
    function renderDropdown(data, query) {
        const { products = [], tags = [] } = data;

        // Aucun résultat dans les deux listes
        if (products.length === 0 && tags.length === 0) {
            dropdown.innerHTML = `
                <div class="search-no-result">
                    😕 Aucun résultat pour « ${escapeHtml(query)} »
                </div>
                <a href="/products/search?q=${encodeURIComponent(query)}" class="search-view-all">
                    Rechercher dans tous les produits →
                </a>`;
            openDropdown();
            return;
        }

        let html = '';

        // ------ SECTION PRODUITS ------
        if (products.length > 0) {
            html += `<div class="search-section-title">Produits</div>`;

            products.forEach(product => {
                // Miniature : fallback si pas de thumbnail
                const thumb = product.thumbnail_url
                    ? escapeHtml(product.thumbnail_url)
                    : '/public/img/placeholder.png';

                html += `
                    <a href="/products/${escapeHtml(product.slug)}"
                       class="search-result-item"
                       role="option">
                        <img
                            src="${thumb}"
                            alt="${escapeHtml(product.title)}"
                            class="search-result-thumb"
                            loading="lazy"
                            width="44"
                            height="44">
                        <div class="search-result-info">
                            <div class="search-result-title">
                                ${highlight(product.title, query)}
                            </div>
                            <div class="search-result-price">
                                ${formatPrice(product.price)}
                            </div>
                        </div>
                    </a>`;
            });
        }

        // ------ SECTION TAGS ------
        if (tags.length > 0) {
            html += `<div class="search-section-title">Tags populaires</div>`;
            html += `<div class="search-tags-container">`;

            tags.forEach(tag => {
                html += `
                    <a href="/products?tag=${encodeURIComponent(tag.slug)}"
                       class="search-tag-item"
                       role="option">
                        🏷️ ${highlight(tag.name, query)}
                    </a>`;
            });

            html += `</div>`;
        }

        // ------ LIEN "VOIR TOUS LES RÉSULTATS" ------
        html += `
            <a href="/products/search?q=${encodeURIComponent(query)}" class="search-view-all">
                Voir tous les résultats pour « ${escapeHtml(query)} » →
            </a>`;

        dropdown.innerHTML = html;
        openDropdown();

        // Reconstruire la liste des items navigables au clavier
        allItems = Array.from(dropdown.querySelectorAll('.search-result-item, .search-tag-item, .search-view-all'));
        highlightIndex = -1;
    }

    // -------------------------------------------------------
    // APPEL AJAX vers /api/search/suggestions
    // -------------------------------------------------------
    async function fetchSuggestions(query) {
        // Évite les requêtes inutiles si la query n'a pas changé
        if (query === currentQuery) return;
        currentQuery = query;

        showLoading();

        try {
            const response = await fetch(
                `/api/search/suggestions?q=${encodeURIComponent(query)}`,
                {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // Identifie les requêtes AJAX
                        'Accept': 'application/json'
                    }
                }
            );

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            renderDropdown(data, query);

        } catch (error) {
            // Erreur réseau ou serveur : on ferme silencieusement
            console.warn('[Search] Erreur lors de la récupération des suggestions :', error);
            closeDropdown();
        }
    }

    // Version debouncée de fetchSuggestions
    const debouncedFetch = debounce(fetchSuggestions, DEBOUNCE_DELAY);

    // -------------------------------------------------------
    // NAVIGATION CLAVIER dans le dropdown
    // ↓ = item suivant, ↑ = item précédent, Entrée = suivre le lien, Escape = fermer
    // -------------------------------------------------------
    function navigateDropdown(direction) {
        if (!allItems.length) return;

        // Retirer le surlignage de l'item courant
        if (highlightIndex >= 0) {
            allItems[highlightIndex].classList.remove('highlighted');
        }

        // Calculer le nouvel index (boucle sur les extrêmes)
        highlightIndex += direction;

        if (highlightIndex < 0) {
            highlightIndex = -1;  // Revenir au champ de saisie
            input.focus();
            return;
        }

        if (highlightIndex >= allItems.length) {
            highlightIndex = 0;
        }

        // Appliquer le surlignage
        allItems[highlightIndex].classList.add('highlighted');
        allItems[highlightIndex].focus();
    }

    // -------------------------------------------------------
    // SOUMISSION DU FORMULAIRE DE RECHERCHE
    // -------------------------------------------------------
    function submitSearch() {
        const query = input.value.trim();
        if (query.length >= MIN_QUERY_LEN) {
            // Redirige vers la page de résultats complète
            window.location.href = `/products/search?q=${encodeURIComponent(query)}`;
        }
    }

    // -------------------------------------------------------
    // ÉVÉNEMENTS SUR LE CHAMP DE SAISIE
    // -------------------------------------------------------
    input.addEventListener('input', function () {
        const query = this.value.trim();

        if (query.length < MIN_QUERY_LEN) {
            // Pas assez de caractères : on ferme et réinitialise
            closeDropdown();
            currentQuery = '';
            return;
        }

        // Lancer la recherche avec debounce
        debouncedFetch(query);
    });

    // Gestion des touches clavier sur le champ
    input.addEventListener('keydown', function (e) {
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                navigateDropdown(+1);  // Item suivant
                break;

            case 'ArrowUp':
                e.preventDefault();
                navigateDropdown(-1);  // Item précédent
                break;

            case 'Enter':
                e.preventDefault();
                // Si un item est surligné, le suivre ; sinon soumettre la recherche
                if (highlightIndex >= 0 && allItems[highlightIndex]) {
                    allItems[highlightIndex].click();
                } else {
                    submitSearch();
                }
                break;

            case 'Escape':
                closeDropdown();      // Fermer le dropdown
                input.blur();
                break;
        }
    });

    // Clic sur le bouton loupe → soumettre
    submitBtn.addEventListener('click', submitSearch);

    // Rouvrir le dropdown si l'utilisateur refocus sur le champ
    input.addEventListener('focus', function () {
        if (this.value.trim().length >= MIN_QUERY_LEN && currentQuery === this.value.trim()) {
            openDropdown();
        }
    });

    // -------------------------------------------------------
    // FERMER LE DROPDOWN en cliquant en dehors
    // -------------------------------------------------------
    document.addEventListener('click', function (e) {
        const searchContainer = document.getElementById('navSearch');
        if (searchContainer && !searchContainer.contains(e.target)) {
            closeDropdown();
        }
    });

    // -------------------------------------------------------
    // ACCESSIBILITÉ : fermer avec Escape sur les items du dropdown
    // -------------------------------------------------------
    dropdown.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeDropdown();
            input.focus();
        }
    });

    console.log('✅ [Search] Autocomplete initialisé');

})();
