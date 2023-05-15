<?php

namespace Eatfit\Site\Core\Middlewares;

use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Exception\ForbiddenException;

class AuthMiddleware extends BaseMiddleware
{
    protected array $actions = [];

    /**
     * AuthMiddleware constructor.
     *
     * @param array $actions Les actions qui nécessitent une authentification.
     */
    public function __construct($actions = [])
    {
        $this->actions = $actions;
    }

    /**
     * Exécute le middleware.
     *
     * @throws ForbiddenException Si l'utilisateur n'est pas authentifié.
     */
    public function execute(): void
    {
        if (Application::isGuest()) {
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                throw new ForbiddenException();
            }
        }
    }
}