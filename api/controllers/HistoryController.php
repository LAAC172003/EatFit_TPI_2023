<?php

namespace Eatfit\Api\Controllers;

use Eatfit\Api\Core\ApiValue;
use Eatfit\Api\Core\Request;
use Eatfit\Api\Models\History;
use Exception;

class HistoryController
{
    /**
     * Crée une nouvelle entrée d'historique.
     *
     * @param Request $request La requête HTTP.
     * @return ApiValue L'objet ApiValue contenant la réponse de l'API.
     * @throws Exception
     */
    public function create(Request $request): ApiValue
    {
        return new ApiValue(History::create($request->getData(['idRecipe'])), 200);
    }

    /**
     * Supprime une entrée d'historique spécifiée.
     *
     * @param Request $request La requête HTTP.
     * @return ApiValue L'objet ApiValue contenant la réponse de l'API.
     * @throws Exception
     */
    public function delete(Request $request): ApiValue
    {
        return new ApiValue(History::delete($request->getData(['idConsumedRecipe'], false)), 200);
    }

    /**
     * Supprime toutes les entrées d'historique.
     *
     * @param Request $request La requête HTTP.
     * @return ApiValue L'objet ApiValue contenant la réponse de l'API.
     */
    public function deleteAll(Request $request): ApiValue
    {
        return new ApiValue(History::deleteAll(), 200);
    }

    /**
     * Met à jour une entrée d'historique spécifiée.
     *
     * @param Request $request La requête HTTP.
     * @return ApiValue L'objet ApiValue contenant la réponse de l'API.
     * @throws Exception
     */
    public function update(Request $request): ApiValue
    {
        return new ApiValue(History::update($request->getData(['idConsumedRecipe', 'consumption_date'])), 200);
    }


    /**
     * Récupère les entrées d'historique pour une recette spécifiée.
     *
     * @param Request $request La requête HTTP.
     * @return ApiValue L'objet ApiValue contenant la réponse de l'API.
     * @throws Exception
     */
    public function read(Request $request): ApiValue
    {
        return new ApiValue(History::read($request->getData(['idRecipe'], false)), 200);
    }


}