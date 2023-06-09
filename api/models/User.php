<?php


namespace Eatfit\Api\Models;

use Eatfit\Api\Core\Application;
use Eatfit\Api\Core\Db\SqlResult;
use Eatfit\Api\Core\Model;
use Exception;

class User extends Model
{
    /**
     * Récupère les informations d'un utilisateur existant dans la base de données.
     *
     * @param array $data Un tableau contenant l'email et le mot de passe de l'utilisateur.
     * @return array|Exception Les informations de l'utilisateur ou une exception en cas d'erreur.
     * @throws Exception
     */
    public static function read(array $data): array|Exception
    {
        return self::getUserInfo($data['email'], $data['password']);
    }

    /**
     * Vérifie si un utilisateur existe dans la base de données.
     *
     * @param $email
     * @param string $password
     * @return array|Exception Les informations de l'utilisateur si l'utilisateur existe, une exception sinon.
     * @throws Exception
     */
    private static function getUserInfo($email, string $password): array|Exception
    {
        $user = Application::$app->db->execute("SELECT * FROM users WHERE email = :email", [":email" => $email]);

        if ($user->isEmpty()) throw new Exception("Utilisateur ou mot de passe invalide", 400);
        $user = $user->getFirstRow();
        if (password_verify($password, $user['password'])) {
            unset($user['password']);
            if ($user['expiration'] < time()) $user['expiration'] = 'expiré';
            return $user;
        }
        throw new Exception("Utilisateur ou mot de passe invalide", 400);
    }

    /**
     * Crée un nouvel utilisateur dans la base de données.
     *
     * @param array $data Un tableau contenant les informations de l'utilisateur.
     * @return array|Exception Les informations de l'utilisateur créé ou une exception en cas d'erreur.
     * @throws Exception
     */
    public static function create(array $data): array|Exception
    {
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$email) throw new Exception("Email invalide", 400);
        $data = self::filterArray($data);
        if (strlen($data['password']) < 8) throw new Exception("Le mot de passe doit comporter au moins 8 caractères", 400);
        if (!self::getUser($email, $data['username'])->isEmpty()) throw new Exception("L'utilisateur existe déjà", 409);
        if ($data['password'] != $data['confirm_password']) throw new Exception("Les mots de passe ne correspondent pas", 400);
        $data = [
            'username' => $data['username'],
            'email' => $email,
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'token' => self::generateJWT($data),
            'expiration' => time() + (2 * 3600)
        ];
        try {
            Application::$app->db->execute("INSERT INTO users (username, email, password, token, expiration) VALUES (:username, :email, :password, :token, :expiration)", [":username" => $data['username'], ":email" => $data['email'], ":password" => $data['password'], ":token" => $data['token'], ":expiration" => $data['expiration']]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la création de l'utilisateur", 500);
        }
        return self::getUser($data['email'], $data['username'])->getFirstRow();
    }

    /**
     * Récupère les informations d'un utilisateur à partir de son adresse e-mail.
     *
     * @param string|null $email L'adresse e-mail de l'utilisateur.
     * @param string|null $username
     * @return SqlResult Les informations de l'utilisateur.
     * @throws Exception
     */
    public static function getUser(string $email = null, string $username = null): SqlResult
    {
        $query = "SELECT * FROM users ";
        $params = [];
        if ($email != null && $username == null) {
            $query .= " WHERE email = :email";
            $params = [":email" => $email];
        } elseif ($username != null && $email == null) {
            $query .= " WHERE username = :username";
            $params = [":username" => $username];
        } else {
            $query .= "WHERE email = :email OR username = :username";
            $params = [":email" => $email, ":username" => $username];
        }
        try {
            return Application::$app->db->execute($query, $params);
        } catch (Exception $e) {
            throw new Exception("Un problème est survenu", 500);
        }
    }

    /**
     * Met à jour les informations d'un utilisateur existant.
     *
     * @param array $data Un tableau contenant les informations mises à jour de l'utilisateur.
     * @return array|string Un message indiquant le succès de la mise à jour.
     * @throws Exception
     */
    public static function update(array $data): array|string
    {
        $token = null;
        $user = parent::getUserByToken(false);
        $idUser = $user['idUser'];
        $updates = [];
        if ($data == null) throw new Exception("Aucune donnée à mettre à jour", 400);
        $data = self::filterArray($data);
        if (isset($data['email']) && $data['email'] != "") {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) throw new Exception("Email invalide", 400);
            if (self::getUser($data['email'])->isEmpty()) $updates['email'] = $data['email'];
            else throw new Exception("Cet utilisateur existe déjà", 409);
        }
        if (isset($data['username']) && $data['username'] != "") {
            if (self::getUser(null, $data['username'])->isEmpty()) $updates['username'] = $data['username'];
            else throw new Exception("Cette utilisateur existe déjà", 409);
        }
        if (isset($data['password']) && $data['password'] != "") {
            if (strlen($data['password']) < 8) throw new Exception("Le mot de passe doit comporter au moins 8 caractères", 400);
            if (!isset($data['confirm_password']) || $data['password'] != $data['confirm_password']) throw new Exception("Les mots de passe ne correspondent pas", 400);
            $updates['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $sb = "";
        if (count($updates) > 0) {
            $sb = "Utilisateur mis à jour avec succès :";
            try {
                unset($user['idUser'], $user['token']);
                $updatedUserData = array_merge($user, $updates);
                $newToken = self::generateJWT($updatedUserData, $updatedUserData['expiration']);
                foreach ($updates as $key => $value) $sb .= " $key = $value";
                $token = $newToken;
                $updates['token'] = $newToken;
                Application::$app->db->execute("UPDATE users SET " . implode(", ", array_map(fn($key) => "$key = :$key", array_keys($updates))) . " WHERE idUser = :idUser", array_merge($updates, [":idUser" => $idUser]));
            } catch (Exception $e) {
                throw new Exception("Erreur lors de la mise à jour de l'utilisateur", 500);
            }
        }
        if ($token != null) return ["Mises à jour" => $sb, "token" => $token];
        return $sb;
    }

    /**
     * Récupère les informations d'un utilisateur à partir du jeton d'authentification.
     *
     * @param bool $expiration Vérifie l'expiration du jeton si true.
     * @return array|null Les informations de l'utilisateur, ou null si l'utilisateur n'est pas trouvé.
     * @throws Exception Si le jeton est invalide.
     */
    public static function getUserByToken(bool $expiration = true): ?array
    {
        return parent::getUserByToken($expiration);
    }

    /**
     * Supprime un utilisateur existant dans la base de données.
     *
     * @return string Un message indiquant que la suppression a été effectuée avec succès.
     * @throws Exception
     */
    public static function delete(): string
    {
        $user = parent::getUserByToken();
        try {
            Application::$app->db->execute("DELETE FROM users WHERE idUser = :idUser", [":idUser" => $user['idUser']]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la suppression de l'utilisateur", 500);
        }
        return "Utilisateur supprimé avec succès";
    }

    /**
     * Authentifie un utilisateur et génère un nouveau jeton d'authentification si nécessaire.
     *
     * @param array $data Un tableau contenant l'email et le mot de passe de l'utilisateur.
     * @return array|Exception Les informations de l'utilisateur authentifié ou une exception en cas d'erreur.
     * @throws Exception
     */
    public static function authenticate(array $data): array|Exception
    {
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        $password = filter_var($data['password'], FILTER_SANITIZE_SPECIAL_CHARS);
        if (!$email || !$password) throw new Exception("Email ou mot de passe invalide", 400);
        $user = self::getUserInfo($email, $password);
        try {
            if (self::isTokenExpired($user['expiration'])) {
                $user['token'] = self::generateJWT($user);
                $user['expiration'] = time() + (2 * 3600);
                Application::$app->db->execute("UPDATE users SET token = :token, expiration = :expiration WHERE idUser = :idUser", [":token" => $user['token'], ":expiration" => $user['expiration'], ":idUser" => $user['idUser']]);
            }
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la mise à jour de l'utilisateur", 500);
        }
        unset($user['password']);
        return $user;
    }
}
