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
            'password_confirm' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']],
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
        return self::getJsonResult([
            'url' => 'user',
            'method' => 'POST',
            'data' => [
                'username' => $this->username,
                'email' => $this->email,
                'password' => $this->password,
                'confirm_password' => $this->password_confirm
            ]
        ]);
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
