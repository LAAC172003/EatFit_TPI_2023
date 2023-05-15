<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="test.css">

    <style>

        .signup-form {
            max-width: 100%;
            overflow: hidden;
            min-height: 550px /* Ajoutez une hauteur minimale pour englober tous les champs */
        }

    </style>
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
                <li class="nav-item">
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

<!-- Signup form -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="login-form mt-5 p-4">
                <h2 class="text-center mb-4">Inscription</h2>
                <form>
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur <span
                                class="obligatoire">*</span></label>
                        <input type="text" class="form-control" id="username"
                               required="required" placeholder="Entrez votre nom d'utilisateur">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="obligatoire">*</span></label>
                        <input type="email" required="required" class="form-control" id="email"
                               placeholder="Entrez votre email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe <span class="obligatoire">*</span></label>
                        <input type="password" class="form-control" id="password"
                               required="required" placeholder="Entrez votre mot de passe">
                    </div>
                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">Confirmez le mot de passe <span
                                class="obligatoire">*</span></label>
                        <input type="password" class="form-control" id="password-confirm"
                               required="required" placeholder="Confirmez votre mot de passe">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Inscription</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Footer -->
<footer class="footer bg-dark mt-auto py-3">
    <div class="container">
        <p>&copy; 2023 - YourSite - All Rights Reserved</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"
        integrity="sha384-eMNCOe7tC1doHpGoJtKh7z7lGz7fuP4F8nfdFvAOA6Gg/z6Y5J6XqqyGXYM2ntX5"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js"
        integrity="sha384-cn7l7gDp0eyniUwwAZgrzD06kc/tftFf19TOAs2zVinnD/C7E91j9yyk5//jjpt/"
        crossorigin="anonymous"></script>
</body>
</html>
