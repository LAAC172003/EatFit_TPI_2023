<?php

namespace Eatfit\Api\Controllers;

use Eatfit\Api\Core\ApiValue;
use Eatfit\Api\Core\Request;
use Eatfit\Api\Models\History;
use Eatfit\Api\Models\Recipe;
use Exception;

class HistoryController
{
    /**
     * @throws Exception
     */
    public function create(Request $request): ApiValue
    {
        return new ApiValue(History::create($request->getData(['idRecipe'])), 200);
    }

    /**
     * @throws Exception
     */
    public function delete(Request $request): ApiValue
    {
        return new ApiValue(History::delete($request->getData(['idConsumedRecipe'], false)), 200);
    }

    public function deleteAll(Request $request): ApiValue
    {
        return new ApiValue(History::deleteAll(), 200);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): ApiValue
    {
        return new ApiValue(History::update($request->getData(['idConsumedRecipe', 'consumption_date'])), 200);
    }

    /**
     * @throws Exception
     */
    public function read(Request $request): ApiValue
    {
        return new ApiValue(History::read($request->getData(['idRecipe'], false)), 200);
    }


}