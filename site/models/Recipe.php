<?php

namespace Eatfit\Site\Models;

use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Model;

class Recipe extends Model
{
    public int $idRecipe = 0;
    public string $title = '';
    public int $preparation_time = 0;
    public string $difficulty = '';
    public string $instructions = '';
    public int $calories = 0;
    public $date = '';
    public int $category = 0;
    public int $image = 0;
    public int $idUser = 0;

    public function rules(): array
    {
        return [
            'title' => [self::RULE_REQUIRED],
            'preparation_time' => [self::RULE_REQUIRED, self::RULE_EMAIL],
            'difficulty' => [self::RULE_REQUIRED, self::RULE_EMAIL],
            'instructions' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8]],
            'calories' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8]],
            'category' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8]],
            'image' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8]],
        ];
    }

    public function labels(): array
    {
        return [
            'title' => "Titre: ",
            'preparation_time' => "Temps de préparation: ",
            'difficulty' => "Difficulté: ",
            'instructions' => "Instructions: ",
            'calories' => "Calories: ",
            'category' => "Catégorie: ",
            'image' => "Image: ",
        ];
    }


    public function save()
    {
        return self::getJsonResult([
            'url' => 'recipe',
            'method' => 'POST',
            'data' => [
                'title' => $this->title,
                'preparation_time' => $this->preparation_time,
                'difficulty' => $this->difficulty,
                'instructions' => $this->instructions,
                'calories' => $this->calories,
                'category' => $this->category,
                'image' => $this->image
            ]
        ], true);
    }

    private function getRecipe($search)
    {
        return self::getJsonResult([
            'url' => 'recipe',
            'method' => 'GET',
            'data' => [
                'search' => $search
            ]
        ], true);
    }

    public function getRecipeByFilter($filter, $search)
    {
        return self::getJsonResult([
            'url' => 'recipe',
            'method' => 'GET',
            'data' => [
                'filter' => [$filter, $search]
            ]
        ], true);
    }
}

