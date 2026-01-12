<?php
/**
 * Système de routing simple et efficace
 * Version Replit ready : gère le préfixe du projet automatiquement
 */
namespace Core;

class Router {
    private $routes = [];
    private $currentRoute = null;

    // ⚡ Indique ici le nom de ton projet Replit
    private $basePath = '/Pure-PHP-DB';

    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }

    private function addRoute($method, $path, $callback) {
        $pattern = $this->convertToPattern($path);
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback,
            'path' => $path
        ];
    }

    private function convertToPattern($path) {
        // Convertir /product/{id} en regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    public function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // 🔹 Retirer automatiquement le préfixe du projet Replit
        if (str_starts_with($uri, $this->basePath)) {
            $uri = substr($uri, strlen($this->basePath));
        }
        if ($uri === '') $uri = '/';

        // 🔹 Chercher la route correspondante
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $callback = $route['callback'];

                if (is_string($callback)) {
                    list($controller, $methodName) = explode('@', $callback);
                    $controller = "App\\Controllers\\{$controller}";

                    if (class_exists($controller)) {
                        $controllerInstance = new $controller();
                        if (method_exists($controllerInstance, $methodName)) {
                            return call_user_func_array([$controllerInstance, $methodName], $params);
                        } else {
                            $this->error404("Méthode {$methodName} introuvable dans {$controller}");
                        }
                    } else {
                        $this->error404("Classe {$controller} introuvable");
                    }
                } elseif (is_callable($callback)) {
                    return call_user_func_array($callback, $params);
                }
            }
        }

        // 404 si aucune route ne match
        $this->error404();
    }

    private function error404($msg = '') {
        http_response_code(404);
        echo "404 - Page non trouvée";
        if ($msg) echo "<br><small>{$msg}</small>";
        exit;
    }
}
