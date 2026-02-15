<?php
/**
 * PAGE LICENCE FONDATEUR - MARKETFLOW PRO
 * Version : 2026.1
 * Prix : 4 990€ (Offre limitée)
 */
?>

<style>
/* ============================================
   SYSTÈME DE DESIGN & VARIABLES
   ============================================ */
:root {
    --primary: #2563eb;
    --primary-dark: #1e40af;
    --accent: #fbbf24;
    --accent-dark: #f59e0b;
    --success: #10b981;
    --text-main: #1f2937;
    --text-light: #6b7280;
    --bg-light: #f8fafc;
}

/* Base & Typography */
.marketflow-page {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    color: var(--text-main);
    line-height: 1.6;
}

/* ============================================
   HERO SECTION
   ============================================ */
.licence-hero {
    background: radial-gradient(circle at top right, #3b82f6, #1e3a8a);
    padding: 100px 20px;
    text-align: center;
    color: white;
    position: relative;
}

.badge-urgence {
    display: inline-block;
    background: rgba(220, 38, 38, 0.9);
    backdrop-filter: blur(5px);
    color: white;
    padding: 10px 25px;
    border-radius: 50px;
    font-weight: 800;
    font-size: 0.9rem;
    margin-bottom: 30px;
    box-shadow: 0 0 20px rgba(220, 38, 38, 0.3);
    border: 1px solid rgba(255,255,255,0.2);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.licence-hero h1 {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 900;
    margin-bottom: 25px;
    line-height: 1.1;
}

.gradient-gold {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hero-subtitle {
    font-size: 1.4rem;
    max-width: 800px;
    margin: 0 auto 40px;
    opacity: 0.9;
}

/* Prix Box */
.prix-card {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 30px;
    border-radius: 20px;
    max-width: 400px;
    margin: 0 auto 40px;
}

.prix-current {
    font-size: 4.5rem;
    font-weight: 900;
    color: var(--accent);
    display: block;
}

.prix-old {
    text-decoration: line-through;
    opacity: 0.6;
    font-size: 1.2rem;
}
	.highlight-green {
    color: #4ade80; /* Un vert émeraude qui ressort parfaitement sur le bleu */
    font-weight: 700;
    opacity: 1 !important; /* Pour annuler l'opacité de 0.9 du sous-titre */
    text-shadow: 0 2px 4px rgba(0,0,0,0.2); /* Un léger ombre pour décoller le texte du fond */
}

/* ============================================
   FEATURES GRID
   ============================================ */
.section-padding { padding: 80px 20px; }
.container { max-width: 1100px; margin: 0 auto; }

.grid-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.card {
    background: white;
    padding: 40px;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.05);
}

.card-icon {
    font-size: 2.5rem;
    margin-bottom: 20px;
    display: block;
}

/* ============================================
   TABLE COMPARATIVE
   ============================================ */
.table-wrapper {
    overflow-x: auto;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.table-compare {
    width: 100%;
    border-collapse: collapse;
}

.table-compare th {
    background: #1e3a8a;
    color: white;
    padding: 25px;
    text-align: left;
}

.table-compare td {
    padding: 20px 25px;
    border-bottom: 1px solid #f1f5f9;
}

.highlight-cell {
    background: #f0f9ff;
    color: var(--primary);
    font-weight: 700;
}

/* ============================================
   BOITE LICENCE (LA REFORMULATION)
   ============================================ */
.licence-box {
    background: #f8fafc;
    border-left: 5px solid var(--primary);
    padding: 30px;
    border-radius: 0 15px 15px 0;
    margin: 20px 0;
}

.licence-item {
    margin-bottom: 15px;
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.licence-item strong { color: var(--primary-dark); }

/* ============================================
   CTA FINAUX
   ============================================ */
.cta-main {
    display: inline-block;
    background: var(--accent);
    color: #1f2937;
    padding: 20px 50px;
    border-radius: 12px;
    font-weight: 800;
    font-size: 1.2rem;
    text-decoration: none;
    transition: 0.3s;
}

.cta-main:hover { background: var(--accent-dark); transform: scale(1.02); }
</style>

<div class="marketflow-page">

    <header class="licence-hero">
        <div class="container">
            <div class="badge-urgence">⚡ EXCLUSIVITÉ : PLUS QUE 3 LICENCES FONDATEURS</div>
            <h1>Propulsez votre Marketplace avec un <span class="gradient-gold">Code Source Souverain</span></h1>
            <p class="hero-subtitle">
    <span class="highlight-green">Fini les commissions sur CA et les boîtes noires.
    Acquérez le moteur PHP/PostgreSQL professionnel pour lancer votre plateforme en 15 jours.
</span><br></p>

            <div class="prix-card">
                <span class="prix-old">Prix standard : 8 990€</span>
                <span class="prix-current">4 990€</span>
                <p style="color:var(--accent); font-weight:700;">Économie immédiate de 4 000€</p>
            </div>

            <a href="#contact" class="cta-main">🚀 RÉSERVER MON ACCÈS FONDATEUR</a>
            <p style="margin-top:20px; opacity:0.8; font-size:0.9rem;">Livraison du code sous 24h après validation</p>
        </div>
    </header>

    <section class="section-padding">
        <div class="container">
            <h2 style="text-align:center; margin-bottom:50px;">L'excellence technique au service de votre business</h2>
            <div class="grid-features">
                <div class="card">
                    <span class="card-icon">🚀</span>
                    <h3>Performance Native</h3>
                    <p>Architecture PHP 8.2+ et PostgreSQL optimisée pour des milliers de transactions simultanées sans latence.</p>
                </div>
                <div class="card">
                    <span class="card-icon">🛡️</span>
                    <h3>Sécurité Bancaire</h3>
                    <p>Intégration Stripe Connect (KYC, TVA, Split de paiements) et dashboard de monitoring des menaces inclus.</p>
                </div>
                <div class="card">
                    <span class="card-icon">🏗️</span>
                    <h3>Dev-Ready</h3>
                    <p>Code 100% commenté en français, typage strict, PSR-12, et suite de tests PHPUnit pour évoluer sans peur.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding" style="background:var(--bg-light);">
        <div class="container">
            <h2 style="text-align:center; margin-bottom:50px;">Pourquoi choisir MarketFlow Pro ?</h2>
            <div class="table-wrapper">
                <table class="table-compare">
                    <thead>
                        <tr>
                            <th>Critères</th>
                            <th>MarketFlow Pro</th>
                            <th>Développement Sur-mesure</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Investissement</strong></td>
                            <td class="highlight-cell">4 990€ (Payé une fois)</td>
                            <td>25 000€ à 60 000€</td>
                        </tr>
                        <tr>
                            <td><strong>Time-to-market</strong></td>
                            <td class="highlight-cell">1 à 2 semaines</td>
                            <td>6 à 9 mois</td>
                        </tr>
                        <tr>
                            <td><strong>Propriété du code</strong></td>
                            <td class="highlight-cell">Totale (Dépôt Git privé)</td>
                            <td>Souvent limitée ou complexe</td>
                        </tr>
                        <tr>
                            <td><strong>Frais récurrents</strong></td>
                            <td class="highlight-cell">0€ (Souveraineté totale)</td>
                            <td>Maintenance coûteuse</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="section-padding">
        <div class="container" style="max-width: 800px;">
            <h2 style="text-align:center;">Une Licence Commerciale "Full Access"</h2>
            <p style="text-align:center; color:var(--text-light); margin-bottom:30px;">
                Nous avons conçu une licence qui protège votre investissement tout en vous offrant une liberté totale d'exploitation.
            </p>
            
            <div class="licence-box">
                <div class="licence-item">
                    <span>✅</span>
                    <div>
                        <strong>Accès Intégral :</strong>
                        <p>Livraison de l'intégralité du code source via un dépôt Git privé pour une autonomie technique complète.</p>
                    </div>
                </div>
                <div class="licence-item">
                    <span>✅</span>
                    <div>
                        <strong>Déploiement Illimité :</strong>
                        <p>Modifiez, personnalisez et installez la solution pour l'ensemble de vos clients sans aucune limite de licence.</p>
                    </div>
                </div>
                <div class="licence-item">
                    <span>✅</span>
                    <div>
                        <strong>Confidentialité & Exclusivité :</strong>
                        <p>Une licence conçue pour sécuriser votre avantage concurrentiel et conserver la primeur de la solution au sein de votre entreprise.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding" style="background:#10b981; color:white; text-align:center;">
        <div class="container">
            <h2>🛡️ Garantie Transparence Totale</h2>
            <p style="font-size:1.2rem; max-width:700px; margin: 20px auto;">
                <strong>Testez avant d'acheter.</strong> Accédez à la démo live et demandez un accès "lecture seule" au dépôt Git pour auditer la qualité du code avant de finaliser votre commande.
            </p>
        </div>
    </section>

    <section class="section-padding">
        <div class="container" style="max-width: 800px;">
            <h2 style="text-align:center; margin-bottom:40px;">Questions Fréquentes</h2>
            <details style="padding:20px; border-bottom:1px solid #eee;">
                <summary style="font-weight:700; cursor:pointer;">Puis-je l'utiliser pour mes clients en agence ?</summary>
                <p style="padding-top:10px; color:var(--text-light);">Oui, c'est l'atout majeur de cette licence. Vous pouvez facturer des prestations entre 15k€ et 30k€ à vos clients en utilisant MarketFlow comme socle technique.</p>
            </details>
            <details style="padding:20px; border-bottom:1px solid #eee;">
                <summary style="font-weight:700; cursor:pointer;">Ai-je besoin de Docker ?</summary>
                <p style="padding-top:10px; color:var(--text-light);">Docker est recommandé pour un déploiement en 15 minutes, mais le code est compatible avec n'importe quel environnement Linux standard (Apache/Nginx).</p>
            </details>
        </div>
    </section>

    <section id="contact" class="section-padding" style="text-align:center; background: #1e293b; color:white;">
        <div class="container">
            <h2>Prêt à posséder votre propre technologie ?</h2>
            <p style="margin-bottom:30px; opacity:0.8;">Contactez-nous pour une démo personnalisée ou pour réserver votre licence.</p>
            <a href="mailto:contact@marketflow.fr?subject=Réservation Licence Fondateur" class="cta-main">📩 CONTACTER L'ÉQUIPE MARKETFLOW</a>
        </div>
    </section>

</div>
