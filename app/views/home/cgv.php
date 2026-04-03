<?php
/**
 * MARKETFLOW PRO - CGV
 * Conditions Générales de Vente
 */

// Titre de la page pour le layout
$pageTitle = 'Conditions Générales de Vente - MarketFlow';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="<?= CSS_URL ?>/style.css">
</head>
<body>
    <?php require_once __DIR__ . '/../layouts/header.php'; ?>
    
    <main class="legal-page">
        <div class="container">
            <h1>📜 Conditions Générales de Vente</h1>
            <p class="updated">Dernière mise à jour : <?= date('d/m/Y') ?></p>
            
            <section>
                <h2>1. Objet</h2>
                <p>
                    Les présentes Conditions Générales de Vente (CGV) régissent les ventes de produits digitaux
                    réalisées sur la plateforme MarketFlow (ci-après "la Plateforme").
                </p>
            </section>
            
            <section>
                <h2>2. Vendeurs</h2>
                <p>
                    MarketFlow est une plateforme de mise en relation entre vendeurs et acheteurs.
                    Chaque vendeur est responsable des produits qu'il propose à la vente.
                </p>
                <p>
                    La Plateforme perçoit une commission de <?= PLATFORM_COMMISSION ?>% sur chaque vente.
                </p>
            </section>
            
            <section>
                <h2>3. Prix</h2>
                <p>
                    Les prix sont affichés en euros (€) toutes taxes comprises (TTC).
                    Le prix applicable est celui affiché au moment de la validation de la commande.
                </p>
            </section>
            
            <section>
                <h2>4. Paiement</h2>
                <p>
                    Le paiement s'effectue en ligne via Stripe, prestataire de paiement sécurisé.
                    Les moyens de paiement acceptés sont : carte bancaire.
                </p>
            </section>
            
            <section>
                <h2>5. Livraison</h2>
                <p>
                    Les produits digitaux sont livrés instantanément par téléchargement après confirmation du paiement.
                    Chaque acheteur dispose de 3 téléchargements par produit acheté.
                </p>
            </section>
            
            <section>
                <h2>6. Droit de rétractation</h2>
                <p>
                    Conformément à l'article L221-28 du Code de la consommation, le droit de rétractation
                    ne peut être exercé pour les contenus numériques fournis immédiatement après validation de la commande.
                </p>
            </section>
            
            <section>
                <h2>7. Garanties</h2>
                <p>
                    Les produits sont garantis conformes à leur description.
                    En cas de non-conformité, l'acheteur peut demander un remboursement dans les 14 jours.
                </p>
            </section>
            
            <section>
                <h2>8. Contact</h2>
                <p>
                    Pour toute question concernant les CGV, contactez-nous :
                    <a href="/contact">Formulaire de contact</a>
                </p>
            </section>
        </div>
    </main>
    
    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
<style>
/* === DESIGN MAQUETTE2 — PAGES LÉGALES (CGU / CGV) === */
body, main { background: #faf9f5 !important; }
.legal-page { padding: 40px 0; }
.container { max-width: 800px; margin: 0 auto; padding: 0 20px; }

/* Titre principal */
h1 {
    font-family: Georgia, serif;
    font-weight: 400;
    color: #1e1208;
    font-size: 26px;
    margin-bottom: 8px;
}

/* Date de mise à jour */
p.updated {
    font-family: 'Manrope', sans-serif;
    font-size: 11px;
    color: #a0907e;
    margin-bottom: 32px;
}

/* Titres de section */
h2 {
    font-family: Georgia, serif;
    font-weight: 400;
    color: #1e1208;
    font-size: 17px;
    margin: 0 0 10px;
    padding-bottom: 8px;
    border-bottom: 0.5px solid #ede8df;
}

/* Paragraphes */
p {
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    color: #6b5c4e;
    line-height: 1.7;
    margin-bottom: 10px;
}

/* Listes */
ul {
    padding-left: 20px;
    margin-bottom: 10px;
}
li {
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    color: #6b5c4e;
    line-height: 1.7;
}

/* Sections encadrées */
section {
    background: #fff;
    border: 0.5px solid #ede8df;
    border-radius: 14px;
    padding: 22px 26px;
    margin-bottom: 12px;
}

/* Liens */
a { color: #7c6cf0; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
