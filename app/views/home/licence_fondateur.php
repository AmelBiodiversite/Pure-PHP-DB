<?php
/**
 * Page de vente - Licence Fondateur MarketPlace Pro (FR)
 * Fragment PHP : chargée via render() — PAS de html/head/body
 * Le nav/footer MarketFlow sont cachés via CSS
 */
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
/* Cache le nav et footer MarketFlow sur cette page */
nav, body > footer { display: none !important; }

body {
    background: #f8f9fb !important;
    color: #1e293b !important;
    font-family: 'Inter', sans-serif !important;
    margin: 0 !important;
    padding: 0 !important;
}

.lp { --blue:#2563eb; --blue-dark:#1d4ed8; --blue-light:#eff6ff; --green:#16a34a;
      --grey-50:#f8f9fb; --grey-100:#f1f3f7; --grey-200:#e2e6ed; --grey-400:#94a3b8;
      --grey-600:#475569; --grey-800:#1e293b; --white:#fff;
      --radius:8px; --radius-lg:14px;
      --shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06);
      --shadow-lg:0 8px 32px rgba(0,0,0,.10); }

/* TOPBAR */
.lp-topbar { background:var(--blue); color:#fff; text-align:center; padding:12px 20px;
    font-family:'Syne',sans-serif; font-weight:700; font-size:.85rem; letter-spacing:.03em; }

/* NAV */
.lp-nav { background:var(--white); border-bottom:1px solid var(--grey-200);
    display:flex; justify-content:space-between; align-items:center; padding:18px 60px; }
.lp-logo { font-family:'Syne',sans-serif; font-weight:800; font-size:1.2rem;
    color:var(--grey-800); text-decoration:none; }
.lp-logo span { color:var(--blue); }
.lp-nav-link { color:var(--grey-600); text-decoration:none; font-size:.9rem;
    font-weight:500; transition:color .2s; }
.lp-nav-link:hover { color:var(--blue); }

/* HERO */
.lp-hero { background:var(--white); border-bottom:1px solid var(--grey-200);
    padding:80px 60px 70px; text-align:center; }
.lp-badge { display:inline-block; background:var(--blue-light); color:var(--blue);
    border:1px solid #bfdbfe; padding:6px 16px; border-radius:100px;
    font-size:.8rem; font-weight:600; letter-spacing:.04em; margin-bottom:32px; }
.lp-hero h1 { font-family:'Syne',sans-serif; font-size:clamp(2.4rem,5vw,4.2rem);
    font-weight:800; line-height:1.08; letter-spacing:-.03em; color:var(--grey-800);
    margin:0 auto 24px; max-width:820px; }
.lp-hero h1 em { font-style:normal; color:var(--blue); }
.lp-hero-sub { font-size:1.1rem; color:var(--grey-600); max-width:560px;
    margin:0 auto 40px; line-height:1.7; }
.lp-actions { display:flex; align-items:center; justify-content:center; gap:20px; flex-wrap:wrap; }
.lp-btn-primary { background:var(--blue); color:#fff; padding:16px 36px;
    font-family:'Syne',sans-serif; font-weight:700; font-size:1rem;
    text-decoration:none; border-radius:var(--radius); transition:all .2s; display:inline-block; }
.lp-btn-primary:hover { background:var(--blue-dark); transform:translateY(-2px);
    box-shadow:0 8px 24px rgba(37,99,235,.25); }
.lp-btn-ghost { color:var(--grey-600); text-decoration:none; font-size:.95rem;
    font-weight:500; border-bottom:1px solid var(--grey-200); padding-bottom:2px; transition:all .2s; }
.lp-btn-ghost:hover { color:var(--blue); border-color:var(--blue); }

/* STATS */
.lp-stats { display:grid; grid-template-columns:repeat(4,1fr);
    border-bottom:1px solid var(--grey-200); background:var(--white); }
.lp-stat { padding:36px 40px; border-right:1px solid var(--grey-200); text-align:center; }
.lp-stat:last-child { border-right:none; }
.lp-stat-num { font-family:'Syne',sans-serif; font-size:2.4rem; font-weight:800;
    color:var(--blue); letter-spacing:-.02em; line-height:1; margin-bottom:6px; }
.lp-stat-label { font-size:.82rem; color:var(--grey-400); }

/* SECTIONS */
.lp-section { max-width:1100px; margin:0 auto; padding:72px 60px;
    border-bottom:1px solid var(--grey-200); }
.lp-section-bg { background:var(--grey-50); max-width:100%; padding:72px 0; }
.lp-section-bg > .lp-inner { max-width:1100px; margin:0 auto; padding:0 60px; }
.lp-section-tag { font-size:.72rem; color:var(--blue); letter-spacing:.12em;
    text-transform:uppercase; font-family:'Syne',sans-serif; font-weight:700; margin-bottom:12px; }
.lp-section h2, .lp-section-bg h2 { font-family:'Syne',sans-serif;
    font-size:clamp(1.6rem,3vw,2.4rem); font-weight:800; letter-spacing:-.02em;
    color:var(--grey-800); margin-bottom:32px; line-height:1.15; }

/* SCREENSHOTS */
.lp-screenshots { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:36px; }
.lp-sc-card { border:1px solid var(--grey-200); border-radius:var(--radius-lg);
    overflow:hidden; background:var(--grey-50); box-shadow:var(--shadow); }
.lp-sc-bar { background:var(--grey-100); padding:10px 14px; display:flex;
    gap:6px; align-items:center; border-bottom:1px solid var(--grey-200); }
.lp-dot { width:10px; height:10px; border-radius:50%; }
.lp-dot-r { background:#ff5f57; } .lp-dot-y { background:#febc2e; } .lp-dot-g { background:#28c840; }
.lp-sc-card img { width:100%; display:block; }
.lp-sc-caption { font-size:.8rem; color:var(--grey-400); text-align:center; padding:10px; }

/* CHECKLIST */
.lp-checklist { list-style:none; margin:0; padding:0; }
.lp-checklist li { padding:16px 0; border-bottom:1px solid var(--grey-100);
    display:flex; align-items:flex-start; gap:14px; font-size:.95rem; color:var(--grey-600); }
.lp-checklist li:last-child { border-bottom:none; }
.lp-check { color:#16a34a; font-weight:700; flex-shrink:0; }
.lp-cross { color:var(--grey-400); font-weight:700; flex-shrink:0; }
.lp-checklist strong { color:var(--grey-800); font-weight:600; }

/* FEATURES */
.lp-features { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:32px; }
.lp-feature { background:var(--white); border:1px solid var(--grey-200);
    border-radius:var(--radius-lg); padding:28px; transition:box-shadow .2s; }
.lp-feature:hover { box-shadow:var(--shadow); }
.lp-feature-icon { font-size:1.5rem; margin-bottom:14px; }
.lp-feature h3 { font-family:'Syne',sans-serif; font-weight:700; font-size:.95rem;
    color:var(--grey-800); margin-bottom:8px; }
.lp-feature p { font-size:.88rem; color:var(--grey-600); line-height:1.6; }

/* CODE BLOCK */
.lp-code { background:#0f172a; border-radius:var(--radius-lg); padding:28px 32px;
    font-family:'Courier New',monospace; font-size:.84rem; color:#94a3b8;
    overflow-x:auto; line-height:1.9; margin-top:28px; }
.lp-code .c-green { color:#4ade80; }
.lp-code .c-blue  { color:#60a5fa; }
.lp-code .c-white { color:#f1f5f9; }

/* TABLE */
.lp-table-wrap { overflow-x:auto; margin-top:32px; }
.lp-table { width:100%; border-collapse:collapse; font-size:.9rem; }
.lp-table th { font-family:'Syne',sans-serif; font-weight:700; font-size:.75rem;
    letter-spacing:.06em; color:var(--grey-400); text-transform:uppercase;
    padding:14px 20px; text-align:left; border-bottom:2px solid var(--grey-200);
    background:var(--grey-50); }
.lp-table td { padding:14px 20px; border-bottom:1px solid var(--grey-100); color:var(--grey-600); }
.lp-table tr.lp-hl td { background:var(--blue-light); color:var(--grey-800); font-weight:500; }
.lp-table tr.lp-hl td:first-child { color:var(--blue); font-weight:700; }

/* FAQ */
.lp-faq { border-bottom:1px solid var(--grey-100); padding:24px 0; }
.lp-faq h3 { font-family:'Syne',sans-serif; font-weight:700; font-size:.95rem;
    color:var(--grey-800); margin-bottom:10px; }
.lp-faq p { font-size:.92rem; color:var(--grey-600); line-height:1.7; }

/* PRICING */
.lp-pricing-card { border:2px solid var(--blue); border-radius:var(--radius-lg);
    padding:48px; max-width:560px; margin:36px auto 0; background:var(--white);
    box-shadow:var(--shadow-lg); text-align:center; }
.lp-pricing-label { font-size:.78rem; color:var(--blue); letter-spacing:.1em;
    text-transform:uppercase; font-family:'Syne',sans-serif; font-weight:700; margin-bottom:20px; }
.lp-price-row { display:flex; align-items:baseline; justify-content:center;
    gap:14px; margin-bottom:8px; }
.lp-price-main { font-family:'Syne',sans-serif; font-size:4rem; font-weight:800;
    color:var(--blue); letter-spacing:-.03em; }
.lp-price-old { font-size:1.4rem; color:var(--grey-400); text-decoration:line-through; }
.lp-price-note { font-size:.84rem; color:var(--grey-400); margin-bottom:32px; }
.lp-pricing-card .lp-btn-primary { width:100%; font-size:1.05rem; padding:18px;
    margin-bottom:24px; text-align:center; }
.lp-pricing-footer { font-size:.78rem; color:var(--grey-400); margin-top:16px; }

/* CTA */
.lp-cta { background:var(--blue); color:#fff; padding:90px 60px; text-align:center; }
.lp-cta h2 { font-family:'Syne',sans-serif; font-size:clamp(1.8rem,3.5vw,3rem);
    font-weight:800; letter-spacing:-.02em; margin-bottom:16px; }
.lp-cta p { font-size:1.05rem; opacity:.8; margin-bottom:36px; }
.lp-btn-white { background:#fff; color:var(--blue); padding:16px 36px;
    font-family:'Syne',sans-serif; font-weight:700; font-size:1rem;
    text-decoration:none; border-radius:var(--radius); display:inline-block; transition:all .2s; }
.lp-btn-white:hover { opacity:.92; transform:translateY(-2px); }

/* FOOTER */
.lp-footer { background:var(--grey-800); color:var(--grey-400); padding:32px 60px;
    display:flex; justify-content:space-between; align-items:center; font-size:.84rem; }
.lp-footer a { color:var(--grey-400); text-decoration:none; }
.lp-footer a:hover { color:#fff; }

/* RESPONSIVE */
@media (max-width:768px) {
    .lp-nav,.lp-hero,.lp-section { padding-left:24px; padding-right:24px; }
    .lp-stats { grid-template-columns:1fr 1fr; }
    .lp-stat { padding:24px; }
    .lp-features,.lp-screenshots { grid-template-columns:1fr; }
    .lp-pricing-card { padding:32px 24px; }
    .lp-footer { flex-direction:column; gap:12px; text-align:center; }
    .lp-cta { padding:60px 24px; }
}
</style>

<div class="lp">

<!-- TOPBAR -->
<div class="lp-topbar">
    🎯 LICENCE FONDATEUR — 2 997€ au lieu de 4 997€ &nbsp;·&nbsp; Code source complet &nbsp;·&nbsp; Démo live : www.marketflow.fr
</div>

<!-- NAV -->
<div class="lp-nav">
    <a href="/" class="lp-logo">Market<span>Place</span> Pro</a>
    <a href="https://www.marketflow.fr" target="_blank" class="lp-nav-link">Voir la démo →</a>
</div>

<!-- HERO -->
<div class="lp-hero">
    <div class="lp-badge">Code source · PHP 8.2 · PostgreSQL · Stripe Connect</div>
    <h1>Une marketplace<br><em>prête à déployer.</em><br>Code complet inclus.</h1>
    <p class="lp-hero-sub">23 349 lignes PHP. Architecture MVC sans framework. En production depuis 3 mois. Vous recevez le code source complet et la licence commerciale.</p>
    <div class="lp-actions">
        <a href="https://buy.stripe.com/cNi00l40003ObSx93i6J202" class="lp-btn-primary">Obtenir la licence — 2 997€ →</a>
        <a href="https://www.marketflow.fr" target="_blank" class="lp-btn-ghost">Tester la démo live</a>
    </div>
</div>

<!-- STATS -->
<div class="lp-stats">
    <div class="lp-stat"><div class="lp-stat-num">23 349</div><div class="lp-stat-label">Lignes de code PHP</div></div>
    <div class="lp-stat"><div class="lp-stat-num">3 mois</div><div class="lp-stat-label">En production live</div></div>
    <div class="lp-stat"><div class="lp-stat-num">139</div><div class="lp-stat-label">Requêtes préparées (0 SQLi)</div></div>
    <div class="lp-stat"><div class="lp-stat-num">15/15</div><div class="lp-stat-label">Tests PHPUnit passants</div></div>
</div>

<!-- SCREENSHOTS -->
<div class="lp-section">
    <div class="lp-section-tag">Démo Live</div>
    <h2>Ce que vous déployez.</h2>
    <p style="color:#475569;">Chaque fonctionnalité visible sur <a href="https://www.marketflow.fr" style="color:#2563eb;font-weight:500;">www.marketflow.fr</a> est dans le code que vous recevez.</p>
    <div class="lp-screenshots">
        <div><div class="lp-sc-card"><div class="lp-sc-bar"><div class="lp-dot lp-dot-r"></div><div class="lp-dot lp-dot-y"></div><div class="lp-dot lp-dot-g"></div></div><img src="/docs/screenshots/homepage.png" alt="Page d'accueil"></div><div class="lp-sc-caption">Page d'accueil</div></div>
        <div><div class="lp-sc-card"><div class="lp-sc-bar"><div class="lp-dot lp-dot-r"></div><div class="lp-dot lp-dot-y"></div><div class="lp-dot lp-dot-g"></div></div><img src="/docs/screenshots/admin_dashboard.png" alt="Dashboard Admin"></div><div class="lp-sc-caption">Dashboard Admin</div></div>
        <div><div class="lp-sc-card"><div class="lp-sc-bar"><div class="lp-dot lp-dot-r"></div><div class="lp-dot lp-dot-y"></div><div class="lp-dot lp-dot-g"></div></div><img src="/docs/screenshots/categories.png" alt="Catégories"></div><div class="lp-sc-caption">Catalogue & Catégories</div></div>
        <div><div class="lp-sc-card"><div class="lp-sc-bar"><div class="lp-dot lp-dot-r"></div><div class="lp-dot lp-dot-y"></div><div class="lp-dot lp-dot-g"></div></div><img src="/docs/screenshots/security_dashboard.png" alt="Dashboard Sécurité"></div><div class="lp-sc-caption">Dashboard Sécurité (unique)</div></div>
    </div>
</div>

<!-- CE QUE VOUS RECEVEZ -->
<div class="lp-section" style="background:var(--grey-50);max-width:100%;padding-top:72px;padding-bottom:72px;">
<div style="max-width:1100px;margin:0 auto;padding:0 60px;">
    <div class="lp-section-tag">Livraison</div>
    <h2>Ce que vous recevez.</h2>
    <ul class="lp-checklist">
        <li><span class="lp-check">✓</span><div><strong>Code source complet</strong> — 23 349 lignes PHP 8.2, commentées en français, architecture MVC propre</div></li>
        <li><span class="lp-check">✓</span><div><strong>Accès GitHub privé</strong> — dépôt privé partagé + archive ZIP sécurisée</div></li>
        <li><span class="lp-check">✓</span><div><strong>Schéma PostgreSQL complet</strong> — tables, index, relations, données démo incluses</div></li>
        <li><span class="lp-check">✓</span><div><strong>Stripe Connect configuré</strong> — paiements multi-vendeurs, webhooks, commissions automatiques</div></li>
        <li><span class="lp-check">✓</span><div><strong>Dockerfile + Railway config</strong> — déploiement en production en moins de 15 minutes</div></li>
        <li><span class="lp-check">✓</span><div><strong>Documentation installation</strong> — README, INSTALL, variables d'environnement</div></li>
        <li><span class="lp-check">✓</span><div><strong>Licence commerciale</strong> — usage illimité pour vos projets clients, modifications autorisées</div></li>
        <li><span class="lp-check">✓</span><div><strong>Support email</strong> — contact@marketflow.fr</div></li>
        <li><span class="lp-cross">✗</span><div><strong style="color:#94a3b8;">Revente du code source</strong> — le code reste confidentiel</div></li>
    </ul>
</div>
</div>

<!-- FONCTIONNALITÉS -->
<div class="lp-section">
    <div class="lp-section-tag">Fonctionnalités</div>
    <h2>Tout est dedans.</h2>
    <div class="lp-features">
        <div class="lp-feature"><div class="lp-feature-icon">💳</div><h3>Stripe Connect</h3><p>Paiements multi-vendeurs avec split automatique. Webhooks, remboursements, TVA. Argent réel en production.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">🔒</div><h3>Dashboard Sécurité</h3><p>Monitoring temps réel : CSRF, XSS, SQLi. Scoring IP suspectes. Alertes email. Inexistant ailleurs.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">👥</div><h3>Multi-Vendeurs</h3><p>Inscription vendeurs, upload produits, dashboard analytics, gestion commandes et commissions.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">👑</div><h3>Panel Admin</h3><p>Gestion utilisateurs, validation produits, modération avis, statistiques globales, outils export.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">🛒</div><h3>Panier & Checkout</h3><p>Panier session persistante, codes promo, calcul TVA, flux Stripe Checkout complet.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">⭐</div><h3>Avis & Wishlist</h3><p>Système d'avis et notes, modération, wishlist, téléchargements sécurisés.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">🌙</div><h3>Dark Mode</h3><p>Mode sombre natif avec toggle. CSS variables pour théming facile. 100% responsive.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">📊</div><h3>Analytics</h3><p>Graphiques Chart.js dans les dashboards vendeur et admin. Revenus, commandes, tendances 7 jours.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">🔧</div><h3>Architecture MVC</h3><p>Pur PHP sans framework. Router, CSRF, RateLimiter, SecurityLogger — tout codé from scratch.</p></div>
    </div>
</div>

<!-- TESTS -->
<div style="background:var(--grey-50);padding:72px 0;">
<div style="max-width:1100px;margin:0 auto;padding:0 60px;">
    <div class="lp-section-tag">Qualité</div>
    <h2 style="font-family:'Syne',sans-serif;font-size:clamp(1.6rem,3vw,2.4rem);font-weight:800;letter-spacing:-.02em;color:var(--grey-800);margin-bottom:16px;">Tests unitaires inclus.</h2>
    <p style="color:#475569;">15 tests PHPUnit passants. Chaque composant critique est couvert.</p>
    <div class="lp-code">
<span class="c-white">PHPUnit 10.5 — MarketPlace Pro</span>

<span class="c-blue">Protection CSRF</span>
  <span class="c-green">✔</span> Génération token (random_bytes + hash_equals)
  <span class="c-green">✔</span> Validation token valide
  <span class="c-green">✔</span> Rejet token invalide

<span class="c-blue">Logique Panier</span>
  <span class="c-green">✔</span> Calcul total prix
  <span class="c-green">✔</span> Calcul avec TVA (20% française)
  <span class="c-green">✔</span> Gestion panier vide
  <span class="c-green">✔</span> Arrondi prix (2 décimales)

<span class="c-blue">Sécurité & Helpers</span>
  <span class="c-green">✔</span> Protection XSS
  <span class="c-green">✔</span> Validation URL
  <span class="c-green">✔</span> Nettoyage inputs

<span class="c-blue">Validation Utilisateur</span>
  <span class="c-green">✔</span> Validation email
  <span class="c-green">✔</span> Rejet email invalide
  <span class="c-green">✔</span> Validation username

<span class="c-white">Tests: 15/15 ✅  |  Assertions: 35  |  Échecs: 0</span>
    </div>
</div>
</div>

<!-- COMPARAISON -->
<div class="lp-section">
    <div class="lp-section-tag">Comparaison</div>
    <h2>Le calcul est simple.</h2>
    <div class="lp-table-wrap">
        <table class="lp-table">
            <thead><tr><th>Option</th><th>Coût</th><th>Délai</th><th>Contrôle</th></tr></thead>
            <tbody>
                <tr><td>Développeur freelance</td><td>15 000€ – 40 000€</td><td>3 – 6 mois</td><td>Dépendance</td></tr>
                <tr><td>Agence web</td><td>50 000€ – 150 000€</td><td>6 – 12 mois</td><td>Aucun</td></tr>
                <tr><td>Sharetribe / SaaS</td><td>300€ – 1 000€ / mois</td><td>Immédiat</td><td>Aucun (code fermé)</td></tr>
                <tr><td>CS-Cart Multi-Vendor</td><td>3 590€+ (sans Stripe natif)</td><td>Immédiat</td><td>Partiel</td></tr>
                <tr class="lp-hl"><td>MarketPlace Pro</td><td>2 997€ (paiement unique)</td><td>24h max</td><td>Total (code source)</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- LICENCE -->
<div style="background:var(--grey-50);padding:72px 0;">
<div style="max-width:1100px;margin:0 auto;padding:0 60px;">
    <div class="lp-section-tag">Licence</div>
    <h2 style="font-family:'Syne',sans-serif;font-size:clamp(1.6rem,3vw,2.4rem);font-weight:800;letter-spacing:-.02em;color:var(--grey-800);margin-bottom:16px;">Usage commercial illimité.</h2>
    <p style="color:#475569;margin-bottom:24px;">Vous achetez une licence non-exclusive. Déploiement pour autant de clients que vous voulez.</p>
    <ul class="lp-checklist">
        <li><span class="lp-check">✓</span><div><strong>Déploiement multi-clients</strong> — installez pour autant de clients que vous voulez</div></li>
        <li><span class="lp-check">✓</span><div><strong>Modifications</strong> — personnalisez, rebrandez, étendez le code</div></li>
        <li><span class="lp-check">✓</span><div><strong>Usage commercial</strong> — facturez vos clients, gardez 100% des revenus</div></li>
        <li><span class="lp-cross">✗</span><div><strong style="color:#94a3b8;">Redistribution</strong> — le code source ne peut pas être revendu ou distribué</div></li>
        <li><span class="lp-cross">✗</span><div><strong style="color:#94a3b8;">Produit concurrent</strong> — ne pas créer un produit concurrent basé sur ce code</div></li>
    </ul>
    <p style="margin-top:20px;font-size:.84rem;color:#94a3b8;">Loi française applicable. Tribunaux de Béziers compétents. Contrat complet fourni à l'achat.</p>
</div>
</div>

<!-- FAQ -->
<div class="lp-section">
    <div class="lp-section-tag">FAQ</div>
    <h2>Questions fréquentes.</h2>
    <div class="lp-faq"><h3>Le code est vraiment en production ?</h3><p>Oui. www.marketflow.fr tourne depuis 3 mois sur Railway avec de vrais paiements Stripe. Zéro bug critique connu.</p></div>
    <div class="lp-faq"><h3>Pourquoi sans framework ?</h3><p>Contrôle total. Pas de dépendances à mettre à jour, pas de magie black-box. Vous comprenez chaque ligne. Facile à modifier et maintenir.</p></div>
    <div class="lp-faq"><h3>Combien de temps pour déployer ?</h3><p>15 minutes avec Docker. Documentation complète incluse. Railway ou VPS avec PostgreSQL, c'est encore plus rapide.</p></div>
    <div class="lp-faq"><h3>Je peux voir le code avant d'acheter ?</h3><p>Testez toutes les fonctionnalités sur www.marketflow.fr. Aucun remboursement après livraison du code source.</p></div>
    <div class="lp-faq"><h3>Quel hébergement nécessaire ?</h3><p>N'importe quel VPS avec PHP 8.2+ et PostgreSQL. Testé sur Railway (5€/mois), DigitalOcean, AWS. Dockerfile fourni.</p></div>
    <div class="lp-faq"><h3>Qu'est-ce que la Licence Fondateur ?</h3><p>Les premières licences sont à 2 997€ au lieu de 4 997€. Ce tarif est limité et peut évoluer à tout moment.</p></div>
</div>

<!-- PRICING -->
<div style="background:var(--grey-50);padding:72px 0;">
<div style="max-width:1100px;margin:0 auto;padding:0 60px;">
    <div class="lp-section-tag">Tarif</div>
    <h2 style="font-family:'Syne',sans-serif;font-size:clamp(1.6rem,3vw,2.4rem);font-weight:800;letter-spacing:-.02em;color:var(--grey-800);margin-bottom:0;">Licence Fondateur.</h2>
    <div class="lp-pricing-card">
        <div class="lp-pricing-label">Licence Fondateur — Paiement unique</div>
        <div class="lp-price-row">
            <div class="lp-price-main">2 997€</div>
            <div class="lp-price-old">4 997€</div>
        </div>
        <div class="lp-price-note">TTC · Livraison sous 24h · Licence perpétuelle</div>
        <a href="https://buy.stripe.com/cNi00l40003ObSx93i6J202" class="lp-btn-primary">Obtenir la licence →</a>
        <ul class="lp-checklist" style="text-align:left;">
            <li><span class="lp-check">✓</span><span>Code source complet (GitHub privé + ZIP)</span></li>
            <li><span class="lp-check">✓</span><span>Stripe Connect multi-vendeurs configuré</span></li>
            <li><span class="lp-check">✓</span><span>Licence commerciale (clients illimités)</span></li>
            <li><span class="lp-check">✓</span><span>Documentation installation complète</span></li>
            <li><span class="lp-check">✓</span><span>Support email — contact@marketflow.fr</span></li>
        </ul>
        <div class="lp-pricing-footer">Paiement sécurisé via Stripe · Facture fournie sous 48h</div>
    </div>
</div>
</div>

<!-- CTA FINAL -->
<div class="lp-cta">
    <h2>Prêt à déployer<br>votre marketplace ?</h2>
    <p>Code source complet. En production depuis 3 mois. Licence commerciale.</p>
    <a href="https://buy.stripe.com/cNi00l40003ObSx93i6J202" class="lp-btn-white">Obtenir la licence — 2 997€ →</a>
</div>

<!-- FOOTER -->
<div class="lp-footer">
    <div>MarketPlace Pro © 2026 — A. Devance</div>
    <div>
        <a href="https://www.marketflow.fr" target="_blank">Démo live</a> &nbsp;·&nbsp;
        <a href="mailto:contact@marketflow.fr">contact@marketflow.fr</a>
    </div>
</div>

</div><!-- /.lp -->
