<?php

namespace Eatfit\Site\Core;

/**
 * La classe Model est une classe de base pour tous les modèles de l'application.
 * Elle fournit des fonctionnalités communes liées à la validation des données et à la gestion des erreurs.
 */
class Model
{
    const RULE_REQUIRED = 'required';
    const RULE_EMAIL = 'email';
    const RULE_MIN = 'min';
    const RULE_MAX = 'max';
    const RULE_MATCH = 'match';

    public array $errors = [];

    /**
     * Effectue une requête API avec les données JSON fournies.
     *
     * @param array $data Les données JSON de la requête.
     * @param bool $addBearer Indique si le jeton d'authentification doit être ajouté à la requête.
     * @param bool $returnArray Indique si la réponse doit être renvoyée sous forme de tableau.
     * @return mixed Le résultat de la requête ou false en cas d'erreur.
     */
    protected static function getJsonResult(array $data, bool $addBearer = false, bool $returnArray = false): mixed
    {
        if (!isset($data['data']) || !isset($data['method']) || !isset($data['url'])) return false;
        $http_header[] = 'Content-Type: application/json';
        if ($addBearer) $http_header[] = 'Authorization: Bearer ' . Application::$app->user->token;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => Application::$API_URL . $data['url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $data['method'],
            CURLOPT_POSTFIELDS => json_encode($data['data']),
            CURLOPT_HTTPHEADER => $http_header,
        ));
        $response = curl_exec($curl);
        if (curl_errno($curl)) $error_msg = curl_error($curl);
        curl_close($curl);
        return $error_msg ?? json_decode($response, $returnArray);
    }

    /**
     * Charge les données dans le modèle.
     *
     * @param array $data Les données à charger dans le modèle.
     * @return void
     */
    public function loadData($data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Retourne les attributs du modèle.
     *
     * @return array Les attributs du modèle.
     */
    public function attributes()
    {
        return [];
    }

    /**
     * Retourne le label d'un attribut donné.
     *
     * @param string $attribute Le nom de l'attribut.
     * @return string Le label de l'attribut.
     */
    public function getLabel(string $attribute): string
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    /**
     * Retourne les labels des attributs du modèle.
     *
     * @return array Les labels des attributs du modèle.
     */
    public function labels(): array
    {
        return [];
    }

    /**
     * Valide les données du modèle en fonction des règles de validation.
     *
     * @return bool True si les données sont valides, False sinon.
     */
    public function validate(): bool
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($rule)) $ruleName = $rule[0];
                if ($ruleName === self::RULE_REQUIRED && !$value) $this->addErrorByRule($attribute, self::RULE_REQUIRED);
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) $this->addErrorByRule($attribute, self::RULE_EMAIL);
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) $this->addErrorByRule($attribute, self::RULE_MIN, ['min' => $rule['min']]);
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) $this->addErrorByRule($attribute, self::RULE_MAX);
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) $this->addErrorByRule($attribute, self::RULE_MATCH, ['match' => $rule['match']]);
            }
        }
        return empty($this->errors);
    }

    /**
     * Retourne les règles de validation du modèle.
     *
     * @return array Les règles de validation du modèle.
     */
    public function rules()
    {
        return [];
    }

    /**
     * Ajoute une erreur de validation basée sur une règle donnée.
     *
     * @param string $attribute L'attribut concerné.
     * @param string $rule La règle de validation.
     * @param array $params Les paramètres à substituer dans le message d'erreur.
     * @return void
     */
    protected function addErrorByRule(string $attribute, string $rule, $params = [])
    {
        $params['field'] ??= $attribute;
        $errorMessage = $this->errorMessage($rule);
        foreach ($params as $key => $value) {
            $errorMessage = str_replace("{{$key}}", $value, $errorMessage);
        }
        $this->errors[$attribute][] = $errorMessage;
    }

    /**
     * Retourne le message d'erreur correspondant à une règle de validation donnée.
     *
     * @param string $rule La règle de validation.
     * @return string Le message d'erreur.
     */
    public function errorMessage($rule): string
    {
        return $this->errorMessages()[$rule];
    }

    /**
     * Retourne les messages d'erreur associés aux règles de validation.
     *
     * @return array Les messages d'erreur associés aux règles de validation.
     */
    public function errorMessages(): array
    {
        return [
            self::RULE_REQUIRED => 'Le champ {field} est requis',
            self::RULE_EMAIL => 'Le champ {field} doit être une adresse email valide',
            self::RULE_MIN => 'Le champ {field} doit contenir au moins {min} caractères',
            self::RULE_MAX => 'Le champ {field} doit contenir au maximum {max} caractères',
            self::RULE_MATCH => 'Le champ {field} doit être identique au champ {match}'
        ];
    }

    /**
     * Ajoute une erreur personnalisée.
     *
     * @param string $attribute L'attribut concerné.
     * @param string $message Le message d'erreur.
     * @return void
     */
    public function addError(string $attribute, string $message)
    {
        $this->errors[$attribute][] = $message;
    }

    /**
     * Vérifie si une erreur existe pour un attribut donné.
     *
     * @param string $attribute L'attribut concerné.
     * @return bool True si une erreur existe, False sinon.
     */
    public function hasError($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    /**
     * Retourne la première erreur pour un attribut donné.
     *
     * @param string $attribute L'attribut concerné.
     * @return string|null Le premier message d'erreur, ou null s'il n'y a pas d'erreur.
     */
    public function getFirstError($attribute)
    {
        $errors = $this->errors[$attribute] ?? [];
        return $errors[0] ?? '';
    }
}