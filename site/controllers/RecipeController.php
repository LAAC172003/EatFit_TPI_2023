<?php

namespace Eatfit\Site\Controllers;

use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Controller;
use Eatfit\Site\Core\Exception\ForbiddenException;
use Eatfit\Site\Core\Exception\NotFoundException;
use Eatfit\Site\Core\Middlewares\AuthMiddleware;
use Eatfit\Site\Core\Request;
use Eatfit\Site\Models\FoodType;
use Eatfit\Site\Models\Rating;
use Eatfit\Site\Models\Recipe;

class RecipeController extends Controller
{

    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['create', 'detail', 'update', 'delete', 'deleteRating', 'addFoodType', 'updateRating']));
    }

    /**
     * Affiche les détails d'une recette.
     *
     * @param Request $request La requête HTTP.
     * @return string Le contenu HTML de la page de détails de recette.
     */
    public function detail(Request $request): string
    {
        $ratings = new Rating();
        $model = new Recipe();
        $recipe = $model->getRecipe("idRecipe", $request->getRouteParams()['idRecipe'])->value;
        $ratings->idUser = Application::$app->user->idUser;
        $ratings->idRecipe = $request->getRouteParams()['idRecipe'];
        if ($request->isPost()) {
            if (!isset($_POST['score'])) {
                Application::$app->session->setFlash('error', 'Veuillez sélectionner une note');
                return $this->render(
                    'recipe_details', [
                    'recipe' => $recipe, 'ratings' => $ratings
                ]);
            }
            $ratings->score = $_POST['score'];
            if (!isset($_POST['comment'])) $ratings->comment = null;
            else $ratings->comment = $_POST['comment'];
            if ($ratings->validate()) {
                if ($ratings->create()->value == null) Application::$app->session->setFlash('error', $ratings->create()->message);
                else Application::$app->session->setFlash('success', 'Votre évaluation a été enregistrée');
                Application::$app->response->redirect('/recipe/detail/' . $request->getRouteParams()['idRecipe']);
            }
        }
        $recipe->image_paths = !empty($recipe->image_paths) && str_contains($recipe->image_paths, ',') ? array_map('trim', explode(',', $recipe->image_paths)) : array($recipe->image_paths);
        $recipe->categories = !empty($recipe->categories) && str_contains($recipe->categories, ',') ? array_map('trim', explode(',', $recipe->categories)) : array($recipe->categories);
        $recipe->foodtypes_with_percentages = !empty($recipe->foodtypes_with_percentages) && str_contains($recipe->foodtypes_with_percentages, ',') ? array_map('trim', explode(',', $recipe->foodtypes_with_percentages)) : array($recipe->foodtypes_with_percentages);
        return $this->render(
            'recipe_details', [
            'recipe' => $recipe, 'ratings' => $ratings
        ]);
    }

    /**
     * Crée une nouvelle recette.
     *
     * @param Request $request La requête HTTP.
     * @return string Le contenu HTML de la page de création de recette.
     */
    public function create(Request $request): string
    {
        $model = new Recipe();
        if (Application::$app->request->isPost()) {
            $model->image = $_FILES;
            $model->idUser = Application::$app->user->idUser;
            if (!isset($_POST['foodtype']) || !isset($_POST['percentage'])) Application::$app->session->setFlash('error', 'Veuillez sélectionner au moins un type de nourriture');
            else {
                $foodTypes = $_POST['foodtype'];
                $percentages = $_POST['percentage'];
                $totalPercentage = array_sum($percentages);
                if ($totalPercentage != 100) {
                    Application::$app->session->setFlash('error', 'La somme des pourcentages doit être de 100');
                    return $this->render('add_recipe', ['model' => $model]);
                }
                if (count($foodTypes) !== count(array_unique($foodTypes))) {
                    Application::$app->session->setFlash('error', 'Les types de nourriture ne peuvent pas se répéter');
                    return $this->render('add_recipe', ['model' => $model]);
                }
                foreach ($foodTypes as $key => $value) {
                    $model->foodType[] = [$value, $percentages[$key]];
                }

                $data = $request->getBody();
                $data = array_merge($data, [['image' => $model->image], ['idUser' => $model->idUser]], [['food_type' => $model->foodType]]);
                $model->categories = [$data['categories']];
                $model->loadData($data);

                if ($model->validate()) {
                    $apiResponse = $model->create();
//                    Application::$app->session->setFlash('success', 'Recipe added');
//                    Application::$app->response->redirect('/');
                }
            }
        }
        return $this->render('add_recipe', ['model' => $model]);
    }

    /**
     * Supprime une évaluation d'une recette.
     *
     * @param Request $request La requête HTTP.
     * @throws ForbiddenException
     */
    public function deleteRating(Request $request): void
    {
        $idRating = $request->getRouteParams()['idRating'];
        $model = new Rating();
        $model->idRating = $idRating;
        $rating = $model->getRatingById();
        $model->idRecipe = $rating->value[0]->idRecipe;
        if ($rating->value[0]->idUser != Application::$app->user->idUser) throw new ForbiddenException();
        $apiResponse = $model->delete();
        Application::$app->session->setFlash('success', $apiResponse->value);
        Application::$app->response->redirect('/recipe/detail/' . $model->idRecipe);
    }

    /**
     * Supprime une recette.
     *
     * @param Request $request La requête HTTP.
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function delete(Request $request): void
    {
        $model = new Recipe();
        $model->idRecipe = $request->getRouteParams()['idRecipe'];
        $recipe = $model->getRecipe("idRecipe", $request->getRouteParams()['idRecipe']);
        $model->idUser = $recipe->value->creator_id;
        if (!$recipe) throw new NotFoundException();
        if (Application::$app->user->idUser != $model->idUser) throw new ForbiddenException();
        $model->delete();
        Application::$app->session->setFlash('success', 'La recette a été supprimée');
        Application::$app->response->redirect('/');
    }

    /**
     * Met à jour une évaluation d'une recette.
     *
     * @param Request $request La requête HTTP.
     * @return string Le contenu HTML de la page de modification d'évaluation.
     * @throws ForbiddenException
     */
    public function updateRating(Request $request)
    {
        $idRating = $request->getRouteParams()['idRating'];
        $model = new Rating();
        $model->idRating = $idRating;
        $test = $model->getRatingById();

        $model->idRecipe = $test->value[0]->idRecipe;
        $model->score = $test->value[0]->score;
        $model->comment = $test->value[0]->comment;
        if ($test->value[0]->idUser != Application::$app->user->idUser) throw new ForbiddenException();
        if ($request->isPost()) {
            if (isset($_POST['score'])) $model->score = $_POST['score'];
            if (isset($_POST['comment'])) $model->comment = $_POST['comment'];
            $apiResponse = $model->update();
            if ($apiResponse->value == null) Application::$app->session->setFlash('error', $apiResponse->message);
            else Application::$app->session->setFlash('success', "Votre évaluation a été modifiée");
            Application::$app->response->redirect('/recipe/detail/' . $model->idRecipe);
        }
        return $this->render('edit_rating', ['model' => $model]);
    }

    /**
     * Met à jour une recette existante.
     *
     * @param Request $request La requête HTTP.
     * @return string Le contenu HTML de la page de modification de recette.
     * @throws ForbiddenException
     */
    public function update(Request $request): string
    {
        $model = new Recipe();
        $recipe = $model->getRecipe("idRecipe", $request->getRouteParams()['idRecipe'])->value;
        $recipe->image_paths = !empty($recipe->image_paths) && str_contains($recipe->image_paths, ',') ? array_map('trim', explode(',', $recipe->image_paths)) : array($recipe->image_paths);
        $recipe->categories = !empty($recipe->categories) && str_contains($recipe->categories, ',') ? array_map('trim', explode(',', $recipe->categories)) : array($recipe->categories);
        $recipe->foodtypes_with_percentages = !empty($recipe->foodtypes_with_percentages) && str_contains($recipe->foodtypes_with_percentages, ',') ? array_map('trim', explode(',', $recipe->foodtypes_with_percentages)) : array($recipe->foodtypes_with_percentages);

        $model->idRecipe = $recipe->recipe_id;
        $model->image = $recipe->image_paths;
        $model->idUser = $recipe->creator_id;
        $model->foodType = $recipe->foodtypes_with_percentages;
        $model->categories = $recipe->categories;
        $model->title = $recipe->recipe_title;
        $model->instructions = $recipe->recipe_instructions;
        $model->calories = $recipe->calories;
        $model->date = $recipe->created_at;
        $model->preparation_time = $recipe->preparation_time;
//        var_dump($model, $recipe);
        if ($model->idUser != Application::$app->user->idUser) throw new ForbiddenException();
        if (Application::$app->request->isPost()) {
            $data = $request->getBody();
            $model->loadData($data);
            var_dump($model);
//            if ($model->update()) {
//                Application::$app->session->setFlash('success', 'Recipe updated');
//                Application::$app->response->redirect('/recipe');
//            }
        }
        return $this->render('edit_recipe', [
            'model' => $model, 'recipe' => $recipe
        ]);
    }

    /**
     * Ajoute un nouveau type de nourriture.
     *
     * @param Request $request La requête HTTP.
     * @return string Le contenu HTML de la page d'ajout de type de nourriture.
     */
    public function addFoodType(Request $request)
    {
        $model = new FoodType();
        if ($request->isPost()) {
            $model->loadData($request->getBody());
            if ($model->validate()) {
                $apiResponse = $model->save();
                if ($apiResponse->value == null) Application::$app->session->setFlash('error', $apiResponse->message);
                else Application::$app->session->setFlash('success', "Votre évaluation a été modifiée");
                Application::$app->response->redirect('/');
            }
        }
        return $this->render('add_foodtype', ['model' => $model]);
    }
}