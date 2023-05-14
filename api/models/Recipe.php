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
     * Récupère une recette ou toutes les recettes en fonction des données fournies.
     * Si aucun titre de recette spécifique n'est fourni dans les filtres de recherche, toutes les recettes sont renvoyées.
     * Sinon, elle renvoie la recette spécifique correspondant au titre.
     * Lance une exception si aucune recette ne correspond au titre.
     *
     * @param array $data Les données de recherche
     * @return array|null Les recettes correspondantes
     * @throws Exception En cas d'erreur ou de recette non trouvée
     */
    public static function read(array $data): ?array
    {
        if (isset($data['idRecipe'])) {
            $recipe = self::getRecipeById($data['idRecipe']);
            if ($recipe == null) throw new Exception("Recette non trouvée", 404);
            return $recipe;
        }
        if (!isset($data['search'])) {
            if (isset($data['filter'])) {
                foreach ($data['filter'] as $filter => $value) {
                    if ($filter == 'category') return self::filter($value)->getValues();
                    if ($filter == 'food_type') return self::filter(null, $value)->getValues();
                }
            }
            return self::getAllRecipes();
        }

        try {
            $recipe = self::search($data['search']);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la recherche de recette : " . $e->getMessage(), 500);
        }
        if ($recipe->isEmpty()) throw new Exception("Recette non trouvée", 404);
        return $recipe->getValues();
    }

    /**
     * Crée une nouvelle recette.
     *
     * @param array $data Les données de la recette
     * @return array|Exception Les informations de la recette créée ou une exception en cas d'erreur
     * @throws Exception En cas d'erreur ou d'authentification échouée
     */
    public static function create(array $data): array|Exception
    {
        $user = self::getUserByToken();
        if (!$user) throw new Exception("Non autorisé", 401);
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
            throw new Exception("Erreur lors de la création de la recette : " . $e->getMessage(), 500);
        }
        return self::getRecipe($data['title'])->getFirstRow();
    }

    /**
     * Valide les données de la recette.
     *
     * @param array $data Les données de la recette
     * @throws Exception En cas de données invalides ou en cas d'erreur
     */
    private static function validateRecipeData(array $data): void
    {
        $data = self::filterArray($data);
        if (!self::getRecipe($data['title'])->isEmpty()) throw new Exception("La recette existe déjà", 400);
        $categories = Application::$app->db->execute("SELECT * FROM categories")->getColumn("name");
        $food_types = Application::$app->db->execute("SELECT * FROM food_types")->getColumn("name");
        $difficulties = ["facile", "moyen", "difficile"];
        if (!is_numeric($data['preparation_time'])) throw new Exception("Temps de préparation invalide : '" . $data['preparation_time'] . "'. Le temps de préparation doit être un nombre", 400);
        if (!in_array($data['difficulty'], $difficulties)) throw new Exception("Difficulté invalide : '" . $data['difficulty'] . "'. Les difficultés autorisées sont : facile, moyen, difficile", 400);
        if (!in_array($data['category'], $categories)) throw new Exception("Catégorie invalide : '" . $data['category'] . "'. Les catégories autorisées sont : " . implode(", ", $categories), 400);
        if (!is_array($data['food_type'])) $data['food_type'] = [$data['food_type']]; //A revoir
        $percentage = 0;
        foreach ($data['food_type'] as $food_type) {
            $percentage += $food_type[1];
            if ($food_type[1] < 0 || $food_type[1] > 100) throw new Exception("Pourcentage invalide : '" . $food_type[1] . "'. Le pourcentage doit être compris entre 0 et 100", 400);
            if (!in_array($food_type[0], $food_types)) throw new Exception("Type d'aliment invalide : '$food_type[0]'. Les types d'aliments autorisés sont : " . implode(", ", $food_types), 400);
        }
        if ($percentage != 100) throw new Exception("La somme des pourcentages doit être égale à 100", 400);
    }

    /**
     * Insère une recette dans la base de données.
     *
     * @param array $data Les données de la recette
     * @param array $user L'utilisateur
     * @throws Exception En cas d'erreur lors de l'insertion de la recette
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
     * Insère les catégories de la recette dans la base de données.
     *
     * @param array $data Les données de la recette
     * @throws Exception En cas d'erreur lors de l'insertion des catégories
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
     * Insère les types d'aliments de la recette dans la base de données.
     *
     * @param array $data Les données de la recette
     * @throws Exception En cas d'erreur lors de l'insertion des types d'aliments
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
     * Insère les images de la recette dans la base de données.
     *
     * @param array $data Les données de la recette
     * @throws Exception En cas d'erreur lors de l'insertion des images
     */
    private static function insertRecipeImages(array $data): void
    {
        if ($data['image'] == "") {
            $image = Application::$app->db->execute(
                "SELECT image_path FROM categories WHERE name = :name",
                [":name" => $data['category']])->getFirstRow()["image_path"];
            Application::$app->db->execute(
                "SELECT insert_unique_image_name(:path, (SELECT idRecipe FROM recipes WHERE title = :title));",
                [":path" => $image, ":title" => $data['title']]
            );
        } else {
            foreach ($data['image'] as $images) {
                $image = explode(",", $images);
                file_put_contents(Application::$UPLOAD_PATH . trim($image[0]), base64_decode(trim($image[1])));
                Application::$app->db->execute(
                    "SELECT insert_unique_image_name(:path, (SELECT idRecipe FROM recipes WHERE title = :title));",
                    [":path" => trim($image[0]), ":title" => $data['title']]
                );
            }
        }
    }

    /**
     * Met à jour une recette existante.
     *
     * @param array $data Les données de mise à jour
     * @return string Message de succès
     * @throws Exception En cas d'erreur ou d'authentification échouée
     */
    public
    static function update(array $data): string
    {
        $data = self::filterArray($data);
        if (!isset($data['idRecipe'])) throw new Exception("L'idRecipe n'a pas été renseigné", 400);
        $recipeResult = self::getRecipeById($data['idRecipe']);
        if ($recipeResult == null) throw new Exception("Recette non trouvée", 404);
        if ($recipeResult['creator_id'] != self::getUserByToken()['idUser']) throw new Exception("Non autorisé", 401);

        $updates = [];
        $difficulties = ["facile", "moyen", "difficile"];
        if (!in_array($data['difficulty'], $difficulties)) throw new Exception("Difficulté invalide : '" . $data['difficulty'] . "'. Les difficultés autorisées sont : facile, moyen, difficile", 400);
        foreach ($data as $key => $value) {
            if ($key == "idRecipe") continue;
            if (isset($value) && $value != "") $updates[$key] = $value;
        }
        $sb = "Recette mise à jour avec succès :";
        try {
            $query = "UPDATE recipes SET " . implode(", ", array_map(fn($key) => "$key = :$key", array_keys($updates))) . " WHERE idRecipe = :idRecipe";
            Application::$app->db->execute($query, array_merge($updates, [":idRecipe" => $data['idRecipe']]));
            $sb .= implode(", ", array_map(fn($key, $value) => "$key = $value", array_keys($updates), array_values($updates)));
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la mise à jour de la recette", 500);
        }
        return $sb;
    }

    /**
     * Supprime une recette.
     *
     * @param array $data Les données de la recette à supprimer
     * @return string Message de succès
     * @throws Exception En cas d'erreur ou d'authentification échouée
     */
    public static function delete(array $data): string
    {
        $recipe = self::getRecipeById($data['idRecipe']);
        if ($recipe == null) throw new Exception("Recette non trouvée", 404);
        if ($recipe['creator_id'] != self::getUserByToken()['idUser']) throw new Exception("Non autorisé", 401);
        try {
            //Call procedure
            Application::$app->db->execute("DELETE FROM recipes WHERE idRecipe= :idRecipe", [":idRecipe" => $data['idRecipe']]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la suppression de la recette", 500);
        }
        // Retournez un message de succès
        return "Recette supprimée avec succès";
    }

    /**
     * Récupère une recette par son titre.
     *
     * @param string $title Le titre de la recette
     * @return SqlResult Les résultats de la requête
     * @throws Exception En cas d'erreur
     */
    private
    static function getRecipe(string $title): SqlResult
    {
        $query = "SELECT * FROM recipes WHERE title = :title";
        return Application::$app->db->execute($query, [":title" => $title]);
    }

    /**
     * Récupère toutes les recettes.
     *
     * @return array Les recettes
     * @throws Exception En cas d'erreur ou si aucune recette n'est trouvée
     */
    private static function getAllRecipes(): array
    {
        $statement = Application::$app->db->execute("SELECT * FROM recipes_details");
        if ($statement->isEmpty()) throw new Exception("No recipes found", 404);
        return $statement->getValues();
    }


    /**
     * Recherche des recettes en fonction des filtres fournis.
     *
     * @param string $search La valeur de recherche
     * @return SqlResult Les résultats de la recherche
     * @throws Exception En cas d'erreur
     */
    private static function search(string $search): SqlResult
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
     *
     * Filtre les recettes par catégorie ou type d'aliment.
     * @param null|string $category La catégorie de recettes à filtrer
     * @param null|string $foodType Le type d'aliment à filtrer
     * @return SqlResult Les résultats du filtrage
     * @throws Exception En cas d'erreur
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
            throw new Exception("Erreur lors du filtrage des recettes", 500);
        }
    }


    /**
     * Récupère une recette par son ID.
     *
     * @param int $idRecipe L'ID de la recette
     * @return array|null Les données de la recette ou null si non trouvée
     * @throws Exception En cas d'erreur
     */
    public static function getRecipeById(int $idRecipe): ?array
    {
        $query = "SELECT * FROM recipes_details WHERE recipe_id = :idRecipe";
        $statement = Application::$app->db->execute($query, [":idRecipe" => $idRecipe]);
        return $statement->isEmpty() ? null : $statement->getFirstRow();
    }


    /**
     * Ajoute un type d'aliment.
     *
     * @param array $data Les données du type d'aliment
     * @return string Message de succès
     * @throws Exception En cas d'erreur ou d'authentification échouée
     */
    public
    static function addFoodType(array $data)
    {
        $user = self::getUserByToken();
        if (!$user) throw new Exception("Unauthorized", 401);
        if (!Application::$app->db->execute("SELECT * FROM food_types WHERE name = :name", [":name" => $data['name']])->isEmpty()) throw new Exception("Le type d'aliment existe déjà", 400);
        Application::$app->db->execute("INSERT INTO food_types(name) VALUES(:name)", [":name" => $data['name']]);
        return "Type d'aliment ajouté avec succès";
    }

    /**
     * Récupère les catégories de recettes.
     *
     * @return array|null Les catégories de recettes ou null si non trouvées
     * @throws Exception En cas d'erreur
     */
    public
    static function getCategories(): ?array
    {
        $query = "SELECT * FROM categories";
        $statement = Application::$app->db->execute($query);
        return $statement->isEmpty() ? throw new Exception("Aucune catégorie trouvée", 404) : $statement->getValues();
    }

    /**
     * Récupère les types d'aliments.
     *
     * @return array|null Les types d'aliments ou null si non trouvés
     * @throws Exception En cas d'erreur
     */
    public static function getFoodTypes()
    {
        $query = "SELECT * FROM food_types";
        $statement = Application::$app->db->execute($query);
        return $statement->isEmpty() ? throw new Exception("Aucun type d'aliment trouvé", 404) : $statement->getValues();
    }
}