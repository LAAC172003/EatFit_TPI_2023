<?php
/** @var $recipe */
/** @var $ratings Rating */

/** @var $this View */

use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\Form\Form;
use Eatfit\Site\Core\View;
use Eatfit\Site\Models\Rating;

$this->title = "Détails de la recette";

$comments = $ratings->getRatingByIdRecipe()->value;
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
                             style="width:600px; height:auto;">
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
    <!--    <img src="https://picsum.photos/1200/600" alt="Image de la recette" class="img-fluid mb-4" id="recipe-image">-->
    <h2 class="recipe-title" id="recipe-title"><?= $recipe->recipe_title ?></h2>
    <p id="recipe-author">Recette numéro : <?= $recipe->recipe_id ?></p>
    <p id="recipe-author">Créé par : <?= $recipe->creator_username ?></p>
    <p> Catégories : <?= implode(",", $recipe->categories) ?></p>
    <p id="recipe-difficulty">Difficulté : <?= $recipe->difficulty ?></p>
    <p id="recipe-calories">Calories : <?= $recipe->calories ?></p>
    <p id="recipe-calories">Note moyenne : <?= round($recipe->average_rating, 1) ?></p>
    <div class="row">
        <div class="col-md-12">
            <h3 class="instructions-title">Instructions</h3>
            <p> <?= $recipe->recipe_instructions ?> </p>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-6">
            <h3 class="prep-time-title">Temps de préparation</h3>
            <p class="prep-time" id="prep-time"><?= $recipe->preparation_time ?> minutes</p>
        </div>
        <div class="col-md-6">
            <h3 class="creation-date-title">Date de création</h3>
            <p class="creation-date" id="creation-date"><?= date('d-m-Y', strtotime($recipe->created_at)) ?></p>
        </div>
    </div>


    <center>
        <canvas id="myChart" width="400" height="400"></canvas>
    </center>
    <div class="consume-recipe-button-container">
        <a href="/addToHistory/<?= $recipe->recipe_id ?>">
            <button name="history" class="consume-recipe-button">Consommer cette recette</button>
        </a>
        <?php
        if (Application::$app->user->idUser == $recipe->creator_id) {
            ?>
            <a href="/recipe/edit/<?= $recipe->recipe_id ?>">
                <button class="consume-recipe-button">Modifier</button>
            </a>
            <a href="/recipe/delete/<?= $recipe->recipe_id ?>">
                <button class="consume-recipe-button">Supprimer</button>
            </a>
            <?php
        }
        ?>
    </div>
    <div class="mt-5">
        <h3 class="mb-4 ">Commentaires</h3>
        <?php if (!$comments) : ?>
            <div class="alert background-color-dark font-color-light" role="alert">
                Aucun commentaire pour cette recette pour le moment. Soyez le premier à commenter !
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
                        <p class="card-text font-color-main">
                            <?php
                            if ($comment->comment == null) echo "Aucun commentaire";
                            else echo $comment->comment;
                            ?>

                            <?php
                            if (Application::$app->user->idUser == $comment->idUser) {
                            ?>
                        <div class="d-flex justify-content-end">
                            <a href="/rating/update/<?= $comment->idRating ?>"
                               class="btn btn-primary-custom btn-sm mr-2">
                                Modifier
                            </a>
                            <a href="/rating/delete/<?= $comment->idRating ?>"
                               class="btn btn-danger-custom btn-sm">
                                Supprimer
                            </a>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php Form::end() ?>
        <?php endif; ?>
    </div>


    <div class="mt-4">
        <h3>Ajouter un commentaire</h3>
        <?php $form = Form::begin("", "post") ?>
        <div class="form-group">
            <?= $form->field($ratings, 'score')->numberField(); ?>
        </div>
        <div class="form-group">
            <?= $form->field($ratings, 'comment')->textarea(); ?>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Envoyer</button>
        <?php Form::end() ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function generateRandomColorAround(rgb = [146, 76, 22]) {
            var variation = 60;
            var r = Math.max(Math.min(Math.round(rgb[0] + (Math.random() - 0.5) * 2 * variation), 255), 0);
            var g = Math.max(Math.min(Math.round(rgb[1] + (Math.random() - 0.5) * 2 * variation), 255), 0);
            var b = Math.max(Math.min(Math.round(rgb[2] + (Math.random() - 0.5) * 2 * variation), 255), 0);
            return 'rgb(' + r + ', ' + g + ', ' + b + ')';
        }

        <?php
        $noms = [];
        $pourcentages = [];

        foreach ($recipe->foodtypes_with_percentages as $foodtype_with_percentage) {
            preg_match('/^(.*?)\s+\((\d+)%\)$/', $foodtype_with_percentage, $matches);
            $noms[] = $matches[1]; // Ajoute le nom du type d'aliment au tableau des noms
            $pourcentages[] = (int)$matches[2]; // Ajoute le pourcentage au tableau des pourcentages
        }
        ?>

        var foodTypes = <?php echo json_encode($recipe->foodtypes_with_percentages); ?>;
        var backgroundColors = [];
        var borderColors = [];

        foodTypes.forEach(function (foodType) {
            var color = generateRandomColorAround();
            backgroundColors.push(color);
            borderColors.push(color);
        });

        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: foodTypes.map(function (foodType) {
                    return foodType;
                }),
                datasets: [{
                    data: [<?php echo implode(", ", $pourcentages); ?>],
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: false,
            }
        });
    </script>

</div>



