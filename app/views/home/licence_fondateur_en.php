<?php
/**
 * Founder License Sales Page - MarketPlace Pro (EN)
 * PHP Fragment : loaded via render() — NO html/head/body tags
 * MarketFlow nav/footer hidden via CSS
 */
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
nav, body > footer { display: none !important; }
body { background:#f8f9fb !important; color:#1e293b !important;
    font-family:'Inter',sans-serif !important; margin:0 !important; padding:0 !important; }

.lp { --blue:#2563eb; --blue-dark:#1d4ed8; --blue-light:#eff6ff; --green:#16a34a;
      --grey-50:#f8f9fb; --grey-100:#f1f3f7; --grey-200:#e2e6ed; --grey-400:#94a3b8;
      --grey-600:#475569; --grey-800:#1e293b; --white:#fff;
      --radius:8px; --radius-lg:14px;
      --shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06);
      --shadow-lg:0 8px 32px rgba(0,0,0,.10); }

.lp-topbar { background:var(--blue); color:#fff; text-align:center; padding:12px 20px;
    font-family:'Syne',sans-serif; font-weight:700; font-size:.85rem; letter-spacing:.03em; }
.lp-nav { background:var(--white); border-bottom:1px solid var(--grey-200);
    display:flex; justify-content:space-between; align-items:center; padding:18px 60px; }
.lp-logo { font-family:'Syne',sans-serif; font-weight:800; font-size:1.2rem;
    color:var(--grey-800); text-decoration:none; }
.lp-logo span { color:var(--blue); }
.lp-nav-link { color:var(--grey-600); text-decoration:none; font-size:.9rem;
    font-weight:500; transition:color .2s; }
.lp-nav-link:hover { color:var(--blue); }
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
.lp-stats { display:grid; grid-template-columns:repeat(4,1fr);
    border-bottom:1px solid var(--grey-200); background:var(--white); }
.lp-stat { padding:36px 40px; border-right:1px solid var(--grey-200); text-align:center; }
.lp-stat:last-child { border-right:none; }
.lp-stat-num { font-family:'Syne',sans-serif; font-size:2.4rem; font-weight:800;
    color:var(--blue); letter-spacing:-.02em; line-height:1; margin-bottom:6px; }
.lp-stat-label { font-size:.82rem; color:var(--grey-400); }
.lp-section { max-width:1100px; margin:0 auto; padding:72px 60px;
    border-bottom:1px solid var(--grey-200); }
.lp-section-tag { font-size:.72rem; color:var(--blue); letter-spacing:.12em;
    text-transform:uppercase; font-family:'Syne',sans-serif; font-weight:700; margin-bottom:12px; }
.lp-section h2 { font-family:'Syne',sans-serif; font-size:clamp(1.6rem,3vw,2.4rem);
    font-weight:800; letter-spacing:-.02em; color:var(--grey-800); margin-bottom:32px; line-height:1.15; }
.lp-h2 { font-family:'Syne',sans-serif; font-size:clamp(1.6rem,3vw,2.4rem);
    font-weight:800; letter-spacing:-.02em; color:var(--grey-800); margin-bottom:32px; line-height:1.15; }
.lp-inner { max-width:1100px; margin:0 auto; padding:0 60px; }
.lp-bg { background:var(--grey-50); padding:72px 0; }
.lp-screenshots { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:36px; }
.lp-sc-card { border:1px solid var(--grey-200); border-radius:var(--radius-lg);
    overflow:hidden; background:var(--grey-50); box-shadow:var(--shadow); }
.lp-sc-bar { background:var(--grey-100); padding:10px 14px; display:flex;
    gap:6px; align-items:center; border-bottom:1px solid var(--grey-200); }
.lp-dot { width:10px; height:10px; border-radius:50%; }
.lp-dot-r { background:#ff5f57; } .lp-dot-y { background:#febc2e; } .lp-dot-g { background:#28c840; }
.lp-sc-card img { width:100%; display:block; }
.lp-sc-caption { font-size:.8rem; color:var(--grey-400); text-align:center; padding:10px; }
.lp-checklist { list-style:none; margin:0; padding:0; }
.lp-checklist li { padding:16px 0; border-bottom:1px solid var(--grey-100);
    display:flex; align-items:flex-start; gap:14px; font-size:.95rem; color:var(--grey-600); }
.lp-checklist li:last-child { border-bottom:none; }
.lp-check { color:#16a34a; font-weight:700; flex-shrink:0; }
.lp-cross { color:var(--grey-400); font-weight:700; flex-shrink:0; }
.lp-checklist strong { color:var(--grey-800); font-weight:600; }
.lp-features { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:32px; }
.lp-feature { background:var(--white); border:1px solid var(--grey-200);
    border-radius:var(--radius-lg); padding:28px; transition:box-shadow .2s; }
.lp-feature:hover { box-shadow:var(--shadow); }
.lp-feature-icon { font-size:1.5rem; margin-bottom:14px; }
.lp-feature h3 { font-family:'Syne',sans-serif; font-weight:700; font-size:.95rem;
    color:var(--grey-800); margin-bottom:8px; }
.lp-feature p { font-size:.88rem; color:var(--grey-600); line-height:1.6; }
.lp-code { background:#0f172a; border-radius:var(--radius-lg); padding:28px 32px;
    font-family:'Courier New',monospace; font-size:.84rem; color:#94a3b8;
    overflow-x:auto; line-height:1.9; margin-top:28px; }
.lp-code .c-green { color:#4ade80; } .lp-code .c-blue { color:#60a5fa; } .lp-code .c-white { color:#f1f5f9; }
.lp-table-wrap { overflow-x:auto; margin-top:32px; }
.lp-table { width:100%; border-collapse:collapse; font-size:.9rem; }
.lp-table th { font-family:'Syne',sans-serif; font-weight:700; font-size:.75rem;
    letter-spacing:.06em; color:var(--grey-400); text-transform:uppercase;
    padding:14px 20px; text-align:left; border-bottom:2px solid var(--grey-200); background:var(--grey-50); }
.lp-table td { padding:14px 20px; border-bottom:1px solid var(--grey-100); color:var(--grey-600); }
.lp-table tr.lp-hl td { background:var(--blue-light); color:var(--grey-800); font-weight:500; }
.lp-table tr.lp-hl td:first-child { color:var(--blue); font-weight:700; }
.lp-faq { border-bottom:1px solid var(--grey-100); padding:24px 0; }
.lp-faq h3 { font-family:'Syne',sans-serif; font-weight:700; font-size:.95rem;
    color:var(--grey-800); margin-bottom:10px; }
.lp-faq p { font-size:.92rem; color:var(--grey-600); line-height:1.7; }
.lp-pricing-card { border:2px solid var(--blue); border-radius:var(--radius-lg);
    padding:48px; max-width:560px; margin:36px auto 0; background:var(--white);
    box-shadow:var(--shadow-lg); text-align:center; }
.lp-pricing-label { font-size:.78rem; color:var(--blue); letter-spacing:.1em;
    text-transform:uppercase; font-family:'Syne',sans-serif; font-weight:700; margin-bottom:20px; }
.lp-price-row { display:flex; align-items:baseline; justify-content:center; gap:14px; margin-bottom:8px; }
.lp-price-main { font-family:'Syne',sans-serif; font-size:4rem; font-weight:800;
    color:var(--blue); letter-spacing:-.03em; }
.lp-price-old { font-size:1.4rem; color:var(--grey-400); text-decoration:line-through; }
.lp-price-note { font-size:.84rem; color:var(--grey-400); margin-bottom:32px; }
.lp-pricing-card .lp-btn-primary { width:100%; font-size:1.05rem; padding:18px;
    margin-bottom:24px; text-align:center; }
.lp-pricing-footer { font-size:.78rem; color:var(--grey-400); margin-top:16px; }
.lp-cta { background:var(--blue); color:#fff; padding:90px 60px; text-align:center; }
.lp-cta h2 { font-family:'Syne',sans-serif; font-size:clamp(1.8rem,3.5vw,3rem);
    font-weight:800; letter-spacing:-.02em; margin-bottom:16px; }
.lp-cta p { font-size:1.05rem; opacity:.8; margin-bottom:36px; }
.lp-btn-white { background:#fff; color:var(--blue); padding:16px 36px;
    font-family:'Syne',sans-serif; font-weight:700; font-size:1rem;
    text-decoration:none; border-radius:var(--radius); display:inline-block; transition:all .2s; }
.lp-btn-white:hover { opacity:.92; transform:translateY(-2px); }
.lp-footer { background:var(--grey-800); color:var(--grey-400); padding:32px 60px;
    display:flex; justify-content:space-between; align-items:center; font-size:.84rem; }
.lp-footer a { color:var(--grey-400); text-decoration:none; }
.lp-footer a:hover { color:#fff; }

@media (max-width:768px) {
    .lp-nav,.lp-hero,.lp-section,.lp-inner { padding-left:24px; padding-right:24px; }
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
    🎯 FOUNDER LICENSE — €2,997 instead of €4,997 &nbsp;·&nbsp; Full source code access &nbsp;·&nbsp; Live demo: www.marketflow.fr
</div>

<!-- NAV -->
<div class="lp-nav">
    <a href="/" class="lp-logo">Market<span>Place</span> Pro</a>
    <a href="https://www.marketflow.fr" target="_blank" class="lp-nav-link">View live demo →</a>
</div>

<!-- HERO -->
<div class="lp-hero">
    <div class="lp-badge">Source code · PHP 8.2 · PostgreSQL · Stripe Connect</div>
    <h1>A marketplace<br><em>ready to deploy.</em><br>Full code included.</h1>
    <p class="lp-hero-sub">23,349 lines of PHP. MVC architecture, no framework. Live in production for 3 months. You receive the complete source code and a commercial license.</p>
    <div class="lp-actions">
        <a href="https://buy.stripe.com/cNi00l40003ObSx93i6J202" class="lp-btn-primary">Get the license — €2,997 →</a>
        <a href="https://www.marketflow.fr" target="_blank" class="lp-btn-ghost">Try the live demo</a>
    </div>
</div>

<!-- STATS -->
<div class="lp-stats">
    <div class="lp-stat"><div class="lp-stat-num">23,349</div><div class="lp-stat-label">Lines of PHP code</div></div>
    <div class="lp-stat"><div class="lp-stat-num">3 months</div><div class="lp-stat-label">Live in production</div></div>
    <div class="lp-stat"><div class="lp-stat-num">139</div><div class="lp-stat-label">Prepared queries (0 SQLi)</div></div>
    <div class="lp-stat"><div class="lp-stat-num">15/15</div><div class="lp-stat-label">PHPUnit tests passing</div></div>
</div>

<!-- SCREENSHOTS -->
<div class="lp-section">
    <div class="lp-section-tag">Live Demo</div>
    <h2>What you deploy.</h2>
    <p style="color:#475569;">Every feature visible on <a href="https://www.marketflow.fr" style="color:#2563eb;font-weight:500;">www.marketflow.fr</a> is included in the code you receive.</p>
    <div class="lp-screenshots">
        <div><div class="lp-sc-card"><div class="lp-sc-bar"><div class="lp-dot lp-dot-r"></div><div class="lp-dot lp-dot-y"></div><div class="lp-dot lp-dot-g"></div></div><img src="/docs/screenshots/homepage.png" alt="Homepage"></div><div class="lp-sc-caption">Homepage</div></div>
        <div><div class="lp-sc-card"><div class="lp-sc-bar"><div class="lp-dot lp-dot-r"></div><div class="lp-dot lp-dot-y"></div><div class="lp-dot lp-dot-g"></div></div><img src="/docs/screenshots/admin_dashboard.png" alt="Admin Dashboard"></div><div class="lp-sc-caption">Admin Dashboard</div></div>
        <div><div class="lp-sc-card"><div class="lp-sc-bar"><div class="lp-dot lp-dot-r"></div><div class="lp-dot lp-dot-y"></div><div class="lp-dot lp-dot-g"></div></div><img src="/docs/screenshots/categories.png" alt="Categories"></div><div class="lp-sc-caption">Catalog & Categories</div></div>
        <div><div class="lp-sc-card"><div class="lp-sc-bar"><div class="lp-dot lp-dot-r"></div><div class="lp-dot lp-dot-y"></div><div class="lp-dot lp-dot-g"></div></div><img src="/docs/screenshots/security_dashboard.png" alt="Security Dashboard"></div><div class="lp-sc-caption">Security Dashboard (unique)</div></div>
    </div>
</div>

<!-- WHAT YOU RECEIVE -->
<div class="lp-bg"><div class="lp-inner">
    <div class="lp-section-tag">Delivery</div>
    <div class="lp-h2">What you receive.</div>
    <ul class="lp-checklist">
        <li><span class="lp-check">✓</span><div><strong>Complete source code</strong> — 23,349 lines of PHP 8.2, commented in French, clean MVC architecture</div></li>
        <li><span class="lp-check">✓</span><div><strong>Private GitHub access</strong> — shared private repository + secure ZIP archive</div></li>
        <li><span class="lp-check">✓</span><div><strong>Full PostgreSQL schema</strong> — tables, indexes, relationships, demo data included</div></li>
        <li><span class="lp-check">✓</span><div><strong>Configured Stripe Connect</strong> — multi-vendor payments, webhooks, automatic commissions</div></li>
        <li><span class="lp-check">✓</span><div><strong>Dockerfile + Railway config</strong> — production deployment in under 15 minutes</div></li>
        <li><span class="lp-check">✓</span><div><strong>Installation documentation</strong> — README, INSTALL, environment variables</div></li>
        <li><span class="lp-check">✓</span><div><strong>Commercial license</strong> — unlimited use for your client projects, modifications allowed</div></li>
        <li><span class="lp-check">✓</span><div><strong>Email support</strong> — contact@marketflow.fr</div></li>
        <li><span class="lp-cross">✗</span><div><strong style="color:#94a3b8;">Source code resale</strong> — the code remains confidential</div></li>
    </ul>
</div></div>

<!-- FEATURES -->
<div class="lp-section">
    <div class="lp-section-tag">Features</div>
    <h2>Everything included.</h2>
    <div class="lp-features">
        <div class="lp-feature"><div class="lp-feature-icon">💳</div><h3>Stripe Connect</h3><p>Multi-vendor payments with automatic splits. Webhooks, refunds, VAT. Real money in production.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">🔒</div><h3>Security Dashboard</h3><p>Real-time monitoring: CSRF, XSS, SQLi attempts. Suspicious IP scoring. Email alerts. Unavailable elsewhere.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">👥</div><h3>Multi-Vendor</h3><p>Vendor registration, product upload, analytics dashboard, order and commission management.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">👑</div><h3>Admin Panel</h3><p>User management, product validation, review moderation, global statistics, export tools.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">🛒</div><h3>Cart & Checkout</h3><p>Persistent session cart, promo codes, VAT calculation, complete Stripe Checkout flow.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">⭐</div><h3>Reviews & Wishlist</h3><p>Review and rating system, moderation, wishlist, secure downloads.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">🌙</div><h3>Dark Mode</h3><p>Native dark mode with toggle. CSS variables for easy theming. 100% responsive.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">📊</div><h3>Analytics</h3><p>Chart.js graphs in vendor and admin dashboards. Revenue, orders, 7-day trends.</p></div>
        <div class="lp-feature"><div class="lp-feature-icon">🔧</div><h3>MVC Architecture</h3><p>Pure PHP, no framework. Router, CSRF, RateLimiter, SecurityLogger — all coded from scratch.</p></div>
    </div>
</div>

<!-- TESTS -->
<div class="lp-bg"><div class="lp-inner">
    <div class="lp-section-tag">Quality</div>
    <div class="lp-h2">Unit tests included.</div>
    <p style="color:#475569;">15 passing PHPUnit tests. Every critical component is covered.</p>
    <div class="lp-code">
<span class="c-white">PHPUnit 10.5 — MarketPlace Pro</span>

<span class="c-blue">CSRF Protection</span>
  <span class="c-green">✔</span> Token generation (random_bytes + hash_equals)
  <span class="c-green">✔</span> Valid token validation
  <span class="c-green">✔</span> Invalid token rejection

<span class="c-blue">Cart Logic</span>
  <span class="c-green">✔</span> Total price calculation
  <span class="c-green">✔</span> Calculation with VAT (20% French)
  <span class="c-green">✔</span> Empty cart handling
  <span class="c-green">✔</span> Price rounding (2 decimals)

<span class="c-blue">Security & Helpers</span>
  <span class="c-green">✔</span> XSS protection
  <span class="c-green">✔</span> URL validation
  <span class="c-green">✔</span> Input sanitization

<span class="c-blue">User Validation</span>
  <span class="c-green">✔</span> Email validation
  <span class="c-green">✔</span> Invalid email rejection
  <span class="c-green">✔</span> Username validation

<span class="c-white">Tests: 15/15 ✅  |  Assertions: 35  |  Failures: 0</span>
    </div>
</div></div>

<!-- COMPARISON -->
<div class="lp-section">
    <div class="lp-section-tag">Comparison</div>
    <h2>The math is simple.</h2>
    <div class="lp-table-wrap">
        <table class="lp-table">
            <thead><tr><th>Option</th><th>Cost</th><th>Timeline</th><th>Control</th></tr></thead>
            <tbody>
                <tr><td>Freelance developer</td><td>€15,000 – €40,000</td><td>3 – 6 months</td><td>Dependency</td></tr>
                <tr><td>Web agency</td><td>€50,000 – €150,000</td><td>6 – 12 months</td><td>None</td></tr>
                <tr><td>Sharetribe / SaaS</td><td>€300 – €1,000 / month</td><td>Immediate</td><td>None (closed code)</td></tr>
                <tr><td>CS-Cart Multi-Vendor</td><td>€3,590+ (no native Stripe)</td><td>Immediate</td><td>Partial</td></tr>
                <tr class="lp-hl"><td>MarketPlace Pro</td><td>€2,997 (one-time)</td><td>24h max</td><td>Total (source code)</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- LICENSE -->
<div class="lp-bg"><div class="lp-inner">
    <div class="lp-section-tag">License</div>
    <div class="lp-h2">Unlimited commercial use.</div>
    <p style="color:#475569;margin-bottom:24px;">You purchase a non-exclusive license. Deploy for as many clients as you want.</p>
    <ul class="lp-checklist">
        <li><span class="lp-check">✓</span><div><strong>Multi-client deployment</strong> — install for as many clients as you want</div></li>
        <li><span class="lp-check">✓</span><div><strong>Modifications</strong> — customize, rebrand, extend the code</div></li>
        <li><span class="lp-check">✓</span><div><strong>Commercial use</strong> — invoice your clients, keep 100% of revenue</div></li>
        <li><span class="lp-cross">✗</span><div><strong style="color:#94a3b8;">Redistribution</strong> — the source code cannot be resold or distributed</div></li>
        <li><span class="lp-cross">✗</span><div><strong style="color:#94a3b8;">Competing product</strong> — do not create a competing product based on this code</div></li>
    </ul>
    <p style="margin-top:20px;font-size:.84rem;color:#94a3b8;">French law applicable. Courts of Béziers have jurisdiction. Full contract provided upon purchase.</p>
</div></div>

<!-- FAQ -->
<div class="lp-section">
    <div class="lp-section-tag">FAQ</div>
    <h2>Frequently asked questions.</h2>
    <div class="lp-faq"><h3>Is the code really in production?</h3><p>Yes. www.marketflow.fr has been running for 3 months on Railway with real Stripe payments. Zero known critical bugs.</p></div>
    <div class="lp-faq"><h3>Why no framework?</h3><p>Total control. No dependencies to update, no black-box magic. You understand every line. Easy to modify and maintain.</p></div>
    <div class="lp-faq"><h3>How long does deployment take?</h3><p>15 minutes with Docker. Full documentation included. If you already have Railway or a VPS with PostgreSQL, it's even faster.</p></div>
    <div class="lp-faq"><h3>Can I see the code before buying?</h3><p>Test all features on www.marketflow.fr. No refunds after source code delivery.</p></div>
    <div class="lp-faq"><h3>What hosting is required?</h3><p>Any VPS with PHP 8.2+ and PostgreSQL. Tested on Railway (€5/month), DigitalOcean, AWS. Dockerfile provided.</p></div>
    <div class="lp-faq"><h3>What is the Founder License?</h3><p>First licenses at €2,997 instead of the standard €4,997. This rate is limited and may change at any time.</p></div>
</div>

<!-- PRICING -->
<div class="lp-bg"><div class="lp-inner">
    <div class="lp-section-tag">Pricing</div>
    <div class="lp-h2">Founder License.</div>
    <div class="lp-pricing-card">
        <div class="lp-pricing-label">Founder License — One-time payment</div>
        <div class="lp-price-row">
            <div class="lp-price-main">€2,997</div>
            <div class="lp-price-old">€4,997</div>
        </div>
        <div class="lp-price-note">VAT incl. · Delivery within 24h · Perpetual license</div>
        <a href="https://buy.stripe.com/cNi00l40003ObSx93i6J202" class="lp-btn-primary">Get the license →</a>
        <ul class="lp-checklist" style="text-align:left;">
            <li><span class="lp-check">✓</span><span>Complete source code (private GitHub + ZIP)</span></li>
            <li><span class="lp-check">✓</span><span>Configured Stripe Connect multi-vendor</span></li>
            <li><span class="lp-check">✓</span><span>Commercial license (unlimited clients)</span></li>
            <li><span class="lp-check">✓</span><span>Complete installation documentation</span></li>
            <li><span class="lp-check">✓</span><span>Email support — contact@marketflow.fr</span></li>
        </ul>
        <div class="lp-pricing-footer">Secure payment via Stripe · Invoice provided within 48h</div>
    </div>
</div></div>

<!-- CTA FINAL -->
<div class="lp-cta">
    <h2>Ready to deploy<br>your marketplace?</h2>
    <p>Complete source code. Live in production for 3 months. Commercial license.</p>
    <a href="https://buy.stripe.com/cNi00l40003ObSx93i6J202" class="lp-btn-white">Get the license — €2,997 →</a>
</div>

<!-- FOOTER -->
<div class="lp-footer">
    <div>MarketPlace Pro © 2026 — A. Devance</div>
    <div>
        <a href="https://www.marketflow.fr" target="_blank">Live demo</a> &nbsp;·&nbsp;
        <a href="mailto:contact@marketflow.fr">contact@marketflow.fr</a>
    </div>
</div>

</div><!-- /.lp -->
