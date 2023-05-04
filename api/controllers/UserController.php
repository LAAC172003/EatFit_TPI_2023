<?php

namespace Eatfit\Api\Controllers;

use Eatfit\Api\Core\ApiValue;
use Eatfit\Api\Core\Request;
use Eatfit\Api\Models\User;
use Exception;

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
        return new ApiValue(User::create($request->getData(['email', "password", "confirm_password", "username"])), 201);
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