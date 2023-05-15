<?php

namespace Eatfit\Site\Models;


use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Model;

class LoginForm extends Model
{
    public string $email = '';
    public string $password = '';

    public function rules()
    {
        return [
            'email' => [self::RULE_REQUIRED, self::RULE_EMAIL],
            'password' => [self::RULE_REQUIRED],
        ];
    }

    public function labels(): array
    {
        return [
            'email' => 'Email:',
            'password' => 'Password:'
        ];
    }

    public function login()
    {
        $user = User::getUser($this->email, $this->password);
        if ($user->value == null && $user->code != 200) {
            Application::$app->session->setFlash('error', $user->message);
            return false;
        }
        return Application::$app->login($user);
    }
}