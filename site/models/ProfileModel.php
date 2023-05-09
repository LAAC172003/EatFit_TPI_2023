<?php

namespace Eatfit\Site\models;

use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Model;
use http\Exception\BadQueryStringException;

class ProfileModel extends Model
{
    public ?string $username = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $passwordConfirm = null;

    public function rules()
    {
        return [
            'username' => [[self::RULE_REQUIRED]],
            'email' => [[self::RULE_EMAIL], [self::RULE_REQUIRED]],
            'password' => [[self::RULE_MIN, 'min' => 8]],
            'passwordConfirm' => [[self::RULE_MATCH, 'match' => 'password']]
        ];
    }

    public function labels()
    {
        return [
            'username' => 'Name:',
            'email' => 'Email:',
            'password' => 'Password:',
            'confirmPassword' => 'Confirm Password:',
        ];
    }

    public function attributes()
    {
        return ['username', 'email', 'password', 'passwordConfirm'];
    }

    /**
     * @throws \Exception
     */
    public function update()
    {
        $updates = [];
        foreach ($this->attributes() as $attribute) {
            if ($this->$attribute === null || empty($this->$attribute)) {
                continue;
            }
            $updates[$attribute] = $this->$attribute;
        }
        $curl = curl_init();

        $postData = json_encode($updates);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://eatfittpi2023api/user',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . Application::$app->user->token
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            // Vous pourriez vouloir gérer les erreurs différemment...
            throw new \Exception("cURL Error #:" . $err);
        } else {
            return json_decode($response);
        }
    }
}