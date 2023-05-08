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
     * Crée un nouvel utilisateur dans la base de données.
     *
     * @param array $data Un tableau contenant les informations de l'utilisateur.
     * @return array|Exception Les informations de l'utilisateur créé ou une exception en cas d'erreur.
     * @throws Exception
     */
    public static function create(array $data): array|Exception
    {
        //a revoir

        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$email) throw new Exception("Invalid email", 400);
        $data = self::filterArray($data);
        if (strlen($data['password']) < 8) throw new Exception("Password must be at least 8 characters long", 400);
        if (!self::getUser($email)->isEmpty()) throw new Exception("User already exists", 409);
        if ($data['password'] != $data['confirm_password']) throw new Exception("Passwords don't match", 400);
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
            throw new Exception("Error creating user", 500);
        }
        return self::getUser($data['email'])->getFirstRow();
    }

    /**
     * Met à jour les informations d'un utilisateur existant.
     *
     * @param array $data Un tableau contenant les informations mises à jour de l'utilisateur.
     * @return string Un message indiquant le succès de la mise à jour.
     * @throws Exception
     */
    public static function update(array $data): string
    {
        $dataToken = self::isTokenValid(false);
        $user = self::getUser($dataToken['payload']['email']);
        if ($user->isEmpty()) throw new Exception("User not found", 404);
        $idUser = $user->getFirstRow()['idUser'];
        $updates = [];
        if ($data == null) throw new Exception("No data to update", 400);
        if (isset($data['email']) && $data['email'] != "") $updates['email'] = $data['email'];
        if (isset($data['username']) && $data['username'] != "") $updates['username'] = $data['username'];
        if (isset($data['password']) && $data['password'] != "") {
            if (strlen($data['password']) < 8) throw new Exception("Password must be at least 8 characters long", 400);
            $updates['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $sb = "";
        if (count($updates) > 0) {
            $sb = "User updated successfully :";
            try {
                Application::$app->db->execute("UPDATE users SET " . implode(", ", array_map(fn($key) => "$key = :$key", array_keys($updates))) . " WHERE idUser = :idUser", array_merge($updates, [":idUser" => $idUser]));
                foreach ($updates as $key => $value) $sb .= " $key = $value";
            } catch (Exception $e) {
                throw new Exception("Error updating user", 500);
            }
        }
        return $sb;
    }

    /**
     * Supprime un utilisateur existant dans la base de données.
     *
     * @return string Un message indiquant que la suppression a été effectuée avec succès.
     * @throws Exception
     */
    public static function delete(): string
    {
        $dataToken = self::isTokenValid(false);
        $user = self::getUser($dataToken['payload']['email']);
        if ($user->isEmpty()) throw new Exception("User not found", 404);
        $idUser = $user->getFirstRow()['idUser'];
        try {
            Application::$app->db->execute("DELETE FROM users WHERE idUser = :idUser", [":idUser" => $idUser]);
        } catch (Exception $e) {
            var_dump($e);
            throw new Exception("Error deleting user", 500);
        }
        return "User deleted successfully";
    }

    /**
     * Vérifie si un utilisateur existe dans la base de données.
     * @param string $email L'adresse e-mail de l'utilisateur.
     * @param string $password
     * @return array|Exception Vrai si l'utilisateur existe, faux sinon.
     * @throws Exception
     */
    private static function getUserInfo(string $email, string $password): array|Exception
    {
        $userTab = self::getUser($email);
        if (!$userTab->getValues()) throw new Exception("User or password invalid", 400);
        $user = $userTab->getFirstRow();
        if (password_verify($password, $user['password'])) {
            unset($user['password']);
            if ($user['expiration'] < time()) $user['expiration'] = 'expired';
            return $user;
        }
        throw new Exception("Invalid user or password", 400);
    }

    /**
     * @throws Exception
     */
    public static function authenticate(array $data): array|Exception
    {

        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        $password = filter_var($data['password'], FILTER_SANITIZE_SPECIAL_CHARS);
        if (!$email || !$password) throw new Exception("Invalid email or password", 400);
        if (strlen($password) < 8) throw new Exception("Password must be at least 8 characters long", 400);
        $user = self::getUserInfo($email, $password);
        try {
            if (self::isTokenExpired($user['expiration'])) {
                $user['token'] = self::generateJWT($user);
                $user['expiration'] = time() + (2 * 3600);
                Application::$app->db->execute("UPDATE users SET token = :token, expiration = :expiration WHERE idUser = :idUser", [":token" => $user['token'], ":expiration" => $user['expiration'], ":idUser" => $user['idUser']]);
            }
        } catch (Exception $e) {
            throw new Exception("Error updating user", 500);
        }
        unset($user['password']);
        return $user;
    }

    /**
     * Récupère les informations d'un utilisateur à partir de son adresse e-mail.
     *
     * @param string $email L'adresse e-mail de l'utilisateur.
     * @return SqlResult Les informations de l'utilisateur.
     * @throws Exception
     */
    public static function getUser(string $email): SqlResult
    {
        try {
            return Application::$app->db->execute("SELECT * FROM users WHERE email = :email", [":email" => $email]);
        } catch (Exception $e) {
            throw new Exception("A problem has occurred", 500);
        }
    }


}
