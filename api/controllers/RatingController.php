<?php

namespace Eatfit\Api\Controllers;

use Eatfit\Api\Core\ApiValue;
use Eatfit\Api\Core\Request;
use Eatfit\Api\Models\Rating;
use Exception;

class RatingController
{
    /**
     * Récupère une évaluation spécifiée pour une recette spécifiée.
     *
     * @param Request $request La requête HTTP.
     * @return ApiValue L'objet ApiValue contenant la réponse de l'API.
     * @throws Exception
     */
    public function read(Request $request): ApiValue
    {
        return new ApiValue(Rating::read($request->getData(['idRecipe', 'idRating'], false)), 200);
    }

    /**
     * Crée une nouvelle évaluation pour une recette spécifiée.
     *
     * @param Request $request La requête HTTP.
     * @return ApiValue L'objet ApiValue contenant la réponse de l'API.
     * @throws Exception
     */
    public function create(Request $request): ApiValue
    {
        return new ApiValue(Rating::create($request->getData(['score', 'comment', 'idRecipe'], false)), 201);
    }

    /**
     * Met à jour une évaluation spécifiée.
     *
     * @param Request $request La requête HTTP.
     * @return ApiValue L'objet ApiValue contenant la réponse de l'API.
     * @throws Exception
     */
    public function update(Request $request): ApiValue
    {
        return new ApiValue(Rating::update($request->getData(["idRating", "score", "comment"], false)), 200);
    }

    /**
     * Supprime une évaluation spécifiée.
     *
     * @param Request $request La requête HTTP.
     * @return ApiValue L'objet ApiValue contenant la réponse de l'API.
     * @throws Exception
     */
    public function delete(Request $request): ApiValue
    {
        return new ApiValue(Rating::delete($request->getData(["idRating"])), 200);
    }
}