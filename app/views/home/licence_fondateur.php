<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketPlace Pro — Code Source Marketplace PHP | 2 997€</title>
    <meta name="description" content="Code source complet d'une marketplace multi-vendeurs PHP 8.2 / PostgreSQL / Stripe Connect. En production depuis 3 mois. 23 349 lignes. Licence commerciale.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        :root {
            --black: #0a0a0a;
            --white: #fafafa;
            --accent: #c8f537;
            --accent-dark: #a8d420;
            --grey-100: #f0f0f0;
            --grey-200: #e0e0e0;
            --grey-400: #999;
            --grey-700: #444;
            --radius: 4px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--black);
            color: var(--white);
            line-height: 1.6;
            font-weight: 300;
        }

        /* ── BARRE TOP ── */
        .topbar {
            background: var(--accent);
            color: var(--black);
            text-align: center;
            padding: 10px 20px;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
        }

        /* ── NAV ── */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 60px;
            border-bottom: 1px solid #1a1a1a;
        }
        .logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            letter-spacing: -0.02em;
        }
        .logo span { color: var(--accent); }
        .nav-demo {
            color: var(--grey-400);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        .nav-demo:hover { color: var(--white); }

        /* ── HERO ── */
        .hero {
            padding: 100px 60px 80px;
            max-width: 1100px;
            margin: 0 auto;
        }
        .hero-tag {
            display: inline-block;
            border: 1px solid #333;
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 0.8rem;
            color: var(--grey-400);
            margin-bottom: 40px;
            letter-spacing: 0.05em;
        }
        .hero h1 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2.8rem, 6vw, 5.5rem);
            font-weight: 800;
            line-height: 1.0;
            letter-spacing: -0.03em;
            margin-bottom: 30px;
        }
        .hero h1 em {
            font-style: normal;
            color: var(--accent);
        }
        .hero-sub {
            font-size: 1.15rem;
            color: var(--grey-400);
            max-width: 560px;
            margin-bottom: 50px;
            font-weight: 300;
        }
        .hero-actions {
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }
        .btn-primary {
            background: var(--accent);
            color: var(--black);
            padding: 18px 40px;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            border-radius: var(--radius);
            letter-spacing: 0.02em;
            transition: all 0.2s;
            display: inline-block;
        }
        .btn-primary:hover {
            background: var(--accent-dark);
            transform: translateY(-2px);
        }
        .btn-demo {
            color: var(--grey-400);
            text-decoration: none;
            font-size: 0.95rem;
            border-bottom: 1px solid #333;
            padding-bottom: 2px;
            transition: all 0.2s;
        }
        .btn-demo:hover { color: var(--white); border-color: var(--white); }

        /* ── STATS BAR ── */
        .stats-bar {
            border-top: 1px solid #1a1a1a;
            border-bottom: 1px solid #1a1a1a;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            max-width: 1100px;
            margin: 0 auto;
        }
        .stat-item {
            padding: 40px 60px;
            border-right: 1px solid #1a1a1a;
        }
        .stat-item:last-child { border-right: none; }
        .stat-num {
            font-family: 'Syne', sans-serif;
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--accent);
            letter-spacing: -0.03em;
            line-height: 1;
            margin-bottom: 8px;
        }
        .stat-label {
            font-size: 0.85rem;
            color: var(--grey-400);
            font-weight: 300;
        }

        /* ── SECTION ── */
        .section {
            max-width: 1100px;
            margin: 0 auto;
            padding: 80px 60px;
            border-bottom: 1px solid #1a1a1a;
        }
        .section-tag {
            font-size: 0.75rem;
            color: var(--accent);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-family: 'Syne', sans-serif;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .section h2 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(1.8rem, 3.5vw, 2.8rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 40px;
            line-height: 1.1;
        }

        /* ── SCREENSHOT ── */
        .screenshot-wrap {
            border: 1px solid #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 16px;
            background: #111;
        }
        .screenshot-bar {
            background: #1a1a1a;
            padding: 10px 16px;
            display: flex;
            gap: 6px;
            align-items: center;
        }
        .dot { width: 10px; height: 10px; border-radius: 50%; }
        .dot-r { background: #ff5f57; }
        .dot-y { background: #febc2e; }
        .dot-g { background: #28c840; }
        .screenshot-wrap img {
            width: 100%;
            display: block;
        }
        .screenshot-caption {
            font-size: 0.8rem;
            color: var(--grey-400);
            text-align: center;
            margin-top: 8px;
        }
        .screenshots-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 40px;
        }

        /* ── FEATURES ── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1px;
            background: #1a1a1a;
            border: 1px solid #1a1a1a;
            margin-top: 40px;
        }
        .feature-item {
            background: var(--black);
            padding: 32px;
        }
        .feature-icon {
            font-size: 1.5rem;
            margin-bottom: 16px;
        }
        .feature-item h3 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 10px;
        }
        .feature-item p {
            font-size: 0.9rem;
            color: var(--grey-400);
            line-height: 1.6;
        }

        /* ── CODE BLOCK ── */
        .code-block {
            background: #0f0f0f;
            border: 1px solid #1a1a1a;
            border-radius: var(--radius);
            padding: 28px 32px;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            color: #888;
            overflow-x: auto;
            line-height: 1.8;
            margin-top: 30px;
        }
        .code-block .c-green { color: #28c840; }
        .code-block .c-accent { color: var(--accent); }
        .code-block .c-white { color: var(--white); }

        /* ── CHECKLIST ── */
        .checklist {
            list-style: none;
            margin-top: 30px;
        }
        .checklist li {
            padding: 14px 0;
            border-bottom: 1px solid #1a1a1a;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            font-size: 0.95rem;
        }
        .checklist li:last-child { border-bottom: none; }
        .check { color: var(--accent); font-weight: 700; flex-shrink: 0; }
        .cross { color: #555; font-weight: 700; flex-shrink: 0; }
        .checklist strong { color: var(--white); font-weight: 500; }
        .checklist span { color: var(--grey-400); }

        /* ── COMPARISON TABLE ── */
        .table-wrap {
            overflow-x: auto;
            margin-top: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        th {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            color: var(--grey-400);
            text-transform: uppercase;
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #1a1a1a;
        }
        td {
            padding: 16px 20px;
            border-bottom: 1px solid #1a1a1a;
            color: var(--grey-400);
        }
        tr.highlight td {
            color: var(--white);
            background: #111;
        }
        tr.highlight td:first-child { color: var(--accent); font-weight: 500; }

        /* ── FAQ ── */
        .faq-item {
            border-bottom: 1px solid #1a1a1a;
            padding: 28px 0;
        }
        .faq-item h3 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 12px;
            color: var(--white);
        }
        .faq-item p {
            color: var(--grey-400);
            font-size: 0.95rem;
        }

        /* ── PRICING ── */
        .pricing-card {
            border: 1px solid #2a2a2a;
            border-radius: 8px;
            padding: 50px;
            max-width: 580px;
            margin: 40px auto 0;
            background: #0f0f0f;
        }
        .pricing-label {
            font-size: 0.8rem;
            color: var(--grey-400);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-family: 'Syne', sans-serif;
            margin-bottom: 16px;
        }
        .pricing-amount {
            display: flex;
            align-items: baseline;
            gap: 16px;
            margin-bottom: 10px;
        }
        .price-main {
            font-family: 'Syne', sans-serif;
            font-size: 4rem;
            font-weight: 800;
            color: var(--accent);
            letter-spacing: -0.03em;
        }
        .price-old {
            font-size: 1.5rem;
            color: #444;
            text-decoration: line-through;
        }
        .price-note {
            font-size: 0.85rem;
            color: var(--grey-400);
            margin-bottom: 36px;
        }
        .pricing-card .btn-primary {
            width: 100%;
            text-align: center;
            font-size: 1.05rem;
            padding: 20px;
            margin-bottom: 20px;
        }
        .pricing-footer {
            font-size: 0.8rem;
            color: var(--grey-400);
            text-align: center;
        }

        /* ── CTA FINAL ── */
        .cta-section {
            background: var(--accent);
            color: var(--black);
            padding: 100px 60px;
            text-align: center;
        }
        .cta-section h2 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2rem, 4vw, 3.5rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 20px;
        }
        .cta-section p {
            font-size: 1.1rem;
            opacity: 0.7;
            margin-bottom: 40px;
        }
        .btn-dark {
            background: var(--black);
            color: var(--accent);
            padding: 18px 40px;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            border-radius: var(--radius);
            display: inline-block;
            transition: all 0.2s;
        }
        .btn-dark:hover { opacity: 0.85; transform: translateY(-2px); }

        /* ── FOOTER ── */
        footer {
            padding: 40px 60px;
            border-top: 1px solid #1a1a1a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: var(--grey-400);
        }
        footer a { color: var(--grey-400); text-decoration: none; }
        footer a:hover { color: var(--white); }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            nav, .hero, .section { padding-left: 24px; padding-right: 24px; }
            .stats-bar { grid-template-columns: 1fr 1fr; }
            .stat-item { padding: 30px 24px; }
            .features-grid { grid-template-columns: 1fr; }
            .screenshots-grid { grid-template-columns: 1fr; }
            .pricing-card { padding: 32px 24px; }
            footer { flex-direction: column; gap: 16px; text-align: center; }
        }
    </style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
    🎯 LICENCE FONDATEUR — 2 997€ au lieu de 4 997€ &nbsp;·&nbsp; Accès code source complet &nbsp;·&nbsp; Démo live : www.marketflow.fr
</div>

<!-- NAV -->
<nav>
    <div class="logo">Market<span>Place</span> Pro</div>
    <a href="https://www.marketflow.fr" target="_blank" class="nav-demo">Voir la démo →</a>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-tag">Code source · PHP 8.2 · PostgreSQL · Stripe Connect</div>
    <h1>Une marketplace<br><em>prête à déployer.</em><br>Code complet inclus.</h1>
    <p class="hero-sub">23 349 lignes PHP. Architecture MVC sans framework. En production depuis 3 mois. Vous recevez le code source complet et la licence commerciale.</p>
    <div class="hero-actions">
        <a href="https://buy.stripe.com/cNi00l40003ObSx93i6J202" class="btn-primary">Obtenir la licence — 2 997€ →</a>
        <a href="https://www.marketflow.fr" target="_blank" class="btn-demo">Tester la démo live</a>
    </div>
</section>

<!-- STATS BAR -->
<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-num">23 349</div>
        <div class="stat-label">Lignes de code PHP</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">3 mois</div>
        <div class="stat-label">En production live</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">139</div>
        <div class="stat-label">Requêtes préparées (0 SQLi)</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">15/15</div>
        <div class="stat-label">Tests PHPUnit passants</div>
    </div>
</div>

<!-- SCREENSHOTS -->
<section class="section">
    <div class="section-tag">Démo Live</div>
    <h2>Ce que vous déployez.</h2>
    <p style="color: var(--grey-400);">Chaque fonctionnalité visible sur <a href="https://www.marketflow.fr" style="color: var(--white);">www.marketflow.fr</a> est dans le code que vous recevez.</p>

    <div class="screenshots-grid">
        <div>
            <div class="screenshot-wrap">
                <div class="screenshot-bar">
                    <div class="dot dot-r"></div>
                    <div class="dot dot-y"></div>
                    <div class="dot dot-g"></div>
                </div>
                <img src="/docs/screenshots/homepage.png" alt="Page d'accueil MarketPlace Pro">
            </div>
            <div class="screenshot-caption">Page d'accueil</div>
        </div>
        <div>
            <div class="screenshot-wrap">
                <div class="screenshot-bar">
                    <div class="dot dot-r"></div>
                    <div class="dot dot-y"></div>
                    <div class="dot dot-g"></div>
                </div>
                <img src="/docs/screenshots/admin_dashboard.png" alt="Dashboard Admin">
            </div>
            <div class="screenshot-caption">Dashboard Admin</div>
        </div>
        <div>
            <div class="screenshot-wrap">
                <div class="screenshot-bar">
                    <div class="dot dot-r"></div>
                    <div class="dot dot-y"></div>
                    <div class="dot dot-g"></div>
                </div>
                <img src="/docs/screenshots/categories.png" alt="Page catégories">
            </div>
            <div class="screenshot-caption">Catalogue & Catégories</div>
        </div>
        <div>
            <div class="screenshot-wrap">
                <div class="screenshot-bar">
                    <div class="dot dot-r"></div>
                    <div class="dot dot-y"></div>
                    <div class="dot dot-g"></div>
                </div>
                <img src="/docs/screenshots/security_dashboard.png" alt="Dashboard Sécurité">
            </div>
            <div class="screenshot-caption">Dashboard Sécurité (unique)</div>
        </div>
    </div>
</section>

<!-- CE QUE VOUS RECEVEZ -->
<section class="section">
    <div class="section-tag">Livraison</div>
    <h2>Ce que vous recevez.</h2>

    <ul class="checklist">
        <li><span class="check">✓</span><div><strong>Code source complet</strong> <span>— 23 349 lignes PHP 8.2, commentées en français, architecture MVC propre</span></div></li>
        <li><span class="check">✓</span><div><strong>Accès GitHub privé</strong> <span>— dépôt privé partagé + archive ZIP sécurisée</span></div></li>
        <li><span class="check">✓</span><div><strong>Schéma PostgreSQL complet</strong> <span>— tables, index, relations, données démo incluses</span></div></li>
        <li><span class="check">✓</span><div><strong>Stripe Connect configuré</strong> <span>— paiements multi-vendeurs, webhooks, commissions automatiques</span></div></li>
        <li><span class="check">✓</span><div><strong>Dockerfile + Railway config</strong> <span>— déploiement en production en moins de 15 minutes</span></div></li>
        <li><span class="check">✓</span><div><strong>Documentation installation</strong> <span>— README, INSTALL, variables d'environnement</span></div></li>
        <li><span class="check">✓</span><div><strong>Licence commerciale</strong> <span>— usage illimité pour vos projets clients, modifications autorisées</span></div></li>
        <li><span class="check">✓</span><div><strong>Support email</strong> <span>— questions sur le code et l'installation, contact@marketflow.fr</span></div></li>
        <li><span class="cross">✗</span><div><strong style="color: #555;">Revente du code source</strong> <span>— le code reste confidentiel</span></div></li>
    </ul>
</section>

<!-- FONCTIONNALITÉS -->
<section class="section">
    <div class="section-tag">Fonctionnalités</div>
    <h2>Tout est dedans.</h2>

    <div class="features-grid">
        <div class="feature-item">
            <div class="feature-icon">💳</div>
            <h3>Stripe Connect</h3>
            <p>Paiements multi-vendeurs avec split automatique. Webhooks, remboursements, TVA. Argent réel en production.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🔒</div>
            <h3>Dashboard Sécurité</h3>
            <p>Monitoring temps réel : tentatives CSRF, XSS, SQLi. Scoring IP suspectes. Alertes email. Inexistant ailleurs.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">👥</div>
            <h3>Multi-Vendeurs</h3>
            <p>Inscription vendeurs, upload produits, dashboard analytics, gestion commandes et commissions.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">👑</div>
            <h3>Panel Admin</h3>
            <p>Gestion utilisateurs, validation produits, modération avis, statistiques globales, outils export.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🛒</div>
            <h3>Panier & Checkout</h3>
            <p>Panier session persistante, codes promo, calcul TVA, flux Stripe Checkout complet.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">⭐</div>
            <h3>Avis & Wishlist</h3>
            <p>Système d'avis et notes, modération, wishlist, téléchargements sécurisés (3x par produit).</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🌙</div>
            <h3>Dark Mode</h3>
            <p>Mode sombre natif avec toggle. CSS variables pour théming facile. 100% responsive.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">📊</div>
            <h3>Analytics</h3>
            <p>Graphiques Chart.js dans les dashboards vendeur et admin. Revenus, commandes, tendances 7 jours.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🔧</div>
            <h3>Architecture MVC</h3>
            <p>Pur PHP sans framework. Router, CSRF, RateLimiter, SecurityLogger — tout codé from scratch.</p>
        </div>
    </div>
</section>

<!-- TESTS -->
<section class="section">
    <div class="section-tag">Qualité</div>
    <h2>Tests unitaires inclus.</h2>
    <p style="color: var(--grey-400);">15 tests PHPUnit passants. Chaque composant critique est couvert.</p>

    <div class="code-block">
<span class="c-white">PHPUnit 10.5 — MarketPlace Pro</span>

<span class="c-accent">Protection CSRF</span>
  <span class="c-green">✔</span> Génération token (random_bytes + hash_equals)
  <span class="c-green">✔</span> Validation token valide
  <span class="c-green">✔</span> Rejet token invalide

<span class="c-accent">Logique Panier</span>
  <span class="c-green">✔</span> Calcul total prix
  <span class="c-green">✔</span> Calcul avec TVA (20% française)
  <span class="c-green">✔</span> Gestion panier vide
  <span class="c-green">✔</span> Arrondi prix (2 décimales)

<span class="c-accent">Sécurité & Helpers</span>
  <span class="c-green">✔</span> Protection XSS (échappement balises script)
  <span class="c-green">✔</span> Validation URL
  <span class="c-green">✔</span> Nettoyage inputs

<span class="c-accent">Validation Utilisateur</span>
  <span class="c-green">✔</span> Validation email
  <span class="c-green">✔</span> Rejet email invalide
  <span class="c-green">✔</span> Validation username

<span class="c-white">Tests: 15/15 ✅ &nbsp;|&nbsp; Assertions: 35 &nbsp;|&nbsp; Échecs: 0</span>
    </div>
</section>

<!-- COMPARAISON -->
<section class="section">
    <div class="section-tag">Comparaison</div>
    <h2>Le calcul est simple.</h2>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Option</th>
                    <th>Coût</th>
                    <th>Délai</th>
                    <th>Contrôle</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Développeur freelance</td>
                    <td>15 000€ – 40 000€</td>
                    <td>3 – 6 mois</td>
                    <td>Dépendance</td>
                </tr>
                <tr>
                    <td>Agence web</td>
                    <td>50 000€ – 150 000€</td>
                    <td>6 – 12 mois</td>
                    <td>Aucun</td>
                </tr>
                <tr>
                    <td>Sharetribe / SaaS</td>
                    <td>300€ – 1 000€ / mois</td>
                    <td>Immédiat</td>
                    <td>Aucun (code fermé)</td>
                </tr>
                <tr>
                    <td>CS-Cart Multi-Vendor</td>
                    <td>3 590€+ (sans Stripe natif)</td>
                    <td>Immédiat</td>
                    <td>Partiel</td>
                </tr>
                <tr class="highlight">
                    <td>MarketPlace Pro</td>
                    <td>2 997€ (paiement unique)</td>
                    <td>24h max</td>
                    <td>Total (code source)</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<!-- LICENCE -->
<section class="section">
    <div class="section-tag">Licence</div>
    <h2>Usage commercial illimité.</h2>
    <p style="color: var(--grey-400); margin-bottom: 30px;">Vous achetez une licence non-exclusive. Vous pouvez déployer pour autant de clients que vous voulez.</p>

    <ul class="checklist">
        <li><span class="check">✓</span><div><strong>Déploiement multi-clients</strong> <span>— installez pour autant de clients que vous voulez</span></div></li>
        <li><span class="check">✓</span><div><strong>Modifications</strong> <span>— personnalisez, rebrandez, étendez le code</span></div></li>
        <li><span class="check">✓</span><div><strong>Usage commercial</strong> <span>— facturez vos clients, gardez 100% des revenus</span></div></li>
        <li><span class="cross">✗</span><div><strong style="color: #555;">Redistribution</strong> <span>— le code source ne peut pas être revendu ou distribué</span></div></li>
        <li><span class="cross">✗</span><div><strong style="color: #555;">Produit concurrent</strong> <span>— ne pas créer un produit concurrent basé sur ce code</span></div></li>
    </ul>

    <p style="margin-top: 24px; font-size: 0.85rem; color: var(--grey-400);">Loi française applicable. Tribunaux de Béziers compétents. Contrat complet fourni à l'achat.</p>
</section>

<!-- FAQ -->
<section class="section">
    <div class="section-tag">FAQ</div>
    <h2>Questions fréquentes.</h2>

    <div class="faq-item">
        <h3>Le code est vraiment en production ?</h3>
        <p>Oui. www.marketflow.fr tourne en production depuis 3 mois sur Railway avec de vrais paiements Stripe. Zéro bug critique connu.</p>
    </div>
    <div class="faq-item">
        <h3>Pourquoi sans framework ?</h3>
        <p>Contrôle total. Pas de dépendances à mettre à jour, pas de magie black-box, pas de bloat. Vous comprenez chaque ligne. Facile à modifier, facile à maintenir.</p>
    </div>
    <div class="faq-item">
        <h3>Combien de temps pour déployer ?</h3>
        <p>15 minutes avec Docker. Documentation d'installation complète incluse. Si vous avez déjà Railway ou un VPS avec PostgreSQL, c'est encore plus rapide.</p>
    </div>
    <div class="faq-item">
        <h3>Je peux voir le code avant d'acheter ?</h3>
        <p>Testez toutes les fonctionnalités sur www.marketflow.fr. Les stats du code (lignes, tests, requêtes) sont vérifiables après livraison. Aucun remboursement après livraison du code source.</p>
    </div>
    <div class="faq-item">
        <h3>Quel hébergement nécessaire ?</h3>
        <p>N'importe quel VPS avec PHP 8.2+ et PostgreSQL. Testé sur Railway (5€/mois), DigitalOcean, AWS. Le Dockerfile est fourni.</p>
    </div>
    <div class="faq-item">
        <h3>Le code est commenté en quelle langue ?</h3>
        <p>Les commentaires sont en français. Le code lui-même (variables, fonctions, architecture) est universel et auto-explicatif.</p>
    </div>
    <div class="faq-item">
        <h3>Qu'est-ce que la Licence Fondateur ?</h3>
        <p>Les premières licences sont proposées à 2 997€ au lieu du prix standard de 4 997€. Ce tarif est limité et peut évoluer à tout moment.</p>
    </div>
</section>

<!-- PRICING -->
<section class="section" style="border-bottom: none;">
    <div class="section-tag">Tarif</div>
    <h2>Licence Fondateur.</h2>

    <div class="pricing-card">
        <div class="pricing-label">Licence Fondateur — Paiement unique</div>
        <div class="pricing-amount">
            <div class="price-main">2 997€</div>
            <div class="price-old">4 997€</div>
        </div>
        <div class="price-note">TTC · Livraison sous 24h · Licence perpétuelle</div>

        <a href="https://buy.stripe.com/cNi00l40003ObSx93i6J202" class="btn-primary">
            Obtenir la licence →
        </a>

        <ul class="checklist" style="margin-top: 0;">
            <li><span class="check">✓</span><span>Code source complet (GitHub privé + ZIP)</span></li>
            <li><span class="check">✓</span><span>Stripe Connect multi-vendeurs configuré</span></li>
            <li><span class="check">✓</span><span>Licence commerciale (clients illimités)</span></li>
            <li><span class="check">✓</span><span>Documentation installation complète</span></li>
            <li><span class="check">✓</span><span>Support email — contact@marketflow.fr</span></li>
        </ul>

        <div class="pricing-footer" style="margin-top: 24px;">
            Paiement sécurisé via Stripe · Facture fournie sous 48h
        </div>
    </div>
</section>

<!-- CTA FINAL -->
<div class="cta-section">
    <h2>Prêt à déployer<br>votre marketplace ?</h2>
    <p>Code source complet. En production depuis 3 mois. Licence commerciale.</p>
    <a href="https://buy.stripe.com/cNi00l40003ObSx93i6J202" class="btn-dark">Obtenir la licence — 2 997€ →</a>
</div>

<!-- FOOTER -->
<footer>
    <div>MarketPlace Pro © 2026 — A. Devance</div>
    <div>
        <a href="https://www.marketflow.fr" target="_blank">Démo live</a> &nbsp;·&nbsp;
        <a href="mailto:contact@marketflow.fr">contact@marketflow.fr</a>
    </div>
</footer>

</body>
</html>
