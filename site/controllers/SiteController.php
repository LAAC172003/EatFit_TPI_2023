<?php

namespace Eatfit\Site\Controllers;

use Eatfit\Site\Core\Controller;
use Eatfit\Site\Models\Recipe;

class SiteController extends Controller
{
    /**
     * Affiche la page d'accueil.
     *
     * @return string Le contenu HTML de la page d'accueil.
     */
    public function home(): string
    {
        $recipeModel = new Recipe();
        return $this->render('home', [
            'model' => $recipeModel
        ]);
    }
}
