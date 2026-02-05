<?php
/**
 * MARKETFLOW PRO - Mentions Légales
 */

$pageTitle = 'Mentions Légales - MarketFlow';
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
            <h1>⚖️ Mentions Légales</h1>
            
            <section>
                <h2>1. Éditeur du site</h2>
                <p>
                    <strong>Raison sociale :</strong> [À COMPLÉTER]<br>
                    <strong>Forme juridique :</strong> [SARL / SAS / Auto-entrepreneur]<br>
                    <strong>Capital social :</strong> [Montant] €<br>
                    <strong>SIRET :</strong> [Numéro SIRET]<br>
                    <strong>Siège social :</strong> [Adresse complète]<br>
                    <strong>Email :</strong> contact@marketflow.fr<br>
                    <strong>Téléphone :</strong> [Numéro]
                </p>
            </section>
            
            <section>
                <h2>2. Directeur de publication</h2>
                <p>
                    <strong>Nom :</strong> [Prénom NOM]<br>
                    <strong>Email :</strong> contact@marketflow.fr
                </p>
            </section>
            
            <section>
                <h2>3. Hébergement</h2>
                <p>
                    <strong>Hébergeur :</strong> Railway Corp<br>
                    <strong>Siège social :</strong> 548 Market St PMB 32061, San Francisco, CA 94104<br>
                    <strong>Site web :</strong> <a href="https://railway.app" target="_blank">railway.app</a>
                </p>
            </section>
            
            <section>
                <h2>4. Propriété intellectuelle</h2>
                <p>
                    L'ensemble du contenu de ce site (textes, images, code) est protégé par le droit d'auteur.
                    Toute reproduction non autorisée est interdite.
                </p>
            </section>
            
            <section>
                <h2>5. Données personnelles</h2>
                <p>
                    Conformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression
                    de vos données personnelles. Consultez notre
                    <a href="/privacy">Politique de Confidentialité</a>.
                </p>
            </section>
            
            <section>
                <h2>6. Cookies</h2>
                <p>
                    Ce site utilise des cookies de session pour le fonctionnement du panier et de l'authentification.
                    Aucun cookie de tracking tiers n'est utilisé.
                </p>
            </section>
        </div>
    </main>
    
    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
