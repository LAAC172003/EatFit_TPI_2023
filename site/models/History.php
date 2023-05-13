<?php

namespace Eatfit\Site\Models;

use Eatfit\Site\Core\Model;

class History extends Model
{
    public int $idRecipe = 0;
    public string $title = '';
    public string $consumption_date = '';
    public string $meal_time = '';
    public int $idUser = 0;
    public int $idConsumedRecipe = 0;

    public function rules(): array
    {
        return [
            'idRecipe' => [self::RULE_REQUIRED],
            'title' => [self::RULE_REQUIRED],
            'consumption_date' => [self::RULE_REQUIRED],
            'meal_time' => [self::RULE_REQUIRED],
        ];
    }

    public function labels(): array
    {
        return [
            'idRecipe' => "Identifiant de la recette: ",
            'title' => "Titre de la recette: ",
            'consumption_date' => "Date de consommation: ",
            'meal_time' => "Moment de la consommation: ",
        ];
    }

    public function save()
    {
        $data = [
            'idRecipe' => $this->idRecipe
        ];

        return self::getJsonResult([
            'url' => 'history',
            'method' => 'POST',
            'data' => $data
        ], true);
    }

    public function getHistory(int $idRecipe = 0, $addBearer = true)
    {
        if ($idRecipe == 0 || $idRecipe == null) {
            return self::getJsonResult([
                'url' => 'history',
                'method' => 'GET',
                'data' => []
            ], $addBearer);
        }
        return self::getJsonResult([
            'url' => 'history',
            'method' => 'GET',
            'data' => [
                'idRecipe' => $idRecipe
            ]
        ], $addBearer);
    }


    public function updateHistory($idConsumedRecipe, $addBearer = true)
    {
        $data = [
            'idConsumedRecipe' => $this->idConsumedRecipe,
            'consumption_date' => $this->consumption_date,
        ];

        return self::getJsonResult([
            'url' => 'history',
            'method' => 'PUT',
            'data' => $data
        ], $addBearer);
    }

    public function deleteHistory($idConsumedRecipe, $addBearer = true)
    {
        return self::getJsonResult([
            'url' => 'history',
            'method' => 'DELETE',
            'data' => [
                'idConsumedRecipe' => $idConsumedRecipe
            ]
        ], $addBearer);
    }

    public function deleteAllHistory($addBearer = true)
    {
        return self::getJsonResult([
            'url' => 'history',
            'method' => 'DELETE',
            'data' => []
        ], $addBearer);
    }
}
