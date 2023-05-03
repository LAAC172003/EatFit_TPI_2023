<?php


namespace eatFitTpi2023\models;

use eatFitTpi2023\core\Application;
use eatFitTpi2023\core\db\SqlResult;

use Exception;
use eatFitTpi2023\core\Model;

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
        return self::login($data['email'], $data['password']);
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
        if (!self::getUser($data['email'])->isEmpty()) throw new Exception("User already exists", 409);
        if (strlen($data['password']) < 8) throw new Exception("Password must be at least 8 characters long", 400);
        $data = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'token' => self::generateJWT($data),
            'expiration' => self::$expiration
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
        $dataToken = self::isValidToken(false);
        $idUser = self::getUser($dataToken['payload']['email'])->getFirstRow()['idUser'];
        $updates = [];
        if ($data == null) throw new Exception("No data to update", 400);
        if (isset($data['email']) && $data['email'] != "") $updates['email'] = $data['email'];
        if (isset($data['username']) && $data['username'] != "") $updates['username'] = $data['username'];
        if (isset($data['password']) && $data['password'] != "") $updates['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
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
        $dataToken = self::isValidToken(false);
        $idUser = self::getUser($dataToken['payload']['email'])->getFirstRow()['idUser'];
        try {
            Application::$app->db->execute("DELETE FROM users WHERE idUser = :idUser", [":idUser" => $idUser]);
        } catch (Exception $e) {
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
    private static function login(string $email, string $password): array|Exception
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
