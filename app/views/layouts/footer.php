<!-- Main Content -->
    <main>
<?php
/**
 * MARKETFLOW PRO - FOOTER LAYOUT
 * Fichier : app/views/layouts/footer.php
 */
?>

    </main>

    <!-- Footer -->
    <footer style="
        background: var(--bg-secondary);
        border-top: 1px solid var(--border-color);
        margin-top: var(--space-16);
        padding: var(--space-12) 0 var(--space-8);
    ">
        <div class="container">
            <div class="grid grid-4" style="margin-bottom: var(--space-8);">
                
                <!-- About -->
                <div>
                    <h4 style="margin-bottom: var(--space-4);">MarketFlow Pro</h4>
                    <p style="font-size: 0.875rem; color: var(--text-tertiary);">
                        La marketplace premium pour créateurs digitaux. 
                        Vendez et achetez des ressources de qualité.
                    </p>
                </div>

                <!-- Products -->
                <div>
                    <h5 style="margin-bottom: var(--space-4); font-size: 1rem;">Produits</h5>
                    <ul style="list-style: none; font-size: 0.875rem;">
                        <li style="margin-bottom: var(--space-2);"><a href="/products" style="color: var(--text-tertiary);">Catalogue</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/category/templates" style="color: var(--text-tertiary);">Templates</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/category/ui-kits" style="color: var(--text-tertiary);">UI Kits</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/category/icones" style="color: var(--text-tertiary);">Icônes</a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div>
                    <h5 style="margin-bottom: var(--space-4); font-size: 1rem;">Entreprise</h5>
                    <ul style="list-style: none; font-size: 0.875rem;">
                        <li style="margin-bottom: var(--space-2);"><a href="/about" style="color: var(--text-tertiary);">À propos</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/contact" style="color: var(--text-tertiary);">Contact</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/terms" style="color: var(--text-tertiary);">CGU</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/privacy" style="color: var(--text-tertiary);">Confidentialité</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h5 style="margin-bottom: var(--space-4); font-size: 1rem;">Support</h5>
                    <ul style="list-style: none; font-size: 0.875rem;">
                        <li style="margin-bottom: var(--space-2);"><a href="/help" style="color: var(--text-tertiary);">Centre d'aide</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/register?type=seller" style="color: var(--text-tertiary);">Devenir vendeur</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/contact" style="color: var(--text-tertiary);">Nous contacter</a></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom -->
            <div style="
                border-top: 1px solid var(--border-color);
                padding-top: var(--space-6);
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 0.875rem;
                color: var(--text-tertiary);
            ">
                <p>© <?= date('Y') ?> MarketFlow Pro. Tous droits réservés.</p>
                <div class="flex gap-6">
                    <a href="#" style="color: var(--text-tertiary);">Twitter</a>
                    <a href="#" style="color: var(--text-tertiary);">LinkedIn</a>
                    <a href="#" style="color: var(--text-tertiary);">Instagram</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="<?= JS_URL ?>/app.js"></script>
    
    <!-- User Menu Dropdown -->
    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
    <script>
    // Créer le dropdown du menu utilisateur
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