<?php
/**
 * Politique de confidentialité
 */
?>

<div class="container mt-8">
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 class="mb-8">Politique de Confidentialité</h1>

        <div class="card" style="padding: var(--space-8); margin-bottom: var(--space-6);">
            <p style="color: var(--text-secondary); margin-bottom: var(--space-6);">
                <strong>Dernière mise à jour :</strong> <?= date('d/m/Y') ?>
            </p>

            <h2 style="margin-top: var(--space-8); margin-bottom: var(--space-4);">1. Collecte des données</h2>
            <p style="line-height: 1.8; color: var(--text-secondary); margin-bottom: var(--space-4);">
                Nous collectons les données suivantes :
            </p>
            <ul style="line-height: 1.8; color: var(--text-secondary); padding-left: var(--space-6);">
                <li>Informations de compte (nom, email, mot de passe crypté)</li>
                <li>Informations de paiement (gérées par Stripe, nous ne stockons pas vos données bancaires)</li>
                <li>Historique d'achats et de ventes</li>
                <li>Données de navigation (cookies, pages visitées)</li>
            </ul>

            <h2 style="margin-top: var(--space-8); margin-bottom: var(--space-4);">2. Utilisation des données</h2>
            <p style="line-height: 1.8; color: var(--text-secondary); margin-bottom: var(--space-4);">
                Vos données sont utilisées pour :
            </p>
            <ul style="line-height: 1.8; color: var(--text-secondary); padding-left: var(--space-6);">
                <li>Gérer votre compte et vos transactions</li>
                <li>Améliorer nos services</li>
                <li>Vous envoyer des notifications importantes</li>
                <li>Prévenir la fraude et assurer la sécurité</li>
            </ul>

            <h2 style="margin-top: var(--space-8); margin-bottom: var(--space-4);">3. Partage des données</h2>
            <p style="line-height: 1.8; color: var(--text-secondary);">
                Nous ne vendons jamais vos données personnelles. Vos données peuvent être partagées avec :
            </p>
            <ul style="line-height: 1.8; color: var(--text-secondary); padding-left: var(--space-6);">
                <li><strong>Stripe :</strong> Pour le traitement des paiements</li>
                <li><strong>Services cloud :</strong> Pour l'hébergement sécurisé</li>
                <li><strong>Autorités légales :</strong> Si requis par la loi</li>
            </ul>

            <h2 style="margin-top: var(--space-8); margin-bottom: var(--space-4);">4. Cookies</h2>
            <p style="line-height: 1.8; color: var(--text-secondary);">
                Nous utilisons des cookies pour améliorer votre expérience. Vous pouvez désactiver les cookies 
                dans votre navigateur, mais certaines fonctionnalités peuvent être limitées.
            </p>

            <h2 style="margin-top: var(--space-8); margin-bottom: var(--space-4);">5. Sécurité</h2>
            <p style="line-height: 1.8; color: var(--text-secondary);">
                Nous prenons la sécurité de vos données très au sérieux. Nous utilisons le chiffrement SSL, 
                des mots de passe cryptés, et des serveurs sécurisés.
            </p>

            <h2 style="margin-top: var(--space-8); margin-bottom: var(--space-4);">6. Vos droits (RGPD)</h2>
            <p style="line-height: 1.8; color: var(--text-secondary); margin-bottom: var(--space-4);">
                Conformément au RGPD, vous avez le droit de :
            </p>
            <ul style="line-height: 1.8; color: var(--text-secondary); padding-left: var(--space-6);">
                <li>Accéder à vos données personnelles</li>
                <li>Rectifier vos données</li>
                <li>Supprimer votre compte et vos données</li>
                <li>Exporter vos données</li>
                <li>Retirer votre consentement</li>
            </ul>

            <h2 style="margin-top: var(--space-8); margin-bottom: var(--space-4);">7. Contact</h2>
            <p style="line-height: 1.8; color: var(--text-secondary);">
                Pour toute question sur la confidentialité ou pour exercer vos droits, contactez-nous via notre 
                <a href="/contact" style="color: var(--primary-600);">page de contact</a>.
            </p>
        </div>
    </div>
</div>