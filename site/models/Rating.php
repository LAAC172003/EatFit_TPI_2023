<?php

namespace Eatfit\Site\Models;

use Eatfit\Site\Core\Model;

class Rating extends Model
{
    public int $idRating = 0;
    public int $score = 0;
    public string $comment = '';
    public int $idRecipe = 0;
    public int $idUser = 0;

    public function rules(): array
    {
        return [
            'score' => [self::RULE_REQUIRED]
        ];
    }

    public function labels(): array
    {
        return [
            'score' => "Note (1-5): ",
            'comment' => "Commentaire: ",
        ];
    }

    public function getRatingByIdRecipe($addBearer = true)
    {
        return self::getJsonResult([
            'url' => 'rating',
            'method' => 'GET',
            'data' => [
                'idRecipe' => $this->idRecipe
            ]
        ], $addBearer);
    }

    public function getRatingById()
    {
        return self::getJsonResult([
            'url' => 'rating',
            'method' => 'GET',
            'data' => [
                'idRating' => $this->idRating
            ]
        ], true);
    }

    public function create()
    {
        return self::getJsonResult([
            'url' => 'rating',
            'method' => 'POST',
            'data' => [
                'score' => $this->score,
                'comment' => $this->comment,
                'idRecipe' => $this->idRecipe
            ]
        ], true);
    }

    public function update()
    {
        $updates = [];
        foreach ($this->attributes() as $attribute) {
            if (!isset($this->$attribute)) continue;
            $updates[$attribute] = $this->$attribute;
        }
        var_dump($updates);
        return self::getJsonResult([
            'url' => 'rating',
            'method' => 'PUT',
            'data' => $updates
        ], true);
    }

    public function attributes(): array
    {
        return ['idRating', 'score', 'comment'];
    }

    public function delete()
    {
        return self::getJsonResult([
            'url' => 'rating',
            'method' => 'DELETE',
            'data' => [
                'idRating' => $this->idRating
            ]
        ], true);
    }
}