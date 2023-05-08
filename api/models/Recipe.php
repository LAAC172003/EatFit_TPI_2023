<?php

namespace Eatfit\Api\Models;

use Eatfit\Api\Core\Application;
use Eatfit\Api\Core\Db\SqlResult;
use Eatfit\Api\Core\Model;
use Exception;

class Recipe extends Model
{
    /**
     * Retrieve a recipe or all recipes based on the given data.
     *
     * @param array $data
     * @return array|null
     * @throws Exception
     */
    public static function read(array $data): ?array
    {
//        if (isset($data['search_filters'])) {
//            $searchFilter = null;
//            foreach ($data['search_filters'] as $key => $value) {
//                if (!empty($value)) $searchFilter[$key] = $value;
//            }
//            return self::search($searchFilter);
//        }

        if (!isset($data['search_filters']['title'])) return self::getAllRecipes();
        if (self::getRecipe($data['search_filters']['title'])->isEmpty()) throw new Exception("Recipe not found", 404);
        return self::getRecipe($data['title'])->getFirstRow();
    }

    /**
     * Create a new recipe.
     *
     * @param array $data
     * @return array|Exception
     * @throws Exception
     */
    public static function create(array $data): array|Exception
    {
        $user = self::getUserByToken();
        if (!$user) throw new Exception("Unauthorized", 401);
//        if (!self::getRecipe($data['title'])->isEmpty()) throw new Exception("Recipe already exists", 400);
        $data = self::filterArray($data);
        if (isset($data['category'])) if (!in_array($data['category'], ["Breakfast", "Appetizer", "Lunch", "Dinner", "Snack", "Dessert"])) throw new Exception("Invalid category, the categories allowed are : Breakfast, Appetizer, Lunch, Dinner, Snack, Dessert", 400);
        try {
            $data = [
                'title' => $data['title'],
                'preparation_time' => $data['preparation_time'],
                'difficulty' => $data['difficulty'],
                'instructions' => $data['instructions'],
                'calories' => $data['calories'],
                'created_at' => date("Y-m-d H:i:s"),
                'image' => $data['image'],
                'category' => $data['category'],
                'idUser' => $user['idUser']
            ];

            Application::$app->db->execute("INSERT INTO images (path) VALUES (:path)", [":path" => $data['image']]);


            Application::$app->db->execute("INSERT INTO recipes 
            (title, preparation_time, difficulty, instructions, calories,created_at,idUser)
            VALUES (:title, :preparation_time, :difficulty, :instructions, :calories,:created_at,:idUser)",
                [
                    ":title" => $data['title'],
                    ":preparation_time" => $data['preparation_time'],
                    ":difficulty" => $data['difficulty'],
                    ":instructions" => $data['instructions'],
                    ":calories" => $data['calories'],
                    ":created_at" => $data['created_at'],
                    ":idUser" => $data['idUser']
                ]);
            Application::$app->db->execute("INSERT INTO recipe_categories (idRecipe, idCategory) VALUES ((SELECT idRecipe FROM recipes WHERE title = :title), (SELECT idCategory FROM categories WHERE name = :name))", [":title" => $data['title'], ":name" => $data['category']]);
            Application::$app->db->execute(
                "INSERT INTO recipes_images (idRecipe, idImage) VALUES ((SELECT idRecipe FROM recipes WHERE title = :title), (SELECT idImage FROM images WHERE path = :path))",
                [":title" => $data['title'], ":path" => $data['image']]);
        } catch (Exception $e) {
            var_dump($e);
            throw new Exception("Error creating new recipe", 500);
        }
        return self::getRecipe($data['title'])->getFirstRow();
    }

    /**
     * Create a connection between a recipe and food types.
     *
     * @param array $data
     * @return string
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
            $query = "INSERT INTO recipe_food_type(idRecipe, idFoodType, percentage) VALUES ";
            $query .= str_repeat("((SELECT idRecipe FROM recipes WHERE title = :title), :idFoodType, :percentage),", count($data['idsFoodType']));
            //Je sais pas si ça marche
            Application::$app->db->execute($query, [":title" => $data['title'], ":idFoodType" => $data['idsFoodType'], ":percentage" => $data['percentage']]);
        } catch (Exception $e) {
            throw new Exception("Error creating new recipe", 500);
        }
        return "Recette créée avec succès";
    }


    /**
     * Update an existing recipe.
     *
     * @param array $data
     * @return string
     * @throws Exception
     */
    public static function update(array $data): string
    {
        $data = self::filterArray($data);
        if (!isset($data['wanted_recipe_title'])) throw new Exception("Missing recipe title, -> 'wanted_recipe_title' : 'title'", 400);
        $recipeResult = self::getRecipe($data['wanted_recipe_title']);
        if ($recipeResult->isEmpty()) throw new Exception("Recipe not found", 404);
        if ($recipeResult->getFirstRow()['idUser'] != self::getUserByToken()['idUser']) throw new Exception("Unauthorized", 401);
        $updates = [];
        if (isset($data['difficulty'])) {
            if ($data['difficulty'] != "easy" && $data['difficulty'] != "medium" && $data['difficulty'] != "hard") throw new Exception("Difficulty must be 'easy', 'medium' or 'hard'", 400);
        }
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
     * Delete a recipe.
     *
     * @param array $data
     * @return string
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
     * Retrieve a recipe by title.
     *
     * @param string $title
     * @return SqlResult
     * @throws Exception
     */
    private static function getRecipe(string $title): SqlResult
    {
        $query = "SELECT * FROM recipes WHERE title = :title";
        return Application::$app->db->execute($query, [":title" => $title]);
    }

    /**
     * Retrieve all recipes.
     *
     * @return array
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
     * Search recipes based on the provided filters.
     *
     * @param array $filters
     * @return array
     * @throws Exception
     */
    public static function search(array $filters): array
    {
        $query = "SELECT * FROM recipes WHERE 1 = 1";
        $params = [];
        if (isset($filters['title'])) {
            $query .= " and title LIKE :title";
            $params[':title'] = '%' . $filters['title'] . '%';
        }

        if (isset($filters['category'])) {
            $query .= " and idCategory = (SELECT idCategory FROM categories WHERE name = :category)";
            $params[':category'] = $filters['category'];
        }

        if (isset($filters['date_added'])) {
            $query .= " and DATE(created_at) = :date_added";
            $params[':date_added'] = $filters['date_added'];
        }
        try {
            $statement = Application::$app->db->execute($query, $params);
        } catch (Exception $e) {
            var_dump($e);
            throw new Exception("Error searching recipes", 500);
        }

        if ($statement->isEmpty()) throw new Exception("No recipes found", 404);

        return $statement->getValues();
    }

    /**
     * Filter recipes by category or food type.
     *
     * @param array $filters
     * @return array
     * @throws Exception
     */
    public static function filterByCategoryOrFoodType(array $filters): array
    {
        $query = "SELECT DISTINCT r .* FROM recipes r";
        $params = [];

        if (isset($filters['category'])) {
            $query .= " INNER JOIN categories c ON r.idCategory = c.idCategory";
            $query .= " AND c.name = :category";
            $params[':category'] = $filters['category'];
        }

        if (isset($filters['food_type'])) {
            $query .= " INNER JOIN recipe_food_type rft ON r.idRecipe = rft.idRecipe";
            $query .= " INNER JOIN food_types ft ON rft.idFoodType = ft.idFoodType";
            $query .= " AND ft.name = :food_type";
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
    /**
     * Add a recipe to the user's history.
     *
     * @param array $data
     * @return string
     * @throws Exception
     */
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

        $date = date("Y - m - d") ?? $data['date'];

        try {
            Application::$app->db->execute("INSERT INTO consumed_recipes(user_id, recipe_id, date) VALUES(:user_id, :recipe_id, :date)", [
                ":user_id" => $user['idUser'],
                ":recipe_id" => $data['recipe_id'],
                ":date" => $date
            ]);
        } catch (Exception $e) {
            throw new Exception("Error adding recipe to history", 500);
        }

        return "Recette ajoutée à l'historique avec succès";
    }

    /**
     * Retrieve a recipe by ID.
     *
     * @param int $recipe_id
     * @return array|null
     * @throws Exception
     */
    private static function getRecipeById(int $recipe_id): ?array
    {
        $query = "SELECT * FROM recipes WHERE idRecipe = :recipe_id";
        $statement = Application::$app->db->execute($query, [":recipe_id" => $recipe_id]);

        return $statement->isEmpty() ? null : $statement->getFirstRow();
    }


}