<?php
namespace Core;

use Database; // Assurez-vous que la classe Database est dans le namespace global ou ajustez ce "use"

/**
 * Contrôleur de base
 */
class Controller {
    protected $db;

    public function __construct() {
        // Vérifie que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Connexion à la base
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Affiche une vue
     */
    protected function view($view, $data = []) {
        extract($data);

        $viewPath = APP_PATH . "/views/{$view}.php";

        if (file_exists($viewPath)) {
            require_once APP_PATH . '/views/layouts/header.php';
            require_once $viewPath;
            require_once APP_PATH . '/views/layouts/footer.php';
        } else {
            die("Vue introuvable : {$view}");
        }
    }

    /**
     * Renvoie du JSON
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirection HTTP
     */
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }

    /**
     * Vérifie si l'utilisateur est connecté
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Récupère l'utilisateur courant
     */
    protected function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Nécessite une connexion utilisateur
     */
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }

    /**
     * Nécessite que l'utilisateur soit vendeur
     */
    protected function requireSeller() {
        $user = $this->getCurrentUser();
        if (!$user || $user['user_type'] !== 'seller') {
            $this->redirect('/');
        }
    }

    /**
     * Nécessite que l'utilisateur soit admin
     */
    protected function requireAdmin() {
        $user = $this->getCurrentUser();
        if (!$user || $user['user_type'] !== 'admin') {
            $this->redirect('/');
        }
    }
}