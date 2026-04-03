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
<style>
/* === DESIGN MAQUETTE2 — MENTIONS LÉGALES === */
body,main{background:#faf9f5!important}
.legal-page{padding:40px 0}
.container{max-width:800px;margin:0 auto;padding:0 20px}
h1{font-family:Georgia,serif;font-weight:400;color:#1e1208;font-size:26px;margin-bottom:32px}
h2{font-family:Georgia,serif;font-weight:400;color:#1e1208;font-size:18px;margin:28px 0 12px;padding-bottom:8px;border-bottom:0.5px solid #ede8df}
p,li{font-family:'Manrope',sans-serif;font-size:13px;color:#6b5c4e;line-height:1.7}
section{background:#fff;border:0.5px solid #ede8df;border-radius:14px;padding:22px 26px;margin-bottom:14px}
a{color:#7c6cf0;text-decoration:none}
a:hover{text-decoration:underline}
</style>
