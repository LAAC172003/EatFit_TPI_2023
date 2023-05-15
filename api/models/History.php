<?php

namespace Eatfit\Api\Models;

use Eatfit\Api\Core\Application;
use Eatfit\Api\Core\Model;
use Exception;

class History extends Model
{
    /**
     *
     * Ajoute une recette à l'historique de l'utilisateur.
     * @param array $data Les données de la recette à ajouter à l'historique
     * @return string Message de succès
     * @throws Exception En cas d'erreur ou d'authentification échouée
     * @throws \Exception
     */
    public
    static function create(array $data): string
    {
        $user = self::getUserByToken();
        if (!$user) throw new Exception("Non autorisé", 401);
        if (!is_numeric($data['idRecipe'])) throw new Exception("L'ID de la recette doit être un nombre", 400);
        $recipe = Recipe::getRecipeById($data['idRecipe']);
        if (!$recipe) throw new Exception("Recette non trouvée", 404);
        try {
            Application::$app->db->execute("INSERT INTO consumed_recipes(idUser, idRecipe, consumption_date) VALUES(:idUser, :idRecipe, :consumption_date)", [
                ":idUser" => $user['idUser'],
                ":idRecipe" => $data['idRecipe'],
                ":consumption_date" => date("Y-m-d H:i:s")
            ]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'ajout de la recette à l'historique", 500);
        }

        return "Recette ajoutée à l'historique avec succès";
    }

    /**
     * Supprime une recette de l'historique de l'utilisateur.
     *
     * @param array $data Les données de la recette à supprimer de l'historique
     * @return string Message de succès
     * @throws Exception En cas d'erreur ou d'authentification échouée
     */
    public
    static function delete(array $data): string
    {
        $user = self::getUserByToken();
        if (!$user) throw new Exception("Non autorisé", 401);
        if (isset($data['idConsumedRecipe'])) {
            if (!is_numeric($data['idConsumedRecipe'])) throw new Exception("L'ID de l'historique doit être un nombre", 400);
            if (self::getHistory($data['idConsumedRecipe'])->isEmpty()) throw new Exception("Historique non trouvé", 404);
            Application::$app->db->execute("DELETE FROM consumed_recipes WHERE idUser = :idUser AND idConsumedRecipe = :idConsumedRecipe", [
                ":idUser" => $user['idUser'],
                ":idConsumedRecipe" => $data['idConsumedRecipe']
            ]);
            return "Recette supprimée de l'historique avec succès";
        }
        Application::$app->db->execute("DELETE FROM consumed_recipes WHERE idUser = :idUser", [
            ":idUser" => $user['idUser']
        ]);
        return "Historique supprimé avec succès";
    }

    private static function getHistory($idConsumedRecipe)
    {
        return Application::$app->db->execute("SELECT * FROM consumed_recipes WHERE idConsumedRecipe = :idConsumedRecipe", [
            ":idConsumedRecipe" => $idConsumedRecipe
        ]);
    }

    /**
     * Met à jour une recette dans l'historique de l'utilisateur.
     *
     * @param array $data Les données de la recette à mettre à jour dans l'historique
     * @return string Message de succès
     * @throws Exception En cas d'erreur ou d'authentification échouée
     */
    public static function update(array $data): string
    {
        $user = self::getUserByToken();
        if (!$user) throw new Exception("Non autorisé", 401);
        if (!is_numeric($data['idConsumedRecipe'])) throw new Exception("L'ID de l'historique doit être un nombre", 400);
        if (self::getHistory($data['idConsumedRecipe'])->isEmpty()) throw new Exception("Historique non trouvé", 404);
        if (!isset($data['consumption_date'])) throw new Exception("La date de consommation doit être renseignée", 400);
        if (!strtotime($data['consumption_date'])) throw new Exception("La date de consommation doit être au format YYYY-MM-DD HH:MM:SS", 400);
        try {
            Application::$app->db->execute("UPDATE consumed_recipes SET consumption_date = :consumption_date WHERE idUser = :idUser AND idConsumedRecipe = :idConsumedRecipe", [
                ":idUser" => $user['idUser'],
                ":idConsumedRecipe" => $data['idConsumedRecipe'],
                ":consumption_date" => date("Y-m-d H:i:s")
            ]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la mise à jour de l'historique", 500);
        }

        return "Historique mis à jour avec succès";
    }

    /**
     * Lit l'historique de l'utilisateur.
     *
     * @return array L'historique de l'utilisateur
     * @throws Exception En cas d'erreur ou d'authentification échouée
     */
    public static function read($data): array
    {
        $user = self::getUserByToken();
        if (!$user) throw new Exception("Non autorisé", 401);
        $history = null;
        $data = self::filterArray($data);
        if (isset($data['idRecipe'])) {
            if (!is_numeric($data['idRecipe'])) throw new Exception("L'ID de la recette doit être un nombre", 400);
            $history = Application::$app->db->execute("SELECT consumed_recipes.*, recipes.title,recipes.created_at FROM consumed_recipes JOIN recipes ON consumed_recipes.idRecipe = recipes.id WHERE consumed_recipes.idUser = :idUser AND consumed_recipes.idRecipe = :idRecipe", [
                ":idUser" => $user['idUser'],
                ":idRecipe" => $data['idRecipe']
            ]);
        } else {
            $history = Application::$app->db->execute("SELECT consumed_recipes.*, recipes.title,recipes.created_at FROM consumed_recipes JOIN recipes ON consumed_recipes.idRecipe = recipes.idRecipe WHERE consumed_recipes.idUser = :idUser ORDER BY consumed_recipes.consumption_date DESC", [":idUser" => $user['idUser']]);
        }
        if ($history->isEmpty()) throw new Exception("Aucun historique trouvé", 404);
        return $history->getValues();
    }
}