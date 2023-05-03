<?php

namespace eatFitTpi2023\core;
class ApiValue
{
    private const STATUS_CODES = [
        200 => 'OK',
        201 => 'Created',
        204 => 'No Content',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        409 => 'Conflict',
        422 => 'Unprocessable Entity',
        498 => 'Token expired/invalid',
        500 => 'Internal Server Error',
    ];

    public mixed $value;
    public string $message;
    public int $code;

    /**
     * Constructeur de la classe ApiValue
     *
     * @param mixed $value La valeur à renvoyer au client.
     * @param int $code Le code HTTP correspondant à la réponse.
     * @param string $message (optionnel) Le message de la réponse (si non fourni, le message sera généré automatiquement en fonction du code).
     */
    public function __construct(mixed $value, int $code, string $message = "")
    {
        $this->message = $message !== "" ? $message : (self::STATUS_CODES[$code] ?? self::STATUS_CODES[500]);
        $this->value = $value;
        $this->setCode($code);
        http_response_code($this->code);
    }

    /**
     * Fonction magique __toString() - retourne l'objet ApiValue sous forme de JSON.
     *
     * @return string L'objet ApiValue sous forme de JSON.
     */
    public function __toString(): string
    {
        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($this);
    }

    /**
     * Fonction setCode() - définit le code HTTP de la réponse.
     *
     * @param int $code Le code HTTP à définir.
     * @return void
     */
    private function setCode(int $code): void
    {
        $this->code = self::STATUS_CODES[$code] !== null ? $code : 400;
    }
}
