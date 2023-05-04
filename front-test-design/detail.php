<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de la recette</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="test.css">
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Logo</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Catalogue</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact</a>
                </li>
            </ul>
        </div>
    </nav>
</header>

<main>
    <div class="container">
        <div class="recipe-detail">
            <img src="https://picsum.photos/1200/600" alt="Image de la recette" class="img-fluid mb-4"
                 id="recipe-image">
            <h2 class="recipe-title" id="recipe-title">Titre de la recette</h2>
            <p class="recipe-description" id="recipe-description">Description de la recette</p>
            <p class="recipe-author" id="recipe-author">Créé par : Auteur</p>
            <p class="recipe-category" id="recipe-category">Catégorie : Catégorie</p>
            <p class="recipe-difficulty" id="recipe-difficulty">Difficulté : Difficulté</p>
            <p class="recipe-calories" id="recipe-calories">Calories : Nombre de calories</p>

            <div class="row">
                <div class="col-md-6">
                    <h3 class="ingredients-title">Ingrédients</h3>
                    <ul class="ingredients-list" id="ingredients-list">
                        <li>Ingredient 1</li>
                        <li>Ingredient 2</li>
                        <li>Ingredient 3</li>
                        <li>Ingredient 4</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h3 class="instructions-title">Instructions</h3>
                    <ol class="instructions-list" id="instructions-list">
                        <li>Étape 1</li>
                        <li>Étape 2</li>
                        <li>Étape 3</li>
                        <li>Étape 4</li>
                    </ol>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <h3 class="prep-time-title">Temps de préparation</h3>
                    <p class="prep-time" id="prep-time">00:00</p>
                </div>
                <div class="col-md-6">
                    <h3 class="cook-time-title">Temps de cuisson</h3>
                    <p class="cook-time" id="cook-time">00:00</p>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <h3 class="serving-size-title">Portions</h3>
                    <p class="serving-size" id="serving-size">0</p>
                </div>
            </div>
        </div>
    </div>
</main>
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <p class="text-center">&copy; 2023 - Tous droits réservés</p>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
