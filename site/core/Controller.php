<?php

namespace Eatfit\Site\Core;

use Eatfit\Site\Core\Middlewares\BaseMiddleware;

/**
 * La classe Controller est la classe de base pour tous les contrôleurs de l'application.
 * Elle fournit des fonctionnalités communes aux contrôleurs tels que la gestion du layout, le rendu des vues
 * et la gestion des middlewares.
 */
class Controller
{
    public string $layout = 'main';
    public string $action = '';

    protected array $middlewares = [];

    /**
     * Rend la vue spécifiée avec les paramètres donnés.
     *
     * @param string $view Le nom de la vue à rendre.
     * @param array $params Les paramètres à passer à la vue.
     * @return string Le contenu de la vue rendue.
     */
    public function render(string $view, array $params = []): string
    {
        return Application::$app->router->renderView($view, $params);
    }

    /**
     * Enregistre un middleware à appliquer aux actions du contrôleur.
     *
     * @param BaseMiddleware $middleware Le middleware à enregistrer.
     * @return void
     */
    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Récupère les middlewares enregistrés pour le contrôleur.
     *
     * @return array Les middlewares enregistrés.
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}