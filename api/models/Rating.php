<?php

namespace Eatfit\Api\Models;

use Eatfit\Api\Core\Application;
use Eatfit\Api\Core\Db\SqlResult;
use Eatfit\Api\Core\Model;
use Exception;

class Rating extends Model
{
    /**
     * Lit toutes les évaluations de la base de données pour une recette spécifiée.
     *
     * @param array $data Les données de la requête.
     * @return array La liste de toutes les évaluations pour la recette spécifiée.
     * @throws Exception Si une erreur se produit lors de l'exécution de la requête.
     */
    public static function read($data): array
    {
        if (isset($data['idRecipe'])) {
            $query = "SELECT idRating,ratings.idUser,score,comment,users.username FROM `ratings` JOIN users ON ratings.idUser = users.idUser ";
            $query .= " WHERE idRecipe = :idRecipe";
            $params = [":idRecipe" => $data['idRecipe']];
            $result = Application::$app->db->execute($query, $params);
            if ($result->isEmpty()) throw new Exception("Aucune évaluation trouvée", 404);
            return $result->getValues();
        }
        if (!isset($data['idRating'])) throw new Exception("L'ID de l'évaluation est requis", 400);
        $rating = self::getRatingById($data['idRating']);
        if ($rating->isEmpty()) throw new Exception("Aucune évaluation trouvée", 404);
        return $rating->getValues();
    }

    /**
     * Méthode pour obtenir une évaluation spécifique par son identifiant.
     *
     * @param $idRating L'identifiant de l'évaluation à obtenir.
     * @return SqlResult Le résultat de la requête SQL.
     * @throws Exception Si une erreur se produit lors de l'exécution de la requête.
     */
    public static function getRatingById($idRating): SqlResult
    {
        return Application::$app->db->execute("SELECT * FROM ratings WHERE idRating = :idRating", [":idRating" => $idRating]);
    }

    /**
     * Crée une nouvelle évaluation pour une recette spécifiée.
     *
     * @param array $data Les données de l'évaluation à créer.
     * @return array Les informations sur l'évaluation créée.
     * @throws Exception Si une erreur se produit lors de la validation des données ou de l'exécution de la requête.
     */
    public static function create(array $data): array
    {
        $query = "INSERT INTO ratings (score, comment, idUser, idRecipe) VALUES (:score, :comment, :idUser, :idRecipe)";
        $data = self::filterArray($data);
        $user = self::getUserByToken();
        if (!isset($data['score'])) throw new Exception("Le score est requis", 400);
        if (!is_numeric($data['score'])) throw new Exception("Le score doit être un nombre", 400);
        if ($data['score'] < 1 || $data['score'] > 5) throw new Exception("Le score doit être entre 1 et 5", 400);
        if (!isset($data['idRecipe'])) throw new Exception("L'ID de la recette est requis", 400);
        if (!is_numeric($data['idRecipe'])) throw new Exception("L'ID de la recette doit être un nombre", 400);
        $data['idUser'] = $user['idUser'];
        if (isset($data['comment']) && $data['comment'] == "") $data['comment'] = null;
        try {
            Application::$app->db->execute($query, [
                ":score" => $data['score'],
                ":comment" => $data['comment'],
                ":idUser" => $data['idUser'],
                ":idRecipe" => $data['idRecipe']
            ]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la création de l'évaluation", 500);
        }
        return Application::$app->db->execute("SELECT * FROM ratings WHERE idRating = :idRating", [":idRating" => Application::$app->db->getLastInsertId()])->getFirstRow();
    }

    /**
     * Méthode pour mettre à jour une évaluation existante.
     *
     * @param array $data Les données de l'évaluation à mettre à jour.
     * @return string Un message indiquant que l'évaluation a été mise à jour avec succès.
     * @throws Exception Si une erreur se produit lors de la validation des données ou de l'exécution de la requête.
     */
    public static function update(array $data): string
    {
        $data = self::filterArray($data);
        if (!isset($data['idRating'])) throw new Exception("L'ID de l'évaluation est requis", 400);
        elseif (!is_numeric($data['idRating'])) throw new Exception("L'ID de l'évaluation doit être un nombre", 400);
        $rating = self::getRatingById($data['idRating']);
        if ($rating->isEmpty()) throw new Exception("Évaluation introuvable", 404);
        if ($rating->getFirstRow()['idUser'] != self::getUserByToken()['idUser']) throw new Exception("Vous ne pouvez pas mettre à jour cette évaluation", 403);
        if (isset($data['score'])) if ($data['score'] < 1 || $data['score'] > 5) throw new Exception("Le score doit être entre 1 et 5", 400);
        $updates = [];
        foreach ($data as $key => $value) {
            if ($key == "idRating") continue;
            if (isset($value) && $value != "") {
                $updates[$key] = $value;
            }
        }
        $sb = "L'évaluation a été mise à jour avec succès :";
        try {
            $query = "UPDATE ratings SET " . implode(", ", array_map(fn($key) => "$key = :$key", array_keys($updates))) . " WHERE idRating = :idRating";
            Application::$app->db->execute($query, array_merge($updates, [":idRating" => $data['idRating']]));
            $sb .= implode(", ", array_map(fn($key, $value) => "$key = $value", array_keys($updates), array_values($updates)));
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la mise à jour de l'évaluation", 500);
        }
        return $sb;
    }

    /**
     * Méthode pour supprimer une évaluation existante.
     *
     * @param array $data Les données de l'évaluation à supprimer.
     * @return string Un message indiquant que l'évaluation a été supprimée avec succès.
     * @throws Exception Si une erreur se produit lors de la validation des données ou de l'exécution de la requête.
     */
    public static function delete(array $data): string
    {
        $data = self::filterArray($data);
        if (!isset($data['idRating'])) throw new Exception("L'ID de l'évaluation est requis", 400);
        elseif (!is_numeric($data['idRating'])) throw new Exception("L'ID de l'évaluation doit être un nombre", 400);
        $rating = self::getRatingById($data['idRating']);
        if ($rating->isEmpty()) throw new Exception("Évaluation introuvable", 404);
        if ($rating->getFirstRow()['idUser'] != self::getUserByToken()['idUser']) throw new Exception("Vous ne pouvez pas supprimer cette évaluation", 403);
        try {
            Application::$app->db->execute("DELETE FROM ratings WHERE idRating = :idRating", [":idRating" => $data['idRating']]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la suppression de l'évaluation", 500);
        }
        return "Évaluation supprimée avec succès";
    }
}

