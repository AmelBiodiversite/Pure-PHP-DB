<!-- =====================================================
         FIN DU CONTENU PRINCIPAL
         ===================================================== -->
    </main>

    <!-- =====================================================
         FOOTER DU SITE
         ===================================================== -->
    <footer style="
        background: var(--bg-secondary);
        border-top: 1px solid var(--border-color);
        margin-top: var(--space-16);
        padding: var(--space-12) 0 var(--space-8);
    ">
        <div class="container">
            <!-- Grille de 4 colonnes pour le footer -->
            <div class="grid grid-4" style="margin-bottom: var(--space-8);">
                
                <!-- ==================== À PROPOS ==================== -->
                <div>
                    <h4 style="margin-bottom: var(--space-4);">MarketFlow Pro</h4>
                    <p style="font-size: 0.875rem; color: var(--text-tertiary);">
                        La marketplace premium pour créateurs digitaux. 
                        Vendez et achetez des ressources de qualité.
                    </p>
                </div>

                <!-- ==================== PRODUITS ==================== -->
                <div>
                    <h5 style="margin-bottom: var(--space-4); font-size: 1rem;">Produits</h5>
                    <ul style="list-style: none; font-size: 0.875rem;">
                        <li style="margin-bottom: var(--space-2);"><a href="/products" style="color: var(--text-tertiary);">Catalogue</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/category/templates" style="color: var(--text-tertiary);">Templates</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/category/ui-kits" style="color: var(--text-tertiary);">UI Kits</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/category/icones" style="color: var(--text-tertiary);">Icônes</a></li>
                    </ul>
                </div>

                <!-- ==================== ENTREPRISE ==================== -->
                <div>
                    <h5 style="margin-bottom: var(--space-4); font-size: 1rem;">Entreprise</h5>
                    <ul style="list-style: none; font-size: 0.875rem;">
                        <li style="margin-bottom: var(--space-2);"><a href="/about" style="color: var(--text-tertiary);">À propos</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/contact" style="color: var(--text-tertiary);">Contact</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/terms" style="color: var(--text-tertiary);">CGU</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/privacy" style="color: var(--text-tertiary);">Confidentialité</a></li>
                    </ul>
                </div>

                <!-- ==================== SUPPORT ==================== -->
                <div>
                    <h5 style="margin-bottom: var(--space-4); font-size: 1rem;">Support</h5>
                    <ul style="list-style: none; font-size: 0.875rem;">
                        <li style="margin-bottom: var(--space-2);"><a href="/help" style="color: var(--text-tertiary);">Centre d'aide</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/register?type=seller" style="color: var(--text-tertiary);">Devenir vendeur</a></li>
                        <li style="margin-bottom: var(--space-2);"><a href="/contact" style="color: var(--text-tertiary);">Nous contacter</a></li>
                    </ul>
                </div>

            </div>

            <!-- ==================== BAS DU FOOTER ==================== -->
            <div style="
                border-top: 1px solid var(--border-color);
                padding-top: var(--space-6);
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 0.875rem;
                color: var(--text-tertiary);
            ">
                <!-- Copyright -->
                <p>© <?= date('Y') ?> MarketFlow Pro. Tous droits réservés.</p>
                
                <!-- Réseaux sociaux -->
                <div class="flex gap-6">
                    <a href="#" style="color: var(--text-tertiary);">Twitter</a>
                    <a href="#" style="color: var(--text-tertiary);">LinkedIn</a>
                    <a href="#" style="color: var(--text-tertiary);">Instagram</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- =====================================================
         SCRIPTS JAVASCRIPT
         ===================================================== -->
    
    <!-- Script principal avec composants (Dropdown, utilitaires, etc.) -->
    <script src="<?= JS_URL ?>/app.js"></script>
    
    <!-- Système de notifications toast modernes -->
    <!-- Convertit automatiquement les messages flash PHP en toast -->
    <script src="<?= JS_URL ?>/notifications.js"></script>
    
  <!-- Chart.js Library v4.4.0 - Graphiques modernes pour dashboard vendeur -->
<!-- Utilisé dans : app/views/seller/dashboard.php -->
<!-- Documentation : https://www.chartjs.org/docs/latest/ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- =====================================================
         MENU UTILISATEUR DROPDOWN
         ===================================================== 
         
         Initialise le dropdown du menu utilisateur si connecté
         Utilise le composant Dropdown de app.js
         ===================================================== -->
    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
    <script>
    /**
     * Création du dropdown menu utilisateur
     * Points d'accès selon le rôle :
     * - Admin : Panel admin
     * - Seller : Dashboard vendeur
     * - Buyer : Compte acheteur
     */
    const userMenuBtn = document.getElementById('userMenuBtn');
    if (userMenuBtn) {
        const dropdown = new MarketFlow.Dropdown('#userMenuBtn');
        
        // Menu pour tous les utilisateurs
        dropdown.addItem('Mon profil', () => window.location.href = '/profile');
        dropdown.addItem('Mes commandes', () => window.location.href = '/orders');
        
        // Menu spécifique vendeur
        <?php if ($_SESSION['user_type'] === 'seller'): ?>
        dropdown.addItem('Dashboard vendeur', () => window.location.href = '/seller/dashboard');
        <?php endif; ?>
        
        // Déconnexion
        dropdown.addItem('Déconnexion', () => window.location.href = '/logout');
    }
    </script>
    <?php endif; ?>

</body>
</html>