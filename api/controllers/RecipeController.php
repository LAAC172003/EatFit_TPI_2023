<?php

namespace Eatfit\Api\Controllers;

use Eatfit\Api\Core\ApiValue;
use Eatfit\Api\Core\Request;
use Eatfit\Api\Models\Recipe;
use Exception;

class RecipeController
{
    /**
     * Récupère les recettes en fonction des filtres de recherche spécifiés.
     *
     * @param Request $request La requête contenant les données des filtres de recherche.
     * @return ApiValue Les recettes trouvées en fonction des filtres, avec un code de statut HTTP 200.
     * @throws Exception
     */
    public function read(Request $request): ApiValue
    {
        return new ApiValue(Recipe::read($request->getData([
            'search',
            'filter',
            "idRecipe"
        ], false)), 200);
    }

    /**
     * Crée une nouvelle recette en utilisant les données fournies.
     *
     * @param Request $request La requête contenant les données de la nouvelle recette.
     * @return ApiValue La recette nouvellement créée, avec un code de statut HTTP 201.
     * @throws Exception
     */
    public function create(Request $request): ApiValue
    {
        return new ApiValue(Recipe::create($request->getData([
            "title",
            "preparation_time",
            "difficulty",
            "instructions",
            "calories",
            "image",
            "category",
            "food_type"
        ])), 201);
    }

    /**
     * Met à jour une recette existante en utilisant les données fournies.
     *
     * @param Request $request La requête contenant les données de mise à jour pour la recette.
     * @return ApiValue La recette mise à jour, avec un code de statut HTTP 200.
     * @throws Exception
     */
    public function update(Request $request): ApiValue
    {
        return new ApiValue(Recipe::update($request->getData(["idRecipe", "title", "preparation_time", "difficulty", "instructions", "calories", "image"], false)), 200);
    }

    /**
     * Supprime une recette en fonction du titre spécifié.
     *
     * @param Request $request La requête contenant le titre de la recette à supprimer.
     * @return ApiValue Un message indiquant que la recette a été supprimée, avec un code de statut HTTP 200.
     * @throws Exception
     */
    public function delete(Request $request): ApiValue
    {
        return new ApiValue(Recipe::delete($request->getData(["idRecipe"])), 200);
    }


    /**
     * Ajoute un nouveau type de nourriture.
     *
     * @param Request $request La requête contenant les données du nouveau type de nourriture.
     * @return ApiValue Le nouveau type de nourriture créé, avec un code de statut HTTP 201.
     * @throws Exception
     */
    public function addFoodType(Request $request): ApiValue
    {
        return new ApiValue(Recipe::addFoodType($request->getData(['name'])), 201);
    }

    /**
     * Récupère les catégories de recettes disponibles.
     *
     * @return ApiValue Les catégories de recettes, avec un code de statut HTTP 200.
     * @throws Exception
     */
    public function getCategories(): ApiValue
    {
        return new ApiValue(Recipe::getCategories(), 200);
    }

    /**
     * Récupère les types de nourriture disponibles.
     *
     * @return ApiValue Les types de nourriture, avec un code de statut HTTP 200.
     * @throws Exception
     */
    public function getFoodTypes(): ApiValue
    {
        return new ApiValue(Recipe::getFoodTypes(), 200);
    }
}
