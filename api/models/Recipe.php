<?php

namespace Eatfit\Api\Models;

use Eatfit\Api\Core\Application;
use Eatfit\Api\Core\Db\SqlResult;
use Eatfit\Api\Core\Model;
use Exception;

class Recipe extends Model
{
    /**
     * @throws Exception
     */
    public static function read(array $data): array|Exception
    {
//        if (self::getUserByToken() === false) throw new Exception("Unauthorized", 401);
        //Si on ne reçoit pas de titre, on renvoie toutes les recettes sinon on renvoie la recette correspondante
        if (!isset($data['title'])) return self::getAllRecipes();
        return self::getRecipe($data['title'])->getFirstRow();
    }

    /**
     * @throws Exception
     */
    public static function create(array $data): array|Exception
    {
        $user = self::getUserByToken();
        if (!$user) throw new Exception("Unauthorized", 401);
        if (!self::getRecipe($data['title'])->isEmpty()) throw new Exception("Recipe already exists", 400);
        try {
            $data = [
                'title' => $data['title'],
                'preparation_time' => $data['preparation_time'],
                'difficulty' => $data['difficulty'],
                'instructions' => $data['instructions'],
                'calories' => $data['calories'],
                'created_at' => date("Y-m-d H:i:s"),
                'image' => $data['image'],
                'idUser' => $user['idUser']
            ];
            Application::$app->db->execute("INSERT INTO recipes (title, preparation_time, difficulty, instructions, calories,created_at,image,idUser) VALUES (:title, :preparation_time, :difficulty, :instructions, :calories,:created_at,:image,:idUser)", [":title" => $data['title'], ":preparation_time" => $data['preparation_time'], ":difficulty" => $data['difficulty'], ":instructions" => $data['instructions'], ":calories" => $data['calories'], ":created_at" => $data['created_at'], ":image" => $data['image'], ":idUser" => $data['idUser']]);
        } catch (Exception $e) {
            throw new Exception("Error creating new recipe", 500);
        }
        return self::getRecipe($data['title'])->getFirstRow();
    }

    /**
     * @throws Exception
     */
    public static function createFoodTypeConnection(array $data): string
    {
        if (self::getRecipe($data['wanted_recipe_title'])->getFirstRow()['idUser'] != self::getUserByToken()['idUser']) throw new Exception("Unauthorized", 401);
        try {
            $data = [
                'title' => "test",
                'idsFoodType' => [1, 2, 3, 4],
                'percentage' => [10, 20, 30, 40],
            ];
            $query = "INSERT INTO recipe_food_type (idRecipe, idFoodType, percentage) VALUES ";
            $query .= str_repeat("((SELECT idRecipe FROM recipes WHERE title = :title), :idFoodType, :percentage),", count($data['idsFoodType']));
            //Je sais pas si ça marche
            Application::$app->db->execute($query, [":title" => $data['title'], ":idFoodType" => $data['idsFoodType'], ":percentage" => $data['percentage']]);
        } catch (Exception $e) {
            throw new Exception("Error creating new recipe", 500);
        }
        return "Recette créée avec succès";
    }


    /**
     * @throws Exception
     */
    public static function update(array $data): string
    {
        if (!isset($data['wanted_recipe_title'])) throw new Exception("Missing recipe title, -> 'wanted_recipe_title' : 'title'", 400);
        if (self::getRecipe($data['wanted_recipe_title'])->getFirstRow()['idUser'] != self::getUserByToken()['idUser']) throw new Exception("Unauthorized", 401);
        if (self::getRecipe($data['wanted_recipe_title'])->isEmpty()) throw new Exception("Recipe not found", 404);
        $updates = [];

        foreach ($data as $key => $value) {
            if ($key == "wanted_recipe_title") continue;
            if (isset($value) && $value != "") $updates[$key] = $value;
        }
        $sb = "Recette mise à jour avec succès :";
        try {
            $query = "UPDATE recipes SET " . implode(", ", array_map(fn($key) => "$key = :$key", array_keys($updates))) . " WHERE title = :wanted_recipe_title";
            Application::$app->db->execute($query, array_merge($updates, [":wanted_recipe_title" => $data['wanted_recipe_title']]));
            $sb .= implode(", ", array_map(fn($key, $value) => "$key = $value", array_keys($updates), array_values($updates)));
        } catch (Exception $e) {
            throw new Exception("Error updating recipe", 500);
        }
        return $sb;
    }

    /**
     * @throws Exception
     */
    public static function delete(array $data): string
    {
        if (self::getRecipe($data['title'])->getFirstRow()['idUser'] != self::getUserByToken()['idUser']) throw new Exception("Unauthorized", 401);
        if (self::getRecipe($data['title'])->isEmpty()) throw new Exception("Recipe not found", 404);
        try {
            Application::$app->db->execute("DELETE FROM recipes WHERE title = :title", [":title" => $data['title']]);
        } catch (Exception $e) {
            throw new Exception("Error deleting recipe", 500);
        }
        // Retournez un message de succès
        return "Recette supprimée avec succès";
    }

    /**
     * @throws Exception
     */
    private static function getRecipe(string $title): SqlResult
    {
        $query = "SELECT * FROM recipes WHERE title = :title";
        return Application::$app->db->execute($query, [":title" => $title]);
    }

    /**
     * @throws Exception
     */
    private
    static function getAllRecipes(): array
    {
        $statement = Application::$app->db->execute("SELECT * FROM recipes");
        if ($statement->isEmpty()) throw new Exception("No recipes found", 404);
        return $statement->getValues();
    }

    /**
     * @throws Exception
     */
    public static function search(array $filters): array
    {
        $query = "SELECT * FROM recipes WHERE 1 = 1";
        $params = [];

        if (isset($filters['title'])) {
            $query .= " AND title LIKE :title";
            $params[':title'] = '%' . $filters['title'] . '%';
        }

        if (isset($filters['category'])) {
            $query .= " AND idCategory = (SELECT idCategory FROM categories WHERE name = :category)";
            $params[':category'] = $filters['category'];
        }

        if (isset($filters['date_added'])) {
            $query .= " AND DATE(created_at) = :date_added";
            $params[':date_added'] = $filters['date_added'];
        }

        $statement = Application::$app->db->execute($query, $params);

        if ($statement->isEmpty()) {
            throw new Exception("No recipes found", 404);
        }

        return $statement->getValues();
    }

    /**
     * @throws Exception
     */
    public static function filterByCategoryOrFoodType(array $filters): array
    {
        $query = "SELECT r.* FROM recipes r";
        $params = [];

        if (isset($filters['category'])) {
            $query .= " INNER JOIN categories c ON r.idCategory = c.idCategory";
            $query .= " WHERE c.name = :category";
            $params[':category'] = $filters['category'];
        }

        if (isset($filters['food_type'])) {
            $query .= " INNER JOIN recipe_food_type rft ON r.idRecipe = rft.idRecipe";
            $query .= " INNER JOIN food_types ft ON rft.idFoodType = ft.idFoodType";
            $query .= isset($filters['category']) ? " AND" : " WHERE";
            $query .= " ft.name = :food_type";
            $params[':food_type'] = $filters['food_type'];
        }

        $statement = Application::$app->db->execute($query, $params);

        if ($statement->isEmpty()) {
            throw new Exception("No recipes found", 404);
        }

        return $statement->getValues();
    }
////////////////////////////////////////////
///
///
///
///
///
///
///
///
///
///          ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public static function addToHistory(array $data): string
    {
        $user = self::getUserByToken();
        if (!$user) {
            throw new Exception("Unauthorized", 401);
        }

        if (!isset($data['recipe_id'])) {
            throw new Exception("Recipe ID is required", 400);
        }

        $recipe = self::getRecipeById($data['recipe_id']);

        if (!$recipe) {
            throw new Exception("Recipe not found", 404);
        }

        $date = isset($data['date']) ? $data['date'] : date("Y-m-d");

        try {
            Application::$app->db->execute("INSERT INTO consumed_recipes (user_id, recipe_id, date) VALUES (:user_id, :recipe_id, :date)", [
                ":user_id" => $user['idUser'],
                ":recipe_id" => $data['recipe_id'],
                ":date" => $date
            ]);
        } catch (Exception $e) {
            throw new Exception("Error adding recipe to history", 500);
        }

        return "Recette ajoutée à l'historique avec succès";
    }

    private static function getRecipeById(int $recipe_id): ?array
    {
        $query = "SELECT * FROM recipes WHERE idRecipe = :recipe_id";
        $statement = Application::$app->db->execute($query, [":recipe_id" => $recipe_id]);

        return $statement->isEmpty() ? null : $statement->getFirstRow();
    }


}