<?php

namespace Eatfit\Site\Models;

use Eatfit\Site\Core\Model;

class FoodType extends Model
{
    public int $idFoodType = 0;
    public string $name = '';


    public static function getFoodTypes()
    {
        return self::getJsonResult([
            'url' => 'food_types',
            'method' => 'GET',
            'data' => []
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [self::RULE_REQUIRED]
        ];
    }

    public function labels(): array
    {
        return [
            'name' => "Nom: "
        ];
    }

    public function save()
    {
        return self::getJsonResult([
            'url' => 'food_type',
            'method' => 'POST',
            'data' => [
                'name' => $this->name
            ]
        ], true);
    }

}