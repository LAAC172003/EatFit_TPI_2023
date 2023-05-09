<?php

namespace Eatfit\Api\Models;

use Eatfit\Api\Core\Application;
use Eatfit\Api\Core\Db\SqlResult;
use Eatfit\Api\Core\Model;
use Exception;
use http\Encoding\Stream\Inflate;

class Rating extends Model
{
    /**
     * @throws Exception
     */
    public static function read(): array
    {
        return Application::$app->db->execute("SELECT * FROM ratings")->getValues();
    }

    /**
     * @throws \Exception
     */
    public static function create(array $data): array
    {
        $query = "INSERT INTO ratings (score,comment, idUser, idRecipe) VALUES (:score, :comment, :idUser, :idRecipe)";
        $data = self::filterArray($data);
        $user = self::getUserByToken();
        if (!isset($data['score'])) throw new \Exception("Score is required", 400);
        if (!is_numeric($data['score'])) throw new \Exception("Score must be a number", 400);
        if ($data['score'] < 1 || $data['score'] > 5) throw new \Exception("Score must be between 1 and 5", 400);
        if (!isset($data['idRecipe'])) throw new \Exception("Recipe id is required", 400);
        if (!is_numeric($data['idRecipe'])) throw new \Exception("Recipe id must be a number", 400);
        $data['idUser'] = $user['idUser'];
        if (isset($data['comment'])) if ($data['comment'] == "") $data['comment'] = null;
        try {
            Application::$app->db->execute($query, [":score" => $data['score'], ":comment" => $data['comment'], ":idUser" => $data['idUser'], ":idRecipe" => $data['idRecipe']]);
        } catch (\Exception $e) {
            var_dump($e);
            throw new \Exception("Error creating rating", 500);
        }
        return Application::$app->db->execute("SELECT * FROM ratings WHERE idRating = :idRating", [":idRating" => Application::$app->db->getLastInsertId()])->getFirstRow();
    }


    /**
     * @throws Exception
     */
    public
    static function update(array $data): string
    {
        $data = self::filterArray($data);
        if (!isset($data['idRating'])) throw new Exception("Rating id is required", 400); else if (!is_numeric($data['idRating'])) throw new Exception("Rating id must be a number", 400);
        if (self::getUserByToken()['idUser'] != Application::$app->db->execute("SELECT idUser FROM ratings WHERE idRating = :idRating", [":idRating" => $data['idRating']])->getFirstRow()['idUser']) throw new Exception("You can't update this rating", 403);

        if (!isset($data['score'])) throw new Exception("Score is required", 400);
        if ($data['score'] < 1 || $data['score'] > 5) throw new \Exception("Score must be between 1 and 5", 400);
        $updates = [];
        foreach ($data as $key => $value) {
            if ($key == "idRating") continue;
            if (isset($value) && $value != "") $updates[$key] = $value;
        }
        $sb = "L'évaluation à été mis à jour avec succès :";
        try {
            $query = "UPDATE ratings SET " . implode(", ", array_map(fn($key) => "$key = :$key", array_keys($updates))) . " WHERE idRating = :idRating";
            Application::$app->db->execute($query, array_merge($updates, [":idRating" => $data['idRating']]));
            $sb .= implode(", ", array_map(fn($key, $value) => "$key = $value", array_keys($updates), array_values($updates)));
        } catch (Exception $e) {
            throw new Exception("Error updating rating", 500);
        }
        return $sb;
    }

    /**
     * @throws Exception
     */
    public
    static function delete(array $data): string
    {
        $data = self::filterArray($data);
        if (!isset($data['idRating'])) throw new Exception("Rating id is required", 400); else if (!is_numeric($data['idRating'])) throw new Exception("Rating id must be a number", 400);
        if (self::getRatingById($data['idRating'])->isEmpty()) throw new Exception("Rating not found", 404);
        if (self::getUserByToken()['idUser'] != Application::$app->db->execute("SELECT idUser FROM ratings WHERE idRating = :idRating", [":idRating" => $data['idRating']])->getFirstRow()['idUser']) throw new Exception("You can't update this rating", 403);
        try {
            Application::$app->db->execute("DELETE FROM ratings WHERE idRating = :idRating", [":idRating" => $data['idRating']]);
        } catch (Exception $e) {
            throw new Exception("Error deleting rating", 500);
        }
        return "Rating deleted successfully";
    }

    /**
     * @throws Exception
     */
    public static function getRatingById($idRating): SqlResult
    {
        return Application::$app->db->execute("SELECT * FROM ratings WHERE idRating = :idRating", [":idRating" => $idRating]);
    }
}