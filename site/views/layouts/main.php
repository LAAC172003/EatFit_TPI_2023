<?php
/** @var $this View */

use Eatfit\Site\Core\Application;
use Eatfit\Site\Core\View;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->title ?></title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-md navbar-dark">
        <a class="navbar-brand font-weight-bold" href="/">Accueil</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <?php
                if (!Application::isGuest()) {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" href="/recipe">Ajouter une nouvelle recette</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" href="/history">Historique des recettes consommées</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" href="/food_type">Ajouter nouveau type de nourriture</a>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <ul class="navbar-nav ml-auto">
                <?php
                if (Application::isGuest()) {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" href="/login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" href="/register">Inscription</a>
                    </li>

                    <?php
                } else {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" href="/profile">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" href="/logout">Se déconnecter</a>
                    </li>
                    <?php
                }
                ?>

            </ul>
        </div>
    </nav>
</header>

<main>
    <div class="container">
        <?php

        if (Application::$app->session->getFlash('success')): ?>
            <div class="alert alert-success">
                <p><?php echo Application::$app->session->getFlash('success') ?></p>
            </div>
        <?php endif; ?>
        <?php

        if (Application::$app->session->getFlash('error')): ?>
            <div class="alert alert-danger">
                <p><?php echo Application::$app->session->getFlash('error') ?></p>
            </div>
        <?php endif; ?>
        {{content}}
    </div>

</main>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="/js/home.js"></script>
<!--<script s></script>-->
</body>
</html>
