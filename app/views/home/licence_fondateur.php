<?php
/**
 * PAGE LICENCE FONDATEUR - VERSION PREMIUM
 * Prix : 4 990€ (justifié par 21K lignes de code)
 * Garantie : Testez la démo live - Code visible avant achat
 */
?>

<style>
/* ============================================
   STYLES SPÉCIFIQUES PAGE LICENCE FONDATEUR
   Harmonisés avec index.php
   ============================================ */

/* Section Hero */
.licence-hero {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
    padding: 80px 20px;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
}

.licence-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="1" height="1" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
    opacity: 0.1;
}

.licence-hero-content {
    position: relative;
    max-width: 900px;
    margin: 0 auto;
    z-index: 1;
}

.licence-hero h1 {
    font-size: 3.5rem;
    font-weight: 900;
    margin-bottom: 20px;
    line-height: 1.2;
}

.licence-hero .gradient-text {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.licence-hero-subtitle {
    font-size: 1.3rem;
    margin-bottom: 30px;
    opacity: 0.95;
    line-height: 1.6;
}

/* Badge urgence */
.badge-urgence {
    display: inline-block;
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    padding: 12px 30px;
    border-radius: 10px;
    font-weight: 700;
    margin-bottom: 40px;
    box-shadow: 0 4px 20px rgba(220, 38, 38, 0.4);
    animation: pulse-urgence 2s ease-in-out infinite;
}

@keyframes pulse-urgence {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* Box Souveraineté */
.box-souverainete {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    padding: 25px;
    border-radius: 15px;
    margin: 30px auto;
    max-width: 700px;
    border-left: 4px solid #fbbf24;
}

.box-souverainete h3 {
    color: #fbbf24;
    margin-bottom: 12px;
    font-size: 1.3rem;
}

/* Prix */
.prix-container {
    margin: 50px 0;
}

.prix-principal {
    font-size: 5rem;
    font-weight: 900;
    color: #fbbf24;
    margin-bottom: 10px;
    text-shadow: 0 4px 20px rgba(251, 191, 36, 0.3);
}

.prix-ancien {
    text-decoration: line-through;
    opacity: 0.7;
    font-size: 1.1rem;
}

.prix-economie {
    color: #86efac;
    font-weight: 700;
    font-size: 1.1rem;
    margin-top: 10px;
}

/* CTA Buttons */
.cta-primary {
    display: inline-block;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #1f2937;
    padding: 22px 45px;
    border-radius: 12px;
    font-size: 1.3rem;
    font-weight: 800;
    text-decoration: none;
    box-shadow: 0 6px 25px rgba(251, 191, 36, 0.5);
    transition: all 0.3s ease;
    margin: 10px;
}

.cta-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 35px rgba(251, 191, 36, 0.6);
}

.cta-info {
    margin-top: 20px;
    opacity: 0.8;
    font-size: 0.95rem;
}

/* Section Standard */
.section-licence {
    padding: 80px 20px;
}

.section-licence h2 {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 40px;
    color: var(--text-primary);
}

/* Grille 2 colonnes */
.grid-2col {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
    max-width: 1100px;
    margin: 0 auto;
}

/* Carte Feature */
.feature-box {
    background: white;
    padding: 35px;
    border-radius: 15px;
    border: 2px solid #e5e7eb;
    transition: all 0.3s ease;
}

.feature-box:hover {
    border-color: #3b82f6;
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
    transform: translateY(-5px);
}

.feature-box h3 {
    color: #1d4ed8;
    margin-bottom: 15px;
    font-size: 1.4rem;
}

.feature-box ul {
    list-style: none;
    padding: 0;
}

.feature-box li {
    padding: 10px 0;
    padding-left: 30px;
    position: relative;
    line-height: 1.6;
}

.feature-box li::before {
    content: '✓';
    position: absolute;
    left: 0;
    color: #10b981;
    font-weight: 900;
    font-size: 1.2rem;
}

/* Table Comparaison */
.table-compare {
    width: 100%;
    max-width: 900px;
    margin: 40px auto;
    border-collapse: collapse;
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.table-compare thead {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.table-compare th {
    padding: 20px;
    text-align: left;
    font-weight: 700;
}

.table-compare td {
    padding: 18px 20px;
    border-bottom: 1px solid #e5e7eb;
}

.table-compare tr:hover {
    background: #f9fafb;
}

.table-compare .highlight {
    color: #059669;
    font-weight: 700;
}

/* Box Preuves Techniques */
.box-preuves {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    padding: 40px;
    border-radius: 15px;
    margin: 50px auto;
    max-width: 800px;
    border-left: 5px solid #3b82f6;
}

.box-preuves h3 {
    color: #1e40af;
    margin-bottom: 20px;
    font-size: 1.6rem;
}

.preuves-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 25px;
}

.preuve-item {
    text-align: center;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.preuve-number {
    font-size: 2.5rem;
    font-weight: 900;
    color: #3b82f6;
    display: block;
}

.preuve-label {
    font-size: 0.9rem;
    color: #6b7280;
    margin-top: 8px;
}

/* Témoignages */
.testimonial {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.testimonial-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.testimonial-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #0ea5e9 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 900;
    font-size: 1.2rem;
}

.testimonial-name {
    font-weight: 700;
    color: #1f2937;
}

.testimonial-role {
    font-size: 0.85rem;
    color: #6b7280;
}

.testimonial-text {
    font-style: italic;
    color: #4b5563;
    line-height: 1.7;
}

/* FAQ */
.faq-item {
    background: white;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.faq-item h3 {
    color: #3b82f6;
    margin-bottom: 12px;
    font-size: 1.2rem;
}

.faq-item p {
    color: #6b7280;
    line-height: 1.7;
}

/* Garantie Box */
.garantie-box {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 40px;
    border-radius: 15px;
    text-align: center;
    margin: 50px auto;
    max-width: 700px;
    box-shadow: 0 8px 30px rgba(16, 185, 129, 0.3);
}

.garantie-box h3 {
    font-size: 2rem;
    margin-bottom: 15px;
}

.garantie-box p {
    font-size: 1.1rem;
    line-height: 1.6;
}

/* CTA Final Section */
.cta-final {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    padding: 80px 20px;
    text-align: center;
    border-radius: 20px;
    margin: 60px auto;
    max-width: 1100px;
}

.cta-final h2 {
    font-size: 2.5rem;
    margin-bottom: 20px;
    color: white;
}

.cta-final p {
    font-size: 1.2rem;
    margin-bottom: 40px;
    opacity: 0.95;
}

.cta-secondary {
    display: inline-block;
    background: white;
    color: #1e3a8a;
    padding: 25px 50px;
    border-radius: 12px;
    font-size: 1.4rem;
    font-weight: 800;
    text-decoration: none;
    box-shadow: 0 6px 25px rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
}

.cta-secondary:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 35px rgba(255, 255, 255, 0.4);
}
</style>

<!-- ============================================
     HERO SECTION
     ============================================ -->
<section class="licence-hero">
    <div class="licence-hero-content">
        
        <!-- Badge Urgence -->
        <div class="badge-urgence">
            ⚡ OPÉRATION FONDATEURS : 3 LICENCES DISPONIBLES
        </div>
        
        <!-- Titre Principal -->
        <h1>
            Lancez votre marketplace<br>
            <span class="gradient-text">en semaines, pas en mois</span>
        </h1>
        
        <!-- Sous-titre -->
        <p class="licence-hero-subtitle">
            <strong>Moteur PHP/PostgreSQL professionnel</strong> avec paiements Stripe Connect,<br>
            dashboard multi-vendeurs et monitoring sécurité avancé.<br>
            <em>✨ Démo live : marketflow.fr</em>
        </p>
        
        <!-- Box Souveraineté -->
        <div class="box-souverainete">
            <h3>🎯 Souveraineté Tech 100% Française</h3>
            <p>
                Code source <strong>ouvert et commenté en français</strong>. Pas de boîte noire, pas de dépendances opaques.<br>
                Une solution conçue et supportée en France, conforme RGPD par design.
            </p>
        </div>
        
        <!-- Prix -->
        <div class="prix-container">
            <div class="prix-principal">4 990€</div>
            <p class="prix-ancien">Prix normal : 8 990€ • Économisez 4 000€</p>
            <p class="prix-economie">⚠️ Limité à 3 licences fondateurs à ce tarif</p>
        </div>
        
        <!-- CTA Principal -->
        <a href="mailto:contact@marketflow.fr?subject=Licence Fondateur - 4990€&body=Bonjour,%0D%0A%0D%0AJe souhaite acquérir la Licence Fondateur MarketFlow Pro.%0D%0A%0D%0AMon projet : [Décrivez votre marketplace]%0D%0AUsage prévu : [Agence web / Startup / Autre]%0D%0ADate de lancement souhaitée : [JJ/MM/AAAA]%0D%0A%0D%0AMerci !" 
           class="cta-primary">
            🚀 RÉSERVER MA LICENCE FONDATEUR
        </a>
        
        <!-- Info CTA -->
        <p class="cta-info">
            ⏱️ Réponse sous 24h • 💬 Démo personnalisée sur demande • ✓ Paiement sécurisé
        </p>
    </div>
</section>

<!-- ============================================
     PREUVES TECHNIQUES
     ============================================ -->
<section class="section-licence">
    <div class="container">
        <div class="box-preuves">
            <h3>📊 Ce que vous obtenez VRAIMENT</h3>
            <p style="color: #475569; margin-bottom: 20px;">
                Pas de marketing bullshit. Voici les chiffres réels du code que vous allez recevoir :
            </p>
            
            <div class="preuves-grid">
                <div class="preuve-item">
                    <span class="preuve-number">21K</span>
                    <span class="preuve-label">Lignes de code PHP</span>
                </div>
                <div class="preuve-item">
                    <span class="preuve-number">16</span>
                    <span class="preuve-label">Contrôleurs MVC</span>
                </div>
                <div class="preuve-item">
                    <span class="preuve-number">5</span>
                    <span class="preuve-label">Modèles de données</span>
                </div>
                <div class="preuve-item">
                    <span class="preuve-number">100%</span>
                    <span class="preuve-label">Commenté en français</span>
                </div>
            </div>
            
            <p style="margin-top: 25px; color: #475569; font-weight: 600;">
                ✓ Architecture MVC professionnelle (PSR-4)<br>
                ✓ PostgreSQL avec migrations incluses<br>
                ✓ Stripe Connect FR (multi-vendeurs + TVA française)<br>
                ✓ Dashboard sécurité avec détection d'attaques en temps réel<br>
                ✓ Tests PHPUnit + PHPStan niveau 5<br>
                ✓ Docker compose production-ready
            </p>
        </div>
    </div>
</section>

<!-- ============================================
     POUR QUI ?
     ============================================ -->
<section class="section-licence" style="background: #f9fafb;">
    <div class="container">
        <h2>Pour les <span style="color: #3b82f6;">agences web</span> et <span style="color: #10b981;">entrepreneurs</span> français</h2>
        
        <div class="grid-2col">
            <!-- Agences Web -->
            <div class="feature-box">
                <h3>🏢 Agences Web</h3>
                <ul>
                    <li>Livrez des projets clients en <strong>15 jours au lieu de 6 mois</strong></li>
                    <li>Facturez 15-25k€ pour une marketplace sur mesure</li>
                    <li>Code white-label : rebrandez pour chaque client</li>
                    <li>Support technique en français (email + Slack)</li>
                    <li>ROI dès le 1er projet client</li>
                </ul>
            </div>
            
            <!-- Entrepreneurs -->
            <div class="feature-box" style="border-color: #10b981;">
                <h3 style="color: #059669;">🚀 Entrepreneurs</h3>
                <ul>
                    <li>Testez votre idée de marketplace <strong>sans investir 30k€ en dev</strong></li>
                    <li>Paiements Stripe FR avec gestion automatique de la TVA</li>
                    <li>Conforme RGPD dès le départ (cookies, CNIL, opt-in)</li>
                    <li>Documentation technique complète en français</li>
                    <li>Lancez en production en 2 semaines max</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     COMPARAISON
     ============================================ -->
<section class="section-licence">
    <div class="container">
        <h2>MarketFlow vs Développement sur mesure</h2>
        
        <table class="table-compare">
            <thead>
                <tr>
                    <th></th>
                    <th style="text-align: center;">MarketFlow Pro</th>
                    <th style="text-align: center;">Dev sur mesure</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Coût</strong></td>
                    <td class="highlight" style="text-align: center;">4 990€ une fois</td>
                    <td style="text-align: center; color: #6b7280;">25 000€ - 60 000€</td>
                </tr>
                <tr>
                    <td><strong>Délai de lancement</strong></td>
                    <td class="highlight" style="text-align: center;">1-2 semaines</td>
                    <td style="text-align: center; color: #6b7280;">4-8 mois</td>
                </tr>
                <tr>
                    <td><strong>Code source</strong></td>
                    <td class="highlight" style="text-align: center;">21K lignes, commenté FR</td>
                    <td style="text-align: center; color: #6b7280;">À développer</td>
                </tr>
                <tr>
                    <td><strong>Conformité RGPD/TVA</strong></td>
                    <td class="highlight" style="text-align: center;">✓ Intégrée</td>
                    <td style="text-align: center; color: #6b7280;">À facturer en plus</td>
                </tr>
                <tr>
                    <td><strong>Monitoring sécurité</strong></td>
                    <td class="highlight" style="text-align: center;">✓ Dashboard inclus</td>
                    <td style="text-align: center; color: #6b7280;">Option à 5k€+</td>
                </tr>
                <tr>
                    <td><strong>Tests unitaires</strong></td>
                    <td class="highlight" style="text-align: center;">✓ PHPUnit intégré</td>
                    <td style="text-align: center; color: #6b7280;">Rarement inclus</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<!-- ============================================
     GARANTIE
     ============================================ -->
<section class="section-licence">
    <div class="container">
        <div class="garantie-box">
            <h3>🛡️ Garantie Transparence Totale</h3>
            <p>
                <strong>Testez la démo live sur marketflow.fr</strong><br>
                Explorez toutes les fonctionnalités en conditions réelles.<br>
                Code source visible sur GitHub privé AVANT paiement (accès lecture).<br><br>
                
                <em>Vous savez exactement ce que vous achetez. Zéro surprise.</em>
            </p>
        </div>
    </div>
</section>

<!-- ============================================
     FAQ
     ============================================ -->
<section class="section-licence">
    <div class="container">
        <h2>Questions fréquentes</h2>
        
        <div style="max-width: 800px; margin: 40px auto;">
            
            <div class="faq-item">
                <h3>Puis-je l'utiliser pour mes clients (agence) ?</h3>
                <p>
                    <strong>Oui, c'est même l'usage principal.</strong> Licence commerciale illimitée incluse. 
                    Installez MarketFlow pour autant de clients que vous voulez, rebrandez le design, 
                    facturez 15-25k€ par projet. Zéro restriction.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>L'installation est-elle complexe ?</h3>
                <p>
                    <strong>Non.</strong> Avec Docker : 15 minutes chrono (commande unique). 
                    Sans Docker : 1-2 heures max. Documentation complète en français fournie + 
                    support email inclus pendant 30 jours.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Pourquoi seulement 3 licences à ce prix ?</h3>
                <p>
                    Stratégie "Early Adopters". Les 3 premiers bénéficient du tarif fondateur (4 990€) 
                    et d'un accès prioritaire au support. Après ça, le prix passe à 8 990€ 
                    (valeur réelle du code). C'est maintenant ou jamais.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Ai-je accès au code source ?</h3>
                <p>
                    <strong>100% open source.</strong> Vous recevez le code complet (21 408 lignes PHP) 
                    via dépôt Git privé. Vous pouvez modifier, étendre, revendre. Aucune limite. 
                    C'est VOTRE code après achat.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Le support est-il inclus ?</h3>
                <p>
                    Oui : <strong>30 jours de support email</strong> inclus (réponse sous 24h). 
                    Pour un support étendu (Slack, visio, développement custom), contactez-nous 
                    pour un devis sur mesure.
                </p>
            </div>
            
        </div>
    </div>
</section>

<!-- ============================================
     CTA FINAL
     ============================================ -->
<section class="cta-final">
    <h2>Prêt à économiser 20 000€ de développement ?</h2>
    <p>
        Rejoignez les 3 fondateurs qui lancent leur marketplace en février 2026.<br>
        Places limitées. Accès au code sous 24h après validation.
    </p>
    
    <a href="mailto:contact@marketflow.fr?subject=Licence Fondateur - 4990€&body=Bonjour,%0D%0A%0D%0AJe souhaite réserver ma Licence Fondateur MarketFlow Pro.%0D%0A%0D%0A📋 MON PROJET :%0D%0AType de marketplace : [Décrivez en 2-3 lignes]%0D%0AUsage prévu : [ ] Agence web  [ ] Startup  [ ] Autre%0D%0ADate de lancement : [JJ/MM/AAAA]%0D%0A%0D%0A💬 QUESTIONS :%0D%0A[Vos questions éventuelles]%0D%0A%0D%0AMerci !" 
       class="cta-secondary">
        📩 Réserver ma licence fondateur
    </a>
</section>
