<?php

namespace Eatfit\Api\Core;

use Eatfit\Api\Models\User;
use Exception;

abstract class Model
{
    /**
     * Filtre un tableau en appliquant un filtre aux valeurs.
     *
     * @param array $array Le tableau à filtrer
     * @param int $filter Le filtre à appliquer aux valeurs (par défaut FILTER_SANITIZE_SPECIAL_CHARS)
     * @return array Le tableau filtré
     */
    protected static function filterArray($array, $filter = FILTER_SANITIZE_SPECIAL_CHARS): array
    {
        $filteredArray = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $filteredArray[$key] = self::filterArray($value, $filter);
            } else {
                $filteredArray[$key] = filter_var($value, $filter);
            }
        }
        return $filteredArray;
    }

    /**
     * Vérifie si le jeton d'authentification est valide.
     *
     * @param bool $expiration Vérifie l'expiration du jeton si true
     * @throws Exception Si le jeton est invalide ou expiré
     * @return array Les données décodées du jeton
     */
    protected static function getDataToken(bool $expiration = true): array
    {
        $data = self::decodeJWT(self::getToken());
        if (!User::getUser($data['payload']['email'], $data['payload']['username'])) throw new Exception("Utilisateur introuvable", 404);
        if (self::isTokenExpired($data['payload']['exp'])) {
            Application::$app->db->execute("UPDATE users SET token = NULL, expiration = NULL WHERE email = :email", [":email" => $data['payload']['email']]);
            if ($expiration) {
                throw new Exception("Jeton expiré ou invalide", 498);
            }
        }
        return $data;
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
        if (count($parts) !== 3) {
            throw new Exception('Format JWT invalide', 498);
        }
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

    /**
     * Récupère le jeton d'authentification à partir des en-têtes de la requête.
     *
     * @return string Le jeton d'authentification
     * @throws Exception Si le jeton est invalide ou manquant
     */
    private static function getToken(): string
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            throw new Exception("Jeton expiré ou invalide", 498);
        }
        return explode(" ", $headers['Authorization'])[1];
    }

    /**
     * Vérifie si le jeton est expiré.
     *
     * @param int $expiration L'horodatage de l'expiration du jeton
     * @return bool True si le jeton est expiré, sinon False
     * @throws Exception
     */
    protected static function isTokenExpired($expiration): bool
    {
        return $expiration < time() || $expiration === 'expiré';
    }

    /**
     * Génère un JSON Web Token (JWT) à partir des données fournies.
     *
     * @param array $data Les données à inclure dans le JWT
     * @param int|null $expiration Expiration du JWT (par défaut 2 heures après la génération)
     * @return string Le JWT généré
     */
    protected static function generateJWT(array $data, $expiration = null): string
    {
        if ($expiration == null) $expiration = time() + (2 * 3600);
        $salt = $_ENV['SALT'] ?? "";
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload = [
            'email' => $data['email'],
            'username' => $data['username'],
            'exp' => $expiration,
        ];
        $encodedHeader = self::urlEncode(json_encode($header));
        $encodedPayload = self::urlEncode(json_encode($payload));
        $encodedParts = $encodedHeader . '.' . $encodedPayload;
        $encodedSignature = self::urlEncode(hash_hmac('sha256', $encodedParts, $salt, true));
        return $encodedHeader . '.' . $encodedPayload . '.' . $encodedSignature;
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
     * Récupère les informations de l'utilisateur à partir du jeton d'authentification.
     *
     * @param bool $expiration Vérifie l'expiration du jeton si true
     * @return array|null Les informations de l'utilisateur, ou null si l'utilisateur n'est pas trouvé
     * @throws Exception Si le jeton est invalide
     */
    protected static function getUserByToken(bool $expiration = true): ?array
    {
        $data = self::decodeJWT(self::getToken());
        if (self::isTokenExpired($data['payload']['exp'])) {
            Application::$app->db->execute("UPDATE users SET token = NULL, expiration = NULL WHERE email = :email", [":email" => $data['payload']['email']]);
            if ($expiration) {
                throw new Exception("Jeton expiré ou invalide", 498);
            }
        }
        $user = User::getUser($data['payload']['email'], $data['payload']['username']);
        if ($user->isEmpty()) throw new Exception("Utilisateur introuvable", 404);
        unset($user->getFirstRow()['password']);
        return $user->getFirstRow();
    }
}
