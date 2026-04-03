<?php
/**
 * Page À propos
 */
?>

<div class="container mt-8">
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 class="mb-8">À propos de MarketFlow Pro</h1>

        <div class="card" style="padding: var(--space-8); margin-bottom: var(--space-8);">
            <h2 style="margin-bottom: var(--space-4);">Notre Mission</h2>
            <p style="line-height: 1.8; color: var(--text-secondary);">
                MarketFlow Pro est une marketplace dédiée aux créateurs digitaux. Notre mission est de permettre 
                à chaque créateur de monétiser son talent en vendant ses créations (templates, UI kits, 
                illustrations, codes, etc.) à une communauté mondiale d'acheteurs.
            </p>
        </div>

        <div class="card" style="padding: var(--space-8); margin-bottom: var(--space-8);">
            <h2 style="margin-bottom: var(--space-4);">Pourquoi MarketFlow Pro ?</h2>
            <div style="display: grid; gap: var(--space-6);">
                <div>
                    <h3 style="color: var(--primary-600); margin-bottom: var(--space-2);">🚀 Simple et Rapide</h3>
                    <p style="color: var(--text-secondary);">
                        Mettez vos produits en vente en quelques minutes. Pas de validation compliquée, 
                        juste un processus simple et efficace.
                    </p>
                </div>
                <div>
                    <h3 style="color: var(--primary-600); margin-bottom: var(--space-2);">💰 Commission Équitable</h3>
                    <p style="color: var(--text-secondary);">
                        Seulement 10% de commission. Vous gardez 90% de vos ventes, c'est vous le créateur !
                    </p>
                </div>
                <div>
                    <h3 style="color: var(--primary-600); margin-bottom: var(--space-2);">🔒 Paiements Sécurisés</h3>
                    <p style="color: var(--text-secondary);">
                        Paiements gérés par Stripe, leader mondial des solutions de paiement en ligne.
                    </p>
                </div>
                <div>
                    <h3 style="color: var(--primary-600); margin-bottom: var(--space-2);">📊 Analytics Détaillés</h3>
                    <p style="color: var(--text-secondary);">
                        Suivez vos ventes, analysez vos performances et optimisez votre stratégie.
                    </p>
                </div>
            </div>
        </div>

        <div class="card" style="padding: var(--space-8); margin-bottom: var(--space-8);">
            <h2 style="margin-bottom: var(--space-4);">Nos Valeurs</h2>
            <ul style="list-style: none; padding: 0; display: grid; gap: var(--space-4);">
                <li style="padding-left: var(--space-6); position: relative;">
                    <span style="position: absolute; left: 0; color: var(--primary-600);">✓</span>
                    <strong>Transparence</strong> : Pas de frais cachés, tout est clair dès le départ
                </li>
                <li style="padding-left: var(--space-6); position: relative;">
                    <span style="position: absolute; left: 0; color: var(--primary-600);">✓</span>
                    <strong>Qualité</strong> : Nous valorisons les créations de qualité
                </li>
                <li style="padding-left: var(--space-6); position: relative;">
                    <span style="position: absolute; left: 0; color: var(--primary-600);">✓</span>
                    <strong>Communauté</strong> : Nous créons un écosystème d'entraide entre créateurs
                </li>
                <li style="padding-left: var(--space-6); position: relative;">
                    <span style="position: absolute; left: 0; color: var(--primary-600);">✓</span>
                    <strong>Innovation</strong> : Nous améliorons constamment notre plateforme
                </li>
            </ul>
        </div>

        <div class="card" style="background: var(--gradient-primary); color: white; text-align: center; padding: var(--space-12);">
            <h2 style="color: white; margin-bottom: var(--space-4);">Rejoignez-nous !</h2>
            <p style="color: rgba(255,255,255,0.9); margin-bottom: var(--space-6);">
                Faites partie de notre communauté de créateurs talentueux
            </p>
            <a href="/register?role=seller" class="btn btn-lg" style="background: white; color: var(--primary-600);">
                Devenir vendeur
            </a>
        </div>
    </div>
</div>
<style>
/* === DESIGN MAQUETTE2 — À PROPOS === */
.container{background:#faf9f5;padding-top:32px!important}
h1,h2,h3{font-family:Georgia,serif;font-weight:400;color:#1e1208}
h1.mb-8{font-size:28px}
.card{background:#fff!important;border:0.5px solid #ede8df!important;border-radius:14px!important;box-shadow:none!important}
p[style*="line-height: 1.8"]{font-family:'Manrope',sans-serif;font-size:13px;color:#6b5c4e;line-height:1.7}
p[style*="color: var(--text-secondary)"]{font-family:'Manrope',sans-serif;font-size:13px;color:#6b5c4e!important}
/* Titres avantages avec icône */
h3[style*="color: var(--primary-600)"]{color:#534ab7!important;font-family:Georgia,serif!important;font-size:16px!important;font-weight:400!important}
/* Valeurs checkmarks */
span[style*="color: var(--primary-600)"]{color:#7c6cf0!important}
/* CTA banner */
.card[style*="background: var(--gradient-primary)"]{background:#ede9fe!important;border-color:#c9c4f5!important}
h2[style*="color: white"]{color:#534ab7!important;font-family:Georgia,serif!important;font-weight:400!important}
p[style*="color: rgba(255,255,255,0.9)"]{color:#6b5c4e!important}
/* Bouton CTA */
a.btn.btn-lg[style*="background: white"]{background:#7c6cf0!important;color:#fff!important;border:none!important;border-radius:8px!important;font-family:'Manrope',sans-serif!important;font-size:13px!important;padding:10px 22px!important}
</style>
