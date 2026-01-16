<?php
namespace Core;
use Core\Database;

class Controller {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

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

    protected function isLoggedIn() {
        return isLoggedIn(); // Appelle la fonction du helper
    }

    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
}
