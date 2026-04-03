<!-- FIN DU CONTENU PRINCIPAL -->
    </main>

    <footer class="ft">
        <div class="ft-top">

            <div>
                <div class="ft-logo">
                    <div class="ft-logo-ic">M</div>
                    <span class="ft-logo-txt">MarketFlow</span>
                </div>
                <p class="ft-tagline">La marketplace pour le développement personnel et l'autonomie.</p>
                <a class="ft-cta-vendeur" href="/register?type=seller">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#534ab7" stroke-width="1.8" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                    <span class="ft-cta-txt">Devenir vendeur sur MarketFlow</span>
                    <span class="ft-cta-arrow">→</span>
                </a>
                <div class="ft-stripe">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#0f6e56" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span class="ft-stripe-txt">Paiements sécurisés par <strong>Stripe</strong></span>
                </div>
            </div>

            <div>
                <div class="ft-col-title">Explorer</div>
                <div class="ft-col">
                    <a href="/products">Tous les produits <span>→</span></a>
                    <a href="/category">Catégories <span>→</span></a>
                    <a href="/products?sort=popular">Plus vendus <span>→</span></a>
                    <a href="/products?sort=new">Nouveautés <span>→</span></a>
                </div>
            </div>

            <div>
                <div class="ft-col-title">Mon compte</div>
                <div class="ft-col">
                    <a href="/login">Connexion <span>→</span></a>
                    <a href="/register">Inscription <span>→</span></a>
                    <a href="/orders">Mes commandes <span>→</span></a>
                    <a href="/wishlist">Mes favoris <span>→</span></a>
                </div>
            </div>

            <div>
                <div class="ft-col-title">Informations</div>
                <div class="ft-col">
                    <a href="/about">À propos <span>→</span></a>
                    <a href="/contact">Contact <span>→</span></a>
                    <a href="/terms">CGU <span>→</span></a>
                    <a href="/privacy">Confidentialité <span>→</span></a>
                    <a href="/mentions-legales">Mentions légales <span>→</span></a>
                </div>
            </div>

        </div>

        <div class="ft-bottom">
            <span class="ft-copy">© <?= date('Y') ?> MarketFlow — Tous droits réservés</span>
            <div class="ft-made">
                <div class="ft-made-dot"></div>
                <span class="ft-made-txt">Fait avec soin en France</span>
            </div>
            <div class="ft-socials">
                <a class="ft-social" href="#" title="Twitter">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53A4.48 4.48 0 0 0 16 2a4.48 4.48 0 0 0-4.48 4.48c0 .35.04.7.11 1.02A12.74 12.74 0 0 1 2 3.8a4.48 4.48 0 0 0 1.39 5.98 4.44 4.44 0 0 1-2.03-.56v.06a4.48 4.48 0 0 0 3.59 4.39 4.5 4.5 0 0 1-2.02.08 4.48 4.48 0 0 0 4.18 3.11A8.98 8.98 0 0 1 2 19.54 12.69 12.69 0 0 0 8.68 21.5c8.22 0 12.71-6.81 12.71-12.71l-.01-.58A9.1 9.1 0 0 0 23 6.07"/></svg>
                </a>
                <a class="ft-social" href="#" title="LinkedIn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                </a>
                <a class="ft-social" href="#" title="Instagram">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                </a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
    <script>
    const userMenuBtn = document.getElementById('userMenuBtn');
    if (userMenuBtn) {
        const dropdown = new MarketFlow.Dropdown('#userMenuBtn');
        dropdown.addItem('Mon profil', () => window.location.href = '/profile');
        dropdown.addItem('Mes commandes', () => window.location.href = '/orders');
        <?php if ($_SESSION['user_type'] === 'seller'): ?>
        dropdown.addItem('Dashboard vendeur', () => window.location.href = '/seller/dashboard');
        <?php endif; ?>
        dropdown.addItem('Déconnexion', () => window.location.href = '/logout');
    }
    </script>
    <?php endif; ?>

</body>
</html>
