<?php

namespace Eatfit\Api\Core;

use Exception;

class Request
{
    private array $routeParams = [];

    /**
     * Récupère la méthode HTTP utilisée pour la requête.
     *
     * @return string La méthode HTTP utilisée.
     */
    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Récupère l'URL de la requête.
     *
     * @return string L'URL de la requête.
     */
    public function getUrl(): string
    {
        // Récupérer l'URI de la requête
        $path = $_SERVER['REQUEST_URI'];
        // Vérifier si l'URI contient un '?', ce qui signifie des paramètres de requête
        $position = strpos($path, '?');
        // Si des paramètres de requête sont présents, ne conserver que la partie de l'URI avant les paramètres de requête
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        return $path;
    }

    /**
     * Récupère tous les paramètres de la route associée à la requête.
     *
     * @return array Les paramètres de la route.
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    /**
     * Définit les paramètres de la route associée à la requête.
     *
     * @param array $params Les paramètres de la route.
     * @return self Une référence à l'objet courant, permettant le chaînage des méthodes.
     */
    public function setRouteParams(array $params): self
    {
        $this->routeParams = $params;
        return $this;
    }

    /**
     * Récupère la valeur d'un paramètre de la route associée à la requête.
     *
     * @param string $param Le nom du paramètre de la route.
     * @param mixed|null $default La valeur par défaut à retourner si le paramètre n'existe pas.
     * @return mixed La valeur du paramètre de la route, ou la valeur par défaut si le paramètre n'existe pas.
     */
    public function getRouteParam(string $param, mixed $default = null): mixed
    {
        return $this->routeParams[$param] ?? $default;
    }

    /**
     * Récupère les données envoyées dans le corps de la requête HTTP en tant que JSON et les retourne sous forme d'array associatif.
     * Si $requiredFields est spécifié, cette fonction valide que tous les champs obligatoires sont présents dans le corps de la requête.
     * Si un champ obligatoire est manquant, une exception est levée avec un code HTTP 400 (Bad Request).
     *
     * @param array|null $requiredFields Un tableau contenant les noms des champs obligatoires dans le corps de la requête.
     * @param bool $requireAllFields Un booléen indiquant si tous les champs sont requis.
     * @return array Les données du corps de la requête sous forme d'array associatif.
     * @throws Exception Si les données sont manquantes, invalides, ou si un champ obligatoire est manquant.
     */
    public function getData(array $requiredFields = null, bool $requireAllFields = true): array
    {
        $body = $this->getBody();
        if (!$requireAllFields && $body == null) return [];
        if (!is_array($body)) throw new Exception("Corps de la requête invalide", 400);
        $this->validateData($body, $requiredFields, $requireAllFields);
        return $body;
    }

    /**
     * Récupère le corps de la requête HTTP.
     *
     * @return mixed Le corps de la requête HTTP.
     */
    private function getBody(): mixed
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    /**
     * Valide les données reçues dans le corps de la requête HTTP.
     *
     * @param array $fields Les champs à valider.
     * @param array|null $requiredFields Les champs requis.
     * @param bool $requireAllFields Un booléen indiquant si tous les champs sont requis.
     * @param string $parentKey La clé parente pour les champs imbriqués.
     * @throws Exception Si les données sont invalides.
     */
    private function validateData(array $fields, array $requiredFields = null, bool $requireAllFields = true, $parentKey = ''): void
    {
        if (empty($fields)) throw new Exception("Pas de données dans le corps de la requête", 400);
        if ($requiredFields !== null) {
            if (!is_array($requiredFields) || count($requiredFields) == 0) throw new Exception("Champs requis invalides", 400);
            $missingFields = [];
            $extraFields = array_diff(array_keys($fields), array_keys($requiredFields));

            foreach ($requiredFields as $field => $nestedFields) {
                if (is_array($nestedFields)) {
                    if (array_key_exists($field, $fields) && is_array($fields[$field])) {
                        $this->validateData($fields[$field], $nestedFields, $requireAllFields, $parentKey . $field . '.');
                        $extraFields = array_diff($extraFields, [$field]);
                    } else if ($requireAllFields) {
                        $missingFields[] = $parentKey . $field;
                    }
                } else {
                    if (!array_key_exists($nestedFields, $fields)) {
                        if ($requireAllFields) $missingFields[] = $parentKey . $nestedFields;
                    } else {
                        $extraFields = array_diff($extraFields, [$nestedFields]);
                    }
                }
            }

            if (!empty($missingFields)) {
                $allowedFields = array_map(function ($key, $value) {
                    return is_array($value) ? $key : $value;
                }, array_keys($requiredFields), $requiredFields);

                $missingFieldsFormatted = array_map(function ($field) {
                    return str_replace(explode('.', $field)[0] . ".", '', $field);
                }, $missingFields);
                throw new Exception("Champs manquants : " . implode(", ", $missingFieldsFormatted) . ". Champs autorisés" . ($parentKey ? " dans " . rtrim($parentKey, '.') : "") . " sont : " . implode(", ", $allowedFields), 422);
            }

            if (!empty($extraFields)) {
                $allowedFields = array_map(function ($key, $value) {
                    return is_array($value) ? $key : $value;
                }, array_keys($requiredFields), $requiredFields);
                throw new Exception("Les champs : " . implode(", ", $extraFields) . " n'existent pas. Les champs autorisés" . ($parentKey ? " dans " . rtrim($parentKey, '.') : "") . " sont : " . implode(", ", $allowedFields), 422);
            }
        }
    }
}
