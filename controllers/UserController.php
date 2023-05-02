<?php

namespace eatFitTpi2023\controllers;

use Exception;
use eatFitTpi2023\core\ApiValue;
use eatFitTpi2023\core\Model;
use eatFitTpi2023\core\Request;
use eatFitTpi2023\models\User;

class UserController
{
    /**
     * @throws Exception
     */
    public function readUser(Request $request): ApiValue
    {
        return new ApiValue(User::read($request->getData(Model::REQUIRED_FIELDS['login'])), 200);
    }

    /**
     *
     * @throws Exception
     */
    public function createUser(Request $request): ApiValue
    {
        return new ApiValue("User created", 201);
    }

    /**
     * @throws Exception
     */
    public function updateUser(Request $request): ApiValue
    {
        return new ApiValue("User updated", 200);
    }

    /**
     * @throws Exception
     */
    public function deleteUser(Request $request): ApiValue
    {
        return new ApiValue("User deleted", 200);
    }
}