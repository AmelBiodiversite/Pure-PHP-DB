<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketFlow Pro - Plateforme Marketplace PHP Prête à Déployer</title>
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
    🚨 VENTE FLASH : 97€ pendant 3 heures seulement (Prix normal : 4 990€) → Puis retour au prix normal
</div>

<header>
    <h1>MarketFlow Pro</h1>
    <p class="subtitle">Plateforme Marketplace PHP Prête à Déployer<br>Développée from scratch en 6 mois. Sans framework. Contrôle total.</p>
</header>

<div class="container">

    <!-- Hero Section -->
    <div class="hero">
        <h2 style="text-align: center;">Ne payez plus 30k€+ pour un développement marketplace sur mesure</h2>
        <p style="text-align: center; font-size: 1.2em; margin: 20px 0;">Obtenez une plateforme marketplace testée en production, avec dashboard sécurité, multi-vendeurs et code source complet.</p>
        
        <div class="price">
            <div class="price-old">4 990€</div>
            <div class="price-new">97€</div>
            <p style="color: #666;">Vente Flash • 3 Licences Fondateur Uniquement</p>
        </div>

        <div style="text-align: center;">
            <a href="https://buy.stripe.com/3cI7sN5445o83m13IY6J200" class="cta-button">
                ACCÈS IMMÉDIAT →
            </a>
            <p style="margin-top: 10px; color: #666;">✓ Livraison instantanée via dépôt Git privé<br>✓ Support email 30 jours inclus</p>
        </div>
    </div>

    <!-- Démo Live -->
    <div class="section">
        <h2>🎯 Voir en Action</h2>
        <p><strong>Démo Live :</strong> <a href="https://www.marketflow.fr" target="_blank">www.marketflow.fr</a></p>
        <p style="margin-top: 10px;">En production depuis 3 mois. Zéro bug critique.</p>
    </div>

    <!-- Ce Que Vous Recevez -->
    <div class="section">
        <h2>📦 Ce Que Vous Recevez</h2>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">24 349</div>
                <div class="stat-label">Lignes de Code PHP</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">16</div>
                <div class="stat-label">Contrôleurs MVC</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">5</div>
                <div class="stat-label">Modèles de Données</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">15 min</div>
                <div class="stat-label">Installation Docker</div>
            </div>
        </div>

        <ul>
            <li><span class="checkmark">✓</span> <strong>Code source complet</strong> (24 349 lignes PHP propres et commentées)</li>
            <li><span class="checkmark">✓</span> <strong>Licence commerciale</strong> (déploiement illimité pour vos clients)</li>
            <li><span class="checkmark">✓</span> <strong>Docker Compose</strong> configuré (prêt pour production)</li>
            <li><span class="checkmark">✓</span> <strong>Base PostgreSQL</strong> avec migrations</li>
            <li><span class="checkmark">✓</span> <strong>Support email 30 jours</strong> (réponse <24h)</li>
            <li><span class="checkmark">✓</span> <strong>Documentation d'installation</strong> détaillée</li>
        </ul>
    </div>

    <!-- Fonctionnalités Principales -->
    <div class="section">
        <h2>⚡ Fonctionnalités Principales</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <h3>💳 Intégration Stripe Connect</h3>
                <p>Paiements multi-vendeurs avec répartition automatique des commissions. Conformité TVA française intégrée. Traitement d'argent réel.</p>
            </div>

            <div class="feature-card">
                <h3>🔒 Dashboard Sécurité</h3>
                <p>Détection d'attaques en temps réel (CSRF, XSS, SQLi). Scoring d'IP suspectes. Tracking d'événements. Alertes sécurité par email.</p>
            </div>

            <div class="feature-card">
                <h3>👥 Système Multi-Vendeurs</h3>
                <p>Inscription vendeurs, upload produits, gestion commandes, suivi commissions. Dashboard vendeur complet.</p>
            </div>

            <div class="feature-card">
                <h3>🛒 Panier d'Achat</h3>
                <p>Panier en session, gestion quantités, calculs prix, gestion TVA, flux de paiement.</p>
            </div>

            <div class="feature-card">
                <h3>📊 Panel Admin</h3>
                <p>Gestion utilisateurs, approbation produits, suivi commandes, monitoring sécurité, outils d'export.</p>
            </div>

            <div class="feature-card">
                <h3>⭐ Système d'Avis</h3>
                <p>Avis produits, notes, file de modération, badges achat vérifié.</p>
            </div>
        </div>
    </div>

    <!-- Stack Technique -->
    <div class="section">
        <h2>🛠️ Stack Technique</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <h3>Backend</h3>
                <ul style="margin: 10px 0;">
                    <li>PHP 8.3 (pur, sans framework)</li>
                    <li>Architecture MVC (PSR-4)</li>
                    <li>Base de données PostgreSQL</li>
                    <li>Structure API RESTful</li>
                </ul>
            </div>

            <div class="feature-card">
                <h3>Sécurité</h3>
                <ul style="margin: 10px 0;">
                    <li>Protection CSRF (random_bytes + hash_equals)</li>
                    <li>Sanitization XSS (htmlspecialchars)</li>
                    <li>Prévention injection SQL (requêtes préparées)</li>
                    <li>Rate limiting intégré</li>
                </ul>
            </div>

            <div class="feature-card">
                <h3>Assurance Qualité</h3>
                <ul style="margin: 10px 0;">
                    <li>16 tests PHPUnit (tous passants)</li>
                    <li>Analyse PHPStan niveau 5</li>
                    <li>35 assertions de test</li>
                    <li>Zéro bug critique en production</li>
                </ul>
            </div>

            <div class="feature-card">
                <h3>DevOps</h3>
                <ul style="margin: 10px 0;">
                    <li>Docker Compose prêt</li>
                    <li>Configuration déploiement Railway</li>
                    <li>Support variables d'environnement</li>
                    <li>Déploiement basé sur Git</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Couverture Tests -->
    <div class="section">
        <h2>✅ Couverture Tests (Tous Passants)</h2>
        
        <div class="code-block">
PHPUnit 10.5.63

Protection CSRF
 ✔ Génération token (random_bytes + hash_equals)
 ✔ Validation token valide
 ✔ Validation token invalide
 ✔ Validation mauvais token
 ✔ Génération champ (htmlspecialchars)

Logique Panier
 ✔ Calcul total prix
 ✔ Calcul avec TVA (20% française)
 ✔ Gestion panier vide
 ✔ Arrondi prix (2 décimales)

Helpers Sécurité
 ✔ Protection XSS (échappement balises script)
 ✔ Échappement guillemets (ENT_QUOTES)
 ✔ Validation URL
 ✔ Nettoyage espaces

Validation Utilisateur
 ✔ Validation email (FILTER_VALIDATE_EMAIL)
 ✔ Rejet email invalide
 ✔ Validation longueur username (3-30 caractères)

Tests: 16/16 ✅ | Assertions: 35 | Échecs: 0
        </div>
    </div>

    <!-- Architecture -->
    <div class="section">
        <h2>🏗️ Architecture Propre</h2>
        
        <p><strong>Pourquoi sans framework ?</strong></p>
        <ul>
            <li><strong>Contrôle total :</strong> Pas de magie black-box, comprenez chaque ligne</li>
            <li><strong>Léger :</strong> Aucun bloat de fonctionnalités framework inutilisées</li>
            <li><strong>Facile à customiser :</strong> Modifiez tout sans combattre le framework</li>
            <li><strong>Maintenance long terme :</strong> Pas de mises à jour forcées quand le framework change</li>
        </ul>

        <p style="margin-top: 20px;"><strong>Structure MVC :</strong></p>
        <div class="code-block">
app/
├── controllers/
│   ├── AuthController.php      (15KB - Login/Inscription/Sessions)
│   ├── CartController.php      (19KB - Logique panier)
│   ├── ProductController.php   (14KB - CRUD produits)
│   ├── PaymentController.php   (12KB - Intégration Stripe)
│   ├── SecurityController.php  (24KB - Détection attaques)
│   └── ... (11 contrôleurs de plus)
├── models/
│   ├── User.php               (11KB - Gestion utilisateurs)
│   ├── Product.php            (19KB - Données produits)
│   ├── Cart.php               (14KB - Opérations panier)
│   └── ... (2 modèles de plus)
└── views/
    └── ... (37 templates PHP)

core/
├── Database.php        (Singleton PDO PostgreSQL)
├── CSRF.php           (Génération + validation tokens)
├── Router.php         (Routage URL)
├── RateLimiter.php    (Limitation requêtes)
└── SecurityLogger.php (Logging attaques)
        </div>
    </div>

    <!-- Pour Qui -->
    <div class="section">
        <h2>👨‍💼 Pour Qui ?</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <h3>Agences Web</h3>
                <p><strong>Livrez des projets marketplace en 2 semaines au lieu de 6 mois.</strong></p>
                <p>Rebrandez-le, customisez-le, facturez votre client 15-25k€. Gardez 100% de profit après la licence à 97€.</p>
            </div>

            <div class="feature-card">
                <h3>Entrepreneurs</h3>
                <p><strong>Testez votre idée marketplace sans 30k€+ de coûts de développement.</strong></p>
                <p>Lancez en quelques jours, validez votre marché, itérez rapidement. Code source complet = customisation illimitée.</p>
            </div>

            <div class="feature-card">
                <h3>Développeurs</h3>
                <p><strong>Apprenez d'un code production-ready avec standards de sécurité modernes.</strong></p>
                <p>Voyez comment fonctionnent en pratique les protections CSRF, XSS, SQLi. Exemple d'architecture MVC. Patterns de tests.</p>
            </div>
        </div>
    </div>

    <!-- Comparaison -->
    <div class="section">
        <h2>💰 Comparaison Coûts</h2>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Option</th>
                    <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Coût</th>
                    <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Délai</th>
                    <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Contrôle</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><strong>Embaucher Développeur</strong></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">25 000€ - 60 000€</td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">4-8 mois</td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">Total (après des mois)</td>
                </tr>
                <tr>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><strong>Agence Dev</strong></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">50 000€ - 150 000€</td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">6-12 mois</td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">Limité (vendor lock)</td>
                </tr>
                <tr>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><strong>Sharetribe (NoCode)</strong></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">10 000€+/an</td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">1-2 semaines</td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">Aucun (SaaS)</td>
                </tr>
                <tr style="background: #e7f5ff;">
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><strong>MarketFlow</strong></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><strong style="color: #667eea;">97€ (unique)</strong></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><strong>48 heures</strong></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><strong>Total (code source)</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Garantie -->
    <div class="guarantee">
        <h3 style="margin-bottom: 10px;">🔒 Garantie Transparence Totale</h3>
        <p><strong>Testez avant d'acheter :</strong></p>
        <ul>
            <li>Démo live sur www.marketflow.fr (entièrement fonctionnelle)</li>
            <li>Tous les 16 tests PHPUnit passants (voir résultats ci-dessus)</li>
            <li>Code sécurité visible dans la documentation</li>
            <li>Pas de frais cachés, pas de charges récurrentes</li>
        </ul>
        <p style="margin-top: 10px;"><strong>Ce que vous voyez est ce que vous obtenez.</strong> La démo EST le produit.</p>
    </div>

    <!-- Détails Licence -->
    <div class="section">
        <h2>📜 Licence Commerciale - Accès Complet</h2>
        
        <p><strong>Ce que vous POUVEZ faire :</strong></p>
        <ul>
            <li><span class="checkmark">✓</span> Déployer pour un nombre illimité de projets clients</li>
            <li><span class="checkmark">✓</span> Modifier, customiser, rebrander le code</li>
            <li><span class="checkmark">✓</span> Facturer vos clients 15-25k€ par déploiement</li>
            <li><span class="checkmark">✓</span> Utiliser en environnements de production commerciale</li>
            <li><span class="checkmark">✓</span> Garder 100% des revenus de vos projets</li>
        </ul>

        <p style="margin-top: 20px;"><strong>Ce que vous NE POUVEZ PAS faire :</strong></p>
        <ul>
            <li><span style="color: #fa5252;">✗</span> Revendre le code source à d'autres développeurs/agences</li>
            <li><span style="color: #fa5252;">✗</span> Distribuer publiquement (GitHub, CodeCanyon, etc.)</li>
            <li><span style="color: #fa5252;">✗</span> Créer un produit marketplace concurrent</li>
        </ul>

        <p style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <strong>Règle simple :</strong> Déploiements clients illimités pour VOTRE entreprise. Le code source reste confidentiel pour protéger votre investissement et avantage concurrentiel.
        </p>
    </div>

    <!-- FAQ -->
    <div class="section">
        <h2>❓ Questions Fréquentes</h2>
        
        <div style="margin: 20px 0;">
            <h3>Pourquoi si peu cher (97€ vs 4 990€) ?</h3>
            <p>Test de market fit. J'ai besoin de 3 early adopters pour feedback avant de passer au prix complet. Après 3 licences vendues, retour à 4 990€.</p>
        </div>

        <div style="margin: 20px 0;">
            <h3>C'est prêt pour la production ?</h3>
            <p>Oui. Tourne en live sur www.marketflow.fr depuis 3 mois. Zéro bug critique. 16 tests PHPUnit passants. Vrais paiements Stripe en traitement.</p>
        </div>

        <div style="margin: 20px 0;">
            <h3>Combien de temps prend l'installation ?</h3>
            <p>15 minutes avec Docker Compose. Documentation complète incluse. Support disponible pour questions d'installation.</p>
        </div>

        <div style="margin: 20px 0;">
            <h3>Le code est commenté en français ?</h3>
            <p>Oui. Tous les commentaires sont en français. Le code lui-même est auto-explicatif. Noms de variables, fonctions, architecture sont universels.</p>
        </div>

        <div style="margin: 20px 0;">
            <h3>Et si je trouve un bug ?</h3>
            <p>Support email 30 jours inclus. Signalez les bugs, recevez les corrections sous 24h. Après 30 jours, vous avez le code source complet pour corriger vous-même.</p>
        </div>

        <div style="margin: 20px 0;">
            <h3>Je peux voir le code avant d'acheter ?</h3>
            <p>Testez la démo live sur www.marketflow.fr. Toutes les fonctionnalités sont actives. L'implémentation sécurité est décrite sur cette page. Après achat, accès immédiat au code complet via dépôt Git privé.</p>
        </div>

        <div style="margin: 20px 0;">
            <h3>Quel hébergement nécessaire ?</h3>
            <p>N'importe quel VPS avec support Docker. Testé sur Railway (5€/mois), AWS, DigitalOcean. Base PostgreSQL requise. Guide hébergement détaillé inclus.</p>
        </div>
    </div>

    <!-- CTA Final -->
    <div class="section" style="text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <h2 style="color: white; margin-bottom: 20px;">Prêt à Lancer Votre Marketplace ?</h2>
        <p style="font-size: 1.2em; margin-bottom: 30px;">Rejoignez 3 détenteurs de licence fondateur. Code source complet. Support 30 jours. Licence commerciale.</p>
        
        <a href="https://buy.stripe.com/3cI7sN5445o83m13IY6J200" class="cta-button" style="background: white; color: #667eea;">
            ACCÈS IMMÉDIAT POUR 97€ →
        </a>

        <p style="margin-top: 20px; opacity: 0.9;">
            ✓ Livraison instantanée via dépôt Git privé<br>
            ✓ Code source complet (24 349 lignes)<br>
            ✓ Configuration Docker + documentation<br>
            ✓ Support email 30 jours inclus
        </p>

        <p style="margin-top: 30px; font-size: 0.9em; opacity: 0.8;">
            Questions ? Email : contact@marketflow.fr
        </p>
    </div>

</div>

<footer style="background: #333; color: white; text-align: center; padding: 30px;">
    <p>MarketFlow Pro © 2026 • Développé avec PHP 8.3, PostgreSQL, Stripe • Sans Framework, Contrôle Total</p>
    <p style="margin-top: 10px; opacity: 0.7;">Démo Live : <a href="https://www.marketflow.fr" style="color: #667eea;">www.marketflow.fr</a></p>
</footer>

</body>
</html>
