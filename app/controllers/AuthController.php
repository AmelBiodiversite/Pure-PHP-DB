<?php
/**
 * MARKETFLOW PRO - AUTH CONTROLLER
 * Gestion de l'authentification
 * Fichier : app/controllers/AuthController.php
 */

namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();

        // Démarrer la session si pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Page de connexion
     */
    public function login() {
        // Si déjà connecté, rediriger
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } else {
            $this->render('auth/login', [
                'title' => 'Connexion'
            ]);
        }
    }

    /**
     * Traiter la connexion
     */
    private function handleLogin() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validation basique
        if (empty($email) || empty($password)) {
            $this->render('auth/login', [
                'title' => 'Connexion',
                'error' => 'Veuillez remplir tous les champs',
                'email' => $email
            ]);
            return;
        }

        // Tenter la connexion
        $user = $this->userModel->authenticate($email, $password);

        if ($user) {
            // Connexion réussie
            $this->createUserSession($user, $remember);

            // Rediriger selon le rôle
            if ($user['role'] === 'admin') {
                $this->redirect('/admin');
            } elseif ($user['role'] === 'seller') {
                $this->redirect('/seller/dashboard');
            } else {
                $this->redirect('/');
            }
        } else {
            // Échec de connexion
            $this->render('auth/login', [
                'title' => 'Connexion',
                'error' => 'Email ou mot de passe incorrect',
                'email' => $email
            ]);
        }
    }

    /**
     * Page d'inscription
     */
    public function register() {
        // Si déjà connecté, rediriger
            if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
        } else {
            $this->render('auth/register', [
                'title' => 'Inscription'
            ]);
        }
    }

    /**
     * Traiter l'inscription
     */
    private function handleRegister() {
        $data = [
            'email' => $_POST['email'] ?? '',
            'username' => $_POST['username'] ?? '',
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
            'role' => $_POST['role'] ?? 'buyer',
            'shop_name' => $_POST['shop_name'] ?? null
        ];

        // Validation basique
        $errors = [];

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide';
        }

        if (empty($data['username']) || strlen($data['username']) < 3) {
            $errors[] = 'Username doit faire au moins 3 caractères';
        }

        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors[] = 'Mot de passe doit faire au moins 6 caractères';
        }

        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Les mots de passe ne correspondent pas';
        }

        if ($data['role'] === 'seller' && empty($data['shop_name'])) {
            $errors[] = 'Nom de boutique requis pour les vendeurs';
        }

        if (!empty($errors)) {
            $this->render('auth/register', [
                'title' => 'Inscription',
                'errors' => $errors,
                'old' => $data
            ]);
            return;
        }

        // Vérifier si email existe
        if ($this->userModel->emailExists($data['email'])) {
            $this->render('auth/register', [
                'title' => 'Inscription',
                'errors' => ['Cet email est déjà utilisé'],
                'old' => $data
            ]);
            return;
        }

        // Vérifier si username existe
        if ($this->userModel->usernameExists($data['username'])) {
            $this->render('auth/register', [
                'title' => 'Inscription',
                'errors' => ['Ce nom d\'utilisateur est déjà pris'],
                'old' => $data
            ]);
            return;
        }

        // Créer l'utilisateur
        unset($data['password_confirm']);
        $userId = $this->userModel->createUser($data);

        if ($userId) {
            // Récupérer l'utilisateur créé
            $user = $this->userModel->find($userId);

            // Créer la session
            $this->createUserSession($user);

            // Rediriger selon le rôle
            if ($user['role'] === 'seller') {
                $this->redirect('/seller/dashboard');
            } else {
                $this->redirect('/');
            }
        } else {
            $this->render('auth/register', [
                'title' => 'Inscription',
                'errors' => ['Erreur lors de l\'inscription'],
                'old' => $data
            ]);
        }
    }

    /**
     * Déconnexion
     */
    public function logout() {
        // Détruire la session
        session_unset();
        session_destroy();

        // Supprimer le cookie de session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        $this->redirect('/');
    }

    /**
     * Créer une session utilisateur
     */
    private function createUserSession($user, $remember = false) {
        // Régénérer l'ID de session pour sécurité
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();

        // Cookie "Remember me" (30 jours)
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $_SESSION['remember_token'] = $token;
            setcookie('remember_token', $token, time() + (86400 * 30), '/', '', true, true);
        }
    }
}