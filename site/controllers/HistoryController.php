<?php

namespace Eatfit\Site\Controllers;

use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Controller;
use Eatfit\Site\Core\Middlewares\AuthMiddleware;
use Eatfit\Site\Core\Request;
use Eatfit\Site\Models\History;

class HistoryController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['history', 'deleteHistory', 'addToHistory']));
    }

    /**
     * Affiche l'historique des recettes consultées par l'utilisateur.
     *
     * @return string Le contenu HTML de la page d'historique.
     */
    public function history(): string
    {
        $historyModel = new History();
        return $this->render('history', ['model' => $historyModel]);
    }

    /**
     * Supprime une recette de l'historique.
     *
     * @param Request $request La requête HTTP.
     */
    public function deleteHistory(Request $request): void
    {
        $historyModel = new History();
        $historyModel->idUser = Application::$app->user->idUser;
        if (isset($request->getRouteParams()['idConsumedRecipe'])) $historyModel->deleteHistory($request->getRouteParams()['idConsumedRecipe']);
        else $historyModel->deleteAllHistory();
        Application::$app->session->setFlash("success", "L'historique a bien été supprimé");
        Application::$app->response->redirect('/history');
    }

    /**
     * Ajoute une recette à l'historique.
     *
     * @param Request $request La requête HTTP.
     */
    public function addToHistory(Request $request): void
    {
        if (Application::isGuest()) {
            Application::$app->session->setFlash("error", "Vous devez être connecté pour accéder à cette page");
            Application::$app->response->redirect('/login');
        }
        $historyModel = new History();
        $historyModel->idRecipe = $request->getRouteParams()['idRecipe'];
        $historyModel->save();
        Application::$app->session->setFlash("success", "La recette a bien été ajoutée à l'historique");
        Application::$app->response->redirect('/recipe/detail/' . $request->getRouteParams()['idRecipe']);
    }
}