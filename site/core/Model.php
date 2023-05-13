<?php

namespace Eatfit\Site\Core;

class Model
{
    const RULE_REQUIRED = 'required';
    const RULE_EMAIL = 'email';
    const RULE_MIN = 'min';
    const RULE_MAX = 'max';
    const RULE_MATCH = 'match';

    public array $errors = [];

    public function loadData($data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function attributes()
    {
        return [];
    }

    public function labels()
    {
        return [];
    }

    public function getLabel($attribute)
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    public function rules()
    {
        return [];
    }

    public function validate()
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

    public function errorMessages(): array
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must be valid email address',
            self::RULE_MIN => 'Min length of this field must be {min}',
            self::RULE_MAX => 'Max length of this field must be {max}',
            self::RULE_MATCH => 'This field must be the same as {match}'
        ];
    }

    public function errorMessage($rule): string
    {
        return $this->errorMessages()[$rule];
    }

    protected function addErrorByRule(string $attribute, string $rule, $params = [])
    {
        $params['field'] ??= $attribute;
        $errorMessage = $this->errorMessage($rule);
        foreach ($params as $key => $value) {
            $errorMessage = str_replace("{{$key}}", $value, $errorMessage);
        }
        $this->errors[$attribute][] = $errorMessage;
    }

    public function addError(string $attribute, string $message)
    {
        $this->errors[$attribute][] = $message;
    }

    public function hasError($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    public function getFirstError($attribute)
    {
        $errors = $this->errors[$attribute] ?? [];
        return $errors[0] ?? '';
    }

    protected static function getJsonResult($data, $addBearer = false, $returnArray = false)
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
}