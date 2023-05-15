<?php

use Eatfit\Api\Controllers\HistoryController;
use Eatfit\Api\Controllers\RatingController;
use Eatfit\Api\Controllers\RecipeController;
use Eatfit\Api\Controllers\UserController;
use Eatfit\Api\Core\ApiValue;
use Eatfit\Api\Core\Application;
use Eatfit\Api\Core\EnvLoader;

require_once __DIR__ . '/../vendor/autoload.php';

// Chargement des variables d'environnement à partir du fichier .env
$dotenv = new EnvLoader(dirname(__DIR__) . '/.env');
try {
    $dotenv->load();
} catch (Exception $e) {
    echo new ApiValue(null, $e->getCode(), $e->getMessage());
    return;
}

// Configuration de l'application
$config = [
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ],
    'UPLOAD_PATH' => $_ENV['UPLOAD_PATH'],
];

// Création de l'instance de l'application
$app = new Application(dirname(__DIR__), $config);

// Définition des routes et des contrôleurs associés

// Route racine
$app->router->get('/', function (): ApiValue {
    return new ApiValue(Application::$app->router->getRoutes(), 200);
});

// Authentification de l'utilisateur
$app->router->put('/login', [UserController::class, 'login']);

// Récupère les informations de l'utilisateur actuel
$app->router->get('/userById', [UserController::class, 'getUserByToken']);

// Routes pour le contrôleur HistoryController
$app->router->get('history', [HistoryController::class, 'read']);
$app->router->post('history', [HistoryController::class, 'create']);
$app->router->put('history', [HistoryController::class, 'update']);
$app->router->delete('history', [HistoryController::class, 'delete']);

// Routes pour le contrôleur UserController
$app->router->get('/user', [UserController::class, 'read']);
$app->router->post('/user', [UserController::class, 'create']);
$app->router->put('/user', [UserController::class, 'update']);
$app->router->delete('/user', [UserController::class, 'delete']);

// Routes pour le contrôleur RecipeController
$app->router->get('/recipe', [RecipeController::class, 'read']);
$app->router->post('/recipe', [RecipeController::class, 'create']);
$app->router->put('/recipe', [RecipeController::class, 'update']);
$app->router->delete('/recipe', [RecipeController::class, 'delete']);

// Routes pour le contrôleur RatingController
$app->router->get('/rating', [RatingController::class, 'read']);
$app->router->post('/rating', [RatingController::class, 'create']);
$app->router->put('/rating', [RatingController::class, 'update']);
$app->router->delete('/rating', [RatingController::class, 'delete']);

// Autres routes
$app->router->post('/food_type', [RecipeController::class, 'addFoodType']);
$app->router->get('/food_types', [RecipeController::class, 'getFoodTypes']);
$app->router->get('/categories', [RecipeController::class, 'getCategories']);

// Lancement de l'application
$app->run();
