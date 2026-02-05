<?php
/**
 * MARKETFLOW PRO - CGU
 * Conditions Générales d'Utilisation
 */

$pageTitle = 'Conditions Générales d\'Utilisation - MarketFlow';
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
            <h1>📋 Conditions Générales d'Utilisation</h1>
            <p class="updated">Dernière mise à jour : <?= date('d/m/Y') ?></p>
            
            <section>
                <h2>1. Acceptation des CGU</h2>
                <p>
                    En accédant à MarketFlow, vous acceptez sans réserve les présentes CGU.
                    Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser la Plateforme.
                </p>
            </section>
            
            <section>
                <h2>2. Inscription</h2>
                <p>
                    Pour accéder à certaines fonctionnalités, vous devez créer un compte.
                    Vous vous engagez à fournir des informations exactes et à les maintenir à jour.
                </p>
                <p>
                    Vous êtes responsable de la confidentialité de vos identifiants de connexion.
                </p>
            </section>
            
            <section>
                <h2>3. Devenir vendeur</h2>
                <p>
                    Tout utilisateur peut devenir vendeur en activant cette option dans son profil.
                    Les vendeurs s'engagent à :
                </p>
                <ul>
                    <li>Proposer des produits conformes à la législation</li>
                    <li>Fournir des descriptions précises</li>
                    <li>Respecter les droits de propriété intellectuelle</li>
                    <li>Livrer les fichiers après paiement</li>
                </ul>
            </section>
            
            <section>
                <h2>4. Contenu interdit</h2>
                <p>Il est strictement interdit de proposer :</p>
                <ul>
                    <li>Des contenus illégaux ou contrefaits</li>
                    <li>Des contenus violents, haineux ou pornographiques</li>
                    <li>Des logiciels malveillants</li>
                    <li>Des données personnelles d'autrui</li>
                </ul>
            </section>
            
            <section>
                <h2>5. Modération</h2>
                <p>
                    MarketFlow se réserve le droit de modérer et supprimer tout contenu non conforme.
                    Les comptes contrevenants peuvent être suspendus ou supprimés sans préavis.
                </p>
            </section>
            
            <section>
                <h2>6. Propriété intellectuelle</h2>
                <p>
                    Le design, la structure et le code de MarketFlow sont protégés par le droit d'auteur.
                    Toute reproduction non autorisée est interdite.
                </p>
            </section>
            
            <section>
                <h2>7. Limitation de responsabilité</h2>
                <p>
                    MarketFlow agit en tant qu'intermédiaire et n'est pas responsable des produits vendus par les vendeurs.
                    La responsabilité de la conformité incombe aux vendeurs.
                </p>
            </section>
        </div>
    </main>
    
    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
