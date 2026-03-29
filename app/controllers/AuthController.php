<?php
/**
 * MARKETFLOW PRO - AUTH CONTROLLER
 * Gestion de l'authentification et des utilisateurs
 * 
 * Ce contrôleur gère :
 * - La connexion (login)
 * - L'inscription (register)
 * - La déconnexion (logout)
 * - La création de sessions utilisateur
 * 
 * SÉCURITÉ :
 * - Protection CSRF sur tous les formulaires
 * - Rate limiting sur les tentatives de connexion
 * - Validation stricte des données
 * - Sessions sécurisées
 * 
 * Fichier : app/controllers/AuthController.php
 */

namespace App\Controllers;

use Core\Controller;
use Core\SecurityLogger;
use App\Models\User;
use Core\BrevoMailer;

class AuthController extends Controller {
    private $securityLogger;
    private $userModel;

    /**
     * Constructeur
     * Initialise le modèle User et le logger de sécurité
     * 
     * ⚠️ NOTE : La session est déjà démarrée dans index.php
     * On n'appelle PAS session_start() ici pour éviter les doublons
     */
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->securityLogger = new SecurityLogger();

        // ✅ La session est déjà démarrée dans index.php
        // Pas besoin de vérifier ou redémarrer ici
        
        // 🛡️ RÉGÉNÉRATION PÉRIODIQUE DE L'ID DE SESSION
        // Protection supplémentaire contre session hijacking
        // Régénérer toutes les 15 minutes (900 secondes)
        if (isset($_SESSION['LAST_REGENERATION'])) {
            if (time() - $_SESSION['LAST_REGENERATION'] > 900) {
                session_regenerate_id(true);
                $_SESSION['LAST_REGENERATION'] = time();
            }
        } else {
            $_SESSION['LAST_REGENERATION'] = time();
        }
    }

    /**
     * Page de connexion (GET) ou traitement (POST)
     * 
     * SÉCURITÉ :
     * - Génère un token CSRF pour protéger contre les attaques
     * - Redirige automatiquement si déjà connecté
     */
    public function login() {
        // Si l'utilisateur est déjà connecté, pas besoin de se reconnecter
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }

        // POST = soumission du formulaire → traiter la connexion
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } 
        // GET = afficher le formulaire
        else {
            $this->render('auth/login', [
                'title' => 'Connexion',
                'csrf_token' => \Core\CSRF::generateToken() // Token pour sécuriser le formulaire
            ]);
        }
    }

    /**
     * Traiter la connexion (appelé en POST)
     * 
     * SÉCURITÉ :
     * 0. Vérifie le rate limiting (max 5 tentatives / 15 min)
     * 1. Vérifie le token CSRF (protection anti-attaque)
     * 2. Valide les champs requis
     * 3. Vérifie email + mot de passe en base
     * 4. Crée une session sécurisée si OK
     * 
     * @return void
     */
    private function handleLogin() {
        // 🔒 ÉTAPE 0 : VÉRIFIER LE RATE LIMITING
        // Protection contre les attaques par force brute
        $email = $_POST['email'] ?? '';

        // Vérifier si l'utilisateur n'est pas bloqué
        if (!\Core\RateLimiter::check('login', $email)) {
            $blockedFor = \Core\RateLimiter::blockedFor('login', $email);
            $this->securityLogger->logLoginBlocked($email, $blockedFor); 
            $this->render('auth/login', [
                'title' => 'Connexion',
                'error' => 'Trop de tentatives de connexion. Veuillez réessayer dans ' . 
                          \Core\RateLimiter::formatBlockedTime($blockedFor) . '.',
                'csrf_token' => \Core\CSRF::generateToken()
            ]);
            return;
        }

        // 🔒 ÉTAPE 1 : VÉRIFIER LE TOKEN CSRF
        // Si le token est invalide, c'est peut-être une attaque CSRF
        if (!\Core\CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $this->securityLogger->logCSRFViolation('login', $_POST);
            $this->render('auth/login', [
                'title' => 'Connexion',
                'error' => 'Token de sécurité invalide. Veuillez réessayer.',
                'csrf_token' => \Core\CSRF::generateToken()
            ]);
            return;
        }

        // 📥 ÉTAPE 2 : RÉCUPÉRER LES DONNÉES DU FORMULAIRE
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']); // Checkbox "Se souvenir de moi"

        // ✅ ÉTAPE 3 : VALIDATION BASIQUE
        // Vérifier que les champs ne sont pas vides
        if (empty($email) || empty($password)) {
            $this->render('auth/login', [
                'title' => 'Connexion',
                'error' => 'Veuillez remplir tous les champs',
                'email' => $email, // Conserver l'email pour l'UX
                'csrf_token' => \Core\CSRF::generateToken()
            ]);
            return;
        }

        // 🔍 ÉTAPE 4 : AUTHENTIFIER L'UTILISATEUR
        // Le modèle User vérifie email + password hashé en base
        $user = $this->userModel->authenticate($email, $password);

        if ($user) {
            // ✅ CONNEXION RÉUSSIE

            // Réinitialiser le rate limiting (connexion réussie)
            \Core\RateLimiter::clear('login', $email);

            $this->securityLogger->logLoginSuccess($email, $user['id']); 

            // Créer la session utilisateur (stocke user_id, role, etc.)
            $this->createUserSession($user, $remember);

            // 🚀 REDIRECTION SELON LE RÔLE
            if ($user['role'] === 'admin') {
                $this->redirect('/admin'); // Tableau de bord admin
            } elseif ($user['role'] === 'seller') {
                $this->redirect('/seller/dashboard'); // Tableau de bord vendeur
            } else {
                $this->redirect('/'); // Page d'accueil pour les buyers
            }
        } else {
            // ❌ ÉCHEC DE CONNEXION

            $this->securityLogger->logLoginFailed($email, 'invalid_credentials');

            // Incrémenter le compteur de tentatives
            // 5 tentatives max, blocage 15 minutes
            \Core\RateLimiter::attempt('login', $email, 5, 15);

            // Calculer les tentatives restantes
            $remaining = \Core\RateLimiter::remaining('login', $email, 5);
            $errorMsg = 'Email ou mot de passe incorrect';

            // Avertir si proche du blocage
            if ($remaining <= 2 && $remaining > 0) {
                $errorMsg .= ' (' . $remaining . ' tentative' . ($remaining > 1 ? 's' : '') . ' restante' . ($remaining > 1 ? 's' : '') . ')';
            }

            // Email ou mot de passe incorrect
            $this->render('auth/login', [
                'title' => 'Connexion',
                'error' => $errorMsg,
                'email' => $email,
                'csrf_token' => \Core\CSRF::generateToken()
            ]);
        }
    }

    /**
     * Page d'inscription (GET) ou traitement (POST)
     * 
     * Permet de créer un nouveau compte (buyer ou seller)
     */
    public function register() {
        // Si déjà connecté, pas besoin de s'inscrire
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }

        // POST = traiter l'inscription
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
        } 
        // GET = afficher le formulaire
        else {
            $this->render('auth/register', [
                'title' => 'Inscription',
                'csrf_token' => \Core\CSRF::generateToken()
            ]);
        }
    }

    /**
     * Traiter l'inscription (appelé en POST)
     * 
     * VALIDATIONS :
     * - Email valide et unique
     * - Username unique (min 3 caractères)
     * - Mot de passe min 6 caractères
     * - Confirmation mot de passe
     * - Nom de boutique requis pour les sellers
     * 
     * @return void
     */
    private function handleRegister() {
        // 🔒 RATE LIMITING - Protection contre abus d'inscription
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (!\Core\RateLimiter::attempt('register', $ip, 3, 60)) {
            $this->render('auth/register', [
                'title' => 'Inscription',
                'errors' => ['Trop de tentatives d\'inscription. Veuillez réessayer dans 60 minutes.'],
                'csrf_token' => \Core\CSRF::generateToken()
            ]);
            return;
        }
        
        // 🔒 VÉRIFIER LE TOKEN CSRF
        if (!\Core\CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $this->securityLogger->logCSRFViolation('register', $_POST); 
            $this->render('auth/register', [
                'title' => 'Inscription',
                'errors' => ['Token de sécurité invalide. Veuillez réessayer.'],
                'csrf_token' => \Core\CSRF::generateToken()
            ]);
            return;
        }

        // 📥 RÉCUPÉRER LES DONNÉES
        $data = [
            'email' => $_POST['email'] ?? '',
            'username' => $_POST['username'] ?? '',
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
            'role' => in_array($_POST['role'] ?? '', ['buyer', 'seller']) ? $_POST['role'] : 'buyer',
            'shop_name' => $_POST['shop_name'] ?? null // Seulement pour sellers
        ];

        // ✅ VALIDATION DES DONNÉES
        $errors = [];

        // Validation email
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide';
        }

        // Validation username
        if (empty($data['username']) || strlen($data['username']) < 3) {
            $errors[] = 'Username doit faire au moins 3 caractères';
        }

        // Validation mot de passe
        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors[] = 'Mot de passe doit faire au moins 6 caractères';
        }

        // Confirmation mot de passe
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Les mots de passe ne correspondent pas';
        }

        // Nom de boutique requis pour vendeurs
        if ($data['role'] === 'seller' && empty($data['shop_name'])) {
            $errors[] = 'Nom de boutique requis pour les vendeurs';
        }

        // S'il y a des erreurs, réafficher le formulaire
        if (!empty($errors)) {
            $this->render('auth/register', [
                'title' => 'Inscription',
                'errors' => $errors,
                'old' => $data, // Conserver les données pour l'UX
                'csrf_token' => \Core\CSRF::generateToken()
            ]);
            return;
        }

        // 🔍 VÉRIFIER L'UNICITÉ

        // Email déjà utilisé ?
        if ($this->userModel->emailExists($data['email'])) {
            $this->render('auth/register', [
                'title' => 'Inscription',
                'errors' => ['Cet email est déjà utilisé'],
                'old' => $data,
                'csrf_token' => \Core\CSRF::generateToken()
            ]);
            return;
        }

        // Username déjà pris ?
        if ($this->userModel->usernameExists($data['username'])) {
            $this->render('auth/register', [
                'title' => 'Inscription',
                'errors' => ['Ce nom d\'utilisateur est déjà pris'],
                'old' => $data,
                'csrf_token' => \Core\CSRF::generateToken()
            ]);
            return;
        }

        // 💾 CRÉER L'UTILISATEUR
        unset($data['password_confirm']); // Ne pas stocker la confirmation
        $userId = $this->userModel->createUser($data);

        if ($userId) {
            // ✅ INSCRIPTION RÉUSSIE

            $this->securityLogger->logRegister($data['email'], $userId);

            // 📧 EMAIL DE BIENVENUE
            BrevoMailer::sendWelcome($data['email'], $data['full_name'] ?: $data['username']);

            // Récupérer l'utilisateur créé
            $user = $this->userModel->find($userId);

            // Connecter automatiquement l'utilisateur
            $this->createUserSession($user);

            // Rediriger selon le rôle
            if ($user['role'] === 'seller') {
                $this->redirect('/seller/dashboard');
            } else {
                $this->redirect('/');
            }
        } else {
            // ❌ ERREUR LORS DE LA CRÉATION
            $this->render('auth/register', [
                'title' => 'Inscription',
                'errors' => ['Erreur lors de l\'inscription'],
                'old' => $data,
                'csrf_token' => \Core\CSRF::generateToken()
            ]);
        }
    }

    /**
     * Déconnexion
     * 
     * SÉCURITÉ :
     * - Détruit complètement la session
     * - Supprime le cookie de session
     * - Empêche toute réutilisation de la session
     */
    public function logout() {
        // Logger la déconnexion avant de détruire la session
        if (isset($_SESSION['user_id'])) {
            $this->securityLogger->logLogout($_SESSION['user_id']);  
        }
        
        // Vider toutes les variables de session
        session_unset();

        // Détruire la session côté serveur
        session_destroy();

        // Supprimer le cookie de session côté navigateur
        // (sinon l'ID de session reste stocké)
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Rediriger vers la page d'accueil
        $this->redirect('/');
    }

    /**
     * Créer une session utilisateur sécurisée
     * 
     * SÉCURITÉ :
     * - Régénère l'ID de session (évite le session fixation)
     * - Stocke les infos essentielles de l'utilisateur
     * - Gère le "Remember me" avec cookie sécurisé
     * 
     * @param array $user Les données de l'utilisateur
     * @param bool $remember Si true, crée un cookie "Remember me" (30 jours)
     * @return void
     */
    private function createUserSession($user, $remember = false) {
        // 🔒 RÉGÉNÉRER L'ID DE SESSION
        // Protection contre le "session fixation attack"
        // (attaque où un pirate force l'utilisation d'un ID de session connu)
        session_regenerate_id(true);

        // 💾 STOCKER LES INFOS ESSENTIELLES
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_role'] = $user['role']; // admin, seller, buyer
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time(); // Timestamp de connexion
        $_SESSION['LAST_REGENERATION'] = time(); // Initialiser le timestamp de régénération

        // 🍪 COOKIE "REMEMBER ME" (optionnel)
        if ($remember) {
            // Générer un token unique et sécurisé
            $token = bin2hex(random_bytes(32)); // 64 caractères hexadécimaux

            // Stocker en session
            $_SESSION['remember_token'] = $token;

            // Créer le cookie (valable 30 jours)
            setcookie(
                'remember_token',  // Nom du cookie
                $token,            // Valeur (token)
                time() + (86400 * 30), // Expiration : 30 jours
                '/',               // Path : tout le site
                '',                // Domain : automatique
                true,              // Secure : HTTPS uniquement (en production)
                true               // HttpOnly : pas accessible en JavaScript (sécurité XSS)
            );
        }
    }
}
