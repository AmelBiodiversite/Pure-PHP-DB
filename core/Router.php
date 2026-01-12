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
    private $basePath = '';

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

        // 🔹 Debug log
        // error_log("Request URI: " . $uri);
        // error_log("Method: " . $method);

        // 🔹 Retirer automatiquement le préfixe du projet Replit
        if (!empty($this->basePath) && str_starts_with($uri, $this->basePath)) {
            $uri = substr($uri, strlen($this->basePath));
        }
        
        // S'assurer que l'URI commence par / et n'est pas vide
        if (empty($uri)) $uri = '/';
        
        // 🔹 Debug log
        // error_log("Processed URI: " . $uri);

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
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Page non trouvée</h1>";
        if ($msg) echo "<p><small>{$msg}</small></p>";
        
        echo "<h3>Debug Info:</h3>";
        echo "<ul>";
        echo "<li>URI: " . htmlspecialchars(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) . "</li>";
        echo "<li>Method: " . htmlspecialchars($_SERVER['REQUEST_METHOD']) . "</li>";
        echo "<li>Base Path: '" . htmlspecialchars($this->basePath) . "'</li>";
        echo "</ul>";
        
        echo "<h3>Routes enregistrées :</h3>";
        echo "<ul>";
        foreach ($this->routes as $r) {
            echo "<li>" . htmlspecialchars($r['method']) . " " . htmlspecialchars($r['path']) . " (Pattern: " . htmlspecialchars($r['pattern']) . ")</li>";
        }
        echo "</ul>";
        exit;
    }
}
