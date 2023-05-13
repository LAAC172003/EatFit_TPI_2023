<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique de casquettes</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" type="text/css"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" type="text/css" href="test.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-md navbar-dark">
        <a class="navbar-brand font-weight-bold" href="index.php">Accueil</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" href="catalogue.php">Catalogue</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" href="catalogue.php">Catalogue</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" href="catalogue.php">Catalogue</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" href="catalogue.php">Catalogue</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" href="catalogue.php">Catalogue</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" href="inscription.php">Inscription</a>
                </li>
            </ul>
        </div>
    </nav>
</header>

<main>
    <?php
    function getProducts()
    {
        $dejeuner = [
            "nomPlat" => "TEst3",
            "categorie" => "dejeuner",
            "description" => "test",
            "ingredients" => [
                "pain",
                "jambon",
                "fromage",
                "oeuf",
                "tomate",
                "salade",
                "mayonnaise"
            ]
        ];
        $diner = [
            "nomPlat" => "Tets2",
            "description" => "test",
            "categorie" => "diner",
            "ingredients" => [
                "qq",
                "ww",
                "fromage",
                "oeuf",
                "tomate",
                "salade",
                "mayonnaise"
            ]
        ];
        $dessert = [
            "nomPlat" => "Test",
            "categorie" => "dessert",
            "description" => "test",
            "ingredients" => [
                "paiddn",
                "jambon",
                "asd",
                "oeuf",
                "tomate",
                "salade",
                "mayonnaise"
            ]
        ];
        $arrayProducts = [
            "déjeuner" => $dejeuner,
            "diner" => $diner,
            "dessert" => $dessert
        ];
        return $arrayProducts;
    }

    $j = 0;
    foreach (getProducts() as $key => $value) {
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
                                        <a href="login.php"><img
                                                src="https://www.mutuellebleue.fr/app/uploads/sites/2/2020/07/petit-dejeuner-complet.jpg"
                                                alt="Nom du plat" class="rounded"></a>
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

</main>
<footer class="mt-auto">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <p class="text-center font-weight-bold">&copy; 2023 Boutique de casquettes</p>
            </div>
        </div>
    </div>
    </div>
</footer>
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="jsbien.js"></script>
<!--<script s></script>-->
</body>
</html>
