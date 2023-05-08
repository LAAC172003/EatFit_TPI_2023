<?php

use Eatfit\Api\Controllers\RatingController;
use Eatfit\Api\Controllers\RecipeController;
use Eatfit\Api\Controllers\UserController;
use Eatfit\Api\Core\ApiValue;
use Eatfit\Api\Core\Application;
use Eatfit\Api\Core\EnvLoader;


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
    ]
];
$app = new Application(dirname(__DIR__), $config);

$app->router->get('/', function (): ApiValue {
    return new ApiValue(Application::$app->router->getRoutes(), 200);
});
$app->router->get('/test', function (): ApiValue {
    $request = new \Eatfit\Api\Core\Request();

    return new ApiValue($request->getData(["search_filters" => ["title", "category","date_added"], "filter" => ["category", "food_type"]], true), 200);
});

$app->router->put('/login', [UserController::class, 'login']);// get User

$app->router->get('/user', [UserController::class, 'read']);// get User
$app->router->post('/user', [UserController::class, 'create']);// get User
$app->router->put('/user', [UserController::class, 'update']);// get User
$app->router->delete('/user', [UserController::class, 'delete']);// get User

$app->router->get('/recipe', [RecipeController::class, 'read']);// get User
//$app->router->get('/recipe/{search}/{filter}', [RecipeController::class, 'read']);// get User
$app->router->post('/recipe', [RecipeController::class, 'create']);// get User
$app->router->put('/recipe', [RecipeController::class, 'update']);// get User
$app->router->delete('/recipe', [RecipeController::class, 'delete']);// get User

$app->router->get('/rating', [RatingController::class, 'read']);// get User
$app->router->post('/rating', [RatingController::class, 'create']);// get User
$app->router->put('/rating', [RatingController::class, 'update']);// get User
$app->router->delete('/rating', [RatingController::class, 'delete']);// get User

$app->run();
