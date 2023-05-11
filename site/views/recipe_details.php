<?php
/** @var $recipe */

use Eatfit\Site\Core\Application;
use Eatfit\Site\Models\Recipe;

var_dump($recipe);


?>

<div class="recipe-detail">
    <?php
    foreach ($recipe->image_paths as $image) {
        ?>
        <img src="<?= Application::$API_URL . "uploads/" . $image ?>" alt="Image de la recette"
             class="img-fluid mb-4" id="recipe-image">
        <?php
    }

    ?>
    <!--    <img src="https://picsum.photos/1200/600" alt="Image de la recette" class="img-fluid mb-4" id="recipe-image">-->
    <h2 class="recipe-title" id="recipe-title"><?= $recipe->recipe_title ?></h2>
    <p id="recipe-author">Recette numéro : <?= $recipe->recipe_id ?></p>
    <p id="recipe-author">Créé par : <?= $recipe->creator_username ?></p>
    <p> Catégories : </p>
    <ul>
        <?php
        foreach ($recipe->categories as $category) {
            ?>
            <li><?= $category ?></li>
            <?php
        }
        ?>
    </ul>


    <p id="recipe-difficulty">Difficulté : <?= $recipe->difficulty ?></p>
    <p id="recipe-calories">Calories : <?= $recipe->calories ?></p>
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



