<?php

namespace eatFitTpi2023\core;


use eatFitTpi2023\models\User;
use Exception;
use InvalidArgumentException;

abstract class Model
{
    public static float $expiration;
    public const REQUIRED_FIELDS = [
        "login" => ['email', "password"],
        "register" => ['email', "password", "username"]
    ];

    /**
     * Récupère le jeton (token) d'authentification à partir des en-têtes de la requête.
     *
     * @return string Le jeton d'authentification
     * @throws Exception Si le jeton est invalide ou manquant
     */
    private static function getToken(): string
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            throw new Exception("Token expired/invalid", 498);
        }
        return explode(" ", $headers['Authorization'])[1];
    }

    /**
     * Vérifie si le jeton d'authentification est valide.
     *
     * @param bool $expiration Vérifie l'expiration du jeton si true
     * @throws Exception Si le jeton est invalide ou expiré
     * @return array Les données décodées du jeton
     */
    protected static function isValidToken(bool $expiration = true): array
    {
        $data = self::decodeJWT(self::getToken());
        if (!User::getUser($data['payload']['email'])) throw new Exception("User not found", 404);
        if ($expiration) {
            if ($data['payload']['exp'] < time()) {
                throw new Exception("Token expired/invalid", 498);
            }
        }
        return $data;
    }

    /**
     * Génère un JSON Web Token (JWT) à partir des données fournies.
     *
     * @param array $data Les données à inclure dans le JWT
     * @return string Le JWT généré
     */
    protected static function generateJWT(array $data): string
    {
        self::$expiration = time() + (2 * 3600);
        $salt = $_ENV['SALT'] ?? "";
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload = [
            'email' => $data['email'],
            'username' => $data['username'],
            'exp' => self::$expiration,
        ];
        $encodedHeader = self::urlEncode(json_encode($header));
        $encodedPayload = self::urlEncode(json_encode($payload));
        $encodedParts = $encodedHeader . '.' . $encodedPayload;
        $encodedSignature = self::urlEncode(hash_hmac('sha256', $encodedParts, $salt, true));
        return $encodedHeader . '.' . $encodedPayload . '.' . $encodedSignature;
    }

    /**
     * Récupère les informations de l'utilisateur à partir du jeton d'authentification.
     *
     * @return array|null Les informations de l'utilisateur, ou null si l'utilisateur n'est pas trouvé
     * @throws Exception Si le jeton est invalide
     */
    protected static function getUserByToken(): ?array
    {
        $data = self::isValidToken();
        $user = User::getUser($data['payload']['email'])->getFirstRow();
        if (!$user) throw new Exception("User not found", 404);
        unset($user['password']);
        return $user;
    }

    /**
     * Encode une chaîne en base64 URL-safe.
     *
     * @param false|string $json_encode La chaîne à encoder
     * @return array|string La chaîne encodée en base64 URL-safe
     */
    private static function urlEncode(false|string $json_encode): array|string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($json_encode));
    }

    /**
     * Décode un JWT et retourne ses composants.
     *
     * @param string $jwt Le JWT à décoder
     * @return array Les composants du JWT
     * @throws Exception Si le JWT est invalide
     */
    private static function decodeJWT(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) throw new Exception('Invalid JWT format', 498);
        $headers = json_decode(self::urlDecode($parts[0]), true);
        $payload = json_decode(self::urlDecode($parts[1]), true);
        $signature = self::urlDecode($parts[2]);
        return ['headers' => $headers, 'payload' => $payload, 'signature' => $signature];
    }

    /**
     * Décode une chaîne encodée en base64 URL-safe.
     *
     * @param string $data La chaîne à décoder
     * @return false|string La chaîne décodée
     */
    private static function urlDecode(string $data): false|string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
