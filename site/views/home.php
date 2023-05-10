<?php
/** @var $products  array */

$j = 0;
foreach ($products as $key => $value) {
    ?>
    <div class="carousel-container">
        <h1><?= $value["categorie"] ?></h1>
        <div class="inner-carousel">
            <div class="track" id="track<?= $j ?>">
                <?php
                for ($i = 0; $i < 10; $i++) {
                    ?>
                    <div class="card-container">

                        <div class="container">
                            <div class="row">
                                <div class="menu-item">
                                    <a href="/detail"><img
                                            src="https://www.mutuellebleue.fr/app/uploads/sites/2/2020/07/petit-dejeuner-complet.jpg"
                                            alt="Nom du plat" class="rounded"></a>
                                    <img src="img/like%20(1).png" alt="Nom du plat" class="rounded" id="rounded">
                                    <div class="ouioui">
                                        <div class="nonnon">
                                            <p class="bulle"><?= $value["ingredients"][0] ?></p>
                                            <p class="bulle"><?= $value["ingredients"][1] ?></p>
                                            <p class="bulle"><?= $value["ingredients"][2] ?></p>
                                        </div>
                                        <h3 style="text-align: left;"><?= $value["nomPlat"] ?></h3>
                                        <p style="text-align: left;"><?= $value["description"] ?>
                                            <?php
                                            if (strlen(("phpsdfjshdfjksdfdsjhdsfhgdsfjhfdsjfdsgj")) > 30) {
                                                echo "...";
                                            } else {
                                                echo "phpsdfjshdfjksdfdsjhdsfhgdsfjhfdsjfdsgj";
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
            <div class="nav">
                <button class="prev" data-index="" id="prev<?= $j ?>" onclick="Prev(<?= $j ?>)"><i
                        class="fas fa-arrow-left fa-2x"></i></button>
                <button class="next" data-index="0" id="next<?= $j ?>" onclick="Next(<?= $j ?>)"><i
                        class="fas fa-arrow-right fa-2x"></i></button>
            </div>
        </div>
    </div>
    <?php
    $j++;
} ?>