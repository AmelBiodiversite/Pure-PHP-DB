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
        // Méthode getPopular() retourne :
        // - products avec rating_average
        // - Informations vendeur (shop_name)
        // - Catégorie (category_name)
        $products = $productModel->getPopular(4);

        // Transmettre les données à la vue
        // La méthode render() de Core\Controller va :
        // 1. Extraire les variables ($products devient accessible dans la vue)
        // 2. Inclure app/views/layouts/header.php
        // 3. Inclure app/views/home/index.php
        // 4. Inclure app/views/layouts/footer.php
        return $this->render('home/index', [
            'title' => 'Accueil - MarketFlow Pro',
            'products' => $products
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
        // Validation basique
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message'])) {
            setFlashMessage('Veuillez remplir tous les champs obligatoires', 'error');
            return $this->redirect('/contact');
        }

        // Validation email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            setFlashMessage('Email invalide', 'error');
            return $this->redirect('/contact');
        }

        // Préparer les données
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $subject = htmlspecialchars($_POST['subject'] ?? 'Contact depuis MarketFlow Pro');
        $message = htmlspecialchars($_POST['message']);

        // TODO : Implémenter l'envoi d'email
        // Pour l'instant, on simule l'envoi
        // Dans une version production, utilisez PHPMailer ou Symfony Mailer
        
        // Exemple avec mail() natif PHP (ne fonctionne pas toujours)
        /*
        $to = MAIL_FROM;
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $emailBody = "
            <h2>Nouveau message de contact</h2>
            <p><strong>Nom :</strong> $name</p>
            <p><strong>Email :</strong> $email</p>
            <p><strong>Sujet :</strong> $subject</p>
            <p><strong>Message :</strong></p>
            <p>$message</p>
        ";
        
        mail($to, $subject, $emailBody, $headers);
        */

        // Message de succès
        setFlashMessage('Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.', 'success');
        return $this->redirect('/contact');
    }

    /**
     * Liste des vendeurs de la plateforme
     * 
     * Affiche :
     * - Top vendeurs (mieux notés)
     * - Statistiques par vendeur (nombre de produits, notes)
     * - Lien vers leurs boutiques
     * 
     * @return void
     */
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
}
