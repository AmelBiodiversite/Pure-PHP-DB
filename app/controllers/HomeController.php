<?php
/**
 * ================================================================
 * MARKETFLOW PRO - CONTRÔLEUR PAGE D'ACCUEIL
 * ================================================================
 * 
 * Fichier : app/controllers/HomeController.php
 * Version : 2.0 (Corrigé - Sans HTML embarqué)
 * Date : 17 janvier 2025
 * 
 * DESCRIPTION :
 * Contrôleur responsable de l'affichage de la page d'accueil.
 * Récupère les produits populaires et les transmet à la vue.
 * 
 * ARCHITECTURE MVC :
 * - Ce fichier contient UNIQUEMENT la logique PHP
 * - Le HTML est dans app/views/home/index.php
 * - La méthode render() inclut automatiquement header + vue + footer
 * 
 * MÉTHODES PUBLIQUES :
 * - index() : Affiche la page d'accueil avec produits populaires
 * - about() : Page à propos
 * - contact() : Formulaire de contact
 * - contactSubmit() : Traitement du formulaire de contact
 * - sellers() : Liste des vendeurs
 * - terms() : CGU
 * - privacy() : Politique de confidentialité
 * - help() : Page d'aide
 * 
 * ================================================================
 */

namespace App\Controllers;

use Core\Controller;
use App\Models\Product;
use App\Models\User;

class HomeController extends Controller {

    /**
     * Page d'accueil
     * 
     * Affiche :
     * - Hero section avec call-to-action
     * - Catégories populaires (4 principales)
     * - Produits populaires (4 mieux notés)
     * - Section "Pourquoi MarketFlow Pro ?"
     * - Call-to-action vendeur
     * 
     * @return void
     */
    public function index() {
        // Instancier le modèle Product
        $productModel = new Product();

        // Récupérer les 4 produits les mieux notés et les plus récents
        $products = $productModel->getPopular(4);

        // 🆕 Récupérer le nombre RÉEL de produits par catégorie (dynamique)
        // Appelle la méthode countByCategory() qui compte en base de données
        // Les chiffres se mettent à jour automatiquement quand on ajoute des produits
        $categoryCounts = [
            'courses' => $productModel->countByCategory('courses'),
            'design' => $productModel->countByCategory('design'),
            'templates' => $productModel->countByCategory('templates'),
            'code' => $productModel->countByCategory('code'),
            'audio' => $productModel->countByCategory('audio'),
            'visual' => $productModel->countByCategory('visual'),
        ];

        // Transmettre les données à la vue
        return $this->render('home/index', [
            'title' => 'Accueil - MarketFlow Pro',
            'products' => $products,
            'categoryCounts' => $categoryCounts // Compteurs dynamiques transmis à la vue
        ]);
    }

    /**
     * Page À propos
     * 
     * Affiche les informations sur MarketFlow Pro :
     * - Histoire de la plateforme
     * - Mission et valeurs
     * - Équipe
     * 
     * @return void
     */
    public function about() {
        return $this->render('home/about', [
            'title' => 'À propos - MarketFlow Pro'
        ]);
    }

    /**
     * Page Contact - Affichage du formulaire
     * 
     * Affiche un formulaire avec :
     * - Nom
     * - Email
     * - Sujet
     * - Message
     * 
     * @return void
     */
    public function contact() {
        return $this->render('home/contact', [
            'title' => 'Contact - MarketFlow Pro'
        ]);
    }

    /**
     * Traitement du formulaire de contact
     * 
     * POST /contact
     * 
     * Données attendues :
     * - name : Nom complet
     * - email : Email de contact
     * - subject : Sujet du message
     * - message : Corps du message
     * 
     * Actions :
     * - Validation des champs
     * - Envoi d'email à l'équipe MarketFlow
     * - Envoi d'email de confirmation à l'utilisateur
     * - Redirection avec message flash
     * 
     * @return void
     */
    public function contactSubmit() {
        // Validation des champs obligatoires
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message'])) {
            setFlashMessage('Veuillez remplir tous les champs obligatoires', 'error');
            return $this->redirect('/contact');
        }

        // Validation email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            setFlashMessage('Email invalide', 'error');
            return $this->redirect('/contact');
        }

        // Préparer les données (sécurisation XSS)
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $subject = htmlspecialchars($_POST['subject'] ?? 'Contact depuis MarketFlow Pro');
        $message = htmlspecialchars($_POST['message']);

        // Envoi email via API HTTP Brevo (SMTP bloqué par Railway)
        $emailBody = "
            <h2>Nouveau message de contact - MarketFlow</h2>
            <p><strong>Nom :</strong> $name</p>
            <p><strong>Email :</strong> $email</p>
            <p><strong>Sujet :</strong> $subject</p>
            <p><strong>Message :</strong><br>" . nl2br($message) . "</p>
            <hr>
            <small>Envoyé depuis le formulaire de contact MarketFlow le " . date('d/m/Y à H:i') . "</small>
        ";

        $result = sendMailApi(
            'contact@marketflow.fr',
            "Nouveau contact MarketFlow : $subject",
            $emailBody
        );

        if ($result === true) {
            // Log succès
            $logLine = date('Y-m-d H:i:s') . " | SUCCESS | To: contact@marketflow.fr | Sujet: $subject | From: $email\n";
            @file_put_contents(__DIR__ . '/../../data/logs/emails.log', $logLine, FILE_APPEND);

            // Email de confirmation à l utilisateur
            $confirmBody = "
                <h2>Merci pour votre message !</h2>
                <p>Bonjour $name,</p>
                <p>Nous avons bien reçu votre demande concernant &laquo; $subject &raquo;.</p>
                <p>Nous vous répondrons dans les plus brefs délais.</p>
                <hr>
                <p><strong>Votre message :</strong><br>" . nl2br($message) . "</p>
                <hr>
                <small>MarketFlow Pro - " . date('d/m/Y') . "</small>
            ";

            $confirmResult = sendMailApi(
                $email,
                "Confirmation de réception - MarketFlow",
                $confirmBody
            );

            if ($confirmResult !== true) {
                error_log('[Contact Confirmation] Erreur : ' . $confirmResult);
            }

            setFlashMessage('Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.', 'success');
        } else {
            // Log erreur
            $logLine = date('Y-m-d H:i:s') . " | ERROR | To: contact@marketflow.fr | Sujet: $subject | From: $email | Msg: $result\n";
            @file_put_contents(__DIR__ . '/../../data/logs/emails.log', $logLine, FILE_APPEND);
            setFlashMessage('Une erreur est survenue. Veuillez réessayer.', 'error');
        }

        return $this->redirect('/contact');
    }

    public function sellers() {
        $userModel = new User();
        
        // Récupérer les 20 vendeurs les plus populaires
        $sellers = $userModel->getPopularSellers(20);

        return $this->render('home/sellers', [
            'title' => 'Nos vendeurs - MarketFlow Pro',
            'sellers' => $sellers
        ]);
    }

    /**
     * Page Conditions Générales d'Utilisation
     * 
     * @return void
     */
    public function terms() {
        return $this->render('home/terms', [
            'title' => 'CGU - MarketFlow Pro'
        ]);
    }

    /**
     * Page Politique de Confidentialité
     * 
     * Conforme RGPD :
     * - Données collectées
     * - Utilisation des données
     * - Droits des utilisateurs
     * - Cookies
     * 
     * @return void
     */
    public function privacy() {
        return $this->render('home/privacy', [
            'title' => 'Politique de confidentialité - MarketFlow Pro'
        ]);
    }

    /**
     * Page d'aide / FAQ
     * 
     * Sections :
     * - Questions fréquentes
     * - Guide vendeur
     * - Guide acheteur
     * - Politique de remboursement
     * - Contact support
     * 
     * @return void
     */
    public function help() {
        return $this->render('home/help', [
            'title' => 'Centre d\'aide - MarketFlow Pro'
        ]);
    }
public function licenceFondateur() {
    return $this->render('home/licence_fondateur', [
        'title' => 'Licence Fondateur MarketFlow - 2 490€',
        'description' => 'Moteur marketplace prêt à l\'emploi. Code source commenté en français. Limité à 3 licences.'
    ]);
}
}
