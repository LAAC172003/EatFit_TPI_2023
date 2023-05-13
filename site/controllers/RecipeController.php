<?php

namespace Eatfit\Site\Controllers;

use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Controller;
use Eatfit\Site\Core\Exception\ForbiddenException;
use Eatfit\Site\Core\Exception\NotFoundException;
use Eatfit\Site\Core\Middlewares\AuthMiddleware;
use Eatfit\Site\Core\Request;
use Eatfit\Site\Models\Recipe;

class RecipeController extends Controller
{

    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['create', 'read', 'update', 'delete']));
    }

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
                $model->loadData($data);
                var_dump($model->create());
                if ($model->validate() && $model->create()) {
                    var_dump($model->image);
//                    Application::$app->session->setFlash('success', 'Recipe added');
//                    Application::$app->response->redirect('/recipe');
                }
            }
        }
        return $this->render('add_recipe', ['model' => $model]);
    }


    public function read(Request $request)
    {
        $model = new Recipe();
        $recipe = $model->getRecipe("idRecipe", $request->getRouteParams()['idRecipe'])->value;
        $recipe->image_paths = !empty($recipe->image_paths) && str_contains($recipe->image_paths, ',') ? array_map('trim', explode(',', $recipe->image_paths)) : array($recipe->image_paths);
        $recipe->categories = !empty($recipe->categories) && str_contains($recipe->categories, ',') ? array_map('trim', explode(',', $recipe->categories)) : array($recipe->categories);
        $recipe->foodtypes_with_percentages = !empty($recipe->foodtypes_with_percentages) && str_contains($recipe->foodtypes_with_percentages, ',') ? array_map('trim', explode(',', $recipe->foodtypes_with_percentages)) : array($recipe->foodtypes_with_percentages);

        return $this->render('recipe_details', ['recipe' => $recipe]);
    }

    /**
     * @throws ForbiddenException
     */
    public function update(Request $request): string
    {
        $model = new Recipe();
        $recipe = $model->getRecipe("idRecipe", $request->getRouteParams()['idRecipe'])->value;
        if ($recipe->creator_id != Application::$app->user->idUser) throw new ForbiddenException();

        $recipe->image_paths = !empty($recipe->image_paths) && str_contains($recipe->image_paths, ',') ? array_map('trim', explode(',', $recipe->image_paths)) : array($recipe->image_paths);
        $recipe->categories = !empty($recipe->categories) && str_contains($recipe->categories, ',') ? array_map('trim', explode(',', $recipe->categories)) : array($recipe->categories);
        $recipe->foodtypes_with_percentages = !empty($recipe->foodtypes_with_percentages) && str_contains($recipe->foodtypes_with_percentages, ',') ? array_map('trim', explode(',', $recipe->foodtypes_with_percentages)) : array($recipe->foodtypes_with_percentages);

        if (Application::$app->request->isPost()) {
            $model->image = $_FILES;
            $model->idUser = Application::$app->user->idUser;
            $data = $request->getBody();
            $data = array_merge($data, [['image' => $model->image], ['idUser' => $model->idUser]]);
            $model->loadData($data);

//            if ($model->validate() && $model->update()) {
//                Application::$app->session->setFlash('success', 'Recipe updated');
//                Application::$app->response->redirect('/recipe');
//            }
        }
        return $this->render('edit_recipe', [
            'model' => $model, 'recipe' => $recipe
        ]);
    }

    /**
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function delete(Request $request)
    {
        $model = new Recipe();
        $recipe = $model->getRecipe("idRecipe", $request->getRouteParams()['idRecipe'])->value;
        if (!$recipe) throw new NotFoundException();
        if (Application::$app->user->idUser != $recipe->creator_id) throw new ForbiddenException();
        $model->idRecipe = $request->getRouteParams()['idRecipe'];
        $model->delete();
        Application::$app->session->setFlash('success', 'La recette a été supprimée');
        Application::$app->response->redirect('/');
    }
}