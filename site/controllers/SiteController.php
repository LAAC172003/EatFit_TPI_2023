<?php

namespace Eatfit\Site\Controllers;

use Eatfit\Site\Core\Controller;
use Eatfit\Site\Core\Middlewares\AuthMiddleware;
use Eatfit\Site\Models\Recipe;

class SiteController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['profile']));
    }

    public function home(): string
    {
        $recipeModel = new Recipe();
//        var_dump($recipeModel->getRecipeByFilter("category", "Déjeuner"));
        return $this->render('home', [
            'name' => 'Lucas Almeida Costa', 'model' => $recipeModel
        ]);
    }
}
