<?php
namespace Core;

class Router {
    private $routes = [];

    public function get($path, $callback) { $this->addRoute('GET', $path, $callback); }
    public function post($path, $callback) { $this->addRoute('POST', $path, $callback); }

    private function addRoute($method, $path, $callback) {
        $pattern = '#^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path) . '$#';
        $this->routes[] = compact('method', 'pattern', 'callback', 'path');
    }

    public function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        if($uri === '') $uri = '/';

        foreach($this->routes as $route){
            if($route['method']===$method && preg_match($route['pattern'], $uri, $matches)){
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $callback = $route['callback'];

                if(is_string($callback)){
                    list($controllerName, $methodName) = explode('@', $callback);
                    $fullControllerClass = "App\\Controllers\\" . $controllerName;

                    if (class_exists($fullControllerClass)) {
                        $controllerInstance = new $fullControllerClass();
                        if(method_exists($controllerInstance, $methodName)){
                            return call_user_func_array([$controllerInstance, $methodName], $params);
                        } else $this->error404("Méthode {$methodName} introuvable");
                    } else $this->error404("Classe {$fullControllerClass} introuvable");
                } elseif(is_callable($callback)){
                    return call_user_func_array($callback, $params);
                }
            }
        }
        $this->error404();
    }

    private function error404($msg=''){ 
        http_response_code(404); 
        echo "404 - Page non trouvée"; 
        if($msg) echo "<br><small>{$msg}</small>"; 
        exit; 
    }
}
