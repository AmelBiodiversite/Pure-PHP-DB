<?php
/**
 * MARKETFLOW PRO - CONTRÔLEUR DE BASE
 * Fichier : core/Controller.php
 */

namespace Core;

use Core\Database;

class Controller {

    protected $db;

    /**
     * Constructeur - Initialise la connexion DB
     */
    public function __construct() {
        // Démarrer la session si pas déjà fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Initialiser la connexion DB
        $this->db = Database::getInstance();
    }

    /**
     * Charger une vue
     */
    protected function view($view, $data = []) {
    extract($data);
    
    $viewFile = __DIR__ . '/../app/views/' . $view . '.php';
    
    if (file_exists($viewFile)) {
        require_once __DIR__ . '/../app/views/layouts/header.php';  // ← DOIT être là
        require_once $viewFile;
        require_once __DIR__ . '/../app/views/layouts/footer.php';  // ← DOIT être là
    } else {
        die("Vue introuvable : $view");
    }
}

    /**
     * Redirection
     */
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }

    /**
     * Réponse JSON
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Vérifier si l'utilisateur est connecté
     */
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page";
            $this->redirect('/login');
        }
    }

    /**
     * Récupérer l'utilisateur actuel
     */
    protected function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        return $stmt->fetch();
    }

    /**
     * Vérifier si l'utilisateur est admin
     */
    protected function requireAdmin() {
        $this->requireAuth();

        $user = $this->getCurrentUser();

        if (!$user || $user['role'] !== 'admin') {
            $_SESSION['error'] = "Accès réservé aux administrateurs";
            $this->redirect('/');
        }
    }

    /**
     * Vérifier si l'utilisateur est vendeur
     */
    protected function requireSeller() {
        $this->requireAuth();

        $user = $this->getCurrentUser();

        if (!$user || !in_array($user['role'], ['seller', 'admin'])) {
            $_SESSION['error'] = "Accès réservé aux vendeurs";
            $this->redirect('/');
        }
    }

    /**
     * Vérifier le token CSRF
     */
    protected function verifyCsrf() {
        $token = $_POST['csrf_token'] ?? '';

        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            $this->json(['error' => 'Token CSRF invalide'], 403);
        }
    }
}