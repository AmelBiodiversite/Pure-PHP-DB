<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketFlow Pro - Production-Ready PHP Marketplace</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
        }
        h1 { font-size: 3em; margin-bottom: 20px; }
        .subtitle { font-size: 1.3em; opacity: 0.95; }
        .flash-banner {
            background: #ff6b6b;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 1.2em;
        }
        .hero {
            background: white;
            padding: 40px;
            margin: 30px 0;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .price {
            text-align: center;
            margin: 30px 0;
        }
        .price-old {
            font-size: 2em;
            text-decoration: line-through;
            color: #999;
        }
        .price-new {
            font-size: 4em;
            color: #667eea;
            font-weight: bold;
        }
        .cta-button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 20px 50px;
            font-size: 1.3em;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            transition: all 0.3s;
        }
        .cta-button:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }
        .section {
            background: white;
            padding: 40px;
            margin: 30px 0;
            border-radius: 10px;
        }
        h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 2em;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .feature-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .feature-card h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .checkmark { color: #51cf66; font-weight: bold; }
        .code-block {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            margin: 20px 0;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
        }
        .stat-number {
            font-size: 3em;
            color: #667eea;
            font-weight: bold;
        }
        .stat-label {
            color: #666;
            margin-top: 10px;
        }
        .guarantee {
            background: #fff3bf;
            border-left: 4px solid #ffd43b;
            padding: 20px;
            margin: 30px 0;
        }
        ul { margin: 20px 0 20px 30px; }
        li { margin: 10px 0; }
    </style>
</head>
<body>

<div class="flash-banner">
    🚨 FLASH SALE: €97 for next 3 hours only (Normally €4,990) → Then back to full price
</div>

<header>
    <h1>MarketFlow Pro</h1>
    <p class="subtitle">Production-Ready PHP Marketplace Platform<br> No Framework. Full Control.</p>
</header>

<div class="container">

    <!-- Hero Section -->
    <div class="hero">
        <h2 style="text-align: center;">Stop paying €30k+ for custom marketplace development</h2>
        <p style="text-align: center; font-size: 1.2em; margin: 20px 0;">Get a battle-tested, production-ready marketplace platform with security dashboard, multi-vendor support, and full source code.</p>
        
        <div class="price">
            <div class="price-old">€4,990</div>
            <div class="price-new">€97</div>
            <p style="color: #666;">Flash Sale • 3 Founder Licenses Only</p>
        </div>

        <div style="text-align: center;">
            <a href="https://buy.stripe.com/3cI7sN5445o83m13IY6J200" class="cta-button">
                GET INSTANT ACCESS →
            </a>
            <p style="margin-top: 10px; color: #666;">✓ Instant delivery via private Git repo<br>✓ 30-day email support included</p>
        </div>
    </div>

    <!-- Live Demo -->
    <div class="section">
        <h2>🎯 See It In Action</h2>
        <p><strong>Live Demo:</strong> <a href="https://www.marketflow.fr" target="_blank">www.marketflow.fr</a></p>
        <p style="margin-top: 10px;">Running in production for 3 months. Zero critical bugs.</p>
    </div>

    <!-- What You Get -->
    <div class="section">
        <h2>📦 What You Get</h2>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">24,349</div>
                <div class="stat-label">Lines of PHP Code</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">16</div>
                <div class="stat-label">MVC Controllers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">5</div>
                <div class="stat-label">Data Models</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">15 min</div>
                <div class="stat-label">Docker Install Time</div>
            </div>
        </div>

        <ul>
            <li><span class="checkmark">✓</span> <strong>Full source code</strong> (24,349 lines of clean, commented PHP)</li>
            <li><span class="checkmark">✓</span> <strong>Commercial license</strong> (deploy for unlimited clients)</li>
            <li><span class="checkmark">✓</span> <strong>Docker Compose</strong> setup (production-ready)</li>
            <li><span class="checkmark">✓</span> <strong>PostgreSQL</strong> database with migrations</li>
            <li><span class="checkmark">✓</span> <strong>30-day email support</strong> (<24h response time)</li>
            <li><span class="checkmark">✓</span> <strong>Installation documentation</strong> (French)</li>
        </ul>
    </div>

    <!-- Core Features -->
    <div class="section">
        <h2>⚡ Core Features</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <h3>💳 Stripe Connect Integration</h3>
                <p>Multi-vendor payments with automatic commission splits. French VAT compliance built-in. Real money processing ready.</p>
            </div>

            <div class="feature-card">
                <h3>🔒 Security Dashboard</h3>
                <p>Real-time attack detection (CSRF, XSS, SQLi). Suspicious IP scoring. Event tracking. Security alerts via email.</p>
            </div>

            <div class="feature-card">
                <h3>👥 Multi-Vendor System</h3>
                <p>Seller registration, product uploads, order management, commission tracking. Complete vendor dashboard.</p>
            </div>

            <div class="feature-card">
                <h3>🛒 Shopping Cart</h3>
                <p>Session-based cart, quantity management, price calculations, VAT handling, checkout flow.</p>
            </div>

            <div class="feature-card">
                <h3>📊 Admin Panel</h3>
                <p>User management, product approval, order tracking, security monitoring, export tools.</p>
            </div>

            <div class="feature-card">
                <h3>⭐ Review System</h3>
                <p>Product reviews, ratings, moderation queue, verified purchase badges.</p>
            </div>
        </div>
    </div>

    <!-- Technical Stack -->
    <div class="section">
        <h2>🛠️ Technical Stack</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <h3>Backend</h3>
                <ul style="margin: 10px 0;">
                    <li>PHP 8.3 (pure, no framework)</li>
                    <li>MVC architecture (PSR-4)</li>
                    <li>PostgreSQL database</li>
                    <li>RESTful API structure</li>
                </ul>
            </div>

            <div class="feature-card">
                <h3>Security</h3>
                <ul style="margin: 10px 0;">
                    <li>CSRF protection (random_bytes + hash_equals)</li>
                    <li>XSS sanitization (htmlspecialchars)</li>
                    <li>SQL injection prevention (prepared statements)</li>
                    <li>Rate limiting built-in</li>
                </ul>
            </div>

            <div class="feature-card">
                <h3>Quality Assurance</h3>
                <ul style="margin: 10px 0;">
                    <li>16 PHPUnit tests (all passing)</li>
                    <li>PHPStan level 5 analysis</li>
                    <li>35 test assertions</li>
                    <li>Zero critical bugs in production</li>
                </ul>
            </div>

            <div class="feature-card">
                <h3>DevOps</h3>
                <ul style="margin: 10px 0;">
                    <li>Docker Compose ready</li>
                    <li>Railway deployment config</li>
                    <li>Environment variable support</li>
                    <li>Git-based deployment</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Test Coverage -->
    <div class="section">
        <h2>✅ Test Coverage (All Passing)</h2>
        
        <div class="code-block">
PHPUnit 10.5.63

CSRF Protection
 ✔ Generate token (random_bytes + hash_equals)
 ✔ Validate valid token
 ✔ Validate invalid token
 ✔ Validate wrong token
 ✔ Field generation (htmlspecialchars)

Cart Logic
 ✔ Calculate total price
 ✔ Calculate with tax (20% French VAT)
 ✔ Empty cart handling
 ✔ Price rounding (2 decimals)

Security Helpers
 ✔ XSS protection (script tag escaping)
 ✔ Quote escaping (ENT_QUOTES)
 ✔ URL validation
 ✔ Trim whitespace

User Validation
 ✔ Email validation (FILTER_VALIDATE_EMAIL)
 ✔ Invalid email rejection
 ✔ Username length validation (3-30 chars)

Tests: 16/16 ✅ | Assertions: 35 | Failures: 0
        </div>
    </div>

    <!-- Architecture -->
    <div class="section">
        <h2>🏗️ Clean Architecture</h2>
        
        <p><strong>Why no framework?</strong></p>
        <ul>
            <li><strong>Full control:</strong> No black-box magic, understand every line</li>
            <li><strong>Lightweight:</strong> No bloat from unused framework features</li>
            <li><strong>Easy to customize:</strong> Modify anything without fighting the framework</li>
            <li><strong>Long-term maintenance:</strong> No forced upgrades when framework changes</li>
        </ul>

        <p style="margin-top: 20px;"><strong>MVC Structure:</strong></p>
        <div class="code-block">
app/
├── controllers/
│   ├── AuthController.php      (15KB - Login/Register/Sessions)
│   ├── CartController.php      (19KB - Shopping cart logic)
│   ├── ProductController.php   (14KB - Product CRUD)
│   ├── PaymentController.php   (12KB - Stripe integration)
│   ├── SecurityController.php  (24KB - Attack detection)
│   └── ... (11 more controllers)
├── models/
│   ├── User.php               (11KB - User management)
│   ├── Product.php            (19KB - Product data)
│   ├── Cart.php               (14KB - Cart operations)
│   └── ... (2 more models)
└── views/
    └── ... (37 PHP templates)

core/
├── Database.php        (Singleton PDO PostgreSQL)
├── CSRF.php           (Token generation + validation)
├── Router.php         (URL routing)
├── RateLimiter.php    (Request throttling)
└── SecurityLogger.php (Attack logging)
        </div>
    </div>

    <!-- Who Is This For -->
    <div class="section">
        <h2>👨‍💼 Who Is This For?</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <h3>Web Agencies</h3>
                <p><strong>Deliver marketplace projects in 2 weeks instead of 6 months.</strong></p>
                <p>Rebrand it, customize it, charge your client €15-25k. Keep 100% profit after the €97 license.</p>
            </div>

            <div class="feature-card">
                <h3>Entrepreneurs</h3>
                <p><strong>Test your marketplace idea without €30k+ development costs.</strong></p>
                <p>Launch in days, validate your market, iterate fast. Full source = unlimited customization.</p>
            </div>

            <div class="feature-card">
                <h3>Developers</h3>
                <p><strong>Learn from production-ready code with modern security standards.</strong></p>
                <p>See how CSRF, XSS, SQLi protection works in practice. MVC architecture example. Testing patterns.</p>
            </div>
        </div>
    </div>

    <!-- Comparison -->
    <div class="section">
        <h2>💰 Cost Comparison</h2>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Option</th>
                    <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Cost</th>
                    <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Timeline</th>
                    <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Control</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><strong>Hire Developer</strong></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">€25,000 - €60,000</td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">4-8 months</td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">Full (after months)</td>
                </tr>
                <tr>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><strong>Dev Agency</strong></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">€50,000 - €150,000</td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">6-12 months</td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">Limited (vendor lock)</td>
                </tr>
                <tr>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><strong>Sharetribe (NoCode)</strong></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">€10,000+/year</td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">1-2 weeks</td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">None (SaaS)</td>
                </tr>
                <tr style="background: #e7f5ff;">
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><strong>MarketFlow</strong></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><strong style="color: #667eea;">€97 (one-time)</strong></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><strong>48 hours</strong></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><strong>Full (source code)</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Guarantee -->
    <div class="guarantee">
        <h3 style="margin-bottom: 10px;">🔒 Total Transparency Guarantee</h3>
        <p><strong>Test before you buy:</strong></p>
        <ul>
            <li>Live demo at www.marketflow.fr (fully functional)</li>
            <li>All 16 PHPUnit tests passing (see results above)</li>
            <li>Security code visible in documentation</li>
            <li>No hidden fees, no recurring charges</li>
        </ul>
        <p style="margin-top: 10px;"><strong>What you see is what you get.</strong> The demo IS the product.</p>
    </div>

    <!-- License Details -->
    <div class="section">
        <h2>📜 Commercial License - Full Access</h2>
        
        <p><strong>What you CAN do:</strong></p>
        <ul>
            <li><span class="checkmark">✓</span> Deploy for unlimited client projects</li>
            <li><span class="checkmark">✓</span> Modify, customize, rebrand the code</li>
            <li><span class="checkmark">✓</span> Charge clients €15-25k per deployment</li>
            <li><span class="checkmark">✓</span> Use in commercial production environments</li>
            <li><span class="checkmark">✓</span> Keep 100% of revenue from your projects</li>
        </ul>

        <p style="margin-top: 20px;"><strong>What you CANNOT do:</strong></p>
        <ul>
            <li><span style="color: #fa5252;">✗</span> Resell the source code to other developers/agencies</li>
            <li><span style="color: #fa5252;">✗</span> Distribute publicly (GitHub, CodeCanyon, etc.)</li>
            <li><span style="color: #fa5252;">✗</span> Create a competing marketplace code product</li>
        </ul>

        <p style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <strong>Simple rule:</strong> Unlimited client deployments for YOUR business. Source code stays confidential to protect your investment and competitive advantage.
        </p>
    </div>

    <!-- FAQ -->
    <div class="section">
        <h2>❓ Frequently Asked Questions</h2>
        
        <div style="margin: 20px 0;">
            <h3>Why so cheap (€97 vs €4,990)?</h3>
            <p>Testing market fit. I need 3 early adopters for feedback before scaling to full price. After 3 licenses sold, price returns to €4,990.</p>
        </div>

        <div style="margin: 20px 0;">
            <h3>Is this production-ready?</h3>
            <p>Yes. Running live at www.marketflow.fr for 3 months. Zero critical bugs. 16 PHPUnit tests passing. Real Stripe payments processing.</p>
        </div>

        <div style="margin: 20px 0;">
            <h3>How long does installation take?</h3>
            <p>15 minutes with Docker Compose. Full documentation included. Support available for setup questions.</p>
        </div>

        <div style="margin: 20px 0;">
            <h3>Do I need to know French?</h3>
            <p>Code comments are in French, but code itself is self-explanatory. Variable names, function names, architecture are universal. English speakers have successfully deployed it.</p>
        </div>

        <div style="margin: 20px 0;">
            <h3>What if I find a bug?</h3>
            <p>30-day email support included. Report bugs, get fixes within 24 hours. After 30 days, you have full source code to fix anything yourself.</p>
        </div>

        <div style="margin: 20px 0;">
            <h3>Can I see the code before buying?</h3>
            <p>Test the live demo at www.marketflow.fr. All features are functional. Security implementation is described in this page. After purchase, immediate access to full source via private Git repo.</p>
        </div>

        <div style="margin: 20px 0;">
            <h3>What hosting do I need?</h3>
            <p>Any VPS with Docker support. Tested on Railway (€5/month), AWS, DigitalOcean. PostgreSQL database required. Detailed hosting guide included.</p>
        </div>
    </div>

    <!-- Final CTA -->
    <div class="section" style="text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <h2 style="color: white; margin-bottom: 20px;">Ready to Launch Your Marketplace?</h2>
        <p style="font-size: 1.2em; margin-bottom: 30px;">Join 3 founder license holders. Full source code. 30-day support. Commercial license.</p>
        
        <a href="https://buy.stripe.com/3cI7sN5445o83m13IY6J200" class="cta-button" style="background: white; color: #667eea;">
            GET INSTANT ACCESS FOR €97 →
        </a>

        <p style="margin-top: 20px; opacity: 0.9;">
            ✓ Instant delivery via private Git repository<br>
            ✓ Complete source code (24,349 lines)<br>
            ✓ Docker setup + documentation<br>
            ✓ 30-day email support included
        </p>

        <p style="margin-top: 30px; font-size: 0.9em; opacity: 0.8;">
            Questions? Email: contact@marketflow.fr
        </p>
    </div>

</div>

<footer style="background: #333; color: white; text-align: center; padding: 30px;">
    <p>MarketFlow Pro © 2026 • Built with PHP 8.3, PostgreSQL, Stripe • No Framework, Full Control</p>
    <p style="margin-top: 10px; opacity: 0.7;">Live Demo: <a href="https://www.marketflow.fr" style="color: #667eea;">www.marketflow.fr</a></p>
</footer>

</body>
</html>
