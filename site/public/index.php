<?php


use Eatfit\Site\Controllers\AboutController;
use Eatfit\Site\Controllers\HistoryController;
use Eatfit\Site\Controllers\RecipeController;
use Eatfit\Site\Controllers\SiteController;
use Eatfit\Site\Controllers\UserController;
use Eatfit\Site\Core\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$config = [
    'API_URL' => "http://eatfittpi2023api/",
    'UPLOAD_PATH' => 'uploads/',
    'MAX_FILE_SIZE' => 3 * 1024 * 1024, //(3mb)
    'MAX_FILES_SIZE' => 70 * 1024 * 1024, //(70mb)
    'ALLOWED_IMAGE_EXTENSIONS' => array('png', 'jpg', 'jpeg'),
];

$app = new Application(dirname(__DIR__), $config);

$app->router->get('/', [SiteController::class, 'home']);
$app->router->post('/', [SiteController::class, 'home']);

$app->router->get('/recipe', [RecipeController::class, 'create']);
$app->router->post('/recipe', [RecipeController::class, 'create']);

$app->router->get('/recipe/detail/{idRecipe}', [RecipeController::class, 'detail']);
$app->router->post('/recipe/detail/{idRecipe}', [RecipeController::class, 'detail']);
$app->router->get('recipe/edit/{idRecipe}', [RecipeController::class, 'update']);
$app->router->get('recipe/delete/{idRecipe}', [RecipeController::class, 'delete']);

$app->router->get('rating/delete/{idRating}', [RecipeController::class, 'deleteRating']);
$app->router->get('rating/update/{idRating}', [RecipeController::class, 'updateRating']);
$app->router->post('rating/update/{idRating}', [RecipeController::class, 'updateRating']);

$app->router->get('/food_type', [RecipeController::class, 'addFoodType']);
$app->router->post('/food_type', [RecipeController::class, 'addFoodType']);

$app->router->get('/login', [UserController::class, 'login']);
$app->router->post('/login', [UserController::class, 'login']);
$app->router->get('/logout', [UserController::class, 'logout']);

$app->router->get('/register', [UserController::class, 'register']);
$app->router->post('/register', [UserController::class, 'register']);

$app->router->get('/profile', [UserController::class, 'profile']);
$app->router->post('/profile/{method}/', [UserController::class, 'profile']);

$app->router->get('/addToHistory/{idRecipe}', [HistoryController::class, 'addToHistory']);
$app->router->get('/history', [HistoryController::class, 'history']);
$app->router->get('/history/delete', [HistoryController::class, 'deleteHistory']);
$app->router->get('/history/delete/{idConsumedRecipe}', [HistoryController::class, 'deleteHistory']);

// /{id}
$app->run();
