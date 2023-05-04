<?php

namespace eatFitTpi2023\controllers;

use eatFitTpi2023\core\ApiValue;
use eatFitTpi2023\core\Request;
use eatFitTpi2023\models\Recipe;

class RecipeController
{
    /**
     * @throws \Exception
     */
    public function read(Request $request): ApiValue
    {
        return new ApiValue(Recipe::read($request->getData(['title'], false)), 200);
    }

    /**
     * @throws \Exception
     */
    public function create(Request $request)
    {
        return new ApiValue(Recipe::create($request->getData(["title", "preparation_time", "difficulty", "instructions", "calories", "created_at", "image"])), 201);
    }

    public function update()
    {
        return "update";
    }

    public function delete()
    {
        return "delete";
    }


}