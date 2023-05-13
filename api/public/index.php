<?php

use Eatfit\Api\Controllers\HistoryController;
use Eatfit\Api\Controllers\RatingController;
use Eatfit\Api\Controllers\RecipeController;
use Eatfit\Api\Controllers\UserController;
use Eatfit\Api\Core\ApiValue;
use Eatfit\Api\Core\Application;
use Eatfit\Api\Core\EnvLoader;
use Eatfit\Api\Core\Request;


require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new EnvLoader(dirname(__DIR__) . '/.env');
try {
    $dotenv->load();
} catch (Exception $e) {
    echo new ApiValue(null, $e->getCode(), $e->getMessage());
    return;
}
$config = [
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ],
    'UPLOAD_PATH' => $_ENV['UPLOAD_PATH'],
];
$app = new Application(dirname(__DIR__), $config);

$app->router->get('/', function (): ApiValue {
    return new ApiValue(Application::$app->router->getRoutes(), 200);
});
$app->router->get('/test', function (Request $request): ApiValue {
    return new ApiValue($request->getData(["search_filters" => ["title", "category", "date_added"], "filter" => ["category", "food_type"]], true), 200);
});

$app->router->put('/login', [UserController::class, 'login']);// Authentification de l'utilisateur

$app->router->get('/userById', [UserController::class, 'getUserByToken']);// Récupère les informations de l'utilisateur actuel

$app->router->get('history', [HistoryController::class, 'read']);// Ajoute une recette à l'historique de l'utilisateur
$app->router->post('history', [HistoryController::class, 'create']);// Ajoute une recette à l'historique de l'utilisateur
$app->router->put('history', [HistoryController::class, 'update']);// Ajoute une recette à l'historique de l'utilisateur
$app->router->delete('history', [HistoryController::class, 'delete']);// Ajoute une recette à l'historique de l'utilisateur

$app->router->get('/user', [UserController::class, 'read']);// Récupère les informations d'un utilisateur
$app->router->post('/user', [UserController::class, 'create']);// Crée un nouvel utilisateur
$app->router->put('/user', [UserController::class, 'update']);// Met à jour les informations d'un utilisateur
$app->router->delete('/user', [UserController::class, 'delete']);// Supprime un utilisateur

$app->router->get('/recipe', [RecipeController::class, 'read']);// Récupère une recette ou toutes les recettes
$app->router->post('/recipe', [RecipeController::class, 'create']);// Crée une nouvelle recette
$app->router->put('/recipe', [RecipeController::class, 'update']);// Met à jour une recette existante
$app->router->delete('/recipe', [RecipeController::class, 'delete']);// Supprime une recette

$app->router->get('/rating', [RatingController::class, 'read']);// Récupère une évaluation ou toutes les évaluations
$app->router->post('/rating', [RatingController::class, 'create']);// Crée une nouvelle évaluation
$app->router->put('/rating', [RatingController::class, 'update']);// Met à jour une évaluation existante
$app->router->delete('/rating', [RatingController::class, 'delete']);// Supprime une évaluation

$app->router->post('/food_type', [RecipeController::class, 'addFoodType']);// Ajoute un nouveau type d'aliment
$app->router->get('/food_types', [RecipeController::class, 'getFoodTypes']);// Récupère tous les types d'aliments
$app->router->get('/categories', [RecipeController::class, 'getCategories']);// Récupère toutes les catégories de recettes

$app->run();// Lance l'application

