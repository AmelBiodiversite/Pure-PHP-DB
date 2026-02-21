<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketPlace Pro — PHP Marketplace Source Code | €2,997</title>
    <meta name="description" content="Complete source code for a PHP 8.2 / PostgreSQL / Stripe Connect multi-vendor marketplace. Live in production for 3 months. 23,349 lines. Commercial license.">
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

        /* ── TOPBAR ── */
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
    🎯 FOUNDER LICENSE — €2,997 instead of €4,997 &nbsp;·&nbsp; Full source code access &nbsp;·&nbsp; Live demo: www.marketflow.fr
</div>

<!-- NAV -->
<nav>
    <div class="logo">Market<span>Place</span> Pro</div>
    <a href="https://www.marketflow.fr" target="_blank" class="nav-demo">View live demo →</a>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-tag">Source code · PHP 8.2 · PostgreSQL · Stripe Connect</div>
    <h1>A marketplace<br><em>ready to deploy.</em><br>Full code included.</h1>
    <p class="hero-sub">23,349 lines of PHP. MVC architecture, no framework. Live in production for 3 months. You receive the complete source code and a commercial license.</p>
    <div class="hero-actions">
        <a href="https://buy.stripe.com/cNi00l40003ObSx93i6J202" class="btn-primary">Get the license — €2,997 →</a>
        <a href="https://www.marketflow.fr" target="_blank" class="btn-demo">Try the live demo</a>
    </div>
</section>

<!-- STATS BAR -->
<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-num">23,349</div>
        <div class="stat-label">Lines of PHP code</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">3 months</div>
        <div class="stat-label">Live in production</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">139</div>
        <div class="stat-label">Prepared queries (0 SQLi)</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">15/15</div>
        <div class="stat-label">PHPUnit tests passing</div>
    </div>
</div>

<!-- SCREENSHOTS -->
<section class="section">
    <div class="section-tag">Live Demo</div>
    <h2>What you deploy.</h2>
    <p style="color: var(--grey-400);">Every feature visible on <a href="https://www.marketflow.fr" style="color: var(--white);">www.marketflow.fr</a> is included in the code you receive.</p>

    <div class="screenshots-grid">
        <div>
            <div class="screenshot-wrap">
                <div class="screenshot-bar">
                    <div class="dot dot-r"></div>
                    <div class="dot dot-y"></div>
                    <div class="dot dot-g"></div>
                </div>
                <img src="/docs/screenshots/homepage.png" alt="MarketPlace Pro Homepage">
            </div>
            <div class="screenshot-caption">Homepage</div>
        </div>
        <div>
            <div class="screenshot-wrap">
                <div class="screenshot-bar">
                    <div class="dot dot-r"></div>
                    <div class="dot dot-y"></div>
                    <div class="dot dot-g"></div>
                </div>
                <img src="/docs/screenshots/admin_dashboard.png" alt="Admin Dashboard">
            </div>
            <div class="screenshot-caption">Admin Dashboard</div>
        </div>
        <div>
            <div class="screenshot-wrap">
                <div class="screenshot-bar">
                    <div class="dot dot-r"></div>
                    <div class="dot dot-y"></div>
                    <div class="dot dot-g"></div>
                </div>
                <img src="/docs/screenshots/categories.png" alt="Categories page">
            </div>
            <div class="screenshot-caption">Catalog & Categories</div>
        </div>
        <div>
            <div class="screenshot-wrap">
                <div class="screenshot-bar">
                    <div class="dot dot-r"></div>
                    <div class="dot dot-y"></div>
                    <div class="dot dot-g"></div>
                </div>
                <img src="/docs/screenshots/security_dashboard.png" alt="Security Dashboard">
            </div>
            <div class="screenshot-caption">Security Dashboard (unique)</div>
        </div>
    </div>
</section>

<!-- WHAT YOU RECEIVE -->
<section class="section">
    <div class="section-tag">Delivery</div>
    <h2>What you receive.</h2>

    <ul class="checklist">
        <li><span class="check">✓</span><div><strong>Complete source code</strong> <span>— 23,349 lines of PHP 8.2, commented in French, clean MVC architecture</span></div></li>
        <li><span class="check">✓</span><div><strong>Private GitHub access</strong> <span>— shared private repository + secure ZIP archive</span></div></li>
        <li><span class="check">✓</span><div><strong>Full PostgreSQL schema</strong> <span>— tables, indexes, relationships, demo data included</span></div></li>
        <li><span class="check">✓</span><div><strong>Configured Stripe Connect</strong> <span>— multi-vendor payments, webhooks, automatic commissions</span></div></li>
        <li><span class="check">✓</span><div><strong>Dockerfile + Railway config</strong> <span>— production deployment in under 15 minutes</span></div></li>
        <li><span class="check">✓</span><div><strong>Installation documentation</strong> <span>— README, INSTALL, environment variables</span></div></li>
        <li><span class="check">✓</span><div><strong>Commercial license</strong> <span>— unlimited use for your client projects, modifications allowed</span></div></li>
        <li><span class="check">✓</span><div><strong>Email support</strong> <span>— questions about the code and installation, contact@marketflow.fr</span></div></li>
        <li><span class="cross">✗</span><div><strong style="color: #555;">Source code resale</strong> <span>— the code remains confidential</span></div></li>
    </ul>
</section>

<!-- FEATURES -->
<section class="section">
    <div class="section-tag">Features</div>
    <h2>Everything included.</h2>

    <div class="features-grid">
        <div class="feature-item">
            <div class="feature-icon">💳</div>
            <h3>Stripe Connect</h3>
            <p>Multi-vendor payments with automatic splits. Webhooks, refunds, VAT. Real money in production.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🔒</div>
            <h3>Security Dashboard</h3>
            <p>Real-time monitoring: CSRF, XSS, SQLi attempts. Suspicious IP scoring. Email alerts. Unavailable elsewhere.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">👥</div>
            <h3>Multi-Vendor</h3>
            <p>Vendor registration, product upload, analytics dashboard, order and commission management.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">👑</div>
            <h3>Admin Panel</h3>
            <p>User management, product validation, review moderation, global statistics, export tools.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🛒</div>
            <h3>Cart & Checkout</h3>
            <p>Persistent session cart, promo codes, VAT calculation, complete Stripe Checkout flow.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">⭐</div>
            <h3>Reviews & Wishlist</h3>
            <p>Review and rating system, moderation, wishlist, secure downloads (3x per product).</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🌙</div>
            <h3>Dark Mode</h3>
            <p>Native dark mode with toggle. CSS variables for easy theming. 100% responsive.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">📊</div>
            <h3>Analytics</h3>
            <p>Chart.js graphs in vendor and admin dashboards. Revenue, orders, 7-day trends.</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🔧</div>
            <h3>MVC Architecture</h3>
            <p>Pure PHP, no framework. Router, CSRF, RateLimiter, SecurityLogger — all coded from scratch.</p>
        </div>
    </div>
</section>

<!-- TESTS -->
<section class="section">
    <div class="section-tag">Quality</div>
    <h2>Unit tests included.</h2>
    <p style="color: var(--grey-400);">15 passing PHPUnit tests. Every critical component is covered.</p>

    <div class="code-block">
<span class="c-white">PHPUnit 10.5 — MarketPlace Pro</span>

<span class="c-accent">CSRF Protection</span>
  <span class="c-green">✔</span> Token generation (random_bytes + hash_equals)
  <span class="c-green">✔</span> Valid token validation
  <span class="c-green">✔</span> Invalid token rejection

<span class="c-accent">Cart Logic</span>
  <span class="c-green">✔</span> Total price calculation
  <span class="c-green">✔</span> Calculation with VAT (20% French)
  <span class="c-green">✔</span> Empty cart handling
  <span class="c-green">✔</span> Price rounding (2 decimals)

<span class="c-accent">Security & Helpers</span>
  <span class="c-green">✔</span> XSS protection (script tag escaping)
  <span class="c-green">✔</span> URL validation
  <span class="c-green">✔</span> Input sanitization

<span class="c-accent">User Validation</span>
  <span class="c-green">✔</span> Email validation
  <span class="c-green">✔</span> Invalid email rejection
  <span class="c-green">✔</span> Username validation

<span class="c-white">Tests: 15/15 ✅ &nbsp;|&nbsp; Assertions: 35 &nbsp;|&nbsp; Failures: 0</span>
    </div>
</section>

<!-- COMPARISON -->
<section class="section">
    <div class="section-tag">Comparison</div>
    <h2>The math is simple.</h2>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Option</th>
                    <th>Cost</th>
                    <th>Timeline</th>
                    <th>Control</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Freelance developer</td>
                    <td>€15,000 – €40,000</td>
                    <td>3 – 6 months</td>
                    <td>Dependency</td>
                </tr>
                <tr>
                    <td>Web agency</td>
                    <td>€50,000 – €150,000</td>
                    <td>6 – 12 months</td>
                    <td>None</td>
                </tr>
                <tr>
                    <td>Sharetribe / SaaS</td>
                    <td>€300 – €1,000 / month</td>
                    <td>Immediate</td>
                    <td>None (closed code)</td>
                </tr>
                <tr>
                    <td>CS-Cart Multi-Vendor</td>
                    <td>€3,590+ (no native Stripe)</td>
                    <td>Immediate</td>
                    <td>Partial</td>
                </tr>
                <tr class="highlight">
                    <td>MarketPlace Pro</td>
                    <td>€2,997 (one-time payment)</td>
                    <td>24h max</td>
                    <td>Total (source code)</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<!-- LICENSE -->
<section class="section">
    <div class="section-tag">License</div>
    <h2>Unlimited commercial use.</h2>
    <p style="color: var(--grey-400); margin-bottom: 30px;">You purchase a non-exclusive license. You can deploy for as many clients as you want.</p>

    <ul class="checklist">
        <li><span class="check">✓</span><div><strong>Multi-client deployment</strong> <span>— install for as many clients as you want</span></div></li>
        <li><span class="check">✓</span><div><strong>Modifications</strong> <span>— customize, rebrand, extend the code</span></div></li>
        <li><span class="check">✓</span><div><strong>Commercial use</strong> <span>— invoice your clients, keep 100% of revenue</span></div></li>
        <li><span class="cross">✗</span><div><strong style="color: #555;">Redistribution</strong> <span>— the source code cannot be resold or distributed</span></div></li>
        <li><span class="cross">✗</span><div><strong style="color: #555;">Competing product</strong> <span>— do not create a competing product based on this code</span></div></li>
    </ul>

    <p style="margin-top: 24px; font-size: 0.85rem; color: var(--grey-400);">French law applicable. Courts of Béziers have jurisdiction. Full contract provided upon purchase.</p>
</section>

<!-- FAQ -->
<section class="section">
    <div class="section-tag">FAQ</div>
    <h2>Frequently asked questions.</h2>

    <div class="faq-item">
        <h3>Is the code really in production?</h3>
        <p>Yes. www.marketflow.fr has been running in production for 3 months on Railway with real Stripe payments. Zero known critical bugs.</p>
    </div>
    <div class="faq-item">
        <h3>Why no framework?</h3>
        <p>Total control. No dependencies to update, no black-box magic, no bloat. You understand every line. Easy to modify, easy to maintain.</p>
    </div>
    <div class="faq-item">
        <h3>How long does deployment take?</h3>
        <p>15 minutes with Docker. Full installation documentation included. If you already have Railway or a VPS with PostgreSQL, it's even faster.</p>
    </div>
    <div class="faq-item">
        <h3>Can I see the code before buying?</h3>
        <p>Test all features on www.marketflow.fr. Code stats (lines, tests, queries) are verifiable after delivery. No refunds after source code delivery.</p>
    </div>
    <div class="faq-item">
        <h3>What hosting is required?</h3>
        <p>Any VPS with PHP 8.2+ and PostgreSQL. Tested on Railway (€5/month), DigitalOcean, AWS. Dockerfile provided.</p>
    </div>
    <div class="faq-item">
        <h3>What language are the comments in?</h3>
        <p>Comments are in French. The code itself (variables, functions, architecture) is universal and self-explanatory.</p>
    </div>
    <div class="faq-item">
        <h3>What is the Founder License?</h3>
        <p>The first licenses are offered at €2,997 instead of the standard price of €4,997. This rate is limited and may change at any time.</p>
    </div>
</section>

<!-- PRICING -->
<section class="section" style="border-bottom: none;">
    <div class="section-tag">Pricing</div>
    <h2>Founder License.</h2>

    <div class="pricing-card">
        <div class="pricing-label">Founder License — One-time payment</div>
        <div class="pricing-amount">
            <div class="price-main">€2,997</div>
            <div class="price-old">€4,997</div>
        </div>
        <div class="price-note">VAT incl. · Delivery within 24h · Perpetual license</div>

        <a href="https://buy.stripe.com/cNi00l40003ObSx93i6J202" class="btn-primary">
            Get the license →
        </a>

        <ul class="checklist" style="margin-top: 0;">
            <li><span class="check">✓</span><span>Complete source code (private GitHub + ZIP)</span></li>
            <li><span class="check">✓</span><span>Configured Stripe Connect multi-vendor</span></li>
            <li><span class="check">✓</span><span>Commercial license (unlimited clients)</span></li>
            <li><span class="check">✓</span><span>Complete installation documentation</span></li>
            <li><span class="check">✓</span><span>Email support — contact@marketflow.fr</span></li>
        </ul>

        <div class="pricing-footer" style="margin-top: 24px;">
            Secure payment via Stripe · Invoice provided within 48h
        </div>
    </div>
</section>

<!-- FINAL CTA -->
<div class="cta-section">
    <h2>Ready to deploy<br>your marketplace?</h2>
    <p>Complete source code. Live in production for 3 months. Commercial license.</p>
    <a href="https://buy.stripe.com/cNi00l40003ObSx93i6J202" class="btn-dark">Get the license — €2,997 →</a>
</div>

<!-- FOOTER -->
<footer>
    <div>MarketPlace Pro © 2026 — A. Devance</div>
    <div>
        <a href="https://www.marketflow.fr" target="_blank">Live demo</a> &nbsp;·&nbsp;
        <a href="mailto:contact@marketflow.fr">contact@marketflow.fr</a>
    </div>
</footer>

</body>
</html>
