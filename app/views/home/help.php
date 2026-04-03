<?php
/**
 * Centre d'aide / FAQ
 */
?>

<div class="container mt-8">
    <div style="max-width: 900px; margin: 0 auto;">
        <h1 class="mb-8 text-center">Centre d'Aide</h1>

        <div class="mb-12" style="text-align: center;">
            <p style="color: var(--text-secondary); font-size: 1.125rem;">
                Trouvez rapidement des réponses à vos questions
            </p>
        </div>

        <!-- Pour les acheteurs -->
        <div class="card" style="padding: var(--space-8); margin-bottom: var(--space-8);">
            <h2 style="color: var(--primary-600); margin-bottom: var(--space-6);">🛒 Pour les Acheteurs</h2>

            <div style="display: grid; gap: var(--space-6);">
                <details style="border-bottom: 1px solid var(--border); padding-bottom: var(--space-4);">
                    <summary style="cursor: pointer; font-weight: 600; margin-bottom: var(--space-2);">
                        Comment acheter un produit ?
                    </summary>
                    <p style="color: var(--text-secondary); padding-left: var(--space-4); margin-top: var(--space-2);">
                        1. Parcourez le catalogue<br>
                        2. Cliquez sur un produit qui vous intéresse<br>
                        3. Cliquez sur "Ajouter au panier"<br>
                        4. Procédez au paiement sécurisé via Stripe<br>
                        5. Téléchargez immédiatement votre produit
                    </p>
                </details>

                <details style="border-bottom: 1px solid var(--border); padding-bottom: var(--space-4);">
                    <summary style="cursor: pointer; font-weight: 600; margin-bottom: var(--space-2);">
                        Puis-je obtenir un remboursement ?
                    </summary>
                    <p style="color: var(--text-secondary); padding-left: var(--space-4); margin-top: var(--space-2);">
                        Oui, vous disposez de 14 jours pour demander un remboursement si le produit ne correspond 
                        pas à la description ou présente des défauts majeurs.
                    </p>
                </details>

                <details style="border-bottom: 1px solid var(--border); padding-bottom: var(--space-4);">
                    <summary style="cursor: pointer; font-weight: 600; margin-bottom: var(--space-2);">
                        Combien de fois puis-je télécharger un produit ?
                    </summary>
                    <p style="color: var(--text-secondary); padding-left: var(--space-4); margin-top: var(--space-2);">
                        Vous pouvez télécharger chaque produit acheté jusqu'à 3 fois. Passé ce délai, 
                        contactez le support si vous avez besoin d'accès supplémentaire.
                    </p>
                </details>

                <details style="border-bottom: 1px solid var(--border); padding-bottom: var(--space-4);">
                    <summary style="cursor: pointer; font-weight: 600; margin-bottom: var(--space-2);">
                        Les paiements sont-ils sécurisés ?
                    </summary>
                    <p style="color: var(--text-secondary); padding-left: var(--space-4); margin-top: var(--space-2);">
                        Oui ! Tous les paiements sont traités par Stripe, leader mondial de la sécurité des paiements en ligne. 
                        Nous ne stockons aucune donnée bancaire.
                    </p>
                </details>
            </div>
        </div>

        <!-- Pour les vendeurs -->
        <div class="card" style="padding: var(--space-8); margin-bottom: var(--space-8);">
            <h2 style="color: var(--success-600); margin-bottom: var(--space-6);">💼 Pour les Vendeurs</h2>

            <div style="display: grid; gap: var(--space-6);">
                <details style="border-bottom: 1px solid var(--border); padding-bottom: var(--space-4);">
                    <summary style="cursor: pointer; font-weight: 600; margin-bottom: var(--space-2);">
                        Comment devenir vendeur ?
                    </summary>
                    <p style="color: var(--text-secondary); padding-left: var(--space-4); margin-top: var(--space-2);">
                        1. Créez un compte vendeur sur <a href="/register?role=seller" style="color: var(--primary-600);">/register</a><br>
                        2. Complétez votre profil<br>
                        3. Ajoutez vos produits<br>
                        4. Attendez l'approbation (généralement 24-48h)<br>
                        5. Commencez à vendre !
                    </p>
                </details>

                <details style="border-bottom: 1px solid var(--border); padding-bottom: var(--space-4);">
                    <summary style="cursor: pointer; font-weight: 600; margin-bottom: var(--space-2);">
                        Quelle est la commission ?
                    </summary>
                    <p style="color: var(--text-secondary); padding-left: var(--space-4); margin-top: var(--space-2);">
                        La commission de MarketFlow Pro est de 10% par vente. Vous conservez 90% du prix de vente.
                    </p>
                </details>

                <details style="border-bottom: 1px solid var(--border); padding-bottom: var(--space-4);">
                    <summary style="cursor: pointer; font-weight: 600; margin-bottom: var(--space-2);">
                        Comment recevoir mes paiements ?
                    </summary>
                    <p style="color: var(--text-secondary); padding-left: var(--space-4); margin-top: var(--space-2);">
                        Les paiements sont automatiques et traités par Stripe. Configurez votre compte Stripe 
                        dans votre dashboard vendeur pour recevoir vos revenus.
                    </p>
                </details>

                <details style="border-bottom: 1px solid var(--border); padding-bottom: var(--space-4);">
                    <summary style="cursor: pointer; font-weight: 600; margin-bottom: var(--space-2);">
                        Quels types de produits puis-je vendre ?
                    </summary>
                    <p style="color: var(--text-secondary); padding-left: var(--space-4); margin-top: var(--space-2);">
                        Templates web, UI kits, illustrations, icônes, codes sources, plugins, thèmes, 
                        formations, ebooks, et tout autre produit digital légal.
                    </p>
                </details>
            </div>
        </div>

        <!-- Contact -->
        <div class="card" style="background: var(--surface); text-align: center; padding: var(--space-12);">
            <h2 style="margin-bottom: var(--space-4);">Vous ne trouvez pas votre réponse ?</h2>
            <p style="color: var(--text-secondary); margin-bottom: var(--space-6);">
                Notre équipe support est là pour vous aider
            </p>
            <a href="/contact" class="btn btn-primary btn-lg">
                Contactez-nous
            </a>
        </div>
    </div>
</div>
<style>
/* === DESIGN MAQUETTE2 — HELP/FAQ === */
.container h1 { font-family: Georgia, serif; color: #1a1a2e; font-size: clamp(1.8rem,3vw,2.4rem); }
.container > div > .card { background: #fff; border: 1px solid #eeeaf7; border-radius: 14px; box-shadow: 0 2px 12px rgba(124,108,240,.06); }
.card h2 { font-family: Georgia, serif; font-size: 1.3rem; }
.card h2[style*="primary"] { color: #7c6cf0 !important; }
details { border-bottom: 1px solid #f0eeff !important; }
details summary { font-family: 'Manrope', sans-serif; font-weight: 600; color: #1a1a2e; cursor: pointer; transition: color .2s; }
details summary::-webkit-details-marker { display: none; }
details summary::before { content: '+ '; color: #7c6cf0; font-weight: 700; }
details[open] summary::before { content: '− '; }
details summary:hover { color: #7c6cf0; }
details p { font-family: 'Manrope', sans-serif; font-size: .93rem; line-height: 1.75; color: #4b5563 !important; }
details p a { color: #7c6cf0 !important; font-weight: 500; text-decoration: none; border-bottom: 1px solid #c4baff; }
.container > div > .card:last-child { background: #faf9f5 !important; border: 1px solid #e8e4f3; }
.container > div > .card:last-child h2 { font-family: Georgia, serif; color: #1a1a2e; }
.btn.btn-primary.btn-lg { background: #7c6cf0; border-color: #7c6cf0; color: #fff; font-family: 'Manrope', sans-serif; font-weight: 600; border-radius: 8px; transition: background .2s, transform .15s; }
.btn.btn-primary.btn-lg:hover { background: #6558d4; transform: translateY(-1px); }
</style>
