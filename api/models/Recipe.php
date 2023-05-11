<?php

namespace Eatfit\Api\Models;

use DateTime;
use Eatfit\Api\Core\Application;
use Eatfit\Api\Core\Db\SqlResult;
use Eatfit\Api\Core\Model;
use Exception;
use http\Exception\BadQueryStringException;

class Recipe extends Model
{
    /**
     * Retrieve a recipe or all recipes based on the given data.
     * If no specific recipe title is provided in search filters, all recipes are returned.
     * Otherwise, it returns the specific recipe matching the title.
     * Throws an exception if no recipe matches the title.
     *
     * @param array $data
     * @return array|null
     * @throws Exception
     */
    public static function read(array $data): ?array
    {
        if (isset($data['idRecipe'])) {
            $recipe = self::getRecipeById($data['idRecipe']);
            if ($recipe == null) throw new Exception("Recipe not found", 404);
            return $recipe;
        }
        if (!isset($data['search'])) {
            if (isset($data['filter'])) {
                if ($data['filter'][0] == 'category') return self::filter($data['filter'][1])->getValues();
                if ($data['filter'][0] == 'food_type') return self::filter(null, $data['filter'][1])->getValues();
            }
            return self::getAllRecipes();
        }

        try {
            $recipe = self::search($data['search']);
        } catch (Exception $e) {
            throw new Exception("Error searching for recipe: " . $e->getMessage(), 500);
        }
        if ($recipe->isEmpty()) throw new Exception("Recipe not found", 404);
        return $recipe->getValues();
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
        self::validateRecipeData($data);

        $data = [
            'title' => $data['title'],
            'preparation_time' => $data['preparation_time'],
            'difficulty' => $data['difficulty'],
            'instructions' => $data['instructions'],
            'calories' => $data['calories'],
            'created_at' => date("Y-m-d H:i:s"),
            'image' => $data['image'],
            'category' => $data['category'],
            'idUser' => $user['idUser'],
            'food_type' => $data['food_type']
        ];

        try {
            Application::$app->db->beginTransaction();
            self::insertRecipe($data, $user);
            self::insertRecipeCategories($data);
            self::insertRecipeFoodType($data);
            self::insertRecipeImages($data);
            Application::$app->db->commit();
        } catch (Exception $e) {
            Application::$app->db->rollBack();
            throw new Exception("Error creating new recipe: " . $e->getMessage(), 500);
        }
        return self::getRecipe($data['title'])->getFirstRow();
    }

    /**
     * @throws Exception
     */
    private static function validateRecipeData(array $data): void
    {
        $data = self::filterArray($data);
        if (!self::getRecipe($data['title'])->isEmpty()) throw new Exception("Recipe already exists", 400);
        $categories = Application::$app->db->execute("SELECT * FROM categories")->getColumn("name");
        $food_types = Application::$app->db->execute("SELECT * FROM food_types")->getColumn("name");
        if (!is_numeric($data['preparation_time'])) throw new Exception("Invalid preparation time '" . $data['preparation_time'] . "' , the preparation time must be a number", 400);
        if ($data['difficulty'] != "easy" && $data['difficulty'] != "medium" && $data['difficulty'] != "hard") throw new Exception("Invalid difficulty '" . $data['difficulty'] . "' , the difficulties allowed are : easy, medium, hard", 400);
        if (!in_array($data['category'], $categories)) throw new Exception("Invalid category '" . $data['category'] . "' , the categories allowed are : " . implode(", ", $categories), 400);
        if (!is_array($data['food_type'])) $data['food_type'] = [$data['food_type']]; //A revoir
        $percentage = 0;
        foreach ($data['food_type'] as $food_type) {
            $percentage += $food_type[1];
            if ($food_type[1] < 0 || $food_type[1] > 100) throw new Exception("Invalid percentage '" . $food_type[1] . "' , the percentage must be between 0 and 100", 400);
            if (!in_array($food_type[0], $food_types)) throw new Exception("Invalid food type '$food_type[0]', the food types allowed are : " . implode(", ", $food_types), 400);
        }
        if ($percentage != 100) throw new Exception("The sum of the percentages must be equal to 100", 400);
    }

    /**
     * @throws Exception
     */
    private static function insertRecipe(array $data, array $user): void
    {
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
    }

    /**
     * @throws Exception
     */
    private static function insertRecipeCategories(array $data): void
    {
        Application::$app->db->execute(
            "INSERT INTO recipe_categories (idRecipe, idCategory) VALUES ((SELECT idRecipe FROM recipes WHERE title = :title), (SELECT idCategory FROM categories WHERE name = :name))",
            [
                ":title" => $data['title'],
                ":name" => $data['category']
            ]
        );
    }

    /**
     * @throws Exception
     */
    private static function insertRecipeFoodType(array $data): void
    {
        $values = [];
        $parameters = [];
        foreach ($data['food_type'] as $index => $food_type) {
            $values[] = "((SELECT idRecipe FROM recipes WHERE title = :title), (SELECT idFoodType FROM food_types WHERE name = :name{$index}), :percentage{$index})";
            $parameters[":name{$index}"] = $food_type[0];
            $parameters[":percentage{$index}"] = $food_type[1];
        }
        $query = "INSERT INTO recipe_food_types (idRecipe, idFoodType, percentage) VALUES " . implode(", ", $values);
        $parameters[":title"] = $data['title'];

        Application::$app->db->execute($query, $parameters);

    }

    /**
     * @throws Exception
     */
    private static function insertRecipeImages(array $data): void
    {
        foreach ($data['image'] as $images) {
            $image = explode(",", $images);
            file_put_contents(Application::$UPLOAD_PATH . trim($image[0]), base64_decode(trim($image[1])));
            Application::$app->db->execute(
                "SELECT insert_unique_image_name(:path, (SELECT idRecipe FROM recipes WHERE title = :title));",
                [":path" => trim($image[0]), ":title" => $data['title']]
            );
        }
    }

    /**
     * Update an existing recipe.
     *
     * @param array $data
     * @return string
     * @throws Exception
     */
    public
    static function update(array $data): string
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
    public
    static function delete(array $data): string
    {
        if (self::getRecipe($data['title'])->getFirstRow()['idUser'] != self::getUserByToken()['idUser']) throw new Exception("Unauthorized", 401);
        if (self::getRecipe($data['title'])->isEmpty()) throw new Exception("Recipe not found", 404);
        try {
            //Call procedure
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
    private
    static function getRecipe(string $title): SqlResult
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
    private static function getAllRecipes(): array
    {
        $statement = Application::$app->db->execute("SELECT * FROM recipes_details");
        if ($statement->isEmpty()) throw new Exception("No recipes found", 404);
        return $statement->getValues();
    }


    /**
     * Search recipes based on the provided filters.
     *
     * @param $search
     * @return SqlResult
     * @throws Exception
     */
    private static function search($search): SqlResult
    {
        $date = DateTime::createFromFormat('Y-m-d', $search);
        $isDate = $date && $date->format('Y-m-d') === $search;
        $searchForText = "%{$search}%";
        $searchForDate = $isDate ? $search : '';
        $sql = "SELECT recipes_details.* FROM recipes_details 
            WHERE recipes_details.recipe_title LIKE :searchText 
            OR recipes_details.categories LIKE :searchText 
            OR DATE_FORMAT(recipes_details.created_at, '%Y-%m-%d') = :searchDate";
        return Application::$app->db->execute($sql, [':searchText' => $searchForText, ':searchDate' => $searchForDate]);
    }


    /**
     * Filter recipes by category or food type.
     *
     * @param null $category
     * @param null $foodType
     * @return SqlResult
     * @throws Exception
     */
    public static function filter($category = null, $foodType = null): SqlResult
    {
        $sql = "SELECT recipes_details.* FROM recipes_details ";
        $where = [];
        $params = [];
        if ($category !== null) {
            $where[] = 'FIND_IN_SET(:category, recipes_details.categories) > 0';
            $params[':category'] = $category;
        }
        if ($foodType !== null) {
            $where[] = 'FIND_IN_SET(:foodType, recipes_details.foodtypes_with_percentages) > 0';
            $params[':foodType'] = $foodType;
        }
        if (!empty($where)) {
            $sql .= 'WHERE ' . implode(' AND ', $where) . ' ';
        }
        try {
            return Application::$app->db->execute($sql, $params);
        } catch (Exception $e) {
            var_dump($e);
            throw new Exception("Error filtering recipes", 500);
        }
    }


    /**
     * Add a recipe to the user's history.
     *
     * @param array $data
     * @return string
     * @throws Exception
     */
    public
    static function addToHistory(array $data): string
    {
        $user = self::getUserByToken();
        if (!$user) throw new Exception("Unauthorized", 401);
        if (!is_numeric($data['idRecipe'])) throw new Exception("Recipe ID must be numeric", 400);
        $recipe = self::getRecipeById($data['idRecipe']);
        if (!$recipe) throw new Exception("Recipe not found", 404);
        try {
            Application::$app->db->execute("INSERT INTO consumed_recipes(idUser, idRecipe, consumption_date) VALUES(:idUser, :idRecipe, :consumption_date)", [
                ":idUser" => $user['idUser'],
                ":idRecipe" => $data['recipe_id'],
                ":consumption_date" => date("Y-m-d")
            ]);
        } catch (Exception $e) {
            throw new Exception("Error adding recipe to history", 500);
        }

        return "Recette ajoutée à l'historique avec succès";
    }

    /**
     * Add a recipe to the user's history.
     *
     * @param array $data
     * @return string
     * @throws Exception
     */
    public
    static function deleteHistory(array $data): string
    {
        $user = self::getUserByToken();
        if (!$user) throw new Exception("Unauthorized", 401);
        if (!is_numeric($data['idRecipe'])) throw new Exception("Recipe ID must be numeric", 400);
        if (isset($data['idRecipe'])) {
            $recipe = self::getRecipeById($data['idRecipe']);
            if (!$recipe) throw new Exception("Recipe not found", 404);
            Application::$app->db->execute("DELETE FROM consumed_recipes WHERE idUser = :idUser AND idRecipe = :idRecipe", [
                ":idUser" => $user['idUser'],
                ":idRecipe" => $data['idRecipe']
            ]);
            return "Recette supprimée de l'historique avec succès";
        }
        Application::$app->db->execute("DELETE FROM consumed_recipes WHERE idUser = :idUser", [
            ":idUser" => $user['idUser']
        ]);
        return "Historique supprimé avec succès";
    }

    /**
     * Retrieve a recipe by ID.
     *
     * @param int $idRecipe
     * @return array|null
     * @throws Exception
     */
    private static function getRecipeById(int $idRecipe): ?array
    {
        $query = "SELECT * FROM recipes_details WHERE recipe_id = :idRecipe";
        $statement = Application::$app->db->execute($query, [":idRecipe" => $idRecipe]);
        return $statement->isEmpty() ? null : $statement->getFirstRow();
    }


    /**
     * @throws Exception
     */
    public
    static function addFoodType(array $data)
    {
        $user = self::getUserByToken();
        if (!$user) throw new Exception("Unauthorized", 401);
        if (!Application::$app->db->execute("SELECT * FROM food_types WHERE name = :name", [":name" => $data['name']])->isEmpty()) throw new Exception("Food type already exists", 400);
        Application::$app->db->execute("INSERT INTO food_types(name) VALUES(:name)", [":name" => $data['name']]);
        return "Food type added successfully";
    }

    /**
     * @throws Exception
     */
    public
    static function getCategories(): ?array
    {
        $query = "SELECT * FROM categories";
        $statement = Application::$app->db->execute($query);
        return $statement->isEmpty() ? throw new Exception("No categories found", 404) : $statement->getValues();
    }

    /**
     * @throws Exception
     */
    public static function getFoodTypes()
    {
        $query = "SELECT * FROM food_types";
        $statement = Application::$app->db->execute($query);
        return $statement->isEmpty() ? throw new Exception("No food types found", 404) : $statement->getValues();
    }


}