<?php
/** @var $recipe */

/** @var $ratings Rating */

use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Form\Form;
use Eatfit\Site\Models\Rating;

$this->title = "Détails de la recette";
$comments = $ratings->getRatingByIdRecipe(false)->value;
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="/css/style.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<div class="recipe-detail">
    <div class="container">
        <div id="myCarousel" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                <?php foreach ($recipe->image_paths as $index => $image): ?>
                    <li data-target="#myCarousel"
                        data-slide-to="<?= $index ?>" <?= $index === 0 ? 'class="active"' : '' ?>></li>
                <?php endforeach; ?>
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner">
                <?php foreach ($recipe->image_paths as $index => $image): ?>
                    <?php if (str_contains($image, 'default')) $image = explode('_', $image)[1]; ?>
                    <div class="item <?= $index === 0 ? 'active' : '' ?>">
                        <img src="<?= Application::$API_URL . "uploads/" . $image ?>" alt="Image de la recette"
                             style="width:100%; height:450px;">
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Left and right controls -->
            <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#myCarousel" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
    <h2 class="recipe-title" id="recipe-title"><?= $recipe->recipe_title ?></h2>
    <p id="recipe-author">Créé par : <?= $recipe->creator_username ?></p>
    <p> Catégories : <?= implode(",", $recipe->categories) ?></p>
    <p id="recipe-difficulty">Difficulté : <?= $recipe->difficulty ?></p>
    <p id="recipe-calories">Note moyenne : <?= round($recipe->average_rating, 1) ?></p>
    <p id="recipe-calories">Temps de préparation : <?= $recipe->preparation_time ?> minutes</p>

    <div class="mt-5">
        <h3 class="mb-4 ">Commentaires</h3>
        <?php if (!$comments) : ?>
            <div class="alert background-color-dark font-color-light" role="alert">
                Aucun commentaire pour cette recette pour le moment. Soyez le premier à commenter ! - <a
                    href="/register"> Vous inscrire ?</a> / <a href="/login"> Vous connecter ?</a>

            </div>
        <?php else: ?>
            <?php $form = Form::begin("", "post") ?>
            <?php foreach ($comments as $comment): ?>
                <div class="card mb-3 border-dark">
                    <div class="card-header background-color-dark">
                        <div class="row align-items-center">
                            <div class="col">
                                <strong class="font-color-highlight"><?= $comment->username ?></strong>
                            </div>
                            <div class="col text-end">
                                <span class="badge bg-primary-custom"><?= $comment->score ?> / 5</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body background-color-light">
                        <p class="card-text font-color-main">      <?php
                            if ($comment->comment == null) echo "Aucun commentaire";
                            else echo $comment->comment;
                            ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php Form::end() ?>
        <?php
        endif; ?>
    </div>
</div>
