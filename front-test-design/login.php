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
                    <a class="nav-link font-weight-bold" href="basket.php">Mon panier</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" href="contact.php">Contact</a>
                </li>
            </ul>
        </div>
    </nav>
</header>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="login-form p-4 mt-5">
                <h2 class="text-center mb-4">Login</h2>
                <form>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" placeholder="Enter your username">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" placeholder="Enter your password"
                               style="max-width: 100%;">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                    <br>
                    <div class="mb-3">
                        <a href="inscription.php"> Vous inscrire ?</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<footer class="mt-auto">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <p class="text-center font-weight-bold">&copy; 2023 Boutique de casquettes</p>
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
