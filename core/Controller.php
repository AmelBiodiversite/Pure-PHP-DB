<?php
namespace Core;
use Core\Database;

/**
 * Classe Controller de base
 * Tous les controllers héritent de cette classe
 */
class Controller {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Rendre une vue avec layout (header + footer)
     * 
     * @param string $view Chemin de la vue (ex: 'home/index')
     * @param array $data Données à passer à la vue
     */
    protected function render($view, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../app/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once __DIR__ . '/../app/views/layouts/header.php';
            require_once $viewFile;
            require_once __DIR__ . '/../app/views/layouts/footer.php';
        } else {
            die("Vue introuvable : $view");
        }
    }

    /**
     * Vérifie si l'utilisateur est connecté
     * 
     * @return bool
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Redirige vers une URL
     * 
     * @param string $url URL de destination
     */
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }

    /**
     * Exige que l'utilisateur soit connecté
     * Redirige vers /login si non connecté
     */
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/account';
            $this->redirect('/login?error=login_required');
        }
    }

    /**
     * Exige que l'utilisateur soit un vendeur
     * Redirige vers / si non vendeur
     */
    protected function requireSeller() {
        $this->requireLogin(); // D'abord vérifier qu'il est connecté
        
        $user = $this->getCurrentUser();
        if (!$user || $user['role'] !== 'seller') {
            $this->redirect('/?error=seller_only');
        }
    }

    /**
     * Exige que l'utilisateur soit un admin
     * Redirige vers / si non admin
     */
    protected function requireAdmin() {
        $this->requireLogin(); // D'abord vérifier qu'il est connecté
        
        $user = $this->getCurrentUser();
        if (!$user || $user['role'] !== 'admin') {
            $this->redirect('/?error=admin_only');
        }
    }

    /**
     * Exige que l'utilisateur NE SOIT PAS connecté (pour login/register)
     * Redirige vers /account si déjà connecté
     */
    protected function requireGuest() {
        if ($this->isLoggedIn()) {
            $this->redirect('/account');
        }
    }

    /**
     * Récupère les informations de l'utilisateur connecté
     * 
     * @return array|null Données utilisateur ou null si non connecté
     */
    protected function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT id, username, email, full_name, role, 
                       shop_name, shop_slug, avatar_url, created_at
                FROM users 
                WHERE id = :user_id AND is_active = TRUE
                LIMIT 1
            ");
            $stmt->execute(['user_id' => $_SESSION['user_id']]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $user ?: null;
        } catch (\PDOException $e) {
            error_log("Erreur getCurrentUser: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Vérifie si l'utilisateur connecté est vendeur
     * 
     * @return bool
     */
    protected function isSeller() {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === 'seller';
    }

    /**
     * Vérifie si l'utilisateur connecté est admin
     * 
     * @return bool
     */
    protected function isAdmin() {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === 'admin';
    }

    /**
     * Affiche un message flash et redirige
     * 
     * @param string $url URL de redirection
     * @param string $message Message à afficher
     * @param string $type Type de message (success, error, warning, info)
     */
    protected function redirectWithMessage($url, $message, $type = 'info') {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
        $this->redirect($url);
    }

    /**
     * Retourne une réponse JSON
     * 
     * @param mixed $data Données à retourner
     * @param int $statusCode Code HTTP
     */
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
