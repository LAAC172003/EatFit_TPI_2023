<?php

namespace Eatfit\Api\Core;

use Exception;

class Router
{
    private Request $request;
    private array $routeMap = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function get(string $url, mixed $callback): void
    {
        $this->addRoute('get', $url, $callback);
    }

    /**
     * Ajoute une route pour une méthode HTTP spécifiée
     *
     * @param string $method La méthode HTTP (ex: GET, POST, etc.)
     * @param string $url L'URL à associer à la route
     * @param mixed $callback Le callback à appeler lorsque la route est atteinte
     * @return void
     */
    private function addRoute(string $method, string $url, mixed $callback): void
    {
        $this->routeMap[strtolower($method)][$url] = $callback;
    }

    public function post(string $url, mixed $callback): void
    {
        $this->addRoute('post', $url, $callback);
    }

    public function put(string $url, mixed $callback): void
    {
        $this->addRoute('put', $url, $callback);
    }

    public function delete(string $url, mixed $callback): void
    {
        $this->addRoute('delete', $url, $callback);
    }

    public function getRoutes(): array
    {
        return $this->routeMap;
    }

    /**
     * Résout la requête HTTP en appelant le bon callback
     *
     * @throws Exception Si la méthode HTTP n'est pas autorisée ou si aucune route n'a été trouvée pour l'URL de la requête courante
     * @return mixed La réponse retournée par le callback appelé
     */
    public function resolve(): mixed
    {
        $method = $this->request->getMethod();
        $url = $this->request->getUrl();

        $callback = $this->routeMap[$method][$url] ?? false;
        if (!$callback) {
            $callback = $this->getCallback();
            $routes = [];
            foreach ($this->routeMap as $key => $value) {
                foreach ($value as $k => $v) {
                    if (in_array($k, $routes)) continue;
                    $routes[] = $k;
                }
            }
            if (in_array($url, $routes)) throw new Exception("La méthode $method n'est pas autorisée pour la route : '$url'", 405);
            if ($callback === false) throw new Exception("Aucune route trouvée pour la route : $url", 404);
        }
        if (is_array($callback)) $callback = [new $callback[0], $callback[1]];
        return call_user_func($callback, $this->request);
    }

    /**
     * Récupère le callback associé à l'URL de la requête courante
     *
     * @return mixed Le callback associé à l'URL de la requête courante, ou false si aucun callback trouvé
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
            if (!$route) continue;
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

    public function getRouteMap(string $method): array
    {
        return $this->routeMap[strtolower($method)] ?? [];
    }
}
