<?php

namespace Eatfit\Site\Models;

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

    public array $categories = [];
    public array $image = [];
    public array $foodType = [];
    public int $idUser = 0;


    public function rules(): array
    {
        return [
            'title' => [self::RULE_REQUIRED],
            'preparation_time' => [self::RULE_REQUIRED],
            'difficulty' => [self::RULE_REQUIRED],
            'instructions' => [self::RULE_REQUIRED],
            'calories' => [self::RULE_REQUIRED],
            'category' => [self::RULE_REQUIRED],
            'foodType' => [self::RULE_REQUIRED]
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
            'foodType' => "Type de nourriture: "
        ];
    }

    function validateAndPrepareImages($files): array
    {
        $images = $files['file-input'];
        $numberOfFiles = count($images['name']);
        $imageContents = [];
        if ($images['error'][0] == 4) return [];
        for ($i = 0; $i < $numberOfFiles; $i++) {
            if ($images['error'][$i] !== UPLOAD_ERR_OK) return ['error' => 'Upload error for file ' . $images['name'][$i]];
            if (!getimagesize($images['tmp_name'][$i])) return ['error' => 'File ' . $images['name'][$i] . ' is not an image'];
            $imageContent = file_get_contents($images['tmp_name'][$i]);
            $imageContents[$images['name'][$i]] = base64_encode($imageContent);
        }

        return $imageContents;
    }

    public function create()
    {
        $validatedImages = $this->validateAndPrepareImages($_FILES);
        if (isset($validatedImages['error'])) {
            return false;
        } else {
            $images = [];
            //https://stackoverflow.com/questions/3967515/how-to-convert-an-image-to-base64-encoding
            foreach ($validatedImages as $name => $base64) $images[] = $name . "," . $base64;
            if ($validatedImages == []) $images = "";
            $data = [
                'title' => $this->title,
                'preparation_time' => $this->preparation_time,
                'difficulty' => $this->difficulty,
                'instructions' => $this->instructions,
                'calories' => $this->calories,
                'image' => $images,
                'category' => $this->categories,
                'food_type' => $this->foodType
            ];
            return self::getJsonResult([
                'url' => 'recipe',
                'method' => 'POST',
                'data' => $data
            ], true);
        }
    }

    public function delete()
    {
        return self::getJsonResult([
            'url' => 'recipe',
            'method' => 'DELETE',
            'data' => [
                'idRecipe' => $this->idRecipe
            ]
        ], true);
    }


    public function getRecipe($field, $search, $addBearer = true)
    {
        return self::getJsonResult([
            'url' => 'recipe',
            'method' => 'GET',
            'data' => [
                $field => $search
            ]
        ], $addBearer);
    }

    public function getRecipeByFilter($filter, $search)
    {
        return self::getJsonResult([
            'url' => 'recipe',
            'method' => 'GET',
            'data' => [
                'filter' => [
                    $filter => $search
                ]
            ]
        ]);
    }

    public function getCategories()
    {
        return self::getJsonResult([
            'url' => 'categories',
            'method' => 'GET',
            'data' => []
        ]);
    }

    public function update()
    {
        $updates = [];
        foreach ($this->attributes() as $attribute) {
            if ($this->{$attribute} != 'idRecipe') continue;
            $updates[$attribute] = $this->{$attribute};
        }
        return self::getJsonResult([
            'url' => 'recipe',
            'method' => 'PUT',
            'data' => $updates
        ], true);
    }
}

