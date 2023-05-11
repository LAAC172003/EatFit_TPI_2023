<?php
/** @var $model Recipe */

use Eatfit\Site\Models\Recipe;

$j = 0;
foreach ($model->getCategories()->value as $category) {
    var_dump($category);
    $recipes = $model->getRecipeByFilter("category", $category->name)->value;
    var_dump($recipes);
    ?>
    <div class="carousel-container">
        <h1><?= $category->name ?></h1>
        <div class="inner-carousel">
            <div class="track" id="track<?= $j ?>">
                <?php
                foreach ($recipes as $recipe) {
                    ?>
                    <div class="card-container">

                        <div class="container">
                            <div class="row">
                                <div class="menu-item">
                                    <a href="/detail/<?= $recipe->recipe_id ?>"><img
                                            src="https://www.mutuellebleue.fr/app/uploads/sites/2/2020/07/petit-dejeuner-complet.jpg"
                                            alt="<?= $recipe->recipe_id ?>" class="rounded"></a>
                                    <img src="img/like%20(1).png" alt="<?= $recipe->recipe_title ?>" class="rounded"
                                         id="rounded">
                                    <div class="ouioui">
                                        <div class="nonnon">
                                            <p class="bulle"><?= "test" ?></p>
                                            <p class="bulle"><?= "test" ?></p>
                                            <p class="bulle"><?= "test" ?></p>
                                        </div>
                                        <h3 style="text-align: left;"><?= $recipe->recipe_title ?></h3>
                                        <p style="text-align: left;"><?= $recipe->recipe_instructions ?>
                                            <?php
                                            if (strlen($recipe->recipe_instructions) > 30) {
                                                echo "...";
                                            } else {
                                                echo $recipe->recipe_instructions;
                                            }
                                            ?></p>

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    <?php
                }
                ?>

            </div>
            <div class="nav" <?php if (count($recipes) < 5) echo 'style="display: none;"' ?>>
                <button class="prev" data-index="" id="prev<?= $j ?>" onclick="Prev(<?= $j ?>)"><i
                        class="fas fa-arrow-left fa-2x"></i></button>
                <button class="next" data-index="0" id="next<?= $j ?>" onclick="Next(<?= $j ?>)"><i
                        class="fas fa-arrow-right fa-2x"></i></button>
            </div>
        </div>
    </div>
    <?php
    $j++;
}