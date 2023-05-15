<?php

namespace Eatfit\Site\models;

use Eatfit\Site\Core\Model;
use Exception;

class ProfileModel extends Model
{
    public int $idUser = 0;
    public ?string $username = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $confirm_password = null;

    public function rules()
    {
        return [
            'username' => [[self::RULE_REQUIRED]],
            'email' => [[self::RULE_EMAIL], [self::RULE_REQUIRED]],
            'password' => [[self::RULE_MIN, 'min' => 8]],
            'confirm_password' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']]
        ];
    }

    public function labels(): array
    {
        return [
            'username' => 'Name:',
            'email' => 'Email:',
            'password' => 'Password:',
            'confirm_password' => 'Confirm Password:',
        ];
    }

    /**
     * @throws Exception
     */
    public function update()
    {
        $updates = [];
        foreach ($this->attributes() as $attribute) {
            if ($this->$attribute == null || empty($this->$attribute)) continue;
            $updates[$attribute] = $this->$attribute;
        }
        return self::getJsonResult([
            'url' => 'user',
            'method' => 'PUT',
            'data' => $updates
        ], true);
    }

    public function attributes(): array
    {
        return ['username', 'email', 'password', 'confirm_password'];
    }

    public function delete()
    {
        return self::getJsonResult([
            'url' => 'user',
            'method' => 'DELETE',
            'data' => []
        ], true);
    }
}