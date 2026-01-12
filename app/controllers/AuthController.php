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
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } else {
            $this->view('auth/login', [
                'title' => 'Connexion',
                'csrf_token' => generateCsrfToken()
            ]);
        }
    }

    /**
     * Traiter la connexion
     */
    private function handleLogin() {
        // Vérifier le token CSRF
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            $this->json(['success' => false, 'error' => 'Token CSRF invalide'], 403);
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validation basique
        if (empty($email) || empty($password)) {
            $this->view('auth/login', [
                'title' => 'Connexion',
                'error' => 'Veuillez remplir tous les champs',
                'email' => $email,
                'csrf_token' => generateCsrfToken()
            ]);
            return;
        }

        // Tenter la connexion
        $result = $this->userModel->login($email, $password);

        if ($result['success']) {
            // Créer la session
            $this->createUserSession($result['user'], $remember);

            // Rediriger selon le type d'utilisateur
            if ($result['user']['user_type'] === 'seller') {
                $this->redirect('/seller/dashboard');
            } elseif ($result['user']['user_type'] === 'admin') {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/');
            }
        } else {
            $this->view('auth/login', [
                'title' => 'Connexion',
                'error' => $result['error'],
                'email' => $email,
                'csrf_token' => generateCsrfToken()
            ]);
        }
    }

    /**
     * Page d'inscription
     */
    public function register() {
        // Si déjà connecté, rediriger
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
        } else {
            $this->view('auth/register', [
                'title' => 'Inscription',
                'csrf_token' => generateCsrfToken()
            ]);
        }
    }

    /**
     * Traiter l'inscription
     */
    private function handleRegister() {
        // Vérifier le token CSRF
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            $this->json(['success' => false, 'error' => 'Token CSRF invalide'], 403);
        }

        $data = [
            'email' => $_POST['email'] ?? '',
            'username' => $_POST['username'] ?? '',
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
            'user_type' => $_POST['user_type'] ?? 'buyer',
            'shop_name' => $_POST['shop_name'] ?? null
        ];

        // Tenter l'inscription
        $result = $this->userModel->register($data);

        if ($result['success']) {
            // Récupérer l'utilisateur créé
            $user = $this->userModel->find($result['user_id']);
            unset($user['password']);

            // Créer la session
            $this->createUserSession($user);

            // Message de succès
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = 'success';

            // Rediriger selon le type
            if ($user['user_type'] === 'seller') {
                $this->redirect('/seller/dashboard');
            } else {
                $this->redirect('/');
            }
        } else {
            $this->view('auth/register', [
                'title' => 'Inscription',
                'errors' => $result['errors'] ?? [],
                'old' => $data,
                'csrf_token' => generateCsrfToken()
            ]);
        }
    }

    /**
     * Déconnexion
     */
    public function logout() {
        // Logger l'activité avant de détruire la session
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->db->prepare("
                INSERT INTO activity_logs (user_id, action, entity_type, entity_id, created_at)
                VALUES (?, 'user_logged_out', 'user', ?, ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $_SESSION['user_id'],
                date('Y-m-d H:i:s')
            ]);
        }

        // Détruire la session
        session_unset();
        session_destroy();

        // Supprimer le cookie de session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        redirectWithMessage('/', 'Vous êtes déconnecté', 'success');
    }

    /**
     * Page de profil utilisateur
     */
    public function profile() {
        $this->requireLogin();

        $user = $this->userModel->getUserWithProfile($_SESSION['user_id']);

        if (!$user) {
            $this->redirect('/login');
        }

        // Récupérer les statistiques si vendeur
        $stats = null;
        if ($user['user_type'] === 'seller') {
            $stats = $this->userModel->getSellerStats($user['id']);
        }

        // Récupérer les dernières commandes
        $orders = $this->userModel->getUserOrders($user['id'], 5);

        $this->view('user/profile', [
            'title' => 'Mon Profil',
            'user' => $user,
            'stats' => $stats,
            'orders' => $orders,
            'csrf_token' => generateCsrfToken()
        ]);
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }

        // Vérifier CSRF
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            $this->json(['success' => false, 'error' => 'Token invalide'], 403);
        }

        $data = [
            'full_name' => $_POST['full_name'] ?? '',
            'username' => $_POST['username'] ?? '',
            'bio' => $_POST['bio'] ?? ''
        ];

        // Gérer l'upload d'avatar
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleAvatarUpload($_FILES['avatar']);
            if ($uploadResult['success']) {
                $data['avatar'] = $uploadResult['path'];
            }
        }

        $result = $this->userModel->updateProfile($_SESSION['user_id'], $data);

        if ($result['success']) {
            // Mettre à jour les infos en session
            $_SESSION['user_name'] = $data['username'];
            
            redirectWithMessage('/profile', $result['message'], 'success');
        } else {
            redirectWithMessage('/profile', $result['error'], 'error');
        }
    }

    /**
     * Mettre à jour le profil vendeur
     */
    public function updateSellerProfile() {
        $this->requireSeller();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }

        // Vérifier CSRF
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            $this->json(['success' => false, 'error' => 'Token invalide'], 403);
        }

        $data = [
            'shop_name' => $_POST['shop_name'] ?? '',
            'shop_description' => $_POST['shop_description'] ?? '',
            'payout_email' => $_POST['payout_email'] ?? '',
            'payout_method' => $_POST['payout_method'] ?? 'paypal'
        ];

        // Gérer les uploads d'images
        if (isset($_FILES['shop_logo']) && $_FILES['shop_logo']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleImageUpload($_FILES['shop_logo'], 'shops');
            if ($uploadResult['success']) {
                $data['shop_logo'] = $uploadResult['path'];
            }
        }

        if (isset($_FILES['shop_banner']) && $_FILES['shop_banner']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleImageUpload($_FILES['shop_banner'], 'shops');
            if ($uploadResult['success']) {
                $data['shop_banner'] = $uploadResult['path'];
            }
        }

        $result = $this->userModel->updateSellerProfile($_SESSION['user_id'], $data);

        redirectWithMessage('/profile', $result['message'] ?? $result['error'], 
                          $result['success'] ? 'success' : 'error');
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }

        // Vérifier CSRF
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            $this->json(['success' => false, 'error' => 'Token invalide'], 403);
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            redirectWithMessage('/profile', 'Veuillez remplir tous les champs', 'error');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            redirectWithMessage('/profile', 'Les mots de passe ne correspondent pas', 'error');
            return;
        }

        $result = $this->userModel->changePassword(
            $_SESSION['user_id'],
            $currentPassword,
            $newPassword
        );

        redirectWithMessage('/profile', $result['message'] ?? $result['error'], 
                          $result['success'] ? 'success' : 'error');
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
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();

        // Cookie "Remember me" (30 jours)
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $_SESSION['remember_token'] = $token;
            setcookie('remember_token', $token, time() + (86400 * 30), '/', '', true, true);
        }
    }

    /**
     * Gérer l'upload d'avatar
     */
    private function handleAvatarUpload($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Type de fichier non autorisé'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Fichier trop volumineux (max 2MB)'];
        }

        // Créer le dossier si nécessaire
        $uploadDir = UPLOAD_PATH . '/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'path' => '/public/uploads/avatars/' . $filename
            ];
        }

        return ['success' => false, 'error' => 'Erreur lors de l\'upload'];
    }

    /**
     * Gérer l'upload d'image générique
     */
    private function handleImageUpload($file, $folder) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Type de fichier non autorisé'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Fichier trop volumineux (max 5MB)'];
        }

        $uploadDir = UPLOAD_PATH . "/{$folder}/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'path' => "/public/uploads/{$folder}/" . $filename
            ];
        }

        return ['success' => false, 'error' => 'Erreur lors de l\'upload'];
    }
}