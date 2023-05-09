<?php

namespace Eatfit\Site\Models;

use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Model;

class User extends Model
{
    public int $idUser = 0;
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirm = '';
    public string $token = '';
    public int $expiration = 0;

    public function rules(): array
    {
        return [
            'username' => [self::RULE_REQUIRED],
            'email' => [self::RULE_REQUIRED, self::RULE_EMAIL],
            'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8]],
            'password_confirm' => [[self::RULE_MATCH, 'match' => 'password']],
        ];
    }

    public function labels(): array
    {
        return [
            'username' => 'Username: ',
            'email' => 'Email: ',
            'password' => 'Password: ',
            'password_confirm' => 'Password Confirm:'
        ];
    }


    public function save()
    {
        $response = self::getJsonResult([
            'url' => 'register',
            'method' => 'POST',
            'data' => [
                'username' => $this->username,
                'email' => $this->email,
                'password' => $this->password,
                'password_confirm' => $this->password_confirm
            ]
        ]);
        if (!$response) return false;
        if ($response->value == null || $response->code != 201) {
            Application::$app->session->setFlash('error', $response->message);
            Application::$app->response->statusCode($response->code);
            if ($response->message == "User already exists") {
                $response->message = "L'utilisateur existe déjà";
            }
            Application::$app->session->setFlash('error', $response->message);
            return false;
        }
        Application::$app->response->statusCode($response->code);
        return $response;
    }

    public static function getUser($email, $password)
    {
        return self::getJsonResult([
            'url' => 'login',
            'method' => 'PUT',
            'data' => [
                'email' => $email,
                'password' => $password
            ]
        ]);
    }

    public static function getUserByToken($token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => Application::$API_URL . '/userById',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $token"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }
}
