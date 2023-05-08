<?php

namespace Eatfit\Api\Core;

use Exception;

class Request
{
    private array $routeParams = [];

    /**
     * Récupère la méthode HTTP utilisée pour la requête.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Récupère l'URL de la requête.
     *
     * @return string
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
     * Définit les paramètres de la route associée à la requête.
     *
     * @param array $params
     * @return self
     */
    public function setRouteParams(array $params): self
    {
        $this->routeParams = $params;
        return $this;
    }

    /**
     * Récupère tous les paramètres de la route associée à la requête.
     *
     * @return array
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    /**
     * Récupère la valeur d'un paramètre de la route associée à la requête.
     *
     * @param string $param
     * @param mixed|null $default
     * @return mixed
     */
    public function getRouteParam(string $param, mixed $default = null): mixed
    {
        return $this->routeParams[$param] ?? $default;
    }

    /**
     * Récupère le corps de la requête HTTP.
     *
     * @return mixed
     */
    private function getBody(): mixed
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    /**
     * Récupère les données envoyées dans le corps de la requête HTTP en tant que JSON et les retourne sous forme d'array associatif.
     * Si $requiredFields est spécifié, cette fonction valide que tous les champs obligatoires sont présents dans le corps de la requête.
     * Si un champ obligatoire est manquant, une exception est levée avec un code HTTP 400 (Bad Request).
     *
     * @param array|null $requiredFields Un tableau contenant les noms des champs obligatoires dans le corps de la requête.
     * @param bool $requireAllFields
     * @return array Les données du corps de la requête sous forme d'array associatif.
     * @throws Exception Si les données sont manquantes, invalides, ou si un champ obligatoire est manquant.
     */
    public function getData(array $requiredFields = null, bool $requireAllFields = true): array
    {
        $body = $this->getBody();
        if (!$requireAllFields && $body == null) return [];
        if (!is_array($body)) throw new Exception("Invalid body", 400);
        $this->validateData($body, $requiredFields, $requireAllFields);
        return $body;
    }

    /**
     * Valide les données reçues dans le corps de la requête HTTP.
     *
     * @param array $fields
     * @param array|null $requiredFields
     * @param bool $requireAllFields
     * @param string $parentKey
     * @throws Exception
     */
    private function validateData(array $fields, array $requiredFields = null, bool $requireAllFields = true, $parentKey = ''): void
    {
        if (empty($fields)) throw new Exception("No data in the body", 400);
        if ($requiredFields !== null) {
            if (!is_array($requiredFields) || count($requiredFields) == 0) throw new Exception("Invalid required fields", 400);
            $missingFields = [];
            $extraFields = array_diff(array_keys($fields), array_keys($requiredFields));

            foreach ($requiredFields as $field => $nestedFields) {
                if (is_array($nestedFields)) {
                    if (array_key_exists($field, $fields) && is_array($fields[$field])) {
                        $this->validateData($fields[$field], $nestedFields, $requireAllFields, $parentKey . $field . '.');
                        $extraFields = array_diff($extraFields, [$field]);
                    } else if ($requireAllFields) $missingFields[] = $parentKey . $field;
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
                throw new Exception("Missing fields: " . implode(", ", $missingFieldsFormatted) . ". Allowed fields" . ($parentKey ? " in " . rtrim($parentKey, '.') : "") . " are: " . implode(", ", $allowedFields), 422);
            }


            if (!empty($extraFields)) {
                $allowedFields = array_map(function ($key, $value) {
                    return is_array($value) ? $key : $value;
                }, array_keys($requiredFields), $requiredFields);
                throw new Exception("The fields: " . implode(", ", $extraFields) . " do not exist. Allowed fields" . ($parentKey ? " in " . rtrim($parentKey, '.') : "") . " are: " . implode(", ", $allowedFields), 422);
            }
        }
    }

}
