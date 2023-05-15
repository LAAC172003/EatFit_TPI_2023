<?php

namespace Eatfit\Site\Core;


/**
 * La classe Request représente une requête HTTP entrante.
 * Elle fournit des méthodes pour récupérer des informations sur la requête.
 */
class Request
{
    private array $routeParams = [];

    /**
     * Retourne l'URL de la requête.
     *
     * @return string L'URL de la requête.
     */
    public function getUrl(): string
    {
        $path = $_SERVER['REQUEST_URI'];
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        return $path;
    }

    /**
     * Récupère les données de la requête.
     *
     * @return array Les données de la requête.
     */
    public function getBody(): array
    {
        $data = [];
        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $data;
    }

    /**
     * Vérifie si la méthode de la requête est GET.
     *
     * @return bool True si la méthode est GET, False sinon.
     */
    public function isGet(): bool
    {
        return $this->getMethod() === 'get';
    }

    /**
     * Retourne la méthode de la requête (GET, POST, etc.).
     *
     * @return string La méthode de la requête.
     */
    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Vérifie si la méthode de la requête est POST.
     *
     * @return bool True si la méthode est POST, False sinon.
     */
    public function isPost(): bool
    {
        return $this->getMethod() === 'post';
    }

    /**
     * Retourne les paramètres de la route.
     *
     * @return array Les paramètres de la route.
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    /**
     * Définit les paramètres de la route.
     *
     * @param array $params Les paramètres de la route.
     * @return self
     */
    public function setRouteParams(array $params): static
    {
        $this->routeParams = $params;
        return $this;
    }

    /**
     * Retourne la valeur d'un paramètre de la route donné.
     *
     * @param string $param Le nom du paramètre.
     * @param mixed|null $default La valeur par défaut à retourner si le paramètre n'est pas trouvé.
     * @return mixed La valeur du paramètre ou la valeur par défaut.
     */
    public function getRouteParam(string $param, mixed $default = null): mixed
    {
        return $this->routeParams[$param] ?? $default;
    }
}
