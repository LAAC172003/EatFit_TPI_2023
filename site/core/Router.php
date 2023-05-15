<?php


namespace Eatfit\Site\Core;


use Eatfit\Site\Core\Exception\NotFoundException;

/**
 * La classe Router gère le routage des requêtes HTTP vers les actions correspondantes.
 */
class Router
{
    private Request $request;
    private Response $response;
    private array $routeMap = [];

    /**
     * Constructeur de la classe Router.
     *
     * @param Request $request L'objet Request pour récupérer les informations de la requête.
     * @param Response $response L'objet Response pour gérer la réponse HTTP.
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Enregistre une route pour la méthode GET.
     *
     * @param string $url L'URL de la route.
     * @param mixed $callback Le callback à exécuter lorsque la route est atteinte.
     */
    public function get(string $url, mixed $callback): void
    {
        $this->routeMap['get'][$url] = $callback;
    }

    /**
     * Enregistre une route pour la méthode POST.
     *
     * @param string $url L'URL de la route.
     * @param mixed $callback Le callback à exécuter lorsque la route est atteinte.
     */
    public function post(string $url, mixed $callback): void
    {
        $this->routeMap['post'][$url] = $callback;
    }

    /**
     * Résout la route actuelle de la requête et exécute le callback correspondant.
     *
     * @return mixed Le résultat du callback exécuté.
     * @throws NotFoundException Si aucune route ne correspond à la route actuelle de la requête.
     */
    public function resolve(): mixed
    {
        $method = $this->request->getMethod();
        $url = $this->request->getUrl();
        $callback = $this->routeMap[$method][$url] ?? false;
        if (!$callback) {

            $callback = $this->getCallback();

            if ($callback === false) {
                throw new NotFoundException();
            }
        }
        if (is_string($callback)) {
            return $this->renderView($callback);
        }
        if (is_array($callback)) {
            /**
             * @var $controller Controller
             */
            $controller = new $callback[0];
            $controller->action = $callback[1];

            Application::$app->controller = $controller;
            $middlewares = $controller->getMiddlewares();
            foreach ($middlewares as $middleware) {
                $middleware->execute();
            }
            $callback[0] = $controller;
        }
        return call_user_func($callback, $this->request, $this->response);
    }

    /**
     * Récupère le callback correspondant à la route actuelle de la requête.
     *
     * @return mixed Le callback correspondant à la route actuelle, ou false si aucune route ne correspond.
     */
    public function getCallback(): mixed
    {
        $method = $this->request->getMethod();
        $url = $this->request->getUrl();
        // Trim slashes
        $url = trim($url, '/');

        // Get all routes for current request method
        $routes = $this->getRouteMap($method);

        $routeParams = false;

        // Start iterating registed routes
        foreach ($routes as $route => $callback) {
            // Trim slashes
            $route = trim($route, '/');
            $routeNames = [];

            if (!$route) {
                continue;
            }

            // Find all route names from route and save in $routeNames
            if (preg_match_all('/\{(\w+)(:[^}]+)?}/', $route, $matches)) {
                $routeNames = $matches[1];
            }

            // Convert route name into regex pattern
            $routeRegex = "@^" . preg_replace_callback('/\{\w+(:([^}]+))?}/', fn($m) => isset($m[2]) ? "({$m[2]})" : '(\w+)', $route) . "$@";

            // Test and match current route against $routeRegex
            if (preg_match_all($routeRegex, $url, $valueMatches)) {
                $values = [];
                for ($i = 1; $i < count($valueMatches); $i++) {
                    $values[] = $valueMatches[$i][0];
                }
                $routeParams = array_combine($routeNames, $values);

                $this->request->setRouteParams($routeParams);
                return $callback;
            }
        }

        return false;
    }

    /**
     * Récupère la liste des routes enregistrées pour une méthode donnée.
     *
     * @param string $method La méthode HTTP (get, post, etc.).
     * @return array Les routes enregistrées pour la méthode donnée.
     */
    public function getRouteMap(string $method): array
    {
        return $this->routeMap[$method] ?? [];
    }

    /**
     * Rend la vue spécifiée avec les paramètres donnés.
     *
     * @param string $view Le nom de la vue.
     * @param array $params Les paramètres à passer à la vue.
     * @return string Le contenu de la vue rendue.
     */
    public function renderView($view, $params = []): false|array|string
    {
        return Application::$app->view->renderView($view, $params);
    }
}
