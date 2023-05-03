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
    public function read(Request $request): ApiValue
    {
        return new ApiValue(User::read($request->getData(['email', "password"])), 200);
    }

    /**
     *
     * @throws Exception
     */
    public function create(Request $request): ApiValue
    {
        return new ApiValue(User::create($request->getData(['email', "password", "username"])), 201);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): ApiValue
    {
        return new ApiValue(User::update($request->getData(['email', "password", "username"], false)), 200);
    }

    /**
     * @throws Exception
     */
    public function delete(Request $request): ApiValue
    {
        return new ApiValue(User::delete(), 200);
    }
}