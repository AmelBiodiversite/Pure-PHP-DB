<?php
/**
 * ================================================================
 * MARKETFLOW PRO - SYSTÈME DE ROUTING
 * ================================================================
 * 
 * Fichier : core/Router.php
 * Version : 2.1
 * Date : 21 janvier 2025
 * 
 * DESCRIPTION :
 * Classe Router personnalisée pour gérer le routing de l'application.
 * Convertit les URLs en appels de contrôleurs/méthodes.
 * 
 * FONCTIONNALITÉS :
 * - Routing GET et POST
 * - Paramètres dynamiques dans les URLs ({id}, {slug}, etc.)
 * - Support des contrôleurs avec namespace (App\Controllers)
 * - Gestion d'erreurs 404 personnalisée
 * - Regex pour patterns d'URL complexes
 * 
 * UTILISATION :
 * $router = new Core\Router();
 * $router->get('/products/{id}', 'ProductController@show');
 * $router->post('/cart/add', 'CartController@add');
 * $router->dispatch();
 * 
 * PARAMÈTRES DYNAMIQUES :
 * {id} - Capturé comme paramètre numérique/alphanumérique
 * {slug} - Capturé comme chaîne pour URLs friendly
 * {username} - Capturé pour profils utilisateurs
 * 
 * ARCHITECTURE :
 * 1. addRoute() : Enregistre une route avec son pattern regex
 * 2. dispatch() : Analyse l'URL et exécute le contrôleur approprié
 * 3. error404() : Affiche une page 404 en cas de route non trouvée
 * 
 * ================================================================
 */

namespace Core;

class Router {

    /**
     * Tableau stockant toutes les routes enregistrées
     * Structure : ['method' => 'GET|POST', 'pattern' => 'regex', 'callback' => 'Controller@method', 'path' => '/original/path']
     * @var array
     */
    private $routes = [];

    // ================================================================
    // MÉTHODES PUBLIQUES - Enregistrement des routes
    // ================================================================

    /**
     * Enregistre une route GET
     * 
     * @param string $path - Chemin de l'URL (ex: '/products/{id}')
     * @param string|callable $callback - Contrôleur@méthode ou fonction callback
     * 
     * Exemple :
     * $router->get('/products', 'ProductController@index');
     * $router->get('/products/{slug}', 'ProductController@show');
     */
    public function get($path, $callback) { 
        $this->addRoute('GET', $path, $callback); 
    }

    /**
     * Enregistre une route POST
     * 
     * @param string $path - Chemin de l'URL
     * @param string|callable $callback - Contrôleur@méthode ou fonction callback
     * 
     * Exemple :
     * $router->post('/login', 'AuthController@login');
     * $router->post('/cart/add', 'CartController@add');
     * $router->post('/admin/products/{id}/approve', 'AdminController@approveProduct');
     */
    public function post($path, $callback) { 
        $this->addRoute('POST', $path, $callback); 
    }

    // ================================================================
    // MÉTHODES PRIVÉES - Logique interne
    // ================================================================

    /**
     * Ajoute une route au système avec conversion en regex
     * 
     * TRANSFORMATION DES PARAMÈTRES :
     * '/products/{id}' devient '#^/products/(?P<id>[^/]+)$#'
     * - {id} est capturé dans un groupe nommé
     * - [^/]+ capture tout caractère sauf le slash
     * - ^ et $ forcent une correspondance exacte
     * 
     * @param string $method - GET ou POST
     * @param string $path - Chemin original avec placeholders
     * @param string|callable $callback - Action à exécuter
     */
    private function addRoute($method, $path, $callback) {
        // Convertir les placeholders {param} en groupes regex nommés
        // Exemple : /products/{id}/edit devient /products/(?P<id>[^/]+)/edit
        $pattern = '#^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path) . '$#';

        // Stocker la route avec toutes ses métadonnées
        $this->routes[] = compact('method', 'pattern', 'callback', 'path');
    }

    /**
     * Dispatcher - Cœur du système de routing
     * 
     * PROCESSUS :
     * 1. Récupère l'URI et la méthode HTTP de la requête
     * 2. Parcourt toutes les routes enregistrées
     * 3. Compare l'URI avec les patterns regex
     * 4. Si match trouvé : extrait les paramètres et exécute le contrôleur
     * 5. Si aucun match : affiche une erreur 404
     * 
     * GESTION DES PARAMÈTRES :
     * - Les paramètres {id}, {slug}, etc. sont extraits de l'URL
     * - Ils sont passés comme arguments positionnels à la méthode du contrôleur
     * - Exemple : /products/123 → ProductController->show(123)
     */
    public function dispatch() {
        // Récupérer l'URI sans les paramètres GET (?key=value)
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Récupérer la méthode HTTP (GET, POST, PUT, DELETE, etc.)
        $method = $_SERVER['REQUEST_METHOD'];

        // Normaliser l'URI vide en '/'
        if ($uri === '') {
            $uri = '/';
        }

        // Parcourir toutes les routes enregistrées
        foreach ($this->routes as $route) {

            // Vérifier si la méthode HTTP correspond ET si le pattern regex match
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {

                // ========================================================
                // EXTRACTION DES PARAMÈTRES
                // ========================================================
                // preg_match retourne un tableau avec :
                // - Indices numériques : [0] = URL complète, [1], [2]... = groupes capturés
                // - Clés string : ['id'] = valeur du paramètre {id}
                //
                // Exemple pour /admin/products/61/approve :
                // $matches = [
                //     0 => '/admin/products/61/approve',
                //     'id' => '61',
                //     1 => '61'  ← doublon numérique
                // ]
                //
                // On garde SEULEMENT les clés string avec array_filter
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                // Résultat : ['id' => '61']

                $callback = $route['callback'];

                // ========================================================
                // CAS 1 : CALLBACK EST UNE STRING (Controller@method)
                // ========================================================
                if (is_string($callback)) {

                    // Séparer le nom du contrôleur et de la méthode
                    // Exemple : 'AdminController@approveProduct'
                    list($controllerName, $methodName) = explode('@', $callback);

                    // Construire le nom complet de la classe avec namespace
                    // Exemple : 'App\Controllers\AdminController'
                    $fullControllerClass = "App\\Controllers\\" . $controllerName;

                    // Vérifier que la classe existe
                    if (class_exists($fullControllerClass)) {

                        // Instancier le contrôleur
                        $controllerInstance = new $fullControllerClass();

                        // Vérifier que la méthode existe dans le contrôleur
                        if (method_exists($controllerInstance, $methodName)) {

                            // ============================================
                            // 🔥 FIX CRUCIAL - PASSAGE DES PARAMÈTRES
                            // ============================================
                            // PROBLÈME AVANT :
                            // call_user_func_array passait ['id' => 61]
                            // Mais PHP attend [0 => 61] pour $id en paramètre positionnel
                            //
                            // SOLUTION :
                            // array_values() convertit ['id' => 61] en [0 => 61]
                            // Maintenant approveProduct($id) reçoit correctement 61
                            //
                            // Exemple :
                            // Avant : approveProduct(['id' => 61]) ❌
                            // Après : approveProduct(61) ✅

                            return call_user_func_array(
                                [$controllerInstance, $methodName], 
                                array_values($params) // ← Convertit tableau associatif en tableau indexé
                            );

                        } else {
                            // Méthode introuvable dans le contrôleur
                            $this->error404("Méthode <strong>{$methodName}</strong> introuvable dans <strong>{$controllerName}</strong>");
                        }

                    } else {
                        // Classe contrôleur introuvable
                        $this->error404("Contrôleur <strong>{$fullControllerClass}</strong> introuvable");
                    }

                // ========================================================
                // CAS 2 : CALLBACK EST UNE FONCTION ANONYME
                // ========================================================
                } elseif (is_callable($callback)) {

                    // Exécuter directement la fonction avec les paramètres
                    // Utilisé rarement, mais supporté pour flexibilité
                    return call_user_func_array($callback, array_values($params));
                }
            }
        }

        // ============================================================
        // AUCUNE ROUTE NE CORRESPOND → 404
        // ============================================================
        $this->error404();
    }

    /**
     * Affiche une page d'erreur 404 personnalisée
     * 
     * @param string $msg - Message de debug optionnel (contrôleur/méthode manquant)
     * 
     * COMPORTEMENT :
     * - Définit le code HTTP 404
     * - Affiche un message utilisateur friendly
     * - Affiche un message de debug si fourni (mode dev)
     * - Stoppe l'exécution du script
     */
    private function error404($msg = '') { 

        // Définir le code de réponse HTTP 404
        http_response_code(404);

        // Affichage HTML simple mais propre
        echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page non trouvée</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .error-container {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
        }
        h1 {
            font-size: 6rem;
            margin: 0;
            color: #667eea;
            font-weight: bold;
        }
        h2 {
            color: #333;
            margin: 1rem 0;
        }
        p {
            color: #666;
            line-height: 1.6;
        }
        .debug {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            color: #d32f2f;
            font-size: 0.9rem;
            font-family: monospace;
        }
        a {
            display: inline-block;
            margin-top: 2rem;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        a:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <h2>Page non trouvée</h2>
        <p>Désolé, la page que vous recherchez n\'existe pas ou a été déplacée.</p>';

        // Afficher le message de debug si fourni (mode développement)
        if ($msg) {
            echo '<div class="debug">🐛 Debug : ' . $msg . '</div>';
        }

        echo '
        <a href="/">← Retour à l\'accueil</a>
    </div>
</body>
</html>';

        // Arrêter l'exécution du script
        exit;
    }
}

/**
 * ================================================================
 * NOTES DE MAINTENANCE POUR LES DÉVELOPPEURS
 * ================================================================
 * 
 * AJOUTER UN NOUVEAU TYPE DE MÉTHODE HTTP :
 * 
 * public function put($path, $callback) { 
 *     $this->addRoute('PUT', $path, $callback); 
 * }
 * 
 * public function delete($path, $callback) { 
 *     $this->addRoute('DELETE', $path, $callback); 
 * }
 * 
 * ----------------------------------------------------------------
 * 
 * DEBUGGING DES ROUTES :
 * 
 * Ajouter temporairement dans dispatch() avant le foreach :
 * 
 * echo '<pre>';
 * echo "URI demandée : {$uri}\n";
 * echo "Méthode : {$method}\n";
 * echo "Routes enregistrées :\n";
 * print_r($this->routes);
 * echo '</pre>';
 * 
 * ----------------------------------------------------------------
 * 
 * TESTER LES REGEX :
 * 
 * $test_uri = '/admin/products/123/approve';
 * $pattern = '#^/admin/products/(?P<id>[^/]+)/approve$#';
 * if (preg_match($pattern, $test_uri, $matches)) {
 *     print_r($matches);
 * }
 * 
 * ----------------------------------------------------------------
 * 
 * PROBLÈMES COURANTS :
 * 
 * 1. Route non trouvée (404) :
 *    - Vérifier que la route est définie AVANT dispatch()
 *    - Vérifier l'orthographe du contrôleur (case-sensitive)
 *    - Vérifier que le contrôleur a le bon namespace (App\Controllers)
 * 
 * 2. Paramètres non reçus :
 *    - Vérifier que array_values() est utilisé
 *    - Vérifier que le nombre de paramètres correspond
 * 
 * 3. Contrôleur introuvable :
 *    - Vérifier l'autoloader dans index.php
 *    - Vérifier le nom de fichier (AdminController.php)
 *    - Vérifier le namespace dans le contrôleur
 * 
 * ================================================================
 */